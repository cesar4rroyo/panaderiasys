<?php
session_start();
$action = $_POST["accion"];
if($action=="genera_cboUnidad"){
	require("../modelo/clsListaUnidad.php");
	$ObjListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjListaUnidad->buscarconxajax($_POST["IdProducto"],$_POST["IdSucursalProducto"],$_POST["Moneda"],$_SESSION["R_TipoCambio"]);

	$Unidads="<select name='cboUnidad' id='cboUnidad' onchange='cambiaPrecioUnidad(this.value);cambiaStock(this.value);'>";
	while($registro=$consulta->fetchObject()){
		if($registro->idunidad==$registro->idunidadbase){ 
			$seleccionar="Selected";
		}else{$seleccionar="";}
		$Unidads=$Unidads."<option value='".$registro->idunidad."' ".$seleccionar.">".$registro->unidad."</option>";
	}
	$Unidads=$Unidads."</select>";
	$Unidads=utf8_encode($Unidads);
	echo $Unidads;
}
if($action=="cambiaStock"){
	require("../modelo/clsStockProducto.php");
	$ObjStockProducto = new clsStockProducto(2,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	echo $ObjStockProducto->obtenerStock($_POST["IdProducto"],$_POST["IdUnidad"],$_SESSION['R_IdSucursal'],$_POST["IdSucursalProducto"]);
}
if($action=="cambiaPrecioUnidad"){
	if(isset($_POST["NroPrecio"])) $nroprecio=$_POST["NroPrecio"]; else $nroprecio=1;
	require("../modelo/clsListaUnidad.php");
	$ObjListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta2 = $ObjListaUnidad->buscarprecio($_POST["IdUnidad"],$_POST["IdProducto"],$_POST["IdSucursalProducto"]);
	$dato=$consulta2->fetchObject();
	if($nroprecio==1){
		$precio=$dato->precio;
	}elseif($nroprecio==2){
		$precio=$dato->precio2;
	}
	echo $precio;
}
if($action=="seleccionarProducto"){
	require("../modelo/clsListaUnidad.php");
	$ObjListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjListaUnidad->buscarconxajax($_POST["IdProducto"],$_POST["IdSucursalProducto"],$_POST["Moneda"],$_SESSION["R_TipoCambio"]);

	while($registro=$consulta->fetchObject()){
		if($registro->idunidad==$registro->idunidadbase){ 
			$precioventa=$registro->precioventa;
			$producto=$registro->producto;
			$stockactual=$registro->stockactual;
			$preciomanoobra=$registro->preciomanoobra;
			$preciocompra=$registro->preciocompra;
			if($_POST["IdProducto"]=="69"){
				$stockactual=round($stockactual*$precioventa,2);
			}
		}else{$seleccionar="";}
	}
	echo "vprecioventa=$precioventa;
	vproducto='$producto';
	vstockactual=$stockactual;
	vpreciomanoobra=$preciomanoobra;
	vpreciocompra=$preciocompra;";
}
if($action=="agregarProducto"){
	$idproducto=$_POST["IdProducto"];
	$idsucursalproducto=$_POST["IdSucursalProducto"];
	$idunidad=$_POST["IdUnidad"];
	$cantidad=$_POST["Cantidad"];
	$precioventa=$_POST["PrecioVenta"];
	$StockActual=$_POST["StockActual"];
	$moneda=$_POST["Moneda"];
	//$preciomanoobra=$_POST["PrecioManoObra"];
	$preciocompra=$_POST["PrecioCompra"];
	if(($idproducto=="69" || $idproducto=="98" || $idproducto=="104" || $idproducto=="101") && $idsucursalproducto=="1"){
		$cantidad=$cantidad/$precioventa;
	}
	
	require("../modelo/clsProducto.php");
	$objProducto = new clsProducto(11,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	
	
	if(isset($_SESSION['R_carroAlmacen']) && $_SESSION['R_carroAlmacen']!="")
		$carroAlmacen=$_SESSION['R_carroAlmacen'];
		
	$rs = $objProducto->buscarxidproductoyidunidad($idproducto,$idsucursalproducto,$idunidad);	
    $reg=$rs->fetchObject();	
		
	$carroAlmacen[($idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$reg->codigo,'producto'=>$reg->producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$reg->unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda);

	$_SESSION['R_carroAlmacen']=$carroAlmacen;
	
	$contador=0;
	$suma=0;
	$registros.="<table class='striped bordered highlight'>
    <thead>
    	<th class='center'>C&oacute;digo</th>
    	<th class='center'>Producto</th>
    	<th class='center'>Unidad</th>
    	<th class='center'>Cantidad</th>
    	<th class='center'>Precio Ofertado</th>	
    	<th class='center'>SubTotal</th>
	<thead>
    <tbody>";
	foreach($carroAlmacen as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td class='center'>".$v["codigo"]."</td>";
		$registros.="<td class='center'>".$v["producto"]."</td>";
		$registros.="<td class='center'>".$v["unidad"]."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"],2)."</td>";
		$registros.="<td class='center'>".number_format($v["precioventa"],2)."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"]*$v["precioventa"],2)."</td>";
		$registros.="<td class='center'><a href='javascript:void(0)' class='btn-floating red tiny' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'><i class='material-icons'>clear</i></a></a></td></tr>";
	}
	$registros.="</tbody><tfoot><tr><th class='center' colspan='5'>Total:</th><th><input size='5' type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' /></th></tr></tfoot></table>";
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="quitarProducto"){
	$idproducto=$_POST["IdProducto"];
	$idsucursalproducto=$_POST["IdSucursalProducto"];
	
	if(isset($_SESSION['R_carroAlmacen']))
		$carroAlmacen=$_SESSION['R_carroAlmacen'];
		
	unset($carroAlmacen[($idproducto.'-'.$idsucursalproducto)]);
	
	$_SESSION['R_carroAlmacen']=$carroAlmacen;
	
	$contador=0;
	$suma=0;
	$registros.="<table class='striped bordered highlight'>
    <thead>
    	<th class='center'>C&oacute;digo</th>
    	<th class='center'>Producto</th>
    	<th class='center'>Unidad</th>
    	<th class='center'>Cantidad</th>
    	<th class='center'>Precio Ofertado</th>	
    	<th class='center'>SubTotal</th>
	<thead>
    <tbody>";
	foreach($carroAlmacen as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td class='center'>".$v["codigo"]."</td>";
		$registros.="<td class='center'>".utf8_decode($v["producto"])."</td>";
		$registros.="<td class='center'>".$v["unidad"]."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
		$registros.="<td class='center'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td class='center'><a href='javascript:void(0)' class='btn-floating red tiny' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'><i class='material-icons'>clear</i></a></a></td></tr>";
	}
	$registros.="</tbody><tfoot><tr><th class='center' colspan='5'>Total:</th><th><input size='5' type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' /></th></tr></tfoot></table>";
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="generaNumero"){
	require("../modelo/clsMovimiento.php");
	$objMovimiento = new clsMovimiento(41,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$numero = $objMovimiento->generaNumeroSinSerie(3,$_POST['IdTipoDocumento'],substr($_SESSION["R_FechaProceso"],3,2));

	echo "vnumero='".$numero."';";
}
if($action=="BuscaProductoJSON"){
    require("../modelo/clsProducto.php");
    $objMovimiento = new clsProducto(41,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $res=$objMovimiento->consultarProductoInterna(10000, 1, 1, 1, 0, '', 0,0,'','','S');
    while($dat=$res->fetchObject()){
        $datos[$dat->descripcion] = $dat->idproducto."-".$dat->idsucursal."|$dat->codigo|$dat->descripcion|$dat->unidad|$dat->stock|$dat->categoria|$dat->precioventa|$dat->preciocompra";
    }
    echo json_encode(["datos"=>$datos]);
}
?>