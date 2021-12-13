<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsProducto.php');
//VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
//Nombre y Codigo de la Clase a Ejecutar
$clase = "MovCaja";
$id_clase = "53";
if($_GET["idcaja"]=="0"){
    $titulo = "Detalle Venta General - Todos";
}else{
    $titulo = "Detalle Venta General - Caja ".$_GET["idcaja"];
}
$ocultarcampos='';
$objProducto = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");
//Filtro Ya validado de Grilla
//$filtro = str_replace("\'" ,"'", $_POST['txtFiltroREPORTE']);

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//Inicio Obtiene Campos a mostrar

//Inicio Ejecutando la consulta
$sql = "select T.fecha,T.numero,T.idtipodocumento,T.idcaja,T.total,(select nombres||' '||apellidos from personamaestro where idpersonamaestro=p1.idpersonamaestro) as cliente,(select nombres||' '||apellidos from personamaestro where idpersonamaestro=(select idpersonamaestro from persona where idpersona=(select idresponsable from movimientohoy where idmovimiento=ds.idpedido and idsucursal=ds.idsucursal))) as mozo,us.nombreusuario as usuario,p.descripcion as producto,dma.cantidad,dma.precioventa,p.kardex,p.idproducto,c.descripcion as categoria,(select X.montotarjeta from (select * from movimientohoy union all select * from movimiento) X where X.idmovimientoref=T.idmovimiento) as montotarjeta,ds.situacion,ds.fecha as fechaentrega,T.numerotarjeta,T.totalpagado,T.comentario
from (select * from movimientohoy union select * from movimiento) as T
inner join detallemovalmacen dma on dma.idmovimiento=T.idmovimiento and T.idsucursal=dma.idsucursal
inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
left join categoria as c on c.idcategoria=p.idcategoria and p.idsucursal=c.idsucursal
inner join persona p1 on p1.idpersona=T.idpersona 
inner join detallestock ds on ds.idventa=T.idmovimiento and ds.idsucursal=T.idsucursal
left join usuario us on us.idusuario=ds.idusuario
where T.estado='N' and T.idsucursal=".$_SESSION["R_IdSucursal"]." and T.fecha>='".$_GET["fechainicio"]."' and T.fecha<='".$_GET["fechafin"]."'";
if($_GET["idcategoria"]!=""){
    $sql.=" and c.idcategoria=".$_GET["idcategoria"];
}
if($_GET["idcaja"]!="0"){
    $sql.=" and T.idcaja=".$_GET["idcaja"];
}
$sql .= " order by T.fecha,T.numero,p.idcategoria,p.descripcion,p.kardex asc";
$rst=$objGrilla->obtenerDataSQL($sql);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Reporte $titulo.xls\"");
$registro="<table border='1'><tr><th colspan='20'>";
$title='Reporte '.$titulo;
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='20'>";
$subtitle="DETALLE DE VENTA DEL ".$_GET["fechainicio"]." AL ".$_GET["fechafin"];
$registro.=$subtitle."</th></tr><tr>";
$registro.="<th align='center'>FECHA</th>";
$registro.="<th align='center'>TIPO DOC.</th>";
$registro.="<th align='center'>NRO.</th>";
$registro.="<th align='center'>MOZO</th>";
$registro.="<th align='center'>CLIENTE</th>";
$registro.="<th align='center'>TOTAL</th>";
$registro.="<th align='center'>EFECTIVO</th>";
$registro.="<th align='center'>VISA</th>";
$registro.="<th align='center'>MASTER</th>";
$registro.="<th align='center'>LOTE VISA</th>";
$registro.="<th align='center'>LOTE MASTER</th>";
$registro.="<th align='center'>CAJA</th>";
$registro.="<th align='center'>CANTIDAD</th>";
$registro.="<th align='center'>PRODUCTO</th>";
$registro.="<th align='center'>P. VENTA</th>";
$registro.="<th align='center'>SUBTOTAL</th>";
$registro.="<th align='center'>SITUAC. DESC.</th>";
$registro.="<th align='center'>FECHA DESC.</th>";
$registro.="<th align='center'>USUARIO DESC.</th>";
$registro.="</tr>";
while($dato=$rst->fetchObject()){
    $registro.="<tr>";
    $registro.="<td>".$dato->fecha."</td>";
    if($dato->idtipodocumento=="4"){
        $tipodocumento="Boleta";
    }elseif($dato->idtipodocumento=="5"){
        $tipodocumento="Factura";
    }else{
        $tipodocumento="Ticket";
    }
    $registro.="<td>".$tipodocumento."</td>";
    $registro.="<td>".$dato->numero."</td>";
    $registro.="<td>".$dato->mozo."</td>";
    $registro.="<td>".$dato->cliente."</td>";
    $registro.="<td>".number_format($dato->total,2,'.','')."</td>";
    $registro.="<td>".number_format($dato->totalpagado,2,'.','')."</td>";
    $monto=explode("|",$dato->montotarjeta);
    $visa=explode("@", $monto[0]);
    $master=explode("@", $monto[1]);
    $lvisa=explode("@",$dato->comentario);
    $registro.="<td>".number_format($visa[1],2,'.','')."</td>";
    $registro.="<td>".number_format($master[1],2,'.','')."</td>";
    $registro.="<td>".$lvisa[1]."</td>";
    $registro.="<td>".$dato->numerotarjeta."</td>";
    $registro.="<td>CAJA ".$dato->idcaja."</td>";
    $registro.="<td>".number_format($dato->cantidad,2,'.','')."</td>";
    $registro.="<td>".$dato->producto."</td>";
    $registro.="<td>".number_format($dato->precioventa,2,'.','')."</td>";
    $registro.="<td>".number_format($dato->cantidad*$dato->precioventa,2,'.','')."</td>";
    $registro.="<td>".($dato->situacion=='P'?'Pendiente':'Confirmado')."</td>";
    $registro.="<td>".$dato->fechaentrega."</td>";
    $registro.="<td>".$dato->usuario."</td>";
    $registro.="</tr>";
}
$registro.="</tr></table>";

echo $registro;
?>