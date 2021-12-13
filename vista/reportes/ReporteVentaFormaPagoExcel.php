<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsProducto.php');
//VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
//Nombre y Codigo de la Clase a Ejecutar
$clase = "MovCaja";
$id_clase = "53";
$titulo = "Ventas Forma Pago";
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
$dataCampos = array("Fecha"=>"Fecha","Nro"=>"Nro", "Tipo_documento"=>"Tipo Documento" ,"Cliente"=>"Cliente","Total"=>"Total","Efectivo"=>"Efectivo","Visa"=>"Visa","Master"=>"Master","Deposito"=>"Deposito");
$w = array("1"=>"55","2"=>"25","3"=>"55","4"=>"25","5"=>"25","6"=>"25","7"=>"25","8"=>"25");
$a = array("C"=>"C","C"=>"C","C"=>"C","C"=>"C","C"=>"C","C"=>"C","C"=>"C");
$CAMPOS[] = array("descripcion"=>"fecha");
$CABECERA[]= "Fecha";
$CAMPOS[] = array("descripcion"=>"numero");
$CABECERA[]= "Nro";
$CAMPOS[] = array("descripcion"=>"tipodocumento");
$CABECERA[]= "Tipo Doc";
$CAMPOS[] = array("descripcion"=>"cliente");
$CABECERA[]= "Cliente";
$CAMPOS[] = array("descripcion"=>"total");
$CABECERA[]= "Total";
$CAMPOS[] = array("descripcion"=>"efectivo");
$CABECERA[]= "Efectivo";
$CAMPOS[] = array("descripcion"=>"visa");
$CABECERA[]= "Visa";
$CAMPOS[] = array("descripcion"=>"master");
$CABECERA[]= "Master";
$CAMPOS[] = array("descripcion"=>"deposito");
$CABECERA[]= "Deposito";

//Inicio Ejecutando la consulta
$sql = "select mv.numero,mv.fecha,td.descripcion as tipodocumento,mv.total,T.totalpagado,
CASE WHEN T.modopago = 'T' AND T.idtipotarjeta=2 THEN T.total-T.totalpagado WHEN T.modopago = 'A' THEN (substr(T.montotarjeta,position('2@' in T.montotarjeta)+2,length(T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric ELSE 0 END as visa,
CASE WHEN T.modopago = 'T' AND T.idtipotarjeta=1 THEN T.total-T.totalpagado WHEN T.modopago = 'A' THEN (substr(T.montotarjeta,position('1@' in T.montotarjeta)+2,position('|' in T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric ELSE 0 END as master,
case when T.modopago = 'D' then T.total else 0 end as deposito,pm.apellidos|| ' ' ||pm.nombres as cliente       
from (select * from movimientohoy union select * from movimiento) as mv
inner join (select * from movimiento union all select * from movimientohoy) as T on mv.idmovimiento=T.idmovimientoref and mv.idsucursal=T.idsucursalref
inner join persona p on p.idpersona=mv.idpersona and p.idsucursal=mv.idsucursalpersona
inner join personamaestro pm on pm.idpersonamaestro=p.idpersonamaestro
inner join tipodocumento td on td.idtipodocumento=mv.idtipodocumento
where mv.estado='N' and mv.idsucursal=".$_SESSION["R_IdSucursal"]." and mv.fecha>='".$_GET["fechainicio"]."' and mv.fecha<='".$_GET["fechafin"]."' and mv.idtipomovimiento=2";
$sql .= " order by T.fecha asc";//echo $sql;die();
$rst=$objGrilla->obtenerDataSQL($sql);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Reporte $titulo.xls\"");
$registro="<table border='1'><tr><th colspan='".count($CABECERA)."'>";
$title='Reporte '.$titulo;
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='".count($CABECERA)."'>";
$subtitle="VENTAS DEL ".$_GET["fechainicio"]." AL ".$_GET["fechafin"];
$registro.=$subtitle."</th></tr><tr>";
for($i=0;$i<count($CABECERA);$i++){
    if($a[$i]=="C") $a[$i]="center";
    if($a[$i]=="L") $a[$i]="left";
    if($a[$i]=="R") $a[$i]="right";
    $registro.="<th align='".$a[$i]."'>".$CABECERA[$i]."</th>";
}
$registro.="</tr>";

//print_r($lista);
while($data=$rst->fetchObject()){
    $platos[]=array("fecha"=>utf8_decode($data->fecha),"numero"=>$data->numero,"tipodocumento"=>$data->tipodocumento,"cliente"=>$data->cliente,"total"=>$data->total,"efectivo"=>$data->totalpagado,"visa"=>$data->visa,"master"=>$data->master,"deposito"=>$data->deposito);
    $resumen["fecha"]="";
    $resumen["numero"]="";
    $resumen["tipodocumento"]="";
    $resumen["cliente"]="TOTAL";
    $resumen["total"]=number_format($data->total+$resumen["total"],2,'.','');
    $resumen["efectivo"]=number_format($data->totalpagado+$resumen["efectivo"],2,'.','');
    $resumen["visa"]=number_format($data->visa+$resumen["visa"],2,'.','');
    $resumen["master"]=number_format($data->master+$resumen["master"],2,'.','');
    $resumen["deposito"]=number_format($data->deposito+$resumen["deposito"],2,'.','');
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
}
$registro.="</tr></table>";

echo $registro;
?>