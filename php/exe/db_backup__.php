<?php
	
	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');		
	
	$CR = new ConfigReader("db.conf.php");

	define("PMBP_EXPORT_DIR", $CR->get("#backupdir"));
	$CONF["sql_host"] = $CR->get("#host");
	$CONF["sql_user"] = $CR->get("#user");
	$CONF["sql_passwd"] = $CR->get("#pass");
	$CONF["sql_db"] = $CR->get("#dbname");	
	$PMBP_SYS_VAR["except_tables"] = "";
	$PMBP_SYS_VAR["memory_limit"] = 4000000; // (less than) 4 mb
					
	$msg = "";
	$doit = true;
						
	if ($doit) {
		
		try {
			// generate db dump
			$backupfile = PMBP_dump($CONF["sql_db"], true, true, true, "gzip", "ais.iaid.ac.id");
			
			// is there no db connection or a db missing?
			if ($backupfile && $backupfile !== "DB_ERROR") {
				// change mode to 0700 
				// if you can't read the files, alter this to e.g. 0777'                   
				@chmod($backupfile,0700);
				# success	
				
				$msg = "Database has been backup successfull. ";
				
			} 
			else if ($backupfile === "DB_ERROR") {
				# error, cannot make db connection
				
				$msg = "Error: Failed to backup database.";
			} 
			else {
				# error, unknown error
				$msg = "Error: Failed to backup database.";
			}
		}
		catch (Exception $e) {			
			$msg = "Error: Unknown error. ";
		}					
	}
	
	echo $msg;
	
	
	
	# -------------------------------- #
	# --- REQUIRED FUNCTIONS BELOW --- #
	# -------------------------------- #
	
	// generates a dump of $db database
	// $tables and $data set whether tables or data to backup. $comment sets the commment text
	// $drop and $zip tell if to include the drop table statement or dry to pack
	function PMBP_dump($db,$tables,$data,$drop,$zip,$comment) {
		global $CONF;
		global $PMBP_SYS_VAR;
		$error=false;
		
		// set max string size before writing to file
		if (@ini_get("memory_limit")) {
			$max_size=500000*ini_get("memory_limit"); 
		} else {
			ini_set("memory_limit",$PMBP_SYS_VAR['memory_limit']/1024000);
			$max_size=500000*($PMBP_SYS_VAR['memory_limit']/1024000);
		}
			
		// set backupfile name
		$time=date("YmdHis");
		$time_2=date("Y-m-d H:i:s");
		if ($zip=="gzip") $backupfile=$db.".".$time.".sql.gz";
			else $backupfile=$db.".".$time.".sql";
		$backupfile=PMBP_EXPORT_DIR . "/ " .$backupfile;
						
		if ($con=PMBP_mysql_connect()) {
	
			//create comment
			$out="# MySQL dump of database '".$db."' on host '".$CONF['sql_host']."'\n";
			$out.="# backup date and time: ".$time_2."\n";
			$out.="# courtesy by phpMyBackupPro "."\n";
			
			// write users comment
			if ($comment) {
				$out.="# comment:\n";
				$comment=preg_replace("'\n'","\n# ","# ".$comment);
				foreach(explode("\n",$comment) as $line) $out.=$line."\n";
				$out.="\n";
			}
			
			
			// set and log character set
			$characterSet = PMBP_set_character_set();
			$out.="### used character set: " . $characterSet . " ###\n";
			$out.="set names " . $characterSet . ";\n\n";
	
			// print "use database" if more than one database is available
			if (count(PMBP_get_db_list())>1) {
				$out.="CREATE DATABASE IF NOT EXISTS `".$db."`;\n\n";
				$out.="USE `".$db."`;\n";
			}
			
			// select db
			@mysql_select_db($db);
			
			// get auto_increment values and names of all tables
			$res=mysql_query("show table status");
			$all_tables=array();
			while($row=mysql_fetch_array($res)) $all_tables[]=$row;
	
			// get table structures
			foreach ($all_tables as $table) {
				$res1=mysql_query("SHOW CREATE TABLE `".$table['Name']."`");
				$tmp=mysql_fetch_array($res1);
				$table_sql[$table['Name']]=$tmp["Create Table"];
			}
	
			// find foreign keys
			$fks=array();
			if (isset($table_sql)) {
				foreach($table_sql as $tablenme=>$table) {
					$tmp_table=$table;
					// save all tables, needed for creating this table in $fks
					while (($ref_pos=strpos($tmp_table," REFERENCES "))>0) {
						$tmp_table=substr($tmp_table,$ref_pos+12);
						$ref_pos=strpos($tmp_table,"(");
						$fks[$tablenme][]=substr($tmp_table,0,$ref_pos);
					}
				}
			}
	
			// order $all_tables and check for ring constraints
			$all_tables_copy = $all_tables;
			$all_tables=PMBP_order_sql_tables($all_tables,$fks);
			$ring_contraints = false;
	
			// ring constraints found
			if ($all_tables===false) {
				$ring_contraints = true;
				$all_tables = $all_tables_copy;
				
				$out.="\n# ring constraints workaround\n";
				$out.="SET FOREIGN_KEY_CHECKS=0;\n"; 
				$out.="SET AUTOCOMMIT=0;\n";
				$out.="START TRANSACTION;\n"; 
			}
			unset($all_tables_copy);
	
			// as long as no error occurred
			if (!$error) {
				foreach ($all_tables as $row) {
					$tablename=$row['Name'];
					$auto_incr[$tablename]=$row['Auto_increment'];
	
					// don't backup tables in $PMBP_SYS_VAR['except_tables']
					if (in_array($tablename,explode(",",$PMBP_SYS_VAR['except_tables']))) {
						continue;
					}
	
					$out.="\n\n";
					// export tables
					if ($tables) {
	
						$out.="### structure of table `".$tablename."` ###\n\n";
						if ($drop) $out.="DROP TABLE IF EXISTS `".$tablename."`;\n\n";
						$out.=$table_sql[$tablename];
	
						// add auto_increment value
						if ($auto_incr[$tablename]) {
							$out.=" AUTO_INCREMENT=".$auto_incr[$tablename];
						}
						$out.=";";
					}
					$out.="\n\n\n";
	
					// export data
					if ($data && !$error) {
						$out.="### data of table `".$tablename."` ###\n\n";
	
						// check if field types are NULL or NOT NULL
						$res3=mysql_query("show columns from `".$tablename."`");
	
						$res2=mysql_query("select * from `".$tablename."`");
						if ($res2) {
							for ($j=0;$j<mysql_num_rows($res2);$j++){
								$out .= "insert into `".$tablename."` values (";
								$row2=mysql_fetch_row($res2);
								// run through each field
								for ($k=0;$k<$nf=mysql_num_fields($res2);$k++) {
									// identify null values and save them as null instead of ''
									if (is_null($row2[$k])) $out .="null"; else $out .="'".mysql_real_escape_string($row2[$k])."'";
									if ($k<($nf-1)) $out .=", ";
								}
								$out .=");\n";
		
								// if saving is successful, then empty $out, else set error flag
								if (strlen($out)>$max_size) {
									if ($out=PMBP_save_to_file($backupfile,$zip,$out,"a")) $out=""; else $error=true;
								}
							}
						} else {
							#echo "MySQL error: ".mysql_error();
							@unlink(PMBP_EXPORT_DIR.$backupfile);
							return false;
						}
	
					// an error occurred! Try to delete file and return error status
					} elseif ($error) {
						@unlink(PMBP_EXPORT_DIR.$backupfile);
						return false;
					}
	
					// if saving is successful, then empty $out, else set error flag
					if (strlen($out)>$max_size) {
						if ($out=PMBP_save_to_file($backupfile,$zip,$out,"a")) $out=""; else $error=true;
					}
				}
				
			// an error occurred! Try to delete file and return error status
			} else {
				@unlink($backupfile);
				return false;
			}
			
			// if db contained ring constraints        
			if ($ring_contraints) {
				$out.="\n\n# ring constraints workaround\n";
				$out .= "SET FOREIGN_KEY_CHECKS=1;\n"; 
				$out .= "COMMIT;\n"; 
			}
	
			// save to file
			if ($backupfile=PMBP_save_to_file($backupfile,$zip,$out,"a")) {
				if ($zip!="zip") return basename($backupfile);
			} else {
				@unlink($backupfile);
				return false;
			}
			
			// create zip file in file system
			include_once("pclzip.lib.php");
			$pclzip = new PclZip($backupfile.".zip");
			$pclzip->create($backupfile,PCLZIP_OPT_REMOVE_PATH,PMBP_EXPORT_DIR);    	
	
			// remove temporary plain text backup file used for zip compression
			@unlink(substr($backupfile,0,strlen($backupfile)));
			 
			if ($pclzip->error_code==0) {
				return basename($backupfile).".zip";
			} else {        	
				// remove temporary plain text backup file 
				@unlink(substr($backupfile,0,strlen($backupfile)-4));
				@unlink($backupfile);
				return false;
			}
	
		} else {
			return "DB_ERROR";
		}
	}
	
	
	// returns list of databases on $host host using $user user and $passwd password
	function PMBP_get_db_list() {
		global $CONF;
	
		// if there is given the name of a single database
		if ($CONF['sql_db']) {
			PMBP_mysql_connect();
			if (@mysql_select_db($CONF['sql_db']))
			{
				$dbs=array($CONF['sql_db']);
			}
			else
			{
				$dbs=array();
			}
			return $dbs;
		}
		
		// else try to get a list of all available databases on the server
		$list=array();
		PMBP_mysql_connect();
		$db_list=@mysql_list_dbs();
		while ($row=@mysql_fetch_array($db_list))
		{
			if (@mysql_select_db($row['Database']))
			{
				$list[]=$row['Database'];
			}
		}
		return $list;
	}
	
	// checks if the server is connected to the web
	function PMBP_is_connected() {
		$timeout=3; //timeout in seconds
		$connected = @fsockopen("phpmybackup.sourceforge.net", 80, $dontCare1,$dontCare2,$timeout);
		if ($connected){
			fclose($connected);
			return true;
		}else{
			return false;
		}
	}
	
	function PMBP_mysql_connect() {
		global $CONF;
		$res = @mysql_connect($CONF['sql_host'],$CONF['sql_user'],$CONF['sql_passwd']);
	//	if($res) {
	//		PMBP_set_character_set();
	//	}
		return $res;
	}
	
	function PMBP_get_character_set() {
		$res = mysql_query("SHOW VARIABLES LIKE 'character_set_database'");
		$obj=mysql_fetch_array($res);
		if($obj['Value']) {
			return $obj['Value'];	
		} else {
			return "utf8";
		}
	}
	
	function PMBP_set_character_set() {
		$characterSet = PMBP_get_character_set();
		@mysql_query("set names " . $characterSet);
		return $characterSet;
	}
	
	// orders the tables in $tables according to the constraints in $fks
	// $fks musst be filled like this: $fks[tablename][0]=needed_table1; $fks[tablename][1]=needed_table2; ...
	function PMBP_order_sql_tables($tables,$fks) {
		// do not order if no contraints exist
		if (!count($fks)) return $tables;
	
		// order
		$new_tables=array();
		$existing=array();
		$modified=true;
		while(count($tables) && $modified==true) {
			$modified=false;
			foreach($tables as $key=>$row) {
				// delete from $tables and add to $new_tables
				if (isset($fks[$row['Name']])) {
					foreach($fks[$row['Name']] as $needed) {
						// go to next table if not all needed tables exist in $existing
						if(!in_array($needed,$existing)) continue 2;
					}
				}
				
				// delete from $tables and add to $new_tables
				$existing[]=$row['Name'];
				$new_tables[]=$row;
				prev($tables);
				unset($tables[$key]);
				$modified=true;
			}
		}
	
		if (count($tables)) {
			// probably there are 'circles' in the constraints, because of that no proper backups can be created
			// This will be fixed sometime later through using 'alter table' commands to add the constraints after generating the tables.
			// Until now I just add the lasting tables to $new_tables, return them and print a warning
			foreach($tables as $row) $new_tables[]=$row;
				// do nothing
			return false;
		}
		return $new_tables;
	}
	
	
	// saves the string in $fileData to the file $backupfile as gz file or not ($zip)
	// returns backup file name if name has changed (zip), else true. If saving failed, return value is false
	function PMBP_save_to_file($backupfile,$zip,&$fileData,$mode) {
		// save to a gzip file
		if ($zip=="gzip") {
			if ($zp=@gzopen($backupfile,$mode."wb9")) {
				@gzwrite($zp,$fileData);
				@gzclose($zp);            
				return $backupfile;
			} else {
				return false;
			}
	
		// save to a plain text file (uncompressed)
		} else {
			if ($zp=@fopen($backupfile,$mode)) {
				@fwrite($zp,$fileData);
				@fclose($zp);
				return $backupfile;
			} else {
				return false;
			}
		}
	}

		
?>