<?php
require("../modelo/clsCaja.php");
require("../modelo/clsBitacora.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objCaja = new clsCaja($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$existe=$objCaja->verificaExisteNumero($_POST["txtNumero"]);
		if($existe==0){
			$rst=$objCaja->insertarCaja(strtoupper($_POST["txtNumero"]), $_POST["cboIdSalon"],  $_SESSION['R_IdSucursal']);
			$dax = $rst->fetchObject();
			$idregistro = $dax->idcaja;
		//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Numero=>'.strtoupper($_POST["txtNumero"]).'; IdSalon=>'.$_POST["cboIdSalon"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Caja","IdCaja",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objCaja->actualizarCaja($_POST["txtId"], $_POST["txtNumero"], $_POST["cboIdSalon"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Numero=> De: '. $dax->numero.' a: '.strtoupper($_POST["txtNumero"]).'; IdSalon=> De: '. $dax->idsalon. ' a: '.strtoupper($_POST["cboIdSalon"]), $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objCaja->eliminarCaja($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>