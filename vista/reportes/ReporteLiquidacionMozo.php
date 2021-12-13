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
	$funcion = 'LiquidacionMozo';
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
//LLenar Cabecera
$pdf->SetCabecera_tabla($CABECERA);
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
//eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."('".$_GET["fecha"]."');");
//$pdf->LlenarTabla_Datos($rst2,$CAMPOS);
while($data=$rst->fetchObject()){
    $platos[0]=array("productos"=>"COMIDA","cantidad"=>1,"preciounitario"=>0,"preciototal"=>0);
    $platos["TRAGOS"]=array("productos"=>"TRAGOS","cantidad"=>1,"preciounitario"=>0,"preciototal"=>0);
    $platos["CHICHA"]=array("productos"=>"CHICHA","cantidad"=>1,"preciounitario"=>0,"preciototal"=>0);
    $platos["LIMONADA"]=array("productos"=>"LIMONADA FROZEN","cantidad"=>0,"preciounitario"=>0,"preciototal"=>0);
    $pdf->SetFont("courier",'B',14);
    $pdf->Cell(0,5,"MOZO: ".$data->responsable."    FECHA: ".$_GET["fecha"],0,1,'C',false);
    $pdf->Ln();
    $rst3=$objGrilla->consultarProductoxMozo($data->idresponsable,$data->idsucursalresponsable);
    while($datos=$rst3->fetchObject()){
        if($datos->kardex=="S"){
            $cantidad= $platos[$datos->idproducto]["cantidad"]+ $datos->cantidad;
            $total= $platos[$datos->idproducto]["preciototal"]+ $datos->total;
            $platos[$datos->idproducto]=array("productos"=>utf8_decode($datos->producto),"cantidad"=>$cantidad,"preciounitario"=>$datos->precioventa,"preciototal"=>$total);
            $totalxmozo = $totalxmozo + $total; 
        }else{
            if(strpos($datos->categoria, "TRAGO") !== FALSE){            
                $platos["TRAGOS"]["preciounitario"]=$platos["TRAGOS"]["preciounitario"]+$datos->total;
                $platos["TRAGOS"]["preciototal"]=$platos["TRAGOS"]["preciototal"]+$datos->total;
                $totalxmozo = $totalxmozo + $datos->total;
            }else{
                if(strpos($datos->producto, " CHICHA") !== FALSE && strpos($dato->productos, "CHICHARR") === FALSE){            
                    $platos["CHICHA"]["preciounitario"]=$platos["CHICHA"]["preciounitario"]+$datos->total;
                    $platos["CHICHA"]["preciototal"]=$platos["CHICHA"]["preciototal"]+$datos->total;
                    $totalxmozo = $totalxmozo + $datos->total;
                }else{
                    if(strpos($datos->producto, "LIMONADA") !== FALSE ){
                        $platos["LIMONADA"]["preciounitario"]=$platos["LIMONADA"]["preciounitario"]+$datos->total;
                        $platos["LIMONADA"]["preciototal"]=$platos["LIMONADA"]["preciototal"]+$datos->total;
                        $platos["LIMONADA"]["cantidad"]=$platos["LIMONADA"]["cantidad"]+1;
                        $totalxmozo = $totalxmozo + $datos->total;
                    }else{
                        $platos[0]["preciounitario"]=$platos[0]["preciounitario"]+$datos->total;
                        $platos[0]["preciototal"]=$platos[0]["preciototal"]+$datos->total;
                        $totalxmozo = $totalxmozo + $datos->total;
                }
            }    
        }  
        }
    }
    if($platos["TRAGOS"]["preciototal"]==0) unset($platos["TRAGOS"]);
    if($platos["CHICHA"]["preciototal"]==0) unset($platos["CHICHA"]);
    if($platos["LIMONADA"]["preciototal"]==0) unset($platos["LIMONADA"]);
    //CREAMOS LA TABLA
    $pdf->LlenarTabla_Cabecera();
    $fill=false;
    foreach($platos as $k=>$v){
        $venta = array_values($v);
        $pdf->Row($venta,$fill,13);
        //$fill=!$fill;//para manejo de colores    
    }
    $resumen["productos"]="";
    $resumen["cantidad"]="";
    $resumen["preciounitario"]="TOTAL";
    $resumen["preciototal"]=number_format($totalxmozo,2,'.','');   
    $resumen = array_values($resumen);
    $pdf->Row($resumen,false,15);
    $pdf->ln();    
    $resumen="";
    $platos="";
    $totalxmozo="";
    
    $pdf->Ln();
}

$pdf->Output($clase.'.pdf','I');
?>