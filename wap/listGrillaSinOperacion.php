<?php
//Nombre y Codigo de la Clase a Ejecutar
$clase = $_GET["clase"];
$id_clase = $_GET["id_clase"];
//Requiere para Ejecutar Clase
eval("require(\"../modelo/cls".$clase.".php\");");

//Nro de Hoja a mostrar en la Grilla
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){//Si no se envia muestra Hoja Nro 1
	$nro_hoja = 1;
}
//Nro de Registros a mostrar en la Grilla
$nro_reg = $_SESSION["R_NroFilaMostrar"];

//Para el Filtro
$filtro_str = $_GET["filtro"];
$filtro = str_replace("\'", "'", $filtro_str);
if(!$filtro){//Si esta vacio cierra busqueda
	$filtro = ");";
}else{//Agrega filtro y cierra busqueda
	$filtro = ", ".$filtro.");";
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
</head>
<body>
<?php
//Instancia la Clase
eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//Para ver que es lo que consulta
//echo "\$rst = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro;
?>
<?php require("tablaheader.php");?>
<table class="tablaint">
<tr>
<?php
//>>Inicio Obtiene Campos a mostrar
$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "<td colspan=100>Error al obtener campos a mostrar</td></tr><tr><td colspan=100>".$rstCampos."</td>";
	echo "</tr></table>";
	exit();
}
$dataCampos = $rstCampos->fetchAll();
$nro_cam=count($dataCampos);
foreach($dataCampos as $value){
?>
<th><a href="#" onClick="javascript:ordenar('<?php echo umill($value['descripcion']);?>');"><?php echo umill($value['comentario'])?></a></th>
<?php 
}
//<<Fin
?>
</tr>
<?php
//>>Inicio Ejecutando la consulta
eval("\$rst = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro);
if(is_string($rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	echo "</tr></table>";
	exit();
}
$nro_registros_total=0;
$c=0;
while($dato = $rst->fetch()){
	$nro_registros_total = $dato["nrototal"];
	reset($dataCampos);
	$c+=1;
?>
<tr class="<?php if($c%2==0) echo 'par'; else echo 'impar';?>">
<?php
	foreach($dataCampos as $value){
?>
<td ><?php echo umill($dato[strtolower($value['descripcion'])])?></td>
<?php
	}
?>
</tr>
<?php
}
if($nro_registros_total==0){
	echo "<tr><td colspan=100>Sin Informaci&oacute;n</td></tr>";
}
?>
</table>
<?php
if($nro_reg==0){
$nro_reg = $nro_registros_total;
}
if($nro_registros_total % $nro_reg == 0){
	$nro_hojas = (int)($nro_registros_total/$nro_reg);
}else{
	$nro_hojas = (int)($nro_registros_total/$nro_reg) + 1;
}
if($nro_hojas<$nro_hoja){
	$nro_hoja=1;
}
if($nro_hoja==$nro_hojas){
	$mostrar = $nro_registros_total % $nro_reg;
}else{
	$mostrar  = $nro_reg;
}
$inicio=($nro_hoja - 1)*$nro_reg + 1;
$fin=($nro_hoja - 1)*$nro_reg + $mostrar;
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td>
<table class="tablaPaginacion">
<tr>
<?php
$ini = "<td><a href=\"#\" onClick=\"buscarGrilla(";
$medio=")\">";
if($nro_hojas>11){
	for($i=1;$i<=3;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
	}
	if($nro_hojas % 2 == 0){
		$mitad = (int)($nro_hojas/2);
	}else{
		$mitad = (int)($nro_hojas/2) + 1;
	}
	if($nro_hoja>3 && $nro_hoja <= $nro_hojas-3){
		if($nro_hoja > 6 && $nro_hoja < $nro_hojas - 5){
			if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
			for($i=$nro_hoja-2;$i<$nro_hoja;$i++){
				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
			}	
			for($i=$nro_hoja;$i<=$nro_hoja+2;$i++){
				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
			}	
			if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
		}else{
			if($nro_hoja>=4 && $nro_hoja<=6){
				for($i=4;$i<=8;$i++){
					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
				}
				if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
			}else{
				if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
				for($i=$nro_hojas-7;$i<=$nro_hojas-3;$i++){
					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
				}
			}
		}
	}else{
		if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
		for($i=(int)$mitad-2;$i<=(int)$mitad+2;$i++){
			if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
		}
		if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
	}
	for($i=(int)$nro_hojas-2;$i<=(int)$nro_hojas;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
	}
}else{
	for($i=1;$i<=$nro_hojas;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i.""."</a></td>";}else{ echo "<td>".$i."</td>";}
	}
}
?>
<td><div id="cargando"></div></td>
</tr>
</table>
</td><td align="right">
<table><tr><td width="100%" align="right"><?php if($nro_registros_total==0){echo "No se hay registros";}else{echo "Registros del $inicio al $fin (".$mostrar.") de ".$nro_registros_total;}?></td></tr></table>
</td></tr></table>
<?php require("tablafooter.php");?>
</body>
</HTML>
<script>
function buscarGrilla(nro_hoja){
	if(document.getElementById("nro_hoj")){
		document.getElementById("nro_hoj").value = nro_hoja;
	}
	buscar();
}
</script>