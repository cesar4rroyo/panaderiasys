<?php
session_start();
?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
g_ajaxGrabar = new AW.HTTP.Request;
function imprimir(idventa) {
/*var ventana = window.open("", '', '');
var contenido = "<html><body onload='window.print();<?php if(strstr($_SERVER['HTTP_USER_AGENT'],'Chrome')){?><?php }else{?>window.close();<?php }//echo $_SERVER['HTTP_USER_AGENT'];?>'><div style='font-family:calibri;font-weight: bold;'>";
contenido = contenido + document.getElementById(que).innerHTML + "</div></body></html>";
ventana.document.open();
ventana.document.write(contenido);
ventana.document.close();*/
        g_ajaxPagina.setURL("http://localhost/lasmusas/vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
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
		g_ajaxPagina.setParameter("accion", "imprimir_ventaelectronica");
		g_ajaxPagina.setParameter("idventa",idventa);
		g_ajaxPagina.response = function(text){
			//alert("imprimiendo");			
		};
		g_ajaxPagina.request();
}
function muestraDetalle(){
	if(document.getElementById('chkPorConsumo').checked){
		document.getElementById('DivDetalleVenta').innerHTML=document.getElementById('DivDetalleVentaPorConsumo').innerHTML;
	}else{
		document.getElementById('DivDetalleVenta').innerHTML=document.getElementById('DivDetalleVentaDetallado').innerHTML;
	}
}

function muestraDetalle2(){
	if(document.getElementById('chkPorGlosa').checked){
		document.getElementById('DivDetalleVenta').innerHTML="<textarea id='txtGlosa' name='txtGlosa'></textarea>";
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

$rst=$objMovimiento->consultarMovimientoComprobante(1,1,'2',1, $idventa, 2, '');//print_R($rst);
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
$detalle=$objMovimiento->buscarDetalleProducto($idventa,'historico');
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
		$descripcion2=substr($descripcion,0,40);
	}
    $registros.="<td align='left' $color style='font-size:12px;'><b>".utf8_decode($descripcion)."</b> </td>
    <td align='right' $color style='font-size:12px;'><b>".$dato->precioventa."&nbsp;&nbsp;</b></td>
    <td align='right' $color style='font-size:12px;'><b>".number_format($dato->precioventa*$dato->cantidad,2)."&nbsp;&nbsp;&nbsp; </b></td>
  </tr>";

	$lineasdescripcion=(strlen($descripcion)/40+0.5)*12;
	$reg.="<tr><td style='font-size:".$lineasdescripcion."px;'>&nbsp;</td><td></td><td></td><td></td></tr>";
  	
	if($i==0){
		$regPorConsumo.="<tr style='font-size:12px;'><td $color>&nbsp;</td><td align='left' $color><b>POR CONSUMO</b></td><td align='right' $color><b>".$detalle2->total."</b>&nbsp;&nbsp;</td><td align='right' $color><b>".$detalle2->total."</b>&nbsp;&nbsp;</td>";
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
	
	//print_r($detalle2);
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
<table>
<tr>
<td>
<p align="center" style="width:650px;"><input type="checkbox" id="chkPorConsumo" name="chkPorConsumo" onChange="javascript: muestraDetalle()"><label for="chkPorConsumo">Por Consumo</label></p>
<p align="center" style="width:650px"><input type="checkbox" id="chkPorGlosa" name="chkPorGlosa" onChange="javascript: muestraDetalle2()"><label for="chkPorGlosa">Por Glosa</label></p>
<p align="center" style="width:650px"><a href="javascript:imprimir('<?php echo $idventa;?>')" class="Estilo16">IMPRIMIR BOLETA </a> </p>
<p align="center" style="width:650px"><a href="#" onClick="javascript: setRun('vista/listVenta','&id_clase=44','frame','carga','imgloading');" class="Estilo16"> IR A DOCUMENTOS DE VENTA </a> </p>
<p align="center" style="width:650px"><a href="#" onClick="javascript: setRun('vista/listPedido','&id_clase=46','frame','carga','imgloading');" class="Estilo16"> IR A PEDIDOS </a> </p>
<td>

<div id='boletaSucursal'>
  <table width="100%">
  	<tr>
		<td  align="center"><br>
	  <span class="Estilo3">
	  <div id="DivNombreSucursal"><?php echo $vDivNombreSucursal;?></div></span></td>
      <td width="33%"><strong><div id="DivRucSucursal" align="center">&nbsp;&nbsp;&nbsp;R.U.C. N° <?php echo $vDivRucSucursal;?></div></strong></td>
	</tr>
  </table>
  <table width="100%">
  <tr>
  	<td width="67%"><span class="Estilo5">
  	  <div id="DivDetalleSucursal" align="center"><?php echo $vDivDetalleSucursal;?></div></span></td><td></td>
  </tr>
  </table>
  
 </div>
<div id='boleta'> 
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
</div>

<?php
if(strstr($_SERVER['HTTP_USER_AGENT'],'IE')){
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
}else{?>
<?php
}//echo $_SERVER['HTTP_USER_AGENT'];
?>

</td>
</tr>
</table>
</body>
</html>