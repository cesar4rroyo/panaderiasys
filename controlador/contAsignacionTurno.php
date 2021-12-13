<?php
require("../modelo/clsAsignacionTurno.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objAsignacionTurno = new clsAsignacionTurno($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objAsignacionTurno->insertarAsignacionTurno($_POST['txtIdSucursalPersona'], $_POST["txtIdPersona"], $_POST["cboIdTurno"], $_POST["cboIdCaja"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objAsignacionTurno->actualizarAsignacionTurno($_POST["txtId"], $_POST['txtIdSucursalPersona'], $_POST["txtIdPersona"], $_POST["cboIdTurno"], $_POST["cboIdCaja"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objAsignacionTurno->eliminarAsignacionTurno($_POST["txtId"]));
		break;
	case "ACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objAsignacionTurno->ActivarAsignacionTurno($_POST["txtId"]));
		break;
	case "DESACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objAsignacionTurno->DesactivarAsignacionTurno($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>