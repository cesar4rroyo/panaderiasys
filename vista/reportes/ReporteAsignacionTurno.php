<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsTurno.php');
require_once('../../modelo/clsSalon.php');
require_once('../../modelo/clsCaja.php');


//--------------------------->CLASE TURNO<---------------------------
$clase_turno = 'Turno';
$id_clase_turno = 54;
//Instancia la ClaseCategoria
eval("\$objTurno = new cls".$clase_turno."(".$id_clase_turno.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//>>Inicio Obtiene Campos a mostrar
$TURNO_rstCampos = $objTurno->obtenerCampos();
if(is_string($TURNO_rstCampos)){
	exit();
}
$TURNO_dataCampos = $TURNO_rstCampos->fetchAll();
//<<Fin

//>>Inicio Ejecutando la consultA DE Categoria
eval("\$TURNO_rst = \$objTurno->consultar".$clase_turno."(10,1, '1',1, 0, '');");
if(is_string($TURNO_rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	exit();
}
//<<Fin

//--------------------------->CLASE SALON<---------------------------
$clase_salon = 'Salon';
$id_clase_salon = 25;
//Instancia la ClaseMarca
eval("\$objSalon = new cls".$clase_salon."(".$id_clase_salon.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//>>Inicio Obtiene Campos a mostrar
$SALON_rstCampos = $objSalon->obtenerCamposMostrar("G");
if(is_string($SALON_rstCampos)){
	exit();
}
$SALON_dataCampos = $SALON_rstCampos->fetchAll();
//<<Fin
//>>Inicio Ejecutando la consultA DE MARCA
eval("\$SALON_rst = \$objSalon->consultar".$clase_salon."(10,1, '1',1, 0,".$_SESSION['R_IdSucursal'].",'');");
if(is_string($SALON_rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	exit();
}
//<<Fin

//--------------------------->CLASE CAJA<---------------------------
$clase_caja = 'Caja';
$id_clase_caja = 52;
//Instancia la ClaseMarca
eval("\$objCaja = new cls".$clase_caja."(".$id_clase_caja.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//>>Inicio Obtiene Campos a mostrar
$CAJA_rstCampos = $objCaja->obtenerCampos();
if(is_string($CAJA_rstCampos)){
	exit();
}
$CAJA_dataCampos = $CAJA_rstCampos->fetchAll();
//<<Fin
//>>Inicio Ejecutando la consultA DE MARCA
eval("\$CAJA_rst = \$objCaja->consultar".$clase_caja."(10,1, '1',1, 0,".$_SESSION['R_IdSucursal'].",'');");
if(is_string($CAJA_rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	exit();
}
//<<Fin


//--------------------------->CLASE PERSONA<---------------------------
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
eval("\$rstTurno = \$objTurno->consultar".$clase_turno."(10,1, '1',1, 0, '');");
eval("\$rstSalon = \$objSalon->consultar".$clase_salon."(10,1, '1',1, 0,".$_SESSION['R_IdSucursal'].",'');");
eval("\$rstCaja = \$objCaja->consultar".$clase_caja."(10,1, '1',1, 0,".$_SESSION['R_IdSucursal'].",'');");
eval("\$rstGrilla = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro);

$rstTurno = $rstTurno->fetchAll();
$rstSalon = $rstSalon->fetchAll();
$rstCaja = $rstCaja->fetchAll();
$rstGrilla = $rstGrilla->fetchAll();
$pdf->LlenarTabla_Datos_Agrupado3($rstGrilla,$dataCampos,$rstTurno,$TURNO_dataCampos,'IdTurno','Nombre',$rstSalon,$SALON_dataCampos,'IdSalon','Descripcion',$rstCaja,$CAJA_dataCampos,'IdCaja','Numero');
$pdf->Output($clase.'.pdf','I');
?>
