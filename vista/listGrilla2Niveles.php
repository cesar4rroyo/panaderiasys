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

$clase2 = $_GET["clase2"];
$id_clase2 = $_GET["id_clase2"];
if(isset($_GET['funcion2'])){
	$funcion2=$_GET['funcion2'];
}else{
	$funcion2='';
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

require('fun.php');
//Requiere para Ejecutar Clase
eval("require(\"../modelo/cls".$clase.".php\");");
eval("require(\"../modelo/cls".$clase2.".php\");");

//Nro de Hoja a mostrar en la Grilla
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){//Si no se envia muestra Hoja Nro 1
	$nro_hoja = 1;
}
//Nro de Registros a mostrar en la Grilla
$nro_reg = 10;

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
<?php /*
if($_GET['zoom']=='SI'){
	$_SESSION['titulo']='PEDIDO DE COMENSALES';
	require("tablaheaderzoom.php");
	echo '<table border="0" width="100%" height="100%"><tr><td>';
}else{
	require("tablaheader.php");
}*/
?>
	<div class="row">
	    <div class="col s12">
			<table class="striped bordered">
		      <thead>
		          <tr>
<?php
//>>Inicio Obtiene Operaciones
$rstOperaciones = $objGrilla->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obtener Operaciones</td></tr><tr><td colspan=100>".$rstCampos."</td>";
	echo "</tr></thead></table>";
	exit();
}
$datoOperaciones = $rstOperaciones->fetchAll();
//<<Fin

//>>Inicio Obtiene Campos a mostrar
$rstCampos = $objGrilla->obtenerCamposMostrar("G");
if(is_string($rstCampos)){
	echo "<td colspan=100>Error al obtener campos a mostrar</td></tr><tr><td colspan=100>".$rstCampos."</td>";
	echo "</tr></thead></table>";
	exit();
}
$dataCampos = $rstCampos->fetchAll();
$nro_campos=count($dataCampos);
foreach($dataCampos as $value){
	if($value['descripcion']=='Fecha'){
		?>
		<th class="center <?php if($_GET['zoom']=='SI') echo 'zoom';?>" onClick="javascript:ordenar('tiempotranscurrido');">Tiempo Trascurrido</th>
		<?php 
	}else{
		?>
		<th class="center <?php if($_GET['zoom']=='SI') echo 'zoom';?>" onClick="javascript:ordenar('<?php echo umill($value['descripcion']);?>');"><?php echo umill($value['comentario'])?></th>
		<?php 
	}
}
//<<Fin
$nro_cam=count($datoOperaciones);
if($nro_cam>0){?><th class="center">Operaciones</th><?php }?>
			</tr>
        </thead>
      <tbody>
<?php
//>>Inicio Ejecutando la consulta
eval("\$rst = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro);
if(is_string($rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	echo "</tr></tbody></table>";
	exit();
}
$nro_registros_total=0;
$c=1;
while($dato = $rst->fetch()){
	$nro_registros_total = $dato["nrototal"];
	reset($dataCampos);
	//$c+=1;
?>
<tr class="hoverable">
<?php
	foreach($dataCampos as $value){
		if($value['descripcion']=='Fecha'){
			$transcurrio=str_replace('days','d&iacute;as',$dato['tiempotranscurrido']);
			$transcurrio=preg_replace('/:/',' horas ',$transcurrio,1);
			$transcurrio=preg_replace('/:/',' minutos ',$transcurrio,1);
			$transcurrio=preg_replace('/\.\d*/',' segundos',$transcurrio,1);
			$transcurrio=str_replace('00 horas','',$transcurrio);
			$transcurrio=str_replace('00 minutos','',$transcurrio);
			?>
            <td class="center <?php if($_GET['zoom']=='SI') echo 'zoom';?>"><?php echo $transcurrio;?></td>
            <?php
		}else{
		?>
		<td class="center <?php if($_GET['zoom']=='SI') echo 'zoom';?>"><?php echo umill($dato[strtolower($value['descripcion'])]);?></td>
		<?php
		}
	}?>
	<td class="center">
				<div class="col s12">
	<?php
	reset($datoOperaciones);
	foreach($datoOperaciones as $operacion){
		if($operacion["tipo"]=="C"){
                    $ocultaO=split(',',$_GET['ocultaope']);
                    if($operacion["idoperacion"]==2){
                            $operacion["imagen"] = "edit";
                            $operacion["color"] = "light-green";
                            $operacion["param"] = $dato['idmesa'].",'".$dato['mesa']."'";
                    }elseif($operacion["idoperacion"]==3){
                            $operacion["imagen"] = "clear";
                            $operacion["color"] = "red";
                            $operacion["param"] = $dato[1];
                    }elseif($operacion["idoperacion"]==5){
                            $operacion["imagen"] = "content_paste";
                            $operacion["color"] = "purple";
                            $operacion["param"] = $dato[1].",'".$dato['mesa']."'";
                    }elseif($operacion["idoperacion"]==7){
                            $operacion["imagen"] = "motorcycle";
                            $operacion["color"] = "teal";
                            $operacion["param"] = $dato[1];
                    }elseif($operacion["idoperacion"]==8){
                            $operacion["imagen"] = "print";
                            $operacion["color"] = "amber";
                            $operacion["param"] = $dato[1];
                    }
                    if(count($ocultaO)>1){
                            $cadenacomparacion='';
                            $cadenacomparacion.='if(';
                            for($i=0;$i<count($ocultaO);$i++){
                                    $cadenacomparacion.=" \$operacion[\"idoperacion\"]!=$ocultaO[$i] and";	
                            }
                            $cadenacomparacion=substr($cadenacomparacion,0,strlen($cadenacomparacion)-3);
                            $cadenacomparacion.='){ $vbool=1;}';
                            $vbool=0;
                            eval($cadenacomparacion);
                            if($vbool==1){
				?>
					<button type="button" style="margin-left: 10px;" class="btn-floating btn-large tooltipped <?php echo $operacion["color"];?> accent-1" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion["comentario"]);?>" onClick="javascript:<?php echo umill($operacion["accion"]);?>(<?php echo $operacion["param"];?>);"><i class="material-icons <?php echo $operacion["color"];?>-text text-darken-4"><?php echo $operacion["imagen"];?></i></button>
				
               <?php }?>
            <?php
			}else{
		?>
			<button type="button" style="margin-left: 10px;" class="btn-floating btn-large tooltipped <?php echo $operacion["color"];?> accent-1" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion["comentario"]);?>" onClick="javascript:<?php echo umill($operacion["accion"]);?>(<?php echo $operacion["param"];?>);"><i class="material-icons <?php echo $operacion["color"];?>-text text-darken-4"><?php echo $operacion["imagen"];?></i></button>
		<?php
			}
		}
	}
?>
                </div>
        </tr>
        <tr>
  <td>&nbsp;</td>
  <td colspan="<?php echo $nro_campos;?>">
<!--INICIO NIVEL 2-->
<?php
//Instancia la Clase
eval("\$objGrilla2 = new cls".$clase2."(".$id_clase2.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//Para ver que es lo que consulta
//echo "\$rst = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro;
?>
	<div class="">
      <div class="row">
          <div class="col s12 DetallePedido">
              <table class="bordered">
                <tbody>
<?php
//>>Inicio Obtiene Operaciones
$rstOperaciones2 = $objGrilla2->obtenerOperaciones();
if(is_string($rstOperaciones2)){
	echo "<td colspan=100>Error al obtener Operaciones</td></tr><tr><td colspan=100>".$rstOperaciones2."</td>";
	echo "</tr></tbody></table>";
	exit();
}
$datoOperaciones2 = $rstOperaciones2->fetchAll();
//<<Fin

//>>Inicio Obtiene Campos a mostrar
$rstCampos2 = $objGrilla2->obtenerCamposMostrar("G");
if(is_string($rstCampos2)){
	echo "<td colspan=100>Error al obtener campos a mostrar</td></tr><tr><td colspan=100>".$rstCampos2."</td>";
	echo "</tr></tbody></table>";
	exit();
}
$dataCampos2 = $rstCampos2->fetchAll();
foreach($dataCampos2 as $value2){
?>
<!--th><a href="#"><?php //echo umill($value2['comentario'])?></a></th-->
<?php 
}
//<<Fin
$nro_cam2=count($datoOperaciones2);
if($nro_cam2>0){?><!--th colspan="<?php echo $nro_cam2;?>"><a>Operaciones</a></th><?php }?>
</tr-->
<?php
//>>Inicio Ejecutando la consulta
//echo "\$rst = \$objGrilla2->consultar".$clase2."(".$nro_reg.",".$nro_hoja.",1,1,0,'',".$dato[1].");";
eval("\$rst2 = \$objGrilla2->consultar".$clase2."(".$nro_reg.",".$nro_hoja.",1,1,0,'',".$dato[1].");");
if(is_string($rst2)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst2."</td>";
	echo "</tr></tbody></table>";
	exit();
}
$nro_registros_total2=0;
$c2=0;
while($dato2 = $rst2->fetch()){
	$nro_registros_total2 = $dato2["nrototal"];
	reset($dataCampos2);
	//$c2+=1;
?>
    <tr>
<?php
	foreach($dataCampos2 as $value2){
?>
<td class="center <?php if($_GET['zoom']=='SI') echo 'zoom';?>"><?php echo umill($dato2[strtolower($value2['descripcion'])])?></td>
<?php
	}?>
	<td class="center">
		<div class="col s12">
	<?php
	reset($datoOperaciones2);
	foreach($datoOperaciones2 as $operacion2){
            if($operacion2["tipo"]=="C"){
                $ocultaO2=split(',',$_GET['ocultaope']);
                if($operacion2["idoperacion"]==2){
                    $operacion2["imagen"] = "edit";
                    $operacion2["color"] = "light-green";
                }elseif($operacion2["idoperacion"]==3){
                    $operacion2["imagen"] = "clear";
                    $operacion2["color"] = "red";
                }
                if(count($ocultaO2)>1){
                        $cadenacomparacion2='';
                        $cadenacomparacion2.='if(';
                        for($i=0;$i<count($ocultaO2);$i++){
                                $cadenacomparacion2.=" \$operacion2[\"idoperacion\"]!=$ocultaO2[$i] and";	
                        }
                        $cadenacomparacion2=substr($cadenacomparacion2,0,strlen($cadenacomparacion2)-3);
                        $cadenacomparacion2.='){ $vbool2=1;}';
                        $vbool2=0;
                        eval($cadenacomparacion2);
                        if($vbool2==1){
                        ?>
                        <button type="button" style="margin-left: 10px;" 
                                class="btn-floating btn-large tooltipped <?php echo $operacion2["color"];?> accent-1" 
                                data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion2["comentario"]);?>" 
                                onClick="javascript:<?php echo umill($operacion2["accion"]);?>(<?php echo $dato2[1];?>);">
                            <i class="material-icons <?php echo $operacion2["color"];?>-text text-darken-4"><?php echo $operacion2["imagen"];?></i></button>
               <?php }?>
            <?php
			}else{
		?>
            <?php if($operacion2["idoperacion"]!=$_GET['ocultaope']){?><button type="button" style="margin-left: 10px;" 
                                class="btn-floating btn-large tooltipped <?php echo $operacion2["color"];?> accent-1" 
                                data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion2["comentario"]);?>" 
                                onClick="javascript:<?php echo umill($operacion2["accion"]);?>(<?php echo $dato2[1];?>);">
                            <i class="material-icons <?php echo $operacion2["color"];?>-text text-darken-4"><?php echo $operacion2["imagen"];?></i></button><?php }?>
		<?php
			}
		}
	}
?>
<?php
}
if($nro_registros_total2==0){
	echo "<tr><td colspan=100>Sin Informaci&oacute;n</td></tr>";
}
?>                                          </div>
                                    </tr>
                            </tbody>
                  </table>
              </div>
          </div>
      </div>
  </td>
</tr>
<!--FIN NIVEL 2-->
<?php
}
if($nro_registros_total==0){
	echo "<tr><td colspan=100>Sin Informaci&oacute;n</td></tr>";
}
?>
			</tbody>
		  </table>
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
		<input id="txtTituloREPORTE" name="txtTituloREPORTE" type="hidden" value="<?php echo $titulo;?>">
		<input id="txtClaseREPORTE" name="txtClaseREPORTE" type="hidden" value="<?php echo $clase;?>">
		<input id="txtIdClaseREPORTE" name="txtIdClaseREPORTE" type="hidden" value="<?php echo $id_clase;?>">
		<input id="txtFuncionREPORTE" name="txtFuncionREPORTE" type="hidden" value="<?php echo $funcion;?>">
		<input id="txtFiltroREPORTE" name="txtFiltroREPORTE" type="hidden" value="<?php echo $filtro;?>">
		<input id="txtNroRegistrosTotalREPORTE" name="txtNroRegistrosTotalREPORTE" type="hidden" value="<?php echo $nro_registros_total;?>">
		<input id="txtNroHojaREPORTE" name="txtNroHojaREPORTE" type="hidden" value="<?php echo $nro_hoja;?>">
		<input id="txtFechaInicioREPORTE" name="txtFechaInicioREPORTE" type="hidden" value="<?php echo $fechainicio;?>">
		<input id="txtFechaFinREPORTE" name="txtFechaFinREPORTE" type="hidden" value="<?php echo $fechafin;?>">
		<div class="col s12" style="padding-bottom: 10px;">
            <button type="button" class="btn-large right" onClick="javascript: document.getElementById('frmDatosReporte').submit();">IMPRIMIR<i class="material-icons right">print</i></button>
        </div>
    </form>
</div>
<?php }?>
<?php 
if($_GET['zoom']=='SI'){
?>
<div class="row">
	<div class="col s12 center">
		<button type="button" class="btn deep-orange lighten-2" onClick="minimizar()"><i class="material-icons black-text large">archive</i></button>
	</div>
</div>
<?php
}
?>
</body>
</html>
<script>
function buscarGrilla(nro_hoja){
	if(document.getElementById("nro_hoj")){
		document.getElementById("nro_hoj").value = nro_hoja;
	}
	buscar();
}
</script>