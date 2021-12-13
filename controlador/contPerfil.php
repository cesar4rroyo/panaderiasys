<?php
require("../modelo/clsPerfil.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objPerfil = new clsPerfil($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objPerfil->insertarPerfil($_POST["txtIdSucursal"], $_POST["txtDescripcion"], $_POST["txtAbreviatura"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objPerfil->actualizarPerfil($_POST["txtId"], $_POST["txtIdSucursal"], $_POST["txtDescripcion"], $_POST["txtAbreviatura"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objPerfil->eliminarPerfil($_POST["txtId"], $_POST["txtIdSucursal"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>