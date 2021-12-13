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
	$funcion = 'LiquidacionGeneral';
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

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");

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
//Fin
//Inicio Ejecutando la consulta
eval("\$rst = \$objGrilla->consultarCierres".$funcion."('".$_GET["fechainicio"]."','".$_GET["fechafin"]."','".$_GET["idsucursal"]."');");
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}
/*if($nro_reg==0){
	echo "Sin Informaci&oacute;n";
	exit();
}*/

// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
$pdf=new PDF_Dinamico('P','mm','A4');
$pdf->Open();
$title='Reporte '.$titulo;
$c=1;
//Recorremos todo el listado de cierres
while($datosGeneral = $rst->fetchObject()){
    $res = $objGrilla->consultarCierreSiguiente($datosGeneral->idmovimiento,$_GET["idsucursal"]);
    if($res->rowCount()==0){ break;}
    $datares = $res->fetchObject();
//Primera página    
$pdf->AddPage();
//Cantidad de Paginas Existentes LO LLAMA EN EL FOOTER ---> {nb}
$pdf->AliasNbPages();
//LLENAMOS LAS VARIABLES
$pdf->SetTamLetraGrilla(12);
$pdf->SetTamLetraCabecera(12);
//LLenar Anchos de Columna
$pdf->SetWidths($w);
//LLenar Alineacion de Columna
$pdf->SetAligns($a);
if($fechaanterior!=$datosGeneral->fecha || $c==1){
        $turno="DIA";$c=2;
        $fechaanterior=$datosGeneral->fecha;
    }else{
        $turno="NOCHE";$c=1;
    }
$pdf->SetFont("courier",'B',16);
$pdf->Cell(200,5,"SUCURSAL :".utf8_decode($_GET["sucursal"])."  TURNO :".$turno,0,1,'C',false);
$pdf->Ln();
$pdf->SetFont("courier",'B',16);
$pdf->Cell(100,5,"INGRESOS DEL ".$datosGeneral->fecha. " :",0,1,'C',false);
$pdf->Ln();
//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA);
//CREAMOS LA TABLA
$pdf->LlenarTabla_Cabecera();
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."('".$datosGeneral->idmovimiento."','".$datares->idmovimiento."','".$_GET["idsucursal"]."');");
eval("\$rest2 = \$objGrilla->consultar".$clase.$funcion."('".$datosGeneral->idmovimiento."','".$datares->idmovimiento."','".$_GET["idsucursal"]."');");
//$pdf->LlenarTabla_Datos($rst2,$CAMPOS);
$platos[0]=array("productos"=>"COMIDA","cantidad"=>1,"preciounitario"=>0,"preciototal"=>0);
$platos["TRAGOS"]=array("productos"=>"TRAGOS","cantidad"=>1,"preciounitario"=>0,"preciototal"=>0);
$platos["CHICHA"]=array("productos"=>"CHICHA","cantidad"=>1,"preciounitario"=>0,"preciototal"=>0);
$platos["LIMONADA"]=array("productos"=>"LIMONADA FROZEN","cantidad"=>0,"preciounitario"=>0,"preciototal"=>0);
while($data=$rst2->fetchObject()){
    if($data->kardex=="S"){
        $platos[$data->idproducto]=array("productos"=>utf8_decode($data->productos),"cantidad"=>$data->cantidad,"preciounitario"=>$data->preciounitario,"preciototal"=>$data->preciototal);
    }else{
        if(strpos($data->categoria, "TRAGO") !== FALSE){            
              $platos[$data->productos]["productos"]=utf8_decode($data->productos);
              $platos[$data->productos]["cantidadl"]=$platos[$data->productos]["cantidad"]+$data->cantidad;
              $platos[$data->productos]["preciounitario"]=$data->preciounitario;
              $platos[$data->productos]["preciototal"]=$platos[$data->productos]["preciototal"]+$data->preciototal;
//            $platos["TRAGOS"]["preciounitario"]=$platos["TRAGOS"]["preciounitario"]+$data->preciototal;
//            $platos["TRAGOS"]["preciototal"]=$platos["TRAGOS"]["preciototal"]+$data->preciototal;
        }else{
            if(strpos($data->productos, "CHICHA") !== FALSE && strpos($data->productos, "CHICHARR") === FALSE){            
                  $platos[$data->productos]["productos"]=utf8_decode($data->productos);
                  $platos[$data->productos]["cantidadl"]=$platos[$data->productos]["cantidad"]+$data->cantidad;
                  $platos[$data->productos]["preciounitario"]=$data->preciounitario;
                  $platos[$data->productos]["preciototal"]=$platos[$data->productos]["preciototal"]+$data->preciototal;
//                $platos["CHICHA"]["preciounitario"]=$platos["CHICHA"]["preciounitario"]+$data->preciototal;
//                $platos["CHICHA"]["preciototal"]=$platos["CHICHA"]["preciototal"]+$data->preciototal;
            }else{
            if(strpos($data->productos, "LIMONADA") !== FALSE ){
                    $platos["LIMONADA"]["preciounitario"]=$platos["LIMONDA"]["preciounitario"]+$data->preciototal;
                    $platos["LIMONADA"]["preciototal"]=$platos["LIMONADA"]["preciototal"]+$data->preciototal;
                    $platos["LIMONADA"]["cantidad"]=$platos["LIMONADA"]["cantidad"]+1;
                }else{
                    $platos[0]["preciounitario"]=$platos[0]["preciounitario"]+$data->preciototal;
                    $platos[0]["preciototal"]=$platos[0]["preciototal"]+$data->preciototal;
                }
            }    
        }  
    }
}

if($platos["TRAGOS"]["preciototal"]==0) unset($platos["TRAGOS"]);
if($platos["CHICHA"]["preciototal"]==0) unset($platos["CHICHA"]);
if($platos["LIMONADA"]["preciototal"]==0) unset($platos["LIMONADA"]);

$fill=true;
foreach($platos as $k=>$v){
    $venta = array_values($v);
    $pdf->Row($venta,$fill,13);
    $fill=!$fill;//para manejo de colores    
}

//$pdf->ln();
while($dato=$rest2->fetchObject()){
    $resumen["productos"]="";
    $resumen["cantidad"]="";
    $resumen["preciounitario"]="TOTAL";
    $resumen["preciototal"]=number_format($dato->preciototal+$resumen["preciototal"],2,'.','');   
}
$resumen = array_values($resumen);
$pdf->Row($resumen,true,13);
$pdf->ln();

//$dataCampos = array("Concepto"=>"Concepto","Total"=>"Total");
$rst3 = $objGrilla->consultarLiquidacionEgresosGeneral($datares->idmovimiento,$datosGeneral->idmovimiento,$_GET["idsucursal"]);
if($rst3->rowCount()>0){
    $pdf->SetFont("courier",'B',16);
    $pdf->Cell(100,5,"EGRESOS DEL ".$datosGeneral->fecha. " :",0,1,'C',false);
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
    $pdf->SetFont("courier",'B',16);
    $pdf->Cell(100,5,"EGRESOS DEL ".$datosGeneral->fecha. " :",0,1,'C',false);
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

    //Para tener el siguiente movimiento y obtener los saldos
    $temp3 = $objGrilla->consultarCierreSiguiente($datares->idmovimiento,$_GET["idsucursal"]);
    if($temp3->rowCount()==0){ 
        $siguientemovimiento=0;
    }else{
        $da=$temp3->fetchObject();
        $siguientemovimiento=$da->idmovimiento;
    }
    
$saldos = $objGrilla->montodecierreLiquidacionGeneral($datares->idmovimiento,$datosGeneral->idmovimiento,$_GET["idsucursal"]);
$datosaldo=$saldos->fetchObject();

$resumen7["conceptopago"]="TARJETA VISA";
$resumen7["total"]=number_format($datosaldo->montocredito + $datosaldo->montodebito,2,'.','');
$resumen7 = array_values($resumen7);

$resumen8["conceptopago"]="EFECTIVO";
$resumen8["total"]=number_format($resumen6[1]-$resumen7[1],2,'.','');
$resumen8 = array_values($resumen8);

$pdf->SetFont("courier",'B',16);
$pdf->Cell(100,5,"RESUMEN DEL ".$datosGeneral->fecha. " :",0,1,'C',false);

$pdf->SetFont("courier",'B',10);
$pdf->Row($resumen4,false,$pdf->tam_letra_grilla);
$pdf->Row($resumen5,false,$pdf->tam_letra_grilla);
$pdf->Row($resumen6,true,$pdf->tam_letra_grilla);
$pdf->Row($resumen7,false,$pdf->tam_letra_grilla);
$pdf->Row($resumen8,true,$pdf->tam_letra_grilla);

$pdf->AddPage("P");
$w2 = array("30"=>20,"20"=>25,"15"=>25,"15"=>0,"0"=>0);
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
$pdf->SetFont("courier",'B',16);
$pdf->Cell(150,5,"SUCURSAL :".utf8_decode($_GET["sucursal"])."  TURNO :".$turno,0,1,'C',false);
$pdf->Ln();
$pdf->SetFont("courier",'B',16);
$pdf->Cell(150,5,"STOCK DEL ".$datosGeneral->fecha,0,1,'C',false);
$pdf->Ln();

//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA2);
//CREAMOS LA TABLA
$pdf->LlenarTabla_Cabecera();
$resproductos = $objProducto->consultarProductoReporteStock(20,1,1,1,"","",0,0,"","","S",$_GET["idsucursal"]);
while($list=$resproductos->fetchObject()){
    $te4 = $objGrilla->consultarStockCierreGeneral($list->idproducto,$datosGeneral->idmovimiento,$datares->idmovimiento,$_GET["idsucursal"]);
    $temp4 = $te4->fetchObject();
    $res= $objGrilla->consultarStockAperturaGeneral($list->idproducto,$datosGeneral->idmovimiento,$datares->idmovimiento,$_GET["idsucursal"]);
    if($res->rowCount()>0){
        $prod = $res->fetchObject();
        $stockactual = $prod->saldoanteriorbase;
    }else{
        $stockactual = 0.00;
    }
    $stock[$list->idproducto]=array("productos"=>utf8_decode($list->descripcion),"stock"=>$stockactual,"ingresos"=>number_format($temp4->saldoactual+$platos[$list->idproducto]["cantidad"]-$stockactual,0,'.',''),"egresos"=>number_format($platos[$list->idproducto]["cantidad"],0,'.',''),"stock final"=>number_format($temp4->saldoactual,0,'.',''));
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

$platos="";
$resumen="";
$resumen2="";
$temp="";
$resumen3="";
$resumen4="";
$resumen5="";
$resumen6="";
$resumen7="";
$resumen8="";
$stock="";
}
//$pdf->LlenarTabla_DatosArray($stock,$CAMPOS2);
$pdf->Output($clase.'.pdf','I');
?>