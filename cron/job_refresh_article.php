<?php
    
    // set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');
	
	require_once(dirname(__FILE__) . "/../php/util/ConfigReader.php");
    
    $CR = new ConfigReader("db.conf.php");
	
	$jUser = $CR->get("#user");
	$jPass = $CR->get("#pass");
	$jDb   = $CR->get("#dbname");
	$jHost = $CR->get("#host");
    
    $user   = "dwh";
    $pass   = "dwh";
    #$dbname = "dwh";
    $dbname = "(DESCRIPTION = " .
                    "(ADDRESS_LIST = " .
                        "(ADDRESS = " .
                            "(PROTOCOL = TCP)" .
                            "(Host = 172.16.9.130)" .
                            "(Port = 1521)" .
                        ")" .
                    ")" .
                    "(CONNECT_DATA = (SID = dwh))" .
                ")";
    
    echo "start job at " . date("Y-m-d H:i:s") . "\n";
    
    $msgToSend = "";
    
    echo "make connection to source database.. ";
    
    $conn = oci_connect($user, $pass, $dbname, "AL32UTF8");
    if ($conn) {
        
        echo "connected.\n";
        
        $dateToProcess = date("d-M-y", strtotime("-1 days"));
        
        echo "querying data from source database.. ";
        
        # -- articles
        $sql =  "select distinct armatcexr ARTICLE_CODE, armatsobdesc ARTICLE_DESC, armattypp TIPO, armavuuvclibl UOM, " .
                "armbcatt BRAND_CODE, armbcllibl BRANCD_DESC, merdiv division, to_char(ARMADDEB, 'yyyy-mm-dd') START_DATE, to_char(ARMADFIN, 'yyyy-mm-dd') END_DATE, to_char(ARMADMAJ, 'yyyy-mm-dd hh24:mi:ss') LAST_UPDATE " . 
                "from GDWH_ARTMASTERA2 " . 
                "inner join GDWH_ARTMASTERB on armatcinr = armbcinr and ARMBCCLA = '1' and (trunc(sysdate) between trunc(armbddeb) and trunc(armbdfin)) " .
				# overcome double brand, remove if data already fixed
                #"and instr(armatsobdesc, armbcllibl) > 0 " .
				"and armbddeb = (select max(x.armbddeb) from GDWH_ARTMASTERB x where x.armbcinr = armatcinr and x.ARMBCCLA = '1' and (trunc(sysdate) between trunc(x.armbddeb) and trunc(x.armbdfin))) " . 
                "inner join gdwh_merch on armatcinr = mervcinr and armavcinv = mervcinv and (trunc(sysdate) between trunc(merdivddeb) and trunc(merdivdfin)) " .
				"and (trunc(sysdate) between trunc(mercatddeb) and trunc(mercatdfin)) " .
				"and (trunc(sysdate) between trunc(merscatddeb) and trunc(merscatdfin)) " .
				"and (trunc(sysdate) between trunc(merclassddeb) and trunc(merclassdfin)) " .
				"and (trunc(sysdate) between trunc(mersclassddeb) and trunc(mersclassdfin)) " .
				"where merdiv in ('A','B','C','D','E') and (trunc(sysdate) between trunc(armaddeb) and trunc(armadfin))"; 
                #"where trunc(ARMADMAJ) = :p_date";
        
		$stid = oci_parse($conn, $sql);
        #oci_bind_by_name($stid, ":p_date", $dateToProcess);
        $ret = oci_execute($stid);
        
        if ($ret) {
            
            echo "succeeded.\n";
            
            echo "make connection to destination database.. ";
            # make connection
			$connM = mysql_connect($jHost, $jUser, $jPass);
			if (!$connM) {
                $msgToSend .= "Failed to connect to destination database.<br>";
				echo "Error: cannot connect to destination database. Leave job.\n";
				exit;
			}
			echo "connected.\n";
			echo "select database.. ";
			if (!mysql_select_db($jDb)) {
				mysql_close($connM);
                $msgToSend .= "Failed to use destination database.<br>";
				echo "Error: cannot use destination database. Leave job.\n";
				exit;
			}
			echo "selected.\n";
            # eo make connection
            
            echo "start transaction.\n";
            $result = mysql_query("START TRANSACTION");
            
			echo "emptying table.. ";
			# -- empty first
			$sql = "truncate table mst_article_gold";
			$result = mysql_query($sql);
			echo "ok.\n";
			
            $totalInsertedRow = 0;
             
            while ($row = oci_fetch_assoc($stid)) {
                $articleCode = $row["ARTICLE_CODE"];
                $articleDesc = $row["ARTICLE_DESC"];
                $tipo = $row["TIPO"];
                $uom = $row["UOM"];
                $brandCode = $row["BRAND_CODE"];
                $brandDesc = $row["BRANCD_DESC"];
				$division = $row["DIVISION"];
                $startDate = $row["START_DATE"];
                $endDate = $row["END_DATE"];
                $lastUpdate = $row["LAST_UPDATE"];
                
                $sqlM = "insert into mst_article_gold (" .
                            "article_code, description, tipo, uom,  brand_code, brand_name, division, start_date, end_date, last_update, created_by, created_date" .
                        ") values ('" .
                            mysql_real_escape_string($articleCode) . "', '" . mysql_real_escape_string($articleDesc) . "', '" .
                            $tipo . "', '" . mysql_real_escape_string($uom) . "', '" . mysql_real_escape_string($brandCode) . "', '" . 
                            mysql_real_escape_string($brandDesc) . "', '" . mysql_real_escape_string($division) . "', '" . mysql_real_escape_string($startDate) . "', '" . 
                            mysql_real_escape_string($endDate) . "', '" . mysql_real_escape_string($lastUpdate) . "', 'system', sysdate()" .
                        ")";
                
                $result = mysql_query($sqlM);
                if ($result) {
                    $totalInsertedRow++;
                    if ($totalInsertedRow % 1000 == 0) {
                        $result = mysql_query("COMMIT");
                        echo "commit at row " . $totalInsertedRow . " and start new transaction.\n";
                        $result = mysql_query("START TRANSACTION");
                    }
                }
                else {
                    $msgToSend .= "Failed to execute \"" . $sqlM . "\".<br>";
                    echo "Failed to execute \"" . $sqlM . "\".\n";
                    continue;
                }
                
            }
            
            $result = mysql_query("COMMIT");
            echo "commit.\n";
            
            echo "total inserted row: " . $totalInsertedRow . ".\n";
            
            echo "close connection to destination database.\n";			
			# close connection
			mysql_close($connM);
            
        }
        else {
            $msgToSend .= "Failed to querying data from source database.<br>";
            echo "Error: cannot querying data from source database. Leave job.\n";
        }
        
        oci_free_statement($stid);
        
        echo "close connection to source database.\n";	
        oci_close($conn);
    }
    else {
        $msgToSend .= "Failed to connect to source database.<br>";
        echo "Error: cannot connect to source database. Leave job.\n";
    }
    
    # -- sending mail
    if ($msgToSend != "") {
        
        echo "some error found, sending error message.\n";
        
        require_once(dirname(__FILE__) . "/../php/util/PHPMailer/PHPMailerAutoload.php");
    
        $mail = new PHPMailer;
        
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = "mail.toserbayogya.com";                // Specify main and backup SMTP servers
        $mail->SMTPAuth = false;                              // Enable SMTP authentication
        $mail->Username = "";      // SMTP username
        $mail->Password = "";                                 // SMTP password
        $mail->SMTPSecure = "tls";                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        
        $mail->From = "spaceintel@mailer.com";
        $mail->FromName = "Space Intelligence";
        $mail->addAddress("jerry.hasudungan@dominomail.yogya.com", "Jerry");     // Add a recipient
        $mail->isHTML(true);                                                    // Set email format to HTML
        
        $mail->Subject = "Space Intelligence ARTICLE Job Error";
        $mail->Body    = $msgToSend;
        $mail->AltBody = $msgToSend;
        
        if (!$mail->send()) {
            echo "message could not be sent.\n";
            echo "mailer Error: " . $mail->ErrorInfo . ".\n\n";
        } else {
            echo "message has been sent.\n\n";
        }    
    }
    # -- mail end
    
    echo "finished all jobs at " . date("Y-m-d H:i:s") . "\n\n";
?>