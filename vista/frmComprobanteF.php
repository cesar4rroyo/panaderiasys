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

$reg.="<table width='' style='margin:2;' style='font-size:12px;'>";
$reg.="<tr>
		<th width='80' align='center' bgcolor='#CCCCCC'>CANT.<strong></strong></th>
		<th width='80' align='center' bgcolor='#CCCCCC'><strong>UNID.</strong></th>
        <th width='850' align='center' bgcolor='#CCCCCC'><strong>DESCRIPCION</strong></th>
		<th width='100' align='center' bgcolor='#CCCCCC'><strong>P.UNIT.</strong></th>
		<th width='200' align='center' bgcolor='#CCCCCC'><strong>TOTAL</strong></th>
    </tr>";

	$registros.="<table width='710' style='margin:2;' style='font-size:12px;'>";

	$registros.="<tr height=20px>
		<td width='80' align='center'></td>
		<td width='80' align='center'><strong></strong></td>
        <td width='850' align='center'><strong></strong></td>
		<td width='100' align='center'><strong></strong></td>
		<td width='200' align='center'><strong></strong></td>
    </tr>";
	
	$regPorConsumo=$registros;
	$i=0;
$detalle=$objMovimiento->buscarDetalleProducto($idventa,"S");
//print_r($detalle);
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
function imprimir(idventa) {
/*var ventana = window.open("", '', '');
var contenido = "<html><body onload='window.print();<?php if(strstr($_SERVER['HTTP_USER_AGENT'],'Chrome')){?><?php }else{?>window.close();<?php }//echo $_SERVER['HTTP_USER_AGENT'];?>'><div style='font-family:calibri;font-weight: bold;'>";
contenido = contenido + document.getElementById(que).innerHTML + "</div></body></html>";
ventana.document.open();
ventana.document.write(contenido);
ventana.document.close();*/
    if(document.getElementById('chkPorConsumo').checked){
        g_ajaxPagina.setParameter("consumo","S");    
    }else{
        g_ajaxPagina.setParameter("consumo","N");
    }
    if(document.getElementById('chkPorGlosa').checked){
        g_ajaxPagina.setParameter("glosa",document.getElementById("txtGlosa").value);    
    }else{
        g_ajaxPagina.setParameter("glosa","");
    }
    g_ajaxPagina.setURL("http://localhost/lasmusas/vista/ajaxPedido.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "imprimir_ventaelectronica");
	g_ajaxPagina.setParameter("idventa",idventa);
	g_ajaxPagina.response = function(text){
		//alert("imprimiendo");			
	};
	g_ajaxPagina.request();
}
function muestraDetalle(){
	if(document.getElementById('chkPorConsumo').checked){
		document.getElementById('DivDetalleVentaa').innerHTML=document.getElementById('DivDetalleVentaaPorConsumo').innerHTML;
	}else{
		document.getElementById('DivDetalleVentaa').innerHTML=document.getElementById('DivDetalleVentaaDetallado').innerHTML;
	}
}
function muestraDetalle2(){
	if(document.getElementById('chkPorGlosa').checked){
		document.getElementById('DivDetalleVentaa').innerHTML="<textarea id='txtGlosa' name='txtGlosa'></textarea>";
	}else{
		document.getElementById('DivDetalleVentaa').innerHTML=document.getElementById('DivDetalleVentaaDetallado').innerHTML;
	}
}
</script>
</head>
<body  style="font:Arial, Helvetica, sans-serif;">

 <?php

require("../vista/numAletras.php");
$aaa='';
$aaa=num2letras($_SESSION['enletra'], false,false,$_SESSION['enletraMoneda']);
?>
<input type="hidden" name="numLetra" value="">
<table>
<tr>
<td>
<p align="center" style="width:700px"><input type="checkbox" id="chkPorConsumo" name="chkPorConsumo" onChange="javascript: muestraDetalle()"><label for="chkPorConsumo">Por Consumo</label></p>
<p align="center" style="width:700px"><input type="checkbox" id="chkPorGlosa" name="chkPorGlosa" onChange="javascript: muestraDetalle2()"><label for="chkPorGlosa">Por Glosa</label></p>
 <p align="center" style="width:700px"><a href="javascript:imprimir('<?php echo $idventa;?>')" class="Estilo16">IMPRIMIR FACTURA </a> </p>
 <p align="center" style="width:700px"><a href="#" onClick="javascript: setRun('vista/listVenta','&id_clase=44','frame','carga','imgloading');" class="Estilo16"> IR A DOCUMENTOS DE VENTA </a> </p>
 <p align="center" style="width:700px"><a href="#" onClick="javascript: setRun('vista/listPedido','&id_clase=46','frame','carga','imgloading');" class="Estilo16"> IR A PEDIDOS </a> </p>
 </td>
<td >
<div id='cabeceraSucursal'>

<table width="100%">
<tr>
<td width="60%" align="center"><span class="Estilo2"><div id="DivNombreSucursal"><?php echo $vDivNombreSucursal;?></div></span></td>
<td width="40%" align="center"><br>
  <span class="Estilo2"><div id="DivRucSucursal"><?php echo $vDivRucSucursal;?></div></span></td>
</tr>
</table>
<table width="60%">
<tr>
<td width="100%" align="center"><span class="Estilo4">
  <div id="DivDetalleSucursal"><?php echo $vDivDetalleSucursal;?></div></span></td>
</tr>
</table>
</div>

 <div id='factura'>
   <table width="750">
	<tr height="80">
		<td>&nbsp;</td>
		<td valign="middle"><div style='font-size:12px;' id="DivFechaEmision" align="right"><b><?php echo substr($vDivFechaEmision,0,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,3,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,8,2)."";?></b></div></td>
	</tr>	
</table>
   <table width="520">
     <tr height="30">
       <td width="85">&nbsp;</td>
       <td align="left"><div style='font-size:12px;' id="DivNombreCliente"><b><?php echo $vdivNombreCliente;?></b></div></td>
       
     </tr>
     <tr>
       <td width="79">&nbsp;</td>
       <td align="right"><div style='font-size:12px;' id="DivDireccionCliente" align="left"><b><?php echo $vDivDireccionCliente;?></b></div></td><td width="20" align="right"><div style='font-size:12px;' id="DivRUC" align="left"><b><?php echo $vDivRUC;?></b></div></td>
     </tr>
   </table>
   <div id='DivDetalleVentaa'><?php echo $vDivDetalleVentaa;?></div>
   <div id='DivDetalleVentaaDetallado' style="display:none"><?php echo $vDivDetalleVentaa;?></div>
   <div id='DivDetalleVentaaPorConsumo' style="display:none"><?php echo $vDivDetalleVentaaPorConsumo;?></div>
   <table width="710" border="0">
   <tr align="left" height=20px>
	  <td width="410"><div style='font-size:12px;'  align="<?php if(strlen($aaa)>48){ $espacioadelante=""; $espacios="&nbsp;&nbsp;&nbsp;&nbsp;"; echo "left";} else{ $espacioadelante="&nbsp;&nbsp;&nbsp;&nbsp;"; $espacios=""; echo "left";}?>" id="DivTotalEnLetras"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $espacioadelante."".$aaa."".$espacios;?></b></div></td>
       <td width="100"><div style='font-size:12px;'  align="right"></div></td>
       <td width="165" align="right"><div style='font-size:12px;' id="DivSubTotal"><b><?php echo $vDivSubTotal;?></b></div></td>
     </tr>
	 <tr align="left" height=25px>
       <td colspan="2"><div style='font-size:12px;' align="center"><b><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,0,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,3,2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substr($vDivFechaEmision,9,1)."";?></b></div></td>
       <td width="165" align="right"><div style='font-size:12px;' id="DivIgv"><b><?php echo $vDivIgv;?></b></div></td>
     </tr>
     <tr align="left" height=20px>
       <td colspan="2">
       <div style='font-size:12px;' align="center"></div>
       </td>
       <td width="165" align="right"><div style='font-size:12px;' id="DivTotall"><b><?php echo $vDivTotall;?></b></div></td>
     </tr>
   </table>
</div>
 </td>
 </tr>
 </table>
</body>
</html>