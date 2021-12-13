<?php
require("../modelo/clsRelacionTablaSucursal.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objRelacionTablaSucursal = new clsRelacionTablaSucursal($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionTablaSucursal->insertarRelacionTablaSucursal($_POST["cboIdTabla"], $_POST["txtIdSucursal"], $_POST["txtDescripcion"], $_POST["txtDescripcionMant"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		if($_POST["chkMultiple"]=="S"){
			$multiple = "S";
		}else{
			$multiple = "N";
		}		
		echo umill($objRelacionTablaSucursal->actualizarRelacionTablaSucursal($_POST["cboIdTabla"], $_POST["txtIdSucursal"], $_POST["txtDescripcion"], $_POST["txtDescripcionMant"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionTablaSucursal->eliminarRelacionTablaSucursal($_POST["txtId"], $_POST["txtIdSucursal"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>