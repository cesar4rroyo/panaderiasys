<?php
require("../modelo/clsCampo.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objCampo = new clsCampo($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objCampo->insertarCampo($_POST["txtIdTabla"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtLongitud"], $_POST["txtDiccionario"], $_POST["chkValidacion"], $_POST["txtMsgValidacion"], $_POST['txtLongitudReporte'], $_POST['txtAlineacionReporte']));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objCampo->actualizarCampo($_POST["txtId"], $_POST["txtIdTabla"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtLongitud"], $_POST["txtDiccionario"], $_POST["chkValidacion"], $_POST["txtMsgValidacion"], $_POST['txtLongitudReporte'], $_POST['txtAlineacionReporte']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objCampo->eliminarCampo($_POST["txtIdTabla"], $_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>