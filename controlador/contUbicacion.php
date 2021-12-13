<?php
require("../modelo/clsUbicacion.php");
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
$objUbicacion = new clsUbicacion($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//echo umill($objUbicacion->insertarUbicacion($_POST["txtCodigo"], $_POST["txtNombre"], $_POST["txtTotalColumnas"], $_POST["txtTotalFilas"], $_SESSION['R_IdSucursal']));
		$existe=$objUbicacion->verificaExisteCodigo($_POST["txtCodigo"]);
		if($existe==0){
		$rst= $objUbicacion->insertarUbicacion(strtoupper($_POST["txtCodigo"]), strtoupper($_POST["txtNombre"]), $_POST["txtTotalColumnas"], $_POST["txtTotalFilas"], $_SESSION['R_IdSucursal']);
		$dax = $rst->fetchObject();
			$idregistro = $dax->idubicacion;
			//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Codigo=>'.strtoupper($_POST["txtCodigo"]).'; Nombre=>'.strtoupper($_POST["txtNombre"]).'; TotalColumnas=>'.$_POST["txtTotalColumnas"].'; TotalFilas=>'.$_POST["txtTotalFilas"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Ubicacion","IdUbicacion",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objUbicacion->actualizarUbicacion($_POST["txtId"], strtoupper($_POST["txtCodigo"]), strtoupper($_POST["txtNombre"]), $_POST["txtTotalColumnas"], $_POST["txtTotalFilas"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Codigo=> De: '. $dax->codigo.' a: '.strtoupper($_POST["txtCodigo"]).'; Nombre=> De: '. $dax->nombre. ' a: '.strtoupper($_POST["txtNombre"]).'; TotalColumnas=> De: '. $dax->totalcolumnas. ' a: '.strtoupper($_POST["txtTotalColumnas"]).'; TotalFilas=> De: '. $dax->totalfilas. ' a: '.strtoupper($_POST["txtTotalFilas"]), $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objUbicacion->eliminarUbicacion($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>