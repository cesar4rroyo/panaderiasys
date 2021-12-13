<?php
require("../modelo/clsPersonaMaestro.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objPersonaMaestro = new clsPersonaMaestro($clase,$_SESSION['R_IdCliente'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objPersonaMaestro->insertarPersonaMaestro(strtoupper(trim($_POST["txtApellidos"])), strtoupper(trim($_POST["txtNombres"])), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objPersonaMaestro->actualizarPersonaMaestro($_POST["txtId"], strtoupper(trim($_POST["txtApellidos"])), strtoupper(trim($_POST["txtNombres"])), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objPersonaMaestro->eliminarPersonaMaestro($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>