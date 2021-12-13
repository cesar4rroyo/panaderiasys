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
	if($titulo=='Producto Detallado'){
		if(strtolower($value['descripcion'])!='fechavencimiento' and strtolower($value['descripcion'])!='compartido' and strtolower($value['descripcion'])!='tipo'){
			$CABECERA[] = cambiaHTML($value['comentario']);
			$w[] = umill($value['longitudreporte']);
			$a[] = umill($value['alineacionreporte']);
			$resumen[strtolower($value['descripcion'])] = '';
		}
	}elseif($titulo=='Utilidad Producto'){
		if(strtolower($value['descripcion'])!='fechavencimiento' and strtolower($value['descripcion'])!='compartido' and strtolower($value['descripcion'])!='tipo'){
			if(strtolower($value['descripcion'])=='descripcion'){
				$CABECERA[] = cambiaHTML($value['comentario']);
				$w[] = 80;
				$a[] = umill($value['alineacionreporte']);
				$resumen[strtolower($value['descripcion'])] = '';
			}else{
				$CABECERA[] = cambiaHTML($value['comentario']);
				$w[] = umill($value['longitudreporte']);
				$a[] = umill($value['alineacionreporte']);
				$resumen[strtolower($value['descripcion'])] = '';
			}
		}
	}else{
		$CABECERA[] = cambiaHTML($value['comentario']);
		$w[] = umill($value['longitudreporte']);
		$a[] = umill($value['alineacionreporte']);
		$resumen[strtolower($value['descripcion'])] = '';
	}
}
if($titulo=='Producto Detallado'){
	$CABECERATABLA=$CABECERA;
	$CABECERATABLA[count($CABECERATABLA)]= "Unidad";
	$CABECERA[count($CABECERA)]= "unidad";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['unidad'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Clientes";
	$CABECERA[count($CABECERA)]= "veces";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['veces'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Precio Venta";
	$CABECERA[count($CABECERA)]= "precioventa";
	$w[count($w)] = "20";
	$a[count($a)] = "R";
	$resumen['precioventa'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Cantidad";
	$CABECERA[count($CABECERA)]= "monto";
	$w[count($w)] = "20";
	$a[count($a)] = "R";
	$resumen['monto'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "SubTotal";
	$CABECERA[count($CABECERA)]= "subtotal";
	$w[count($w)] = "20";
	$a[count($a)] = "R";
	$resumen['subtotal'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Puesto";
	$CABECERA[count($CABECERA)]= "puesto";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['puesto'] = '';
}elseif($titulo=='Utilidad Producto'){
	$CABECERATABLA=$CABECERA;
	$CABECERATABLA[count($CABECERATABLA)]= "Unidad";
	$CABECERA[count($CABECERA)]= "unidad";
	$w[count($w)] = "15";
	$a[count($a)] = "L";
	$resumen['unidad'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Clientes";
	$CABECERA[count($CABECERA)]= "veces";
	$w[count($w)] = "10";
	$a[count($a)] = "R";
	$resumen['veces'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Precio Prod.";
	$CABECERA[count($CABECERA)]= "preciocompra";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['preciocompra'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Precio Venta";
	$CABECERA[count($CABECERA)]= "precioventa";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['precioventa'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Cantidad";
	$CABECERA[count($CABECERA)]= "monto";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['monto'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "SubT Prod.";
	$CABECERA[count($CABECERA)]= "subtotalcompra";
	$w[count($w)] = "17";
	$a[count($a)] = "R";
	$resumen['subtotalcompra'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "SubT Venta";
	$CABECERA[count($CABECERA)]= "subtotalventa";
	$w[count($w)] = "17";
	$a[count($a)] = "R";
	$resumen['subtotalventa'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Utilidad";
	$CABECERA[count($CABECERA)]= "utilidad";
	$w[count($w)] = "18";
	$a[count($a)] = "R";
	$resumen['utilidad'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Puesto";
	$CABECERA[count($CABECERA)]= "puesto";
	$w[count($w)] = "10";
	$a[count($a)] = "R";
	$resumen['puesto'] = '';	
}elseif($titulo=='Producto'){
	$CABECERATABLA=$CABECERA;
	$CABECERATABLA[count($CABECERATABLA)]= "Unidad";
	$CABECERA[count($CABECERA)]= "unidad";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['unidad'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Cantidad";
	$CABECERA[count($CABECERA)]= "monto";
	$w[count($w)] = "20";
	$a[count($a)] = "R";
	$resumen['monto'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Clientes";
	$CABECERA[count($CABECERA)]= "veces";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['veces'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Puesto";
	$CABECERA[count($CABECERA)]= "puesto";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['puesto'] = '';
}elseif($titulo=='ProductoxDia'){
	$CABECERATABLA=$CABECERA;
	$CABECERATABLA[count($CABECERATABLA)]= "Dia";
	$CABECERA[count($CABECERA)]= "dia";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['unidad'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Fecha";
	$CABECERA[count($CABECERA)]= "fecha";
	$w[count($w)] = "20";
	$a[count($a)] = "R";
	$resumen['monto'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Cantidad";
	$CABECERA[count($CABECERA)]= "cantidad";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['veces'] = '';
	$CABECERATABLA[count($CABECERATABLA)]= "Puesto";
	$CABECERA[count($CABECERA)]= "orden";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['puesto'] = '';
}else{
	$CABECERA[count($CABECERA)]= "monto";
	$w[count($w)] = "20";
	$a[count($a)] = "R";
	$resumen['monto'] = '';
	$CABECERA[count($CABECERA)]= "veces";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['veces'] = '';
	$CABECERA[count($CABECERA)]= "puesto";
	$w[count($w)] = "15";
	$a[count($a)] = "R";
	$resumen['puesto'] = '';
	$CABECERATABLA=$CABECERA;
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
$fechaInicio=$_POST['txtFechaInicioREPORTE'];
$fechaFin=$_POST['txtFechaFinREPORTE'];
while($dato=$rst->fetchObject()){
	if($titulo=='Producto'){
		$resumen['marca']='TOTAL';
		$resumen['monto']=number_format($resumen['monto']+$dato->monto,2,'.','');
		$resumen['veces']=number_format($resumen['veces']+$dato->veces,0,'.','');
	}elseif($titulo=='Producto Detallado'){
		$resumen['marca']='TOTAL';
		$resumen['precioventa']=number_format($resumen['precioventa']+$dato->precioventa,2,'.','');
		$resumen['monto']=number_format($resumen['monto']+$dato->monto,2,'.','');
		$resumen['subtotal']=number_format($resumen['subtotal']+$dato->subtotal,2,'.','');
		$resumen['veces']=number_format($resumen['veces']+$dato->veces,0,'.','');
	}elseif($titulo=='Utilidad Producto'){
		$resumen['unidad']='TOTAL';
		$resumen['veces']=number_format($resumen['veces']+$dato->veces,0,'.','');
		$resumen['preciocompra']=number_format($resumen['preciocompra']+$dato->preciocompra,2,'.','');
		$resumen['precioventa']=number_format($resumen['precioventa']+$dato->precioventa,2,'.','');
		$resumen['monto']=number_format($resumen['monto']+$dato->monto,2,'.','');
		$resumen['subtotalcompra']=number_format($resumen['subtotalcompra']+$dato->subtotalcompra,2,'.','');
		$resumen['subtotalventa']=number_format($resumen['subtotalventa']+$dato->subtotalventa,2,'.','');
		$resumen['utilidad']=number_format($resumen['utilidad']+$dato->utilidad,2,'.','');
	}elseif($titulo=='Mesero'){
		$resumen['departamento']='TOTAL';
		$resumen['monto']=number_format($resumen['monto']+$dato->monto,2,'.','');
		$resumen['veces']=number_format($resumen['veces']+$dato->veces,0,'.','');
	}elseif($titulo=='Cliente'){
		$resumen['departamento']='TOTAL';
		$resumen['monto']=number_format($resumen['monto']+$dato->monto,2,'.','');
		$resumen['veces']=number_format($resumen['veces']+$dato->veces,0,'.','');
	}elseif($titulo=='Cajero'){
		$resumen['departamento']='TOTAL';
		$resumen['monto']=number_format($resumen['monto']+$dato->monto,2,'.','');
		$resumen['veces']=number_format($resumen['veces']+$dato->veces,0,'.','');
	}
}
//------RESUMEN
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Reporte Tops $titulo.xls\"");
$registro="<table border='1'><tr><th colspan='".count($CABECERA)."'>";
$title='Reporte Tops '.$titulo;
$registro.=$title."</th></tr>";
$registro.="<tr><th colspan='".count($CABECERATABLA)."'>";
$subtitle=($fechaInicio!=''?'Del: '.$fechaInicio:'').' al: '.$fechaFin;
$registro.=$subtitle."</th></tr><tr>";
for($i=0;$i<count($CABECERATABLA);$i++){
    if($a[$i]=="C") $a[$i]="center";
    if($a[$i]=="L") $a[$i]="left";
    if($a[$i]=="R") $a[$i]="right";
    $registro.="<th align='".$a[$i]."'>".$CABECERATABLA[$i]."</th>";
}
$registro.="</tr>";
//Creamos el Objeto de Datos y lo enviamos al Llenar Datos
eval("\$rst2 = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
while($dato = $rst2->fetch()){
    $cont = 0;
    $nro_registros_total = $dato["nrototal"];
    reset($CABECERA);
    $registro.="<tr>";
    foreach($CABECERA as $value){
        if($a[$cont]=="C") $a[$cont]="center";
        if($a[$cont]=="L") $a[$cont]="left";
        if($a[$cont]=="R") $a[$cont]="right";
        $registro.="<td align='".$a[$cont]."'>".($dato[strtolower($value)])."</td>";
        $cont++;
    }
    $registro.="</tr>";
}

$resumen=array_values($resumen);
$registro.="<tr>";
for($i=0;$i<count($resumen);$i++){
    if($a[$i]=="C") $a[$i]="center";
    if($a[$i]=="L") $a[$i]="left";
    if($a[$i]=="R") $a[$i]="right";
    $registro.="<th align='".$a[$i]."'>".$resumen[$i]."</th>";
}
$registro.="</tr>";
echo $registro."</table>";
?>