<?php
require("../modelo/clsDetalleCompuesto.php");
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
$objDetalleCompuesto = new clsDetalleCompuesto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$objDetalleCompuesto->insertarDetalleCompuesto($_POST['txtIdSucursalProducto'], $_POST["txtIdProductoCompuesto"], $_POST['txtIdSucursalProducto'], $_POST["cboUnidad"],$_POST["txtIdProductoSeleccionado"], $_POST['txtIdSucursalProductoSeleccionado'], $_POST["txtCantidad"]);
		//ACTUALIZO PRECIO DE COMPRA POR EL PRECIO DE PRODUCCION
		$preciocompuesto=$objDetalleCompuesto->consultarTotal($_POST["txtIdProductoCompuesto"],$_POST["txtIdSucursalProducto"]);
		$rstProducto = $objDetalleCompuesto->obtenerDataSQL("select p.descripcion, p.idunidadbase, u.descripcion as Unidad, preciomanoobra, precioventa from producto p inner join unidad u on p.idunidadbase=u.idunidad inner join LISTAUNIDAD LU on LU.idproducto= p.idproducto and p.idsucursal=LU.idsucursal and LU.idunidad=p.idunidadbase  where p.idproducto = ".$_POST["txtIdProductoCompuesto"]." and p.idsucursal=".$_POST["txtIdSucursalProducto"]);
		$dato=$rstProducto->fetchObject();
		$preciomanoobra=$dato->preciomanoobra;
		$precioproduccion=$preciocompuesto+$preciomanoobra;
		$rstProducto = $objDetalleCompuesto->ejecutarSQL("Update ListaUnidad SET preciocompra = ".$precioproduccion." WHERE idunidadbase=".$dato->idunidadbase." and idsucursal = ".$_SESSION['R_IdSucursal']." and idproducto=".$_POST["txtIdProductoCompuesto"]." and idsucursalproducto=".$_POST["txtIdSucursalProducto"].";");
		//FIN ACTUALIZO PRECIO DE COMPRA POR EL PRECIO DE PRODUCCION
		
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'IdSucursal=>'.$_POST["txtIdSucursalProducto"].'; IdProducto=>'.$_POST["txtIdProductoCompuesto"].'; IdSucursalProducto=>'.$_POST["txtIdSucursalProducto"].'; IdUnidad=>'.$_POST["cboUnidad"].'; IdIngrediente=>'.$_POST["txtIdProductoSeleccionado"].'; IdSucursalIngrediente=>'.$_POST["txtIdSucursalProductoSeleccionado"].'; Cantidad=>'.$_POST["txtCantidad"].';', $_SESSION['R_IdSucursal'], 0 ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		//FIN BITACORA
		break;
	case "ACTUALIZAR" :
		return 'PENDIENTE';break;
		if(ob_get_length()) ob_clean();
		if(isset($_POST["optTipo"])) $opttipo=$_POST["optTipo"]; else $opttipo='S';
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"DetalleCompuesto","IdDetalleCompuesto",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objDetalleCompuesto->actualizarDetalleCompuesto($_POST["txtId"], $_POST['txtIdSucursal'], $_POST["txtIdProducto"], $_POST["cboIdUnidad"], $_POST["txtFormula"], $_POST["txtPrecioCompra"], $_POST["txtPrecioManoObra"], $_POST["txtPrecioVenta"], $_POST["txtPrecioVenta2"], $opttipo);
		
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'IdProducto=>'.$_POST["txtIdProducto"].'; IdUnidad=> De: '.$dax->idunidad.' a: '.$_POST["cboIdUnidad"].'; IdUnidadBase=>'.$_POST["txtIdUnidadBase"].'; Formula=> De: '.$dax->formula.' a: '.$_POST["txtFormula"].'; PrecioCompra=> De: '.$dax->preciocompra.' a: '.$_POST["txtPrecioCompra"].'; PrecioManoObra=> De: '.$dax->preciomanoobra.' a: '.$_POST["txtPrecioManoObra"].'; PrecioVenta=> De: '.$dax->precioventa.' a: '.$_POST["txtPrecioVenta"].'; PrecioVenta2=> De: '.$dax->precioventa2.' a: '.$_POST["txtPrecioVenta2"].'; Moneda=> De: '.$dax->moneda.' a: '.$opttipo.';', $_SESSION['R_IdSucursal'], 0 ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		//FIN BITACORA
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objDetalleCompuesto->eliminarDetalleCompuesto($_POST["txtId"], $_POST['txtIdSucursal']);
		
		//ACTUALIZO PRECIO DE COMPRA POR EL PRECIO DE PRODUCCION
		$preciocompuesto=$objDetalleCompuesto->consultarTotal($_POST["txtIdProductoCompuesto"],$_POST["txtIdSucursalProductoCompuesto"]);
		$rstProducto = $objDetalleCompuesto->obtenerDataSQL("select p.descripcion, p.idunidadbase, u.descripcion as Unidad, preciomanoobra, precioventa from producto p inner join unidad u on p.idunidadbase=u.idunidad inner join LISTAUNIDAD LU on LU.idproducto= p.idproducto and p.idsucursal=LU.idsucursal and LU.idunidad=p.idunidadbase  where p.idproducto = ".$_POST["txtIdProductoCompuesto"]." and p.idsucursal=".$_POST["txtIdSucursalProductoCompuesto"]);
		$dato=$rstProducto->fetchObject();
		$preciomanoobra=$dato->preciomanoobra;
		$precioproduccion=$preciocompuesto+$preciomanoobra;
		$rstProducto = $objDetalleCompuesto->ejecutarSQL("Update ListaUnidad SET preciocompra = ".$precioproduccion." WHERE idunidadbase=".$dato->idunidadbase." and idsucursal = ".$_SESSION['R_IdSucursal']." and idproducto=".$_POST["txtIdProductoCompuesto"]." and idsucursalproducto=".$_POST["txtIdSucursalProductoCompuesto"].";");
		//FIN ACTUALIZO PRECIO DE COMPRA POR EL PRECIO DE PRODUCCION
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Eliminado', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>