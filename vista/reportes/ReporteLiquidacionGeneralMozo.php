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
	$titulo = "Liquidacion de Mozo";
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
eval("\$rst = \$objGrilla->consultarCierres".$funcion."('".$_GET["fechainicio"]."','".$_GET["fechafin"]."','".$_GET["idsucursal"]."');");
//eval("\$rst = \$objGrilla->consultar".$clase.$funcion."('".$_GET["fecha"]."');");
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
$pdf->SetTamLetraGrilla(10);
$pdf->SetTamLetraCabecera(11);
//LLenar Anchos de Columna
$pdf->SetWidths($w);
//LLenar Alineacion de Columna
$pdf->SetAligns($a);
//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA);

if($fechaanterior!=$datosGeneral->fecha || $c==1){
        $turno="DIA";$c=2;
        $fechaanterior=$datosGeneral->fecha;
    }else{
        $turno="NOCHE";$c=1;
    }
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."Mozo('".$datosGeneral->idmovimiento."','".$datares->idmovimiento."','".$_GET["idsucursal"]."');");
//$pdf->LlenarTabla_Datos($rst2,$CAMPOS);
while($data=$rst2->fetchObject()){
    $pdf->SetFont("courier",'B',12);
    $pdf->Cell(0,5,"MOZO: ".$data->responsable."  TURNO: ".$turno."  FECHA: ".$datosGeneral->fecha,0,1,'C',false);
    $pdf->Ln();
    eval("\$rst3=\$objGrilla->consultarGeneralProductoxMozo(".$data->idresponsable.",".$data->idsucursalresponsable.",'".$datosGeneral->idmovimiento."','".$datares->idmovimiento."','".$_GET["idsucursal"]."');");
    while($datos=$rst3->fetchObject()){
        $platos[$datos->idproducto]=array("productos"=>utf8_decode($datos->producto),"cantidad"=>$datos->cantidad,"preciounitario"=>$datos->precioventa,"preciototal"=>$datos->total);
        $total = $total + $datos->total;
    }
    //CREAMOS LA TABLA
    $pdf->LlenarTabla_Cabecera();
    $fill=false;
    foreach($platos as $k=>$v){
        $venta = array_values($v);
        $pdf->Row($venta,$fill,10);
        //$fill=!$fill;//para manejo de colores    
    }
    $resumen["productos"]="";
    $resumen["cantidad"]="";
    $resumen["preciounitario"]="TOTAL";
    $resumen["preciototal"]=number_format($total,2,'.','');   
    $resumen = array_values($resumen);
    $pdf->Row($resumen,false,13);
    $pdf->ln();    
    $resumen="";
    $platos="";
    $total="";
    $rst3 ="";
    $pdf->Ln();
}
}
$pdf->Output($clase.'.pdf','I');
?>