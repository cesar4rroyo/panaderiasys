<?php
//Nombre y Codigo de la Clase a Ejecutar
$clase = $_GET["clase"];
$id_clase = $_GET["id_clase"];
$nombre = $_GET["nombre"];
//Uso la variable nombre para el ordenar y paginar
//Requiere para Ejecutar Clase
eval("require(\"../modelo/cls".$clase.".php\");");

//Nro de Hoja a mostrar en la Grilla
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){//Si no se envia muestra Hoja Nro 1
	$nro_hoja = 1;
}
//Nro de Registros a mostrar en la Grilla
$nro_reg = $_GET["nro_reg"];
if(!$nro_reg){//Si no se envia muestra segun session
	$nro_reg = $_SESSION["R_NroFilaMostrar"];
}

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
//echo "\$rst = \$objGrilla->consultar".$clase."Interna(".$nro_reg.",".$nro_hoja.$filtro;
?>
<input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
<input type="hidden" id="txtMesa" value="<?=$_GET["txtMesa"]?>" />
<input type="hidden" id="categoria" value="<?=$_GET["categoria"]?>" />
<!--<h3 align="center">PLATOS - <?=$_GET["categoria"]?></h3>-->
<table align="center"><tr><td>
<table id="tabla<?php echo $nombre?>" class="tablaint">
<tr>
<th class="zoom" colspan="4"><?=$_GET["categoria"]?></th>
</tr>

<?php
//>>Inicio Obtiene Operaciones
$rstOperaciones = $objGrilla->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obtener Operaciones</td></tr><tr><td colspan=100>".$rstCampos."</td>";
	echo "</tr></table>";
	exit();
}
$datoOperaciones = $rstOperaciones->fetchAll();
//<<Fin

//>>Inicio Obtiene Campos a mostrar
$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "<td colspan=100>Error al obtener campos a mostrar</td></tr><tr><td colspan=100>".$rstCampos."</td>";
	echo "</tr></table>";
	exit();
}
$dataCampos = $rstCampos->fetchAll();
/*foreach($dataCampos as $value){
?>
<th><?php echo umill($value['comentario'])?></th>
<?php 
}
//<<Fin
$nro_cam=count($datoOperaciones);
?>
<th colspan="<?php echo $nro_cam;?>"><a>Operaciones</a></th>
</tr>
<?php 
*/
//>>Inicio Ejecutando la consulta
//echo "\$rst = \$objGrilla->consultar".$clase."Interna(".$nro_reg.",".$nro_hoja.$filtro;
eval("\$rst = \$objGrilla->consultar".$clase."Interna(".$nro_reg.",".$nro_hoja.$filtro);
if(is_string($rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	echo "</tr></table>";
	exit();
}
$nro_registros_total=0;
$c=0;
//PRINT_R($rst);
while($dato = $rst->fetch()){
	$nro_registros_total = $dato["nrototal"];
	reset($dataCampos);
	$c+=1;
    if(($c%2-1)==0){
?>
<tr id='<?php echo $dato[1];?>-<?php echo $dato[2];?>' class="<?php echo 'impar';?>">
<?php
}
	//foreach($dataCampos as $value){--->dato[33]indica la abreviatura, el 4 indica la descripcion
?>
<td width="50%"><input class="" style="font-size: 25px;font-weight: bold;width: auto;" type="button" style="text-transform:uppercase;" value="<?php echo substr(umill($dato[4]),0,26)?>" onclick="seleccionar(<?php echo $dato[1];?>,<?php echo $dato[2];?>);"/></td>
<?php
if($c%2==0){
    echo "</tr>";
}

	/*}
	//reset($datoOperaciones);
	//foreach($datoOperaciones as $operacion){
	//	if($operacion["tipo"]=="C"){
		?>
<td><a href="#" title="<?php echo umill($operacion["comentario"]);?>" onClick="javascript:<?php echo umill($operacion["accion"]);?>(<?php echo $dato[1];?>,<?php echo $dato[2];?>);"><?php echo umill($operacion["descripcion"]);?></a></td>
		<?php
		//}
	}*/
?>
<?php
}
if($nro_registros_total==0){
	echo "<tr><td class='zoom21' colspan=100>Sin Informaci&oacute;n</td></tr>";
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
$ini = "<td class='zoom21'><a style='font-size: 23px;' href=\"#\" onClick=\"buscarGrillaInterna(";
$medio=")\">";
if($nro_hojas>11){
	for($i=1;$i<=3;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
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
				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
			}	
			for($i=$nro_hoja;$i<=$nro_hoja+2;$i++){
				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
			}	
			if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
		}else{
			if($nro_hoja>=4 && $nro_hoja<=6){
				for($i=4;$i<=8;$i++){
					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
				}
				if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
			}else{
				if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
				for($i=$nro_hojas-7;$i<=$nro_hojas-3;$i++){
					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
				}
			}
		}
	}else{
		if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
		for($i=(int)$mitad-2;$i<=(int)$mitad+2;$i++){
			if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
		}
		if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
	}
	for($i=(int)$nro_hojas-2;$i<=(int)$nro_hojas;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
	}
}else{
	for($i=1;$i<=$nro_hojas;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i.""."</a></td>";}else{ echo "<td class='zoom3'>".$i."</td>";}
	}
}
?>
<td><div id="cargando"></div></td>
</tr>
</table>
</td><td align="right">
<table><tr><td class="zoom2" width="100%" align="right"><?php if($nro_registros_total==0){echo "No hay registros";}else{echo "Registros del $inicio al $fin (".$mostrar.") de ".$nro_registros_total;}?></td><td></td></tr></table>
</td></tr></table>
</td></tr></table>
</body>
</HTML>

<script>
function inicio(){
    document.getElementById("url").value="vista/frmComanda";
    document.getElementById("par").value="&mesa=<?=$_GET["mesa"]?>";
    document.getElementById("div").value="frame";
    document.getElementById("msj").value="frame";
    document.getElementById("img").value="imgloading";
}

function buscarGrillaInterna(nro_hoja){
	if(document.getElementById("nro_hoj")){
		document.getElementById("nro_hoj").value = nro_hoja;
	}
	setRun('vista/listplatos','&mesa=<?=$_GET["mesa"]?>&categoria=<?=$_GET["categoria"]?>&idcategoria=<?=$_GET["idcategoria"]?>&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=45&filtro=<?=$_GET["filtro"]?>', 'cargagrilla', 'cargagrilla', 'img03');
}

function seleccionar(idproducto,idsucursalproducto){
    document.getElementById("cargagrilla").innerHTML="";
    setRun('vista/frmProductoSeleccionado','&mesa=<?=$_GET["mesa"]?>&categoria=<?=$_GET["categoria"]?>&idcategoria=<?=$_GET["idcategoria"]?>&idproducto='+idproducto+'&idsucursalproducto='+idsucursalproducto+'&clase=Producto&nombre=Producto&id_clase=45&filtro=<?=$_GET["filtro"]?>', 'cargagrilla', 'cargagrilla', 'img03');
}
inicio();
</script>