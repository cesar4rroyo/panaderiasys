<?php
require("../modelo/clsTipoDocumento.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objTipoDocumento = new clsTipoDocumento($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objTipoDocumento->insertarTipoDocumento($_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_POST["txtStock"], $_POST["cboIdTipoMovimiento"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objTipoDocumento->actualizarTipoDocumento($_POST["txtId"], $_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_POST["txtStock"], $_POST["cboIdTipoMovimiento"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objTipoDocumento->eliminarTipoDocumento($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>