<?php
require("../modelo/clsOpcionMenu.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objOpcionMenu = new clsOpcionMenu($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objOpcionMenu->insertarOpcionMenu($_POST["cboIdModulo"], $_POST["txtDescripcion"], $_POST["cboIdMenuPrincipal"], $_POST["cboIdTabla"], $_POST["txtAccion"], $_POST["txtDiccionario"], $_POST["chkWAP"]));
		break;
		
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objOpcionMenu->actualizarOpcionMenu($_POST["txtId"], $_POST["cboIdModulo"], $_POST["txtDescripcion"], $_POST["cboIdMenuPrincipal"], $_POST["cboIdTabla"], $_POST["txtAccion"], $_POST["txtDiccionario"], $_POST["chkWAP"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objOpcionMenu->eliminarOpcionMenu($_POST["txtId"]));
		break;
	case "SUBIR" :
		if(ob_get_length()) ob_clean();
		echo umill($objOpcionMenu->subirOpcionMenu($_POST["txtId"]));
		break;
	case "BAJAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objOpcionMenu->bajarOpcionMenu($_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>