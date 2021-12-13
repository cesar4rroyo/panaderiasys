<?php
require("../modelo/clsListaUnidad.php");
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
$objListaUnidad = new clsListaUnidad($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		if(isset($_POST["optTipo"])) $opttipo=$_POST["optTipo"]; else $opttipo='S';
		$objListaUnidad->insertarListaUnidad($_POST['txtIdSucursal'], $_POST["txtIdProducto"], $_POST["txtIdSucursalProducto"], $_POST["cboIdUnidad"],$_POST["txtIdUnidadBase"], $_POST["txtFormula"], $_POST["txtPrecioCompra"], $_POST["txtPrecioManoObra"], $_POST["txtPrecioVenta"], $_POST["txtPrecioVenta2"], $opttipo);
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'IdProducto=>'.$_POST["txtIdProducto"].'; IdSucursalProducto=>'.$_POST["txtIdSucursalProducto"].'; IdUnidad=>'.$_POST["cboIdUnidad"].'; IdUnidadBase=>'.$_POST["txtIdUnidadBase"].'; Formula=>'.$_POST["txtFormula"].'; PrecioCompra=>'.$_POST["txtPrecioCompra"].'; PrecioManoObra=>'.$_POST["txtPrecioManoObra"].'; PrecioVenta=>'.$_POST["txtPrecioVenta"].'; PrecioVenta2=>'.$_POST["txtPrecioVenta2"].'; Moneda=>'.$opttipo.';', $_SESSION['R_IdSucursal'], 0 ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		//FIN BITACORA
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		if(isset($_POST["optTipo"])) $opttipo=$_POST["optTipo"]; else $opttipo='S';
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"ListaUnidad","IdListaUnidad",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objListaUnidad->actualizarListaUnidad($_POST["txtId"], $_POST['txtIdSucursal'], $_POST["txtIdProducto"], $_POST["txtIdSucursalProducto"], $_POST["cboIdUnidad"], $_POST["txtFormula"], $_POST["txtPrecioCompra"], $_POST["txtPrecioManoObra"], $_POST["txtPrecioVenta"], $_POST["txtPrecioVenta2"], $opttipo);
		
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'IdProducto=>'.$_POST["txtIdProducto"].'; IdSucursalProducto=>'.$_POST["txtIdSucursalProducto"].'; IdUnidad=> De: '.$dax->idunidad.' a: '.$_POST["cboIdUnidad"].'; IdUnidadBase=>'.$_POST["txtIdUnidadBase"].'; Formula=> De: '.$dax->formula.' a: '.$_POST["txtFormula"].'; PrecioCompra=> De: '.$dax->preciocompra.' a: '.$_POST["txtPrecioCompra"].'; PrecioManoObra=> De: '.$dax->preciomanoobra.' a: '.$_POST["txtPrecioManoObra"].'; PrecioVenta=> De: '.$dax->precioventa.' a: '.$_POST["txtPrecioVenta"].'; PrecioVenta2=> De: '.$dax->precioventa2.' a: '.$_POST["txtPrecioVenta2"].'; Moneda=> De: '.$dax->moneda.' a: '.$opttipo.';', $_SESSION['R_IdSucursal'], 0 ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		//FIN BITACORA
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objListaUnidad->eliminarListaUnidad($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>