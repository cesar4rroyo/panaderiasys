<?php
require("../modelo/clsRolPersona.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objRolPersona = new clsRolPersona($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objRolPersona->insertarRolPersona($_POST['txtIdSucursal'],$_POST["txtIdPersona"], $_POST["cboIdRol"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRolPersona->actualizarRolPersona($_POST["txtId"], $_POST['txtIdSucursal'],$_POST["txtIdPersona"], $_POST["cboIdRol"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRolPersona->eliminarRolPersona($_POST["txtId"],$_POST['txtIdSucursal']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>