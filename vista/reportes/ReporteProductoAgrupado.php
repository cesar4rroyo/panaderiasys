<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsCategoria.php');
require_once('../../modelo/clsMarca.php');


//--------------------------->CLASE CATEGORIA<---------------------------
$clase_categoria = 'Categoria';
$id_clase_categoria = 28;
//Instancia la ClaseCategoria
eval("\$objCategoria = new cls".$clase_categoria."(".$id_clase_categoria.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//>>Inicio Obtiene Campos a mostrar
$CATEGORIA_rstCampos = $objCategoria->obtenerCamposMostrar("G");
if(is_string($CATEGORIA_rstCampos)){
	exit();
}
$CATEGORIA_dataCampos = $CATEGORIA_rstCampos->fetchAll();
//<<Fin

//>>Inicio Ejecutando la consultA DE Categoria
eval("\$CATEGORIA_rst = \$objCategoria->consultar".$clase_categoria."(0,0,'IdCategoria',1,0,".$_SESSION['R_IdSucursal'].",\""."\",\""."reporte"."\");");
//$rst = $objCategoria->consultarCategoria(0,0,'vIdCategoria',1,0,$_SESSION['R_IdSucursal'],'','reporte');
//$rst = $objGrilla->consultar.$clase.(0,0,vIdCategoria,1,0,".$_SESSION['R_IdSucursal'].",'','reporte'"."\")");
if(is_string($CATEGORIA_rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	exit();
}
//<<Fin

//--------------------------->CLASE MARCA<---------------------------
$clase_marca = 'Marca';
$id_clase_marca = 36;
//Instancia la ClaseMarca
eval("\$objMarca = new cls".$clase_marca."(".$id_clase_marca.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//>>Inicio Obtiene Campos a mostrar
$MARCA_rstCampos = $objMarca->obtenerCamposMostrar("G");
if(is_string($MARCA_rstCampos)){
	exit();
}
$MARCA_dataCampos = $MARCA_rstCampos->fetchAll();
//<<Fin
//>>Inicio Ejecutando la consultA DE MARCA
eval("\$MARCA_rst = \$objMarca->consultar".$clase_marca."(0,0,IdMarca,1,0,".$_SESSION['R_IdSucursal'].",\""."\",\""."reporte"."\");");
if(is_string($MARCA_rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	exit();
}
//<<Fin



//--------------------------->CLASE PRODUCTO<---------------------------
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


// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
$pdf=new PDF_Dinamico('L','mm','A4');
$pdf->Open();
$title='Reporte '.$titulo;
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
eval("\$rstCategoria = \$objCategoria->consultar".$clase_categoria."(0,0,IdCategoria,1,0,".$_SESSION['R_IdSucursal'].",\""."\",\""."reporte"."\");");
eval("\$rstMarca = \$objMarca->consultar".$clase_marca."(0,0,IdMarca,1,0,".$_SESSION['R_IdSucursal'].",\""."\",\""."reporte"."\");");
eval("\$rstGrilla = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro);
$rstCategoria = $rstCategoria->fetchAll();
$rstMarca = $rstMarca->fetchAll();
$rstGrilla = $rstGrilla->fetchAll();
$pdf->LlenarTabla_Datos_Agrupado2($rstGrilla,$dataCampos,$rstCategoria,$CATEGORIA_dataCampos,'IdCategoria','Descripcion',$rstMarca,$MARCA_dataCampos,'IdMarca','Descripcion');

$pdf->Output($clase.'.pdf','I');
?>