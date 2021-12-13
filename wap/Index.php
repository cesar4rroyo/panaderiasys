<?php
session_start();
$_SESSION['R_Version']='1.0';
$_SESSION['R_ini_ses']="si";
if(!isset($_SESSION['R_ContSecure'])){$_SESSION['R_ContSecure']=0;}
$_SESSION['R_Estilo']='estiloazul';
header("Location: login.php");
exit();
?>