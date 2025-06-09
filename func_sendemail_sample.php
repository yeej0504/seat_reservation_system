<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__.'/PHPMailer/src/Exception.php');
require_once(__DIR__.'/PHPMailer/src/PHPMailer.php');
require_once(__DIR__.'/PHPMailer/src/SMTP.php');



// SMTP Configs
$smtp_host     = 'student.nsysu.edu.tw';    // SMTP Server
$smtp_port     = 25;                        // SMTP Port
$smtp_username = 'M133040097';              // SMTP Username
$smtp_password = 'Ns@510904';               // SMTP Password

function sendemail_sample($sender_email, $sender_name, $recipient_email, $recipient_name, $subject, $body) {

    // Sample for DBS 2025
    
    // SMTP Configs - Global Parameters
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password;

    // UTF-8 Encode
    date_default_timezone_set('Asia/Taipei');
    mb_internal_encoding('UTF-8');

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $smtp_host;                             //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $smtp_username;                         //SMTP username
        $mail->Password   = $smtp_password;                         //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          //Enable implicit TLS encryption
        $mail->Port       = $smtp_port;                             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($sender_email, mb_encode_mimeheader($sender_name,"UTF-8"));
        $mail->addAddress($recipient_email, mb_encode_mimeheader($recipient_name,"UTF-8"));  //Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(false);                                  //Set email format to HTML
        $mail->Subject = mb_encode_mimeheader($subject,"UTF-8");
        $mail->Body    = $body;

        $mail->send();
        return '寄信成功!';
    } catch (Exception $e) {
        return "寄信失敗，失敗原因: {$mail->ErrorInfo}";
    }
}
?>