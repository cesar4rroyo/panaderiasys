<?php
require("../modelo/clsRelacionCampo.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objRelacionCampo = new clsRelacionCampo($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionCampo->insertarRelacionCampo($_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"], $_POST["txtDiccionario"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		if($_POST["chkMultiple"]=="S"){
			$multiple = "S";
		}else{
			$multiple = "N";
		}		
		echo umill($objRelacionCampo->actualizarRelacionCampo($_POST["txtId"], $_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"], $_POST["txtDiccionario"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionCampo->eliminarRelacionCampo($_POST["txtIdTabla"], $_POST["txtId"]));
		break;
	case "ACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionCampo->activarRelacionCampo($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdCampo"], $_POST["txtTipo"]));
		break;
	case "DESACTIVAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionCampo->desactivarRelacionCampo($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdCampo"], $_POST["txtTipo"]));
		break;
	case "SUBIR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionCampo->subirRelacionCampo($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdCampo"], $_POST["txtTipo"]));
		break;
	case "BAJAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionCampo->bajarRelacionCampo($_POST["txtIdSucursal"], $_POST["txtIdTabla"], $_POST["txtIdCampo"], $_POST["txtTipo"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>