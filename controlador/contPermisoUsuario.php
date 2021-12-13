<?php
require("../modelo/clsPermisoUsuario.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objPermisoUsuario = new clsPermisoUsuario($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "ACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objPermisoUsuario->activarPermisoUsuario($_POST["txtIdSucursal"], $_POST["txtIdPerfil"], $_POST["txtIdOpcionMenu"], $_POST["txtDescripcion"], $_POST["txtIdTabla"], $_POST["txtAccion"]));
		break;
	case "DESACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objPermisoUsuario->desactivarPermisoUsuario($_POST["txtIdSucursal"], $_POST["txtIdPerfil"], $_POST["txtIdOpcionMenu"], $_POST["txtDescripcion"], $_POST["txtIdTabla"], $_POST["txtAccion"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>