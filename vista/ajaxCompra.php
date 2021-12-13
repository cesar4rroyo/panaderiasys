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
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
	
	require("../modelo/clsProducto.php");
	$objProducto = new clsProducto(11,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	
	
	if(isset($_SESSION['R_carroCompra']) && $_SESSION['R_carroCompra']!="")
		$carroCompra=$_SESSION['R_carroCompra'];
		
	$rs = $objProducto->buscarxidproductoyidunidad($idproducto,$idsucursalproducto,$idunidad);	
    $reg=$rs->fetchObject();	
		
	$carroCompra[($idproducto.'-'.$idsucursalproducto.'-'.$preciocompra)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$reg->codigo,'producto'=>$reg->producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$reg->unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda,"afecto"=>"true");

	$_SESSION['R_carroCompra']=$carroCompra;
	
	$contador=0;
	$suma=0;
	$registros.='<table class="striped bordered highlight">
        <thead>
          <tr>
            <th class="center">CODIGO</th>
            <th class="center">PRODUCTO</th>
            <th class="center">UNIDAD</th>
            <th class="center">CANTIDAD</th>
            <th class="center">PRECIO COMPRA</th>
            <th class="center">SUBTOTAL</th>
            <th></th>
          </tr>
        </thead>
        <tbody>';
	foreach($carroCompra as $k => $v){
        if($v["afecto"]=="true"){
            $afecto = $afecto + $v['cantidad']*$v['preciocompra'] ;
        }else{
            $inafecto = $inafecto + $v['cantidad']*$v['preciocompra'];
        }	   
		$subto=$v['cantidad']*$v['preciocompra'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td class='center'><input type='checkbox' onclick='calcularAfecto(this.checked,".$v["idproducto"].")' ".($v["afecto"]=="true"?"checked":"")." id='chkProducto".$v["idproducto"]."' name='chkProducto".$v["idproducto"]."'/><label  for='chkProducto".$v["idproducto"]."'>".$v["codigo"]."</label></td>";
		$registros.="<td class='center'>".$v["producto"]."</td>";
		$registros.="<td class='center'>".$v["unidad"]."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
		$registros.="<td class='center'>".number_format($v["preciocompra"],4,'.',' ')."</td>";
		//$registros.="<td class='center'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td class='center'><input type='hidden' id='txtSubProducto".$v["idproducto"]."' name='txtSubProducto".$v["idproducto"]."' value='".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."' />".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."</td>";
		$registros.="<td class='center'><a href='javascript:void(0)' class='btn-floating red tiny' onClick=\"quitar('".$v["idproducto"]."',".$idsucursalproducto.",'".$v["preciocompra"]."');\"><i class='material-icons'>clear</i></a></td></tr>";
	}
	
	$igv=($_SESSION["R_IGV"]/100)*$afecto;
	$total=number_format($suma,2)+number_format($igv,2);
		
	//$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	//$igv2=number_format($suma,2)-number_format($sub2,2);
    $igv2=($_SESSION["R_IGV"]/100)*$afecto;
    $sub2=number_format($suma,2)-number_format($igv2,2);
	
	if($documento!='2'){	$type='hidden';	$t1='';$t2='';$t3='';$t4='';}else{$type ='text';$t1='IGV';$t2='SUBTOTAL';$t3='INAFECTO';$t4='AFECTO';}
	
    $registros.="</tbody>
        <tfoot>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t3</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtInafecto' id='txtInafecto' value='".number_format($inafecto,2,'.','')."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t4</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtAfecto' id='txtAfecto' value='".number_format($afecto,2,'.','')."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>";
	$registros.="
	<th class='center'>$t2</th><th class='center'>";
	
	if($impuesto=='N'){
            $registros.="<div class='input-field col s12'><input type='$type' size='6' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2,'.','')."' ></div>";
    }else{
        $registros.="<div class='input-field col s12'><input type='$type' size='6' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2,'.','')."' ></div>";
    }
	
	$registros.="
	<input type='hidden' name='rSub' size='6' id='rSub' value='".number_format($suma,2,'.','')."'>
	<input type='hidden' name='rSub2' size='6' id='rSub2' value='".number_format($sub2,2,'.','')."'></th>
            </tr>
            <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>$t1</th><th class='center'>";
	
	if($impuesto=='N'){
	   $registros.="<div class='input-field col s12'><input type='$type' size='6' name='txtIgv' id='txtIgv' value='".number_format($igv,2,'.','')."' ></div>";
    }else{
	   $registros.="<div class='input-field col s12'><input type='$type' size='6' name='txtIgv' id='txtIgv' value='".number_format($igv2,2,'.','')."' ></div>";}
	
	$registros.="
	<input type='hidden' name='rIgv' id='rIgv' value='".number_format($igv,2,'.','')."'>
	<input type='hidden' name='rIgv2' id='rIgv2' value='".number_format($igv2,2,'.','')."'></th>
            </tr>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>TOTAL</th><th class='center'>"; 
	
	if($impuesto=='N'){
	$registros.="<div class='input-field col s12'><input type='text' size='6' name='txtTotal' id='txtTotal' size='6' value='".number_format($total,2,'.','')."' ></div>";}
	else{
	$registros.="<div class='input-field col s12'><input type='text' size='6' name='txtTotal' id='txtTotal' size='6' value='".number_format($suma,2,'.','')."' ></div>";}

	$registros.="
	<input type='hidden' name='rTotal' id='rTotal' size='6' value='".number_format($total,2,'.','')."'>
	<input type='hidden' name='rTotal2' id='rTotal2' size='6' value='".number_format($suma,2,'.','')."'></th>
            </tr>
	</center>
	</div></tfoot>
    </table>";
        
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="quitarProducto"){
	$idproducto=$_POST["IdProducto"];
	$idsucursalproducto=$_POST["IdSucursalProducto"];
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
	$preciocompra=$_POST["PrecioCompra"];
	
	if(isset($_SESSION['R_carroCompra']))
		$carroCompra=$_SESSION['R_carroCompra'];
		
	unset($carroCompra[($idproducto.'-'.$idsucursalproducto.'-'.$preciocompra)]);
	
	$_SESSION['R_carroCompra']=$carroCompra;
	
	$contador=0;
	$suma=0;
	$registros.='<table class="striped bordered highlight">
        <thead>
          <tr>
            <th class="center">CODIGO</th>
            <th class="center">PRODUCTO</th>
            <th class="center">UNIDAD</th>
            <th class="center">CANTIDAD</th>
            <th class="center">PRECIO COMPRA</th>
            <th class="center">SUBTOTAL</th>
            <th></th>
          </tr>
        </thead>
        <tbody>';
	foreach($carroCompra as $k => $v){
        if($v["afecto"]=="true"){
            $afecto = $afecto + $v['cantidad']*$v['preciocompra'] ;
        }else{
            $inafecto = $inafecto + $v['cantidad']*$v['preciocompra'];
        }
		$subto=$v['cantidad']*$v['preciocompra'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td class='center'><input type='checkbox' onclick='calcularAfecto(this.checked,".$v["idproducto"].")' ".($v["afecto"]=="true"?"checked":"")." id='chkProducto".$v["idproducto"]."' name='chkProducto".$v["idproducto"]."'/><label  for='chkProducto".$v["idproducto"]."'>".$v["codigo"]."</label></td>";
		$registros.="<td class='center'>".$v["producto"]."</td>";
		$registros.="<td class='center'>".$v["unidad"]."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
		$registros.="<td class='center'>".number_format($v["preciocompra"],4,'.',' ')."</td>";
		//$registros.="<td class='center'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td class='center'><input type='hidden' id='txtSubProducto".$v["idproducto"]."' name='txtSubProducto".$v["idproducto"]."' value='".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."' />".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."</td>";
		$registros.="<td class='center'><a href='javascript:void(0)' class='btn-floating red tiny' onClick=\"quitar(".$v["idproducto"].",".$idsucursalproducto.",'".$v["preciocompra"]."');\"><i class='material-icons'>clear</i></a></td></tr>";
	}
	
    $igv=($_SESSION["R_IGV"]/100)*$afecto;
	$total=number_format($suma,2)+number_format($igv,2);
		
	//$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	//$igv2=number_format($suma,2)-number_format($sub2,2);
    $igv2=($_SESSION["R_IGV"]/100)*$afecto;
    $sub2=number_format($suma,2)-number_format($igv2,2);
	
	if($documento!='2'){	$type='hidden';	$t1='';$t2='';$t3='';$t4='';}else{$type ='text';$t1='IGV';$t2='SUBTOTAL';$t3='INAFECTO';$t4='AFECTO';}
	
    $registros.="</tbody>
        <tfoot>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t3</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtInafecto' id='txtInafecto' value='".number_format($inafecto,2,'.','')."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t4</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtAfecto' id='txtAfecto' value='".number_format($afecto,2,'.','')."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>";
	$registros.="
	<th class='center'>$t2</th><th class='center'>";
	
	if($impuesto=='N'){
            $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2,'.','')."' ></div>";
        }else{
            $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2,'.','')."' ></div>";
        }
	
	$registros.="
	<input type='hidden' name='rSub' size='6' id='rSub' value='".number_format($suma,2,'.','')."'>
	<input type='hidden' name='rSub2' size='6' id='rSub2' value='".number_format($sub2,2,'.','')."'></th>
            </tr>
            <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>$t1</th><th class='center'>";
	
	if($impuesto=='N'){
	$registros.="<div class='input-field col s12'><input type='$type' size='6' name='txtIgv' id='txtIgv' value='".number_format($igv,2,'.','')."'  ></div>";}
	else{
	$registros.="<div class='input-field col s12'><input type='$type' size='6' name='txtIgv' id='txtIgv' value='".number_format($igv2,2,'.','')."'  ></div>";}
	
	$registros.="
	<input type='hidden' name='rIgv' size='6' id='rIgv' value='".number_format($igv,2,'.','')."'>
	<input type='hidden' name='rIgv2' size='6' id='rIgv2' value='".number_format($igv2,2,'.','')."'></th>
            </tr>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>TOTAL</th><th class='center'>"; 
	
	if($impuesto=='N'){
	$registros.="<div class='input-field col s12'><input type='text' size='6' name='txtTotal' id='txtTotal' size='6' value='".number_format($total,2,'.','')."' ></div>";}
	else{
	$registros.="<div class='input-field col s12'><input type='text' size='6' name='txtTotal' id='txtTotal' size='6' value='".number_format($suma,2,'.','')."' ></div>";}

	$registros.="
	<input type='hidden' name='rTotal' size='6' id='rTotal' value='".number_format($total,2,'.','')."'>
	<input type='hidden' name='rTotal2' size='6' id='rTotal2' value='".number_format($suma,2,'.','')."'></th>
            </tr>
	</center>
	</div></tfoot>
    </table>";
        
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="actualizarDetalleCompra"){
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
	
	if(isset($_SESSION['R_carroCompra']))
		$carroCompra=$_SESSION['R_carroCompra'];
		
	$_SESSION['R_carroCompra']=$carroCompra;
	
	$contador=0;
	$suma=0;
	$registros.='<table class="striped bordered highlight">
        <thead>
          <tr>
            <th class="center">CODIGO</th>
            <th class="center">PRODUCTO</th>
            <th class="center">UNIDAD</th>
            <th class="center">CANTIDAD</th>
            <th class="center">PRECIO COMPRA</th>
            <th class="center">SUBTOTAL</th>
            <th></th>
          </tr>
        </thead>
        <tbody>';
	foreach($carroCompra as $k => $v){
        if($v["afecto"]=="true"){
            $afecto = $afecto + $v['cantidad']*$v['preciocompra'] ;
        }else{
            $inafecto = $inafecto + $v['cantidad']*$v['preciocompra'];
        }
		$subto=$v['cantidad']*$v['preciocompra'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td class='center'><input type='checkbox' onclick='calcularAfecto(this.checked,".$v["idproducto"].")' ".($v["afecto"]=="true"?"checked":"")." id='chkProducto".$v["idproducto"]."' name='chkProducto".$v["idproducto"]."'/><label  for='chkProducto".$v["idproducto"]."'>".$v["codigo"]."</label></td>";
		$registros.="<td class='center'>".$v["producto"]."</td>";
		$registros.="<td class='center'>".$v["unidad"]."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
		$registros.="<td class='center'>".number_format($v["preciocompra"],4,'.',' ')."</td>";
		//$registros.="<td class='center'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td class='center'><input type='hidden' id='txtSubProducto".$v["idproducto"]."' name='txtSubProducto".$v["idproducto"]."' value='".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."' />".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."</td>";
		$registros.="<td class='center'><a href='javascript:void(0)' class='btn-floating red tiny' onClick=\"quitar(".$v["idproducto"].",".$idsucursalproducto.",'".$v["preciocompra"]."');\"><i class='material-icons'>clear</i></a></td></tr>";
	}
	
    $igv=($_SESSION["R_IGV"]/100)*$afecto;
	$total=number_format($suma,2)+number_format($igv,2);
		
	//$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	//$igv2=number_format($suma,2)-number_format($sub2,2);
    $igv2=($_SESSION["R_IGV"]/100)*$afecto;
    $sub2=number_format($suma,2)-number_format($igv2,2);
	
	if($documento!='2'){	$type='hidden';	$t1='';$t2='';$t3='';$t4='';}else{$type ='text';$t1='IGV';$t2='SUBTOTAL';$t3='INAFECTO';$t4='AFECTO';}
	
    $registros.="</tbody>
        <tfoot>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t3</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtInafecto' id='txtInafecto' value='".number_format($inafecto,2)."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t4</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtAfecto' id='txtAfecto' value='".number_format($afecto,2)."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>";
        
	$registros.="
	<th class='center'>$t2</th><th class='center'>";
	
	if($impuesto=='N'){
        $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2)."' ></div>";
    }else{
        $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2)."' ></div>";
    }
	
	$registros.="
	<input type='hidden' name='rSub' size='6' id='rSub' value='".number_format($suma,2)."'>
	<input type='hidden' name='rSub2' size='6' id='rSub2' value='".number_format($sub2,2)."'></th>
            </tr>
            <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>$t1</th><th class='center'>";
	
	if($impuesto=='N'){
	$registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv,2)."'  ></div>";}
	else{
	$registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv2,2)."'  ></div>";}
	
	$registros.="
	<input type='hidden' name='rIgv' size='6' id='rIgv' value='".number_format($igv,2)."'>
	<input type='hidden' name='rIgv2' size='6' id='rIgv2' value='".number_format($igv2,2)."'></th>
            </tr>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>TOTAL</th><th class='center'>"; 
	
	if($impuesto=='N'){
	$registros.="<div class='input-field col s12'><input size='6' type='text' name='txtTotal' id='txtTotal' size='6' value='".number_format($total,2)."' ></div>";}
	else{
	$registros.="<div class='input-field col s12'><input size='6' type='text' name='txtTotal' id='txtTotal' size='6' value='".number_format($suma,2)."' ></div>";}

	$registros.="
	<input type='hidden' name='rTotal' size='6' id='rTotal' value='".number_format($total,2)."'>
	<input type='hidden' name='rTotal2' size='6' id='rTotal2' value='".number_format($suma,2)."'></th>
            </tr>
	</center>
	</div></tfoot>
    </table>";
        
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="actualizarDetalleCompraAfecto"){
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
	
	if(isset($_SESSION['R_carroCompra']))
		$carroCompra=$_SESSION['R_carroCompra'];

		
	$_SESSION['R_carroCompra']=$carroCompra;
	$carroCompra[($_POST["idproducto"].'-'.$_SESSION["R_IdSucursal"])]["afecto"]=$_POST["check"];
    
	$contador=0;
	$suma=0;
	$registros.='<table class="striped bordered highlight">
        <thead>
          <tr>
            <th class="center">CODIGO</th>
            <th class="center">PRODUCTO</th>
            <th class="center">UNIDAD</th>
            <th class="center">CANTIDAD</th>
            <th class="center">PRECIO COMPRA</th>
            <th class="center">SUBTOTAL</th>
            <th></th>
          </tr>
        </thead>
        <tbody>';
	foreach($carroCompra as $k => $v){
        if($v["afecto"]=="true"){
            $afecto = $afecto + $v['cantidad']*$v['preciocompra'] ;
        }else{
            $inafecto = $inafecto + $v['cantidad']*$v['preciocompra'];
        }
		$subto=$v['cantidad']*$v['preciocompra'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td class='center'><input type='checkbox' onclick='calcularAfecto(this.checked,".$v["idproducto"].")' ".($v["afecto"]=="true"?"checked":"")." id='chkProducto".$v["idproducto"]."' name='chkProducto".$v["idproducto"]."'/><label  for='chkProducto".$v["idproducto"]."'>".$v["codigo"]."</label></td>";
		$registros.="<td class='center'>".$v["producto"]."</td>";
		$registros.="<td class='center'>".$v["unidad"]."</td>";
		$registros.="<td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
		$registros.="<td class='center'>".number_format($v["preciocompra"],4,'.',' ')."</td>";
		//$registros.="<td class='center'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td class='center'><input type='hidden' id='txtSubProducto".$v["idproducto"]."' name='txtSubProducto".$v["idproducto"]."' value='".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."' />".number_format($v["cantidad"]*$v["preciocompra"],2,'.',' ')."</td>";
		$registros.="<td class='center'><a href='javascript:void(0)' class='btn-floating red tiny' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'><i class='material-icons'>clear</i></a></td></tr>";
	}
	
	$igv=($_SESSION["R_IGV"]/100)*$afecto;
	$total=number_format($suma,2)+number_format($igv,2);
		
	//$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	//$igv2=number_format($suma,2)-number_format($sub2,2);
    $igv2=($_SESSION["R_IGV"]/100)*$afecto;
    $sub2=number_format($suma,2)-number_format($igv2,2);
	
	if($documento!='2'){	$type='hidden';	$t1='';$t2='';$t3='';$t4='';}else{$type ='text';$t1='IGV';$t2='SUBTOTAL';$t3='INAFECTO';$t4='AFECTO';}
	
    $registros.="</tbody>
        <tfoot>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t3</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtInafecto' id='txtInafecto' value='".number_format($inafecto,2)."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class='center'>$t4</th>
            <th class='center'>";
    $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtAfecto' id='txtAfecto' value='".number_format($afecto,2)."' ></div>
            </th>
          </tr>
          <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>";
        
	$registros.="
	<th class='center'>$t2</th><th class='center'>";
	
	if($impuesto=='N'){
        $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2)."' ></div>";
    }else{
        $registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2)."' ></div>";
    }
	
	$registros.="
	<input type='hidden' name='rSub' size='6' id='rSub' value='".number_format($suma,2)."'>
	<input type='hidden' name='rSub2' size='6' id='rSub2' value='".number_format($sub2,2)."'></th>
            </tr>
            <tr $type=''>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>$t1</th><th class='center'>";
	
	if($impuesto=='N'){
	$registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv,2)."' ></div>";}
	else{
	$registros.="<div class='input-field col s12'><input size='6' type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv2,2)."'  ></div>";}
	
	$registros.="
	<input type='hidden' name='rIgv' size='6' id='rIgv' value='".number_format($igv,2)."'>
	<input type='hidden' name='rIgv2' size='6' id='rIgv2' value='".number_format($igv2,2)."'></th>
            </tr>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
	<th class='center'>TOTAL</th><th class='center'>"; 
	
	if($impuesto=='N'){
	$registros.="<div class='input-field col s12'><input size='6' type='text' name='txtTotal' id='txtTotal' size='6' value='".number_format($total,2)."' ></div>";}
	else{
	$registros.="<div class='input-field col s12'><input size='6' type='text' name='txtTotal' id='txtTotal' size='6' value='".number_format($suma,2)."' ></div>";}

	$registros.="
	<input type='hidden' name='rTotal' size='6' id='rTotal' value='".number_format($total,2)."'>
	<input type='hidden' name='rTotal2' size='6' id='rTotal2' value='".number_format($suma,2)."'></th>
            </tr>
	</center>
	</div></tfoot>
    </table>";
        
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="generaNumero"){
	require("../modelo/clsMovimiento.php");
	$objMovimiento = new clsMovimiento(41,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$numero = $objMovimiento->generaNumeroSinSerie(3,$_POST['IdTipoDocumento'],substr($_SESSION["R_FechaProceso"],3,2));

	echo "vnumero='".$numero."';";
}
?>