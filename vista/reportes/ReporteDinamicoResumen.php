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
//$nro_hoja = $_POST['txtNroHojaREPORTE'];
$nro_hoja="-1";
//Nro de Registros a mostrar en la Grilla
$nro_reg=$_POST['txtNroRegistrosTotalREPORTE'];
//$nro_reg=99999999999;
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
    print_R($rst);
	exit();
}
if($nro_reg==0){
	echo "Sin Informaci&oacute;n";
	exit();
}
//------RESUMEN
//$fechaInicio=substr($filtro,strlen($filtro)-26,10);
//$fechaFin=substr($filtro,strlen($filtro)-13,10);
$fechaInicio=$_POST['txtFechaInicioREPORTE'];
$fechaFin=$_POST['txtFechaFinREPORTE'];
while($dato=$rst->fetchObject()){
        /*if($dato->modopago=="T" && $dato->idtipotarjeta==2){
            $dato->montocredito = $dato->total - $dato->totalpagado;
            $dato->montodebito = 0;
        }elseif($dato->modopago=="T" && $dato->idtipotarjeta==1){
            $dato->montocredito = 0;
            $dato->montodebito = $dato->total - $dato->totalpagado;
        }elseif($dato->modopago=="A"){
            $total = $dato->tarjetas;
            $total = explode("|", $total);
            $total_visa = explode("@", $total[0]);
            $dato->montodebito = $total_visa[1];
            $total_mastercard = explode("@", $total[1]);
            $dato->montocredito = $total_mastercard[1];
        }*/
	if($origen=='Venta'){
	    if($dato->estado!="I"){
		  $resumen['fecha']='TOTAL';
		  $resumen['subtotal']=number_format($resumen['subtotal']+$dato->subtotal,2,'.','');
		  $resumen['igv']=number_format($resumen['igv']+$dato->igv,2,'.','');
		  $resumen['total']=number_format($resumen['total']+$dato->total,2,'.','');
	    }
    }elseif($origen=='Compra'){
		$resumen['moneda']='TOTAL';
		if($resumen['subtotal']==""){
			$resumen['subtotal']=0;
			$resumen['igv']=0;
			$resumen['total']=0;
		}
		$resumen['subtotal']=number_format($resumen['subtotal']+$dato->subtotal,2,'.','');
		$resumen['igv']=number_format($resumen['igv']+$dato->igv,2,'.','');
		$resumen['total']=number_format($resumen['total']+$dato->total,2,'.','');
	}elseif($origen=='FlujoCaja'){
		$resumen['conceptopago']='TOTAL';
		$resumen['ingresos']=number_format($resumen['ingresos']+$dato->ingresos,2,'.','');
		$resumen['egresos']=number_format($resumen['egresos']+$dato->egresos,2,'.','');
		$resumen['persona']='SALDO';
		$resumen['comentario']=number_format($resumen['ingresos']-$resumen['egresos'],2,'.','');
        
        $resumen2['tipodocumento']='TARJETA DEBITO';
		$resumen2['conceptopago']=number_format($resumen2['conceptopago']+$dato->montodebito,2,'.','');
        $resumen2['ingresos']='TARJETA CREDITO';
		$resumen2['egresos']=number_format($resumen2['egresos']+$dato->montocredito,2,'.','');
		$resumen2['persona']='TOTAL';
		$resumen2['comentario']=number_format($resumen2['conceptopago']+$resumen2['egresos'],2,'.','');
        
        $resumen3['tipodocumento']='TOTAL EFECTIVO';
		$resumen3['conceptopago']=number_format($resumen['comentario'],2,'.','');
        $resumen3['ingresos']='TOTAL CREDITO';
		$resumen3['egresos']=number_format($resumen2['comentario'],2,'.','');
		$resumen3['persona']='SALDO EFECTIVO';
		$resumen3['comentario']=number_format($resumen['comentario']-$resumen2['comentario'],2,'.','');
                
		$fechaInicio=substr($filtro,strlen($filtro)-20,10);
		$fechaFin=date('d/m/Y');
	}elseif($origen=='CajaChica'){
            if($dato->idconceptopago==1){
                $monto_apertura = $dato->total;
            }
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
		$resumen3['egresos']=number_format($resumen['egresos']+$resumen2['comentario'],2,'.','');
		$resumen3['persona']='SALDO EFECTIVO';
		$resumen3['comentario']=number_format($resumen3['conceptopago']-$resumen3['egresos'],2,'.','');
        
		//$fechaInicio=substr($filtro,strlen($filtro)-18,10);
		$fechaInicio='';
		$fechaFin=date('d/m/Y');
	}elseif($origen=='Pedido'){
		$resumen['mesa']='TOTAL';
		$resumen['total']=number_format($resumen['total']+$dato->total,2,'.','');
	}elseif($origen=='VentaxMesoxSemana'){
		$resumen['ano']='TOTAL';
		$resumen['total']=number_format($resumen['total']+$dato->total,2,'.','');
		$fechaInicio=substr($filtro,strlen($filtro)-32,10);
		$fechaFin=substr($filtro,strlen($filtro)-19,10);
	}elseif($origen=='Gastos'){
		$resumen['conceptopago']='SALDO';
        if($dato->tipodocumento=="INGRESO"){
            $resumen['total']=number_format($resumen['total']+$dato->total,2,'.','');    
        }else{
            $resumen['total']=number_format($resumen['total']-$dato->total,2,'.','');
        }
		//$resumen['egresos']=number_format($resumen['egresos']+$dato->total,2,'.','');
		//$resumen['persona']='SALDO';
		//$resumen['comentario']=number_format($resumen['ingresos']-$resumen['egresos'],2,'.','');
        
		$fechaInicio='';
		$fechaFin=date('d/m/Y');
	}
	
}
//------RESUMEN
// UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
$pdf=new PDF_Dinamico('L','mm','A4');
$pdf->Open();
$title='Reporte '.$titulo;
$subtitle=($fechaInicio!=''?'Del: '.$fechaInicio:'').' al: '.$fechaFin;
//Primera p�gina
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
//CREAMOS LA TABLA
$pdf->LlenarTabla_Cabecera();
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
$pdf->LlenarTabla_Datos($rst2,$dataCampos);
$pdf->SetTextColor(0,0,0);
//------RESUMEN
$pdf->ln();
//print_r($resumen);
$resumen=array_values($resumen);
if($origen=='FlujoCaja' || $origen=='CajaChica'){
    $resumen2=array_values($resumen2);
    $resumen3=array_values($resumen3);
}
//print_r($resumen);
$pdf->Row($resumen,true,$pdf->tam_letra_grilla);
if($origen=='FlujoCaja' || $origen=='CajaChica'){
$pdf->Ln();
$pdf->Row($resumen2,true,$pdf->tam_letra_grilla);
$pdf->Row($resumen3,true,$pdf->tam_letra_grilla);
}
//------RESUMEN
$pdf->Output($clase.'.pdf','I');
?>