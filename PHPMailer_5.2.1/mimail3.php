<?php
require("../class.phpmailer.php");
$mail = new PHPMailer();
$mail->Host = "localhost";
 
$mail->From = "geynen@gmail.com";
$mail->FromName = "Nombre del Remitente";
$mail->Subject = "Subject del correo";
$mail->AddAddress("sistemas@miuragrill.com.pe","Nombre 01");
//$mail->AddAddress("destino2@correo.com","Nombre 02");
//$mail->AddCC("usuariocopia@correo.com");
//$mail->AddBCC("usuariocopiaoculta@correo.com");
 
$body  = "Hola <strong>amigo</strong><br>";
$body .= "probando <i>PHPMailer<i>.<br><br>";
$body .= "<font color='red'>Saludos</font>";
$mail->Body = $body;
$mail->AltBody = "Hola amigo\nprobando PHPMailer\n\nSaludos";
//$mail->AddAttachment("images/foto.jpg", "foto.jpg");
//$mail->AddAttachment("files/demo.zip", "demo.zip");
$mail->Send();
?>