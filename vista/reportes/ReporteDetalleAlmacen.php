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
header("Content-Disposition: attachment; filename=\"Reporte Detalle Almacen.xls\"");
$registro="<table border='1'><thead><tr><th colspan='8'>";
$title='Reporte Detalle Almacen del '.$_GET["fechainicio"]." al ".$_GET["fechafin"];
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='8'>";
$registro.="</th></tr><tr>";
$registro.="<th align='center'>NRO</th>";
$registro.="<th align='center'>FECHA</th>";
$registro.="<th align='center'>PERSONA</th>";
$registro.="<th align='center'>COMENTARIO</th>";
$registro.="<th align='center'>CANT.</th>";
$registro.="<th align='center'>PRODUCTO</th>";
$registro.="<th align='center'>PRECIO COMPRA</th>";
$registro.="<th align='center'>PRECIO VENTA</th>";
$registro.="<th align='center'>SUBTOTAL</th>";
$registro.="</tr></thead>";
$sql="select dma.cantidad,dma.precioventa,dma.preciocompra,p.descripcion as producto,T.fecha,T.numero,T.idtipodocumento,pm.nombres,pm.apellidos,T.comentario,lu.preciocompra as preciocompra2
	from detallemovalmacen dma
	inner join (select * from movimiento union all select * from movimientohoy) as T on T.idmovimiento=dma.idmovimiento and T.idsucursal=dma.IdSucursal
	inner join producto p on p.idproducto=dma.idproducto and dma.idsucursalproducto=p.idsucursal
	inner join persona pe on pe.idpersona=T.idpersona and pe.idsucursal=T.idsucursalpersona
	inner join listaunidad lu on lu.idproducto=p.idproducto and lu.idunidadbase=p.idunidadbase and lu.idunidad=lu.idunidadbase and lu.idsucursal=p.idsucursal and lu.idsucursalproducto=p.idsucursal
	inner join personamaestro pm on pm.idpersonamaestro=pe.idpersonamaestro
	where T.idtipomovimiento=3 and T.estado='N' and fecha>='".$_GET["fechainicio"]." 00:00:00' and fecha<='".$_GET["fechafin"]." 23:59:59' and T.idsucursal=".$_SESSION["R_IdSucursal"];
if($_GET["idtipodocumento"]!="0"){
	$sql.=" and T.idtipodocumento=".$_GET["idtipodocumento"];
}
if($_GET["persona"]!=""){
	$sql.=" and concat(pm.nombres,' ',pm.apellidos) like '%".$_GET["persona"]."%'";
}
$sql.=" order by T.idmovimiento,T.fecha desc";
$rst = $objProducto->obtenerDataSQL($sql);
while($dat=$rst->fetchObject()){
	$registro.="<tr>";
	$registro.="<td>".$dat->numero."</td>";
	$registro.="<td>".date("d/m/Y H:i:s",strtotime($dat->fecha))."</td>";
	$registro.="<td>".$dat->nombres." ".$dat->apellidos."</td>";
	$registro.="<td>".$dat->comentario."</td>";
	$registro.="<td>".$dat->cantidad."</td>";
	$registro.="<td>".$dat->producto."</td>";
	if($_SESSION["R_IdSucursal"]=="14"){
		$registro.="<td>".$dat->preciocompra2."</td>";
		$registro.="<td>".$dat->precioventa."</td>";
		$registro.="<td>".$dat->cantidad*$dat->preciocompra2."</td>";
	}else{
		$registro.="<td>".$dat->preciocompra2."</td>";
		$registro.="<td>".$dat->precioventa."</td>";
		$registro.="<td>".$dat->cantidad*$dat->precioventa."</td>";
	}
	$registro.="</tr>";
}
echo $registro;
?>