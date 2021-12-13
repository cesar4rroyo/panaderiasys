<?php
session_start();
require_once("verificaNavegador.php");
$_SESSION['R_Version']='2.0';
$_SESSION['R_ini_ses']="si";
$_SESSION['R_origen_ses']="E"; //I->INTERNO (quiere decir que se logeo); E->Externo (solo prar usuarios externos)
if(!isset($_SESSION['R_ContSecure'])){$_SESSION['R_ContSecure']=0;}
$_SESSION['R_Estilo']='estiloazul';
//usuario cloud solo cuando se trabaje con roles del postgres
$_SESSION['R_NombreUsuarioCloud']='geynen';
$_SESSION['R_ClaveCloud']=md5('123');
$_SESSION['R_UltimoAcceso']=NULL;
$_SESSION['R_Inactividad']=0;//ILIMITADO
$_SESSION["R_NroFilaMostrar"]=10;
// Mostrar contenido para navegadores normales  
header("Location: login.php");
exit();
?>