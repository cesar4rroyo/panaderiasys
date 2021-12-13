<?php
require("../modelo/clsUnidad.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objUnidad = new clsUnidad($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objUnidad->insertarUnidad($_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_POST["optTipo"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objUnidad->actualizarUnidad($_POST["txtId"], $_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_POST["optTipo"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objUnidad->eliminarUnidad($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>