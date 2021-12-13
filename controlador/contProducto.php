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
		if(isset($_POST["txtPeso"]) and $_POST["txtPeso"]<>'') $peso=$_POST["txtPeso"]; else $peso=0;
		if(isset($_POST["txtStockMinimo"]) and $_POST["txtStockMinimo"]<>'') $stockminimmo=$_POST["txtStockMinimo"]; else $stockminimmo=0;
		if(isset($_POST["txtStockMaximo"]) and $_POST["txtStockMaximo"]<>'') $stockmaximo=$_POST["txtStockMaximo"]; else $stockmaximo=0;
		if(isset($_POST["txtStockOptimo"]) and $_POST["txtStockOptimo"]<>'') $stockoptimo=$_POST["txtStockOptimo"]; else $stockoptimo=0;
		if(isset($_POST["txtMinimoVender"]) and $_POST["txtMinimoVender"]<>'') $minimovender=$_POST["txtMinimoVender"]; else $minimovender=0;
		if(isset($_POST["txtMinimoComprar"]) and $_POST["txtMinimoComprar"]<>'') $minimocomprar=$_POST["txtMinimoComprar"]; else $minimocomprar=0;		
				
		$rst = $objProducto->insertarProducto($_SESSION['R_IdSucursal'], $_POST["txtCodigo"], trim(strtoupper($_POST["txtDescripcion"])),$_POST["cboIdCategoria"], $_POST["cboIdMarca"], $_POST["cboIdUnidadBase"], $peso, $_POST["cboIdMedidaPeso"], $fechaven, $stockminimmo, $stockmaximo, $stockoptimo, $minimovender, $minimocomprar, $_POST["cboIdUbicacion"], $columna, $fila, $_POST["chkKardex"], $_POST["chkCompuesto"], trim(strtoupper($_POST["txtComentario"])), $_POST["txtImagen"], $_POST["chkCompartido"], $_POST["cboTipo"],$_POST["txtAbreviatura"],($_POST["cboImpresora"]==""?0:$_POST["cboImpresora"]));
		$dax = $rst->fetchObject();
		$idregistro = $dax->idproducto;
		//$objProducto->ejecutarSQL("update producto set idproductoref=".$_POST["cboCortesia"]." where idproducto=".$idregistro." and idsucursal=".$_SESSION["R_IdSucursal"]);
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Codigo=>'.trim(strtoupper($_POST["txtCodigo"])).'; Tipo=>'.$_POST["cboTipo"].'; Descripcion=>'.trim(strtoupper($_POST["txtDescripcion"])).'; IdCategoria=>'.$_POST["cboIdCategoria"].'; IdMarca=>'.$_POST["cboIdMarca"].'; IdUnidadBase=>'.$_POST["cboIdUnidadBase"].'; Peso=>'.$peso.'; IdMedidaPeso=>'.$_POST["cboIdMedidaPeso"].'; FechaVencimiento=>'.$fechaven.'; StockMinimo=>'.$_POST["txtStockMinimo"].'; StockMaximo=>'.$_POST["txtStockMaximo"].'; StockOptimo=>'.$_POST["txtStockOptimo"].'; MinimoVender=>'.$_POST["txtMinimoVender"].'; MinimoComprar=>'.$_POST["txtMinimoComprar"].'; IdUbicacion=>'.$_POST["cboIdUbicacion"].'; Columna=>'.$columna.'; Fila=>'.$fila.'; Kardex=>'.$_POST["chkKardex"].'; Compuesto=>'.$_POST["chkCompuesto"].'; Comentario=>'.trim(strtoupper($_POST["txtComentario"])).'; Compartido=>'.$_POST["chkCompartido"].'; Imagen=>'.$_POST["txtImagen"].'; Abreviatura=>'.$_POST["txtAbreviatura"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
				$objListaUnidad->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				exit();
			}
			//$dax=$rst->fetchObject();
		//$res= $objListaUnidad->insertarListaUnidad($_SESSION['R_IdSucursal'], $dax->idproducto, $_POST["cboIdUnidadBase"], $_POST["cboIdUnidadBase"], 1.00, $_POST["txtPrecioCompra"], $_POST["txtPrecioManoObra"], $_POST["txtPrecioVenta"], $_POST["txtPrecioVenta2"], 'S');
		if(isset($_POST["txtPrecioCompra"]) and $_POST["txtPrecioCompra"]<>'') $PrecioCompra=$_POST["txtPrecioCompra"]; else $PrecioCompra=0;
		if(isset($_POST["txtPrecioManoObra"]) and $_POST["txtPrecioManoObra"]<>'') $PrecioManoObra=$_POST["txtPrecioManoObra"]; else $PrecioManoObra=0;
		if(isset($_POST["txtPrecioVenta"]) and $_POST["txtPrecioVenta"]<>'') $PrecioVenta=$_POST["txtPrecioVenta"]; else $PrecioVenta=0;
		if(isset($_POST["txtPrecioVenta2"]) and $_POST["txtPrecioVenta2"]<>'') $PrecioVenta2=$_POST["txtPrecioVenta2"]; else $PrecioVenta2=0;
		if(isset($_POST["txtPrecioVenta3"]) and $_POST["txtPrecioVenta3"]<>'') $PrecioVenta3=$_POST["txtPrecioVenta3"]; else $PrecioVenta3=0;
		if(isset($_POST["txtPrecioVenta4"]) and $_POST["txtPrecioVenta4"]<>'') $PrecioVenta4=$_POST["txtPrecioVenta4"]; else $PrecioVenta4=0;
		
		$res= $objListaUnidad->insertarListaUnidadSucursales($_SESSION['R_IdEmpresa'], $_SESSION['R_IdSucursal'], $dax->idproducto,$_SESSION['R_IdSucursal'], $_POST["cboIdUnidadBase"], $_POST["cboIdUnidadBase"], 1.00, $PrecioCompra, $PrecioManoObra, $PrecioVenta, $PrecioVenta2, 'S', $PrecioVenta3, $PrecioVenta4);
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], 5, 'Nuevo Registro', 'IdProducto=>'.$dax->idproducto.'; IdUnidad=>'.$_POST["cboIdUnidadBase"].'; IdUnidadBase=>'.$_POST["cboIdUnidadBase"].'; Formula=>1.00; PrecioCompra=>'.$PrecioCompra.'; PrecioManoObra=>'.$PrecioManoObra.'; PrecioVenta=>'.$PrecioVenta.'; PrecioVenta2=>'.$PrecioVenta2.'; Moneda=>S; Se agrego para todas las sucursales de la empresa', $_SESSION['R_IdSucursal'], 0 ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
				$objListaUnidad->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				exit();
		}
		if($res==0){
				$objListaUnidad->finalizarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Guardado correctamente";
		}
		exit();
	case "ACTUALIZAR" :
	$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Producto","IdProducto",$_POST["txtId"]);
	$dax = $rt->fetchObject();
		if(ob_get_length()) ob_clean();
		if(isset($_POST["cboColumna"])) $columna=$_POST["cboColumna"]; else $columna=0;
		if(isset($_POST["cboFila"])) $fila=$_POST["cboFila"]; else $fila=0;
		if(isset($_POST["txtFechaVencimiento"])) $fechaven=$_POST["txtFechaVencimiento"]; else $fechaven='';
		if(isset($_POST["txtPeso"]) and $_POST["txtPeso"]<>'') $peso=$_POST["txtPeso"]; else $peso=0;
		if(isset($_POST["txtStockMinimo"]) and $_POST["txtStockMinimo"]<>'') $stockminimmo=$_POST["txtStockMinimo"]; else $stockminimmo=0;
		if(isset($_POST["txtStockMaximo"]) and $_POST["txtStockMaximo"]<>'') $stockmaximo=$_POST["txtStockMaximo"]; else $stockmaximo=0;
		if(isset($_POST["txtStockOptimo"]) and $_POST["txtStockOptimo"]<>'') $stockoptimo=$_POST["txtStockOptimo"]; else $stockoptimo=0;
		if(isset($_POST["txtMinimoVender"]) and $_POST["txtMinimoVender"]<>'') $minimovender=$_POST["txtMinimoVender"]; else $minimovender=0;
		if(isset($_POST["txtMinimoComprar"]) and $_POST["txtMinimoComprar"]<>'') $minimocomprar=$_POST["txtMinimoComprar"]; else $minimocomprar=0;
		
		$objProducto->actualizarProducto($_POST["txtId"], $_POST["txtCodigo"], trim(strtoupper($_POST["txtDescripcion"])),$_POST["cboIdCategoria"], $_POST["cboIdMarca"], $_POST["cboIdUnidadBase"], $peso, $_POST["cboIdMedidaPeso"], $fechaven, $stockminimmo, $stockmaximo, $stockoptimo, $minimovender, $minimocomprar, $_POST["cboIdUbicacion"], $columna, $fila, $_POST["chkKardex"], $_POST["chkCompuesto"], trim(strtoupper($_POST["txtComentario"])), $_POST["txtImagen"], $_POST['txtIdSucursal'], $_POST["chkCompartido"], $_POST["cboTipo"],$_POST["txtAbreviatura"],($_POST["cboImpresora"]==""?0:$_POST["cboImpresora"]));
		$info = 'Codigo=> De: '. $dax->codigo.' a: '.trim(strtoupper($_POST["txtCodigo"])).'; Tipo=> De: '. $dax->tipo. ' a: '.$_POST["cboTipo"].'; Descripcion=> De: '. $dax->descripcion. ' a: '.trim(strtoupper($_POST["txtDescripcion"])).'; IdCategoria=> De: '. $dax->idcategoria. ' a: '.$_POST["cboIdCategoria"].'; IdMarca=> De: '. $dax->idmarca. ' a: '.$_POST["cboIdMarca"].'; IdUnidadBase=> De: '. $dax->idunidadbase. ' a: '.$_POST["cboIdUnidadBase"].'; Peso=> De: '. $peso. ' a: '.$_POST["txtPeso"].'; IdMedidaPeso=> De: '.$dax->idmedidapeso. ' a: '.$_POST["cboIdMedidaPeso"].'; FechaVencimiento=> De: '. $dax->fechavencimiento. ' a: '.$fechaven.'; StockMinimo=> De: '. $dax->stockminimo. ' a: '.$_POST["txtStockMinimo"].'; StockMaximo=> De: '. $dax->stockmaximo. ' a: '.$_POST["txtStockMaximo"].'; StockOptimo=> De: '. $dax->stockoptimo. ' a: '.$_POST["txtStockOptimo"].'; MinimoVender=> De: '. $dax->minimovender. ' a: '.$_POST["txtMinimoVender"];
        $info = $info .  '; MinimoComprar=> De: '. $dax->minimocomprar. ' a: '.$_POST["txtMinimoComprar"].'; IdUbicacion=> De: '. $dax->idubicacion. ' a: '.$_POST["cboIdUbicacion"].'; Kardex=> De: '. $dax->kardex. ' a: '.$_POST["chkKardex"].'; Compuesto=> De: '. $dax->compuesto. ' a: '.$_POST["chkCompuesto"].'; Comentario=> De: '. $dax->comentario. ' a: '.trim(strtoupper($_POST["txtComentario"])).'; Compartido=> De: '. $dax->compartido. ' a: '.$_POST["chkCompartido"].'; Imagen=> De: '. $dax->imagen. ' a: '.$_POST["txtImagen"].'; Abreviatura=> De: ' .$dax->abreviatura. ' a:'.$_POST["txtAbreviatura"];
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', $info, $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		
		//$objProducto->ejecutarSQL("update producto set idproductoref=".$_POST["cboCortesia"]." where idproducto=".$_POST["txtId"]." and idsucursal=".$_SESSION["R_IdSucursal"]);


		if(isset($_POST["txtPrecioCompra"]) and $_POST["txtPrecioCompra"]<>'') $PrecioCompra=$_POST["txtPrecioCompra"]; else $PrecioCompra=0;
		if(isset($_POST["txtPrecioManoObra"]) and $_POST["txtPrecioManoObra"]<>'') $PrecioManoObra=$_POST["txtPrecioManoObra"]; else $PrecioManoObra=0;
		if(isset($_POST["txtPrecioVenta"]) and $_POST["txtPrecioVenta"]<>'') $PrecioVenta=$_POST["txtPrecioVenta"]; else $PrecioVenta=0;
		if(isset($_POST["txtPrecioVenta2"]) and $_POST["txtPrecioVenta2"]<>'') $PrecioVenta2=$_POST["txtPrecioVenta2"]; else $PrecioVenta2=0;
		if(isset($_POST["txtPrecioVenta3"]) and $_POST["txtPrecioVenta3"]<>'') $PrecioVenta3=$_POST["txtPrecioVenta3"]; else $PrecioVenta3=0;
		if(isset($_POST["txtPrecioVenta4"]) and $_POST["txtPrecioVenta4"]<>'') $PrecioVenta4=$_POST["txtPrecioVenta4"]; else $PrecioVenta4=0;
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"ListaUnidad","IdListaUnidad",$_POST["txtIdListaUnidad"]);
		$dax = $rt->fetchObject();
				
		$objListaUnidad->actualizarListaUnidadBase($_POST["txtIdListaUnidad"], $_SESSION['R_IdSucursal'], $_POST["txtId"], $_POST['txtIdSucursal'], $_POST["cboIdUnidadBase"], $dax->formula, $PrecioCompra, $PrecioManoObra, $PrecioVenta, $PrecioVenta2,'S', $PrecioVenta3, $PrecioVenta4);
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'IdProducto=>'.$_POST["txtId"].'; IdUnidad=> De: '.$dax->idunidad.' a: '.$_POST["cboIdUnidadBase"].'; IdUnidadBase=>'.$_POST["cboIdUnidadBase"].'; Formula=> De: '.$dax->formula.' a: '.$dax->formula.'; PrecioCompra=> De: '.$dax->preciocompra.' a: '.$PrecioCompra.'; PrecioManoObra=> De: '.$dax->preciomanoobra.' a: '.$PrecioManoObra.'; PrecioVenta=> De: '.$dax->precioventa.' a: '.$PrecioVenta.'; PrecioVenta2=> De: '.$dax->precioventa2.' a: '.$PrecioVenta2.'; Moneda=> De: '.$dax->moneda.' a: '.$opttipo.';', $_SESSION['R_IdSucursal'], $_POST["txtIdListaUnidad"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		//FIN BITACORA
		exit();
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objProducto->eliminarProducto($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		exit();
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>