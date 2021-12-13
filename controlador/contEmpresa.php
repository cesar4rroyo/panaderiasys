<?php
require("../modelo/clsEmpresa.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objEmpresa = new clsEmpresa($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objEmpresa->insertarEmpresa($_POST["txtNombreEmpresa"], $_POST["txtDireccion"], $_POST["txtRuc"], $_POST["txtEmail"], $_POST["txtTelefonoFijo"], $_POST["txtTelefonoMovil"], $_POST["txtFax"], $_POST["txtLogo"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objEmpresa->actualizarEmpresa($_POST["txtId"], $_POST["txtNombreEmpresa"], $_POST["txtDireccion"], $_POST["txtRuc"], $_POST["txtEmail"], $_POST["txtTelefonoFijo"], $_POST["txtTelefonoMovil"], $_POST["txtFax"], $_POST["txtLogo"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objEmpresa->eliminarEmpresa($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>