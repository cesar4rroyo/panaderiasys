<?php
require("../modelo/clsMenuPrincipal.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objMenuPrincipal = new clsMenuPrincipal($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objMenuPrincipal->insertarMenuPrincipal($_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_POST["txtOrden"], $_POST['chkExpandido']));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objMenuPrincipal->actualizarMenuPrincipal($_POST["txtId"], $_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_POST["txtOrden"], $_POST['chkExpandido']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objMenuPrincipal->eliminarMenuPrincipal($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>