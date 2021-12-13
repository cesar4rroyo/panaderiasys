<?php
session_start();
require("../modelo/clsDetalleAlmacen.php");
require("../modelo/clsStockProducto.php");

$accion = $_POST["accion"];
switch($accion){
case "listaDetallePedido" :
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjDetalleAlmacen->consultarDetalleAlmacenconAjax($_POST["IdPedido"],0);

	$cadena="<table width='100%' border='1'><tr>
	<th>N&uacute;mero Pedido</th>	
    <th>C&oacute;digo</th>
    <th>Producto</th>
    <th>Unidad</th>
    <th>Cantidad</th>
	<th>Precio venta</th>
    <th>Subtotal</th>
	<th align='left'><a id='agregar' href='#' onclick='agregartodo(".$_POST["IdPedido"].")'>Agregar&nbsp;Todo</a></th>
	</tr>";
	while($registro=$consulta->fetchObject()){
		$cadena.="<tr>";
		$cadena.="<td>".$registro->numero."&nbsp;</td>
		<td>".$registro->codpro."&nbsp;</td>
		<td>".utf8_decode($registro->producto)."&nbsp;</td>
		<td>".$registro->unidad."&nbsp;</td>
		<td align='right'>".$registro->cantidad."&nbsp;</td>
		<td align='right'>".number_format($registro->precioventa,2)."&nbsp;</td>
		<td align='right'>".number_format($registro->subtotal,2)."</td>
		<td><a href='#' onclick='agregar(".$registro->iddetalle.")'>Agregar</a></td>";
		$cadena.="<tr>";
		$suma+=$registro->subtotal;
	}
	$cadena.="<tr><td colspan='5'></td><td align='right'>Total:</td><td align='right'>".number_format($suma,2)."</td><td></td></tr></table>";
	$cadena=utf8_encode($cadena);
	echo $cadena;
	break;
case "agregarDetalleVenta" :
	$iddetalle=$_POST["IdDetalle"];
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
	
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	
	if(isset($_SESSION['R_carroVenta']))
		$carroVenta=$_SESSION['R_carroVenta'];

	$rs = $ObjDetalleAlmacen->consultarDetalleAlmacenconAjax(0,$iddetalle);		
    $reg=$rs->fetchObject();	

	$carroVenta[($iddetalle)]=array('iddetalle'=>($iddetalle),'idpedido'=>$reg->idmovimiento,'nropedido'=>$reg->numero,'idproducto'=>$reg->idproducto,'idsucursalproducto'=>$reg->idsucursalproducto,'codigo'=>$reg->codpro,'producto'=>$reg->producto,'cantidad'=>$reg->cantidad,'idunidad'=>$reg->idunidad,'unidad'=>$reg->unidad, 'precioventa'=>$reg->precioventa,'precioventaoriginal'=>$reg->precioventa,'preciocompra'=>$reg->preciocompra,'kardex'=>$reg->kardex,'compuesto'=>$reg->compuesto,'moneda'=>$moneda);

	$_SESSION['R_carroVenta']=$carroVenta;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>N&uacute;mero Pedido</th>	
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cantidad</th>
	<th>Precio Venta</th>	
	<th>SubTotal</th>
	";
	foreach($carroVenta as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$registros.="<tr><td>".$v["nropedido"]."</td>";
		$registros.="<td>".$v["codigo"]."</td>";
		$registros.="<td>".utf8_decode($v["producto"])."</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],0)."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2)."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["iddetalle"].");'>Quitar</a></td></tr>";
	}
	$registros.="</table>";
	
	$igv=($_SESSION["R_IGV"]/100)*$suma;
	$total=number_format($suma,2)+number_format($igv,2);
		
	$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	$igv2=number_format($suma,2)-number_format($sub2,2);
	
	
	if($documento!='5'){	$type='hidden';	$t1='';$t2='';}else{$type ='text';$t1='Igv: ';$t2='Subtotal: ';}
	
	$registros.="
	<div><center><b>$t2 ";
	
	if($impuesto=='N'){
	$registros.="<input type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2,'.','')."' readonly=''>";}
	else{
	$registros.="<input type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2,'.','')."' readonly=''>";}
	
	$registros.="
	<input type='hidden' name='rSub' id='rSub' value='".number_format($suma,2,'.','')."'>
	<input type='hidden' name='rSub2' id='rSub2' value='".number_format($sub2,2,'.','')."'>
	$t1";
	
	if($impuesto=='N'){
	$registros.="<input type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv,2,'.','')."' readonly='' >";}
	else{
	$registros.="<input type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv2,2,'.','')."' readonly='' >";}
	
	$registros.="
	<input type='hidden' name='rIgv' id='rIgv' value='".number_format($igv,2,'.','')."'>
	<input type='hidden' name='rIgv2' id='rIgv2' value='".number_format($igv2,2,'.','')."'>
	Total :"; 
	
	if($impuesto=='N'){
	$registros.="<input type='text' name='txtTotal' id='txtTotal' value='".number_format($total,2,'.','')."' readonly=''>";}
	else{
	$registros.="<input type='text' name='txtTotal' id='txtTotal' value='".number_format($suma,2,'.','')."' readonly=''>";}

	$registros.="
	<input type='hidden' name='rTotal' id='rTotal' value='".number_format($total,2,'.','')."'>
	<input type='hidden' name='rTotal2' id='rTotal2' value='".number_format($suma,2,'.','')."'>
	</center>
	</div>";

	$registros=utf8_encode($registros);
	echo $registros;
	break;
case "agregarTodoDetalleVenta" :
	$idpedido=$_POST["IdPedido"];
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
        $tipoVenta = $_POST["tipoVenta"];
        if($tipoVenta=="V"){
            $descuento = $_POST["modalidadCampo2"];
        }elseif($tipoVenta=="A"){
            $pago = $_POST["modalidadCampo2"];
        }elseif($tipoVenta=="T"){
            $idscortesia = $_POST["modalidadCampo4"];
            $idscortesia = explode(",",$idscortesia);
        }
        
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	
	if(isset($_SESSION['R_carroVenta']))
		$carroVenta=$_SESSION['R_carroVenta'];

	$rs = $ObjDetalleAlmacen->consultarDetalleAlmacenconAjax($idpedido, 0);		
	while($reg=$rs->fetchObject())	{
		$carroVenta[($reg->iddetalle)]=array('iddetalle'=>$reg->iddetalle,'idpedido'=>$reg->idmovimiento,'nropedido'=>$reg->numero,'idproducto'=>$reg->idproducto,
                                            'idsucursalproducto'=>$reg->idsucursalproducto,'codigo'=>$reg->codpro,'producto'=>$reg->producto,'cantidad'=>$reg->cantidad,
                                            'idunidad'=>$reg->idunidad,'unidad'=>$reg->unidad, 'precioventa'=>$reg->precioventa,'precioventaoriginal'=>$reg->precioventa,
                                            'preciocompra'=>$reg->preciocompra,'kardex'=>$reg->kardex,'compuesto'=>$reg->compuesto,'moneda'=>$moneda,'bar'=>$reg->bar,
                                            'comida'=>$reg->comida);
	}
	
	$_SESSION['R_carroVenta']=$carroVenta;
	
	$contador=0;
	$suma=0;
        $registros.='
            <table class="striped bordered highlight">
                <thead>
                    <tr>
                        <th class="center">NUMERO</th>
                        <th class="center">CODIGO</th>
                        <th class="center">PRODUCTO</th>
                        <th class="center">UNIDAD</th>
                        <th class="center">CANTIDAD</th>
                        <th class="center">PRECIO VENTA</th>
                        <th class="center">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody id="tBodyDetalleVenta">';
    $registros2='
        <table class="striped bordered highlight">
            <thead>
                <tr>
                    <th class="center">CANTIDAD</th>
                    <th class="center">PRODUCTO</th>
                    <th class="center">PRECIO VENTA</th>
                    <th class="center">CANTIDAD A PAGAR</th>
                    <th class="center">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>';
    foreach($carroVenta as $k => $v){
        if($tipoVenta=="D" && $v['bar']=="N"){
            $v['precioventa'] = round($v['precioventa']/2,2);
        }elseif($tipoVenta=="T" && in_array($v["idproducto"], $idscortesia)){
            $v['precioventa'] = 0;
        }
        $subto=$v['cantidad']*$v['precioventa'];
        $suma=$suma+$subto;
        $registros.='
                    <tr class="hoverable" id="trTblDetalle_'.$v["idproducto"].'">
                        <td class="center">'.$v["nropedido"].'</td>
                        <td class="center">'.$v["codigo"].'</td>
                        <td class="center"><textarea id="txtProducto'.$v["idproducto"].'" name="txtProducto'.$v["idproducto"].'">'.utf8_decode($v["producto"]).'</textarea></td>
                        <td class="center">'.$v["unidad"].'</td>
                        <td class="center" id="tdCant_'.$v["idproducto"].'">'.number_format($v["cantidad"],0).'</td>
                        <td class="center" id="tdPrecio_'.$v["idproducto"].'">'.number_format($v["precioventa"],2).'</td>
                        <td class="center" id="tdSubtotal_'.$v["idproducto"].'">'.number_format($v["cantidad"]*$v["precioventa"],2).'</td>
                    </tr>';
        $registros2.='
            <tr class="hoverable">
                <td class="center">'.number_format($v["cantidad"],2).'</td>
                <td class="center">'.utf8_decode($v["producto"]).'</td>
                <td class="center">'.number_format($v["precioventa"],2).'</td>
                <td style="padding-top: 5px;padding-bottom: 5px;" class="valign-wrapper">
                  <div class="col s4 center">
                  <button type="button" onclick="if(parseFloat($(\'#txt'.$v["idproducto"].'\').val())<parseFloat('.$v["cantidad"].')){$(\'#txt'.$v["idproducto"].'\').val(parseFloat($(\'#txt'.$v["idproducto"].'\').val())+1);$(\'#txt'.$v["idproducto"].'\').trigger(\'keyup\');}" class="btn-floating" style="margin-left: 10px;"><i class="material-icons">add</i></button>
                  <button type="button" onclick="if(parseFloat($(\'#txt'.$v["idproducto"].'\').val())>0){$(\'#txt'.$v["idproducto"].'\').val(parseFloat($(\'#txt'.$v["idproducto"].'\').val())-1);$(\'#txt'.$v["idproducto"].'\').trigger(\'keyup\');}" class="btn-floating"><i class="material-icons">remove</i></button>
                  </div>
                  <div class="col s8 center"><div class="input-field inline" style="margin-top: 0px;">
                      <input id="txt'.$v["idproducto"].'" iddetalle="'.$v["iddetalle"].'" style="margin-bottom: 0px;" class="inptCantDetalleDividir" '
                . 'type="text" value="0.00" '
                . 'onKeyPress="return validarsolonumerosdecimales(event,this.value);" '
                . 'onkeyup="$(\'#txtSubtotal'.$v["idproducto"].'\').html(parseFloat(this.value)*parseFloat('.number_format($v["precioventa"],2).'));"  '
                . 'onblur="javascript:if(parseFloat(this.value)>parseFloat('.number_format($v["cantidad"],2).')){alert(\'Debe ingresar una cantidad correcta\');this.value=\'0.00\';/*$(#\'txtSubtotal'.$v["idproducto"].'\').html(\'0.00\');*/$(this).trigger(\'keyup\');} subcuenta();">
                  </div></div>
                </td>
                <td class="center" id="txtSubtotal'.$v["idproducto"].'">0.00</td>
            </tr>';
            $datosIdProductos.=$v["idproducto"]."/";
	}
        if($tipoVenta=="V"){
            $registros.='<tr class="hoverable" id="">'
                    . '<td class="center">-</td>'
                    . '<td class="center">-</td>'
                    . '<td class="center">DESCUENTO POR VALE</td>'
                    . '<td class="center">UNIDAD</td>'
                    . '<td class="center">1</td>'
                    . '<td class="center">-'.  number_format($descuento,2,'.','').'</td><td class="center">-'.  number_format($descuento,2,'.','').'</td>'
                    . '</tr></tbody><tfoot>';
            $suma = $suma - $descuento;
        }elseif($tipoVenta=="A"){
            $registros.='<tr class="hoverable" id="">'
                    . '<td class="center">-</td>'
                    . '<td class="center">-</td>'
                    . '<td class="center">MONTO POR PAGO ANTICIPADO</td>'
                    . '<td class="center">UNIDAD</td>'
                    . '<td class="center">1</td>'
                    . '<td class="center">-'.  number_format($pago,2,'.','').'</td><td class="center">-'.  number_format($pago,2,'.','').'</td>'
                    . '</tr></tbody><tfoot>';
            $suma = $suma - $pago;
        }else{
            $registros.="</tbody><tfoot>";
        }
        if($suma<0){
            $suma = 0;
        }
	$registros2.='</tbody>
                      <tfoot>
                          <tr class="blue lighten-4 blue-text text-darken-4">
                              <th colspan="3">&nbsp;</th>
                              <th class="right">TOTAL</th>
                              <th class="center" id="txtTotalSubcuenta">0.00</th>
                          </tr>
                      </tfoot>
                    </table>';
        $datosIdProductos= "<input type='hidden' id='txtDatosProductos' value='".substr($datosIdProductos,0,strlen($datosIdProductos)-1)."' />" ;
	$igv=($_SESSION["R_IGV"]/100)*$suma;
	$total=number_format($suma,2)+number_format($igv,2);
		
	$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	$igv2=number_format($suma,2)-number_format($sub2,2);
	
	if($impuesto=='N'){
            $registros.="<input type='hidden' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2,'.','')."' readonly=''>";
            $registros.='
            <tr class="blue lighten-4 blue-text text-darken-4">
                <th colspan="5">&nbsp;</th>
                <th class="right">SUBTOTAL</th>
                <th class="center" id="thSubtotalGeneral">'.number_format($suma,2,'.','').'</th>
            </tr>';
        }else{
            $registros.="<input type='hidden' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2,'.','')."' readonly=''>";
            $registros.='
            <tr class="blue lighten-4 blue-text text-darken-4" hidden>
                <th colspan="5">&nbsp;</th>
                <th class="right">SUBTOTAL</th>
                <th class="center" id="thSubtotalGeneral">'.number_format($sub2,2,'.','').'</th>
            </tr>';
        }
	
	$registros.="
	<input type='hidden' name='rSub' id='rSub' value='".number_format($suma,2,'.','')."'>
	<input type='hidden' name='rSub2' id='rSub2' value='".number_format($sub2,2,'.','')."'>";
	
	if($impuesto=='N'){
            $registros.="<input type='hidden' name='txtIgv' id='txtIgv' value='".number_format($igv,2,'.','')."' readonly='' >";
            $registros.='
                <tr class="blue lighten-5 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">I.G.V.</th>
                    <th class="center" id="thIgvGeneral">'.number_format($igv,2,'.','').'</th>
                </tr>';
        }else{
            $registros.="<input type='hidden' name='txtIgv' id='txtIgv' value='".number_format($igv2,2,'.','')."' readonly='' >";
            $registros.='
                <tr class="blue lighten-5 blue-text text-darken-4" hidden>
                    <th colspan="5">&nbsp;</th>
                    <th class="right">I.G.V.</th>
                    <th class="center" id="thIgvGeneral">'.number_format($igv2,2,'.','').'</th>
                </tr>';
        }
	
	$registros.="
	<input type='hidden' name='rIgv' id='rIgv' value='".number_format($igv,2,'.','')."'>
	<input type='hidden' name='rIgv2' id='rIgv2' value='".number_format($igv2,2,'.','')."'>"; 
	
	if($impuesto=='N'){
            $registros.="<input type='hidden' name='txtTotal' id='txtTotal' value='".number_format($total,2,'.','')."' size='5' readonly=''>";
            $registros.='
                <tr class="blue lighten-4 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">TOTAL</th>
                    <th class="center" id="thTotalGeneral">'.number_format($total,2,'.','').'</th>
                </tr>';
        }
	else{
            $registros.="<input type='hidden' name='txtTotal' id='txtTotal' value='".number_format($suma,2,'.','')."' size='5' readonly=''>";
            $registros.='
                <tr class="blue lighten-4 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">TOTAL</th>
                    <th class="center" id="thTotalGeneral">'.number_format($suma,2,'.','').'</th>
                </tr>';
        }

	$registros.="
	<input type='hidden' name='rTotal' id='rTotal' value='".number_format($total,2,'.','')."'>
	<input type='hidden' name='rTotal2' id='rTotal2' value='".number_format($suma,2,'.','')."'>
        <input type='hidden' name='txtSubcuenta' id='txtSubcuenta' value='NO'>
	</tfoot></table>";
        $registros.=$datosIdProductos;
        
	//$registros=utf8_encode($registros."<br /><div id='divDividirCuenta' style='display: none;'><label style='font-size:14px'>Dividir Cuenta : </label>".$registros2."</div><br />".$datosIdProductos);
        $array= array("registros"=>  utf8_encode($registros),"registros2"=>  utf8_encode($registros2));
	echo json_encode($array);
	break;
case "quitarDetalleVenta" :
	$iddetalle=$_POST["IdDetalle"];
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];

	if(isset($_SESSION['R_carroVenta']))
		$carroVenta=$_SESSION['R_carroVenta'];

	unset($carroVenta[($iddetalle)]);
		
	$_SESSION['R_carroVenta']=$carroVenta;
	
	$contador=0;
	$suma=0;
	$registros.="<table class=registros width='100%' border=1>
	<th>N&uacute;mero Pedido</th>	
	<th>C&oacute;digo</th>
	<th>Producto</th>
	<th>Unidad</th>
	<th>Cantidad</th>
	<th>Precio Venta</th>	
	<th>SubTotal</th>
	";
	foreach($carroVenta as $k => $v){
		$subto=$v['cantidad']*$v['precioventa'];
		$suma=$suma+$subto;
		$registros.="<tr><td>".$v["nropedido"]."</td>";
		$registros.="<td>".$v["codigo"]."</td>";
		$registros.="<td>".utf8_decode($v["producto"])."</td>";
		$registros.="<td>".$v["unidad"]."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"],0)."</td>";
		$registros.="<td align='right'>".number_format($v["precioventa"],2)."</td>";
		$registros.="<td align='right'>".number_format($v["cantidad"]*$v["precioventa"],2)."</td>";
		$registros.="<td><a href='#' onClick='quitar(".$v["iddetalle"].");'>Quitar</a></td></tr>";
	}
	$registros.="</table>";
	$igv=($_SESSION["R_IGV"]/100)*$suma;
	$total=number_format($suma,2)+number_format($igv,2);
		
	$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	$igv2=number_format($suma,2)-number_format($sub2,2);
	
	
	if($documento!='5'){	$type='hidden';	$t1='';$t2='';}else{$type ='text';$t1='Igv: ';$t2='Subtotal: ';}
	
	$registros.="
	<div><center><b>$t2 ";
	
	if($impuesto=='N'){
	$registros.="<input type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2,'.','')."' readonly=''>";}
	else{
	$registros.="<input type='$type' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2,'.','')."' readonly=''>";}
	
	$registros.="
	<input type='hidden' name='rSub' id='rSub' value='".number_format($suma,2,'.','')."'>
	<input type='hidden' name='rSub2' id='rSub2' value='".number_format($sub2,2,'.','')."'>
	$t1";
	
	if($impuesto=='N'){
	$registros.="<input type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv,2,'.','')."' readonly='' >";}else{
	$registros.="<input type='$type' name='txtIgv' id='txtIgv' value='".number_format($igv2,2,'.','')."' readonly='' >";}
	
	$registros.="
	<input type='hidden' name='rIgv' id='rIgv' value='".number_format($igv,2,'.','')."'>
	<input type='hidden' name='rIgv2' id='rIgv2' value='".number_format($igv2,2,'.','')."'>
	Total :"; 
	
	if($impuesto=='N'){
	$registros.="<input type='text' name='txtTotal' id='txtTotal' value='".number_format($total,2,'.','')."' readonly=''>";}
	else{
	$registros.="<input type='text' name='txtTotal' id='txtTotal' value='".number_format($suma,2,'.','')."' readonly=''>";}

	$registros.="
	<input type='hidden' name='rTotal' id='rTotal' value='".number_format($total,2,'.','')."'>
	<input type='hidden' name='rTotal2' id='rTotal2' value='".number_format($suma,2,'.','')."'>
	</center>
	</div>";

	$registros=utf8_encode($registros);
	echo $registros;
	break;
case "actualizarDetalleVenta" :
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
        $tipoVenta = $_POST["tipoVenta"];
        if($tipoVenta=="V"){
            $descuento = $_POST["modalidadCampo2"];
        }elseif($tipoVenta=="A"){
            $pago = $_POST["modalidadCampo2"];
        }elseif($tipoVenta=="T"){
            $idscortesia = $_POST["modalidadCampo4"];
            $idscortesia = explode(",",$idscortesia);
        }

	if(isset($_SESSION['R_carroVenta']))
		$carroVenta=$_SESSION['R_carroVenta'];

	$_SESSION['R_carroVenta']=$carroVenta;
        if(!is_array($carroVenta)){
            $carroVenta=array();
        }
	
	$contador=0;
	$suma=0;
        $registros.='
            <table class="striped bordered highlight">
                <thead>
                    <tr>
                        <th class="center">NUMERO</th>
                        <th class="center">CODIGO</th>
                        <th class="center">PRODUCTO</th>
                        <th class="center">UNIDAD</th>
                        <th class="center">CANTIDAD</th>
                        <th class="center">PRECIO VENTA</th>
                        <th class="center">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody id="tBodyDetalleVenta">';
	foreach($carroVenta as $k => $v){
            if($tipoVenta=="D" && $v['bar']=="N"){
                $v['precioventa'] = round($v['precioventa']/2,2);
            }elseif($tipoVenta=="T" && in_array($v["idproducto"], $idscortesia)){
                $v['precioventa'] = 0;
            }
            $subto=$v['cantidad']*$v['precioventa'];
            $suma=$suma+$subto;
            $registros.='
                <tr class="hoverable" id="trTblDetalle_'.$v["idproducto"].'">
                    <td class="center">'.$v["nropedido"].'</td>
                    <td class="center">'.$v["codigo"].'</td>
                    <td class="center"><textarea id="txtProducto'.$v["idproducto"].'" name="txtProducto'.$v["idproducto"].'">'.utf8_decode($v["producto"]).'</textarea></td>
                    <td class="center">'.$v["unidad"].'</td>
                    <td class="center" id="tdCant_'.$v["idproducto"].'">'.number_format($v["cantidad"],0).'</td>
                    <td class="center" id="tdPrecio_'.$v["idproducto"].'">'.number_format($v["precioventa"],2).'</td>
                    <td class="center" id="tdSubtotal_'.$v["idproducto"].'">'.number_format($v["cantidad"]*$v["precioventa"],2).'</td>
                </tr>';
            $datosIdProductos.=$v["idproducto"]."/";
	}
        $datosIdProductos= "<input type='hidden' id='txtDatosProductos' value='".substr($datosIdProductos,0,strlen($datosIdProductos)-1)."' />" ;
	
        if($tipoVenta=="V"){
            $registros.='<tr class="hoverable" id="">'
                    . '<td class="center">-</td>'
                    . '<td class="center">-</td>'
                    . '<td class="center">DESCUENTO POR VALE</td>'
                    . '<td class="center">UNIDAD</td>'
                    . '<td class="center">1</td>'
                    . '<td class="center">-'.  number_format($descuento,2).'</td><td class="center">-'.  number_format($descuento,2).'</td>'
                    . '</tr></tbody><tfoot>';
            $suma = $suma - $descuento;
        }elseif($tipoVenta=="A"){
            $registros.='<tr class="hoverable" id="">'
                    . '<td class="center">-</td>'
                    . '<td class="center">-</td>'
                    . '<td class="center">MONTO POR PAGO ANTICIPADO</td>'
                    . '<td class="center">UNIDAD</td>'
                    . '<td class="center">1</td>'
                    . '<td class="center">-'.  number_format($pago,2).'</td><td class="center">-'.  number_format($pago,2).'</td>'
                    . '</tr></tbody><tfoot>';
            $suma = $suma - $pago;
        }else{
            $registros.="</tbody><tfoot>";
        }
        if($suma<0){
            $suma = 0;
        }
	$igv=($_SESSION["R_IGV"]/100)*$suma;
	$total=number_format($suma,2)+number_format($igv,2);
		
	$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	$igv2=number_format($suma,2)-number_format($sub2,2);
	
	if($documento!='5'){	$type='hidden';	$t1='';$t2='';}else{$type ='text';$t1='Igv: ';$t2='Subtotal: ';}
	
	if($impuesto=='N'){
            $registros.="<input type='hidden' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2,'.','')."' readonly=''>";
            $registros.='
            <tr class="blue lighten-4 blue-text text-darken-4">
                <th colspan="5">&nbsp;</th>
                <th class="right">SUBTOTAL</th>
                <th class="center" id="thSubtotalGeneral">'.number_format($suma,2,'.','').'</th>
            </tr>';
        }else{
            $registros.="<input type='hidden' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2,'.','')."' readonly=''>";
            if($documento==5){
                $registros.='
                <tr class="blue lighten-4 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">SUBTOTAL</th>
                    <th class="center" id="thSubtotalGeneral">'.number_format($sub2,2,'.','').'</th>
                </tr>';
            }else{
                $registros.='
                <tr class="blue lighten-4 blue-text text-darken-4" hidden>
                    <th colspan="5">&nbsp;</th>
                    <th class="right">SUBTOTAL</th>
                    <th class="center" id="thSubtotalGeneral">'.number_format($sub2,2,'.','').'</th>
                </tr>';
            }
        }
	
	$registros.="
	<input type='hidden' name='rSub' id='rSub' value='".number_format($suma,2,'.','')."'>
	<input type='hidden' name='rSub2' id='rSub2' value='".number_format($sub2,2,'.','')."'>";
	
	if($impuesto=='N'){
            $registros.="<input type='hidden' name='txtIgv' id='txtIgv' value='".number_format($igv,2,'.','')."' readonly='' >";
            $registros.='
                <tr class="blue lighten-5 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">I.G.V.</th>
                    <th class="center" id="thIgvGeneral">'.number_format($igv,2,'.','').'</th>
                </tr>';
        }else{
            $registros.="<input type='hidden' name='txtIgv' id='txtIgv' value='".number_format($igv2,2,'.','')."' readonly='' >";
            if($documento==5){
                $registros.='
                    <tr class="blue lighten-5 blue-text text-darken-4">
                        <th colspan="5">&nbsp;</th>
                        <th class="right">I.G.V.</th>
                        <th class="center" id="thIgvGeneral">'.number_format($igv2,2,'.','').'</th>
                    </tr>';
            }else{
                $registros.='
                    <tr class="blue lighten-5 blue-text text-darken-4" hidden>
                        <th colspan="5">&nbsp;</th>
                        <th class="right">I.G.V.</th>
                        <th class="center" id="thIgvGeneral">'.number_format($igv2,2,'.','').'</th>
                    </tr>';
            }
        }
	
	$registros.="
	<input type='hidden' name='rIgv' id='rIgv' value='".number_format($igv,2,'.','')."'>
	<input type='hidden' name='rIgv2' id='rIgv2' value='".number_format($igv2,2,'.','')."'>"; 
	
	if($impuesto=='N'){
            $registros.="<input type='hidden' name='txtTotal' id='txtTotal' value='".number_format($total,2,'.','')."' size='5' readonly=''>";
            $registros.='
                <tr class="blue lighten-4 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">TOTAL</th>
                    <th class="center" id="thTotalGeneral">'.number_format($total,2,'.','').'</th>
                </tr>';
        }
	else{
            $registros.="<input type='hidden' name='txtTotal' id='txtTotal' value='".number_format($suma,2,'.','')."' size='5' readonly=''>";
            $registros.='
                <tr class="blue lighten-4 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">TOTAL</th>
                    <th class="center" id="thTotalGeneral">'.number_format($suma,2,'.','').'</th>
                </tr>';
        }

	$registros.="
	<input type='hidden' name='rTotal' id='rTotal' value='".number_format($total,2,'.','')."'>
	<input type='hidden' name='rTotal2' id='rTotal2' value='".number_format($suma,2,'.','')."'>
        <input type='hidden' name='txtSubcuenta' id='txtSubcuenta' value='NO'>
	</tfoot></table>";
        
        $registros.=$datosIdProductos;

	$registros=utf8_encode($registros);
	echo $registros;
	break;
 case "generaNumero" :
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    if($_POST["serie"]=="N"){
        $serie="001";
    }else{
        $serie="002";
        $_POST["IdTipoDocumento"]=4;
    }
	$numero = $ObjDetalleAlmacen->generaNumero(2,$_POST["IdTipoDocumento"],substr($_SESSION["R_FechaProceso"],6,4),$serie);

	echo "vnumero='".$numero."';";
	break;
 case "generaNumeroElectronico" :
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    if($_POST["IdTipoDocumento"]==4){
        $serie="B00".$_SESSION["R_IdCaja"];
    }elseif($_POST["IdTipoDocumento"]==5){
        $serie="F00".$_SESSION["R_IdCaja"];
    }else{
        $serie="T00".$_SESSION["R_IdCaja"];
    }
	$numero = $ObjDetalleAlmacen->generaNumeroElectronico(2,$_POST["IdTipoDocumento"],substr($_SESSION["R_FechaProceso"],6,4),$serie);

	echo "vnumero='".$numero."';";
	break;
 case "genera_cboCaja" :
	$seleccionado=$_POST['seleccionado'];
	$disabled=$_POST['disabled'];
	require("../modelo/clsCaja.php");
	$ObjCaja = new clsCaja(52,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjCaja->consultarCajaxSalon($_POST["IdSalon"]);

	$Cajas="<select name='cboIdCaja' id='cboIdCaja' title='Debe indicar una caja' ".$disabled.">";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		$seleccionar="";
		if($registro->idmesa==$seleccionado) $seleccionar="selected";
		$Cajas=$Cajas."<option value='".$registro->idcaja."' ".$seleccionar.">".$registro->numero."</option>";
	}
	}else{
		$Cajas=$Cajas."<option value='0'>No hay cajas disponibles</option>";
	}
	$Cajas=$Cajas."</select>";
	$Cajas=utf8_encode($Cajas);
	echo $Cajas;
	break;
case "obtenerDatosReserva" :
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$rst = $ObjDetalleAlmacen->consultarMovimiento(1,1,'2',1,$_POST["IdPedido"],5);
	$dato = $rst->fetchObject();
	echo "vidpersona='".$dato->idpersona."';vidsucursalpersona='".$dato->idsucursalpersona."';";
	break;
case "buscarDetalleProducto":
    //require("../modelo/clsMovimiento.php");
    $objMovimiento=new clsMovimiento(10, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $detalle=$objMovimiento->buscarDetalleProducto2($_POST["idmov"]);
    $nuevoregistro = '<table id="tbpaginaweb" class="bordered highlight">
    <thead>
     <tr>
	<th class="center">Producto</th>
	<th class="center">Cantidad</th>
	<th class="center">P. Unitario</th>
        <th class="center">P. Total</th>
     </tr>
    </thead>
    <tbody>';
    $nuevototal = 0.0;
    while($dato = $detalle->fetchObject()){
        $descripcion=$dato->producto;
	if(strlen($descripcion)>=50){
		$descripcion=substr($descripcion,0,49);
	}
        $nuevoregistro.='<tr class="yellow lighten-2">
            <td>'.($descripcion).'</td>
            <td class="center">'.  number_format($dato->cantidad,2).'</td>
            <td class="center">'.  number_format($dato->precioventa,2).'</td>
            <td class="center">'.  number_format($dato->precioventa*$dato->cantidad,2).'</td>
        </tr>';
        $nuevototal+=$dato->precioventa*$dato->cantidad;
    }
    $nuevoregistro.='</tbody>
            <tfoot>
              <tr>
                  <th></th>
                  <th></th>
                  <th class="center indigo darken-4 white-text">TOTAL</th>
                  <th class="center indigo darken-4 white-text"">'.  number_format($nuevototal, 2).'</th>
              </tr>
            </tfoot>
      </table>';
    echo $nuevoregistro;
    break;
case "buscarRetencionMovimiento":
    //require("../modelo/clsMovimiento.php");
    $objMovimiento=new clsMovimiento(10, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $detalle=$objMovimiento->obtenerDataSQL("SELECT * FROM retencion WHERE estado = 'N' AND idmovimiento = ".$_POST["idmov"])->fetchObject();
    if(empty($detalle)){
        $detalle = new ArrayObject(array());
        $detalle->idretencion = "0";
        $detalle->fecha = date("Y-m-d");
        $detalle->monto = "";
        $detalle->numero = "";
        $disable = "";
        $clase = "";
        $numeromoveg = "";
    }else{
        $disable = 'readonly=""';
        $clase = 'class="active"';
        $numero=$objMovimiento->obtenerDataSQL("SELECT * FROM (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) AS MOV WHERE MOV.estado = 'N' AND MOV.idmovimiento = ".$detalle->idmovegreso)->fetchObject();
        $numero = $numero->numero;
        $numeromoveg = '
                  <div class="col s12">
                      <div class="input-field inline">
                        <input type="text" '.$disable.' value="'.$numero.'">
                        <label class="active">Numero de Movimiento de EGRESO</label>
                      </div>
                  </div>';
    }
    $nuevoregistro = '<input type="hidden" id="Retencionidretencion" name="idretencion" value="'.$detalle->idretencion.'">
                <input type="hidden" id="Retencionidmovimiento" name="idmovimiento" value="'.$_POST["idmov"].'">
                <div class="row">
                  <div class="col s12">
                      <div class="input-field inline">
                        <input id="monto" type="number" '.$disable.' name="monto" step="0.01" min="0.01" value="'.$detalle->monto.'">
                        <label '.$clase.' for="monto">Monto de Retencion</label>
                      </div>
                  </div>
                  <div class="col s12">
                      <div class="input-field inline">
                        <input id="numero" type="text" '.$disable.' name="numero" value="'.$detalle->numerodocumento.'">
                        <label '.$clase.' for="numero">Numero de documento</label>
                      </div>
                  </div>
                  <div class="col s12">
                      <div class="input-field inline">
                        <input id="fecha" type="date" '.$disable.' name="fecha" value="'.$detalle->fecha.'">
                        <label class="active">Fecha de Retencion</label>
                      </div>
                  </div>'.$numeromoveg.'
                </div>';
    echo $nuevoregistro;
    break;
case "guardarRetencionMovimiento":
    $ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objMovimiento=new clsMovimiento(10, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    if(ob_get_length()) ob_clean();
    $objMovimiento->iniciarTransaccion();
    $detalle=$objMovimiento->obtenerDataSQL("SELECT * FROM (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) AS MOV WHERE MOV.estado = 'N' AND MOV.idmovimiento = ".$_POST["idmovimiento"])->fetchObject();
    $_POST["txtTotal"] = $_POST["monto"];
    $monto=$_POST["txtTotal"];
    $_POST["cboConceptoPago"] = 29;//ID DE CONCEPTO CREADO PARA LAS RETENCIONES
    $_POST["cboIdTipoDocumento"] = 10;//ID DE EGRESOS
    $_POST["IdTipoDocumento"] = $_POST["cboIdTipoDocumento"];
    $_POST["txtFecha"] = date("Y-m-d");
    $_POST["txtIdPersona"] = $detalle->idpersona;
    $_POST["txtNroOperacion"] = $_POST["numero"];
    $_POST["txtComentario"] = "MOVIMIENTO DE EGRESO AUTOMATICO POR RETENCION DE MOVIMIENTO ".$detalle->numero;
    $_POST["txtIdSucursalPersona"] = $_SESSION['R_IdSucursalUsuario'];
    $numero = $ObjDetalleAlmacen->generaNumeroSinSerie(4,$_POST["IdTipoDocumento"],substr($_SESSION["R_FechaProceso"],3,2));
    $_POST["txtNumero"] = $numero;
    $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $monto, 0, $monto, $monto, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdUsuario'], NULL, NULL, $_POST["txtComentario"],'N',0,$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario']);
    $dato=$rst->fetchObject();
    if(is_string($rst)){
        $objMovimiento->abortarTransaccion(); 
        if(ob_get_length()) ob_clean();
        echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
        exit();
    }else{
        $rst = $objMovimiento->ejecutarSQL("update movimientohoy set numerooperacion='".$_POST["txtNroOperacion"]."' where idmovimiento=".$dato->idmovimiento." and idsucursal=".$_SESSION["R_IdSucursal"]);
        $rst = $objMovimiento->ejecutarSQL("INSERT INTO retencion (idmovimiento,monto,numerodocumento,fecha,idmovegreso) VALUES (".$_POST["idmovimiento"].",".$monto.",'".$_POST["numero"]."','".$_POST["fecha"]."',".$dato->idmovimiento.")");
        $objMovimiento->finalizarTransaccion(); 
        if(ob_get_length()) ob_clean();
        echo "Guardado correctamente";
    }
    break;
case "actualizarDetalleVenta3" :
	$moneda=$_POST["Moneda"];
	$documento=$_POST["IdTipoDocumento"];
	$impuesto=$_POST["IncluyeIgv"];
    $tipoVenta = $_POST["tipoVenta"];
    
	if(isset($_SESSION['R_carroVenta']))
		$carroVenta=$_SESSION['R_carroVenta'];

	$_SESSION['R_carroVenta']=$carroVenta;
    if(!is_array($carroVenta)){
        $carroVenta=array();
    }
	
	$contador=0;
	$suma=0;
    $registros.='
        <table class="striped bordered highlight">
            <thead>
                <tr>
                    <th class="center">NUMERO</th>
                    <th class="center">CODIGO</th>
                    <th class="center">PRODUCTO</th>
                    <th class="center">UNIDAD</th>
                    <th class="center">CANTIDAD</th>
                    <th class="center">PRECIO VENTA</th>
                    <th class="center">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody id="tBodyDetalleVenta">';
	foreach($carroVenta as $k => $v){
        if($_POST["Comida"]=="S" && $v['comida']=="S"){
            $v['precioventa'] = round($v['precioventa']*(1-$_POST["descuento"]/100),2);
        }
        if($_POST["Bar"]=="S" && $v['bar']=="S"){
            $v['precioventa'] = round($v['precioventa']*(1-$_POST["descuento"]/100),2);
        }
        /*if($v['bar']=="N"){
            $v['precioventa'] = round($v['precioventa']*(1-$_POST["descuento"]/100),2);
        }*/
        $subto=$v['cantidad']*$v['precioventa'];
        $suma=$suma+$subto;
        $registros.='
            <tr class="hoverable" id="trTblDetalle_'.$v["idproducto"].'">
                <td class="center">'.$v["nropedido"].'</td>
                <td class="center">'.$v["codigo"].'</td>
                <td class="center"><textarea id="txtProducto'.$v["idproducto"].'" name="txtProducto'.$v["idproducto"].'">'.utf8_decode($v["producto"]).'</textarea></td>
                <td class="center">'.$v["unidad"].'</td>
                <td class="center" id="tdCant_'.$v["idproducto"].'">'.number_format($v["cantidad"],0).'</td>
                <td class="center" id="tdPrecio_'.$v["idproducto"].'">'.number_format($v["precioventa"],2).'</td>
                <td class="center" id="tdSubtotal_'.$v["idproducto"].'">'.number_format($v["cantidad"]*$v["precioventa"],2).'</td>
            </tr>';
        $datosIdProductos.=$v["idproducto"]."/";
	}
    $datosIdProductos= "<input type='hidden' id='txtDatosProductos' value='".substr($datosIdProductos,0,strlen($datosIdProductos)-1)."' />" ;

    $registros.="</tbody><tfoot>";
    
    if($suma<0){
        $suma = 0;
    }
	$igv=($_SESSION["R_IGV"]/100)*$suma;
	$total=number_format($suma,2)+number_format($igv,2);
		
	$sub2=(100/(100+$_SESSION["R_IGV"]))*$suma;
	$igv2=number_format($suma,2)-number_format($sub2,2);
	
	if($documento!='5'){	$type='hidden';	$t1='';$t2='';}else{$type ='text';$t1='Igv: ';$t2='Subtotal: ';}
	
	if($impuesto=='N'){
        $registros.="<input type='hidden' name='txtSubtotal' id='txtSubtotal' value='".number_format($suma,2,'.','')."' readonly=''>";
        $registros.='
        <tr class="blue lighten-4 blue-text text-darken-4">
            <th colspan="5">&nbsp;</th>
            <th class="right">SUBTOTAL</th>
            <th class="center" id="thSubtotalGeneral">'.number_format($suma,2,'.','').'</th>
        </tr>';
    }else{
        $registros.="<input type='hidden' name='txtSubtotal' id='txtSubtotal' value='".number_format($sub2,2,'.','')."' readonly=''>";
        if($documento==5){
            $registros.='
            <tr class="blue lighten-4 blue-text text-darken-4">
                <th colspan="5">&nbsp;</th>
                <th class="right">SUBTOTAL</th>
                <th class="center" id="thSubtotalGeneral">'.number_format($sub2,2,'.','').'</th>
            </tr>';
        }else{
            $registros.='
            <tr class="blue lighten-4 blue-text text-darken-4" hidden>
                <th colspan="5">&nbsp;</th>
                <th class="right">SUBTOTAL</th>
                <th class="center" id="thSubtotalGeneral">'.number_format($sub2,2,'.','').'</th>
            </tr>';
        }
    }
	
	$registros.="
	<input type='hidden' name='rSub' id='rSub' value='".number_format($suma,2,'.','')."'>
	<input type='hidden' name='rSub2' id='rSub2' value='".number_format($sub2,2,'.','')."'>";
	
	if($impuesto=='N'){
        $registros.="<input type='hidden' name='txtIgv' id='txtIgv' value='".number_format($igv,2,'.','')."' readonly='' >";
        $registros.='
            <tr class="blue lighten-5 blue-text text-darken-4">
                <th colspan="5">&nbsp;</th>
                <th class="right">I.G.V.</th>
                <th class="center" id="thIgvGeneral">'.number_format($igv,2,'.','').'</th>
            </tr>';
    }else{
        $registros.="<input type='hidden' name='txtIgv' id='txtIgv' value='".number_format($igv2,2,'.','')."' readonly='' >";
        if($documento==5){
            $registros.='
                <tr class="blue lighten-5 blue-text text-darken-4">
                    <th colspan="5">&nbsp;</th>
                    <th class="right">I.G.V.</th>
                    <th class="center" id="thIgvGeneral">'.number_format($igv2,2,'.','').'</th>
                </tr>';
        }else{
            $registros.='
                <tr class="blue lighten-5 blue-text text-darken-4" hidden>
                    <th colspan="5">&nbsp;</th>
                    <th class="right">I.G.V.</th>
                    <th class="center" id="thIgvGeneral">'.number_format($igv2,2,'.','').'</th>
                </tr>';
        }
    }
	
	$registros.="
	<input type='hidden' name='rIgv' id='rIgv' value='".number_format($igv,2,'.','')."'>
	<input type='hidden' name='rIgv2' id='rIgv2' value='".number_format($igv2,2,'.','')."'>"; 
	
	if($impuesto=='N'){
        $registros.="<input type='hidden' name='txtTotal' id='txtTotal' value='".number_format($total,2,'.','')."' size='5' readonly=''>";
        $registros.='
            <tr class="blue lighten-4 blue-text text-darken-4">
                <th colspan="5">&nbsp;</th>
                <th class="right">TOTAL</th>
                <th class="center" id="thTotalGeneral">'.number_format($total,2,'.','').'</th>
            </tr>';
    }else{
        $registros.="<input type='hidden' name='txtTotal' id='txtTotal' value='".number_format($suma,2,'.','')."' size='5' readonly=''>";
        $registros.='
            <tr class="blue lighten-4 blue-text text-darken-4">
                <th colspan="5">&nbsp;</th>
                <th class="right">TOTAL</th>
                <th class="center" id="thTotalGeneral">'.number_format($suma,2,'.','').'</th>
            </tr>';
    }

	$registros.="
	<input type='hidden' name='rTotal' id='rTotal' value='".number_format($total,2,'.','')."'>
	<input type='hidden' name='rTotal2' id='rTotal2' value='".number_format($suma,2,'.','')."'>
        <input type='hidden' name='txtSubcuenta' id='txtSubcuenta' value='NO'>
	</tfoot></table>";
        
    $registros.=$datosIdProductos;

	$registros=utf8_encode($registros);
	echo $registros;
	break;
    
case "BuscaVentaJSON":
	$objMovimiento = new clsMovimiento(41,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $objMovimiento->obtenerDataSQL("select * from (select * from movimiento union all select * from movimientohoy) as T
    where idtipomovimiento in (2) and estado<>'A' and situacion='N'");
    $datos = array();
	while($registro=$consulta->fetchObject()){
        $datos["$registro->numero"] = "$registro->idsucursal|$registro->idmovimiento";
	}
    echo json_encode(["datos"=>$datos]);
    break;
case "agregarDetalleNota":
    $objMovimiento=new clsMovimiento(10, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $detalle=$objMovimiento->buscarDetalleProducto2($_POST["IdDocVenta"]);
    $nuevoregistro = '<table id="tbpaginaweb" class="bordered highlight">
    <thead>
     <tr>
	<th class="center">Producto</th>
	<th class="center">Cantidad</th>
	<th class="center">P. Unitario</th>
        <th class="center">P. Total</th>
     </tr>
    </thead>
    <tbody>';
    $nuevototal = 0.0;
    while($dato = $detalle->fetchObject()){
        $descripcion=$dato->producto;
	if(strlen($descripcion)>=50){
		$descripcion=substr($descripcion,0,49);
	}
        $nuevoregistro.='<tr class="yellow lighten-2">
            <td>'.($descripcion).'</td>
            <td class="center">'.  number_format($dato->cantidad,2).'</td>
            <td class="center">'.  number_format($dato->precioventa,2).'</td>
            <td class="center">'.  number_format($dato->precioventa*$dato->cantidad,2).'</td>
        </tr>';
        $nuevototal+=$dato->precioventa*$dato->cantidad;
    }
    $nuevoregistro.='</tbody>
            <tfoot>
              <tr>
                  <th></th>
                  <th></th>
                  <th class="center indigo darken-4 white-text">TOTAL</th>
                  <th class="center indigo darken-4 white-text"">'.  number_format($nuevototal, 2).'</th>
              </tr>
            </tfoot>
      </table>';
    echo $nuevoregistro;
    break;
case "listaFacturacion":
    $ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $rs=$ObjDetalleAlmacen->obtenerDataSQL("select * from movimiento where manual='N' and fecha>='2020-10-23 00:00:00' and idpersona=76 and idtipodocumento=5 and idtipodocumento!=19");
    while($dato=$rs->fetchObject()){
        $datos.=$dato->idmovimiento."|".$dato->idtipodocumento."@";
    }
    echo substr($datos,0,strlen($datos)-1);
    break;
case "cambiarComprobante":
    $ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $serie="B001";
    $numero = $ObjDetalleAlmacen->generaNumeroElectronico(2,4,substr($_SESSION["R_FechaProceso"],6,4),$serie);
    $rs=$ObjDetalleAlmacen->ejecutarSQL("update movimiento set numero='$numero',manual='N',idtipodocumento=4 where idmovimiento=".$_POST["idventa"]);
    $rst=$ObjDetalleAlmacen->ejecutarSQL("update movimientohoy set numero='$numero',manual='N',idtipodocumento=4 where idmovimiento=".$_POST["idventa"]);
    echo $rs." - ".$rst;
    break;
case "confirmarPedido":
    $idventa=$_POST["idventa"];
    $objMovimiento = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objStockProducto = new clsStockProducto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $rs1=$objMovimiento->buscarDetalleProducto($idventa,"h");
    $carroPedido=array();
    while($dat=$rs1->fetchObject()){
        $carroPedido[]=array("cantidad"=>$dat->cantidad,"precioventa"=>$dat->precioventa,"abreviatura"=>trim($dat->comentario==""?$dat->abreviatura:$dat->comentario),"preciocompra"=>$dat->preciocompra,"kardex"=>$dat->kardex,"compuesto"=>$dat->compuesto,"idproducto"=>$dat->idproducto,"idsucursalproducto"=>$dat->idsucursalproducto,"idunidad"=>$dat->idunidad);
    }
    //if($_SESSION["R_IdCaja"]=="4" || $_SESSION["R_IdCaja"]=="5"){//
    $idsucursal=14;
    $venta = $objMovimiento->obtenerDataSQL("select T.* from (select * from movimientohoy union all select * from movimiento) T where T.idmovimiento=".$idventa)->fetchObject();
    if($venta->idcaja=="4" || $venta->idcaja=="5"){
        $idsucursal2="2";
    }elseif($venta->idcaja=="6"){
        $idsucursal2="3";
    }else{
        $idsucursal2="1";
    }
    /*}else{
        $idsucursal=$_SESSION['R_IdSucursal'];
    }*/
    foreach($carroPedido as $v){
        if($v['kardex']=='S'){
            $res=$objStockProducto->insertar($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$idventa,'S',$v["preciocompra"],date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            if($res!='Guardado correctamente'){
                if(ob_get_length()) ob_clean();
                echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
                exit();
            }
            $res=$objStockProducto->insertar($idsucursal2,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$idventa,'S',$v["preciocompra"],date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
        }elseif($v['kardex']!='S' and $v['compuesto']=='S'){
            $res=$objStockProducto->insertarcompuesto($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$idventa,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            if($res!='Guardado correctamente'){
                if(ob_get_length()) ob_clean();
                echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
                exit();
            }
            $res=$objStockProducto->insertarcompuesto($idsucursal2,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$idventa,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            
        }
    }


    $objMovimiento->ejecutarSQL("update detallestock set fecha='".date('Y-m-d H:i:s')."',idusuario=".$_SESSION["R_IdUsuario"].",situacion='C',idsucursalstock=".$idsucursal." where iddetallestock=".$_POST["id"]);
    echo "Descarga ok";
    break;
case "confirmarPedido2":
    $objMovimiento = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objStockProducto = new clsStockProducto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

    $rs=$objMovimiento->obtenerDataSQL("select * from detallestock where situacion='C' and fecha>='2018-10-20 10:00:00'");
    while($da=$rs->fetchObject()){
        $idventa=$da->idventa;
        $rs1=$objMovimiento->buscarDetalleProducto($idventa,"h");
        $carroPedido=array();
        while($dat=$rs1->fetchObject()){
            $carroPedido[]=array("cantidad"=>$dat->cantidad,"precioventa"=>$dat->precioventa,"abreviatura"=>trim($dat->comentario==""?$dat->abreviatura:$dat->comentario),"preciocompra"=>$dat->preciocompra,"kardex"=>$dat->kardex,"compuesto"=>$dat->compuesto,"idproducto"=>$dat->idproducto,"idsucursalproducto"=>$dat->idsucursalproducto,"idunidad"=>$dat->idunidad);
        }
        if($_SESSION["R_IdCaja"]=="4" || $_SESSION["R_IdCaja"]=="5"){//
            $idsucursal=2;
        }else{
            $idsucursal=$_SESSION['R_IdSucursal'];
        }
        foreach($carroPedido as $v){
            if($v['kardex']=='S'){
                $res=$objStockProducto->insertar($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$idventa,'S',$v["preciocompra"],date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                if($res!='Guardado correctamente'){
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
                    exit();
                }
            }elseif($v['kardex']!='S' and $v['compuesto']=='S'){
                $res=$objStockProducto->insertarcompuesto($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$idventa,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                if($res!='Guardado correctamente'){
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
                    exit();
                }
            }
        }
    }
    echo "Descarga ok";
    break;
case "validarUsuario":
    $ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $rs=$ObjDetalleAlmacen->obtenerDataSQL("select * from usuario where clave='".md5($_POST["pass"])."' and estado='N' and idperfil in (2,1)");
    if($rs->rowCount()>0){
        echo "vmsg='S';";
    }else{
        echo "vmsg='N';";
    }
    break;
case "validarAP":
    $ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $band=true;
    if(trim($_POST["visa"]!="")){
        $lista = explode("-", trim($_POST["visa"]));
        for($c=0;$c<count($lista);$c++){
            if(trim($lista[$c])!=""){
                $rs=$ObjDetalleAlmacen->obtenerDataSQL("select T.* from (select * from movimiento union all select * from movimientohoy) as T where T.estado='N' and T.idtipomovimiento=2 and T.comentario like '%@".trim($lista[$c])."%'");
                if($rs->rowCount()>0){
                    $band=false;
                    $msg="Ref Visa ya registrado";
                }
            }
        }
    }
    if(trim($_POST["master"]!="")){
        $lista = explode("-", trim($_POST["master"]));
        for($c=0;$c<count($lista);$c++){
            if(trim($lista[$c])!=""){
                $rs=$ObjDetalleAlmacen->obtenerDataSQL("select T.* from (select * from movimiento union all select * from movimientohoy) as T where T.estado='N' and T.idtipomovimiento=2 and T.numerotarjeta like '%".trim($lista[$c])."%'");
                if($rs->rowCount()>0){
                    $band=false;
                    $msg="Ref Master ya registrado";
                }
            }
        }
    }
    if($band){
        echo "vmsg='S';";
    }else{
        echo "vmsg='$msg';";
    }
    break;
case "detalleVenta":
    $objMovimiento=new clsMovimiento(10, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $detalle=$objMovimiento->buscarDetalleProducto2($_POST["id"]);
    $registro = '<table id="tbpaginaweb" class="bordered highlight">
    <thead>
     <tr>
        <th class="center">Producto</th>
        <th class="center">Cantidad</th>
        <th class="center">P. Unitario</th>
        <th class="center">P. Total</th>
     </tr>
    </thead>
    <tbody>';
    $nuevototal = 0.0;
    while($dato = $detalle->fetchObject()){
        $registro.="<tr>";
        $registro.="<td>$dato->producto</td>";
        $registro.="<td>$dato->cantidad</td>";
        $registro.="<td>".number_format($dato->precioventa,2,'.','')."</td>";
        $registro.="<td>".number_format($dato->precioventa*$dato->cantidad,2,'.','')."</td>";
        $registro.="</tr>";
    }
    echo $registro;
    break;
default:
	echo "Error en el Servidor: Operacion no Implementada.";
	exit();
}
?>