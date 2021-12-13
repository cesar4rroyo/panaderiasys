<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsTipoDocumento.php');

//--------------------------->CLASE TIPO DOCUMENTO<---------------------------
$clase_tipodocumento = 'TipoDocumento';
$id_clase_tipodocumento = 33;
//Instancia la ClaseTipoDocumento
eval("\$objTipoDocumento = new cls".$clase_tipodocumento."(".$id_clase_tipodocumento.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//>>Inicio Obtiene Campos a mostrar
$TIPODOCUMENTO_rstCampos = $objTipoDocumento->obtenerCamposMostrar("G");
if(is_string($TIPODOCUMENTO_rstCampos)){
	exit();
}
$TIPODOCUMENTO_dataCampos = $TIPODOCUMENTO_rstCampos->fetchAll();
//<<Fin

//>>Inicio Ejecutando la consultA DE TipoDocumento
eval("\$TIPODOCUMENTO_rst = \$objTipoDocumento->consultar".$clase_tipodocumento."(0,0,'IdTipoDocumento',1,0,\"\",\""."reporte"."\");");
//$rst = $objTipoDocumento->consultarTipoDocumento(0,0,'vIdTipoDocumento',1,0,$_SESSION['R_IdSucursal'],'','reporte');
//$rst = $objGrilla->consultar.$clase.(0,0,vIdTipoDocumento,1,0,".$_SESSION['R_IdSucursal'].",'','reporte'"."\")");
if(is_string($TIPODOCUMENTO_rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	exit();
}
//<<Fin

//--------------------------->CLASE DOCUMENTOVENTA<---------------------------
//VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
//Nombre y Codigo de la Clase a Ejecutar
$clase = $_POST['txtClaseREPORTE'];
$id_clase = $_POST['txtIdClaseREPORTE'];
if(isset($_POST['txtTituloREPORTE']) and $_POST['txtTituloREPORTE']<>''){
	$titulo = $_POST['txtTituloREPORTE'];
}else{
	$titulo = $clase;
}
if(isset($_POST['txtFuncionREPORTE']) and $_POST['txtFuncionREPORTE']<>''){
	$funcion = $_POST['txtFuncionREPORTE'];
}else{
	$funcion = '';
}
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");
//Nro de Hoja a mostrar YA VALIDADO en la Grilla
$nro_hoja = 1;
//Nro de Registros a mostrar en la Grilla
$nro_reg=$_POST['txtNroRegistrosTotalREPORTE'];
//Filtro Ya validado de Grilla
$filtro = str_replace("\'" ,"'", $_POST['txtFiltroREPORTE']);

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");

//>>Inicio Obtiene Campos a mostrar
$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "Error al obtener campos a mostrar".$rstCampos."";
	exit();
}
//CREAMOS LA CABECERA A MOTRAR EN LA TABLA
$CABECERA = array();
$dataCampos = $rstCampos->fetchAll();
foreach($dataCampos as $value){
$CABECERA[] = cambiaHTML($value['comentario']);
$w[] = umill($value['longitudreporte']);
$a[] = umill($value['alineacionreporte']);
}
//<<Fin
//>>Inicio Ejecutando la consulta
eval("\$rst = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}
if($nro_reg==0){
	echo "Sin Informaci&oacute;n";
	exit();
}
//<<Fin
$fechaInicio=$_POST['txtFechaInicioREPORTE'];
$fechaFin=$_POST['txtFechaFinREPORTE'];

// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
$pdf=new PDF_Dinamico('L','mm','A4');
$pdf->Open();
$title='Reporte '.$titulo;
$subtitle=($fechaInicio!=''?'Del: '.$fechaInicio:'').' al: '.$fechaFin;
//Primera página
$pdf->AddPage();
//Cantidad de Paginas Existentes LO LLAMA EN EL FOOTER ---> {nb}
$pdf->AliasNbPages();
//LLENAMOS LAS VARIABLES
//LLenar Anchos de Columna
$pdf->SetWidths($w);
//LLenar Alineacion de Columna
$pdf->SetAligns($a);
//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA);
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rstTipoDocumento = \$objTipoDocumento->consultar".$clase_tipodocumento."(0,0,'IdTipoDocumento',1,0,\"\",\""."reporte"."\");");
eval("\$rstGrilla = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro);
$rstTipoDocumento = $rstTipoDocumento->fetchAll();
$rstGrilla = $rstGrilla->fetchAll();
$pdf->LlenarTabla_Datos_Agrupado_Resumen('VentaAgrupado',$rstGrilla,$dataCampos,$rstTipoDocumento,$TIPODOCUMENTO_dataCampos,'IdTipoDocumento','Descripcion');

$pdf->Output($clase.'.pdf','I');
?>