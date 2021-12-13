<?php

// example on using PHPMailer with GMAIL

include("class.phpmailer.php");
include("class.smtp.php"); // note, this is optional - gets called from main class if not already loaded

$mail             = new PHPMailer();

//$body             = file_get_contents('contents.html');
//$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

$mail->IsSMTP();
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 465;                   // set the SMTP port

$mail->Username   = "sistemas@miuragrill.com.pe";  // GMAIL username
$mail->Password   = "miuraadmin";            // GMAIL password

$mail->From       = "sistemas@miuragrill.com.pe";
$mail->FromName   = "Webmaster";
$mail->Subject    = "Mensaje de Prueba";
$body  = "Hola <strong>amigo</strong><br>";
$body .= "probando <i>PHPMailer<i>.<br><br>";
$body .= "<font color='red'>Saludos</font>";
$mail->Body = $body;
//$mail->AltBody = "Hola amigo\nprobando PHPMailer\n\nSaludos";
//$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
$mail->WordWrap   = 50; // set word wrap

//$mail->MsgHTML($body);

//$mail->AddReplyTo("geynen_0710@hotmail.com","Webmaster");

//$mail->AddAttachment("/path/to/file.zip");             // attachment
//$mail->AddAttachment("/path/to/image.jpg", "new.jpg"); // attachment

$mail->AddAddress("geynen_0710@hotmail.com","First Last");

$mail->IsHTML(true); // send as HTML

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message has been sent";
}

?>
