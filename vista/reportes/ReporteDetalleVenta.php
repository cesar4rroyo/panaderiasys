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
$dataCampos = array("Productos"=>"Productos","Cantidad"=>"Cantidad","preciounitario"=>"preciounitario","preciototal"=>"preciototal");
$w = array("30"=>"30","20"=>"20","15"=>"15","15"=>"15");
$a = array("C"=>"C","C"=>"C","C"=>"C","C"=>"C");
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
    $sql.=" and T.idcaja in (".$_GET["idcaja"].")";
}
$sql .= " group by p.descripcion,p.idcategoria ,dma.precioventa,p.kardex,p.idproducto,c.descripcion order by p.idcategoria,p.descripcion,p.kardex asc";
$rst=$objGrilla->obtenerDataSQL($sql);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}

// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
$pdf=new PDF_Dinamico('L','mm','A4');
$pdf->Open();
$title='Reporte '.$titulo;
//Primera página
$pdf->AddPage();
//Cantidad de Paginas Existentes LO LLAMA EN EL FOOTER ---> {nb}
$pdf->AliasNbPages();
//LLENAMOS LAS VARIABLES
$pdf->SetTamLetraGrilla(12);
$pdf->SetTamLetraCabecera(13);
//LLenar Anchos de Columna
$pdf->SetWidths($w);
//LLenar Alineacion de Columna
$pdf->SetAligns($a);
$pdf->SetFont("courier",'B',14);
$pdf->Cell(200,5,"INGRESOS DEL ".$_GET["fechainicio"]." AL ".$_GET["fechafin"],0,1,'C',false);
$pdf->Ln();
//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA);
//CREAMOS LA TABLA
$pdf->LlenarTabla_Cabecera();
//$pdf->LlenarTabla_Datos($rst2,$CAMPOS);

while($data=$rst->fetchObject()){
    if(isset($platos[$data->idproducto.'-'.$data->preciounitario])){
        $platos[$data->idproducto.'-'.$data->preciounitario]["cantidad"]=$platos[$data->idproducto]["cantidad"]+$data->cantidad;
        $resumen["productos"]="";
        $resumen["cantidad"]="";
        $resumen["preciounitario"]="TOTAL";
        $resumen["preciototal"]=number_format($data->preciototal+$resumen["preciototal"],2,'.','');
    }else{
        $platos[$data->idproducto.'-'.$data->preciounitario]=array("productos"=>utf8_decode($data->productos),"cantidad"=>$data->cantidad,"preciounitario"=>$data->preciounitario,"preciototal"=>$data->preciototal);
        $resumen["productos"]="";
        $resumen["cantidad"]="";
        $resumen["preciounitario"]="TOTAL";
        $resumen["preciototal"]=number_format($data->preciototal+$resumen["preciototal"],2,'.','');           
    }
}
$fill=true;
if(count($platos)>0){
    foreach($platos as $k=>$v){
        $venta = array_values($v);
        $pdf->Row($venta,$fill,13);
        $fill=!$fill;//para manejo de colores    
}
}
if(count($resumen)>0){
    $resumen = array_values($resumen);
    $pdf->Row($resumen,true,13);
}
$pdf->Output($clase.'.pdf','I');
?>