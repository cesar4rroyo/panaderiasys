<?php
require("../modelo/clsCategoriaParametro.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objCategoriaParametro = new clsCategoriaParametro($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objCategoriaParametro->insertarCategoriaParametro(strtoupper($_POST["txtDescripcion"])));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objCategoriaParametro->actualizarCategoriaParametro($_POST["txtId"], strtoupper($_POST["txtDescripcion"])));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objCategoriaParametro->eliminarCategoriaParametro($_POST["txtId"]));
		break;
	case "SUBIR" :
		if(ob_get_length()) ob_clean();
		echo umill($objOpcionMenu->subirCategoriaParametro($_POST["txtId"]));
		break;
	case "BAJAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objOpcionMenu->bajarCategoriaParametro($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>