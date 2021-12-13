<?php
session_start();
error_reporting(0);
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
//$nro_hoja = $_POST['txtNroHojaREPORTE'];
$nro_hoja="-1";
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
$dataCampos = $rstCampos->fetchAll();
foreach($dataCampos as $value){
$CABECERA[] = cambiaHTML($value['comentario']);
$w[] = umill($value['longitudreporte']);
$a[] = umill($value['alineacionreporte']);
$resumen[strtolower($value['descripcion'])] = '';
}
//Fin
//Inicio Ejecutando la consulta
eval("\$rst = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
//print_r($rst);
if(is_string($rst)){
	echo "Error al ejecutar consulta";
	exit();
}
if($nro_reg==0){
	echo "Sin Informaci&oacute;n";
	exit();
}
//------RESUMEN
$fechaInicio=$_POST['txtFechaInicioREPORTE'];
$fechaFin=$_POST['txtFechaFinREPORTE'];//print_r($rst);
while($dato=$rst->fetchObject()){
	if($origen=='CC'){
		$resumen['conceptopago']='TOTAL';
		$resumen['ingresos']=number_format($resumen['ingresos']+$dato->ingresos,2,'.','');
        $resumen['totalpagado']=number_format($resumen['totalpagado']+$dato->ingresos - $dato->montodebito - $dato->montocrebito,2,'.','');
		$resumen['egresos']=number_format($resumen['egresos']+$dato->egresos,2,'.','');
		$resumen['persona']='SALDO';
		$resumen['comentario']=number_format($resumen['ingresos']-$resumen['egresos'],2,'.','');
        //print_r($dato);
        $resumen2['tipodocumento']='TARJETA VISA';
		$resumen2['conceptopago']=number_format($resumen2['conceptopago']+$dato->montodebito,2,'.','');
        $resumen2['ingresos']='TARJETA MASTERCARD';
		$resumen2['egresos']=number_format($resumen2['egresos']+$dato->montocredito,2,'.','');
		$resumen2['persona']='TOTAL TARJETAS';
		$resumen2['comentario']=number_format($resumen2['conceptopago']+$resumen2['egresos'],2,'.','');
        
        $resumen3['tipodocumento']='TOTAL INGRESOS';
		$resumen3['conceptopago']=number_format($resumen['ingresos'],2,'.','');
        $resumen3['ingresos']='TOTAL EGRESOS';
		$resumen3['egresos']=number_format($resumen['egresos'],2,'.','');
		$resumen3['persona']='SALDO EFECTIVO';
		$resumen3['comentario']=number_format($resumen3['conceptopago']-$resumen3['egresos']-$resumen2['comentario'],2,'.','');
	}
	if($origen=='FC'){
		$resumen['conceptopago']='TOTAL';
		$resumen['ingresos']=number_format($resumen['ingresos']+$dato->ingresos,2,'.','');
		$resumen['egresos']=number_format($resumen['egresos']+$dato->egresos,2,'.','');
		$resumen['persona']='SALDO';
		$resumen['comentario']=number_format($resumen['ingresos']-$resumen['egresos'],2,'.','');
        
        $resumen2['tipodocumento']='TARJETA VISA';
		$resumen2['conceptopago']=number_format($resumen2['conceptopago']+$dato->montodebito,2,'.','');
        $resumen2['ingresos']='TARJETA MASTERCARD';
		$resumen2['egresos']=number_format($resumen2['egresos']+$dato->montocredito,2,'.','');
		$resumen2['persona']='TOTAL TARJETAS';
		$resumen2['comentario']=number_format($resumen2['conceptopago']+$resumen2['egresos'],2,'.','');
        
        $resumen3['tipodocumento']='TOTAL INGRESOS';
		$resumen3['conceptopago']=number_format($resumen['ingresos'],2,'.','');
        $resumen3['ingresos']='TOTAL EGRESOS';
		$resumen3['egresos']=number_format($resumen['egresos'],2,'.','');
		$resumen3['persona']='SALDO EFECTIVO';
		$resumen3['comentario']=number_format($resumen3['conceptopago']-$resumen3['egresos']-$resumen2['comentario'],2,'.','');
        
	}
    if($origen=='GA'){
		$resumen['conceptopago']='INGRESO';
		$resumen['total']=number_format($resumen['total']+$dato->ingreso,2,'.','');
		$resumen['egresos']=number_format($resumen['egresos']+$dato->egreso,2,'.','');
		$resumen['persona']='SALDO';
		$resumen['comentario']=number_format($resumen['total']-$resumen['egresos'],2,'.','');
    }
}
//------RESUMEN
// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
class PDF_Caja extends PDF_Dinamico{
	//CONSTRUCTOR DE LA CLASE
	function __construct($orientation, $unit, $format){
			parent::__construct($orientation, $unit,$format);
	}

	//LLenar la Grilla de Tabla Agrupada con Resumen: 1 nivel de agrupamiento
	function LlenarTabla_Datos_Agrupado_Resumen_AperturaCaja($origen,$rstGrilla,$dataCampos){
	
	//---------------------->LLENAR ARRAY GRILLA<--------------------------------
		$Grilla = array();
		foreach($rstGrilla as $dato){
			  $Grilla[] = $dato;
		}
		
		reset($dataCampos);
		$resumen = array();
		foreach($dataCampos as $value){
			$resumen[strtolower($value['descripcion'])] = '';
		}
		$resumenfinal=$resumen;
		
			//---------------------->LLENAR GRILLA<--------------------------------
			$this->SetFont('courier','B',$this->tam_letra_grilla);
			//Grilla
			$fill=false;
			$cont = 0;
            
			$comienzoN1=false;
			foreach($Grilla as $dato){
			
				 $FILA_DATOS = array();
	
				 reset($dataCampos);
				foreach($dataCampos as $value){
				 $FILA_DATOS[] = utf8_decode($dato[strtolower($value['descripcion'])]);
				}
				
				if($N1_cont==0){$muestraresumen=false;}
				 if(($dato['idconceptopago'])!=1){
					 if($comienzoN1){
						$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
					 }else{
						$N1_cont++;
						$this->SetFont('courier','B',$this->tam_letra_grilla+2);
						$this->Cell(10,10,$N1_cont.'.- FECHA: '.substr($dato['fecha'],0,10),0,1,'L');
						$this->Ln(2);
						//CREAMOS LA TABLA
						$this->LlenarTabla_Cabecera();
						$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
						$comienzoN1=true;
					 }
				  	$fill=!$fill;
				 }else{
				 	if($muestraresumen){
						$resumen=array_values($resumen);
						$this->SetFont('courier','B',$this->tam_letra_grilla+2);
						$this->Row($resumen,true,$this->tam_letra_grilla+2);
						//$muestraresumen=true;
						reset($dataCampos);
						$resumen = array();
						foreach($dataCampos as $value){
							$resumen[strtolower($value['descripcion'])] = '';
						}
                        if($origen=='FC' || $origen=='CC'){
                            $resumen2=array_values($resumen2);
                            $resumen3=array_values($resumen3);
                            $this->Ln();
                            $this->Row($resumen2,true,$this->tam_letra_grilla+1);
                            $this->Row($resumen3,true,$this->tam_letra_grilla+1);
                            $resumen2=null;
                            $resumen3=null;
                        }
					}
					$muestraresumen=true;
					$N1_cont++;
					$this->SetFont('courier','B',$this->tam_letra_grilla+2);
					$this->Cell(10,10,$N1_cont.'.- FECHA: '.substr($dato['fecha'],0,10),0,1,'L');
					$this->Ln(2);
					//CREAMOS LA TABLA
					$this->LlenarTabla_Cabecera();
					$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
					$comienzoN1=true;
					$fill=!$fill;
				 }
				 
				 if($origen=='CC'){
						$muestraresumen=true;
						$resumen['conceptopago']='TOTAL';
                        if(trim($dato['idconceptopago'])=='3'){
                            $resumen['ingresos']=number_format($resumen['ingresos']+$dato['totalpagado']+$dato["montodebito"]+$dato["montocredito"],2,'.','');
                            if(($dato['totalpagado']+$dato["montodebito"]+$dato["montocredito"])>0){
                                $credito=0;
                            }else{//VENTA AL CREDITO
                                $credito=$dato['ingresos'];
                            }
                        }elseif(trim($dato['idconceptopago'])!='1'){
                            $resumen['ingresos']=number_format($resumen['ingresos']+$dato['ingresos'],2,'.','');
                            $credito=0;
                        }else{
                            $resumen['ingresos']=number_format(0,2,'.','');
                            $credito=0;
                        }
                        //$resumen['totalpagado']=number_format($resumen['totalpagado']+$dato['ingresos'] - $dato['montodebito'] - $dato['montocrebito'],2,'.','');
						$resumen['egresos']=number_format($resumen['egresos']+$dato['egresos'],2,'.','');
						$resumen['persona']='SALDO';
						$resumen['comentario']=number_format($resumen['ingresos']-$resumen['egresos'],2,'.','');
						//print_r($resumen);echo "resumen<br>";
						$resumenfinal['conceptopago']='TOTAL GENERAL';
						$resumenfinal['ingresos']=number_format($resumenfinal['ingresos']+$dato['ingresos'],2,'.','');
						$resumenfinal['egresos']=number_format($resumenfinal['egresos']+$dato['egresos'],2,'.','');
						$resumenfinal['persona']='SALDO';
						$resumenfinal['comentario']=number_format($resumenfinal['ingresos']-$resumenfinal['egresos'],2,'.','');
                                                
                        $resumen2['tipodocumento']='TARJETA VISA';
                		$resumen2['conceptopago']=number_format($resumen2['conceptopago']+$dato["montodebito"],2,'.','');
                        $resumen2['ingresos']='TARJETA MASTERCARD';
                		$resumen2['egresos']=number_format($resumen2['egresos']+$dato["montocredito"],2,'.','');
                		$resumen2['persona']='TOTAL TARJETAS';
                		$resumen2['comentario']=number_format($resumen2['conceptopago']+$resumen2['egresos'],2,'.','');
                        $resumen2['visa']='VENTA CREDITO';
                        $resumen2['master']=number_format($resumen2['master']+$credito,2,'.','');
                        
                        $resumen3['tipodocumento']='TOTAL INGRESOS';
                		$resumen3['conceptopago']=number_format($resumen['ingresos'],2,'.','');
                        $resumen3['ingresos']='TOTAL EGRESOS';
                		$resumen3['egresos']=number_format($resumen['egresos'],2,'.','');
                		$resumen3['persona']='SALDO EFECTIVO';
                		$resumen3['comentario']=number_format($resumen3['conceptopago']-$resumen3['egresos']-$resumen2['comentario'],2,'.','');

						//print_r($resumenfinal); echo "resumenfinal<br>";
				}elseif($origen=='FC'){
						$muestraresumen=true;
						$resumen['conceptopago']='TOTAL';
						$resumen['ingresos']=number_format($resumen['ingresos']+$dato['ingresos'],2,'.','');
						$resumen['egresos']=number_format($resumen['egresos']+$dato['egresos'],2,'.','');
						$resumen['persona']='SALDO';
						$resumen['comentario']=number_format($resumen['ingresos']-$resumen['egresos'],2,'.','');
						//print_r($resumen);echo "resumen<br>";
						$resumenfinal['conceptopago']='TOTAL GENERAL';
						$resumenfinal['ingresos']=number_format($resumenfinal['ingresos']+$dato['ingresos'],2,'.','');
						$resumenfinal['egresos']=number_format($resumenfinal['egresos']+$dato['egresos'],2,'.','');
						$resumenfinal['persona']='SALDO';
						$resumenfinal['comentario']=number_format($resumenfinal['ingresos']-$resumen['egresos'],2,'.','');
						//print_r($resumenfinal); echo "resumenfinal<br>";
				}elseif($origen=='GA'){
						$muestraresumen=true;
						$resumen['conceptopago']='INGRESOS';
						$resumen['total']=number_format($resumen['total']+$dato['ingreso'],2,'.','');
						$resumen['egresos']=number_format($resumen['egresos']+$dato['egreso'],2,'.','');
						$resumen['persona']='SALDO';
						$resumen['comentario']=number_format($resumen['total']-$resumen['egresos'],2,'.','');
						//print_r($resumen);echo "resumen<br>";
						$resumenfinal['conceptopago']='INGRESO GENERAL';
						$resumenfinal['total']=number_format($resumenfinal['total']+$dato['ingreso'],2,'.','');
						$resumenfinal['egresos']=number_format($resumenfinal['egresos']+$dato['egreso'],2,'.','');
						$resumenfinal['persona']='SALDO GENERAL';
						$resumenfinal['comentario']=number_format($resumenfinal['total']-$resumen['egresos'],2,'.','');
						//print_r($resumenfinal); echo "resumenfinal<br>";
				}

			}//cierra for
			
		$resumen=array_values($resumen);
		$this->SetFont('courier','B',$this->tam_letra_grilla+2);
		$this->Row($resumen,true,$this->tam_letra_grilla+2);
					
		if($comienzoN1==TRUE){
		   $this->Cell(array_sum($this->widths),0,'','T');
		   $this->Ln(5);
		}
        if($origen=='FC' || $origen=='CC'){
            $resumen2=array_values($resumen2);
            $resumen3=array_values($resumen3);
            $this->Ln();
            $this->Row($resumen2,true,$this->tam_letra_grilla+1);
            $this->Row($resumen3,true,$this->tam_letra_grilla+1);
            //$this->Cell(20,5,"Imprimir Cierre",0,0,'',false,"http://localhost/chanis/vista/ajaxPedido.php?accion=");
        }
		$resumenfinal=array_values($resumenfinal);
		$this->SetFont('courier','B',$this->tam_letra_grilla+2);
		$this->Row($resumenfinal,true,$this->tam_letra_grilla+2);
        
        
	}
}

$pdf=new PDF_Caja('L','mm','A4');
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
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
eval("\$rst3 = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
$pdf->LlenarTabla_Datos_Agrupado_Resumen_AperturaCaja($origen,$rst2,$dataCampos);

/*while($dat=$rst3->fetchObject()){
    if($dat->idconceptopago==3 && $dat->idmovimientoref>0){
        $da=$objGrilla->obtenerDataSQL("select T.* from (select * from movimiento union all select * from movimientohoy) T where idmovimiento in (select D.idmovimientoref from (select * from detallemovimiento union all select * from detallemovimientohoy) D where D.idmovimiento=".($dat->idmovimientoref==""?0:$dat->idmovimientoref).")")->fetchObject();
        $da1=$objGrilla->obtenerDataSQL("select * from personamaestro where idpersonamaestro in (select idpersonamaestro from persona where idpersona=".$da->idresponsable.")")->fetchObject();
        $lista[$da1->nombres.'-'.$da->numero]=array("mozo"=>$da1->nombres,"numero"=>$da->numero,"total"=>$dat->total);
        $list[$da1->nombres]=array("mozo"=>$da1->nombres);
    }    
}
//print_r($lista);
foreach($list as $k=>$v){
    $total=0;$c=0;
    $pdf->SetFont('courier','B',11);
    $pdf->Cell(10,4,$v["mozo"],0,1,'L');
    $pdf->Ln();
    $pdf->SetFont('courier','',10);
    foreach($lista as $m=>$n){//print_r($n);
        $c=$c+1;
        if($n["mozo"]==$v["mozo"]){
            $pdf->Cell(18,4,$n["numero"],1,0,'L');
            $total=$total+$n["total"];
        }
        if($c==15){
            $c=0;
            $pdf->Ln();
        }
    }
    $pdf->Ln();
    $pdf->SetFont('courier','B',10);
    $pdf->Cell(10,4,"TOTAL: S/.".number_format($total,2,'.',''),0,1,'L');
    $pdf->Ln();
}*/

$pdf->Output($clase.'.pdf','I');
?>