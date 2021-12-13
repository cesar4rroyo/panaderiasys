<?php
session_start();
$action = $_POST["accion"];
if($action=="genera_cboUnidad"){
	require("../modelo/clsListaUnidad.php");
	$ObjListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjListaUnidad->buscarconxajax($_POST["IdProducto"],$_POST["Moneda"],$_SESSION["R_TipoCambio"]);

	$Unidads="<table><tr><td><select name='cboUnidad' id='cboUnidad' onchange='cambiaStock()'>";
	while($registro=$consulta->fetchObject()){
		if($registro->idunidad==$registro->idunidadbase){ 
			$seleccionar="Selected";
		}else{$seleccionar="";}
		$Unidads=$Unidads."<option value='".$registro->idunidad."' ".$seleccionar.">".$registro->unidad."</option>";
	}
	$Unidads=$Unidads."</select></td><td><a href='listListaUnidad.php?IdProducto=$idproducto' target='_blank' title='Ver formula unidades'>...</a></td></tr></table>";
	$Unidads=utf8_encode($Unidads);
	echo $Unidads;
}
if($action=="seleccionarProducto"){
	require("../modelo/clsListaUnidad.php");
	$ObjListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjListaUnidad->buscarconxajax($_POST["IdProducto"],$_POST["Moneda"],$_SESSION["R_TipoCambio"]);

	while($registro=$consulta->fetchObject()){
		if($registro->idunidad==$registro->idunidadbase){ 
			$precioventa=$registro->precioventa;
			$producto=$registro->producto;
			$stockactual=$registro->stockactual;
			$preciomanoobra=$registro->preciomanoobra;
			$preciocompra=$registro->preciocompra;
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
	$idunidad=$_POST["IdUnidad"];
	$cantidad=$_POST["Cantidad"];
	$precioventa=$_POST["PrecioVenta"];
	$StockActual=$_POST["StockActual"];
	$moneda=$_POST["Moneda"];
	//$preciomanoobra=$_POST["PrecioManoObra"];
	$preciocompra=$_POST["PrecioCompra"];
	
	require("../modelo/clsProducto.php");
	$objProducto = new clsProducto(11,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	
	
	if(isset($_SESSION['R_carroPedido']))
		$carroPedido=$_SESSION['R_carroPedido'];
		
	$rs = $objProducto->buscarxidproductoyidunidad($idproducto,$idunidad);	
    $reg=$rs->fetchObject();	
		
	$carroPedido[($idproducto)]=array('idproducto'=>($idproducto),'codigo'=>$reg->codigo,'producto'=>$reg->producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$reg->unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda);

	$_SESSION['R_carroPedido']=$carroPedido;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cant.</th>
	<th>Precio</th>	
	<th>SubTotal</th>
	";
	foreach($carroPedido as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td>".$v["codigo"]."</td>";
		$registros.="<td>".$v["producto"]."</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2)."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["idproducto"].");'>Quitar</a></td></tr>";
	}
	$registros.="</table><div><center>Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2)."' /></center></div>";
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="quitarProducto"){
	$idproducto=$_POST["IdProducto"];
	
	if(isset($_SESSION['R_carroPedido']))
		$carroPedido=$_SESSION['R_carroPedido'];
		
	unset($carroPedido[($idproducto)]);
	
	$_SESSION['R_carroPedido']=$carroPedido;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cant.</th>
	<th>Precio</th>	
	<th>SubTotal</th>
	";
	foreach($carroPedido as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td>".$v["codigo"]."</td>";
		$registros.="<td>".$v["producto"]."</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2)."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["idproducto"].");'>Quitar</a></td></tr>";
	}
	$registros.="</table><div><center>Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2)."' /></center></div>";
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="agregarDetallesProducto"){
	
	require("../modelo/clsMovimiento.php");
	$objProducto = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		
	if(isset($_SESSION['R_carroPedido']))
		$carroPedido=$_SESSION['R_carroPedido'];
		
	$rs = $objProducto->buscarDetalleProducto($_POST['idmovimiento']);	
    while($reg=$rs->fetchObject()){	
		$idproducto=$reg->idproducto;
		$producto=$reg->producto;
		$codigo=$reg->codigo;
		$idunidad=$reg->idunidad;
		$unidad=$reg->unidad;
		$cantidad=$reg->cantidad;
		$precioventa=$reg->precioventa;
		$moneda=$reg->moneda;
		//$preciomanoobra=$reg->preciomanoobra;
		$preciocompra=$reg->preciocompra;
		
		$carroPedido[($idproducto)]=array('idproducto'=>($idproducto),'codigo'=>$codigo,'producto'=>$producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda);
	}
	$_SESSION['R_carroPedido']=$carroPedido;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cant.</th>
	<th>Precio</th>	
	<th>SubTotal</th>
	";
	foreach($carroPedido as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td>".$v["codigo"]."</td>";
		$registros.="<td>".$v["producto"]."</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2)."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["idproducto"].");'>Quitar</a></td></tr>";
	}
	$registros.="</table><div><center>Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2)."' /></center></div>";
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="genera_cboMesa"){
	if(isset($_POST['situacion'])){
		$situacion=$_POST['situacion'];
	}else{
		$situacion='N';}
	$seleccionado=$_POST['seleccionado'];
	$disabled=$_POST['disabled'];
	require("../modelo/clsMesa.php");
	$ObjMesa = new clsMesa(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjMesa->consultarMesaxSalon($_POST["IdSalon"],$situacion);

	$Mesas="<select name='cboMesa' id='cboMesa' title='Debe indicar una mesa disponible' ".$disabled.">";
	if($consulta->rowCount()>0){
		while($registro=$consulta->fetchObject()){
			if($registro->situacion=='N' or ($registro->situacion=='O' and $registro->idmesa==$seleccionado)){
				$seleccionar="";
				if($registro->idmesa==$seleccionado) $seleccionar="selected";
				$Mesas=$Mesas."<option value='".$registro->idmesa."' ".$seleccionar.">".$registro->numero." | ".$registro->nropersonas." personas</option>";
			}
		}
	}else{
		$Mesas=$Mesas."<option value='0'>No hay mesas disponibles</option>";
	}
	$Mesas=$Mesas."&lt;/select>";
	$Mesas=utf8_encode($Mesas);
	echo $Mesas;
}
if($action=="genera_diagramaMesas"){
	require("../modelo/clsMesa.php");
	$ObjMesa = new clsMesa(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjMesa->consultarMesaxSalon($_POST["IdSalon"],'%');

	if($consulta->rowCount()>0){
		$numMesas=$consulta->rowCount();
		$numCol=3;
		if($numMesas>=$numCol) {if($numMesas%$numCol==0){$limite=$numMesas/$numCol;}else{$limite=$numMesas/$numCol+1;} }else{ $limite=1;$numCol=$numMesas;}
		$Mesas.="<table border=\"1\">";
		for($i=1;$i<=$limite;$i++){
			$Mesas.="<tr>";
			for($j=1;$j<=$numCol;$j++){
				if($registro=$consulta->fetchObject()){
					$Mesas.="<td id= \"$registro->idmesa\" onClick=\"javascript:window.open('mantPedido.php?accion=NUEVO&id_clase=50&idsalon=".$_POST['IdSalon']."&idmesa=$registro->idmesa&situacionmesa=$registro->situacion','_self')\" style=\"background-image:url(../img/hot_rest2.jpg); width:100px; height:90px; background-position:center;\" align=\"center\"><font size=\"+1\"><b>$registro->numero</b></font><br>$registro->nropersonas Personas<br>";
					if($registro->situacion=='O'){
						$Mesas.="<img src=\"../img/ocupado.png\" width=32 height=32>";
					}elseif($registro->situacion=='R'){
						$Mesas.="<img src=\"img/reservado.png\" width=32 height=32>";
					}
					$Mesas.="</td>";
				}
			}
			$Mesas.="</tr>";
		}
		$Mesas.="</table>";
	}else{
		$Mesas.="No hay mesas disponibles";
	}
	
	$Mesas=utf8_encode($Mesas);
	echo $Mesas;
}
if($action=="reportePedidosxMeseros"){
	require("../modelo/clsMovimiento.php");
	$ObjMovimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjMovimiento->reportePedidosxMeseros($_POST['fechainicio'],$_POST['fechafin']);
	$cadena="<table border=\"1\" align=\"center\"><tr><th>N&deg;</th><th>Mesero</th><th>Cant</th></tr>";
	
	if($consulta->rowCount()>0){
		$suma=0;
		while($registro=$consulta->fetchObject()){
			$cadena.="<tr><td>".$registro->idmesero."</td><td>".$registro->mesero."</td><td>".$registro->cantidad."</td></tr>";
			$datos[$registro->idmesero]=$registro->cantidad;
			$suma+=$registro->cantidad;
		}
		$cadena.="<tr><th colspan='2' align='center'>Total</th><th>".$suma."</th></tr>";
		$_SESSION['R_data']=$datos;
		$grafico="<div align='center'><img src='grafico1.php'/></div>";
	}else{
		$cadena.="<tr><td colspan='3'>No se realizaron pedidos</td></tr>";
		$grafico="";
	}
	$cadena.="</table>".$grafico;
	$cadena=utf8_encode($cadena);
	echo $cadena;
}
?>