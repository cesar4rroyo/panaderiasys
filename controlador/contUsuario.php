<?php
require("../modelo/clsUsuario.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objUsuario = new clsUsuario($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$existe=$objUsuario->verificaExisteNombreUsuario($_POST["txtNombreUsuario"]);
		if($existe==0){
			echo umill($objUsuario->insertarUsuario($_POST["txtIdSucursal"], 0, $_POST["txtNombreUsuario"], md5($_POST["txtPassword"]), $_POST["txtIdPersona"], $_POST["cboIdPerfil"], $_POST["txtNroFilaMostrar"], $_POST["txtOpcionMenuDefecto"]));
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		if($_POST["chkMultiple"]=="S"){
			$multiple = "S";
		}else{
			$multiple = "N";
		}		
		if(strlen($_POST["txtPassword"])==32){
			echo umill($objUsuario->actualizarUsuario($_POST["txtIdSucursal"], $_POST["txtId"], $_POST["txtNombreUsuario"], ($_POST["txtPassword"]), $_POST["txtIdPersona"], $_POST["cboIdPerfil"], $_POST["txtNroFilaMostrar"], $_POST["txtOpcionMenuDefecto"]));
		}else{
			echo umill($objUsuario->actualizarUsuario($_POST["txtIdSucursal"], $_POST["txtId"], $_POST["txtNombreUsuario"], md5($_POST["txtPassword"]), $_POST["txtIdPersona"], $_POST["cboIdPerfil"], $_POST["txtNroFilaMostrar"], $_POST["txtOpcionMenuDefecto"]));
		}
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objUsuario->eliminarUsuario($_POST["txtId"],$_POST["txtIdSucursal"]));
		break;
	case "CAMBIARCLAVE" :
		if(ob_get_length()) ob_clean();
		if(md5($_POST["txtClave"])==$_SESSION['R_Clave']){
			$_SESSION['R_Clave']=md5($_POST["txtNuevaClave"]);
			echo 'window.open("main.php","_self");alert("'.umill($objUsuario->cambiarclaveUsuario($_POST["txtIdCliente"], $_POST["txtId"], md5($_POST["txtNuevaClave"]))).'");';
		}else{
			echo 'alert("El password actual es incorrecto");';
		}
		break;		
	case "CAMBIARDATOSPERFIL" :
		if(ob_get_length()) ob_clean();
	
		$nombre_original=$_FILES['txtImagen']['name'];
		$tipo=$_FILES['txtImagen']['type'];
		$tam=$_FILES['txtImagen']['size'];
		$temporal=$_FILES['txtImagen']['tmp_name'];
		$rutafoto="";
		$rutafotodb="";
		if(!empty($nombre_original)){
			$nombre_carpeta = "../img/empresas/".$_SESSION["R_RucEmpresa"]; 
			$rutafoto=$nombre_carpeta."/usuario-".$_SESSION["R_RucEmpresa"]."-".$_POST["txtIdPersona"]."-".$_POST["txtIdCliente"].".jpg";
			$rutafotodb=$_POST["txtRucEmpresa"]."/usuario-".$_SESSION["R_RucEmpresa"]."-".$_POST["txtIdPersona"]."-".$_POST["txtIdCliente"].".jpg";
			copy($temporal,$rutafoto);
		}		
		
		require_once '../modelo/clsPersona.php';
		$objPersonal = new clsPersona(23,$_SESSION['R_IdCliente'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		
		//$objPersonaMaestro->actualizarPersonaMaestrodesdePerfil($_POST["txtIdPersonaMaestro"], $_POST["txtFechaNac"]);
		
		$objPersonal->actualizarPersonadesdePerfil($_POST["txtIdPersona"], $_POST["txtIdCliente"], $_POST["txtIdPersonaMaestro"], $_POST["txtDireccion"], $_POST["txtTelefonoFijo"], $_POST["txtTelefonoMovil"], $_POST["txtEmail"], $rutafotodb);

		$objUsuario->actualizarUsuariodesdePerfil($_POST["txtIdCliente"], $_POST["txtId"], $_POST["txtNroFilaMostrar"]);
		
		echo "<script>alert('Guardado correctamente');window.open('../main.php?idclase=16&ruta=vista/mantPerfilUsuario','_self');</script>";
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>