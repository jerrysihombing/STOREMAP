<?php
    
    // set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Jakarta');
	
	require_once(dirname(__FILE__) . "/../php/util/ConfigReader.php");
    
    $CR = new ConfigReader("db.conf.php");
	
	$jUser = $CR->get("#user");
	$jPass = $CR->get("#pass");
	$jDb   = $CR->get("#dbname");
	$jHost = $CR->get("#host");
    $webService = $CR->get("#webservice");
	
	$pDate = date("d-M-y", strtotime("-1 days"));
	#$pDate = "18-Feb-15";
	
    echo "start job at " . date("Y-m-d H:i:s") . "\n";
    
    $msgToSend = "";
    
	$url = $webService . "/ws_gold_article_by_date.php?p_date=" . $pDate;
	
	echo "read data from web service.. ";
    
	$json = @file_get_contents($url);
	if ($json) {
		echo "succeeded.\n";
		
		$obj = json_decode($json);
		
		echo "read articles data.. ";
		if ($obj->articles) {
			
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
            
            $totalInsertedRow = 0;
            
			echo "loop through data.\n";
			
			foreach ($obj->articles as $row) {
			
				$articleCode = $row->ARTICLE_CODE;
                $articleDesc = $row->ARTICLE_DESC;
                $tipo = $row->TIPO;
                $uom = $row->UOM;
                $brandCode = $row->BRAND_CODE;
                $brandDesc = $row->BRANCD_DESC;
				$division = $row->DIVISION;
                $startDate = $row->START_DATE;
                $endDate = $row->END_DATE;
                $lastUpdate = $row->LAST_UPDATE;
                
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
			
			echo "end loop.\n";
			
			$result = mysql_query("COMMIT");
            echo "commit.\n";
            
            echo "total inserted row: " . $totalInsertedRow . ".\n";
            
            echo "close connection to destination database.\n";			
			# close connection
			mysql_close($connM);
			
		}
		else {
			echo "empty. Leave job.\n";
		}
	}
	else {
		$msgToSend .= "Invalid response from web service.<br>";
		echo "invalid response from web service. Leave job.\n";
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