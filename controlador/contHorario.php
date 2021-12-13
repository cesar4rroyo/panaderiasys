<?php
require("../modelo/clsHorario.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objHorario = new clsHorario($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		foreach($_POST["cboDia"] as $semana => $dia){
		$objHorario->insertarHorario($_POST["txtIdSucursal"], $dia[0], $_POST["txtHoraInicio"], $_POST["txtHoraFin"]);
		}
		echo "Guardado Correctamente";
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objHorario->actualizarHorario($_POST["txtId"], $_POST["txtIdSucursal"], $_POST["cboDia"], $_POST["txtHoraInicio"], $_POST["txtHoraFin"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objHorario->eliminarHorario($_POST["txtId"], $_POST["txtIdSucursal"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>