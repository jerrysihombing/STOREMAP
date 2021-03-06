<?php
	
	@session_start();
	$a_auth = $_SESSION["a_auth"];
	$branch_code = $_SESSION["branch_code"];
	$store_code = $_SESSION["store_code"];
	$userId = $_SESSION["user_id"];
	$init = $_SESSION["salesListInit"];
	
	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	require_once(dirname(__FILE__) . "/../model/Site.php");
	
	# 31-07-2008 -> 2008-07-31
	function toYmdDate($v) {
		if (empty($v)) {
			return "";
		}
		return substr($v, 6, 4) . "-" . substr($v, 3, 2)  . "-" . substr($v, 0, 2);
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables */
	$aColumns = array('trans_date', 'brand_name', 'division', 'quantity', 'amount', 'store_init');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "trans_date";
	
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
				// ori
				#$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ] . " " . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", ";
				
				# check for date field				
				if (intval($_GET['iSortCol_'.$i]) == 0) { 
					$sOrder .= "trans_date_ori " . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", "; # format by original date field
				}
				# numeric
				else if (intval($_GET['iSortCol_'.$i]) == 3) { 
					$sOrder .= "quantity_ori " . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", "; 
				}
				# currency
				else if (intval($_GET['iSortCol_'.$i]) == 4) { 
					$sOrder .= "amount_ori " . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", "; 
				}
				else {
					$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ] . " " . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", ";
				}
				
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
	#$sWhere = "";
	#if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	#{
	#	$sWhere = "WHERE (";
	#	#for ( $i=0 ; $i<count($aColumns) ; $i++ )
	#	for ( $i=1 ; $i<count($aColumns) ; $i++ )	# start from 1, skip ID
	#	{
	#		$sWhere .= $aColumns[$i]." LIKE '%" . mysql_real_escape_string( $_GET['sSearch'] ) . "%' OR ";
	#	}
	#	$sWhere = substr_replace( $sWhere, "", -3 );
	#	$sWhere .= ')';
	#}
	
	# using my custom single search
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = " AND x.trans_date = current_date AND y.brand_name like '%" . $_GET['sSearch'] . "%'";		
	}
	
	# using my custom advance search
	if ( isset($_GET['s_adv']) && $_GET['s_adv'] == "yes" ) {
		
		$sWhere = ""; # reset filter				
		
		if ( isset($_GET['s_start_date']) && $_GET['s_start_date'] != "" )
		{
				if ( isset($_GET['s_end_date']) && $_GET['s_end_date'] != "" ) {
					$sWhere .= " AND x.trans_date between '" . toYmdDate($_GET['s_start_date']) . "' and '" . toYmdDate($_GET['s_end_date']) . "'";	
				}
				else {
					$sWhere .= " AND x.trans_date = '" . toYmdDate($_GET['s_start_date']) . "'";	
				}
		}
		else {
			if ( isset($_GET['s_end_date']) && $_GET['s_end_date'] != "" ) {
				$sWhere .= " AND x.trans_date = '" . toYmdDate($_GET['s_end_date']) . "'";	
			}
		}
		
		if ( isset($_GET['s_brand_name']) && $_GET['s_brand_name'] != "" )
		{
			$sWhere .= " AND y.brand_name = '" . $_GET['s_brand_name'] . "'";		
		}
		
		if ( isset($_GET['s_division']) && $_GET['s_division'] != "" )
		{
			$sWhere .= " AND w.name = '" . $_GET['s_division'] . "'";		
		}	
		/*
		if ( isset($_GET['s_article_type']) && $_GET['s_article_type'] != "" )
		{
			$sWhere .= " AND article_type = '" . $_GET['s_article_type'] . "'";		
		}
		*/
		if ( isset($_GET['s_store_init']) && $_GET['s_store_init'] != "" )
		{
			$sWhere .= " AND store_init = '" . $_GET['s_store_init'] . "'";		
		}
		
	}

	/*
	 * SQL queries
	 * Get data to display
	 */
	
	if ($init) {
		$table = 	"select '' trans_date, '' trans_date_ori, '' brand_name, '' division, ''  quantity, '' quantity_ori, '' amount, '' amount_ori, '' store_init ";
		$sQuery =   $table;	
	}
	else {
		$wBranch = ($userId == "admin" ? "" : "and store_init = '" . $store_code . "'");
		
		$table =	"select x.trans_date, y.brand_name, w.name division, " .
					"sum(x.qty) quantity, sum(x.gross_sale-x.disc) amount, x.store_init " .
					"from trn_sales_tpl x " .
					"inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date) " .
					"inner join mst_division w on y.division = w.code " .
					"where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code) " .
					$wBranch . " " .
					"group by x.trans_date, y.brand_name, w.name, store_init ";
		
		$sQuery = 	"select SQL_CALC_FOUND_ROWS date_format(x.trans_date, '%d-%m-%Y') trans_date, x.trans_date trans_date_ori, y.brand_name, w.name division, " .
					"format(sum(x.qty), 0) quantity, sum(x.qty) quantity_ori, format(sum(x.gross_sale-x.disc), 0) amount, sum(x.gross_sale-x.disc) amount_ori, x.store_init " .
					"from trn_sales_tpl x " .
					"inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date) " .
					"inner join mst_division w on y.division = w.code " .
					"where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code) " .
					$wBranch . " " .
					$sWhere . " " .
					"group by x.trans_date, y.brand_name, w.name, store_init " .
					$sOrder . " " .
					$sLimit;
	}
	
	# debuging
	/*
	$handle = fopen("/tmp/test.txt", "w");
	fwrite($handle, $sQuery);
	fclose($handle);	
	*/
	# eo debuging
		
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "SELECT COUNT(1) FROM (" . $table . ") t";
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
	
	$ST = new Site();
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				#$row[] = $aRow[ $aColumns[$i] ];
				
				if ($aColumns[$i] == "store_init") {
					$row[] = ($userId == "admin" ? $ST->storeGetInit($aRow[$aColumns[$i]]) : ($init ? "" : $branch_code));
				}
				else {
					$row[] = $aRow[ $aColumns[$i] ];	
				}
				
			}
		}
		
		/*
		 * Optional Configuration:
		 * If you need to add any extra columns (add/edit/delete etc) to the table, that aren't in the
		 * database - you can do it here
		 */		 
		# --- my code to make edit and delete functionality --- #		
		/*
		if ($CR->is_oper_allowable(143, $a_auth)) {
			$row[] = "<a href='/sales/edit/" . $aRow["id"] . ".html'><img src='../images/edit_24.png' title='Edit'></a>";
		}
		else {
			$row[] = "&nbsp;";
		}	
		
		if ($CR->is_oper_allowable(144, $a_auth)) {
			$row[] = "<span title='Click here to delete..' style='cursor:pointer' onclick='deleteAlert(" . $aRow["id"] . ", \"" . $aRow["trans_date"] . "\", \"" . $aRow["brand_name"] . "\", \"" . $aRow["division"] . "\", \"" . $aRow["article_type"] . "\", \"" . $aRow["store_init"] . "\")'><img src='../images/delete_24.png' /></span>";					
		}
		else {
			$row[] = "&nbsp;";
		}
		*/
		# --- eo my code --- #
				
		$output['aaData'][] = $row;				
	}
	
	echo json_encode( $output );
?>