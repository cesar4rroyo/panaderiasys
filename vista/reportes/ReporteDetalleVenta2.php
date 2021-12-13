<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsProducto.php');
//VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
//Nombre y Codigo de la Clase a Ejecutar
$clase = "MovCaja";
$id_clase = "53";

$ocultarcampos='';
$objProducto = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Reporte Detalle Venta.xls\"");
$registro="<table border='1'><thead><tr><th colspan='9'>";
$title='Reporte Detalle Venta del '.$_GET["fechainicio"]." al ".$_GET["fechafin"];
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='9'>";
$registro.="</th></tr><tr>";
$registro.="<th align='center'>TIPO DOC.</th>";
$registro.="<th align='center'>NRO</th>";
$registro.="<th align='center'>FECHA</th>";
$registro.="<th align='center'>RUC/DNI</th>";
$registro.="<th align='center'>PERSONA</th>";
$registro.="<th align='center'>COMENTARIO</th>";
$registro.="<th align='center'>CANT.</th>";
$registro.="<th align='center'>PRODUCTO</th>";
$registro.="<th align='center'>PRECIO</th>";
$registro.="<th align='center'>SUBTOTAL</th>";
$registro.="</tr></thead>";
$sql="select dma.cantidad,dma.preciocompra,dma.precioventa,p.descripcion as producto,T.fecha,T.numero,T.idtipodocumento,pm.nombres,pm.apellidos,T.comentario,pm.nrodoc,T.estado
	from detallemovalmacen dma
	inner join (select * from movimiento union all select * from movimientohoy) as T on T.idmovimiento=dma.idmovimiento and T.idsucursal=dma.IdSucursal
	inner join producto p on p.idproducto=dma.idproducto and dma.idsucursalproducto=p.idsucursal
	inner join persona pe on pe.idpersona=T.idpersona and pe.idsucursal=T.idsucursalpersona
	inner join personamaestro pm on pm.idpersonamaestro=pe.idpersonamaestro
	where T.idtipomovimiento=2 and fecha>='".$_GET["fechainicio"]."' and fecha<='".$_GET["fechafin"]."' and T.idsucursal=".$_SESSION["R_IdSucursal"];
/*if($_GET["idtipodocumento"]!="0"){
	$sql.=" and T.idtipodocumento=".$_GET["idtipodocumento"];
}*/
if($_GET["persona"]!=""){
	$sql.=" and concat(pm.nombres,' ',pm.apellidos) like '%".$_GET["persona"]."%'";
}
$sql.=" order by T.idmovimiento,T.fecha desc";
$rst = $objProducto->obtenerDataSQL($sql);
while($dat=$rst->fetchObject()){
	$registro.="<tr>";
	$registro.="<td>".($dat->idtipodocumento=="4"?"Boleta Venta":($dat->idtipodocumento=="5"?"Factura Venta":"Ticket de Venta"))."</td>";
	$registro.="<td>".$dat->numero."</td>";
	$registro.="<td>".date("d/m/Y H:i:s",strtotime($dat->fecha))."</td>";
	$registro.="<td>".$dat->nrodoc."</td>";
	$registro.="<td>".$dat->nombres." ".$dat->apellidos."</td>";
	$registro.="<td>".$dat->comentario."</td>";
	$registro.="<td>".$dat->cantidad."</td>";
	$registro.="<td>".$dat->producto."</td>";
	if($dat->estado!="N"){
		$registro.="<td>0.00</td>";
		$registro.="<td>".number_format($dat->cantidad*0,2,'.','')."</td>";
	}else{
		$registro.="<td>".$dat->precioventa."</td>";
		$registro.="<td>".number_format($dat->cantidad*$dat->precioventa,2,'.','')."</td>";
	}
	$registro.="</tr>";
}
echo $registro;
?>