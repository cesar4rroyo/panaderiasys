<?php
session_start();
require_once('clsReporteDinamico.php');

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
if(isset($_POST['txtOcultarCamposREPORTE'])){
	$ocultarcampos=explode('-',$_POST['txtOcultarCamposREPORTE']);
}else{
	$ocultarcampos='';
}
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");
//Nro de Hoja a mostrar YA VALIDADO en la Grilla
$nro_hoja = $_POST['txtNroHojaREPORTE'];
//Nro de Registros a mostrar en la Grilla
$nro_reg=$_POST['txtNroRegistrosTotalREPORTE'];
//Filtro Ya validado de Grilla
$filtro = str_replace("\'" ,"'", $_POST['txtFiltroREPORTE']);

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");

//Inicio Obtiene Campos a mostrar
$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "Error al obtener campos a mostrar".$rstCampos."";
	exit();
}
//CREAMOS LA CABECERA A MOTRAR EN LA TABLA
$CABECERA = array();
$CAMPOS = array();
$dataCampos = $rstCampos->fetchAll();
foreach($dataCampos as $value){
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
}
//Fin
//Inicio Ejecutando la consulta
eval("\$rst = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}
if($nro_reg==0){
	echo "Sin Informaci&oacute;n";
	exit();
}

// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Reporte $titulo.xls\"");
$registro="<table border='1'><tr><th colspan='".count($CABECERA)."'>";
$title='Reporte '.$titulo;
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='".count($CABECERA)."'>";
$subtitle=($fechaInicio!=''?'Del: '.$fechaInicio:'').' al: '.$fechaFin;
$registro.=$subtitle."</th></tr><tr>";
for($i=0;$i<count($CABECERA);$i++){
    if($a[$i]=="C") $a[$i]="center";
    if($a[$i]=="L") $a[$i]="left";
    if($a[$i]=="R") $a[$i]="right";
    $registro.="<th align='".$a[$i]."'>".$CABECERA[$i]."</th>";
}
$registro.="</tr>";
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
//print_r($rst2);
while($dato = $rst2->fetch()){
    $cont = 0;
    $nro_registros_total = $dato["nrototal"];
    reset($dataCampos);
    $registro.="<tr>";
    foreach($dataCampos as $value){
        if($a[$cont]=="C") $a[$cont]="center";
        if($a[$cont]=="L") $a[$cont]="left";
        if($a[$cont]=="R") $a[$cont]="right";
        if($dato["estado"]=="I"){
            $color = "color:red";
            $dato["total"]=0;
        }else{
            $color = "";
        }
        $registro.="<td style='$color' align='".$a[$cont]."'>".($dato[strtolower($value['descripcion'])])."</td>";
        $cont++;
    }
    $registro.="</tr>";
}
echo $registro."</table>";
?>