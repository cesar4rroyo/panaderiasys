<?php
require("../modelo/clsRelacionOperacionSucursal.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objRelacionOperacionSucursal = new clsRelacionOperacionSucursal($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionSucursal->insertarRelacionOperacionSucursal($_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		if($_POST["chkMultiple"]=="S"){
			$multiple = "S";
		}else{
			$multiple = "N";
		}		
		echo umill($objRelacionOperacionSucursal->actualizarRelacionOperacionSucursal($_POST["txtId"], $_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionSucursal->eliminarRelacionOperacionSucursal($_POST["txtIdTabla"], $_POST["txtId"]));
		break;
	case "ACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionSucursal->activarRelacionOperacionSucursal($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtTipo"]));
		break;
	case "DESACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionSucursal->desactivarRelacionOperacionSucursal($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtTipo"]));
		break;
	case "SUBIR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionSucursal->subirRelacionOperacionSucursal($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtTipo"]));
		break;
	case "BAJAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionSucursal->bajarRelacionOperacionSucursal($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtTipo"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>