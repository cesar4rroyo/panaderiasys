<?php
require("../modelo/clsBanco.php");
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
$objBanco = new clsBanco($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//echo umill($objMarca->insertarMarca($_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_SESSION['R_IdSucursal']));
		$existe=$objBanco->verificaExisteDescripcion($_POST["txtDescripcion"]);
		if($existe==0){
			$rst=$objBanco->insertarBanco(strtoupper($_POST["txtDescripcion"]), $_SESSION['R_IdSucursal']);
			$dax = $rst->fetchObject();
			$idregistro = $dax->idbanco;
			//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Descripcion=>'.strtoupper($_POST["txtDescripcion"]), $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Banco","IdBanco",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objBanco->actualizarBanco($_POST["txtId"], strtoupper($_POST["txtDescripcion"]),$_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Descripcion=> De: '. $dax->descripcion.' a: '.strtoupper($_POST["txtDescripcion"]), $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objBanco->eliminarBanco($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>