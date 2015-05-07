<?php

    // set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');
	
	require_once(dirname(__FILE__) . "/../php/util/ConfigReader.php");
	include_once(dirname(__FILE__) . "/../php/conf/ftp.conf.man.php");
	
	$CR = new ConfigReader("db.conf.php");
	
	$jUser = $CR->get("#user");
	$jPass = $CR->get("#pass");
	$jDb = $CR->get("#dbname");
	$jHost = $CR->get("#host");
    
    $timeIdx = (isset($argv[1]) ? $argv[1] : 0);
    $testIdx = sizeof($TIME_EXEC)-1;
    if ($timeIdx > $testIdx) {
        echo "Invalid argument. Maximum argument allowed is $testIdx.\n";
        exit;
    }
    $timeExec = $dateToProcess . $TIME_EXEC[$timeIdx];
    
    echo "start job at " . date("Y-m-d H:i:s") . "\n"; 
    echo "checking store list..";
    
    $msgToSend = "";
        
    # store live
    if (isset($STORE_LIVE)) {
    
        echo " checked.\n";
        
        $COLLECTED_FILE = array();
        
        # -- FTP JOB
        echo "preparing ftp job..\n";
        echo "loop through store list..\n";
        
        # loop through store live
        for ($i = 0; $i < sizeof($STORE_LIVE); $i++) {
            
            $store = $STORE_LIVE[$i];
            $server_file = $timeExec . "__." . $store; 
            
            $no = $i + 1;
            echo $no . ". " . $store . " -> file: " . $server_file . "\n";
            
            # start ftp job
            $ftp_server = $FTP_SERVER[$store]["IP"];
            echo "    start ftp to $ftp_server..";
            
            if ($ftp_conn = ftp_connect($ftp_server)) {
                echo " connected.\n";
            }
            else {
                $msgToSend .= "Failed to connect to " . $ftp_server . ".<br>";
                echo " failed to connect.\n";
                # continue to next server
                continue;
            }
            
            echo "    try to login..";
            if ($login = ftp_login($ftp_conn, $FTP_SERVER[$store]["USER"], $FTP_SERVER[$store]["PASSWD"])) {
                echo " connection established.\n";
            }
            else {
                $msgToSend .= "Failed to login to " . $ftp_server . ".<br>";
                echo "couldn't establish a connection.\n";
                // close connection and file handler
                ftp_close($ftp_conn);
                # continue to next server
                continue;
            }
            
            echo "    change dir to " . $FTP_SERVER[$store]["DIR"] . "..";
            // change the current directory to php
            ftp_chdir($ftp_conn, $FTP_SERVER[$store]["DIR"]);
            echo " changed.\n";
            
            echo "    looking for file..";
            $found = false;
            // get file list of current directory
            $file_list = ftp_nlist($ftp_conn, ".");
            // need this, because we don't know file name exactly
            foreach ($file_list as $file) {
                if ((substr($file, 0, 10) == $timeExec) && (substr($file, 13, 3) == $store)) {
                    $server_file = $file;
                    echo " got " . $file . ".\n";
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                echo "    checking file..";
            
                // get size of $file
                $fsize = ftp_size($ftp_conn, $server_file);
                if ($fsize == -1) {
                    $msgToSend .= "Failed to getting file size of " . $server_file . ".<br>";
                    echo " error getting file size.\n";    
                }
                else {
                    // open local file to write to
                    $local_file = $FORGE . "/" . $server_file;
                    $fp = fopen($local_file, "w");
                    
                    echo "\n    downloading file..";
                    
                    // download server file and save it to open local file
                    if (ftp_fget($ftp_conn, $fp, $server_file, FTP_ASCII, 0)) {
                        echo " $fsize bytes downloaded successfully.\n";
                        # collect file name
                        $COLLECTED_FILE[] = $server_file;
                    }
                    else {
                        $msgToSend .= "Failed to download file " . $server_file . ".<br>";
                        echo " failed.\n";
                    }
                    
                    fclose($fp);
                }    
            }
            else {
                $msgToSend .= "File " . $server_file . " not found.<br>";
                echo " not found.\n";
            }
            
            // close connection and file handler
            ftp_close($ftp_conn);
            
            echo "    leave ftp.\n";
        }
        
        echo "end loop.\n";
        echo "job done.. leave job.\n\n";
        # -- END FTP
        
        # -- ETL JOB
        if (!empty($COLLECTED_FILE)) {
            
            echo "preparing etl job..\n";
    
            echo "make connection to database.. ";
			# make connection
			$conn = mysql_connect($jHost, $jUser, $jPass);
			if (!$conn) {
                $msgToSend .= "Failed to connect to database.<br>";
				echo "Error: cannot connect to database. Leave job.\n";
				exit;
			}
			echo "connected.\n";
			echo "select database.. ";
			if (!mysql_select_db($jDb)) {
				mysql_close($conn);
                $msgToSend .= "Failed to use database.<br>";
				echo "Error: cannot use database. Leave job.\n";
				exit;
			}
			echo "selected.\n";
            # eo make connection
            
            for ($i = 0; $i < sizeof($COLLECTED_FILE); $i++) {
                $file = $COLLECTED_FILE[$i];
                $store = substr($file, -3);
                $dayToProcess = "";
                
                $no = $i + 1;
                echo $no . ". " . $file . ".\n";
                
                echo "start transaction.\n";
                $result = mysql_query("START TRANSACTION");
                
                echo "delete existing sales tpl data.. ";
                $sql = "delete from trn_sales_tpl where id_import in (select y.id from trn_sales_import y where y.filename = '" . mysql_real_escape_string($file) . "')";
                $result = mysql_query($sql);
                if ($result) {
                    echo "deleted.\n";
                }
                else {
                    $msgToSend .= "Failed to delete sales tpl data with filename " . mysql_real_escape_string($file) . ".<br>";
                    echo "Failed to delete sales tpl data with filename " .  mysql_real_escape_string($file) . ".\n";
                    $result = mysql_query("ROLLBACK");
                    echo "rollback.\n";
                    continue;
                }
                    
                # insert import table
                $sql = "insert into trn_sales_import (filename, created_by, created_date) values ('" . mysql_real_escape_string($file) . "', 'system', sysdate())";
                $result = mysql_query($sql);
                
                # get last insert id
                $seq = mysql_insert_id();
                
                $lineNumber = 0;
                $totalInsertedRow = 0;
                $filePath = $FORGE . "/" . $file;
                $lines = file($filePath);
                foreach ($lines as $line) {
                    
                    # prevent PHP Fatal error:  Can't use function return value in write context
                    $line = trim($line);
                    if (!empty($line)) {
                        
                        $DATA = explode("|", $line);    
                        
                        $transDate = trim((isset($DATA[0]) ? $DATA[0] : ""));
                        $hourMinute = trim((isset($DATA[1]) ? $DATA[1] : ""));
                        $tplPlu = trim((isset($DATA[2]) ? $DATA[2] : ""));
                        $sku = trim((isset($DATA[3]) ? $DATA[3] : ""));
                        $goldPlu = trim((isset($DATA[4]) ? $DATA[4] : ""));
                        $dept = trim((isset($DATA[5]) ? $DATA[5] : ""));
                        $unitPrice = trim((isset($DATA[6]) ? $DATA[6] : 0));
                        $qty = trim((isset($DATA[7]) ? $DATA[7] : 0));
                        $grossSale = trim((isset($DATA[8]) ? $DATA[8] : 0));
                        $disc = trim((isset($DATA[9]) ? $DATA[9] : 0));
                        
                        $lineNumber++;
                        
                        # insert sales table
                        $sql =  "insert into trn_sales_tpl (" .
                                    "id_import, store_init, trans_date, tpl_plu, sku, gold_plu, dept, unit_price, qty, gross_sale, disc" .
                                ") values ('" .
                                    $seq . "', '" . mysql_real_escape_string($store) . "', str_to_date('" . $transDate . "', '%d/%m/%y'), '" . mysql_real_escape_string($tplPlu) . "', '" .
                                    mysql_real_escape_string($sku) . "', '" . mysql_real_escape_string($goldPlu) . "', '" . mysql_real_escape_string($dept) . "', '" .
                                    (is_numeric($unitPrice) ? $unitPrice : 0) . "', '" . (is_numeric($qty) ? $qty : 0) . "', '" . (is_numeric($grossSale) ? $grossSale : 0) . "', '" .
                                    (is_numeric($disc) ? $disc : 0) . "'" .
                                ")";
                        $result = mysql_query($sql);
                        if ($result) {
                            $totalInsertedRow++;
                            $dayToProcess = $transDate;
                        }
                        else {
                            $msgToSend .= "Failed to insert data from file " . $file . " at line " . $lineNumber . ".<br>";
                            echo "Failed to insert data at line " . $lineNumber . ".\n";
                            continue;
                        }
                    }
                    
                }
                
                $result = mysql_query("COMMIT");
                echo "commit.\n";
                
                echo "total inserted row: " . $totalInsertedRow . ".\n";
                
                # -- create sales by brand
                
                if ($dayToProcess != "") {
                    echo "start sales by brand process.\n";
                    
                    echo "start transaction.\n";
                    $result = mysql_query("START TRANSACTION");
                    
                    echo "delete existing sales by brand data.. ";
                    $sql = "delete from trn_sales_by_brand where date_format(trans_date, '%d/%m/%y') = '" . mysql_real_escape_string($dayToProcess) . "' and store_init = '" . mysql_real_escape_string($store) . "'";
                    $result = mysql_query($sql);
                    if ($result) {
                        echo "deleted.\n";
                    }
                    else {
                        $msgToSend .= "Failed to delete sales by brand data at trans date " . $dayToProcess . " and store " . $store . ".<br>";
                        echo "Failed to delete sales by brand data at trans date " . $dayToProcess . " and store " . $store . ".\n";
                        $result = mysql_query("ROLLBACK");
                        echo "rollback.\n";    
                        continue;
                    }
                    
                    echo "insert replacer data.. ";
                    $sql = "insert into trn_sales_by_brand (store_init, trans_date, brand_name, division, qty, amount, last_update) ";
                    $sql .= "select x.store_init, x.trans_date, y.brand_name, w.name, sum(x.qty), sum(x.gross_sale-x.disc), sysdate() " .
                            "from trn_sales_tpl x " .
                            "inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date) " . 
                            "inner join mst_division w on y.division = w.code " . 
                            "where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code) " . 
                            "and store_init = '" . mysql_real_escape_string($store) . "' and date_format(x.trans_date, '%d/%m/%y') = '" . mysql_real_escape_string($dayToProcess) . "' " .
                            "group by x.trans_date, y.brand_name, w.name, store_init";
                    $result = mysql_query($sql);
                    if ($result) {
                        echo "inserted.\n";
                    }
                    else {
                        $msgToSend .= "Failed to insert sales by brand data at trans date " . $dayToProcess . " and store " . $store . ".<br>";
                        echo "Failed to insert sales by brand data at trans date " . $dayToProcess . " and store " . $store . ".\n";
                        $result = mysql_query("ROLLBACK");
                        echo "rollback.\n";    
                        continue;
                    }
                    
                    $result = mysql_query("COMMIT");
                    echo "commit.\n";
                    
                    echo "end sales by brand.\n";
                }
                
                # -- end sales by brand
                
            } # end for
            
			echo "close connection.\n";			
			# close connection
			mysql_close($conn);
        
            echo "job done.. leave job.\n\n";    
        }
        # -- END ETL

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
        
        $mail->Subject = "Space Intelligence SALES Job Error";
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