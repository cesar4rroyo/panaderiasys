<?php
require("../modelo/clsMarca.php");
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
$objMarca = new clsMarca($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//echo umill($objMarca->insertarMarca($_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_SESSION['R_IdSucursal']));
		$existe=$objMarca->verificaExisteDescripcion($_POST["txtDescripcion"]);
		if($existe==0){
			$rst=$objMarca->insertarMarca(strtoupper($_POST["txtDescripcion"]), strtoupper($_POST["txtAbreviatura"]), $_SESSION['R_IdSucursal']);
			$dax = $rst->fetchObject();
			$idregistro = $dax->idmarca;
			//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Descripcion=>'.strtoupper($_POST["txtDescripcion"]).'; Abreviatura=>'.strtoupper($_POST["txtAbreviatura"]), $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Marca","IdMarca",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objMarca->actualizarMarca($_POST["txtId"], strtoupper($_POST["txtDescripcion"]), strtoupper($_POST["txtAbreviatura"]), $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Descripcion=> De: '. $dax->descripcion.' a: '.strtoupper($_POST["txtDescripcion"]).'; Abreviatura=> De: '. $dax->abreviatura. ' a: '.strtoupper($_POST["txtAbreviatura"]), $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objMarca->eliminarMarca($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>