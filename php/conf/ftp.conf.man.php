<?php

$dateToProcess = "20150420";

$FORGE = "/Users/user/Data/TEMP";

# -- store live --
$STORE_LIVE[] = "183";

# -- time execution
$TIME_EXEC[] = "13";
$TIME_EXEC[] = "17";
$TIME_EXEC[] = "06";

# -- YCW --
$FTP_SERVER["183"]["IP"] = "192.168.138.5";
$FTP_SERVER["183"]["USER"] = "tux";
$FTP_SERVER["183"]["PASSWD"] = "tux";
$FTP_SERVER["183"]["DIR"] = "/home3/import/" . $dateToProcess;

# -- ANOTHER STORE --

?>
