<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsProducto.php');
//VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
//Nombre y Codigo de la Clase a Ejecutar
$clase = "MovCaja";
$id_clase = "53";
if($_GET["idcaja"]=="0"){
    $titulo = "Detalle Venta - Todos";
}else{
    $titulo = "Detalle Venta - Caja ".$_GET["idcaja"];
}
$ocultarcampos='';
$objProducto = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");
//Filtro Ya validado de Grilla
//$filtro = str_replace("\'" ,"'", $_POST['txtFiltroREPORTE']);

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//Inicio Obtiene Campos a mostrar
//CREAMOS LA CABECERA A MOTRAR EN LA TABLA
$CABECERA = array();
$CABECERA3 = array();
$CAMPOS = array();
$CAMPOS3 = array();
$dataCampos = array("Categoria"=>"Categoria","Productos"=>"Productos","Cantidad"=>"Cantidad","preciounitario"=>"preciounitario","preciototal"=>"preciototal");
$w = array("20"=>"20","30"=>"30","20"=>"20","15"=>"15","15"=>"15");
$a = array("C"=>"C","C"=>"C","C"=>"C","C"=>"C");
$CAMPOS[] = array("descripcion"=>"Categoria");
$CABECERA[]= "Categoria";
$CAMPOS[] = array("descripcion"=>"Productos");
$CABECERA[]= "Productos";
$CAMPOS[] = array("descripcion"=>"Cantidad");
$CABECERA[]= "Cantidad";
$CAMPOS[] = array("descripcion"=>"preciounitario");
$CABECERA[]= "P. Unit.";
$CAMPOS[] = array("descripcion"=>"preciototal");
$CABECERA[]= "P. Total";

//Inicio Ejecutando la consulta
$sql = "select p.descripcion as productos,sum(dma.cantidad) as cantidad,dma.precioventa as preciounitario,round(sum(dma.precioventa*dma.cantidad),2) as preciototal,p.kardex,p.idproducto,c.descripcion as categoria
from (select * from movimientohoy union select * from movimiento) as T
inner join (select * from detallemovimientohoy union select * from detallemovimiento) as D on D.idmovimiento=T.idmovimiento and D.idsucursal=T.idsucursal
inner join detallemovalmacen dma on dma.idmovimiento=T.idmovimiento and D.iddetallemovalmacen=dma.iddetallemovalmacen and dma.idsucursal=T.idsucursal
inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
left join categoria as c on c.idcategoria=p.idcategoria and p.idsucursal=c.idsucursal
where T.estado='N' and T.idsucursal=".$_SESSION["R_IdSucursal"]." and T.fecha>='".$_GET["fechainicio"]."' and T.fecha<='".$_GET["fechafin"]."'";
if($_GET["idcategoria"]!=""){
    $sql.=" and c.idcategoria=".$_GET["idcategoria"];
}
if($_GET["idcaja"]!="0"){
    $sql.=" and T.idcaja=".$_GET["idcaja"];
}
$sql .= " group by p.descripcion,p.idcategoria ,dma.precioventa,p.kardex,p.idproducto,c.descripcion order by p.idcategoria,p.descripcion,p.kardex asc";
$rst=$objGrilla->obtenerDataSQL($sql);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}

$sql1 = "select sum(T.total-T.subtotal) as pagoanticipado from (select * from movimiento union all select * from movimientohoy) as T where T.fecha>='".$_GET["fechainicio"]."' and T.fecha<='".$_GET["fechafin"]."' and T.tipoventa='A'";
$rst1 = $objGrilla->obtenerDataSQL($sql1);
$pagoanticipado = $rst1->fetchObject()->pagoanticipado;
// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Reporte $titulo.xls\"");
$registro="<table border='1'><tr><th colspan='".count($CABECERA)."'>";
$title='Reporte '.$titulo;
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='".count($CABECERA)."'>";
$subtitle="INGRESOS DEL ".$_GET["fechainicio"]." AL ".$_GET["fechafin"];
$registro.=$subtitle."</th></tr><tr>";
for($i=0;$i<count($CABECERA);$i++){
    if($a[$i]=="C") $a[$i]="center";
    if($a[$i]=="L") $a[$i]="left";
    if($a[$i]=="R") $a[$i]="right";
    $registro.="<th align='".$a[$i]."'>".$CABECERA[$i]."</th>";
}
$registro.="</tr>";
$total = 0;
while($data=$rst->fetchObject()){
    if(isset($platos[$data->idproducto.'-'.$data->preciounitario])){
        $platos[$data->idproducto.'-'.$data->preciounitario]["cantidad"]=$platos[$data->idproducto]["cantidad"]+$data->cantidad;
        $resumen["categoria"]="";
        $resumen["productos"]="";
        $resumen["cantidad"]="";
        $resumen["preciounitario"]="TOTAL";
        $resumen["preciototal"]=number_format($data->preciototal+$resumen["preciototal"],2,'.','');
    }else{
        $platos[$data->idproducto.'-'.$data->preciounitario]=array("categoria"=>utf8_decode($data->categoria),"productos"=>utf8_decode($data->productos),"cantidad"=>$data->cantidad,"preciounitario"=>$data->preciounitario,"preciototal"=>$data->preciototal);
        $resumen["productos"]="";
        $resumen["cantidad"]="";
        $resumen["preciounitario"]="TOTAL";
        $resumen["preciototal"]=number_format($data->preciototal+$resumen["preciototal"],2,'.','');           
    }
}

foreach($platos as $dato){
    $registro.="<tr>";
    foreach($CAMPOS as $value){
        if($a[$cont]=="C") $a[$cont]="center";
        if($a[$cont]=="L") $a[$cont]="left";
        if($a[$cont]=="R") $a[$cont]="right";
        $registro.="<td align='".$a[$cont]."'>".($dato[strtolower($value['descripcion'])])."</td>";
        $cont++;
    }
    $registro.="</tr>";
}

$resumen=array_values($resumen);
$registro.="<tr>";
for($i=0;$i<count($resumen);$i++){
    if($a[$i]=="C") $a[$i]="center";
    if($a[$i]=="L") $a[$i]="left";
    if($a[$i]=="R") $a[$i]="right";
    $registro.="<th align='".$a[$i]."'>".$resumen[$i]."</th>";
    $total = $resumen[$i];
}
$registro.="<tr><th></th><th></th><th>Pago Anticipado</th><th>$pagoanticipado</th></tr>";
$registro.="<tr><th></th><th></th><th>Total Final</th><th>".($total + $pagoanticipado)."</th></tr>";
$registro.="</tr></table>";


echo $registro;
?>