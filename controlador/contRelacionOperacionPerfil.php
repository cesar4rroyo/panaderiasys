<?php
require("../modelo/clsRelacionOperacionPerfil.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objRelacionOperacionPerfil = new clsRelacionOperacionPerfil($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionPerfil->insertarRelacionOperacionPerfil($_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		if($_POST["chkMultiple"]=="S"){
			$multiple = "S";
		}else{
			$multiple = "N";
		}		
		echo umill($objRelacionOperacionPerfil->actualizarRelacionOperacionPerfil($_POST["txtId"], $_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionPerfil->eliminarRelacionOperacionPerfil($_POST["txtIdTabla"], $_POST["txtId"]));
		break;
	case "ACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionPerfil->activarRelacionOperacionPerfil($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtIdPerfil"]));
		break;
	case "DESACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionPerfil->desactivarRelacionOperacionPerfil($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtIdPerfil"]));
		break;
	case "SUBIR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionPerfil->subirRelacionOperacionPerfil($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtIdPerfil"]));
		break;
	case "BAJAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacionPerfil->bajarRelacionOperacionPerfil($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdOperacion"], $_POST["txtIdPerfil"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>