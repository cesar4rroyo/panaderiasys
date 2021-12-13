<?php
session_start();

?>

<html>
<head>
<style type="text/css">
<!--
.Estilo1 {
	font-size: 12px;
	font-weight: bold;
	<a href="frmComprobanteF.php">frmComprobanteF.php</a>
}

-->
</style>
<style type="text/css">

table.botonera {
    margin: auto;
    border-spacing: 0px;
    border-collapse: collapse;
    empty-cells: show;
    width: auto;
    background: url(img/comprobante/background-botoneragral.gif) repeat-x;
	background-color: #FFFFFF;
}

table.botonera table {
    border-spacing: 0px;
    border-collapse: collapse;
    empty-cells: show;
    width: 100%;
	background-color: #FFFFFF;
}

table.botonera td.puntos {
    height: 3px;
    background: url(img/comprobante/background-plkpuntos-hor.gif) repeat-x;
	background-color: #FFFFFF;
}

table.botonera td.frameTL {
    width: 6px;
    height: 3px;
    padding: 0;
    background: url(img/comprobante/esq-formato-interior-izq-sup.gif) no-repeat left top;
	background-color: #FFFFFF;
}

table.botonera td.frameTC {
    padding: 0;
    background: url(img/comprobante/background-formato-interior-sup.gif) repeat-x;
	background-color: #FFFFFF;
}

table.botonera td.frameTR {
    width: 6px;
    height: 3px;
    padding: 0;
    background: url(img/comprobante/esq-formato-interior-der-sup.gif) no-repeat right top;
}

table.botonera td.frameBL {
	width: 6px;
	height: 5.9px;
	padding: 0;
	background: url(img/comprobante/esq-formato-interior-izq-inf.gif) no-repeat left bottom;
	background-color: #FFFFFF;
}

table.botonera td.frameBC {
    padding: 0;
    background: url(img/comprobante/background-formato-interior-inf.gif) repeat-x;
	background-color: #FFFFFF;
}

table.botonera td.frameBR {
    width: 6px;
    height: 5.9px;
    padding: 0;
    background: url(img/comprobante/esq-formato-interior-der-inf.gif) no-repeat right bottom;
	background-color: #FFFFFF;
}

table.botonera td.frameCL {
    padding: 0;
    background: url(img/comprobante/background-formato-interior-izq.gif) repeat-y;
	background-color: #FFFFFF;
}


table.botonera td.frameC {
    padding: 0;
    
	background-color: #FFFFFF;
}

table.botonera td.frameCR {
    padding: 0;
    background: url(img/comprobante/background-formato-interior-der.gif) repeat-y;
	background-color: #FFFFFF;
}

table.botonera td.linkItem {
    height: 25px;
	background-color: #FFFFFF;
}

table.botonera a:link, table.botonera a:active, table.botonera a:visited {
    color: #3F4C69;
    text-decoration: none;
	background-color: #FFFFFF;
}

table.botonera a:hover { 
    color: #C82E28;
    text-decoration: underline;
	background-color: #FFFFFF;
}
.Estilo3 {font-size: 36px}
.Estilo5 {font-size: 12px}
.Estilo2 {
	font-size: 24px;
	font-weight: bold;
}
.Estilo4 {font-size: 12px}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><head>
<?php
$idventa=$_GET['idventa'];
require('../modelo/clsSucursal.php');
$objSucursal=new clsSucursal(58, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
require('../modelo/clsMovimiento.php');
$objMovimiento=new clsMovimiento(10, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

require('../modelo/clsPersona.php');
$objPersona=new clsPersona(23, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

$rst=$objMovimiento->consultarMovimientoComprobante(1,1,'2',1, $idventa, 2, '');
$detalle2 = $rst->fetchObject();

$pers=$objPersona->consultarxId($detalle2->idpersona,$detalle2->idsucursalpersona);
$detallePersona = $pers->fetchObject();

$reg.="<table width='710' style='margin:2;' style='font-size:12px;'>";
$reg.="<tr>
		<th width='80' align='center' bgcolor='#CCCCCC'>CANT.<strong></strong></th>
		<th width='80' align='center' bgcolor='#CCCCCC'><strong>UNID.</strong></th>
        <th width='750' align='center' bgcolor='#CCCCCC'><strong>DESCRIPCION</strong></th>
		<th width='100' align='center' bgcolor='#CCCCCC'><strong>P.UNIT.</strong></th>
		<th width='200' align='center' bgcolor='#CCCCCC'><strong>TOTAL</strong></th>
    </tr>";

	$registros.="<table width='710' style='margin:2;' style='font-size:12px;'>";

	$registros.="<tr height=20px>
		<td width='80' align='center'></td>
		<td width='80' align='center'><strong></strong></td>
        <td width='750' align='center'><strong></strong></td>
		<td width='100' align='center'><strong></strong></td>
		<td width='200' align='center'><strong></strong></td>
    </tr>";
	
	$regPorConsumo=$registros;
	$i=0;
$detalle=$objMovimiento->buscarDetalleProducto($idventa);
  while($dato = $detalle->fetchObject()){
  
  if(($i+1)%2==0)
		$color=" bgcolor='#CCCCCC'";
	else
		$color=" ";
  
  $registros.="<tr style='font-size:12px;' height=20px>
    <td align='center' ><b>".$dato->cantidad."</b></td>
	<td align='center' ><b>".$dato->unidad."</b></td>";
	//$descripcion=$dato->producto.' /'.$dato->categoria.'_'.$dato->marca.'_'.$dato->peso.$dato->unidadpeso."";
	$descripcion=utf8_decode($dato->producto);
	if(strlen($descripcion)>=80){
		$descripcion=substr($descripcion,0,79);
	}
	$registros.="<td align='left' ><b>&nbsp;".$descripcion."</b></td>
    <td align='right'   ><b>".$dato->precioventa."</b></td>
    <td align='right'  ><b>".number_format($dato->cantidad*$dato->precioventa,2)."</b></td>
  </tr>";
  
 	$reg.="<tr style='font-size:12px;' height=20px><td $color>&nbsp;</td><td $color>&nbsp;</td><td $color>&nbsp;</td><td  $color>&nbsp;</td><td  $color>&nbsp;</td>";
	
	if($i==0){
		$regPorConsumo.="<tr style='font-size:12px;' height=20px><td $color>&nbsp;</td><td $color>&nbsp;</td><td align='left' $color>&nbsp;&nbsp;&nbsp;<b>POR CONSUMO</b></td><td align='right' $color><b>".$detalle2->total."</b></td><td align='right' $color><b>".$detalle2->total."</b></td>";
	}else{
		$regPorConsumo.="<tr style='font-size:12px;' height=20px><td $color>&nbsp;</td><td $color>&nbsp;</td><td $color>&nbsp;</td><td align='right' $color>&nbsp;</td><td  align='right' $color> - </td>";
	}

  	$i=$i+1;
  }
  
  while($i<9){
  
  	if(($i+1)%2==0)
		$color=" bgcolor='#CCCCCC'";
	else
		$color=" ";
		
  	$registros.="<tr style='font-size:12px;' height=20px>
    <td align='center'>&nbsp;</td>
	<td align='center' >&nbsp;</td>
    <td align='center' >&nbsp;</td>
    <td align='right' >&nbsp;&nbsp; </td>
    <td align='right'> - </td>
  	</tr>";
  	$reg.="<tr style='font-size:12px;' height=20px><td $color>&nbsp;</td><td $color>&nbsp;</td><td $color>&nbsp;</td><td  $color>&nbsp;</td><td  $color>&nbsp;</td>";
	$regPorConsumo.="<tr style='font-size:12px;' height=20px><td $color>&nbsp;</td><td $color>&nbsp;</td><td $color>&nbsp;</td><td align='right' $color>&nbsp;</td><td  align='right' $color> - </td>";

	$i=$i+1;
  }
	
 	$registros.=" </table>";
	$reg.=" </table>";
	$regPorConsumo.=" </table>";
	if($detalle2->moneda=="S"){
	$etiquetaTotal="S/. ";
	}else{
	$etiquetaTotal="$ ";
	}
	
	$_SESSION['enletra']=$detalle2->total;
	$_SESSION['enletraMoneda']=$detalle2->moneda;
	

	$us=$objSucursal->consultarxId($detalle2->idsucursal);
	$sucur=$us->fetchObject();
	$descripcionS=$sucur->direccion;
	$celularS=$sucur->telefonofijo;
	$NroDocS=$sucur->ruc;
	
	
	if(strlen($_SESSION['R_NombreSucursal'])>25){
	$nombresucursal=substr($_SESSION['R_NombreSucursal'],0,25);
	}else{
	$nombresucursal=$_SESSION['R_NombreSucursal'];
	}
	
	$nombresucursal=utf8_encode($nombresucursal);
	$registros=utf8_encode($registros);
	
	$vDivNumeroDoc=$detalle2->numero;
	$vDivSerieDoc=$detalle2->numero;
	$vDivDetalleVentaa=$registros;
	$vDivDetalleFondo=$reg;
	$vDivDetalleVentaaPorConsumo=$regPorConsumo;
	
	$vDivNombreSucursal=$nombresucursal;
	$vDivDetalleSucursal=$descripcionS . " - TELF. ".$celularS;
	$vDivRucSucursal="R.U.C. ".$NroDocS;

	//$vDivFechaEmision=substr($detalle2->fecha,0,2)."&nbsp;&nbsp;&nbsp;".substr($detalle2->fecha,3,2)."&nbsp;&nbsp;&nbsp;".substr($detalle2->fecha,6,4);
	$vDivFechaEmision=substr($detalle2->fecha,0,10);
	
	$vDivRUC="&nbsp;".$detallePersona->nrodoc;
	$vdivNombreCliente=$detallePersona->nombres;
	$vDivFP="&nbsp;&nbsp;&nbsp;&nbsp;".$FP;
	$vDivSubTotal=$etiquetaTotal."".$detalle2->subtotal."";
	$vDivIgv=$etiquetaTotal."".$detalle2->igv."";
	$vDivTotall=$etiquetaTotal.$detalle2->total."";
	$vDivNumeroCuotas=$NC."&nbsp;&nbsp;&nbsp;&nbsp;";
	$vDivIniciall=$Inicial;
	$vDivGuiaRemision="-----------";
	$vDivDireccionCliente="&nbsp;&nbsp;".$detallePersona->direccion;
?>
<script>
function imprimir(que) {
	var ventana = window.open("", '', '');
	var contenido = "<html><body onload='window.print();<?php if(strstr($_SERVER['HTTP_USER_AGENT'],'Chrome')){?><?php }else{?>window.close();<?php }//echo $_SERVER['HTTP_USER_AGENT'];?>'><div style='font-family:calibri;font-weight: bold;'>";
	contenido = contenido + document.getElementById(que).innerHTML + "</div></body></html>";
	ventana.document.open();
	ventana.document.write(contenido);
	ventana.document.close();
}
function muestraDetalle(){
	if(document.getElementById('chkPorConsumo').checked){
		document.getElementById('DivDetalleVentaa').innerHTML=document.getElementById('DivDetalleVentaaPorConsumo').innerHTML;
	}else{
		document.getElementById('DivDetalleVentaa').innerHTML=document.getElementById('DivDetalleVentaaDetallado').innerHTML;
	}
}
</script>
</head>
<body  style="font:Arial, Helvetica, sans-serif;">
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <?php

require("../vista/numAletras.php");
$aaa='';
$aaa=num2letras($_SESSION['enletra'], false,false,$_SESSION['enletraMoneda']);
?>
<input type="hidden" name="numLetra" value="">
<div id='cabeceraSucursal' style="position:absolute; left: 280px; top: 50px; width: 700px; height: 495px; z-index:50">
<br>
<br>
<table width="100%">
<tr>

<td width="60%" align="center"><span class="Estilo2"><div id="DivNombreSucursal"><?php echo $vDivNombreSucursal;?></div></span></td>
<td width="40%" align="center"><br>
  <span class="Estilo2"><div id="DivRucSucursal"><?php echo $vDivRucSucursal;?></div></span></td>
</tr>
</table>
<br><br><br><br>
<table width="60%">
<tr>
<td width="100%" align="center"><span class="Estilo4">
  <div id="DivDetalleSucursal"><?php echo $vDivDetalleSucursal;?></div></span></td>
</tr>
</table>
</div>
<div id='cabecera' style="position:absolute; left: 280px; top: 60px; width: 700px; height: 495px; z-index:1"   >
<br>
<br>
 <img src="img/comprobante/cabeceraFactura.PNG" width="700px" height="119px">
</div>
<div id='cabecera2' style="position:absolute; left: 540px; top: 55px; width: 700px; height: 495px; z-index:4"   >
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

 <!--<img src="../imagenes/cabeceraFacturaAbajo.PNG">-->
</div>

 
 <div id='factura' style="position:absolute; left: 280px; top: 125px; width: 710px; height: 495px; z-index:3"   >
   <br><br>
   <table width="715" style="position:absolute">
	<tr><td width="400"></td>
	  <td><div style="font-size:12px" id="DivNumeroDoc" align="left"><?php //echo $vDivNumeroDoc;?></div></td>
	</tr>
	<tr height="80">
		<td>&nbsp;</td>
		<td valign="bottom"><div style='font-size:12px;' id="DivFechaEmision" align="right"><b><?php echo substr($vDivFechaEmision,0,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,3,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,9,1)."&nbsp;&nbsp;";?></b></div></td>
	</tr>	
	<tr>
		<td>&nbsp;</td>
		<td><div id="DivGuiaRemision"></div></td>
	</tr>
</table>
<br>
<p style="margin:20;"></p>
<br><br>
   <table width="410" >
     <tr>
       <td width="85">&nbsp;</td>
       <td align="left"><div style='font-size:12px;' id="DivNombreCliente"><b><?php echo $vdivNombreCliente;?></b></div></td>
       
     </tr>
     <tr>
       <td width="79">&nbsp;</td>
       <td align="right"><div style='font-size:12px;' id="DivDireccionCliente" align="left"><b><?php echo $vDivDireccionCliente;?></b></div></td>
     </tr>
     <tr>
       <td width="79">&nbsp;</td>
       <td align="right"><div style='font-size:12px;' id="DivRUC" align="left"><b><?php echo $vDivRUC;?></b></div></td>
     </tr>
   </table>
   <!--<table width="430">
     <tr>
       <td width="59">&nbsp;</td>
       <td width="98" align="right"><div style='font-size:12px;' align="left">
         <div id="DivFP"></div>
       </div></td>
       <td width="73"><strong>
         <div align="right" class="Estilo1" style='font-size:12px;'></div>
       </strong></td>
       <td width="41"><div align="center">
         <div style='font-size:12px;' id="DivNumeroCuotas"></div>
       </div></td>
       <td width="68"><strong>
         <div align="right" class="Estilo1" style='font-size:12px;'></div>
       </strong></td>
       <td width="63"><div align="center">
         <div style='font-size:12px;' id="DivIniciall"></div>
       </div></td>
     </tr>
   </table>-->
   <div id='DivDetalleVentaa'><?php echo $vDivDetalleVentaa;?></div>
   <div id='DivDetalleVentaaDetallado' style="display:none"><?php echo $vDivDetalleVentaa;?></div>
   <div id='DivDetalleVentaaPorConsumo' style="display:none"><?php echo $vDivDetalleVentaaPorConsumo;?></div>
    <p style="margin:8;"></p>
   <table width="710" >
   <tr align="left" height=20px>
	  <td width="410"><div style='font-size:12px;'  align="<?php if(strlen($aaa)>48){ $espacioadelante=""; $espacios="&nbsp;&nbsp;&nbsp;&nbsp;"; echo "left";} else{ $espacioadelante="&nbsp;&nbsp;&nbsp;&nbsp;"; $espacios=""; echo "left";}?>" id="DivTotalEnLetras"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $espacioadelante."".$aaa."".$espacios;?></b></div></td>
       <td width="100"><div style='font-size:12px;'  align="right"></div></td>
       <td width="150" align="right"><div style='font-size:12px;' id="DivSubTotal"><b><?php echo $vDivSubTotal;?></b></div></td>
     </tr>
	 <tr align="left" height=20px>
       <td colspan="2"><div style='font-size:12px;' align="right"></div></td>
       <td width="150" align="right"><div style='font-size:12px;' id="DivIgv"><b><?php echo $vDivIgv;?></b></div></td>
     </tr>
     <tr align="left" height=20px>
       <td colspan="2">
       <div style='font-size:12px;' align="right"><b><?php echo substr($vDivFechaEmision,0,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,3,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,9,1)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";?></b></div>
       </td>
       <td width="150" align="right"><div style='font-size:12px;' id="DivTotall"><b><?php echo $vDivTotall;?></b></div></td>
     </tr>
   </table>
</div>
 <p align="center">&nbsp;</p>
  <p align="center">&nbsp;</p>
   <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
  <p align="center">&nbsp;</p>
   <p align="center">&nbsp;</p>
      <p align="center">&nbsp;</p>
    <p align="center" style="width:700px"><label><input type="checkbox" id="chkPorConsumo" name="chkPorConsumo" onChange="javascript: muestraDetalle()">Por Consumo</label></p>

 <p align="center" style="width:700px"><a href="javascript:imprimir('factura')" class="Estilo16">IMPRIMIR FACTURA </a> </p>
 <p align="center" style="width:700px"><a href="#" onClick="javascript: setRun('vista/listVenta','&id_clase=44','frame','carga','imgloading');" class="Estilo16"> IR A DOCUMENTOS DE VENTA </a> </p>
 <p align="center" style="width:700px"><a href="#" onClick="javascript: setRun('vista/listPedido','&id_clase=46','frame','carga','imgloading');" class="Estilo16"> IR A PEDIDOS </a> </p>
 <div id='fondoFactura' style="position:absolute; left: 280px; top: 53px; width: 700px; height: 495px; z-index:1 " >
   <p>&nbsp;</p><p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <br>
   <table width="260" align="right" class="botonera">

	<tr ><td width="4" class="frameTL"></td>
    <td class="frameTC"></td><td class="frameTC"></td><td width="5" class="frameTR"></td>
    </tr>
	<tr align="left">
		<td height="24" class="frameCL"></td>
		<td width="116" align="right" class="frameC"><strong>
		  <div style='font-size:12px;' id="DivEtiquetaTotal" align="left">Fecha Emisi&oacute;n: </div>
	  </strong></td>
	  <td width="110" align="right" class="frameC"></td>
		
      <td class="frameCR"></td></tr>
	<tr align="left">
		<td height="30" class="frameCL"></td>
		<td width="116" align="right" class="frameC"><strong>
		  <div style='font-size:12px;' id="DivEtiquetaTotal" align="left">G. Remisi&oacute;n: </div>
	  </strong></td>
	  <td width="110" align="right" class="frameC"></td>
		
    <td class="frameCR"></td></tr>
	<tr align="left">
		
		
    <td class="frameLR"></td></tr>
	
    <tr><td height="2" class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBC"></td><td class="frameBR"></td></tr>
	
  </table>
   <table width="400"  class="botonera">
    <tr ><td width="-2" class="frameTL"></td>
    <td class="frameTC"></td><td width="10" class="frameTR"></td>
    </tr>
    <tr>
    <td class="frameCL"></td>
       <td class="frameC" width="400" style='font-size:12px;'><strong>Cliente:</strong></td>
       
     <td class="frameCR"></td></tr>
    <tr><td height="2" class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBR"></td></tr>
</table>
   <table width="400"  class="botonera">
    <tr ><td width="-2" class="frameTL"></td>
    <td class="frameTC"></td><td width="10" class="frameTR"></td>
    </tr>
    <tr>
    <td class="frameCL"></td>
       <td width="400" class="frameC" style='font-size:12px;'><strong>Direcci&oacute;n:</strong></td>
       <td class="frameCR"></td></tr>
    <tr><td height="2" class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBR"></td></tr>
</table>
   <table width="400"  class="botonera">
    <tr ><td width="4" class="frameTL"></td>
    <td class="frameTC"></td><td width="10" class="frameTR"></td>
    </tr>
    <tr>
    <td class="frameCL"></td>
       <td width="400" class="frameC" style='font-size:12px;'><strong>R.U.C. N&deg;:</strong></td>
       <td class="frameCR"></td></tr>
    <tr><td height="2" class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBR"></td></tr>
</table>
   <!--<table width="460"  class="botonera">
    <tr ><td width="-2" class="frameTL"></td>
    <td class="frameTC"></td><td width="10" class="frameTR"></td>
    </tr>
    <tr>
    <td class="frameCL"></td>
       <td width="418" class="frameC" style='font-size:12px;'><strong>T.PAGO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&deg; CUOTAS: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INICIAL:</strong></td>
       <td class="frameCR"></td></tr>
    <tr><td height="2" class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBR"></td></tr>
</table>
-->
   <div id='DivDetalleFondo'><?php echo $vDivDetalleFondo;?></div>
   <br>
   <table width="410"  class="botonera" align="left">
    <tr ><td width="-2" class="frameTL"></td>
    <td class="frameTC"></td><td  class="frameTR"></td>
    </tr>
    <tr>
    <td class="frameCL"></td>
       <td class="frameC" width="400" style='font-size:12px;'><strong>Son:</strong></td>
       
       <td class="frameCR"></td></tr>
    <tr><td class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBR"></td></tr>
    <tr><td></td></tr>
    <tr><td align="center" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CANCELADO</td></tr>
    <tr><td align="right" colspan="3">Fecha,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;de&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;del 201&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
</table>


   <table width="240"  align="right" class="botonera">
     <tr bgcolor="#FFFFFF" >
       <td width="4"  ></td>
       <td ></td>
       <td width="150"  ></td>
       <td width="4"  c></td>
     </tr>
     <tr >
       <td class="frameTL"></td>
       <td class="frameTC"></td>
       <td class="frameTC"></td>
       <td  class="frameTR"></td>
     </tr>
     <tr align="left">
       <td height="20"  class="frameCL"></td>
       <td  align="right" class="frameC"><strong>
         <div style='font-size:12px;' id="div" align="left">SUB TOTAL : </div>
       </strong></td>
       <td  align="right" class="frameC"></td>
       <td class="frameCR"></td>
     </tr>
	  <tr align="left">
       <td height="20" class="frameCL"></td>
       <td  align="right" class="frameC"><strong>
         <div style='font-size:12px;' id="div" align="left">I.G.V. <?php echo $_SESSION['R_IGV'];?>%  : </div>
       </strong></td>
       <td align="right" class="frameC"></td>
       <td class="frameCR"></td>
     </tr>
	 
<tr align="left">
       <td height="21" class="frameCL"></td>
       <td  align="right" class="frameC"><strong>
         <div style='font-size:12px;' id="div" align="left">TOTAL : </div>
       </strong></td>
       <td  align="right" class="frameC"></td>
       <td class="frameCR"></td>
     </tr>
     <tr align="left">
       <td class="frameLR"></td>
     </tr>
     <tr>
       <td height="2" class="frameBL"></td>
       <td class="frameBC"></td>
       <td class="frameBC"></td>
       <td class="frameBR"></td>
     </tr>
   </table>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
 </div>
</body>
</html>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 