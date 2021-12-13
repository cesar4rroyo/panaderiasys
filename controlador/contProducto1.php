<?php
require("../modelo/clsProducto.php");
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
$objProducto = new clsProducto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$objListaUnidad->iniciarTransaccion();
		if(isset($_POST["cboColumna"])) $columna=$_POST["cboColumna"]; else $columna=0;
		if(isset($_POST["cboFila"])) $fila=$_POST["cboFila"]; else $fila=0;
		if(isset($_POST["txtFechaVencimiento"])) $fechaven=$_POST["txtFechaVencimiento"]; else $fechaven='';
		if(isset($_POST["txtStockMinimo"]) and $_POST["txtStockMinimo"]<>'') $stockminimmo=$_POST["txtStockMinimo"]; else $stockminimmo=0;
		if(isset($_POST["txtStockMaximo"]) and $_POST["txtStockMaximo"]<>'') $stockmaximo=$_POST["txtStockMaximo"]; else $stockmaximo=0;
		if(isset($_POST["txtStockOptimo"]) and $_POST["txtStockOptimo"]<>'') $stockoptimo=$_POST["txtStockOptimo"]; else $stockoptimo=0;
		if(isset($_POST["txtMinimoVender"]) and $_POST["txtMinimoVender"]<>'') $minimovender=$_POST["txtMinimoVender"]; else $minimovender=0;
		if(isset($_POST["txtMinimoComprar"]) and $_POST["txtMinimoComprar"]<>'') $minimocomprar=$_POST["txtMinimoComprar"]; else $minimocomprar=0;
				
		$rst = $objProducto->insertarProducto($_SESSION['R_IdSucursal'], $_POST["txtCodigo"], trim(strtoupper($_POST["txtDescripcion"])),$_POST["cboIdCategoria"], $_POST["cboIdMarca"], $_POST["cboIdUnidadBase"], $_POST["txtPeso"], $_POST["cboIdMedidaPeso"], $fechaven, $stockminimmo, $stockmaximo, $stockoptimo, $minimovender, $minimocomprar, $_POST["cboIdUbicacion"], $columna, $fila, $_POST["chkKardex"], $_POST["chkCompuesto"], trim(strtoupper($_POST["txtComentario"])), $_POST["txtImagen"],'N');
		$dax = $rst->fetchObject();
		$idregistro = $dax->idproducto;
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Codigo=>'.trim(strtoupper($_POST["txtCodigo"])).'; Descripcion=>'.trim(strtoupper($_POST["txtDescripcion"])).'; IdCategoria=>'.$_POST["cboIdCategoria"].'; IdMarca=>'.$_POST["cboIdMarca"].'; IdUnidadBase=>'.$_POST["cboIdUnidadBase"].'; Peso=>'.$_POST["txtPeso"].'; IdMedidaPeso=>'.$_POST["cboIdMedidaPeso"].'; FechaVencimiento=>'.$fechaven.'; StockMinimo=>'.$_POST["txtStockMinimo"].'; StockMaximo=>'.$_POST["txtStockMaximo"].'; StockOptimo=>'.$_POST["txtStockOptimo"].'; MinimoVender=>'.$_POST["txtMinimoVender"].'; MinimoComprar=>'.$_POST["txtMinimoComprar"].'; IdUbicacion=>'.$_POST["cboIdUbicacion"].'; Columna=>'.$columna.'; Fila=>'.$fila.'; Kardex=>'.$_POST["chkKardex"].'; Compuesto=>'.$_POST["chkCompuesto"].'; Comentario=>'.trim(strtoupper($_POST["txtComentario"])).'; Imagen=>'.$_POST["txtImagen"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
				$objListaUnidad->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				break 2;
			}
			//$dax=$rst->fetchObject();
		$res= $objListaUnidad->insertarListaUnidad($_SESSION['R_IdSucursal'], $dax->idproducto, $_POST["cboIdUnidadBase"], $_POST["cboIdUnidadBase"], 1.00, $_POST["txtPrecioCompra"], $_POST["txtPrecioManoObra"], $_POST["txtPrecioVenta"], $_POST["txtPrecioVenta2"], 'S');
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], 5, 'Nuevo Registro', 'IdProducto=>'.$dax->idproducto.'; IdUnidad=>'.$_POST["cboIdUnidadBase"].'; IdUnidadBase=>'.$_POST["cboIdUnidadBase"].'; Formula=>1.00; PrecioCompra=>'.$_POST["txtPrecioCompra"].'; PrecioManoObra=>'.$_POST["txtPrecioManoObra"].'; PrecioVenta=>'.$_POST["txtPrecioVenta"].'; PrecioVenta2=>'.$_POST["txtPrecioVenta2"].'; Moneda=>S;', $_SESSION['R_IdSucursal'], 0 ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
				$objListaUnidad->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				break 3;
		}
		if($res==0){
				$objListaUnidad->finalizarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Guardado correctamente";
		}
		break;
	case "ACTUALIZAR" :
	$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Producto","IdProducto",$_POST["txtId"]);
	$dax = $rt->fetchObject();
		if(ob_get_length()) ob_clean();
		if(isset($_POST["cboColumna"])) $columna=$_POST["cboColumna"]; else $columna=0;
		if(isset($_POST["cboFila"])) $fila=$_POST["cboFila"]; else $fila=0;
		if(isset($_POST["txtFechaVencimiento"])) $fechaven=$_POST["txtFechaVencimiento"]; else $fechaven='';
		if(isset($_POST["txtStockMinimo"]) and $_POST["txtStockMinimo"]<>'') $stockminimmo=$_POST["txtStockMinimo"]; else $stockminimmo=0;
		if(isset($_POST["txtStockMaximo"]) and $_POST["txtStockMaximo"]<>'') $stockmaximo=$_POST["txtStockMaximo"]; else $stockmaximo=0;
		if(isset($_POST["txtStockOptimo"]) and $_POST["txtStockOptimo"]<>'') $stockoptimo=$_POST["txtStockOptimo"]; else $stockoptimo=0;
		if(isset($_POST["txtMinimoVender"]) and $_POST["txtMinimoVender"]<>'') $minimovender=$_POST["txtMinimoVender"]; else $minimovender=0;
		if(isset($_POST["txtMinimoComprar"]) and $_POST["txtMinimoComprar"]<>'') $minimocomprar=$_POST["txtMinimoComprar"]; else $minimocomprar=0;
		
		$objProducto->actualizarProducto($_POST["txtId"], $_POST["txtCodigo"], trim(strtoupper($_POST["txtDescripcion"])),$_POST["cboIdCategoria"], $_POST["cboIdMarca"], $_POST["cboIdUnidadBase"], $_POST["txtPeso"], $_POST["cboIdMedidaPeso"], $fechaven, $stockminimmo, $stockmaximo, $stockoptimo, $minimovender, $minimocomprar, $_POST["cboIdUbicacion"], $columna, $fila, $_POST["chkKardex"], $_POST["chkCompuesto"], trim(strtoupper($_POST["txtComentario"])), $_POST["txtImagen"], $_POST['txtIdSucursal'], 'N');
		
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Codigo=> De: '. $dax->codigo.' a: '.trim(strtoupper($_POST["txtCodigo"])).'; Descripcion=> De: '. $dax->descripcion. ' a: '.trim(strtoupper($_POST["txtDescripcion"])).'; IdCategoria=> De: '. $dax->idcategoria. ' a: '.$_POST["cboIdCategoria"].'; IdMarca=> De: '. $dax->idmarca. ' a: '.$_POST["cboIdMarca"].'; IdUnidadBase=> De: '. $dax->idunidadbase. ' a: '.$_POST["cboIdUnidadBase"].'; Peso=> De: '. $dax->peso. ' a: '.$_POST["txtPeso"].'; IdMedidaPeso=> De: '. $dax->idmedidapeso. ' a: '.$_POST["cboIdMedidaPeso"].'; FechaVencimiento=> De: '. $dax->fechavencimiento. ' a: '.$fechaven.'; StockMinimo=> De: '. $dax->stockminimo. ' a: '.$_POST["txtStockMinimo"].'; StockMaximo=> De: '. $dax->stockmaximo. ' a: '.$_POST["txtStockMaximo"].'; StockOptimo=> De: '. $dax->stockoptimo. ' a: '.$_POST["txtStockOptimo"].'; MinimoVender=> De: '. $dax->minimovender. ' a: '.$_POST["txtMinimoVender"].'; MinimoComprar=> De: '. $dax->minimocomprar. ' a: '.$_POST["txtMinimoComprar"].'; IdUbicacion=> De: '. $dax->idubicacion. ' a: '.$_POST["cboIdUbicacion"].'; Kardex=> De: '. $dax->kardex. ' a: '.$_POST["chkKardex"].'; Compuesto=> De: '. $dax->compuesto. ' a: '.$_POST["chkCompuesto"].'; Comentario=> De: '. $dax->comentario. ' a: '.trim(strtoupper($_POST["txtComentario"])).'; Imagen=> De: '. $dax->imagen. ' a: '.$_POST["txtImagen"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"ListaUnidad","IdListaUnidad",$_POST["txtIdListaUnidad"]);
		$dax = $rt->fetchObject();
		
		$objListaUnidad->actualizarListaUnidadBase($_POST["txtIdListaUnidad"], $_SESSION['R_IdSucursal'], $_POST["txtId"], $_POST['txtIdSucursal'], $_POST["cboIdUnidadBase"], $dax->formula, $_POST["txtPrecioCompra"], $_POST["txtPrecioManoObra"], $_POST["txtPrecioVenta"], $_POST["txtPrecioVenta2"],'S');
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'IdProducto=>'.$_POST["txtId"].'; IdUnidad=> De: '.$dax->idunidad.' a: '.$_POST["cboIdUnidadBase"].'; IdUnidadBase=>'.$_POST["cboIdUnidadBase"].'; Formula=> De: '.$dax->formula.' a: '.$dax->formula.'; PrecioCompra=> De: '.$dax->preciocompra.' a: '.$_POST["txtPrecioCompra"].'; PrecioManoObra=> De: '.$dax->preciomanoobra.' a: '.$_POST["txtPrecioManoObra"].'; PrecioVenta=> De: '.$dax->precioventa.' a: '.$_POST["txtPrecioVenta"].'; PrecioVenta2=> De: '.$dax->precioventa2.' a: '.$_POST["txtPrecioVenta2"].'; Moneda=> De: '.$dax->moneda.' a: '.$opttipo.';', $_SESSION['R_IdSucursal'], $_POST["txtIdListaUnidad"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		//FIN BITACORA
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objProducto->eliminarProducto($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>