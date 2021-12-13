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
	height: 6px;
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
    height: 6px;
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
.Estilo3 {font-size: 24px}
.Estilo5 {font-size: 12px}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
		document.getElementById('DivDetalleVenta').innerHTML=document.getElementById('DivDetalleVentaPorConsumo').innerHTML;
	}else{
		document.getElementById('DivDetalleVenta').innerHTML=document.getElementById('DivDetalleVentaDetallado').innerHTML;
	}
}
</script>
<?php 
$idventa=$_GET['idventa'];
require('../modelo/clsSucursal.php');
$objSucursal=new clsSucursal(58, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
require('../modelo/clsMovimiento.php');
$objMovimiento=new clsMovimiento(10, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

require('../modelo/clsPersona.php');
$objPersona=new clsPersona(23, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

$rst=$objMovimiento->consultarMovimientoComprobanteCompra(1,1,'2',1, $idventa, 1, '');
$detalle2 = $rst->fetchObject();

$pers=$objPersona->consultarxId($detalle2->idpersona,$detalle2->idsucursalpersona);
$detallePersona = $pers->fetchObject();

$reg.="<table width='528'>";
	$registros.="<table width='528'>";
	
	$reg.="<tr>
		<th width='40' align='center' bgcolor='#CCCCCC' style='font-size:12px;'><strong>Cant.</strong></th>
        <th width='320' align='center' bgcolor='#CCCCCC' style='font-size:12px;'><strong>DESCRIPCION</strong></th>
		<th width='60' align='center' bgcolor='#CCCCCC' style='font-size:12px;'><strong>P. Unit.</strong></th>
		<th width='100' align='center' bgcolor='#CCCCCC' style='font-size:12px;'><strong>IMPORTE</strong></th>
    </tr>";
	
		$registros.="<tr>
		<td width='40' align='center'><strong>&nbsp;</strong></td>
		<td width='320' align='center'><strong></strong></td>
        	<td width='60' align='center'><strong></strong></td>
		<td width='100' align='center'><strong></strong></td>
    </tr>";
	
	$regPorConsumo=$registros;
	$i=0;
$detalle=$objMovimiento->buscarDetalleProducto($idventa);
  while($dato = $detalle->fetchObject()){
  
  
	if(($i+1)%2==0)
		$color=" bgcolor='#CCCCCC'";
	else
		$color=" ";
	$registros.="<tr>
    <td align='center' $color style='font-size:12px;'><b>".$dato->cantidad."</b></td>";
	//$descripcion=$dato->producto.' /'.$dato->categoria.'_'.$dato->marca.'_'.$dato->peso.$dato->unidadpeso."";
	//$descripcion=$dato->producto.' /'.$dato->categoria.'_'.$dato->marca;
	$descripcion=$dato->producto;
	if(strlen($descripcion)>=50){
		$descripcion=substr($descripcion,0,49);
	}
    $registros.="<td align='left' $color style='font-size:12px;'><b>".utf8_decode($descripcion)."</b> </td>
    <td align='right' $color style='font-size:12px;'><b>".$dato->precioventa."&nbsp;&nbsp;</b></td>
    <td align='right' $color style='font-size:12px;'><b>".number_format($dato->precioventa*$dato->cantidad,2)."&nbsp;&nbsp;&nbsp; </b></td>
  </tr>";
  //if($i<4)
    
	$reg.="<tr><td style='font-size:12px;'>&nbsp;</td><td></td><td></td><td></td></tr>";
  	
	if($i==0){
		$regPorConsumo.="<tr style='font-size:12px;'><td $color>&nbsp;</td><td align='left' $color>POR CONSUMO</td><td align='right' $color><b>".$detalle2->total."</b>&nbsp;&nbsp;</td><td align='right' $color><b>".$detalle2->total."</b>&nbsp;&nbsp;</td>";
	}else{
		$regPorConsumo.="<tr style='font-size:12px;'><td $color>&nbsp;</td><td $color>&nbsp;</td><td align='right' $color>&nbsp;</td><td  align='right' $color> - &nbsp;&nbsp;</td>";
	}
	
	$i=$i+1;
  }
  
  while($i<7){
  	if(($i+1)%2==0)
		$color=" bgcolor='#CCCCCC'";
	else
		$color=" ";
		
	$registros.="<tr>
    <td align='center' $color></td>
    <td align='center' $color></td>
    <td align='right' $color>&nbsp;&nbsp; </td>
    <td align='right' $color style='font-size:12px;'> -&nbsp;&nbsp;&nbsp;</td>
  	</tr>";
	//if($i<4)
    $reg.="<tr><td style='font-size:12px;'>&nbsp;</td><td></td><td></td><td></td></tr>";
	
	$regPorConsumo.="<tr>
    <td align='center' $color></td>
    <td align='center' $color></td>
    <td align='right' $color>&nbsp;&nbsp; </td>
    <td align='right' $color style='font-size:12px;'> -&nbsp;&nbsp;&nbsp;</td>
  	</tr>";
	
  	$i=$i+1;
  }
	
 	$registros.=" </table>";
	
	
	$us=$objSucursal->consultarxId($detalle2->idsucursal);
	$sucur=$us->fetchObject();
	$descripcionS=$sucur->direccion;
	$NroDocS=$sucur->ruc;
	$celularS=$sucur->telefonofijo;
	
	if(strlen($_SESSION['R_NombreSucursal'])>25){
	$nombresucursal=substr($_SESSION['R_NombreSucursal'],0,25);
	}else{
	$nombresucursal=$_SESSION['R_NombreSucursal'];
	}
	
	
	$registros=utf8_encode($registros);
	$vDivDetalleVenta=$registros;
	$vDivDetalleVentax=$reg."</table>";
	$vDivDetalleVentaPorConsumo=$regPorConsumo."</table>";
	
	$vDivNombreSucursal=$nombresucursal;
	$vDivDetalleSucursal=$descripcionS." - TELF ".$celularS;
	$vDivRucSucursal=$NroDocS;
	
	$vDivNumDoc=utf8_encode("N&deg; ".$detalle2->numero);
	$vdiaFecha="&nbsp;&nbsp;&nbsp;".substr($detalle2->fecha,0,2);
	$vmesFecha="&nbsp;&nbsp;&nbsp;".substr($detalle2->fecha,3,2);
	$vanioFecha="&nbsp;&nbsp;&nbsp;".substr($detalle2->fecha,6,4);
	
	if($detalle2->idpersona==2){
		$vdivNombreCliente="&nbsp;&nbsp;&nbsp;".$detalle2->nombrespersona;
	}else{
		$vdivNombreCliente="&nbsp;&nbsp;&nbsp;".$detallePersona->nombres;
	}
	$vdivTP=$FP;
	$vDivTotal=$etiquetaMonedal." &nbsp;".$detalle2->total;
	$vDivNCuotas=$NC;
	$vDivInicial="&nbsp;".$Inicial;
	if($detalle2->idpersona!=2){
	$vDivDocIdentidad=$detallePersona->nrodoc."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$vDivDireccionCliente="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$detallePersona->direccion;
	}
?>
</head>
<body bgcolor="#FFFFFF"  style="font:Arial, Helvetica, sans-serif; font-size:12px;">
<div id='ticketSucursal' style="position:absolute; left: 280px; top: 57px; width: 514px; height: 500px; z-index:1;">
 <p> 
<br>
  <table width="100%">
  	<tr>
		<td  align="center"><br>
	  <span class="Estilo3">
	  <div id="DivNombreSucursal"><?php echo $vDivNombreSucursal;?></div></span></td>
      <td width="33%"><strong><div id="DivRucSucursal" align="center">&nbsp;&nbsp;&nbsp;R.U.C. N° <?php echo $vDivRucSucursal;?></div></strong></td>
	</tr>
  </table>
  <br>
  <br>
  <br>
  <br>
  <table width="100%">
  <tr>
  	<td width="67%"><span class="Estilo5">
  	  <div id="DivDetalleSucursal" align="center"><?php echo $vDivDetalleSucursal;?></div></span></td><td></td>
  </tr>
  </table>
  
 </div>
<div id='ticket' style="position:absolute; left: 280px; top: 180px; width: 510px; height: 1px; z-index:2;"> 
  <table width="500" style="position:relative" >
      <tr>
        <td   width="45">&nbsp;</td>
        <td width="30"></td>
        <td width="30"></td>
        <td width="50" ></td>
        <td width="150"></td>
        <td width="190" align="center"><div id="div4" style='font-size:12px;'><?php //echo $vDivNumDoc;?></div></td>
      </tr>
    </table>
	<p  style=" margin:25px;"></p>
  <table width="522" >
      <tr>
        <td width="60">&nbsp;</td>
        <td width="260" align="left" ><div id="divNombreCliente" style='font-size:12px;'><b><?php echo $vdivNombreCliente;?></b></div></td>
        <td width="90">&nbsp;</td>
        <td width="70" align="left"></td><td></td>
        <td></td>
        <td></td>
      </tr>
     <tr>
        <td width="60">&nbsp;</td>
        <td width="260" align="left" ><div id="DivDireccionCliente" style='font-size:12px;' align="left"><b><?php echo $vDivDireccionCliente;?></b></div></td>
        <td width="90">&nbsp;</td>
        <td width="70" align="left"><div id="DivDocIdentidad" style='font-size:12px;'>
          &nbsp;&nbsp;<?php echo $vDivDocIdentidad;?></div></td><td><div id="diaFecha" style='font-size:12px;'><b><?php echo $vdiaFecha;?></b></div></td>
        <td><div id="mesFecha" style='font-size:12px;'><b>&nbsp;&nbsp;<?php echo $vmesFecha;?></b></div></td>
        <td><div id="anioFecha" style='font-size:12px;'><b>&nbsp;&nbsp;<?php echo $vanioFecha;?></b></div></td>
      </tr>
     </table>
  <div id='DivDetalleVenta' style='font-size:12px;'><?php echo $vDivDetalleVenta;?></div>
  <div id='DivDetalleVentaDetallado' style="display:none"><?php echo $vDivDetalleVenta;?></div>
  <div id='DivDetalleVentaPorConsumo' style="display:none"><?php echo $vDivDetalleVentaPorConsumo;?></div>
  <table width="522">
      <tr align="left">
        <td width="393"><div style='font-size:12px;' id="div11" align="right"><strong></strong></div></td>
        <td width="95" align="right"><div style='font-size:12px;' id="DivTotal"><b><?php echo $vDivTotal;?></b></div></td>
      </tr>
    </table>
  <p>&nbsp;</p>
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
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
 <p align="center">&nbsp;</p>
<div id='ticketFondo' style="position:absolute; left: 280px; top: 57px; width: 518px; height: 1px;">
    <br>
    <br>
    <p><img src="img/comprobante/cabeceraticket.PNG" width="518" height="115"></p>

  <table width="518" class="botonera">
    <tr ><td class="frameTL"></td>
    <td class="frameTC"></td><td class="frameTR"></td>
    </tr>
    <tr>
    <td class="frameCL"></td><td class="frameC" width="500" align="left"> &nbsp;SR.(ES):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Doc. Ident.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha</td>
    <td class="frameCR"></td></tr>
    <tr><td class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBR"></td></tr>

    <tr ><td class="frameTL"></td>
    <td class="frameTC"></td><td class="frameTR"></td>
    </tr>
    <tr>
    <td class="frameCL"></td><td class="frameC" width="500" align="left"> &nbsp;DIRECCION:</td>
    <td class="frameCR"></td></tr>
    <tr><td class="frameBL"></td>
    <td class="frameBC" height="2"></td><td class="frameBR"></td></tr>
	
</table>
  <div id='DivDetalleVentax' style='font-size:12px;'><?php echo $vDivDetalleVentax;?></div>
  <table width="190" align="right" class="botonera">
    <tr ><td width="7" class="frameTL"></td>
    <td class="frameTC"></td><td class="frameTC"></td><td width="11" class="frameTR"></td>
    </tr>
	<tr align="left">
		<td class="frameCL"></td>
		<td width="55" align="right" class="frameC"><strong>
		  <div style='font-size:12px;' id="DivEtiquetaTotal" align="right">TOTAL:</div>
	  </strong></td>
      	<td width="87" class="frameC"></td>
    <td class="frameCR"></td></tr>
    <tr><td height="2" class="frameBL"></td>
    <td class="frameBC"></td><td class="frameBC"></td><td class="frameBR"></td></tr>
	
  </table>
 
  <p>&nbsp;</p>
  <p>&nbsp;</p>
</div>
<?php
if(strstr($_SERVER['HTTP_USER_AGENT'],'IE')){
?>
<?php
}else{?>
  <p>&nbsp;</p>
    <p>&nbsp;</p>
      <p>&nbsp;</p>
        <p>&nbsp;</p>
          <p>&nbsp;</p>
            <p>&nbsp;</p>
<?php
}//echo $_SERVER['HTTP_USER_AGENT'];
?>
<p align="center" style="width:650px"><label><input type="checkbox" id="chkPorConsumo" name="chkPorConsumo" onChange="javascript: muestraDetalle()">Por Consumo</label></p>
<!--p align="center" style="width:650px"><a href="javascript:imprimir('ticket')" class="Estilo16">IMPRIMIR</a> </p-->
<p align="center" style="width:650px"><a href="#" onClick="javascript: setRun('vista/listCompra','&id_clase=65','frame','carga','imgloading');" class="Estilo16"> IR A DOCUMENTOS DE COMPRA </a> </p>
</html>