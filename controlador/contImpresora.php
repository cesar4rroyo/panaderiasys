<?php
require("../modelo/clsImpresora.php");
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
$objImpresora = new clsImpresora($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//echo umill($objMarca->insertarMarca($_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_SESSION['R_IdSucursal']));
		$existe=$objImpresora->verificaExisteDescripcion($_POST["txtNombre"]);
		if($existe==0){
			$rst=$objImpresora->insertarImpresora($_POST["txtNombre"],$_POST["txtIp"], $_SESSION['R_IdSucursal']);
			$dax = $rst->fetchObject();
			$idregistro = $dax->idimpresora;
			//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Nombre=>'.$_POST["txtNombre"].',Ip=>'.$_POST["txtIp"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Impresora","IdImpresora",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objImpresora->actualizarImpresora($_POST["txtId"], $_POST["txtNombre"],$_POST["txtIp"],$_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'txtNombre=> De: '. $dax->nombre.' a: '.$_POST["txtNombre"].',Ip=> De: '.$dax->ip.' a: '.$_POST["txtIp"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objImpresora->eliminarImpresora($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>