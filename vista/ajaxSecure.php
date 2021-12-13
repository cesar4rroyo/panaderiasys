<?php
session_start();
$codigo=strtoupper($_POST["CAPTCHA_CODE"]);
if($codigo!=$_SESSION['R_CAPTCHA_CODE']){
	echo "El código ingresado no coincide con la imagen";
}else{
	echo "SI";
}
?>