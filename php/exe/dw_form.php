<?php

	switch ($ac) {
		case "dwform":
			header('Content-disposition: attachment; filename=article_sample.xls');
			#header('Content-type: application/vnd.ms-excel');
			header('Content-type: application/octet-stream');
			readfile('sample/article_sample.xls');					

			break;
		
		case "dwform-sales":
			header('Content-disposition: attachment; filename=sales_sample.xls');
			#header('Content-type: application/vnd.ms-excel');
			header('Content-type: application/octet-stream');
			readfile('sample/sales_sample.xls');					

			break;
		
		case "dwform-brand":
			header('Content-disposition: attachment; filename=brand_sample.xls');
			#header('Content-type: application/vnd.ms-excel');
			header('Content-type: application/octet-stream');
			readfile('sample/brand_sample.xls');					

			break;
		
		case "dwlog":
			$myfile = fopen($id, "r") or die ("Unable to open file!");
			echo fread($myfile, filesize($id));
			fclose($myfile);

			break;
		
		default:
			break;			
	}
?>