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
if(isset($_POST['txtOrigenREPORTE']) and $_POST['txtOrigenREPORTE']<>''){
	$origen = $_POST['txtOrigenREPORTE'];
}else{
	$origen = '';
}
//Requiere para Ejecutar Clase
eval("require(\"../../modelo/cls".$clase.".php\");");
//Nro de Hoja a mostrar YA VALIDADO en la Grilla
$nro_hoja = 1;
//Nro de Registros a mostrar en la Grilla
$nro_reg=$_POST['txtNroRegistrosTotalREPORTE'];
//Filtro Ya validado de Grilla
$filtro = str_replace("\'" ,"'", $_POST['txtFiltroREPORTE']);
/*$filtroarray=explode(',',$filtro);
$filtroarray[1]="'codigopro,idkardex'";
$filtro=implode(',',$filtroarray);*/

eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");

//Inicio Obtiene Campos a mostrar
$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "Error al obtener campos a mostrar".$rstCampos."";
	exit();
}
//CREAMOS LA CABECERA A MOTRAR EN LA TABLA
$CABECERA = array();
$dataCampos = $rstCampos->fetchAll();
foreach($dataCampos as $value){
	//if(strtolower($value['descripcion'])!='codigopro' and strtolower($value['descripcion'])!='producto'){
		$CABECERA[] = cambiaHTML($value['comentario']);
		$w[] = umill($value['longitudreporte']);
		$a[] = umill($value['alineacionreporte']);
	//}
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
//------RESUMEN
$fechaInicio=substr($filtro,strlen($filtro)-32,10);
$fechaFin=substr($filtro,strlen($filtro)-19,10);
//------RESUMEN
// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
class PDF_Caja extends PDF_Dinamico{
	//CONSTRUCTOR DE LA CLASE
	function __construct($orientation, $unit, $format){
			parent::__construct($orientation, $unit,$format);
	}

	//LLenar la Grilla de Tabla Agrupada con Resumen: 1 nivel de agrupamiento
	function LlenarTabla_Datos_Agrupado_kardex($origen,$rstGrilla,$dataCampos){
	
	//---------------------->LLENAR ARRAY GRILLA<--------------------------------
		$Grilla = array();
		foreach($rstGrilla as $dato){
			  $Grilla[] = $dato;
		}
		
		reset($dataCampos);

			$fill=false;
			$cont = 0;
	
			$comienzoN1=false;
			foreach($Grilla as $dato){
				$this->SetFont('courier','B',$this->tam_letra_grilla);			
				 $FILA_DATOS = array();
	
				 reset($dataCampos);
				foreach($dataCampos as $value){
					if(strtolower($value['descripcion'])!='codigopro' and strtolower($value['descripcion'])!='producto'){
				 		$FILA_DATOS[] = utf8_decode($dato[strtolower($value['descripcion'])]);
					}
				}
				
				if($dato['codigopro']!=$ultimocodigo) $comienzoN1=false;
				$ultimocodigo=$dato['codigopro'];
				
				 if($comienzoN1){
					$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
				 }else{
					$N1_cont++;
					$this->SetFont('courier','B',$this->tam_letra_grilla+2);
					$this->Cell(10,10,$N1_cont.'.- PRODUCTO: '.$dato['codigopro'].' '.$dato['producto'],0,1,'L');
					$this->Ln(2);
					//CREAMOS LA TABLA
					$this->LlenarTabla_Cabecera();
					$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
					$comienzoN1=true;
				 }
				$fill=!$fill;
			}//cierra for
			
			if($comienzoN1==TRUE){
			   $this->Cell(array_sum($this->widths),0,'','T');
			   $this->Ln(5);
			}
	}
}

/*$pdf=new PDF_Caja('L','mm','A4');
$pdf->Open();
$title='Reporte '.$titulo;
$subtitle='Del: '.$fechaInicio.' al: '.$fechaFin;
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
$pdf->SetCabecera_tabla($CABECERA);*/
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);

//$pdf->LlenarTabla_Datos_Agrupado_kardex($origen,$rst2,$dataCampos);

//$pdf->Output($clase.'.pdf','I');


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