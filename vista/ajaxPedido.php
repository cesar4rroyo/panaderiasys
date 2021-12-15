<?php
session_start();
    /* Change to the correct path if you copy this example! */
    require __DIR__ . '/../autoload.php';
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
    use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
    use Mike42\Escpos\EscposImage;

$action = $_POST["accion"];
if($action=="genera_cboUnidad"){
	require("../modelo/clsListaUnidad.php");
	$ObjListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjListaUnidad->buscarconxajax($_POST["IdProducto"],$_POST["IdSucursalProducto"],$_POST["Moneda"],$_SESSION["R_TipoCambio"]);

    $class=$_POST["class"];
	$Unidads="<table class='$class'><tr class='$class'><td class='$class'><select class='$class' name='cboUnidad' id='cboUnidad' onchange='cambiaPrecioUnidad(this.value)'>";
	while($registro=$consulta->fetchObject()){
		if($registro->idunidad==$registro->idunidadbase){ 
			$seleccionar="Selected";
		}else{$seleccionar="";}
		$Unidads=$Unidads."<option class='$class' value='".$registro->idunidad."' ".$seleccionar.">".$registro->unidad."</option>";
	}
	$Unidads=$Unidads."</select></td></tr></table>";
	$Unidads=utf8_encode($Unidads);
	echo $Unidads;
}
if($action=="genera_propiedad"){
	require("../modelo/clsCategoria.php");
	$ObjCategoria = new clsCategoria(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjCategoria->obtenerDataSQL("select * from detallecategoria where idcategoria=(select idcategoria from producto where idproducto=".$_POST["IdProducto"]." and idsucursal=".$_POST["IdSucursalProducto"].") and estado='N' and idsucursal=".$_POST["IdSucursalProducto"]);

	$Unidads="<table><tr>";
	while($dato1=$consulta->fetchObject()){
		$Unidads=$Unidads.'<td align="left" style="font-weight: bold;width: auto;"><input  type="checkbox" onclick="detalleCategoria(this.checked,'.$dato1->iddetallecategoria.')" />'.$dato1->abreviatura.'</td>';
	}
	$Unidads=$Unidads."</tr></table>";
	$Unidads=utf8_encode($Unidads);
	echo $Unidads;
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
	$preciocompra=$dato->preciocompra;
	echo "
	vpreciocompra=$preciocompra;
	vprecioventa=$precio;";
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
	vpreciocompra=$preciocompra;
	";
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
	
	require("../modelo/clsProducto.php");
	$objProducto = new clsProducto(11,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	
	
	if(isset($_SESSION['R_carroPedido']))
		$carroPedido=$_SESSION['R_carroPedido'];
		
  
    if($_POST["listaDetalle"]!=""){
        $list=split("-",$_POST["listaDetalle"]);	
        for($c=0;$c<count($list);$c++){
            $rst1=$objProducto->obtenerDataSQL("select * from detallecategoria where iddetallecategoria=".$list[$c]);
            $dat1=$rst1->fetchObject();
            $carroDetalle[$dat1->iddetallecategoria]=array("iddetallecategoria"=>$dat1->iddetallecategoria,"descripcion"=>$dat1->descripcion,"abreviatura"=>$dat1->abreviatura);
        }
	}else{
	    $carroDetalle=null;
	}
    
	$rs = $objProducto->buscarxidproductoyidunidad($idproducto,$idsucursalproducto,$idunidad);	
    $reg=$rs->fetchObject();	
		
	IF(isset($carroPedido[($idproducto.'-'.$idsucursalproducto)])){
        $carroPedido[($idproducto.'-'.$idsucursalproducto)]["cantidad"]=$carroPedido[($idproducto.'-'.$idsucursalproducto)]["cantidad"]+$cantidad;
	}else{
        $carroPedido[($idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$reg->codigo,'producto'=>$reg->producto,
        'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$reg->unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,
        'moneda'=>$moneda,'carroDetalle'=>$carroDetalle);
    }
    
	$_SESSION['R_carroPedido']=$carroPedido;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cantidad</th>
	<th>Precio Ofertado</th>	
	<th>SubTotal</th>
	";
	foreach($carroPedido as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td>".$v["codigo"]."</td>";
		$registros.="<td>".utf8_decode($v["producto"]);
        if(count($v["carroDetalle"])>0){
            $registros.="<br />";
            foreach($v["carroDetalle"] as $x => $y){
                $registros.="*".$y["abreviatura"]." ";
            }		  
		}
        $registros.="</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],0,'.',' ')."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'>Quitar</a></td></tr>";
	}
    if($_POST["IdMesa"]=="111" || $_POST["IdMesa"]=="112" || $_POST["IdMesa"]=="113" || $_POST["IdMesa"]=="114"){
        $registros.="</table><div><center>Dinero: <input type='text' id='txtDinero' name='txtDinero' value='' size='6' onkeyup='calcularVuelto();' onKeyPress='return validarsolonumerosdecimales(event,this.value);' />&nbsp;&nbsp;Vuelto: <input type='text' id='txtVuelto' name='txtVuelto' value='0' readonly='' size='6' />&nbsp;&nbsp;Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' size='6' /></center></div>";        
    }else{
        $registros.="</table><div><center>Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' size='6' /></center></div>";    
    }
	
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="quitarProducto"){
	$idproducto=$_POST["IdProducto"];
	$idsucursalproducto=$_POST["IdSucursalProducto"];
	
	if(isset($_SESSION['R_carroPedido']))
		$carroPedido=$_SESSION['R_carroPedido'];
		
	unset($carroPedido[($idproducto.'-'.$idsucursalproducto)]);
	
	$_SESSION['R_carroPedido']=$carroPedido;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cantidad</th>
	<th>Precio Ofertado</th>	
	<th>SubTotal</th>
	";
	foreach($carroPedido as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td>".$v["codigo"]."</td>";
		$registros.="<td>".utf8_decode($v["producto"]);
        if(count($v["carroDetalle"])>0){
            $registros.="<br />";
            foreach($v["carroDetalle"] as $x => $y){
                $registros.="*".$y["abreviatura"]." ";
            }		  
		}
        $registros.="</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],0,'.',' ')."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'>Quitar</a></td></tr>";
	}
    if($_POST["IdMesa"]=="111" || $_POST["IdMesa"]=="112" || $_POST["IdMesa"]=="113" || $_POST["IdMesa"]=="114"){
        $registros.="</table><div><center>Dinero: <input type='text' id='txtDinero' name='txtDinero' value='' size='6' onkeyup='calcularVuelto();' onKeyPress='return validarsolonumerosdecimales(event,this.value);' />&nbsp;&nbsp;Vuelto: <input type='text' id='txtVuelto' name='txtVuelto' value='0' readonly='' size='6' />&nbsp;&nbsp;Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' size='6' /></center></div>";        
    }else{
        $registros.="</table><div><center>Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' size='6' /></center></div>";    
    }
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
		$idsucursalproducto=$reg->idsucursal;
		$producto=$reg->producto;
		$codigo=$reg->codigo;
		$idunidad=$reg->idunidad;
		$unidad=$reg->unidad;
		$cantidad=$reg->cantidad;
		$precioventa=$reg->precioventa;
		$moneda=$reg->moneda;
		//$preciomanoobra=$reg->preciomanoobra;
		$preciocompra=$reg->preciocompra;
        $rst=$objProducto->obtenerDataSQL("select * from detallecategoria where iddetallecategoria in (select iddetallecategoria from detallemovcategoria where idmovimiento=".$_POST["idmovimiento"]." and idsucursal=".$_SESSION["R_IdSucursal"]." and idproducto=".$idproducto.")");
        if($rst->rowCount()>0){
            while($dato1=$rst->fetchObject()){
                $carroDetalle[$dato1->iddetallecategoria]=array("iddetallecategoria"=>$dato1->iddetallecategoria,"descripcion"=>$dato1->descripcion,"abreviatura"=>$dato1->abreviatura);
            }
        }else{
            $carroDetalle=null;
        }  
		
		$carroPedido[($idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$codigo,'producto'=>$producto,'cantidad'=>$cantidad,
        'idunidad'=>$idunidad, 'unidad'=>$unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda,'carroDetalle'=>$carroDetalle);
	}
	$_SESSION['R_carroPedido']=$carroPedido;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cantidad</th>
	<th>Precio Ofertado</th>	
	<th>SubTotal</th>
	";
	foreach($carroPedido as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
		$registros.="<tr><td>".$v["codigo"]."</td>";
		$registros.="<td>".utf8_decode($v["producto"]);
		if(count($v["carroDetalle"])>0){
            $registros.="<br />";
            foreach($v["carroDetalle"] as $x => $y){
                $registros.="*".$y["abreviatura"]." ";
            }		  
		}
        $registros.="</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],0,'.',' ')."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'>Quitar</a></td></tr>";
	}
    if($_POST["IdMesa"]=="111" || $_POST["IdMesa"]=="112" || $_POST["IdMesa"]=="113" || $_POST["IdMesa"]=="114"){
        $registros.="</table><div><center>Dinero: <input type='text' id='txtDinero' name='txtDinero' value='".$_POST["Dinero"]."' size='6' onkeyup='calcularVuelto();' onKeyPress='return validarsolonumerosdecimales(event,this.value);' />&nbsp;&nbsp;Vuelto: <input type='text' id='txtVuelto' name='txtVuelto' value='0' readonly='' size='6' />&nbsp;&nbsp;Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' size='6' /></center></div>";        
    }else{
        $registros.="</table><div><center>Total: <input type='text' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' size='6' /></center></div>";    
    }
	$registros=utf8_encode($registros);
	echo $registros;
}
if($action=="agregarProductoMozo"){
    $class=$_POST["class"];
	$idproducto=$_POST["IdProducto"];
	$idsucursalproducto=$_POST["IdSucursalProducto"];
    $numerocomanda=$_POST["comanda"];
   	$cantidad=$_POST["Cantidad"];
    $actual = $_POST["Actual"];
    
    if($actual=="Actual"){
        unset($_SESSION["R_carroPedidoMozo"][($idproducto.'-'.$idsucursalproducto."-actual")]);
    }
    
    
    if($_POST["modo"]=="PlatosPredeterminado"){
        require("../modelo/clsListaUnidad.php");
    	$ObjListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    	$consulta = $ObjListaUnidad->buscarconxajax($_POST["IdProducto"],$_POST["IdSucursalProducto"],"S",$_SESSION["R_TipoCambio"]);
    	while($registro=$consulta->fetchObject()){
    		if($registro->idunidad==$registro->idunidadbase){ 
    			if($_SESSION["R_PrecioVenta"]=="J"){
    				$precioventa=$registro->precioventa;
                }elseif($_SESSION["R_PrecioVenta"]=="V"){
                    $precioventa=$registro->precioventa2;
                }elseif($_SESSION["R_PrecioVenta"]=="S"){
                    $precioventa=$registro->precioventa3;
                }elseif($_SESSION["R_PrecioVenta"]=="D"){
                    $precioventa=$registro->precioventa4;
                }
    			$StockActual=$registro->stockactual;
    			$preciocompra=$registro->preciocompra;
                $idunidad=$registro->idunidad;
                $moneda="S";
                exit();
    		}
    	}
    }else{
        $idunidad=$_POST["IdUnidad"];
    	$precioventa=$_POST["PrecioVenta"];
    	$StockActual=$_POST["StockActual"];
    	$moneda=$_POST["Moneda"];
    	$preciocompra=$_POST["PrecioCompra"];
    }
	
	
	require_once("../modelo/clsProducto.php");
	$objProducto = new clsProducto(11,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	
	
	if(isset($_SESSION['R_carroPedidoMozo']) && $_SESSION["R_carroPedidoMozo"]!=""){
		$carroPedido=$_SESSION['R_carroPedidoMozo'];
	}
		
	$rs = $objProducto->buscarxidproductoyidunidad($idproducto,$idsucursalproducto,$idunidad);	
    $reg=$rs->fetchObject();
    
    if($_POST["listaDetalle"]!=""){
        $list=explode("-",$_POST["listaDetalle"]);
        if(!empty($carroPedido[($idproducto.'-'.$idsucursalproducto.'-actual')])){
            $carroDetalleGuardado = $carroPedido[($idproducto.'-'.$idsucursalproducto.'-actual')]["carroDetalle"];
        }
        if(!empty($carroPedido[($idproducto.'-'.$idsucursalproducto)])){
            $carroDetalleTemporal = $carroPedido[($idproducto.'-'.$idsucursalproducto)]["carroDetalle"];
        }
        $numeroplato = 0;
        if(!empty($carroDetalleGuardado)){
            foreach ($carroDetalleGuardado as $key => $val) {
                if($val["numeroplato"]>$numeroplato){
                    $numeroplato = $val["numeroplato"];
                }
            }
        }
        if(!empty($carroDetalleTemporal)){
            foreach ($carroDetalleTemporal as $key => $val) {
                if($val["numeroplato"]>$numeroplato){
                    $numeroplato = $val["numeroplato"];
                }
            }
            $carroDetalle = $carroDetalleTemporal;
        }
        $numeroplato++;
        for($c=0;$c<count($list);$c++){
            $rst1=$objProducto->obtenerDataSQL("select * from detallecategoria where iddetallecategoria=".$list[$c]);
            $dat1=$rst1->fetchObject();
            $carroDetalle[$dat1->iddetallecategoria.'|'.$numeroplato]=array("iddetallecategoria"=>$dat1->iddetallecategoria,"descripcion"=>$dat1->descripcion,"abreviatura"=>$dat1->abreviatura,"numeroplato"=>$numeroplato);
        }
    }else{
        $carroDetalle=null;
    }
    if($_POST["Tipo"]=="T"){
    	$cantidad = $cantidad/$precioventa;
    }
    if(!empty($carroPedido[($idproducto.'-'.$idsucursalproducto)]) && $_POST["accionPropiedad"]=="Nuevo"){
        $cantidad = $carroPedido[($idproducto.'-'.$idsucursalproducto)]["cantidad"] + $cantidad;
    }
    if($reg->idproducto=="69" || $reg->idproducto=="98" || $reg->idproducto=="104" || $reg->idproducto=="101"){
    	$stock2=round($reg->stock*$reg->precioventa,2);
    }else{
    	$stock2 = $reg->stock;
    }
	$carroPedido[($idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$reg->codigo,'producto'=>$reg->producto." (".$stock2.")",'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$reg->unidad, 'precioventa'=>$precioventa,
    'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda,'abreviatura'=>$reg->abreviatura,'estado'=>'nuevo','kardex'=>$reg->kardex,'compuesto'=>$reg->compuesto,'categoria'=>$reg->categoria,'idimpresora'=>$reg->idimpresora,
    'impresora'=>$reg->impresora,'ipimpresora'=>$reg->ipimpresora,"carroDetalle"=>$carroDetalle,"comentario"=>$_POST["comentario"],"bar"=>$reg->bar);

	if($reg->idproductoref>0 && $reg->idproductoref!=""){
		$cortesia = $objProducto->buscarxidproductoyidunidad($reg->idproductoref,$idsucursalproducto,0)->fetchObject();
		$carroPedido[($cortesia->idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($cortesia->idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$cortesia->codigo,'producto'=>$cortesia->producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$cortesia->unidad, 'precioventa'=>0,'precioventaoriginal'=>0 ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>0,'moneda'=>$moneda,'abreviatura'=>$cortesia->abreviatura,'estado'=>'nuevo','kardex'=>$cortesia->kardex,'compuesto'=>$cortesia->compuesto,'categoria'=>$cortesia->categoria,'idimpresora'=>$cortesia->idimpresora,'impresora'=>$cortesia->impresora,'ipimpresora'=>$cortesia->ipimpresora,"carroDetalle"=>null,"comentario"=>'',"bar"=>$cortesia->bar);
	}

	$_SESSION['R_carroPedidoMozo']=$carroPedido;
	
	$contador=0;
	$suma=0;
    $band=false;
    if(strlen($_SESSION['R_NombreSucursal'])>18) $empresa=substr($_SESSION['R_NombreSucursal'],0,18)."<p style='margin:4px'>".substr($_SESSION['R_NombreSucursal'],18,strlen($_SESSION['R_NombreSucursal']));else $empresa=$_SESSION['R_NombreSucursal'];
        
	$registros.="<table id='tbpaginaweb' class='bordered highlight' width='100%'>
    <thead>
     <tr>
	<th class='center'>Cantidad</th>
	<th class='center'>Producto</th>
	<th class='center'>P. Unit.</th>
	<th class='center'>Subt.</th>
    <th class='center'>&nbsp;</th>	
     </tr>
	</thead></tbody>";$w=0;
	foreach($carroPedido as $k => $v){$w=$w+1;
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
        if($v["estado"]=="nuevo") $color="";else $color="yellow lighten-2";
        if($v["estado"]=="nuevo"){
        	$registros.="<tr class='$color'><td class='center' onclick=\"modalPropiedades(".$v["idproducto"].",'".$v["producto"]."','',".$v["cantidad"].",".$v["idsucursalproducto"].",'Actualizar');\">".number_format($v["cantidad"],0,'.',' ')."</td>";
        }else{
			$registros.="<tr class='$color'><td class='center'>".number_format($v["cantidad"],0,'.',' ')."</td>";
		}
 	    if($v["estado"]=="nuevo"){
			$registros.="<td onclick=\"modalPropiedades(".$v["idproducto"].",'".$v["producto"]."','',".$v["cantidad"].",".$v["idsucursalproducto"].",'Actualizar');\">".utf8_decode($v["producto"]);
		}else{
			if($_SESSION["R_IdPerfil"]==4){
                $registros.="<td ondblclick=\"modalPropiedades(".$v["idproducto"].",'".utf8_decode($v["producto"])."','Actual',".number_format($v["cantidad"],2,'.',' ').",".$_SESSION["R_IdSucursal"].");\">".utf8_decode($v["producto"]);
            }else{
				$registros.="<td>".utf8_decode($v["producto"]);			
			}
		}
		if(trim($v["comentario"])!=""){
			$registros.="<label style='display: block; margin-left: 10px;'>*".strtoupper($v["comentario"])." <br />";
		}
		if($v["carroDetalle"]!="" && count($v["carroDetalle"])>0){
            if(trim($v["comentario"])=="") $registros.="<label style='display: block; margin-left: 10px;'>";
            $numeroplato = 0;
            $primero = true;
            foreach($v["carroDetalle"] as $x => $y){
                if($numeroplato!=$y["numeroplato"]){
                    $numeroplato = $y["numeroplato"];
                    if($primero){
                        $registros.="PLATO ".$numeroplato.": ";
                        $primero=false;
                    }else{
                        $registros.="</br>PLATO ".$numeroplato.": ";
                    }
                }
                $registros.="*".$y["descripcion"]." ";
            }	
            $registros.="</label>";	  
		}elseif(trim($v["comentario"])!=""){
			$registros.="</label>";
		}

        $registros.="</td><td align='right'>".$v["precioventa"]."</td>";
		$registros.="<td align='right' class='center'><input type='checkbox' id='chkDescuento".$v["idproducto"]."' onclick='modalDescuento(".$v["idproducto"].",\"".$v["producto"]."\",".$v["precioventa"].",".$v["idsucursalproducto"].")' ><label for='chkDescuento".$v["idproducto"]."'></label>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		if($v["estado"]!="nuevo"){
            if($_SESSION["R_IdPerfil"]==4){
                $registros.="<td><a class='btn-floating red' href='#' onClick='quitarActual(".$v["idproducto"].",".$idsucursalproducto.",".$v["iddetallemovalmacen"].");'><i class='material-icons'>clear</i></a></td></tr>";
            }else{
            	$registros.="<td></td></tr>";
            }
        }else {
            $registros.="<td><a class='btn-floating red' href='#' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'><i class='material-icons'>clear</i></a></td></tr>";
        }
	}
	$registros.="</tbody></table><input size='6' type='hidden' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.','')."' /><input type='hidden' id='txtTotalProducto' name='txtTotalProducto' value='$w' />";
	$registros=utf8_encode($registros);
	echo $registros;//.$registros2.$registros3.$registros4
}

if($action=="quitarProductoMozo"){
	$class=$_POST["class"];
    $numerocomanda=$_POST["comanda"];
    $actual = $_POST["Actual"];
    
    
    $idproducto=$_POST["IdProducto"];
	$idsucursalproducto=$_POST["IdSucursalProducto"];
	
	if(isset($_SESSION['R_carroPedidoMozo']))
		$carroPedido=$_SESSION['R_carroPedidoMozo'];
	
        if($actual=="Actual"){
            require_once("../modelo/clsProducto.php");
            $objProducto = new clsProducto(11,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
            $rs = $objProducto->ejecutarSQL("SELECT eliminar_detalle_pedido(".$_POST['iddetalle'].",".$_SESSION['R_IdSucursal'].",".$_SESSION['R_IdUsuario'].",'".$_POST['comentario']."')");
            $rst=$objProducto->obtenerDataSQL("select * from salon where idsalon=(select idsalon from mesa where idmesa=(select idmesa from movimientohoy where idsucursal=".$_SESSION["R_IdSucursal"]." and idmovimiento in (select idmovimiento from detallemovalmacen where iddetallemovalmacen=".$_POST["iddetalle"]." and idsucursal=".$_SESSION["R_IdSucursal"].") and idsucursal=".$_SESSION["R_IdSucursal"].") and idsucursal=".$_SESSION["R_IdSucursal"].")")->fetchObject();
            if($rst->idsalon!="1"){//SALON DIFERENTE DE CLIENTE
                /*$dato=$objProducto->obtenerDataSQL("select * from impresora where idsucursal=".$_SESSION['R_IdSucursal']." and idimpresora in (select idimpresora from producto where idsucursal=".$_SESSION["R_IdSucursal"]." and idproducto=$idproducto)")->fetchObject();
                if($dato->idimpresora==2){
                    $connector = new WindowsPrintConnector("CAJA");    
                }else{
                    $connector = new NetworkPrintConnector($dato->ip, 9100);
                }
                
                $printer = new Printer($connector);
                $printer -> setTextSize(1,3 , 1,3);
                //$printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text(""."\n");
                $printer -> text(""."\n");
                $printer -> text(""."\n");
                $printer -> text(""."\n");
            	$printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text("ANULACION"."\n");
                $printer -> setTextSize(1,2 , 1,2);
                $printer -> text($dato->nombre."\n");
                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $rst1=$objProducto->obtenerDataSQL("select * from mesa where idmesa=(select idmesa from movimientohoy where idsucursal=".$_SESSION["R_IdSucursal"]." and idmovimiento in (select idmovimiento from detallemovalmacen where iddetallemovalmacen=".$_POST["iddetalle"]." and idsucursal=".$_SESSION["R_IdSucursal"].") and idsucursal=".$_SESSION["R_IdSucursal"].") and idsucursal=".$_SESSION["R_IdSucursal"])->fetchObject();
                $rst2=$objProducto->obtenerDataSQL("select * from personamaestro where idpersonamaestro=(select idpersonamaestro from persona where idpersona=(select idresponsable from movimientohoy where idmovimiento=(select idmovimiento from detallemovalmacen where iddetallemovalmacen=".$_POST["iddetalle"]." and idsucursal=".$_SESSION["R_IdSucursal"].") and idsucursal=".$_SESSION["R_IdSucursal"].") and idsucursal=".$_SESSION["R_IdSucursal"].")")->fetchObject();
                $printer -> text($rst1->numero." - ".$rst->descripcion." \n");
                $printer -> setTextSize(1,1 , 1,1);
                $printer -> text("Fecha: ".date("d-m-Y h:i:s")."\n");
                $printer -> text("Mozo: ".$rst2->nombres."\n");
                $printer -> text("Motivo: ".$_POST["comentario"]."\n");
                $printer -> text($carroPedido[($idproducto.'-'.$idsucursalproducto."-actual")]["cantidad"]."   ".$carroPedido[($idproducto.'-'.$idsucursalproducto."-actual")]["abreviatura"]."\n");
                $printer -> text(""."\n");
                $printer -> cut();
                
                $printer -> close();  */
            }          
            unset($carroPedido[($idproducto.'-'.$idsucursalproducto."-actual")]);
        }else{
            unset($carroPedido[($idproducto.'-'.$idsucursalproducto)]);
        }
	
	$_SESSION['R_carroPedidoMozo']=$carroPedido;
	
	$contador=0;
	$suma=0;
    $band=false;
       
    if(strlen($_SESSION['R_NombreSucursal'])>18) $empresa=substr($_SESSION['R_NombreSucursal'],0,18)."<p style='margin:4px'>".substr($_SESSION['R_NombreSucursal'],18,strlen($_SESSION['R_NombreSucursal']));else $empresa=$_SESSION['R_NombreSucursal'];
        
	$registros.="<table id='tbpaginaweb' class='bordered highlight' width='100%'>
    <thead>
     <tr>
	<th class='center'>Cantidad</th>
	<th class='center'>Producto</th>
	<th class='center'>P. Unit.</th>
	<th class='center'>Subt.</th>
    <th class='center'></th>
     </tr>
    </thead>
    <tbody>
	";
	$w=0;
	foreach($carroPedido as $k => $v){$w=$w+1;
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
        if($v["estado"]=="nuevo") $color="";else $color="yellow lighten-2";
		if($v["estado"]=="nuevo"){
        	$registros.="<tr class='$color'><td class='center' onclick=\"modalPropiedades(".$v["idproducto"].",'".$v["producto"]."','',".$v["cantidad"].",".$v["idsucursalproducto"].");\">".number_format($v["cantidad"],0,'.',' ')."</td>";
        }else{
			$registros.="<tr class='$color'><td class='center'>".number_format($v["cantidad"],0,'.',' ')."</td>";
		}
		if($v["estado"]=="nuevo"){
			if($_SESSION["R_IdPerfil"]==4){
                $registros.="<td ondblclick=\"modalPropiedades(".$v["idproducto"].",'".utf8_decode($v["producto"])."','Actual',".number_format($v["cantidad"],2,'.',' ').",".$_SESSION["R_IdSucursal"].",'Actualizar');\">".utf8_decode($v["producto"]);
            }else{
            	$registros.="<td onclick=\"modalPropiedades(".$v["idproducto"].",'".$v["producto"]."','',".$v["cantidad"].",".$v["idsucursalproducto"].",'Actualizar');\">".utf8_decode($v["producto"]);
            }
		}else{
			$registros.="<td>".utf8_decode($v["producto"]);
		}
		if(trim($v["comentario"])!=""){
			$registros.="<label style='display: block; margin-left: 10px;'>*".strtoupper($v["comentario"])." <br />";
		}
		if($v["carroDetalle"]!="" && count($v["carroDetalle"])>0){
            if(trim($v["comentario"])=="") $registros.="<label style='display: block; margin-left: 10px;'>";
            $numeroplato = 0;
            $primero = true;
            foreach($v["carroDetalle"] as $x => $y){
                if($numeroplato!=$y["numeroplato"]){
                    $numeroplato = $y["numeroplato"];
                    if($primero){
                        $registros.="PLATO ".$numeroplato.": ";
                        $primero=false;
                    }else{
                        $registros.="</br>PLATO ".$numeroplato.": ";
                    }
                }
                $registros.="*".$y["descripcion"]." ";
            }
            $registros.="</label>";	  
		}elseif(trim($v["comentario"])!=""){
			$registros.="</label>";
		}

        $registros.="</td><td align='right'>".$v["precioventa"]."</td>";
		$registros.="<td align='right' class='center'>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		if($v["estado"]!="nuevo"){
            if($_SESSION["R_IdPerfil"]==4){
                $registros.="<td><a class='btn-floating red' href='#' onClick='quitarActual(".$v["idproducto"].",".$idsucursalproducto.",".$v["iddetallemovalmacen"].");'><i class='material-icons'>clear</i></a></td></tr>";
            }else{
            	$registros.="<td></td></tr>";
            }
        }else {
            $registros.="<td><a class='btn-floating red' href='#' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'><i class='material-icons'>clear</i></a></td></tr>";
        }
	}
	$registros.="</tbody></table><input size='6' type='hidden' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' /><input type='hidden' id='txtTotalProducto' name='txtTotalProducto' value='$w' />";
    $registros=utf8_encode($registros);
	echo $registros;//.$registros2.$registros4
}

if($action=="actualizarProductoMozo"){
	$class=$_POST["class"];
    $numerocomanda=$_POST["comanda"];
    $actual = $_POST["Actual"];
    
    
    $idproducto=$_POST["idproducto"];
	$idsucursalproducto=$_POST["idsucursalproducto"];
	
	if(isset($_SESSION['R_carroPedidoMozo']) && $_SESSION["R_carroPedidoMozo"]!=""){
		$carroPedido=$_SESSION['R_carroPedidoMozo'];
	}
	
    $carroPedido[($idproducto.'-'.$idsucursalproducto)]["precioventa"]=$_POST["precioventa"];
	
	$_SESSION['R_carroPedidoMozo']=$carroPedido;
	
	$contador=0;
	$suma=0;
    $band=false;
       
    if(strlen($_SESSION['R_NombreSucursal'])>18) $empresa=substr($_SESSION['R_NombreSucursal'],0,18)."<p style='margin:4px'>".substr($_SESSION['R_NombreSucursal'],18,strlen($_SESSION['R_NombreSucursal']));else $empresa=$_SESSION['R_NombreSucursal'];
        
	$registros.="<table id='tbpaginaweb' class='bordered highlight' width='100%'>
    <thead>
     <tr>
	<th class='center'>Cantidad</th>
	<th class='center'>Producto</th>
	<th class='center'>P. Unit.</th>
    <th class='center'></th>
     </tr>
    </thead>
    <tbody>
	";
	$w=0;
	foreach($carroPedido as $k => $v){$w=$w+1;
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
        if($v["estado"]=="nuevo") $color="";else $color="yellow lighten-2";
		if($v["estado"]=="nuevo"){
        	$registros.="<tr class='$color'><td class='center' onclick=\"modalPropiedades(".$v["idproducto"].",'".$v["producto"]."','',".$v["cantidad"].",".$v["idsucursalproducto"].");\">".number_format($v["cantidad"],2,'.',' ')."</td>";
        }else{
			$registros.="<tr class='$color'><td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
		}
		if($v["estado"]=="nuevo"){
			if($_SESSION["R_IdPerfil"]==4){
                $registros.="<td ondblclick=\"modalPropiedades(".$v["idproducto"].",'".utf8_decode($v["producto"])."','Actual',".number_format($v["cantidad"],2,'.',' ').",".$_SESSION["R_IdSucursal"].",'Actualizar');\">".utf8_decode($v["producto"]);
            }else{
            	$registros.="<td onclick=\"modalPropiedades(".$v["idproducto"].",'".$v["producto"]."','',".$v["cantidad"].",".$v["idsucursalproducto"].",'Actualizar');\">".utf8_decode($v["producto"]);
            }
		}else{
			$registros.="<td>".utf8_decode($v["producto"]);
		}
		if(trim($v["comentario"])!=""){
			$registros.="<label style='display: block; margin-left: 10px;'>*".strtoupper($v["comentario"])." <br />";
		}
		if($v["carroDetalle"]!="" && count($v["carroDetalle"])>0){
            if(trim($v["comentario"])=="") $registros.="<label style='display: block; margin-left: 10px;'>";
            $numeroplato = 0;
            $primero = true;
            foreach($v["carroDetalle"] as $x => $y){
                if($numeroplato!=$y["numeroplato"]){
                    $numeroplato = $y["numeroplato"];
                    if($primero){
                        $registros.="PLATO ".$numeroplato.": ";
                        $primero=false;
                    }else{
                        $registros.="</br>PLATO ".$numeroplato.": ";
                    }
                }
                $registros.="*".$y["descripcion"]." ";
            }
            $registros.="</label>";	  
		}elseif(trim($v["comentario"])!=""){
			$registros.="</label>";
		}

        $registros.="</td><td align='right'>".$v["precioventa"]."</td>";
		$registros.="<td align='right' class='center'><input type='checkbox' id='chkDescuento".$v["idproducto"]."' onclick='modalDescuento(".$v["idproducto"].",\"".$v["producto"]."\",".$v["precioventa"].",".$v["idsucursalproducto"].")' ><label for='chkDescuento".$v["idproducto"]."'></label>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		if($v["estado"]!="nuevo"){
            if($_SESSION["R_IdPerfil"]==4){
                $registros.="<td><a class='btn-floating red' href='#' onClick='quitarActual(".$v["idproducto"].",".$idsucursalproducto.",".$v["iddetallemovalmacen"].");'><i class='material-icons'>clear</i></a></td></tr>";
            }else{
            	$registros.="<td></td></tr>";
            }
        }else {
            $registros.="<td><a class='btn-floating red' href='#' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'><i class='material-icons'>clear</i></a></td></tr>";
        }
	}
	$registros.="</tbody></table><input size='6' type='hidden' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' /><input type='hidden' id='txtTotalProducto' name='txtTotalProducto' value='$w' />";
    $registros=utf8_encode($registros);
	echo $registros;//.$registros2.$registros4
}

if($action=="agregarDetallesProductoMozo"){
	$class=$_POST["class"];
    $numerocomanda=$_POST["comanda"];
    
	require("../modelo/clsMovimiento.php");
	$objProducto = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		
	if(isset($_SESSION['R_carroPedidoMozo']))
		$carroPedido=$_SESSION['R_carroPedidoMozo'];
		
	$rs = $objProducto->buscarDetalleProductoxMesa($_POST['idmesa']);	
    while($reg=$rs->fetchObject()){	
        $iddetallemovalmacen = $reg->iddetallemovalmacen;
		$idproducto=$reg->idproducto;
		$idsucursalproducto=$reg->idsucursal;
		$producto=$reg->producto;
		$codigo=$reg->codigo;
		$idunidad=$reg->idunidad;
		$unidad=$reg->unidad;
		$cantidad=$reg->cantidad;
		$precioventa=$reg->precioventa;
		$moneda=$reg->moneda;
        $abreviatura=$reg->abreviatura;
		//$preciomanoobra=$reg->preciomanoobra;
		$preciocompra=$reg->preciocompra;
	    $idmovimiento=$reg->idmovimiento;
		//$rst=$objProducto->obtenerDataSQL("select * from detallecategoria where iddetallecategoria in (select iddetallecategoria from detallemovcategoria where idmovimiento=".$idmovimiento." and idsucursal=".$_SESSION["R_IdSucursal"]." and idproducto=".$idproducto.")");
        $rst=$objProducto->obtenerDataSQL("select dc.*,dmc.numeroplato,dmc.iddetallemovcategoria from detallemovcategoria dmc LEFT JOIN detallecategoria dc ON dmc.iddetallecategoria = dc.iddetallecategoria where dmc.idmovimiento=".$idmovimiento." and dmc.idsucursal=".$_SESSION["R_IdSucursal"]." and dmc.idproducto=".$idproducto." order by dmc.numeroplato");
		$carroDetalle=null;
        if($rst->rowCount()>0){
            while($dato1=$rst->fetchObject()){
                $carroDetalle[$dato1->iddetallecategoria."|".$dato1->numeroplato]=array("iddetallecategoria"=>$dato1->iddetallecategoria,"descripcion"=>$dato1->descripcion,"abreviatura"=>$dato1->abreviatura,"numeroplato"=>$dato1->numeroplato);
            }
        }	    	
		$carroPedido[($idproducto.'-'.$idsucursalproducto.'-actual')]=array('iddetallemovalmacen'=>$iddetallemovalmacen,'idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$codigo,'producto'=>$producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$unidad, 
                                                                            'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda,'abreviatura'=>$abreviatura,'estado'=>'actual',"comentario"=>$reg->comentario,
                                                                            "carroDetalle"=>$carroDetalle,'idimpresora'=>$reg->idimpresora);
	}
	$_SESSION['R_carroPedidoMozo']=$carroPedido;
	//print_r($_SESSION['R_carroPedidoMozo']);
	$contador=0;
	$suma=0;
    $band=false;
    
    if(strlen($_SESSION['R_NombreSucursal'])>18) $empresa=substr($_SESSION['R_NombreSucursal'],0,18)."<p style='margin:4px'>".substr($_SESSION['R_NombreSucursal'],18,strlen($_SESSION['R_NombreSucursal']));else $empresa=$_SESSION['R_NombreSucursal'];
        
    $registros.="<table id='tbpaginaweb' class='bordered highlight' width='100%'>
    <thead>
     <tr>
	<th class='center'>Cantidad</th>
	<th class='center'>Producto</th>
	<th class='center'>P. Unit.</th>
        <th class='center'></th>
     </tr>
    </thead>
    <tbody>
	";
    $w=0;
	foreach($carroPedido as $k => $v){$w=$w+1;
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$contador++;
        if($v["estado"]=="nuevo") $color="";else $color="yellow lighten-2";
		if($_SESSION["R_IdPerfil"]==4){
                    $registros.="<tr class='$color'><td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
                }else{
                    $registros.="<tr class='$color'><td class='center'>".number_format($v["cantidad"],2,'.',' ')."</td>";
                }
		if($_SESSION["R_IdPerfil"]==4){
                    $registros.="<td ondblclick=\"modalPropiedades(".$v["idproducto"].",'".utf8_decode($v["producto"])."','Actual',".number_format($v["cantidad"],2,'.',' ').",".$_SESSION["R_IdSucursal"].",'Actualizar');\">".utf8_decode($v["producto"]);
                }else{
                    $registros.="<td>".utf8_decode($v["producto"]);
                }
		if($v["precioventa"]>0) $registros2.="<td>".utf8_encode($v["abreviatura"])."</td>";
		if(trim($v["comentario"])!=""){
			$registros.="<label style='display: block; margin-left: 10px;'>*".strtoupper($v["comentario"])." <br />";
		}
		if(count($v["carroDetalle"])>0){
            if(trim($v["comentario"])=="") $registros.="<label style='display: block; margin-left: 10px;'>";
            $numeroplato = 0;
            $primero = true;
            foreach($v["carroDetalle"] as $x => $y){
                if($numeroplato!=$y["numeroplato"]){
                    $numeroplato = $y["numeroplato"];
                    if($primero){
                        $registros.="PLATO ".$numeroplato.": ";
                        $primero=false;
                    }else{
                        $registros.="</br>PLATO ".$numeroplato.": ";
                    }
                }
                $registros.="*".$y["descripcion"]." ";
            }	
            $registros.="</label>";	  
		}elseif(trim($v["comentario"])!=""){
			$registros.="</label>";
		}
		$registros.="</td>";
		$registros.="<td align='right' class='center'>".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."</td>";
		if($v["estado"]!="nuevo"){
            if($_SESSION["R_IdPerfil"]==4){
                $registros.="<td><a class='btn-floating red' href='#' onClick='quitarActual(".$v["idproducto"].",".$idsucursalproducto.",".$v["iddetallemovalmacen"].");'><i class='material-icons'>clear</i></a></td></tr>";
            }else{
                $registros.="<td></td></tr>";
            }
        }else {
		    $registros.="<td><a class='btn-floating red' href='#' onClick='quitar(".$v["idproducto"].",".$idsucursalproducto.");'><i class='material-icons'>clear</i></a></td></tr>";
            $band=true;
        }
	}
	$registros.="</tbody></table><input type='hidden' size='6' name='txtTotal' id='txtTotal' readonly='true' value='".number_format($suma,2,'.',' ')."' /><input type='hidden' id='txtidmov' name='txtidmov' value='".$idmovimiento."' /><input type='hidden' id='txtTotalProducto' name='txtTotalProducto' value='$w' />";
    $registros=utf8_encode($registros);
	echo $registros;//.$registros2.$registros3.$registros4
}

if($action=="verificarusuario"){
    require("../modelo/clsMovimiento.php");
	$objMovimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$rs = $objMovimiento->buscarUsuarioxMesa2($_POST["IdMesa"]);
    $dato = $rs->fetchObject();
    //print_r($rs);
    if($dato->idusuario==$_SESSION["R_IdUsuario"] || $_SESSION["R_IdPerfil"]==4){
        echo json_encode(array("modificar"=>true,"situacion"=>$dato->situacion));
    }else{
        echo json_encode(array("modificar"=>false,"situacion"=>$dato->situacion));
    }
}

if($action=="verificarmesa"){
    $situacion='N';
    require("../modelo/clsMesa.php");
    $ObjMesa = new clsMesa(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $consulta = $ObjMesa->consultarMesaxSalon2($_POST["idsalon"],$situacion);

    if($consulta->rowCount()>0){
        while($registro=$consulta->fetchObject()){
            if($registro->numero == $_POST['txtMesa']){
                $disponible="true";
                $idmesa=$registro->idmesa;
                $nropersonas=$registro->nropersonas;
                exit();			    
            }else{
               $disponible="false";
               $idmesa="0";
               $nropersonas="0";
            }
        }
    }else{
       $disponible="false";
       $idmesa="0";
       $nropersonas="0";
    }
    if($idmesa=="0"){
        $consulta = $ObjMesa->consultarMesaxSalon($_POST["idsalon"],'O');
        while($registro=$consulta->fetchObject()){
            if($registro->numero==$_POST['txtMesa']){
                $disponible="false";
                $idmesa=$registro->idmesa;
                $nropersonas=$registro->nropersonas;
                exit();
            }
        }
    }
    if($idmesa=="0"){
        $consulta = $ObjMesa->consultarMesaxSalon($_POST["idsalon"],'C');
        while($registro=$consulta->fetchObject()){
            if($registro->numero==$_POST['txtMesa']){
                $disponible="false";
                $idmesa=$registro->idmesa;
                $nropersonas=$registro->nropersonas;
                exit();
            }
        }
    }
	echo "var vdisponible=".$disponible.";
    var vidmesa=".$idmesa.";
    var vnropersonas=".$nropersonas.";";
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
			if($registro->situacion=='N' or (($registro->situacion=='O' or $registro->situacion=='R') and $registro->idmesa==$seleccionado)){
				$seleccionar="";
				if($registro->idmesa==$seleccionado) $seleccionar="selected";
				$Mesas=$Mesas."<option value='".$registro->idmesa."' ".$seleccionar.">".$registro->numero." | ".$registro->nropersonas." personas</option>";
			}
		}
	}else{
		$Mesas=$Mesas."<option value='0'>No hay mesas disponibles</option>";
	}
	$Mesas=$Mesas."</select>";
	$Mesas=utf8_encode($Mesas);
	echo $Mesas;
}
if($action=="genera_diagramaMesas"){
	require("../modelo/clsMesa.php");
	$ObjMesa = new clsMesa(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$ObjMesa->reservarMesas($_SESSION['R_IdSucursal']);
	$consulta = $ObjMesa->consultarMesaxSalon2($_POST["IdSalon"],'%');

	if($consulta->rowCount()>0){
		$numMesas=$consulta->rowCount();
		$numCol=11;
		if($numMesas>=$numCol) {if($numMesas%$numCol==0){$limite=$numMesas/$numCol;}else{$limite=$numMesas/$numCol+1;} }else{ $limite=1;$numCol=$numMesas;}
		$Mesas.="<table border=\"1\">";
		for($i=1;$i<=$limite;$i++){
			$Mesas.="<tr>";
			for($j=1;$j<=$numCol;$j++){
				if($registro=$consulta->fetchObject()){
					$Mesas.="<td id= \"$registro->idmesa\" onClick=\"javascript:setRun('vista/mantPedido', 'accion=NUEVO&id_clase=46&idsalon=".$_POST['IdSalon']."&idmesa=$registro->idmesa&situacionmesa=$registro->situacion&idmovimientoreserva=$registro->idmovimiento&nroreserva=$registro->nromovimiento', 'cargamant','cargamant', 'img04');\" style=\"background-image:url(img/hot_rest2.jpg); width:100px; height:90px; background-position:center; cursor:pointer\"  valign=\"baseline\"><div align=\"right\"><span title=\"Capacidad\">$registro->nropersonas</span></div><div align=\"center\"><font size=\"+1\"><b>$registro->numero</b></font><br>";
					if($registro->situacion=='O'){
						$Mesas.="<img src=\"img/ocupado.png\" width=32 height=32>";
					}elseif($registro->situacion=='R'){
						$Mesas.="<img src=\"img/reservado.png\" width=32 height=32>";
					}
					if(isset($registro->transcurrido)){
						if(substr($registro->transcurrido,0,1)=='-'){
							$transcurrio=str_replace('-','',$registro->transcurrido);
							$transcurrio=str_replace('days','d&iacute;as',$transcurrio);
							$Mesas.="<br>Falta ".$transcurrio;
						}else{
							$transcurrio=str_replace('days','d&iacute;as',$registro->transcurrido);
							if($registro->situacion=='O'){
								if($registro->situacionmovimiento=='O'){
									$Mesas.="<br>Pedido hace ".$transcurrio;
								}elseif($registro->situacionmovimiento=='A'){
									$Mesas.="<br>Atendido hace ".$transcurrio;
								}else{
									$Mesas.="<br>Paso ".$transcurrio;
								}
							}else{
								$Mesas.="<br>Paso ".$transcurrio;
							}
						}
					}
					$Mesas.="</div></td>";
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
if($action=="genera_diagramaMesasMozo"){
	require("../modelo/clsMesa.php");
	$ObjMesa = new clsMesa(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$ObjMesa->reservarMesas($_SESSION['R_IdSucursal']);
	$consulta = $ObjMesa->consultarMesaxSalon2($_POST["IdSalon"],'%');

	if($consulta->rowCount()>0){
		$numMesas=$consulta->rowCount();
		$numCol=10;
		if($numMesas>=$numCol) {if($numMesas%$numCol==0){$limite=$numMesas/$numCol;}else{$limite=$numMesas/$numCol+1;} }else{ $limite=1;$numCol=$numMesas;}
                $Mesas.='<div class="Div-Activo"><div class="container Mesas"><div class="row">';
                //SITUACION CUENTA IMPRESA POR MOZO
                //TIENE LA CLASE "MesaImpresa" en lugar de "MesaInactiva" POR EJEMPLO LA MESA 99
                //$Mesas.='<div class="col s6 m4 l2"><div class="card-panel hoverable z-depth-1 Mesa MesaImpresa" id= "0"><div class="row"><div class="col s12 center"><div class="Mesa-titulo">MESA 99</div></div></div><div class="row"><div class="col s8 offset-s2 center"><div class="Mesa-tiempo">'.$tiempo.'</div></div></div><div class="row"><div class="col s12 center"><div class="Mesa-mozo truncate">EESPINOZAL</div></div></div><div class="row"><div class="col s10 offset-s1 m10 offset-m1 l12 center"><div class="Mesa-monto">S/. 999.99</div></div></div></div></div>';
		for($i=1;$i<=$limite;$i++){
                    for($j=1;$j<=$numCol;$j++){
                        if($registro=$consulta->fetchObject()){
                            $tiempo = "";
                            if(isset($registro->transcurrido)){
                                if(substr($registro->transcurrido,0,1)=='-'){
                                    $transcurrio=str_replace('-','',$registro->transcurrido);
                                    $transcurrio=str_replace('days','d&iacute;as',$transcurrio);
                                    $tiempo.="Falta ".$transcurrio;
                                }else{
                                    $transcurrio=str_replace('days','d&iacute;as',$registro->transcurrido);
                                    if($registro->situacion=='O' || $registro->idmovimiento>0){
                                        if($registro->situacionmovimiento=='O'){
                                            $tiempo.="Pedido hace ".$transcurrio;
                                        }elseif($registro->situacionmovimiento=='A'){
                                            $tiempo.="Atendido hace ".$transcurrio;
                                        }else{
                                            $tiempo.="Paso ".$transcurrio;
                                        }
                                    }else{
                                        $tiempo.="Paso ".$transcurrio;
                                    }
                                }
                            }
                            $tiempo=$transcurrio;
                            //$tiempo = "59:45";
                            if($registro->situacion=='O' && $registro->idmovimiento>0){
                                $Mesas.='<div class="col s6 m4 l2"><div class="card-panel hoverable z-depth-1 Mesa MesaInactiva" id= "'.$registro->idmesa.'" onClick="javascript:verificarmesa(\''.$registro->numero.'\',\''.$_POST["IdSalon"].'\');"><div class="row"><div class="col s12 center"><div class="Mesa-titulo">'.($_POST["IdSalon"]==1?$registro->nombrespersona:$registro->numero).'</div></div></div><div class="row"><div class="col s8 offset-s2 center"><div class="Mesa-tiempo">'.$tiempo.'</div></div></div><div class="row"><div class="col s12 center"><div class="Mesa-mozo truncate">'.  strtoupper($registro->nombreusuario).'</div></div></div><div class="row"><div class="col s10 offset-s1 m10 offset-m1 l12 center"><div class="Mesa-monto">S/. '.  number_format($registro->total, 2).'</div></div></div></div></div>';
                            }elseif($registro->situacion=='U'){
                                $ObjMesa2 = new clsMesa(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
                                $result = $ObjMesa2->obtenerDataSQL("SELECT * FROM mesaunida WHERE idmesa = $registro->idmesa AND idsucursal = ".$_SESSION['R_IdSucursal']);
                                $mesa_padre = $result->fetchObject()->idmesa_padre;
                                $consulta2 = $ObjMesa2->consultarMesaxSalon2xId($_POST["IdSalon"],$mesa_padre);
                                $result = $consulta2->fetchObject();
                                $Mesas.='<div class="col s6 m4 l2"><div class="card-panel hoverable z-depth-1 Mesa MesaInactiva" id= "'.$result->idmesa.'" onClick="javascript:verificarmesa(\''.$result->numero.'\',\''.$_POST["IdSalon"].'\');"><div class="row"><div class="col s12 center"><div class="Mesa-titulo">'.$registro->numero.'</div></div></div><div class="row"><div class="col s8 offset-s2 center"><div style="font-size: 1.2rem;background-color: #006064;color: #84ffff;border-radius: 10px;">U-'.$result->numero.'</div></div></div><div class="row"><div class="col s12 center"><div class="Mesa-mozo truncate">'.  strtoupper($result->nombreusuario).'</div></div></div><div class="row"><div class="col s10 offset-s1 m10 offset-m1 l12 center"><div class="Mesa-monto">S/. '.  number_format($result->total, 2).'</div></div></div></div></div>';
                            }elseif($registro->situacion=='R'){
                                $Mesas.="<img src=\"img/reservado.png\" width=32 height=32>";
                            }elseif($registro->situacion=='C'){
                            	$Mesas.='<div class="col s6 m4 l2"><div class="card-panel hoverable z-depth-1 Mesa MesaImpresa" id= "'.$registro->idmesa.'" onClick="javascript:verificarmesa(\''.$registro->numero.'\',\''.$_POST["IdSalon"].'\');"><div class="row"><div class="col s12 center"><div class="Mesa-titulo">'.($_POST["IdSalon"]==1?$registro->nombrespersona:$registro->numero).'</div></div></div><div class="row"><div class="col s8 offset-s2 center"><div class="Mesa-tiempo">'.$tiempo.'</div></div></div><div class="row"><div class="col s12 center"><div class="Mesa-mozo truncate">'.  strtoupper($registro->nombreusuario).'</div></div></div><div class="row"><div class="col s10 offset-s1 m10 offset-m1 l12 center"><div class="Mesa-monto">S/. '.  number_format($registro->total, 2).'</div></div></div></div></div>';
                            }else{
                                $Mesas.='<div class="col s6 m4 l2"><div class="card-panel hoverable z-depth-1 Mesa MesaActiva" id= "'.$registro->idmesa.'" onClick="javascript:verificarmesa(\''.$registro->numero.'\',\''.$_POST["IdSalon"].'\');"><div class="row"><div class="col s12 m12 l12 center"><div class="Mesa-titulo">'.$registro->numero.'<div class="right">'.$registro->nropersonas.'</div></div></div></div></div></div>';
                            }
                        }
                    }
		}
		$Mesas.="</div></div></div>";
	}else{
		$Mesas.="No hay mesas disponibles";
	}
        
	$Mesas=utf8_encode($Mesas);
	echo $Mesas;
}
if($action=="generaNumero"){
	require("../modelo/clsMovimiento.php");
	$objMovimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$datosR=explode('-',$_POST["IdMesero"]);
	//echo " console.log(JSON.parse('".json_encode($_SESSION)."'));";
	$numero = $objMovimiento->generaNumeroxMesero(6,$_SESSION['R_IdSucursal']);
	//$numero = "ERROR CONSLTA";

	echo "vnumero='".$numero."';";
}

//$printer = "\\\\ASISTENTE\\EPSON L200 Series (Copiar 1)";

if($action=="imprimir_cuenta"){
	require("../modelo/clsDetalleAlmacen.php");
	require("../modelo/clsImpresora.php");
	
	$objMovimientoAlmacen = new clsDetalleAlmacen($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$objImpresora = new clsImpresora(68,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    
    /* Most printers are open on port 9100, so you just need to know the IP 
     * address of your receipt printer, and then fsockopen() it on that port.
     */
	//CONSULTAR MOVIMIENTO
	$txtNombrePersona = $objMovimientoAlmacen->obtenerDataSQL("SELECT * FROM movimientohoy where idmovimiento=".$_POST["idmovimiento"])
					->fetchObject()->nombrespersona;

    try {
        $rs=$objImpresora->consultarImpresora(100000,1,'idimpresora','1',0,$_SESSION["R_IdSucursal"],'','');
	    $c=0;
	    if($_SESSION['R_carroPedidoMozo']==''){
        	$carroPedido=$_SESSION['R_carroPedidoMozo2'];
        }else{
			$carroPedido=$_SESSION['R_carroPedidoMozo'];
        } 
	    while($dato=$rs->fetchObject()){
	        $band=true;
	        foreach($carroPedido as $k => $v){
				if($v["idimpresora"]==$dato->idimpresora){
					if($band){
				        /*if($_SESSION["R_IdCaja"]==2){
				    		$connector = new NetworkPrintConnector("192.168.1.102", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==1){
				    		$connector = new NetworkPrintConnector("192.168.1.101", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==3){
				    		$connector = new NetworkPrintConnector("192.168.1.103", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==4){
				    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==5){
				    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
				    	}else{*/
				    		$connector = new WindowsPrintConnector("CAJA");
				    	//}
				        /* Print a "Hello world" receipt" */
				        $printer = new Printer($connector);
				        //$printer -> setJustification(Printer::JUSTIFY_CENTER);
				        $printer -> setTextSize(2 , 2);
				        $printer -> setJustification(Printer::JUSTIFY_CENTER);
				        $printer -> text("EL CLUB");
				        $printer -> feed();
					    $printer -> text("ENTREGADO - CAJA".($_SESSION["R_IdCaja"]-1)."\n");
					    $printer -> text("CLIENTE: ".$txtNombrePersona."\n");
					    $printer -> setTextSize(1 , 1);
				        $printer -> feed();
				        $printer -> text("Nro: ".($_POST["numerocomanda"])."  Mozo: ".($_POST["mesero"])."\n");
				        //$printer -> setTextSize(1,1 , 1,1);
				        $printer -> setJustification(Printer::JUSTIFY_LEFT);
				        $printer -> text("-----------------------------------------"."\n");
				    	$printer -> text("Cant.  Descr.              P.Unit.   Subt.\n");
				        $printer -> text("-----------------------------------------"."\n");
				        $band=false;
				        $suma=0;
				    }
    	   			$subto=$v['cantidad']*$v['precioventa'];
	        		$suma=$suma+$subto;
            		$printer -> text(number_format($v["cantidad"],0,'.','')."  ".str_pad((substr($v["abreviatura"],0,25)),25," ").($v["precioventa"]<10?" ":"").number_format($v["precioventa"],2,'.','')."   ".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."\n");    	                
    			}
    		}
    		if(!$band){
		        $printer -> text("----------------------------------------"."\n");
		        //$printer -> setTextSize(2 , 1,7);
		        $printer -> text("Total   ".number_format($suma,2,'.',' ')."\n");
		        $printer -> text("-----------------------------------------");
		        $printer -> feed();
		        $printer -> text("Fecha: ".date("d-m-Y H:i:s")."\n");
		        $printer -> cut();
		        
		        /* Close printer */
		        $printer -> close();
		    }
		}
        
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }   

	//$res = $objMovimientoAlmacen->cambiarSituacionMesa($_POST["idmovimiento"],'C');
	//echo($res);
}

if($action=="imprimir_cuenta2"){
	require("../modelo/clsDetalleAlmacen.php");
	require("../modelo/clsImpresora.php");
	$objMovimientoAlmacen = new clsDetalleAlmacen($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$objImpresora = new clsImpresora(68,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    
    /* Most printers are open on port 9100, so you just need to know the IP 
     * address of your receipt printer, and then fsockopen() it on that port.
     */
    try {
        $rs1=$objMovimientoAlmacen->buscarDetalleProducto($_POST["idmovimiento"]);
	    $carroPedido=array();
	    while($dat=$rs1->fetchObject()){
	        $carroPedido[]=array("cantidad"=>$dat->cantidad,"precioventa"=>$dat->precioventa,"abreviatura"=>trim($dat->comentario==""?$dat->abreviatura:$dat->comentario),"idimpresora"=>$dat->idimpresora);
	    }

        $dat=$objMovimientoAlmacen->obtenerDataSQL("select * from movimientohoy where idmovimiento=".$_POST["idmovimiento"])->fetchObject();
        $mozo=$objMovimientoAlmacen->obtenerDataSQL("select * from personamaestro where idpersonamaestro=(select idpersonamaestro from persona where idpersona=$dat->idresponsable and idsucursal=".$_SESSION["R_IdSucursal"].")")->fetchObject();

        $rs=$objImpresora->consultarImpresora(100000,1,'idimpresora','1',0,$_SESSION["R_IdSucursal"],'','');
	    $c=0;
	    while($dato=$rs->fetchObject()){
	        $band=true;
	        foreach($carroPedido as $k => $v){
				if($v["idimpresora"]==$dato->idimpresora){
					if($band){
						/*if($_SESSION["R_IdCaja"]==2){
				    		$connector = new NetworkPrintConnector("192.168.1.102", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==1){
				    		$connector = new NetworkPrintConnector("192.168.1.101", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==3){
				    		$connector = new NetworkPrintConnector("192.168.1.103", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==4){
				    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
				    	}elseif($_SESSION["R_IdCaja"]==5){
				    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
				    	}else{*/
				    		$connector = new WindowsPrintConnector("CAJA");
				    	//}
				        /* Print a "Hello world" receipt" */
				        $printer = new Printer($connector);
				        //$printer -> setJustification(Printer::JUSTIFY_CENTER);
				        //$printer -> bitImage($tux,Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
				        $printer -> setTextSize(2 , 2);
				        $printer -> setJustification(Printer::JUSTIFY_CENTER);
				        $printer -> text("EL CLUB");
				        $printer -> feed();
					    $printer -> text("ENTREGADO - CAJA".($_SESSION["R_IdCaja"] - 1)."\n");
					    $printer -> setTextSize(1 , 1);
				        $printer -> feed();
				        $printer -> text("Nro: ".($dat->numero)."  Mozo: ".($mozo->nombres)."\n");
				        //$printer -> setTextSize(1,1 , 1,1);
				        $printer -> setJustification(Printer::JUSTIFY_LEFT);
				        $printer -> text("-----------------------------------------"."\n");
				    	$printer -> text("Cant.  Descr.              P.Unit.   Subt.\n");
				        $printer -> text("-----------------------------------------"."\n");
				        $suma=0;
				        $band=false;
					}
            		$subto=$v['cantidad']*$v['precioventa'];
	        		$suma=$suma+$subto;
            		$printer -> text(number_format($v["cantidad"],0,'.','')."  ".str_pad((substr($v["abreviatura"],0,25)),25," ").($v["precioventa"]<10?" ":"").number_format($v["precioventa"],2,'.','')."   ".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."\n");    	                
            	}
            }
            if(!$band){
         		$printer -> text("----------------------------------------"."\n");
		        //$printer -> setTextSize(2 , 1,7);
		        $printer -> text("Total   ".number_format($suma,2,'.',' ')."\n");
		        $printer -> text("-----------------------------------------");
		        $printer -> feed();
		        $printer -> text("Fecha: ".date("d-m-Y H:i:s")."\n");
		        $printer -> cut();
		        /* Close printer */
		        $printer -> close();   
        	}
    	}
        
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }   

	//$res = $objMovimientoAlmacen->cambiarSituacionMesa($_POST["idmovimiento"],'C');
	//echo($res);
}

if($action=="imprimir_comanda"){
    try {
        $connector = new WindowsPrintConnector("CAJA");
        
        /* Print a "Hello world" receipt" */
        $printer = new Printer($connector);
        $printer -> setTextSize(1,2 , 1,2);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
    	   $printer -> text("REIMPRESION DE COMANDA"."\n");
        $printer -> setTextSize(1,1 , 1,1);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text(""."\n");
        $printer -> text("M".strtolower(substr($_POST["mesa"],1,strlen($_POST["mesa"])))."\n");
        $printer -> text("Mozo: ".substr($_SESSION['R_NombresPersona'],0,15)."\n");
    	$printer -> text("-----------------------------------------"."\n");
    	$printer -> text("Cant.       Descr.\n");
        $printer -> text("-----------------------------------------"."\n");
    	$carroPedido=$_SESSION['R_carroPedidoMozo'];
    	$c = 160;
    	foreach($carroPedido as $k => $v){
			$subto=$v['cantidad']*$v['precioventa'];
	        $suma=$suma+$subto;
            $descuento=$descuento+round($v['cantidad']*$v['precioventa']*$_POST[""]/100,2);
	        $c=$c+40;
            $printer -> text(number_format($v["cantidad"],0,'.','')."   ".str_pad(utf8_decode(substr($v["abreviatura"],0,25)),26," ")."\n");
            //$printer -> setJustification(Printer::JUSTIFY_RIGHT);
            //$printer -> text(($v["precioventa"]<10?" ":"").number_format($v["precioventa"],2,'.','')."     ".number_format($v["cantidad"],0,'.','')."     ".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."\n");    	                
    	}
        $printer -> text("-----------------------------------------"."\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setTextSize(2 , 1,7);
        $printer -> setTextSize(1,1 , 1,1);
        $printer -> cut();
        
        /* Close printer */
        $printer -> close();
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }
       
}
//$printer = "\\\\ASISTENTE\\EPSON L200 Series (Copiar 1)";
if($action=="imprimir_ticket"){
	try {error_log("SE IMPRIMIO TICKET DESDE: ".$_SERVER["REMOTE_ADDR"]);} catch (Exception $e) {}
    require("../modelo/clsImpresora.php");
    require("../modelo/clsMovimiento.php");    
    $objImpresora = new clsImpresora(68,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objMovimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $rs2=$objMovimiento->consultarNumeroComanda($_SESSION['R_IdSucursal']);
    if($rs2->rowCount()>0){
        $data2=$rs2->fetchObject();
        $numero=$data2->numerocomanda;   
    }else{
        $numero=1;
    } 
    $rs2=$objMovimiento->consultarMovimiento(100, 1, 1, 1, $_POST["txtId"],0);
    $dat2=$rs2->fetchObject();      

    $rs=$objImpresora->consultarImpresora(100000,1,'idimpresora','1',0,$_SESSION["R_IdSucursal"],'','');
    $c=0;
    while($dato=$rs->fetchObject()){
        $band=true;
        if($_SESSION['R_carroPedidoMozo']==''){
        	$carroPedido=$_SESSION['R_carroPedidoMozo2'];
        }else{
			$carroPedido=$_SESSION['R_carroPedidoMozo'];
        }   
        foreach($carroPedido as $k => $v){
            if(($v["idimpresora"]==$dato->idimpresora && trim($_POST["salon"])!="CLIENTE" && $v["estado"]=="nuevo") || ($v["idimpresora"]==$dato->idimpresora && trim($_POST["salon"])=="CLIENTE")){//$v["estado"]=="nuevo" && 
                //$handle = printer_open(trim($dato->nombre));
                if($band){
                	/*if($_SESSION["R_IdCaja"]==2){
                		$connector = new NetworkPrintConnector("192.168.1.102", 9100);
                	}elseif($_SESSION["R_IdCaja"]==1){
                		$connector = new NetworkPrintConnector("192.168.1.101", 9100);
                	}elseif($_SESSION["R_IdCaja"]==3){
                		$connector = new NetworkPrintConnector("192.168.1.103", 9100);
                	}elseif($_SESSION["R_IdCaja"]==4){
                		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
                	}elseif($_SESSION["R_IdCaja"]==5){
                		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
                	}else{*/
                		$connector = new WindowsPrintConnector("CAJA");
                	//}
                    //$connector = new NetworkPrintConnector($dato->ip, 9100);
                    /* Print a "Hello world" receipt" */
                    $printer = new Printer($connector);
                    $printer -> setTextSize(1,2 , 1,2);
                    //$printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $printer -> text(""."\n");
                    $printer -> text(""."\n");
                    $printer -> text(""."\n");
                    $printer -> text(""."\n");
                	$printer -> setJustification(Printer::JUSTIFY_CENTER);
                	if($_SESSION["R_IdCaja"]==2){
                    	$printer -> text("CAJA1"."\n");
                    }elseif($_SESSION["R_IdCaja"]==1){
                    	$printer -> text("CAJA1"."\n");
                    }elseif($_SESSION["R_IdCaja"]==3){
                    	$printer -> text("CAJA2"."\n");
                    }elseif($_SESSION["R_IdCaja"]==4){
                    	$printer -> text("CAJA4"."\n");
                    }elseif($_SESSION["R_IdCaja"]==5){
                    	$printer -> text("CAJA5"."\n");
                    }else{
                    	$printer -> text($dato->nombre."\n");
                    }
                    $printer -> setJustification(Printer::JUSTIFY_LEFT);
                    $printer -> setTextSize(1,1 , 1,1);
	                $printer -> text("Fecha: ".date("d-m-Y h:i:s")."\n");
                    //$printer -> setTextSize(2 , 1,2);
                    $printer -> text("Nro: ".($_POST["numerocomanda"])."\n");
                    $printer -> setTextSize(1,1 , 1,1);                 
                    $printer -> text("------------------------------------------"."\n");
	                $band=false;
            	}
                $printer -> setTextSize(2 , 1,2);
            	$printer -> text(number_format($v["cantidad"],0,'.',' ')."   ".(substr($v["abreviatura"],0,20))."  ".number_format($v["precioventa"]*$v["cantidad"],2,'.','')."\n");
                $printer -> setTextSize(1,1 , 1,1);
                if(trim($v["comentario"])!=""){
                    $printer -> text(strtoupper("*".$v["comentario"])."\n");
				}
				$registros="";
				if(count($v["carroDetalle"])>0){
				    $numeroplato = 0;
                    $primero = true;
            		foreach($v["carroDetalle"] as $x => $y){
                        if($v["bar"]=="N"){//NO MUESTRO CUANDO ES BAR
                            if($numeroplato!=$y["numeroplato"]){
                                $numeroplato = $y["numeroplato"];
                                if($primero){
                                    $registros.="PLATO ".$numeroplato.": ";
                                    $primero=false;
                                }else{
                                    $registros.="\n PLATO ".$numeroplato.": ";
                                }
                            }
                        }else{
                            if($numeroplato!=$y["numeroplato"]){
                                $numeroplato = $y["numeroplato"];
                                if($primero){
                                    //$registros.="PLATO ".$numeroplato.": ";
                                    $primero=false;
                                }else{
                                    $registros.="\n ";
                                }
                            }
                        }
                		$registros.="*".$y["descripcion"]." ";
            		}	
                    $printer -> text(strtoupper($registros)."\n");	
				}
            }        
        }
        if(!$band){
            $printer -> text("-----------------------------------------"."\n");
            $printer -> text(""."\n");
            $printer -> text(""."\n");
            $printer -> cut();
            /* Close printer */
            $printer -> close();            
        }
    }
    $numero=$numero+1;
    $band=$objMovimiento->actualizarNumeroComanda($numero,$_SESSION['R_IdSucursal']);
}

if($action=="imprimir_ticket2"){
    require("../modelo/clsImpresora.php");
    require("../modelo/clsMovimiento.php");    
    $objImpresora = new clsImpresora(68,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objMovimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $rs2=$objMovimiento->consultarNumeroComanda($_SESSION['R_IdSucursal']);
    if($rs2->rowCount()>0){
        $data2=$rs2->fetchObject();
        $numero=$data2->numerocomanda;   
    }else{
        $numero=1;
    }    
    
	$rs = $objMovimiento->buscarDetalleProducto($_POST['txtId']);	
    while($reg=$rs->fetchObject()){	
		$idproducto=$reg->idproducto;
		$idsucursalproducto=$reg->idsucursal;
		$producto=$reg->producto;
		$codigo=$reg->codigo;
		$idunidad=$reg->idunidad;
		$unidad=$reg->unidad;
		$cantidad=$reg->cantidad;
		$precioventa=$reg->precioventa;
		$moneda=$reg->moneda;
		$preciocompra=$reg->preciocompra;
		
		$carroPedido[($idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$codigo,'producto'=>$producto,
        'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 
        'preciocompra'=>$preciocompra,'moneda'=>$moneda,'abreviatura'=>$reg->abreviatura,"idimpresora"=>$reg->idimpresora);
	}
    
    $rs=$objImpresora->consultarImpresora(100000,1,'idimpresora','1',0,$_SESSION["R_IdSucursal"],'','');
    while($dato=$rs->fetchObject()){
        foreach($carroPedido as $k => $v){
            if($v["idimpresora"]==$dato->idimpresora){
                //$handle = printer_open(trim($dato->nombre));
                $handle = printer_open("CUENTA2");
                printer_start_doc($handle, "Mi Documento");
                printer_start_page($handle);
                $font = printer_create_font("Sans Serif",35,15,800,true,false, false,0);
                printer_select_font($handle, $font);
                printer_draw_text($handle,"        COMANDA:".$numero,4,0);
                printer_draw_text($handle,substr($_SESSION['R_NombresPersona'],0,15)."    MESA:".$_POST["mesa"],4,30);
                //printer_draw_text($handle," ---LISTA DE PLATOS---",4,25);
                $font = printer_create_font("Sans Serif",35,15,800,true,false, false,0);
                printer_select_font($handle, $font);
                printer_draw_text($handle,"FECHA: ".date("d-m-Y"),4,60);
                //printer_draw_text($handle,"MESERO: ".substr($_SESSION["R_ApellidosPersona"],0,1).". ".SUBSTR($_SESSION['R_NombresPersona'],0,15),4,75);
                //printer_draw_text($handle,"  CANT.    DESCRIP.      P.VENT.",4,100);            
                printer_draw_text($handle,"   ".number_format($v["cantidad"],0,'.',' ')."   ".str_pad(utf8_decode(substr($v["abreviatura"],0,20)),16," "),4,90);
                printer_draw_text($handle," ",4,180);    
                printer_delete_font($font);
                printer_end_page($handle);
                printer_end_doc($handle);
                printer_close($handle);
                $handle=null;
                $numero=$numero+1;
            }        
        }
    }
    $band=$objMovimiento->actualizarNumeroComanda($numero,$_SESSION['R_IdSucursal']);
}

if($action=="imprimir_venta"){
    require("../modelo/clsMovimiento.php");
	$objMovimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $idventa=$_POST["idventa"];
    
    $rst=$objMovimiento->consultarMovimientoComprobante(1,1,'2',1, $idventa,0, '');
    $detalle = $rst->fetchObject();

   // $connector = new WindowsPrintConnector("smb://DESKTOP1VC83HD/GenericTextOnly");
    $connector = new WindowsPrintConnector("smb://DESKTOP1VC83HD/Star SP700R");
    $printer = new Printer($connector);
    //$printer -> setJustification(Printer::JUSTIFY_CENTER);
    //$printer -> bitImage($tux,Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
    /*$printer -> text("    INVERSIONES TURISTICAS TRES AMIGOS");
    $printer -> feed();
    $printer -> text("         SOCIEDAD ANONIMA CERRADA");
    $printer -> feed();
    $printer -> text("AV. LA MARINA NRO.823 URB.SANTA VICTORIA");
    $printer -> feed();
    $printer -> text("       LAMBAYEQUE-CHICLAYO-CHICLAYO");
    $printer -> feed();
    $printer -> text("             RUC:20600652100");
    $printer -> feed();
    $printer -> text("           NRO.SERIE:291170800358");
    $printer -> feed();
    $printer -> text("        NRO.AUTORIZ.:0073845119972");*/
    $printer -> feed();
    IF($detalle->idtipodocumento=="4"){
        $printer -> text("Boleta Nro: ".substr($detalle->numero,0,10));
        $printer -> feed();
        $detalle->subtotal = number_format($detalle->total/1.18,2,'.','');
        $detalle->igv = number_format($detalle->total - $detalle->subtotal,2,'.','');
    }else{
        $printer -> text("Factura Nro: ".substr($detalle->numero,0,10));
        $detalle->subtotal = number_format($detalle->total/1.18,2,'.','');
        $detalle->igv = number_format($detalle->total - $detalle->subtotal,2,'.','');
        $printer -> feed();
    }
    $printer -> text("Fecha: ".substr($detalle->fecha,0,10));
    $printer -> feed();
    $printer -> text("Cond.Pago:CONTADO");
    $printer -> feed();
    $rs=$objMovimiento->obtenerDataSQL("select pm.apellidos,pm.nombres,pm.nrodoc,p.direccion from personamaestro pm inner join persona p on p.idpersonamaestro=pm.idpersonamaestro where p.idpersona=".$detalle->idpersona);
    $dato=$rs->fetchObject();
    if($dato->nombres!="VARIOS"){
        $printer -> text("Cliente: ".$dato->apellidos." ".$dato->nombres);
        $printer -> feed();
        $printer -> text("Dir.: ".$dato->direccion);
        $printer -> feed();
        $printer -> text("RUC/DNI: ".$dato->nrodoc);
        $printer -> feed();
    }else{
        $printer -> text("Cliente: ");
        $printer -> feed();
        $printer -> text("Dir.: SIN DOMICILIO");
        $printer -> feed();
        $printer -> text("RUC/DNI: 0");
        $printer -> feed();
    }
	$printer -> text("Cant.  Producto                 Importe");
    $printer -> feed();
    $printer -> text("---------------------------------------"."\n");
	
	$c = 160;
    $rs1=$objMovimiento->buscarDetalleProducto($idventa);
    $carroPedido=array();
    while($dat=$rs1->fetchObject()){
        $carroPedido[]=array("cantidad"=>$dat->cantidad,"precioventa"=>$dat->precioventa,"abreviatura"=>trim($dat->comentario==""?$dat->abreviatura:$dat->comentario));
    }
    if($_POST["consumo"]=="S"){
        $printer -> text(str_pad("CONSUMO",30," ")."  ".number_format($detalle->total,2,'.',' ')."\n");
    }elseif(trim($_POST["glosa"])!=""){
        $printer -> text(str_pad($_POST["glosa"],30," ")."  ".number_format($detalle->total,2,'.',' ')."\n");
    }else{
        foreach($carroPedido as $k => $v){
            if($v['precioventa']>0){
                $subto=$v['cantidad']*$v['precioventa'];
                $suma=$suma+$subto;
                $printer -> text(number_format($v["cantidad"],0,'.','')."  ".str_pad((substr($v["abreviatura"],0,25)),30," ")." ".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."\n");
            }    	                
    	}
    }
    $printer -> text("---------------------------------------"."\n");
    //$printer -> setTextSize(2 , 1,7);
    $printer -> text(str_pad("SUBTOTAL",32," "));
    $printer -> text(number_format($detalle->subtotal,2,'.',' ')."\n");
    $printer -> text(str_pad("IGV (18%)",32," "));
    $printer -> text(number_format($detalle->igv,2,'.',' ')."\n");
    $printer -> text(str_pad("TOTAL S/. ",32," "));
    $printer -> text(number_format($detalle->total,2,'.',' ')."\n");
    $printer -> text("---------------------------------------"."\n");
    $printer -> text("Hora: ".date("H:i:s")."\n");
    $printer -> text("      GRACIAS POR SU PREFERENCIA"."\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> feed();
    $printer -> feed();
    $printer -> cut();
    
    /* Close printer */
    $printer -> close();       
}

if($action=="imprimir_egreso"){
	require("../modelo/clsMovCaja.php");
	$objMovimiento = new clsMovCaja($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

    /* Most printers are open on port 9100, so you just need to know the IP 
     * address of your receipt printer, and then fsockopen() it on that port.
     */
    try {
        $rs=$objMovimiento->consultarMovCaja(1,1,1,1,$_POST["id"],4,"","");
        $dat=$rs->fetchObject();
        if($dat->conceptopago=="PAGO ADELANTADO"){
            $dat->conceptopago="PAGO DE NOTA DE PEDIDO";
        }
        /*if($dat->idtipodocumento!=10){
            echo "vmsg='No se puede imprimir un ingreso';";
            exit();
        }*/
        $connector = new WindowsPrintConnector("CAJA");
        $printer = new Printer($connector);
        $printer -> setTextSize(1 , 1);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("CHANIS");
        $printer -> feed();
        $printer -> feed();
        if($dat->idtipodocumento==10){
            $printer -> text("RECIBO DE EGRESO NRO: ".$dat->numero."\n");   
        }else{
            $printer -> text("RECIBO DE INGRESO NRO: ".$dat->numero."\n");
        }
        $printer -> feed();
        $printer -> feed();
        //$printer -> setTextSize(1,1 , 1,1);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("FECHA: ".$dat->fecha."\n");
        $printer -> feed();
        $printer -> text("PERSONA: ".$dat->persona."\n");
        $printer -> feed();
        $printer -> text("CONCEPTO: ".$dat->conceptopago." - ".$dat->comentario."\n");
        $printer -> feed();
        $printer -> text("TOTAL: ".number_format($dat->total,2,'.',' ')."\n");
        $printer -> feed();
        $printer -> text("UUSARIO: ".substr($_SESSION['R_NombresPersona'],0,15));
        $printer -> feed();
        /*$printer -> feed();
        $printer -> text("FIRMA: \n");
        $printer -> feed();*/
        $printer -> feed();
        $printer -> cut();
        echo "vmsg='Imprimiendo';";
        /* Close printer */
        $printer -> close();
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }   
}

if($action=="imprimir_cierre"){
	require("../modelo/clsMovCaja.php");
	$objMovimiento = new clsMovCaja($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

    /* Most printers are open on port 9100, so you just need to know the IP 
     * address of your receipt printer, and then fsockopen() it on that port.
     */
    try {
        $_POST["efectivo"]=str_replace(",","",$_POST["efectivo"]);
        $_POST["visa"]=str_replace(",","",$_POST["visa"]);
        $_POST["master"]=str_replace(",","",$_POST["master"]);
        $_POST["gastos"]=str_replace(",","",$_POST["gastos"]);
        $_POST["ingresos"]=str_replace(",","",$_POST["ingresos"]);
        if($_SERVER['REMOTE_ADDR']=="192.168.1.51"){//caja1
        	$idcaja2=1;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.52"){//caja2
	        $idcaja2=2;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.53"){//caja3
	        $idcaja2=3;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.54"){//caja4
	        $idcaja2=4;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.58"){//caja5
	        $idcaja2=5;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.56"){//caja6
	        $idcaja2=6;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.57"){//boleteria
	        $idcaja2=7;
	    }else{
	        $idcaja2=1;
	    }
        /*if($idcaja2==2){
    		$connector = new NetworkPrintConnector("192.168.1.102", 9100);
    	}elseif($idcaja2==1){
    		$connector = new NetworkPrintConnector("192.168.1.101", 9100);
    	}elseif($idcaja2==3){
    		$connector = new NetworkPrintConnector("192.168.1.103", 9100);
    	}elseif($idcaja2==4){
    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
    	}elseif($idcaja2==5){
    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
    	}else{*/
    		$connector = new WindowsPrintConnector("CAJA");
    	//}
    	$idapertura = $objMovimiento->obtenerDataSQL("select * from movimientohoy where idconceptopago=1 and idcaja=".$_SESSION["R_IdCaja"]." order by idmovimiento desc limit 1")->fetchObject()->idmovimiento;
        
        $printer = new Printer($connector);
        $printer -> setTextSize(1 , 1);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("EL CLUB");
        $printer -> feed();
	    $printer -> text("REPORTE DE CAJA"."\n");
        $printer -> feed();
        //$printer -> setTextSize(1,1 , 1,1);
        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
        $printer -> text("USARIO: ".substr($_SESSION['R_NombresPersona'],0,15)."  FECHA: ".date("d/m/Y H:i:s")."\n");
       	$printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("=========================================\n");
        $printer -> text("* DETALLE VENTA: \n");
        $printer -> text("-----------------------------------------\n");
        $printer -> text(" CANT.     DESCRIPCION \n");
        $printer -> text("-----------------------------------------\n");
        $rs=$objMovimiento->obtenerDataSQL("select sum(dma.cantidad) as cantidad,p.idproducto,p.descripcion as producto from detallemovalmacen dma inner join producto p on p.idproducto=dma.idproducto inner join movimientohoy m on m.idmovimiento=dma.idmovimiento where m.idcaja=".$_SESSION["R_IdCaja"]." and m.idtipomovimiento=2 and m.estado='N' and m.idmovimiento>$idapertura group by p.idcategoria,p.idproducto,p.descripcion order by p.idcategoria,p.descripcion");
        while($dat=$rs->fetchObject()){
            $printer -> text("- ".number_format($dat->cantidad,2,'.','').($dat->cantidad<10?' ':'')."   ".$dat->producto."\n");
        }
        $printer -> text("=========================================\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("* VENTA TOTAL: ".number_format($_POST["efectivo"] + $_POST["visa"] + $_POST["master"],2,'.','')."\n");
        $printer -> text("TARJETA(T): ".number_format($_POST["visa"] + $_POST["master"],2,'.','')."  V: ".number_format($_POST["visa"],2,'.','')."  M: ".number_format($_POST["master"],2,'.','')."\n");
        $printer -> text("\n");
        $printer -> text("EFECTIVO(E): ".number_format($_POST["efectivo"],2,'.','')."    CAJA INICIO:".number_format($_POST["apertura"],2,'.','')."\n");
        $printer -> text("=========================================\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("* INGRESOS: "."\n");
        $rs=$objMovimiento->obtenerDataSQL("SELECT mh.*,c.descripcion as concepto FROM movimientohoy mh inner join conceptopago c on c.idconceptopago=mh.idconceptopago WHERE mh.idtipodocumento = 9 and mh.idconceptopago<>3 AND mh.estado = 'N' and idmovimiento>$idapertura");
        while($dat=$rs->fetchObject()){
            $printer -> text("  - $dat->concepto($dat->comentario): ".number_format($dat->total,2,'.','')."\n");
        }
        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
        $printer -> text("TOTAL INGRESOS: ".number_format($_POST["ingresos"],2,'.','')."\n");
        $printer -> text("=========================================\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("* GASTO: "."\n");
        $rs=$objMovimiento->obtenerDataSQL("SELECT mh.*,c.descripcion as concepto FROM movimientohoy mh inner join conceptopago c on c.idconceptopago=mh.idconceptopago WHERE mh.idtipodocumento = 10 AND mh.estado = 'N' and idmovimiento>$idapertura");
        while($dat=$rs->fetchObject()){
            $printer -> text("  - $dat->concepto($dat->comentario): ".number_format($dat->total,2,'.','')."\n");
        }
        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
        $printer -> text("TOTAL GASTO: ".number_format($_POST["gastos"],2,'.','')."\n");
        $printer -> text("=========================================\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("* CIERRE CAJA: "."\n");
        $printer -> text("E + I - G: ".number_format($_POST["efectivo"] + $_POST["ingresos"] - $_POST["gastos"],2,'.','')."         REAL: ".number_format($_POST["real"],2,'.','')."\n");
        $printer -> text("CAJA CHICA FINAL: ".number_format($_POST["final"],2,'.','')."\n");
        $printer -> text("=========================================\n");
        $printer -> text("* ANULADOS: \n");
        $printer -> text("-----------------------------------------\n");
        $printer -> text(" CANT.     DESCRIPCION \n");
        $printer -> text("-----------------------------------------\n");
        $rs=$objMovimiento->obtenerDataSQL("select sum(dma.cantidad) as cantidad,p.idproducto,p.descripcion as producto from detallemovalmacen dma inner join producto p on p.idproducto=dma.idproducto inner join movimientohoy m on m.idmovimiento=dma.idmovimiento where m.idcaja=".$_SESSION["R_IdCaja"]." and m.idtipomovimiento=2 and m.estado='I' and m.idmovimiento>$idapertura group by p.idcategoria,p.idproducto,p.descripcion order by p.idcategoria,p.descripcion");
        while($dat=$rs->fetchObject()){
            $printer -> text("* ".number_format($dat->cantidad,2,'.','').($dat->cantidad<10?' ':'')."   ".$dat->producto."\n");
        }
        $printer -> text("=========================================\n");
        $printer -> text("OBSERVACIONES: \n");
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> text("FIRMA: \n");
        $printer -> feed();
        $printer -> feed();
        $printer -> cut();
        echo "vmsg='Imprimiendo';";
        /* Close printer */
        $printer -> close();
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }   
}

if($action=="reimprimir_cierre"){
	require("../modelo/clsMovCaja.php");
	$objMovimiento = new clsMovCaja($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

    /* Most printers are open on port 9100, so you just need to know the IP 
     * address of your receipt printer, and then fsockopen() it on that port.
     */
    try {
    	$idapertura=$_POST["idapertura"];
    	$idcierre=$_POST["idcierre"];
    	$apertura = $objMovimiento->obtenerDataSQL("SELECT sum(T.total) FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 1 AND T.estado='N' and T.idmovimiento=".$idapertura." and T.idmovimiento<".$idcierre)
                 ->fetchObject()->sum;
    	$efectivo = $objMovimiento->obtenerDataSQL("SELECT CASE WHEN sum(T.totalpagado) IS NULL THEN 0 ELSE sum(T.totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='E' OR T.modopago='A') and T.idmovimiento>".$idapertura." and T.idmovimiento<".$idcierre)
                 ->fetchObject()->sum;
	    $visa_modoT = $objMovimiento->obtenerDataSQL("SELECT CASE WHEN sum(T.total-T.totalpagado) IS NULL THEN 0 ELSE sum(T.total-T.totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='T') AND T.idtipotarjeta = 1 and T.idmovimiento>".$idapertura." and T.idmovimiento<".$idcierre)
	                      ->fetchObject()->sum;
	    $visa_modoA = $objMovimiento->obtenerDataSQL("SELECT CASE WHEN sum((substr(T.montotarjeta,position('1@' in T.montotarjeta)+2,position('|' in T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(T.montotarjeta,position('1@' in T.montotarjeta)+2,position('|' in T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='A') and T.idmovimiento>".$idapertura." and T.idmovimiento<".$idcierre)
	            		  ->fetchObject()->sum;
	    $visa = $visa_modoT + $visa_modoA;
	    $mastercard_modoT = $objMovimiento->obtenerDataSQL("SELECT CASE WHEN sum(T.total) IS NULL THEN 0 ELSE sum(T.total) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='T') AND T.idtipotarjeta = 2 and T.idmovimiento>".$idapertura." and T.idmovimiento<".$idcierre)
	            				->fetchObject()->sum;
	    $mastercard_modoA = $objMovimiento->obtenerDataSQL("SELECT CASE WHEN sum((substr(T.montotarjeta,position('2@' in T.montotarjeta)+2,length(T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(T.montotarjeta,position('2@' in T.montotarjeta)+2,length(T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='A') and T.idmovimiento>".$idapertura." and T.idmovimiento<".$idcierre)
	            				->fetchObject()->sum;
	    $master = $mastercard_modoT + $mastercard_modoA;
	    $ingresos = $objMovimiento->obtenerDataSQL("SELECT sum(T.total) FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idtipodocumento = 9 AND T.idconceptopago NOT IN (1,3) AND T.estado='N' and T.idmovimiento>".$idapertura." and T.idmovimiento<".$idcierre)
	                    ->fetchObject()->sum;
	    $egresos = $objMovimiento->obtenerDataSQL("SELECT sum(T.total) FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idtipodocumento = 10 and T.idconceptopago<>2 AND T.estado = 'N' and T.idmovimiento>".$idapertura." and T.idmovimiento<".$idcierre)
	                   ->fetchObject()->sum;
        if($_SERVER['REMOTE_ADDR']=="192.168.1.51"){//caja1
        	$idcaja2=1;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.52"){//caja2
	        $idcaja2=2;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.53"){//caja3
	        $idcaja2=3;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.54"){//caja4
	        $idcaja2=4;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.58"){//caja5
	        $idcaja2=5;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.56"){//caja6
	        $idcaja2=6;
	    }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.57"){//boleteria
	        $idcaja2=7;
	    }else{
	        $idcaja2=1;
	    }
        /*if($idcaja2==2){
    		$connector = new NetworkPrintConnector("192.168.1.102", 9100);
    	}elseif($idcaja2==1){
    		$connector = new NetworkPrintConnector("192.168.1.101", 9100);
    	}elseif($idcaja2==3){
    		$connector = new NetworkPrintConnector("192.168.1.103", 9100);
    	}elseif($idcaja2==4){
    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
    	}elseif($idcaja2==5){
    		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
    	}else{*/
    		$connector = new WindowsPrintConnector("CAJA");
    	//}
        $printer = new Printer($connector);
        $printer -> setTextSize(1 , 1);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("EL CLUB");
        $printer -> feed();
        $printer -> feed();
	    $printer -> text("REPORTE DE CAJA"."\n");
        $printer -> feed();
        //$printer -> setTextSize(1,1 , 1,1);
        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
        $printer -> text("FECHA: ".date("d/m/Y H:i:s")."\n");
        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("* EFECTIVO: ".number_format($efectivo,2,'.','')."\n");
        $printer -> feed();
        $printer -> text("* VISA: ".number_format($visa,2,'.','')."\n");
        $printer -> feed();
        $printer -> text("* MASTER: ".number_format($master,2,'.','')."\n");
        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
        $printer -> text("TOTAL INGRESO: ".number_format($efectivo + $visa + $master + $ingresos,2,'.','')."\n");
        $printer -> text("=========================================\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("* GASTO: "."\n");
        $rs=$objMovimiento->obtenerDataSQL("SELECT mh.*,c.descripcion as concepto FROM (select * from  movimientohoy union all select * from movimiento) mh inner join conceptopago c on c.idconceptopago=mh.idconceptopago WHERE mh.idtipodocumento = 10 AND mh.estado = 'N' and idmovimiento>$idapertura and idmovimiento<$idcierre");
        while($dat=$rs->fetchObject()){
            $printer -> text("  - $dat->concepto: ".number_format($dat->total,2,'.','')."\n");
        }
        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
        $printer -> text("TOTAL GASTO: ".number_format($egresos,2,'.','')."\n");
        $printer -> text("=========================================\n");
        $printer -> text("APERTURA: ".number_format($apertura,2,'.','')."\n");
        $printer -> text("SALDO: ".number_format($efectivo + $visa + $apertura + $master - $egresos + $ingresos,2,'.','')."\n");
        $printer -> text("=========================================\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("DETALLE VENTA: \n");
        $printer -> text("-----------------------------------------\n");
        $printer -> text(" CANT.     DESCRIPCION \n");
        $printer -> text("-----------------------------------------\n");
        $rs=$objMovimiento->obtenerDataSQL("select sum(dma.cantidad) as cantidad,p.idproducto,p.descripcion as producto from detallemovalmacen dma inner join producto p on p.idproducto=dma.idproducto inner join (select * from movimientohoy union all select * from movimiento) m on m.idmovimiento=dma.idmovimiento where m.idcaja=".$_SESSION["R_IdCaja"]." and m.idtipomovimiento=2 and m.estado='N' and m.idmovimiento>$idapertura and m.idmovimiento<$idcierre group by p.idcategoria,p.idproducto,p.descripcion order by p.idcategoria,p.descripcion");
        while($dat=$rs->fetchObject()){
            $printer -> text("* ".number_format($dat->cantidad,2,'.','').($dat->cantidad<10?' ':'')."   ".$dat->producto."\n");
        }
        $printer -> text("=========================================\n");
        $printer -> text("ANULADOS: \n");
        $printer -> text("-----------------------------------------\n");
        $printer -> text(" CANT.     DESCRIPCION \n");
        $printer -> text("-----------------------------------------\n");
        $rs=$objMovimiento->obtenerDataSQL("select sum(dma.cantidad) as cantidad,p.idproducto,p.descripcion as producto from detallemovalmacen dma inner join producto p on p.idproducto=dma.idproducto inner join (select * from movimientohoy union all select * from movimiento) m on m.idmovimiento=dma.idmovimiento where m.idcaja=".$_SESSION["R_IdCaja"]." and m.idtipomovimiento=2 and m.estado='I' and m.idmovimiento>$idapertura and m.idmovimiento<$idcierre group by p.idcategoria,p.idproducto,p.descripcion order by p.idcategoria,p.descripcion");
        while($dat=$rs->fetchObject()){
            $printer -> text("* ".number_format($dat->cantidad,2,'.','').($dat->cantidad<10?' ':'')."   ".$dat->producto."\n");
        }
        $printer -> text("=========================================\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("USARIO: ".substr($_SESSION['R_NombresPersona'],0,15));
        $printer -> feed();
        $printer -> feed();
        $printer -> text("OBSERVACIONES: \n");
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> feed();
        $printer -> text("FIRMA: \n");
        $printer -> feed();
        $printer -> feed();
        $printer -> cut();
        echo "vmsg='Imprimiendo';";
        /* Close printer */
        $printer -> close();
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }   
}


if($action=="detalleProducto"){
	require("../modelo/clsMovimiento.php");
	$objProducto = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$rst1=$objProducto->obtenerDataSQL("select * from detallecategoria where idcategoria=(select idcategoria from producto where idproducto=".$_POST["idproducto"]." and idsucursal=".$_SESSION["R_IdSucursal"].") and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]);
	$estructura = "";$comentario="";
	if(isset($_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]])){
		$arrayDetalles = $_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]]["carroDetalle"];
	}else{
		$arrayDetalles = array();
	}
	$lista = array();
	while($dato1=$rst1->fetchObject()){
		$checked = "";
		if(isset($arrayDetalles[$dato1->iddetallecategoria])){
			$checked = "checked";
			$lista[] = $dato1->iddetallecategoria;
		}
		$estructura.= '<div class="col s6 m3 l2"><p class="white-text">
                              <input class="filled-in" id="propiedad-'.$dato1->iddetallecategoria.'" type="checkbox" onclick="detalleCategoria(this.checked,'.$dato1->iddetallecategoria.')" '.$checked.'/>
                              <label class="white-text" for="propiedad-'.$dato1->iddetallecategoria.'">'.$dato1->descripcion.'</label>
                          </p></div>';
	}
	if(isset($_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]])){
		$comentario=$_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]]["comentario"];
	}
	echo json_encode(array("estructura"=>$estructura,"comentario"=>$comentario,"lista"=>$lista));
}

if($action=="detalleProductoActual"){
	require("../modelo/clsMovimiento.php");
	$objProducto = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$rst1=$objProducto->obtenerDataSQL("select * from detallecategoria where idcategoria=(select idcategoria from producto where idproducto=".$_POST["idproducto"]." and idsucursal=".$_SESSION["R_IdSucursal"].") and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]);
	$estructura = "";$comentario="";
	if(isset($_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]."-actual"])){
		$arrayDetalles = $_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]."-actual"]["carroDetalle"];
	}else{
		$arrayDetalles = array();
	}
	$lista = array();
	while($dato1=$rst1->fetchObject()){
		$checked = "";
		if(isset($arrayDetalles[$dato1->iddetallecategoria])){
			$checked = "checked";
			$lista[] = $dato1->iddetallecategoria;
		}
		$estructura.= '<div class="col s6 m3 l2"><p class="white-text">
                              <input class="filled-in" id="propiedad-'.$dato1->iddetallecategoria.'" type="checkbox" onclick="detalleCategoria(this.checked,'.$dato1->iddetallecategoria.')" '.$checked.'/>
                              <label class="white-text" for="propiedad-'.$dato1->iddetallecategoria.'">'.$dato1->descripcion.'</label>
                          </p></div>';
	}
	if(isset($_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]])){
		$comentario=$_SESSION["R_carroPedidoMozo"][$_POST["idproducto"]."-".$_SESSION["R_IdSucursal"]]["comentario"];
	}
	echo json_encode(array("estructura"=>$estructura,"comentario"=>$comentario,"lista"=>$lista));
}

if($action=="imprimir_ventaelectronica"){
    require("../modelo/clsMovimiento.php");
	$objMovimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $idventa=$_POST["idventa"];
    
    $rst=$objMovimiento->consultarMovimientoComprobante(1,1,'2',1, $idventa,0, '');
    $detalle = $rst->fetchObject();

    for($z=0;$z<1;$z++){
	   // $connector = new WindowsPrintConnector("smb://DESKTOP1VC83HD/GenericTextOnly");
	    /*if($_SESSION["R_IdCaja"]==2){
			$connector = new NetworkPrintConnector("192.168.1.102", 9100);
		}elseif($_SESSION["R_IdCaja"]==1){
			$connector = new NetworkPrintConnector("192.168.1.101", 9100);
		}elseif($_SESSION["R_IdCaja"]==3){
			$connector = new NetworkPrintConnector("192.168.1.103", 9100);
		}elseif($_SESSION["R_IdCaja"]==4){
			$connector = new NetworkPrintConnector("192.168.1.104", 9100);
		}elseif($_SESSION["R_IdCaja"]==5){
			$connector = new NetworkPrintConnector("192.168.1.104", 9100);
		}else{*/
			$connector = new WindowsPrintConnector("CAJA");
		//}
	    $printer = new Printer($connector);
	    $printer -> setJustification(Printer::JUSTIFY_CENTER);
	    //$printer -> bitImage($tux,Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
	    $printer -> text("LA DOCE BAR & RESTAURANT SRL");
	    $printer -> feed();
	    $printer -> text("AV. LOS INCAS NRO 192");
	    $printer -> feed();
	    $printer -> text("CHICLAYO - CHICLAYO - LAMBAYEQUE");
	    $printer -> feed();
	    $printer -> text("RUC:20603074751");
	    $printer -> feed();
	    $printer -> setJustification(Printer::JUSTIFY_LEFT);
	    if($detalle->idtipodocumento=="4"){
	        $printer -> text("Boleta Electronica: ".substr($detalle->numero,0,13));
	        $printer -> feed();
	        $detalle->subtotal = number_format($detalle->total/1.18,2,'.','');
	        $detalle->igv = number_format($detalle->total - $detalle->subtotal,2,'.','');
	    }elseif($detalle->idtipodocumento=="5"){
	        $printer -> text("Factura Electronica: ".substr($detalle->numero,0,13));
	        $detalle->subtotal = number_format($detalle->total/1.18,2,'.','');
	        $detalle->igv = number_format($detalle->total - $detalle->subtotal,2,'.','');
	        $printer -> feed();
	    }else{
	    	$printer -> text("Ticket: ".substr($detalle->numero,0,13));
	        $detalle->subtotal = number_format($detalle->total/1.18,2,'.','');
	        $detalle->igv = number_format($detalle->total - $detalle->subtotal,2,'.','');
	        $printer -> feed();
	    }
	    $printer -> text("Fecha: ".substr($detalle->fecha,0,10));
	    $printer -> feed();
	    $rs=$objMovimiento->obtenerDataSQL("select pm.apellidos,pm.nombres,pm.nrodoc,p.direccion,pm.tipopersona from personamaestro pm inner join persona p on p.idpersonamaestro=pm.idpersonamaestro where p.idpersona=".$detalle->idpersona);
	    $dato=$rs->fetchObject();
	    if($dato->nombres!="VARIOS"){
	        $printer -> text("Cliente: ".$dato->apellidos." ".$dato->nombres);
	        $printer -> feed();
	        $printer -> text("Dir.: ".$dato->direccion);
	        $printer -> feed();
	        if($dato->tipopersona=="JURIDICA" && $detalle->idtipodocumento=="4"){
	            $printer -> text("RUC/DNI: 0");
	        }else{
	            $printer -> text("RUC/DNI: ".$dato->nrodoc);
	        }
	        $printer -> feed();
	    }else{
	        $printer -> text("Cliente: ");
	        $printer -> feed();
	        $printer -> text("Dir.: SIN DOMICILIO");
	        $printer -> feed();
	        $printer -> text("RUC/DNI: 0");
	        $printer -> feed();
	    }
	    $printer -> text("---------------------------------------"."\n");
		$printer -> text("Cant.  Producto                 Importe");
	    $printer -> feed();
	    $printer -> text("---------------------------------------"."\n");
		
		$c = 160;
	    $rs1=$objMovimiento->buscarDetalleProducto($idventa,"h");
	    $carroPedido=array();
	    while($dat=$rs1->fetchObject()){
	        $carroPedido[]=array("cantidad"=>$dat->cantidad,"precioventa"=>$dat->precioventa,"abreviatura"=>trim($dat->comentario==""?$dat->abreviatura:$dat->comentario));
	    }
	    if($_POST["consumo"]=="S"){
	        $printer -> text(str_pad("CONSUMO",30," ")."  ".number_format($detalle->total,2,'.',' ')."\n");
	    }elseif(trim($_POST["glosa"])!=""){
	        $printer -> text(str_pad($_POST["glosa"],30," ")."  ".number_format($detalle->total,2,'.',' ')."\n");
	    }else{
	        foreach($carroPedido as $k => $v){
	            $subto=$v['cantidad']*$v['precioventa'];
	            $suma=$suma+$subto;
	            $printer -> text(number_format($v["cantidad"],0,'.','')."  ".str_pad((substr($v["abreviatura"],0,25)),30," ")." ".number_format($v["cantidad"]*$v["precioventa"],2,'.',' ')."\n");
	    	}
	    }
	    $printer -> text("---------------------------------------"."\n");
	    $printer -> text(str_pad("Op. Gravada:",32," "));
	    $printer -> text(number_format($detalle->subtotal,2,'.',' ')."\n");
	    $printer -> text(str_pad("I.G.V. (18%)",32," "));
	    $printer -> text(number_format($detalle->igv,2,'.',' ')."\n");
	    $printer -> text(str_pad("Op. Inafecta:",32," "));
	    $printer -> text(number_format(0,2,'.',' ')."\n");
	    $printer -> text(str_pad("Op. Exonerada:",32," "));
	    $printer -> text(number_format(0,2,'.',' ')."\n");
	    $printer -> text(str_pad("TOTAL S/ ",32," "));
	    $printer -> text(number_format($detalle->total,2,'.',' ')."\n");
	    include_once '../modelo/NumeroTexto.php';
	    $importe_total_venta = number_format($detalle->total,2,'.','');
	    $numeroTexto = new NumeroTexto($importe_total_venta);
	    $decimales = intval(round($importe_total_venta,2)*100);
	    $decimales = $decimales - intval(round($importe_total_venta,2))*100;
	    $decimales = intval($decimales);
	    if($decimales==0){
	        $decimales = '00';
	    }else{
	        if($decimales<10){
	            $decimales = '0'.strval($decimales);
	        }else{
	            $decimales = strval($decimales);
	        }
	    }
	    $son = strtoupper("SON: ".$numeroTexto->convertirLetras($importe_total_venta)).' CON '.$decimales.'/100 SOLES';
	    $printer -> text("\n");
	    $printer -> text($son."\n");
	    $printer -> text("---------------------------------------"."\n");
	    if($detalle->idtipodocumento!="19"){
		    include_once '../modelo/mdlSolicitud.php';
			include_once '../controlador/Algoritmos.php';
			$mdlSolicitud = new mdlSolicitud();
			$comprobantes = $mdlSolicitud->listarSolicitudes2("","","",substr($detalle->numero,0,13),"","",1,10);
			if(file_exists("../ficheros/".$comprobantes[0][0]["nombre_solicitud"]."zip")){
		        Algoritmos::CrearQR($comprobantes[0][0]["nombre_solicitud"],$comprobantes["username_solicitud"]);
		    }
			if(file_exists("../ficheros/".$comprobantes[0][0]["nombre_solicitud"]."png")){
				$tux = EscposImage::load("../ficheros/".$comprobantes[0][0]["nombre_solicitud"]."png",true);
				$printer -> setJustification(Printer::JUSTIFY_CENTER);
			    $printer -> bitImage($tux,Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
			    $printer -> text("---------------------------------------"."\n");
			}
			//CODIGO QR
		}
		$printer -> setJustification(Printer::JUSTIFY_LEFT);
	    $printer -> text("Hora: ".date("H:i:s")."\n");

	    $mozo=$objMovimiento->obtenerDataSQL("select * from personamaestro where idpersonamaestro=(select idpersonamaestro from persona where idpersona=(select idresponsable from movimientohoy where idmovimiento=(select idmovimientoref from detallemovimientohoy where idmovimiento=$idventa limit 1) and idsucursal=".$_SESSION["R_IdSucursal"].") and idsucursal=".$_SESSION["R_IdSucursal"].")")->fetchObject();

	    $printer -> text("Mozo: ".$mozo->nombres."\n");
	    $printer -> text("\n");
	    if($detalle->idtipodocumento!="19"){
	    	$printer -> text(("Representación impresa del Comprobante Electrónico, consulte en https://facturae-garzasoft.com"));
	    	$printer -> text("\n");
	    }
	    $printer -> text("\n");

	    $printer -> text("           GRACIAS POR SU PREFERENCIA"."\n");
	    /*$printer -> text("\n");
	    $printer -> text("\n");
	    $printer -> text("\n");
	    $printer -> text("\n");*/
	    $printer -> text("\n");
	    $printer -> feed();
	    $printer -> feed();
	    $printer -> cut();
	    $printer -> pulse();
	    /* Close printer */
	    $printer -> close();       
	}
}

if($action=="imprimirStock"){
	if($_SESSION["R_IdCaja"]==2){
		$connector = new NetworkPrintConnector("192.168.1.102", 9100);
	}elseif($_SESSION["R_IdCaja"]==1){
		$connector = new NetworkPrintConnector("192.168.1.101", 9100);
	}elseif($_SESSION["R_IdCaja"]==3){
		$connector = new NetworkPrintConnector("192.168.1.103", 9100);
	}elseif($_SESSION["R_IdCaja"]==4){
		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
	}elseif($_SESSION["R_IdCaja"]==5){
		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
	}else{
		$connector = new WindowsPrintConnector("CAJA");
	}
    $printer = new Printer($connector);
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //$printer -> bitImage($tux,Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
    $printer -> text("EL CLUB S.R.L.");
    $printer -> feed();
    $printer -> text("STOCK DEL ".date("d/m/Y")." - ".$_POST["barra"]);
    $printer -> feed();
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    require("../modelo/clsProducto.php");
    $objProducto = new clsProducto(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $rs=$objProducto->consultarProductoReporteStock(100000, 1, 1, 1, 0, '', 0,0,'','','S',$_POST["idsucursal"]);
    $printer -> text("---------------------------------------"."\n");
	$printer -> text("Cant.  Producto            ");
    $printer -> feed();
	$printer -> text("---------------------------------------"."\n");
	while($dat=$rs->fetchObject()){
		$printer -> text(round($dat->stock,2).'  '.$dat->descripcion);
    	$printer -> feed();
	}	
	$printer -> feed();
    $printer -> feed();
    $printer -> cut();
    
    /* Close printer */
    $printer -> close();
    echo "ok";    
}

if($action=="imprimirAlmacen"){
	require("../modelo/clsProducto.php");
    $objProducto = new clsProducto(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $dato=$objProducto->obtenerDataSQL("select T.* from (select * from movimientohoy union all select * from movimiento) T where idmovimiento=".$_POST["idmovimiento"]." and idsucursal=".$_POST["idsucursal"])->fetchObject();
    if($dato->idsucursalref==1){
		$connector = new NetworkPrintConnector("192.168.1.101", 9100);
	}elseif($dato->idsucursalref==2){
		$connector = new NetworkPrintConnector("192.168.1.104", 9100);
	}elseif($dato->idsucursalref==3){
		$connector = new NetworkPrintConnector("192.168.1.103", 9100);
	}else{
		$connector = new WindowsPrintConnector("CAJA");
	}
    $printer = new Printer($connector);
    $printer -> text("EL CLUB S.R.L.");
    $printer -> feed();
    $printer -> text("DOC. ".($dato->idtipodocumento==7?"I":"S").$dato->numero);
    $printer -> feed();
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text("FECHA:".$dato->fecha);
    $printer -> feed();
    $printer -> text("COMENTARIO:".$dato->comentario);
    $printer -> feed();
    $printer -> text("---------------------------------------"."\n");
	$printer -> text("Cant.  Producto            ");
    $printer -> feed();
	$printer -> text("---------------------------------------"."\n");
	$rs=$objProducto->obtenerDataSQL("select dm.*,p.descripcion as producto from detallemovalmacen dm inner join producto p on p.idproducto=dm.idproducto where dm.idsucursal=".$_POST["idsucursal"]." and dm.idproducto=".$_POST["idproducto"]);
	while($dat=$rs->fetchObject()){
		$printer -> text(round($dat->cantidad,2).'  '.$dat->descripcion);
    	$printer -> feed();
	}	
	$printer -> feed();
    $printer -> feed();
    $printer -> cut();
    
    /* Close printer */
    $printer -> close();
    echo "ok";
}
if($action=="EnviarURL"){
	error_reporting(E_ERROR | E_PARSE);
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $_POST["url"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    print_r($info);
    echo $data;
    curl_close($ch);
}
?>