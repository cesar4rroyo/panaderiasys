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
header("Content-Disposition: attachment; filename=\"Reporte Diario Compras.xls\"");
$registro="<table border='1'><thead><tr><th colspan='4'>";
$title='Reporte Diario Compras del '.$_GET["fechainicio"]." al ".$_GET["fechafin"];
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='4'>";
$registro.="</th></tr><tr>";
$registro.="<th align='center'>FECHA</th>";
$registro.="<th align='center'>TIPO DOC.</th>";
$registro.="<th align='center'>PERSONA</th>";
$registro.="<th align='center'>TOTAL</th>";
$registro.="</tr></thead>";
$sql="select T.idtipodocumento,cast(T.fecha as date) as fecha,sum(T.total) as total,T.motivo,pm.apellidos,pm.nombres,td.descripcion as tipodocumento
	FROM (select * from movimiento union all select * from movimientohoy) as T
	inner join persona pe on pe.idpersona=T.idpersona and pe.idsucursal=T.idsucursalpersona
	inner join personamaestro pm on pm.idpersonamaestro=pe.idpersonamaestro
	inner join tipodocumento td on td.idtipodocumento=T.idtipodocumento
	where T.idtipomovimiento=1 and T.estado='N' and fecha>='".$_GET["fechainicio"]." 00:00:00' and fecha<='".$_GET["fechafin"]." 23:59:59' and T.idsucursal=".$_SESSION["R_IdSucursal"];
if($_GET["idtipodocumento"]!="0"){
	$sql.=" and T.idtipodocumento=".$_GET["idtipodocumento"];
}
if($_GET["persona"]!=""){
	$sql.=" and concat(pm.nombres,' ',pm.apellidos) like '%".$_GET["persona"]."%'";
}
$sql.=" group by cast(T.fecha as date),T.idtipodocumento,T.motivo,pm.apellidos,pm.nombres,td.descripcion order by cast(T.fecha as date) asc";
$rst = $objProducto->obtenerDataSQL($sql);
while($dat=$rst->fetchObject()){
	$registro.="<tr>";
	$registro.="<td>".date("d/m/Y",strtotime($dat->fecha))."</td>";
	$registro.="<td>".($dat->tipodocumento)."</td>";
	$registro.="<td>".$dat->apellidos." ".$dat->nombres."</td>";
	$registro.="<td>".$dat->total."</td>";
	$registro.="</tr>";
}
echo $registro;
?>