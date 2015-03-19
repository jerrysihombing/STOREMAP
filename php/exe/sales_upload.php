<?php
	require_once(dirname(__FILE__) . "/../util/ConfigReader.php");
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');
	
	$CR = new ConfigReader("db.conf.php");
	$uploaddir = $CR->get("#uploaddir2");
	
	if (!file_exists($uploaddir)) {
		mkdir($uploaddir, 0777);
	}
	
	$uploaddir .= "/";
	
	# change this to target location
	define("UPLOAD_DIR", $uploaddir);
	
	function readErrorFileUpload($err) {
		switch ($err) {
			case 1:
			case 2:
				return "Maximum file size exceeded.";
				#return "Ukuran file tidak boleh melebihi 1MB.";
				break;
			
			case 3:
				return "File was only partially uploaded.";
				#return "File hanya di-upload sebagian.";
				break;
			
			case 4:
				return "No file was uploaded.";
				#return "Tidak ada file yang di-upload.";
				break;
			
			case 7:
				return "Failed to write file to disk.";
				#return "Gagal menyimpan file di disk.";
				break;
				
			default:
				return "Unknown error.";
				#return "Error tidak diketahui.";
				break;
		}
	}
	
	$allowedExts = array("xls");
	$temp = explode(".", $_FILES["trans_file"]["name"]);
	$extension = strtolower(end($temp));

	$msg = "";
	$error = "";
	$new_image_name = "";
	
	if ($_FILES["trans_file"]["error"] != UPLOAD_ERR_OK) {
		$error = readErrorFileUpload($_FILES["trans_file"]["error"]); 
	}
	else {
		if (!in_array($extension, $allowedExts)) {
			$error = "Invalid file. Must be \'.xls\' file. ";
		}
		else {
			$new_image_name = date("YmdHis") . "_sales_" . uniqid() . "." . $extension;
			
			$uploadfile = UPLOAD_DIR . $new_image_name;
			if (move_uploaded_file($_FILES["trans_file"]["tmp_name"], $uploadfile)) {
				$msg = "Uploading file done. ";
			}
		}
	}
	
	echo "{msg: '" . $new_image_name . "', error: '" . $error . "'}";
	
?>