<?php	
	
	@session_start();
	
	$toChange = isset($_POST["toChange"]) ? $_POST["toChange"] : "";
	$changeTo = isset($_POST["changeTo"]) ? $_POST["changeTo"] : "";
	
	if ($toChange) {
		$_SESSION[$toChange] = $changeTo;
		@session_write_close();
	}
	
?>