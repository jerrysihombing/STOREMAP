<?php        
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
        $mail->Body    = "test";
        $mail->AltBody = "test";
        
        if (!$mail->send()) {
            echo "message could not be sent.\n";
            echo "mailer Error: " . $mail->ErrorInfo . ".\n\n";
        } else {
            echo "message has been sent.\n\n";
        }
?>