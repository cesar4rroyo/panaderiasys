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
    <div class="row">
		<div class="col s12">
		    <table class="striped bordered highlight">
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
foreach($dataCampos as $value){
?>
<th class="center" onClick="javascript:ordenar('<?php echo umill($value['descripcion']);?>');"><?php echo umill($value['comentario'])?></th>
<?php 
}
//<<Fin
$nro_cam=count($datoOperaciones);
?><th class="center">Operaciones</th>
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
$c=0;
while($dato = $rst->fetch()){
	$nro_registros_total = $dato["nrototal"];
	reset($dataCampos);
	$c+=1;
?>
<tr class="hoverable">
<?php
	foreach($dataCampos as $value){
?>
<td class="center"><?php echo umill($dato[strtolower($value['descripcion'])])?></td>
<?php
	}
	reset($datoOperaciones);
	foreach($datoOperaciones as $operacion){
		if($operacion["tipo"]=="C"){
                    if($operacion["idoperacion"]==2){
                            $operacion["imagen"] = "edit";
                            $operacion["color"] = "light-green";
                    }elseif($operacion["idoperacion"]==3){
                            $operacion["imagen"] = "clear";
                            $operacion["color"] = "red";
                    }elseif($operacion["idoperacion"]==4){
                            $operacion["imagen"] = "content_paste";
                            $operacion["color"] = "purple";
                    }elseif($operacion["idoperacion"]==5){
                            $operacion["imagen"] = "delete";
                            $operacion["color"] = "pink";
                    }elseif($operacion["idoperacion"]==7){
                            $operacion["imagen"] = "motorcycle";
                            $operacion["color"] = "teal";
                    }elseif($operacion["idoperacion"]==8){
                            $operacion["imagen"] = "print";
                            $operacion["color"] = "amber";
                    }
		?>
<button <?php echo $operacion["idoperacion"];?> type="button" style="margin-left: 10px;" 
            class="btn-floating btn-large tooltipped <?php echo $operacion["color"];?> accent-1" 
            data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion["comentario"]);?>" 
            onClick="javascript:<?php echo umill($operacion["accion"]);?>(<?php echo $dato[1];?>,<?php echo $dato[2];?>,<?php echo $dato[3];?>,<?php echo $dato[4];?>,<?php echo $dato[5];?>);"><i class="material-icons <?php echo $operacion["color"];?>-text text-darken-4"><?php echo $operacion["imagen"];?></i></button>
		<?php
		}
	}
?>          </div>
        </td>
      </tr>
<?php
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