<?php
require("../modelo/clsTipoCambio.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objTipoCambio = new clsTipoCambio($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objTipoCambio->insertarTipoCambio($_SESSION['R_IdSucursal'], $_POST["txtMonto"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objTipoCambio->actualizarTipoCambio($_POST["txtId"], $_SESSION['R_IdSucursal'], $_POST["txtMonto"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objTipoCambio->eliminarTipoCambio($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>