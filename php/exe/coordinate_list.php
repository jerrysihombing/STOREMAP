<?php
	
	@session_start();
	$a_auth = $_SESSION["a_auth"];
	$branch_code = $_SESSION["branch_code"];
	$userId = $_SESSION["user_id"];
	
	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables */
	$aColumns = array('code', 'name', 'description', 'brand_name', 'division', 'map_code', 'wide_f', 'terminal_no', 'store_init');
	
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
				# ori
				#$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ] . " " . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", ";
				
				# check for number field				
				if (intval($_GET['iSortCol_'.$i]) == 6) { 
					$sOrder .= "wide " . mysql_real_escape_string( $_GET['sSortDir_'.$i] ) . ", "; # format by original number field
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
		$sWhere = " AND name like '%" . $_GET['sSearch'] . "%'";		
	}
	
	# using my custom advance search
	if ( isset($_GET['s_adv']) && $_GET['s_adv'] == "yes" ) {
		
		$sWhere = ""; # reset filter				
		
		if ( isset($_GET['s_code']) && $_GET['s_code'] != "" )
		{
			$sWhere .= " AND code like '%" . addslashes($_GET['s_code']). "%'";		
		}	
		
		if ( isset($_GET['s_name']) && $_GET['s_name'] != "" )
		{
			$sWhere .= " AND name like '%" . addslashes($_GET['s_name']) . "%'";		
		}	
		
		if ( isset($_GET['s_description']) && $_GET['s_description'] != "" )
		{
			$sWhere .= " AND description like '%" . addslashes($_GET['s_description']). "%'";		
		}	
		
		if ( isset($_GET['s_brand_name']) && $_GET['s_brand_name'] != "" )
		{
			$sWhere .= " AND brand_name = '" . $_GET['s_brand_name'] . "'";		
		}
		
		if ( isset($_GET['s_division']) && $_GET['s_division'] != "" )
		{
			$sWhere .= " AND division = '" . $_GET['s_division'] . "'";		
		}
		
		if ( isset($_GET['s_map_code']) && $_GET['s_map_code'] != "" )
		{
			$sWhere .= " AND map_code like '%" . addslashes($_GET['s_map_code']) . "%'";		
		}
		
		if ( isset($_GET['s_store_init']) && $_GET['s_store_init'] != "" )
		{
			$sWhere .= " AND store_init like '%" . addslashes($_GET['s_store_init']) . "%'";		
		}
		
		if ( isset($_GET['s_shape']) && $_GET['s_shape'] != "" )
		{
			$sWhere .= " AND shape = '" . $_GET['s_shape'] . "'";		
		}
		
	}

	/*
	 * SQL queries
	 * Get data to display
	 */
	
	$wBranch = ($userId == "admin" ? " WHERE 1 = 1 " : " where store_init = '" . $branch_code . "' ");
	
	$table = 	"select x.id, x.code, x.name, x.description, x.brand_name, x.division, x.map_code, format(x.wide, 2) wide_f, x.wide, y.store_init, terminal_no, " .
				"case x.shape when 'circle' then 'Circle' when 'rect' then 'Rectangular' when 'poly' then 'Polygon' else '' end shape " .
			 	"from mst_storemap x inner join mst_map y on y.code = x.map_code";
			 
	$sQuery =   "SELECT SQL_CALC_FOUND_ROWS id, code, name, description, brand_name, division, map_code, wide_f, wide, store_init, shape, terminal_no " .
				"FROM (" . $table . ") t" . $wBranch . 
				$sWhere . " " .
				$sOrder . " " .
				$sLimit;
	
	# debuging
	#$handle = fopen("/tmp/test.log", "a");
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
	$sQuery = "SELECT COUNT(id) FROM (" . $table . ") t";
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
			if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		
		/*
		 * Optional Configuration:
		 * If you need to add any extra columns (add/edit/delete etc) to the table, that aren't in the
		 * database - you can do it here
		 */		 
		# --- my code to make edit and delete functionality --- #
		
		if ($CR->is_oper_allowable(125, $a_auth)) {
			$row[] = "<a class='fancybox fancybox.ajax' href='/section/view/" . $aRow["id"] . ".html'><img src='../images/properties_24.png' title='Click here to view shape info..' /></a>";
		}
		else {
			$row[] = "&nbsp;";
		}
		
		if ($CR->is_oper_allowable(123, $a_auth)) {
			$row[] = "<a href='/section/edit/" . $aRow["id"] . ".html'><img src='../images/edit_24.png' title='Edit'></a>";
		}
		else {
			$row[] = "&nbsp;";
		}	
		
		if ($CR->is_oper_allowable(124, $a_auth)) {
			$row[] = "<span title='Click here to delete..' style='cursor:pointer' onclick='deleteAlert(" . $aRow["id"] . ", \"" . $aRow["code"] . "\")'><img src='../images/delete_24.png' /></span>";					
		}
		else {
			$row[] = "&nbsp;";
		}			
		
		# --- eo my code --- #
				
		$output['aaData'][] = $row;				
	}
	
	echo json_encode( $output );
?>