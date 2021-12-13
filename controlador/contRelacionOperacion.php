<?php
require("../modelo/clsRelacionOperacion.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objRelacionOperacion = new clsRelacionOperacion($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacion->insertarRelacionOperacion($_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"], $_POST["txtVerSi"]));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		if($_POST["chkMultiple"]=="S"){
			$multiple = "S";
		}else{
			$multiple = "N";
		}		
		echo umill($objRelacionOperacion->actualizarRelacionOperacion($_POST["txtId"], $_POST["txtIdTabla"], $_POST["optTipo"], $_POST["txtDescripcion"], $_POST["txtComentario"], $_POST["txtAccion"], $_POST["txtImagen"], $_POST["txtVerSi"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objRelacionOperacion->eliminarRelacionOperacion($_POST["txtIdTabla"], $_POST["txtId"]));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>