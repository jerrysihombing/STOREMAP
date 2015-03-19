<?php
	
	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
		
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables */
	$aColumns = array('user_id', 'user_name', 'email', 'departement', 'branch_code', 'role_name', 'active');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	$CR = new ConfigReader("db.conf.php");
	
	/* Database connection information */
	$gaSql['user']       = $CR->get("#user");
	$gaSql['password']   = $CR->get("#pass");
	$gaSql['db']         = $CR->get("#dbname");
	$gaSql['server']     = $CR->get("#host");
		
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
		
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT " . mysql_real_escape_string( $_GET['iDisplayStart'] ) . ", " . mysql_real_escape_string( $_GET['iDisplayLength'] );
	}	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ] . "
				 	" . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
			
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		#for ( $i=1 ; $i<count($aColumns) ; $i++ )	# start from 1, skip ID
		{
			$sWhere .= $aColumns[$i]." LIKE '%" . mysql_real_escape_string( $_GET['sSearch'] ) . "%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
				
	/*
	 * SQL queries
	 * Get data to display
	 */
	$table = "select id, user_id, user_name, email, departement, branch_code, role_name, case when active = 1 then 'Y' else 'N' end as active from adm_user"; 

	$sQuery =   "SELECT SQL_CALC_FOUND_ROWS x.id, x.user_id, x.user_name, x.email, x.departement, x.branch_code, x.role_name, x.active, " .
				" concat(x.id, '#', x.user_id, '#', x.user_name, '#', x.email, '#', x.branch_code, '#', x.departement, '#', x.role_name, '#', cast(x.active as char(1))) as joined " .
				"FROM (" . $table . ") x " . 
				$sWhere . " " .
				$sOrder . " " .
				$sLimit;
	
	# debuging
	#$handle = fopen("/tmp/test.txt", "w");
	#fwrite($handle, $sQuery);
	#fclose($handle);	
	# eo debuging
		
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "SELECT COUNT(x.id) FROM (" . $table . ") x ";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
		
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			#if ( $aColumns[$i] != ' ' )
			#{
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			#}
		}
		
		/*
		 * Optional Configuration:
		 * If you need to add any extra columns (add/edit/delete etc) to the table, that aren't in the
		 * database - you can do it here
		 */		 
		# --- my code to make edit and delete functionality --- #
		if ($aRow["user_id"] == "admin") {
			$row[] = "&nbsp;";
			$row[] = "&nbsp;";
		}
		else {
			$row[] = "<img src=\"../images/edit_24.png\" border=\"0\" title=\"Edit\" style=\"cursor:pointer;\" onclick=\"goEditUser('" . $aRow["joined"] . "')\" >";
			$row[] = "<img src=\"../images/delete_24.png\" border=\"0\" title=\"Delete\" style=\"cursor:pointer;\" onclick=\"goDeleteUser('" . $aRow["joined"] . "')\" >";
		}
		# --- eo my code --- #
		
		$output['aaData'][] = $row;				
	}
	
	echo json_encode( $output );
?>