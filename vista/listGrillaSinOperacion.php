<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
$clase = $_GET["clase"];
$id_clase = $_GET["id_clase"];
if(isset($_GET['funcion'])){
	$funcion=$_GET['funcion'];
}else{
	$funcion='';
}
if(isset($_GET['idgrilla'])){
	$idgrilla=$_GET['idgrilla'];
}else{
	$idgrilla='';
}
if(isset($_GET['imprimir'])){
	$imprimir=$_GET['imprimir'];
}else{
	$imprimir='NO';
}
if(isset($_GET['tiporeporte'])){
	$tiporeporte=$_GET['tiporeporte'];
}else{
	$tiporeporte='Dinamico';
}
if(isset($_GET['titulotabla'])){
	$titulotabla=$_GET['titulotabla'];
}else{
	$titulotabla='';
}
if(isset($_GET['titulo'])){
	$titulo=$_GET['titulo'];
}else{
	$titulo='';
}
if(isset($_GET['origen'])){
	$origen=$_GET['origen'];
}else{
	$origen='';
}
if(isset($_GET['grafico'])){
	$grafico=$_GET['grafico'];
}else{
	$grafico='';
}
if(isset($_GET['linkgrafico'])){
	$linkgrafico=$_GET['linkgrafico'];
}else{
	$linkgrafico='SI';
}
if(isset($_GET['datografico'])){
	$datografico=$_GET['datografico'];
}else{
	$datografico='nombres';
}
if(isset($_GET['tamanografico'])){
	$tamanografico=$_GET['tamanografico'];
}else{
	$tamanografico='';
}
if(isset($_GET['solografico'])){
	$solografico=$_GET['solografico'];
}else{
	$solografico='';
}
if(isset($_GET['sesiongrafico'])){
	$sesionGrafico=$_GET['sesiongrafico'];
}else{
	$sesionGrafico='datagrafico';
}
if(isset($_GET['accionvermas'])){
	$accionvermas=$_GET['accionvermas'];
}else{
	$accionvermas='';
}
if(isset($_GET['fechainicio'])){
	$fechainicio=$_GET['fechainicio'];
}else{
	$fechainicio='';
}
if(isset($_GET['fechafin'])){
	$fechafin=$_GET['fechafin'];
}else{
	$fechafin='';
}
if(isset($_GET['ocultarcampos'])){
	$ocultarcampos=split('-',$_GET['ocultarcampos']);
}else{
	$ocultarcampos='';
}

//Requiere para Ejecutar Clase
eval("require(\"../modelo/cls".$clase.".php\");");

//Nro de Hoja a mostrar en la Grilla
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){//Si no se envia muestra Hoja Nro 1
	$nro_hoja = 1;
}
//Nro de Registros a mostrar en la Grilla
$nro_reg = $_GET["nro_reg"];
if(!$nro_reg){//Si no se envia muestra lo de la session
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
<html>
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
<div class="row">
		<div class="col s12">
		    <table class="striped bordered highlight">
		        <thead>
		            <tr>
<?php
//>>Inicio Obtiene Campos a mostrar
$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "<td colspan=100>Error al obtener campos a mostrar</td></tr><tr><td colspan=100>".$rstCampos."</td>";
	echo "</tr></thead></table>";
	exit();
}
$dataCampos = $rstCampos->fetchAll();
$nro_cam=count($dataCampos);
foreach($dataCampos as $value){
	if($ocultarcampos!=''){
		if(!in_array(strtolower($value['descripcion']),$ocultarcampos)){
			?>
                        <th class="center" onClick="javascript:ordenar('<?php echo umill($value['descripcion']);?>');"><?php echo umill($value['comentario'])?></th>
			<?php 
		}
	}else{
	?>
        <th class="center" onClick="javascript:ordenar('<?php echo umill($value['descripcion']);?>');"><?php echo umill($value['comentario'])?></th>
	<?php
	}
}
//<<Fin
?>
</tr>
        </thead>
        <tbody>
<?php
//>>Inicio Ejecutando la consulta
if($id_clase=="44" || $_GET["historico"]=="SI"){
    $nro_hoja="'h".$nro_hoja."'";    
}
eval("\$rst = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
if($id_clase=="44"){
      $nro_hoja=substr($nro_hoja,2,strlen($nro_hoja)-2);
}
//print_r("\$rst = \$objGrilla->consultar".$clase.$funcion."(".$nro_reg.",".$nro_hoja.$filtro);
//print_R($rst);
if(is_string($rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	echo "</tr></tbody></table>";
	exit();
}
$nro_registros_total=0;
$c=0;
while($dato = $rst->fetch()){
	$nro_registros_total = $dato["nrototal"];
	reset($dataCampos);
	$c+=1;
?>
<tr class="hoverable">
<?php
	foreach($dataCampos as $value){
		if($ocultarcampos!=''){
			if(!in_array(strtolower($value['descripcion']),$ocultarcampos)){
			?>
                        <td class="center" <?php if($dato['estado']=='I') echo 'style="color:#F00"';?>><?php echo umill($dato[strtolower($value['descripcion'])])?></td>
			<?php
			}
		}else{
			?>
                        <td class="center" <?php if($dato['estado']=='I') echo 'style="color:#F00"';?>><?php echo umill($dato[strtolower($value['descripcion'])])?></td>
			<?php
		}
	}
?>
</tr>
<?php
    if($grafico=='SI'){
	$datosgrafico[$dato[strtolower($datografico)]]=$dato['total'];
    }
}
if($grafico=='SI'){
$_SESSION[$sesionGrafico]=$datosgrafico;
}
if($nro_registros_total==0){
	echo "<tr><td colspan=100>Sin Informaci&oacute;n</td></tr>";
}
?>
</tbody></table>
                                        </div>
                                    </div>
                                    <div class="row">
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
<div class="col s12 m12 l2 right right-align"><?php if($nro_registros_total==0){echo "No hay registros";}else{echo "Registros del $inicio al $fin (".$mostrar.") de ".$nro_registros_total;}?></div>
<div class="col s12 m12 l8 offset-l2 center">
        <ul class="pagination">
            <li class="disabled"><a href="#!"><i class='material-icons'>chevron_left</i></a></li>
<?php
$ini = '<li class="waves-effect"><a href="#!"  onClick="buscarGrilla(';
$medio=');">';
if($nro_hojas>11){
	for($i=1;$i<=3;$i++){
		//if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td>".$i."</td>";}
                if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
	}
	if($nro_hojas % 2 == 0){
		$mitad = (int)($nro_hojas/2);
	}else{
		$mitad = (int)($nro_hojas/2) + 1;
	}
	if($nro_hoja>3 && $nro_hoja <= $nro_hojas-3){
		if($nro_hoja > 6 && $nro_hoja < $nro_hojas - 5){
			if($nro_hoja!=4){echo $ini.'4'.$medio."<i class='material-icons'>chevron_right</i></a></li>";}else{ echo "<li><i class='material-icons'>chevron_right</i></li>";}
			for($i=$nro_hoja-2;$i<$nro_hoja;$i++){
				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
			}	
			for($i=$nro_hoja;$i<=$nro_hoja+2;$i++){
				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
			}	
			if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<i class='material-icons'>chevron_left</i></a></li>";}else{ echo "<li><i class='material-icons'>chevron_left</i></li>";}
		}else{
			if($nro_hoja>=4 && $nro_hoja<=6){
				for($i=4;$i<=8;$i++){
					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
				}
				if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<i class='material-icons'>chevron_left</i></a></li>";}else{ echo "<li><i class='material-icons'>chevron_left</i></li>";}
			}else{
				if($nro_hoja!=4){echo $ini.'4'.$medio."<i class='material-icons'>chevron_right</i></a></li>";}else{ echo "<li><i class='material-icons'>chevron_right</i></li>";}
				for($i=$nro_hojas-7;$i<=$nro_hojas-3;$i++){
					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
				}
			}
		}
	}else{
		if($nro_hoja!=4){echo $ini.'4'.$medio."<i class='material-icons'>chevron_right</i></a></li>";}else{ echo "<li><i class='material-icons'>chevron_right</i></li>";}
		for($i=(int)$mitad-2;$i<=(int)$mitad+2;$i++){
			if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
		}
		if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<i class='material-icons'>chevron_left</i></a></li>";}else{ echo "<li><i class='material-icons'>chevron_left</i></li>";}
	}
	for($i=(int)$nro_hojas-2;$i<=(int)$nro_hojas;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
	}
}else{
	for($i=1;$i<=$nro_hojas;$i++){
		if($nro_hoja!=$i){echo $ini.$i.$medio.$i.""."</a></li>";}else{ echo "<li class='active'>".$i."</li>";}
	}
}
?>
                <li class="disabled"><a href="#!"><i class='material-icons'>chevron_right</i></a></li>
            </ul>
        </div>
    </div>  
<?php if($imprimir=='SI'){?>
    <div class="row">
        <form id="frmDatosReporte" method="post" target="_blank" action="vista/reportes/Reporte<?php echo $tiporeporte;?>.php">
            <input id="txtOrigenREPORTE" name="txtOrigenREPORTE" type="hidden" value="<?php echo $origen;?>">
            <input id="txtTituloREPORTE" name="txtTituloREPORTE" type="hidden" value="<?php echo utf8_encode($titulo);?>">
            <input id="txtClaseREPORTE" name="txtClaseREPORTE" type="hidden" value="<?php echo $clase;?>">
            <input id="txtIdClaseREPORTE" name="txtIdClaseREPORTE" type="hidden" value="<?php echo $id_clase;?>">
            <input id="txtFuncionREPORTE" name="txtFuncionREPORTE" type="hidden" value="<?php echo $funcion;?>">
            <input id="txtFiltroREPORTE" name="txtFiltroREPORTE" type="hidden" value="<?php echo $filtro;?>">
            <input id="txtNroRegistrosTotalREPORTE" name="txtNroRegistrosTotalREPORTE" type="hidden" value="<?php echo $nro_registros_total;?>">
            <?php if($id_clase=="44"){$nro_hoja="h".$nro_hoja;}?>
            <input id="txtNroHojaREPORTE" name="txtNroHojaREPORTE" type="hidden" value="<?php echo $nro_hoja;?>">
            <?php if($id_clase=="44"){ $nro_hoja=substr($nro_hoja,1,strlen($nro_hoja)-1);}?>
            <input id="txtFechaInicioREPORTE" name="txtFechaInicioREPORTE" type="hidden" value="<?php echo $fechainicio;?>">
            <input id="txtFechaFinREPORTE" name="txtFechaFinREPORTE" type="hidden" value="<?php echo $fechafin;?>">
            <input id="txtOcultarCamposREPORTE" name="txtOcultarCamposREPORTE" type="hidden" value="<?php echo $_GET['ocultarcampos'];?>">
            <div class="col s12" style="padding-bottom: 10px;">
                <button type="button" class="btn-large right" onClick="javascript:document.getElementById('frmDatosReporte').action='vista/reportes/Reporte<?php echo $tiporeporte;?>.php';document.getElementById('frmDatosReporte').submit();">IMPRIMIR<i class="material-icons right">print</i></button>
                <button type="button" class="btn-large right green" style="margin-right: 10px;" onClick="javascript:document.getElementById('frmDatosReporte').action='vista/reportes/Reporte<?php echo $tiporeporte;?>Excel.php';document.getElementById('frmDatosReporte').submit();">EXCEL<i class="material-icons right">description</i></button>
            </div>
        </form>
    </div>
<?php }?>
<?php if($grafico=='SI' and $linkgrafico=='SI'){?>
    <div class="row">
        <div class="col s12 center">
            <button type="button" class="orange btn-floating btn-large tooltipped" data-position="top" data-delay="50" data-tooltip="VER GRAFICO" onClick="grafico();"><i class="material-icons large">assessment</i></button>
        </div>
    </div>
<?php }?>
<?php if($grafico=='SI'){?>
    <?php if($nro_registros_total==0){
            echo '
    <div class="row">
        <div class="col s12 center" id="divGrafico'.$idgrilla.'">
            <div class="GraficoInterno">Sin Informaci&oacute;n</div>
        </div>';
    }else{?>
    <div class="row">
        <div class="col s12 center" id="divGrafico<?php echo $idgrilla;?>" style="display:none">
            <?php 
            if($tamanografico=='S'){//Pequeño
                    $anchoGrafico=400;
                    $altoGrafico=250;
            }elseif($tamanografico=='M'){//Mediano
                    $anchoGrafico=800;
                    $altoGrafico=500;
            }
            $aleatorio = rand (1,1000000);
            ?>
            <div class="GraficoInterno">
                <img src='vista/graficos/grafico.php?rand=<?php echo $aleatorio;?>&sesiongrafico=<?php echo $sesionGrafico;?>&anchoGrafico=<?php echo $anchoGrafico;?>&altoGrafico=<?php echo $altoGrafico;?>'/>
            </div>
        </div>
    <?php }?>
        <?php if($accionvermas!=''){?>
        <button type="button" class="btn-floating right btn-large tooltipped yellow darken-4" data-position="bottom" data-delay="50" data-tooltip="VER MAS" onClick="<?php echo $accionvermas;?>"><i class="material-icons <?php echo $operacion["color"];?>-text text-darken-4">search</i></button>
        <?php }?>
    </div>
<?php }else{?>
    <?php if($accionvermas!=''){?>
    <button type="button" class="btn-floating right btn-large tooltipped yellow darken-4" data-position="bottom" data-delay="50" data-tooltip="VER MAS" onClick="<?php echo $accionvermas;?>"><i class="material-icons <?php echo $operacion["color"];?>-text text-darken-4">search</i></button>
    <?php }
}
?>
</body>
</html>
<script>
function grafico(){
	if(divGrafico.style.display=='none'){
		divGrafico.style.display=''
	}else{
		divGrafico.style.display='none'
	}
}
function buscarGrilla(nro_hoja){
	if(document.getElementById("nro_hoj")){
		document.getElementById("nro_hoj").value = nro_hoja;
	}
	buscar();
}
<?php if($solografico=='SI'){?>
document.getElementById('divGrafico<?php echo $idgrilla;?>').style.display='';
document.getElementById('tabla<?php echo $idgrilla;?>').style.display='none';
document.getElementById('paginacion<?php echo $idgrilla;?>').style.display='none';
document.getElementById('paginacion2<?php echo $idgrilla;?>').style.display='none';
document.getElementById('lnkgrafico<?php echo $idgrilla;?>').style.display='none';
<?php }?>
</script>