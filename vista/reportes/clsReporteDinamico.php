<?php
session_start();
require_once('fpdf/fpdf.php');
//Modificamos la clase FPDF
class PDF_Dinamico extends FPDF{



//VARIABLES
//Tama�o de Letra de Cabecera y de Grilla
var $tam_letra_cabecera=9;
var $tam_letra_grilla=8;
//Array de Ancho en CADA COLUMNA DE LA TABLA
var $widths=array();
//Array de Alineaciones(Centrado,Justificado,Etc) en CADA COLUMNA DE LA TABLA
var $aligns=array();
//Array de Encabezado de la Tabla(Campos a Mostrar) en CADA COLUMNA DE LA TABLA
var $cabecera_tabla=array();


//CONSTRUCTOR DE LA CLASE
function __construct($orientation, $unit, $format){
		parent::__construct($orientation, $unit,$format);
}



//INICIALIZAMOS VARIABLES
//Ancho de Cada Columna
function SetWidths($w,$posicion='L'){
    //Calculamos el Ancho Total de La Tabla
    //print_r($w);
    $ancho_tabla = array_sum($w);
    //Comparamos con el Ancho Maximo q puede Tener un tabla en Formato A4
    if($posicion=='L'){
    	$tamano=277;
    }else{
    	$tamano=180;
    }
    if($ancho_tabla==$tamano){
       $this->widths=$w;
    }else{
       //Se Aumenta o Disminuye el Ancho de Cada Columna: Por Desborde o Falta de la Tabla
	   for($i=0;$i<count($w);$i++){
		   if($w[$i]==0) $cantCeros++;
	   }
       if($ancho_tabla<$tamano){
          //$ancho_agregado= (277-$ancho_tabla)/count($w);
		  $ancho_agregado= ($tamano-$ancho_tabla)/$cantCeros;
          $w2=array();
          for($i=0;$i<count($w);$i++){
			  if($w[$i]!=0){
				$w2[]=$w[$i];
			  }else{
              	$w2[]=$ancho_agregado+$w[$i];
			  }
          }
          $this->widths=$w2;
       }else{
          //$ancho_restado= ($ancho_tabla-277)/count($w);
		  $ancho_restado= ($ancho_tabla-$tamano)/$cantCeros;
          $w2=array();
          for($i=0;$i<count($w);$i++){
			  if($w[$i]!=0){
				$w2[]=$w[$i];
			  }else{
				$w2[]=$w[$i]-$ancho_restado;
			  }
          }
          $this->widths=$w2;
       }
    }

}
//Alineacion de Cada Columna
function SetAligns($a){
    //Establecer el conjunto de alineaciones columna
    $this->aligns=$a;
}
//Se Llena el Encabezado de Tabla
function SetCabecera_tabla($CABECERA){
    //Establecer el conjunto de alineaciones columna
    $this->cabecera_tabla=$CABECERA;
}
//Se Llena el Tama�o de Letra del Encabezado de Tabla
function SetTamLetraCabecera($tam){
    $this->tam_letra_cabecera=$tam;
}
//Se Llena el Tama�o de Letra de la Grilla de Tabla
function SetTamLetraGrilla($tam){
    $this->tam_letra_grilla=$tam;
}


//IMPLEMENTAMOS LA CABECERA Y PIE DE PAGINA DEL FPDF (Logos,Titulos,Fecha,etc)
//Cabecera de p�gina/FIJO
function Header(){
//Logo Empresa
	if($_SESSION['R_Logo']<>''){
		$ruta='empresas/'.$_SESSION['R_Logo'];
	}else{
		$ruta='razon.jpg';
	}
    //$this->Image("../../img/".$ruta , 10 ,8 , 50 , 20 );
    //$this->Image("logo.jpg", 10 ,8, 30 , 20 , "JPG" );
	//T�tulo
    global $title;
	//Arial bold 15
    $this->SetFont('courier','BU',24);
    //Calculamos ancho y posici�n del t�tulo.
    $w=$this->GetStringWidth($title)+6;
    if($this->CurOrientation=='L'){
    	$tamano = 297;
    }else{
    	$tamano = 190;
    }
    /*print_r($w);
    print_r("-".$tamano."-");*/
    $this->SetX(($tamano-$w)/2);
    //Colores de los bordes, fondo y texto
    $this->SetDrawColor(0,80,180);
    $this->SetFillColor(145,238,243);
    $this->SetTextColor(0);
    //Ancho del borde (1 mm)
    $this->SetLineWidth(1);
    //T�tulo
    $this->SetFillColor(145,238,243);
    $this->Cell($w,15,  strtoupper($title),0,1,'C',1);
	//SubT�tulo
    global $subtitle;
	if(isset($subtitle) or $subtitle!=''){
		//Arial bold 15
		$this->SetFont('courier','B',10);
		//Calculamos ancho y posici�n del t�tulo.
		$w=$this->GetStringWidth($subtitle)+6;
		$this->SetX(($tamano-$w)/2);
		//Colores de los bordes, fondo y texto
		$this->SetDrawColor(0,80,180);
		$this->SetFillColor(145,238,243);
		$this->SetTextColor(0);
		//Ancho del borde (1 mm)
		$this->SetLineWidth(1);
		//SubT�tulo
		$this->Cell($w,5,$subtitle,1,1,'C',false);
	}
//Logo Sucursal
    //Logo para Vertical
    //$this->Image("logo.jpg" , 180 ,8, 20 , 20 , "JPG" );
    //Logo para Horizonal
    //$this->Image("logo.jpg" , 267 ,8, 20 , 20 , "JPG" );
	if($_SESSION['R_Logo']<>''){
		$ruta='empresas/'.$_SESSION['R_Logo'];
	}else{
		$ruta='razon.jpg';
	}
    //$this->Image("../../img/".$ruta , 237 ,8 , 50 , 20 );
//Salto de l�nea
    $this->Ln(6);
}
//Pie de p�gina/FIJO
function Footer(){
    //Posici�n: a 1,5 cm del final
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('courier','',8);
	$fecha = time ();
	$fecha  = date ( "Y/m/d H:i:s a" , $fecha );
	$this->Cell(10,5,$fecha);
    //N�mero de p�gina
    $this->Cell(0,5,'Pagina '.$this->PageNo().' de {nb}',0,0,'R');
	//$this->Cell(0,5,'SISREST 1.0',0,0,'R');
}



//FUNCIONES DE LLENADO DE TABLA
//LLenar la Cabecera de Tabla
function LlenarTabla_Cabecera(){
//Colores, ancho de l�nea y fuente en negrita
    $this->SetFillColor(24,150,180);
    $this->SetTextColor(0);
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(0.3);
    $this->SetFont('courier','B',$this->tam_letra_cabecera);
//Cabecera
    //Tama�o de cada columna a mostrar
    $fill=false;
    //LLamamos a la funcion Row para Establecer los Anchos
    $this->Row($this->cabecera_tabla,$fill,$this->tam_letra_cabecera+2,false,TRUE);
    $this->Ln(0);
    //Restauraci�n de Color de Fondo y fuente
    $this->SetFillColor(145,238,243);
    //$this->SetFont('');
}
//LLenar la Grilla de Tabla
function LlenarTabla_Datos($rst2,$dataCampos){
    $this->SetFont('courier','B',$this->tam_letra_grilla);
//Grilla
    $fill=false;
    $cont = 0;
    while($dato = $rst2->fetch()){
        $FILA_DATOS = array();
        $cont = 0;
        $nro_registros_total = $dato["nrototal"];
        reset($dataCampos);
        foreach($dataCampos as $value){
            $FILA_DATOS[] = utf8_decode($dato[strtolower($value['descripcion'])]);
            $cont++;
        }
        if($dato["estado"]=="I"){
            $this->SetTextColor(255,0,0);
        }else{
            $this->SetTextColor(0,0,0);
        }
        $this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
        $fill=!$fill;
        //reset($datoOperaciones);
    }
    $this->Cell(array_sum($this->widths),0,'','T');
}

function LlenarTabla_DatosArray($rst,$arrayCampos){
    //Tipo de Letra
    $this->SetFont('courier','B',$this->tam_letra_grilla);
    //fill --> True: Fondo de Celda de Color / False : Fondo de Celda Transparente
    $fill=false;
    //Recorremos Fila del rst
    $w=0;
    foreach($rst as $dato){
         //Creamos un Array Fila para Mostrar en la Grilla
		 $fila = array();
	     //$nro_registros_total = $dato["nrototal"];
	     //Llenamos Array FILA --> Solo Listamos los Campos Permitidos
         for($i=0;$i<count($arrayCampos);$i++){
            $fila[] = utf8_decode($dato[strtolower($arrayCampos[$i])]);
         }
         //Llamamos a la Funcion Row para Mostrar en PDF
		 $this->Row($fila,$fill,$this->tam_letra_grilla,true,false,$w%2==0);
         $fill=!$fill;
         $w++;
    }
    //Cerramos la Linea de la Grilla
    $this->Cell(array_sum($this->widths),0,'','T');
}

//LLenar la Grilla de Tabla Agrupada: 1 nivel de agrupamiento
function LlenarTabla_Datos_Agrupado($rstGrilla,$dataCampos,$rstN1,$dataCamposN1,$campoId1,$campoDescripcion1){
//function LlenarTabla_Datos_Agrupado($rstGrilla,$dataCampos,$CATEGORIA_rst,$CATEGORIA_dataCampos,$MARCA_rst,$MARCA_dataCampos){


//---------------------->LLENAR ARRAY GRILLA<--------------------------------
    $Grilla = array();
    foreach($rstGrilla as $dato){
          $Grilla[] = $dato;
    }

	$N1_cont=0;
	foreach($rstN1 as $datoN1){
	
		$comienzoN1=false;
		reset($dataCamposN1);
		foreach($dataCamposN1 as $valueN1){
			if(trim(($valueN1['descripcion']))==$campoId1){
			   $Id_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
			if($valueN1['descripcion']==$campoDescripcion1){
			   $Descripcion_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
		}

		//---------------------->LLENAR GRILLA<--------------------------------
		$this->SetFont('courier','B',$this->tam_letra_grilla);
		//Grilla
		$fill=false;
		$cont = 0;
		$GrillaTemporal = array();
		$GrillaTemporal = $Grilla;

		$posicion_grilla=0;
		foreach($Grilla as $dato){
			 $FILA_DATOS = array();

			 reset($dataCampos);
	
			 if(($dato[strtolower($campoId1)])==$Id_N1){
				  foreach($dataCampos as $value){
					 $FILA_DATOS[] = utf8_decode($dato[strtolower($value['descripcion'])]);
				  }
	
					 if($comienzoN1){
						$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
					 }else{
						$N1_cont++;
						$this->SetFont('courier','B',$this->tam_letra_grilla+2);
						$this->Cell(10,10,$N1_cont.'.-'.$Descripcion_N1,0,1,'L');
						$this->Ln(2);
						//CREAMOS LA TABLA
						$this->LlenarTabla_Cabecera();
						$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
						$comienzoN1=true;
					 }
				  $fill=!$fill;
				  unset($GrillaTemporal[$posicion_grilla]);
			 }
			 $posicion_grilla++;
		}//cierra for
		
		//ELIMINA LOS ESPACIOS VACIOAS Q DEJA EL UNSET
		$GrillaTemporal = array_values($GrillaTemporal);
		$Grilla = $GrillaTemporal;
		
		if($comienzoN1==TRUE){
		   $this->Cell(array_sum($this->widths),0,'','T');
		   $this->Ln(5);
		}
	}
}

//LLenar la Grilla de Tabla Agrupada: 2 niveles de agrupamiento
function LlenarTabla_Datos_Agrupado2($rstGrilla,$dataCampos,$rstN1,$dataCamposN1,$campoId1,$campoDescripcion1,$rstN2,$dataCamposN2,$campoId2,$campoDescripcion2){
//function LlenarTabla_Datos_Agrupado($rstGrilla,$dataCampos,$CATEGORIA_rst,$CATEGORIA_dataCampos,$MARCA_rst,$MARCA_dataCampos){


//---------------------->LLENAR ARRAY GRILLA<--------------------------------
    $Grilla = array();
    foreach($rstGrilla as $dato){
          $Grilla[] = $dato;
    }

	$N1_cont=0;
	foreach($rstN1 as $datoN1){
	
		$comienzoN1=false;
		reset($dataCamposN1);
		foreach($dataCamposN1 as $valueN1){
			if(trim(($valueN1['descripcion']))==$campoId1){
			   $Id_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
			if($valueN1['descripcion']==$campoDescripcion1){
			   $Descripcion_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
		}

		$N2_cont=0;

		foreach($rstN2 as $datoN2){
			$comienzoN2=false;
			
			reset($dataCamposN2);
			foreach($dataCamposN2 as $valueN2){
				
				if(trim(($valueN2['descripcion']))==$campoId2){
				   $Id_N2=utf8_decode(trim(str_replace("","&nbsp;",$datoN2[strtolower($valueN2['descripcion'])])));
				}
				if($valueN2['descripcion']==$campoDescripcion2){
				   $Descripcion_N2=utf8_decode(trim(str_replace("","&nbsp;",$datoN2[strtolower($valueN2['descripcion'])])));
				}
			}
		
			//---------------------->LLENAR GRILLA<--------------------------------
			$this->SetFont('courier','B',$this->tam_letra_grilla);
			//Grilla
			$fill=false;
			$cont = 0;
			$GrillaTemporal = array();
			$GrillaTemporal = $Grilla;

			$posicion_grilla=0;
			foreach($Grilla as $dato){
				 $FILA_DATOS = array();

				 reset($dataCampos);
		
				 if(($dato[strtolower($campoId1)])==$Id_N1 and ($dato[strtolower($campoId2)])==$Id_N2){
					  foreach($dataCampos as $value){
						 $FILA_DATOS[] = utf8_decode($dato[strtolower($value['descripcion'])]);
					  }
		
					  if($comienzoN2){
						 $this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
					  }else{
						 if($comienzoN1){
							$N2_cont++;
							$this->SetFont('courier','B',$this->tam_letra_grilla+2);
							$this->Cell(10,10,'    '.$N1_cont.'.'.$N2_cont.'.-'.$Descripcion_N2,0,1,'L');
							$this->Ln(2);
							//CREAMOS LA TABLA
							$this->LlenarTabla_Cabecera();
							$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
							$comienzoN2=true;
						 }else{
							$N1_cont++;
							$N2_cont++;
							$this->SetFont('courier','B',$this->tam_letra_grilla+3);
							$this->Cell(10,10,$N1_cont.'.-'.$Descripcion_N1,0,1,'L');
							$this->SetFont('courier','B',$this->tam_letra_grilla+2);
							$this->Cell(10,10,'    '.$N1_cont.'.'.$N2_cont.'.-'.$Descripcion_N2,0,1,'L');
							$this->Ln(2);
							//CREAMOS LA TABLA
							$this->LlenarTabla_Cabecera();
							$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
							$comienzoN1=true;
							$comienzoN2=true;
						 }
					  }
					  $fill=!$fill;
					  unset($GrillaTemporal[$posicion_grilla]);
				 }
				 $posicion_grilla++;
			}//cierra for
			
			//ELIMINA LOS ESPACIOS VACIOAS Q DEJA EL UNSET
			$GrillaTemporal = array_values($GrillaTemporal);
			$Grilla = $GrillaTemporal;
			
			if($comienzoN2==TRUE){
			   $this->Cell(array_sum($this->widths),0,'','T');
			   $this->Ln(5);
			}
		}
	}
}

//LLenar la Grilla de Tabla Agrupada: 3 niveles de agrupamiento
function LlenarTabla_Datos_Agrupado3($rstGrilla,$dataCampos,$rstN1,$dataCamposN1,$campoId1,$campoDescripcion1,$rstN2,$dataCamposN2,$campoId2,$campoDescripcion2,$rstN3,$dataCamposN3,$campoId3,$campoDescripcion3){
//function LlenarTabla_Datos_Agrupado($rstGrilla,$dataCampos,$CATEGORIA_rst,$CATEGORIA_dataCampos,$MARCA_rst,$MARCA_dataCampos){


//---------------------->LLENAR ARRAY GRILLA<--------------------------------
    $Grilla = array();
    foreach($rstGrilla as $dato){
          $Grilla[] = $dato;
    }

	$N1_cont=0;
	foreach($rstN1 as $datoN1){
	
		$comienzoN1=false;
		reset($dataCamposN1);
		foreach($dataCamposN1 as $valueN1){
			if(trim(($valueN1['descripcion']))==$campoId1){
			   $Id_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
			if($valueN1['descripcion']==$campoDescripcion1){
			   $Descripcion_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
		}

		$N2_cont=0;

		foreach($rstN2 as $datoN2){
			$comienzoN2=false;
			
			reset($dataCamposN2);
			foreach($dataCamposN2 as $valueN2){
				
				if(trim(($valueN2['descripcion']))==$campoId2){
				   $Id_N2=utf8_decode(trim(str_replace("","&nbsp;",$datoN2[strtolower($valueN2['descripcion'])])));
				}
				if($valueN2['descripcion']==$campoDescripcion2){
				   $Descripcion_N2=utf8_decode(trim(str_replace("","&nbsp;",$datoN2[strtolower($valueN2['descripcion'])])));
				}
			}
		
			$N3_cont=0;

			foreach($rstN3 as $datoN3){
				$comienzoN3=false;
				
				reset($dataCamposN3);
				foreach($dataCamposN3 as $valueN3){
					
					if(trim(($valueN3['descripcion']))==$campoId3){
					   $Id_N3=utf8_decode(trim(str_replace("","&nbsp;",$datoN3[strtolower($valueN3['descripcion'])])));
					}
					if($valueN3['descripcion']==$campoDescripcion3){
					   $Descripcion_N3=utf8_decode(trim(str_replace("","&nbsp;",$datoN3[strtolower($valueN3['descripcion'])])));
					}
				}
				//---------------------->LLENAR GRILLA<--------------------------------
				$this->SetFont('courier','B',$this->tam_letra_grilla);
				//Grilla
				$fill=false;
				$cont = 0;
				$GrillaTemporal = array();
				$GrillaTemporal = $Grilla;
	
				$posicion_grilla=0;
				foreach($Grilla as $dato){
					 $FILA_DATOS = array();
	
					 reset($dataCampos);
			
					 if(($dato[strtolower($campoId1)])==$Id_N1 and ($dato[strtolower($campoId2)])==$Id_N2 and ($dato[strtolower($campoId3)])==$Id_N3){
						  foreach($dataCampos as $value){
							 $FILA_DATOS[] = utf8_decode($dato[strtolower($value['descripcion'])]);
						  }
			
						  if($comienzoN3){
							 $this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
						  }else{
							 if($comienzoN2){
								$N3_cont++;
								$this->SetFont('courier','B',$this->tam_letra_grilla+2);
								$this->Cell(10,10,'        '.$N1_cont.'.'.$N2_cont.'.'.$N3_cont.'.-'.$Descripcion_N3,0,1,'L');
								$this->Ln(2);
								//CREAMOS LA TABLA
								$this->LlenarTabla_Cabecera();
								$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
								$comienzoN3=true;
							 }else{
								if($comienzoN1){
								   $N2_cont++;
								   $N3_cont++;
								   $this->SetFont('courier','B',$this->tam_letra_grilla+2);
								   $this->Cell(10,10,'    '.$N1_cont.'.'.$N2_cont.'.-'.$Descripcion_N2,0,1,'L');
								   $this->Ln(2);
								   $this->SetFont('courier','B',$this->tam_letra_grilla+2);
								   $this->Cell(10,10,'        '.$N1_cont.'.'.$N2_cont.'.'.$N3_cont.'.-'.$Descripcion_N3,0,1,'L');
								   $this->Ln(2);
								   //CREAMOS LA TABLA
								   $this->LlenarTabla_Cabecera();
								   $this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
								   $comienzoN2=true;
								   $comienzoN3=true;
								}else{
								   $N1_cont++;
								   $N2_cont++;
								   $N3_cont++;
								   $this->SetFont('courier','B',$this->tam_letra_grilla+3);
								   $this->Cell(10,10,$N1_cont.'.-'.$Descripcion_N1,0,1,'L');
								   $this->SetFont('courier','B',$this->tam_letra_grilla+2);
								   $this->Cell(10,10,'    '.$N1_cont.'.'.$N2_cont.'.-'.$Descripcion_N2,0,1,'L');
								   $this->Ln(2);
								   $this->SetFont('courier','B',$this->tam_letra_grilla+2);
								   $this->Cell(10,10,'        '.$N1_cont.'.'.$N2_cont.'.'.$N3_cont.'.-'.$Descripcion_N3,0,1,'L');
								   $this->Ln(2);
								   //CREAMOS LA TABLA
								   $this->LlenarTabla_Cabecera();
								   $this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
								   $comienzoN1=true;
								   $comienzoN2=true;
								   $comienzoN3=true;
								}
							 }
						  }
						  $fill=!$fill;
						  unset($GrillaTemporal[$posicion_grilla]);
					 }
					 $posicion_grilla++;
				}//cierra for
				
				//ELIMINA LOS ESPACIOS VACIOAS Q DEJA EL UNSET
				$GrillaTemporal = array_values($GrillaTemporal);
				$Grilla = $GrillaTemporal;
				
				if($comienzoN3==TRUE){
				   $this->Cell(array_sum($this->widths),0,'','T');
				   $this->Ln(5);
				}
			}
		}
	}
}

//LLenar la Grilla de Tabla Agrupada con Resumen: 1 nivel de agrupamiento
function LlenarTabla_Datos_Agrupado_Resumen($origen,$rstGrilla,$dataCampos,$rstN1,$dataCamposN1,$campoId1,$campoDescripcion1){
//function LlenarTabla_Datos_Agrupado($rstGrilla,$dataCampos,$CATEGORIA_rst,$CATEGORIA_dataCampos,$MARCA_rst,$MARCA_dataCampos){


//---------------------->LLENAR ARRAY GRILLA<--------------------------------
    $Grilla = array();
    foreach($rstGrilla as $dato){
          $Grilla[] = $dato;
    }
	
	$resumenfinal = array();
	$N1_cont=0;
	foreach($rstN1 as $datoN1){
		
		reset($dataCampos);
		$resumen = array();
		foreach($dataCampos as $value){
			$resumen[strtolower($value['descripcion'])] = '';
		}
		if($N1_cont==0){$resumenfinal=$resumen;}
	
		$comienzoN1=false;
		reset($dataCamposN1);
		foreach($dataCamposN1 as $valueN1){
			if(trim(($valueN1['descripcion']))==$campoId1){
			   $Id_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
			if($valueN1['descripcion']==$campoDescripcion1){
			   $Descripcion_N1=utf8_decode(trim(str_replace("","&nbsp;",$datoN1[strtolower($valueN1['descripcion'])])));
			}
	
		}

		//---------------------->LLENAR GRILLA<--------------------------------
		$this->SetFont('courier','B',$this->tam_letra_grilla);
		//Grilla
		$fill=TRUE;
		$cont = 0;
		$GrillaTemporal = array();
		$GrillaTemporal = $Grilla;

		$posicion_grilla=0;
		foreach($Grilla as $dato){
			 $FILA_DATOS = array();

			 reset($dataCampos);
	
			 if(($dato[strtolower($campoId1)])==$Id_N1){
				  foreach($dataCampos as $value){
					 $FILA_DATOS[] = utf8_decode($dato[strtolower($value['descripcion'])]);
				  }
				  if($origen=='VentaAgrupado'){
					$muestraresumen=true;
					$resumen['moneda']='TOTAL';
					$resumen['subtotal']=number_format($resumen['subtotal']+$dato['subtotal'],2,'.','');
					$resumen['igv']=number_format($resumen['igv']+$dato['igv'],2,'.','');
					$resumen['total']=number_format($resumen['total']+$dato['total'],2,'.','');
					//print_r($resumen);echo "resumen<br>";
					$resumenfinal['moneda']='TOTAL';
					$resumenfinal['subtotal']=number_format($resumenfinal['subtotal']+$dato['subtotal'],2,'.','');
					$resumenfinal['igv']=number_format($resumenfinal['igv']+$dato['igv'],2,'.','');
					$resumenfinal['total']=number_format($resumenfinal['total']+$dato['total'],2,'.','');
					//print_r($resumenfinal); echo "resumenfinal<br>";
				  }
	
					 if($comienzoN1){
						$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
					 }else{
						$N1_cont++;
						$this->SetFont('courier','B',$this->tam_letra_grilla+2);
						$this->Cell(10,10,$N1_cont.'.-'.$Descripcion_N1,0,1,'L');
						$this->Ln(2);
						//CREAMOS LA TABLA
						$this->LlenarTabla_Cabecera();
						$this->Row($FILA_DATOS,$fill,$this->tam_letra_grilla);
						$comienzoN1=true;
					 }
				  $fill=!$fill;
				  unset($GrillaTemporal[$posicion_grilla]);
			 }
			 $posicion_grilla++;
		}//cierra for
		
		//ELIMINA LOS ESPACIOS VACIOAS Q DEJA EL UNSET
		$GrillaTemporal = array_values($GrillaTemporal);
		$Grilla = $GrillaTemporal;
		
		if($muestraresumen){
			$resumen=array_values($resumen);
			$this->SetFont('courier','B',$this->tam_letra_grilla+2);
			$this->Row($resumen,true,$this->tam_letra_grilla+2);
			$muestraresumen=false;
		}

		if($comienzoN1==TRUE){
		   $this->Cell(array_sum($this->widths),0,'','T');
		   $this->Ln(5);
		}
	}//cierra for
	$resumenfinal=array_values($resumenfinal);
	$this->SetFont('courier','B',$this->tam_letra_grilla+2);
	$this->Row($resumenfinal,true,$this->tam_letra_grilla+2);
}

//FUNCIONES PARA ALINEACION
//Llenar UNA SOLA FILA, alinea las filas si ay Saltos de Linea
function Row($data,$fill,$tam_fuente,$alinear=true,$header=false,$par=true,$borde=''){
	//print_r($this->widths);
    $COLUMNA_SALTO = array();
    if($header){
        $this->SetFillColor(110,20,35);
    }else{
        if($par){
            $this->SetFillColor(110,20,35);
        }else{
            $this->SetFillColor(110,20,35);
        }
    }
    $this->SetDrawColor(0,0,0);
    //Calcula la altura de la fila
    $nb=0;
    for($i=0;$i<count($data);$i++){
        //Guardamos el Numero de Saltos por Cada Columna
        $COLUMNA_SALTO[]=$this->NbLines($this->widths[$i],trim($data[$i]));
	    $nb=max($nb,$this->NbLines($this->widths[$i],trim($data[$i])));
	}
	//Multiplica el Tama�o de Fuente por La Alto Maximo
	$h=$tam_fuente*$nb;
    //Emitir un salto de p�gina en primer lugar si es necesario
    $this->CheckPageBreak($h);
    //Dibuja las celdas de la Fila
    for($i=0;$i<count($data);$i++){
        $w=$this->widths[$i];
        $a=(isset($this->aligns[$i]) and $alinear) ? $this->aligns[$i] : 'C';
        //Guardar la posici�n actual
        $x=$this->GetX();
        $y=$this->GetY();
        //Dibuja el Borde
        //$this->Rect($x,$y,$w,$h,'F');
        //Imprime el Texto
        $this->SetLineWidth(0.3);
		$altura=$tam_fuente*($nb/$COLUMNA_SALTO[$i]);
        //Solo Minusculas
        //$this->MultiCell($w,$altura,(strtolower(trim($data[$i]))),'TRLB',$a,$fill);
        //Como sta en la BD
        if($header){
            //$this->SetFont("", "B",10);
            if($i>0 && $i<count($data)-1){
                $this->MultiCell($w,$altura,trim($data[$i]),($borde==''?'LR':$borde),$a,$fill);
            }else{
                $this->MultiCell($w,$altura,trim($data[$i]),($borde==''?'':$borde),$a,true);
            }
        }else{
            $this->SetFont("", "");
                if($i==0){
                    $this->MultiCell($w,$altura,trim($data[$i]),($borde==''?'T':$borde),$a,$fill);
                }elseif($i==count($data)-1){
                    $this->MultiCell($w,$altura,trim($data[$i]),($borde==''?'T':$borde),$a,$fill);
                }else{
                    $this->MultiCell($w,$altura,trim($data[$i]),($borde==''?'T':$borde),$a,$fill);
                }
        }
        //Pone la posici�n a la derecha de la celda
        $this->SetXY($x+$w,$y);
    }
    //Ir a la siguiente l�nea   SE MODIFICO PARA UN SALTO DE 0.5 DE SALTO DE LINEA
    $this->Ln($h*0.5);
    
}
//Emitir Salto de Pagina si es q Ay Desborde de Pagina
function CheckPageBreak($h){
    //Si la altura h podr�a causar un desbordamiento, a�adir una nueva p�gina de inmediato
    if($this->GetY()+$h*0.5>$this->PageBreakTrigger){
       $this->AddPage($this->CurOrientation);
	   $this->SetFont('courier','B',$this->tam_letra_cabecera+2);
       $this->LlenarTabla_Cabecera($this->cabecera_tabla);
       //Restablece La Fuente para la Sgte Pagina
       $this->SetFont('courier','B',$this->tam_letra_grilla);
    }
}
//Calcula el Numero de Saltos de Linea, Dependiendo del Ancho de Columna
function NbLines($w,$txt){
    //Calcula el n�mero de l�neas a MultiCell de ancho w se
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}

?>