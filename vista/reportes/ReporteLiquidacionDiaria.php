<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsProducto.php');
//VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
//Nombre y Codigo de la Clase a Ejecutar
$clase = "MovCaja";
$id_clase = "53";
if(isset($_POST['txtTituloREPORTE']) and $_POST['txtTituloREPORTE']<>''){
	$titulo = $_POST['txtTituloREPORTE'];
}else{
	$titulo = "Liquidacion de Ingresos";
}
if(isset($_POST['txtFuncionREPORTE']) and $_POST['txtFuncionREPORTE']<>''){
	$funcion = $_POST['txtFuncionREPORTE'];
}else{
	$funcion = 'LiquidacionDiaria';
}
if(isset($_POST['txtOcultarCamposREPORTE'])){
	$ocultarcampos=split('-',$_POST['txtOcultarCamposREPORTE']);
}else{
	$ocultarcampos='';
}
$objProducto = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");
//Nro de Hoja a mostrar YA VALIDADO en la Grilla
$nro_hoja = $_POST['txtNroHojaREPORTE'];
//Nro de Registros a mostrar en la Grilla
$nro_reg=$_POST['txtNroRegistrosTotalREPORTE'];
//Filtro Ya validado de Grilla
//$filtro = str_replace("\'" ,"'", $_POST['txtFiltroREPORTE']);

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//Inicio Obtiene Campos a mostrar
/*$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "Error al obtener campos a mostrar".$rstCampos."";
	exit();
}*/
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

$CAMPOS3[] = array("descripcion"=>"conceptopago");
$CABECERA3[]= "Concepto";
$CAMPOS3[] = array("descripcion"=>"total");
$CABECERA3[]= "Total";
/*foreach($dataCampos as $value){
	if($ocultarcampos!=''){
		if(!in_array(strtolower($value['descripcion']),$ocultarcampos)){
			$CABECERA[] = cambiaHTML($value['comentario']);
			$CAMPOS[] = $value;
			$w[] = umill($value['longitudreporte']);
			$a[] = umill($value['alineacionreporte']);
		}
	}else{
		$CABECERA[] = cambiaHTML($value['comentario']);
		$CAMPOS[] = $value;
		$w[] = umill($value['longitudreporte']);
		$a[] = umill($value['alineacionreporte']);
	}
}*/
//Fin
//Inicio Ejecutando la consulta
eval("\$rst = \$objGrilla->consultar".$clase.$funcion."('".$_GET["fecha"]."');");
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}
/*if($nro_reg==0){
	echo "Sin Informaci&oacute;n";
	exit();
}*/
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
$pdf->SetFont("courier",'B',15);
$pdf->Cell(70,5,"INGRESOS: ",0,1,'C',false);
$pdf->Ln();
//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA);
//CREAMOS LA TABLA
$pdf->LlenarTabla_Cabecera();
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."('".$_GET["fecha"]."');");
//$pdf->LlenarTabla_Datos($rst2,$CAMPOS);
while($data=$rst2->fetchObject()){
    $platos[$data->idproducto]=array("productos"=>$data->productos,"cantidad"=>$data->cantidad,"preciounitario"=>$data->preciounitario,"preciototal"=>$data->preciototal);
}
$fill=true;
foreach($platos as $k=>$v){
    $venta = array_values($v);
    $pdf->Row($venta,$fill,13);
    $fill=!$fill;//para manejo de colores    
}

//$pdf->ln();
while($dato=$rst->fetchObject()){
    $resumen["productos"]="";
    $resumen["cantidad"]="";
    $resumen["preciounitario"]="TOTAL";
    $resumen["preciototal"]=number_format($dato->preciototal+$resumen["preciototal"],2,'.','');   
}
$resumen = array_values($resumen);
$pdf->Row($resumen,true,13);
$pdf->ln();

//$dataCampos = array("Concepto"=>"Concepto","Total"=>"Total");
$rst3 = $objGrilla->consultarLiquidacionEgresosGeneral($idcierre,$objGrilla->penultimocierre());
if($rst3->rowCount()>0){
    $pdf->SetFont("courier",'B',15);
    $pdf->Cell(70,5,"EGRESOS: ",0,1,'C',false);
    $fill=true;
    $pdf->SetFont("courier",'B',13);
    while($dato3=$rst3->fetchObject()){
        $temp["conceptopago"]=$dato3->conceptopago;
        $temp["total"]=$dato3->total;
        $temp=array_values($temp);
        $pdf->Row($temp,$fill,$pdf->tam_letra_grilla);
        $fill=!$fill;
        $temp="";
        
        $resumen3["conceptopago"]="TOTAL";
        $resumen3["total"]=number_format($resumen3["total"]+$dato3->total,2,'.','');
    }
    //$rst3 = $objGrilla->consultarLiquidacionEgresos();
    //$pdf->LlenarTabla_Datos($rst3,$CAMPOS3);
    $resumen3 = array_values($resumen3);
    //$pdf->ln();
    $pdf->Row($resumen3,true,$pdf->tam_letra_grilla);
    $pdf->ln();
}else{
    $pdf->SetFont("courier",'B',15);
    $pdf->Cell(70,5,"EGRESOS: ",0,1,'C',false);
    $resumen3[1]=number_format('0.00',2,'.','');
    $pdf->ln();    
}

$resumen4["conceptopago"]="TOTAL INGRESOS";
$resumen4["total"]=$resumen[3];
$resumen4 = array_values($resumen4);

$resumen5["conceptopago"]="TOTAL EGRESOS";
$resumen5["total"]=$resumen3[1];
$resumen5 = array_values($resumen5);

$resumen6["conceptopago"]="SALDO";
$resumen6["total"]=number_format($resumen4[1]-$resumen5[1],2,'.','');
$resumen6 = array_values($resumen6);


$idcierre = $objGrilla->consultarultimocierre();


$saldos = $objGrilla->montodecierreLiquidacionGeneral($idcierre,$objGrilla->penultimocierre());

$datosaldo=$saldos->fetchObject();

$resumen7["conceptopago"]="TARJETA VISA";
$resumen7["total"]=number_format($datosaldo->montovisa,2);
$resumen7 = array_values($resumen7);

$resumen8["conceptopago"]="TARJETA MASTER CARD";
$resumen8["total"]=number_format($datosaldo->montomaster,2);
$resumen8 = array_values($resumen8);

$resumen9["conceptopago"]="EFECTIVO";
$resumen9["total"]=number_format($resumen6[1]-$resumen7[1]-$resumen8[1],2);
$resumen9 = array_values($resumen9);

$pdf->SetFont("courier",'B',15);
$pdf->Cell(70,5,"RESUMEN: ",0,1,'C',false);

$pdf->SetFont("courier",'B',10);
$pdf->Row($resumen4,false,$pdf->tam_letra_grilla);
$pdf->Row($resumen5,false,$pdf->tam_letra_grilla);
$pdf->Row($resumen6,true,$pdf->tam_letra_grilla);
$pdf->Row($resumen7,false,$pdf->tam_letra_grilla);
$pdf->Row($resumen8,true,$pdf->tam_letra_grilla);
$pdf->Row($resumen9,true,$pdf->tam_letra_grilla);

$pdf->AddPage("L");
$w2 = array("30"=>20,"20"=>45,"15"=>45,"15"=>0,"0"=>0);
$a2 = array("C"=>"C","C"=>"C","C"=>"C","C"=>"C","C"=>"C");
//LLenar Anchos de Columna
$pdf->SetWidths($w2);
//LLenar Alineacion de Columna
$pdf->SetAligns($a2);

$CAMPOS2[] = array("descripcion"=>"productos");
$CABECERA2[]= "Productos";
$CAMPOS2[] = array("descripcion"=>"stock");
$CABECERA2[]= "Stock";
$CAMPOS2[] = array("descripcion"=>"ingresos");
$CABECERA2[]= "Ingresos";
$CAMPOS2[] = array("descripcion"=>"egresos");
$CABECERA2[]= "Egresos";
$CAMPOS2[] = array("descripcion"=>"stock final");
$CABECERA2[]= "Stock Final";

//$pdf->Row($CABECERA2,true,$pdf->tam_letra_grilla);
//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA2);
//CREAMOS LA TABLA
$pdf->LlenarTabla_Cabecera();
$resproductos = $objProducto->consultarProductoReporteStock(20,1,1,1,"","",0,0,"","","S");
while($list=$resproductos->fetchObject()){
    $res= $objGrilla->consultarStockApertura($list->idproducto);
    $te4 = $objGrilla->consultarStockCierreGeneral($list->idproducto,0,$idcierre);
    $temp4 = $te4->fetchObject();
    if($res->rowCount()>0){
        $prod = $res->fetchObject();
        $stockactual = $prod->saldoanteriorbase;
    }else{
        $stockactual = 0.00;
    }
    $stock[$list->idproducto]=array("productos"=>utf8_decode($list->descripcion),"stock"=>$stockactual,"ingresos"=>number_format($temp4->saldoactual+$platos[$list->idproducto]["cantidad"]-$stockactual,0,'.',''),"egresos"=>$platos[$list->idproducto]["cantidad"],"stock final"=>$temp4->saldoactual);
}
//print_R($stock);
//$pdf->ln();
$fill=true;
foreach($stock as $k2=>$v2){
    $temp2 = array_values($v2);
    $pdf->Row($temp2,$fill,13);
    //print_r($temp2);
    $fill=!$fill;//para manejo de colores    
}
//$pdf->LlenarTabla_DatosArray($stock,$CAMPOS2);
$pdf->Output($clase.'.pdf','I');
?>