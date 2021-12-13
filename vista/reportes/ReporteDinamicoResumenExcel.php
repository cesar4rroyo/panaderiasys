<?php
session_start();
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
	if($origen=='Venta'){
	    if($dato->estado!="I"){
		  $resumen['fecha']='TOTAL';
          $dato->subtotal=number_format($dato->total/1.18,2,'.','');
          $dato->igv=number_format($dato->total - $dato->subtotal,2,'.','');
		  $resumen['subtotal']=number_format($resumen['subtotal']+$dato->subtotal,2,'.','');
		  $resumen['igv']=number_format($resumen['igv']+$dato->igv,2,'.','');
		  $resumen['total']=number_format($resumen['total']+$dato->total,2,'.','');
	    }
    }elseif($origen=='Compra'){
		$resumen['moneda']='TOTAL';
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
        if(strtolower($value['descripcion'])=="subtotal"){
        	if($origen=='Venta'){
            	$dato["subtotal"]=number_format($dato["total"]/1.18,2,'.','');
            	$dato["igv"]=number_format($dato["total"] - $dato["igv"],2,'.','');
            }else{
            	$dato["subtotal"]=number_format($dato["total"],2,'.','');
            	$dato["igv"]=number_format(0,2,'.','');
            }
        }
        $registro.="<td style='$color' align='".$a[$cont]."'>".($dato[strtolower($value['descripcion'])])."</td>";
        $cont++;
    }
    $registro.="</tr>";
}

$resumen=array_values($resumen);
if($origen=='FlujoCaja' || $origen=='CajaChica'){
    $resumen2=array_values($resumen2);
    $resumen3=array_values($resumen3);
}
$registro.="<tr>";
for($i=0;$i<count($resumen);$i++){
    if($a[$i]=="C") $a[$i]="center";
    if($a[$i]=="L") $a[$i]="left";
    if($a[$i]=="R") $a[$i]="right";
    $registro.="<th align='".$a[$i]."'>".$resumen[$i]."</th>";
}
$registro.="</tr>";
if($origen=='FlujoCaja' || $origen=='CajaChica'){
    $registro.="<tr>";
    for($i=0;$i<count($resumen2);$i++){
        if($a[$i]=="C") $a[$i]="center";
        if($a[$i]=="L") $a[$i]="left";
        if($a[$i]=="R") $a[$i]="right";
        $registro.="<th align='".$a[$i]."'>".$resumen2[$i]."</th>";
    }
    $registro.="</tr>";
    $registro.="<tr>";
    for($i=0;$i<count($resumen3);$i++){
        if($a[$i]=="C") $a[$i]="center";
        if($a[$i]=="L") $a[$i]="left";
        if($a[$i]=="R") $a[$i]="right";
        $registro.="<th align='".$a[$i]."'>".$resumen3[$i]."</th>";
    }
    $registro.="</tr>";
}
echo $registro."</table>";
?>