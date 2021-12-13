<?php
ini_set('memory_limit', '512M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB

echo "Realizando Copia de Seguridad de SISREST<BR><BR>Porfavor espere un momento...";
// example on using PHPMailer with GMAIL

include("class.phpmailer.php");
include("class.smtp.php"); // note, this is optional - gets called from main class if not already loaded

$mail             = new PHPMailer();

//$body             = file_get_contents('contents.html');
//$body             = preg_replace('/\\\\/','', $body); //Strip backslashes
//$mail->SMTPDebug  = 1;
$mail->IsSMTP();
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 465;                   // set the SMTP port

$mail->Username   = "backups@miuragrill.com.pe";  // GMAIL username
$mail->Password   = "miurabackup";            // GMAIL password

$mail->From       = "backups@miuragrill.com.pe";
$mail->FromName   = "Backup WebMaster SISREST";
$mail->Subject    = "Backup SISREST ".date('d-m-Y');
//$body  = "Hola <strong>amigo</strong><br>";
//$body .= "probando <i>PHPMailer<i>.<br><br>";
//$body .= "<font color='red'>Saludos</font>";
$body  = "Backup SISREST ".date('d-m-Y');
$mail->Body = $body;
//$mail->AltBody = "Hola amigo\nprobando PHPMailer\n\nSaludos";
//$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
$mail->WordWrap   = 50; // set word wrap

//$mail->MsgHTML($body);

//$mail->AddReplyTo("geynen_0710@hotmail.com","Webmaster");

$mail->AddAttachment("../BACKUP_RESTAURANTE.backup");             // attachment
//$mail->AddAttachment("/path/to/file.zip");             // attachment
//$mail->AddAttachment("/path/to/image.jpg", "new.jpg"); // attachment

$mail->AddAddress("backups@miuragrill.com.pe","Backup WebMaster SISREST");

$mail->IsHTML(true); // send as HTML
echo 'ok';
if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Copia de Seguridad Enviada Correctamente";
  echo "<script>
	window.open('','_parent','');
	window.close(); 
	</script>";
}
//NO OLVIDAR DE ACTIVAR EL SSL EN EL PHP.INI
//extension=php_openssl.dll
//echo phpinfo();
?>