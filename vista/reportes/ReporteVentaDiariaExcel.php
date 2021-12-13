<?php 
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsProducto.php');
//VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
//Nombre y Codigo de la Clase a Ejecutar
$clase = "MovCaja";
$id_clase = "53";
$titulo = "Ventas Diaria";
$ocultarcampos='';
$objProducto = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");
//Filtro Ya validado de Grilla
//$filtro = str_replace("\'" ,"'", $_POST['txtFiltroREPORTE']);

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//Inicio Obtiene Campos a mostrar
//CREAMOS LA CABECERA A MOTRAR EN LA TABLA

//Inicio Ejecutando la consulta
$sql = "select cast(mv.fecha as date) as fecha,sum(T.total) as total,sum(T.totalpagado) as totalpagado,
sum(CASE WHEN T.modopago = 'T' AND T.idtipotarjeta=2 THEN T.total-T.totalpagado WHEN T.modopago = 'A' THEN (substr(T.montotarjeta,position('2@' in T.montotarjeta)+2,length(T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric ELSE 0 END) as visa,
sum(CASE WHEN T.modopago = 'T' AND T.idtipotarjeta=1 THEN T.total-T.totalpagado WHEN T.modopago = 'A' THEN (substr(T.montotarjeta,position('1@' in T.montotarjeta)+2,position('|' in T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric ELSE 0 END) as master,
sum(case when T.modopago = 'D' then T.total else 0 end) as deposito
from (select * from movimientohoy union select * from movimiento) as mv
inner join (select * from movimiento union all select * from movimientohoy) as T on mv.idmovimiento=T.idmovimientoref and mv.idsucursal=T.idsucursalref
inner join persona p on p.idpersona=mv.idpersona and p.idsucursal=mv.idsucursalpersona
inner join personamaestro pm on pm.idpersonamaestro=p.idpersonamaestro
inner join tipodocumento td on td.idtipodocumento=mv.idtipodocumento
where mv.estado='N' and mv.idsucursal=".$_SESSION["R_IdSucursal"]." and mv.fecha>='".$_GET["fechainicio"]."' and mv.fecha<='".$_GET["fechafin"]."' and mv.idtipomovimiento=2";
$sql .= " group by cast(mv.fecha as date) order by cast(mv.fecha as date) asc";//echo $sql;die();
$rst=$objGrilla->obtenerDataSQL($sql);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Reporte $titulo.xls\"");
$registro="<table border='1'><tr><th colspan='8'>";
$title='Reporte '.$titulo;
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='8'>";
$subtitle="VENTAS POR DIA DEL ".$_GET["fechainicio"]." AL ".$_GET["fechafin"];
$registro.=$subtitle."</th></tr><tr>";
$registro.="<th align='center' rowspan='2'>FECHA</th>";
$registro.="<th align='center' colspan='6'>VENTAS</th>";
$registro.="<th align='center' rowspan='2'>GASTOS</th>";
$registro.="</tr>";
$registro.="<tr>";
$registro.="<th align='center'>TOTAL</th>";
$registro.="<th align='center'>EFECTIVO</th>";
$registro.="<th align='center'>VISA</th>";
$registro.="<th align='center'>MASTER</th>";
$registro.="<th align='center'>DEPOSITO</th>";
$registro.="<th align='center'>PAGO ANTICIPADO</th>";
$registro.="</tr>";

//print_r($lista);
while($data=$rst->fetchObject()){
    $registro.="<tr>";
    $registro.="<td>$data->fecha</td>";
    $registro.="<td>$data->total</td>";
    $registro.="<td>$data->totalpagado</td>";
    $registro.="<td>$data->visa</td>";
    $registro.="<td>$data->master</td>";
    $registro.="<td>$data->deposito</td>";
    $registro.="<td>".number_format($data->total-$data->totalpagado-$data->visa-$data->master-$data->deposito,2,'.','')."</td>";
    $sql="select (case when sum(T.total) is null then 0 else sum(T.total) end) as gastos
    from (select total,fecha,idconceptopago,idtipodocumento,estado from movimientohoy union all select total,fecha,idconceptopago,idtipodocumento,estado from movimiento) as T
    where T.idconceptopago not in (1,2,3) and T.idtipodocumento=10 and T.estado='N' and T.fecha>='$data->fecha 00:00:00' and T.fecha<='$data->fecha 23:59:59'";
    $gastos = $objGrilla->obtenerDataSQL($sql)->fetchObject()->gastos;
    $registro.="<td>$gastos</td>";
    $registro.="</tr>";
}



echo $registro;
?>