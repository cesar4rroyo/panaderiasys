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
<!--th><a href="#" onClick="javascript:ordenar('<?php echo umill($value['descripcion']);?>');"><?php echo umill($value['comentario'])?></a></th-->
<th class="center" onClick="javascript:ordenar('<?php echo umill($value['descripcion']);?>');"><?php echo umill($value['comentario'])?></th>
<?php 
}
//<<Fin

$nro_cam=count($datoOperaciones);
if($nro_cam>0){?><th class="center">Operaciones</th><?php }?>
            </tr>
        </thead>
        <tbody>
<?php
//>>Inicio Ejecutando la consulta
if($id_clase=="53"){
    $nro_hoja="'h".$nro_hoja."'";    
}
eval("\$rst = \$objGrilla->consultar".$clase."(".$nro_reg.",".$nro_hoja.$filtro);
if($id_clase=="53"){
      $nro_hoja=substr($nro_hoja,2,strlen($nro_hoja)-2);
}
if(is_string($rst)){
	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
	echo "</tr></tbody></table>";
	exit();
}
$nro_registros_total=0;
//print_r($rst);
$c=0;
while($dato = $rst->fetch()){
	$nro_registros_total = $dato["nrototal"];
	reset($dataCampos);
	$c+=1;
?>
<!--tr class="<?php if($c%2==0) echo 'par'; else echo 'impar';?>"-->
            <tr class="hoverable <?php if($dato['tipodocumento']=='EGRESO' || $dato['stock']=="R"){ echo 'red lighten-4';}elseif($dato['tipodocumento']=='INGRESO'  || $dato['stock']=="S"){ echo 'green lighten-4';}?>">
<?php
	foreach($dataCampos as $value){
?>
<!--td <?php //if($dato['estado']=='I') echo 'style="color:#F00"';?>><?php //echo umill($dato[strtolower($value['descripcion'])])?></td-->
<td class="center"<?php if($value['descripcion']=="Numero" && $id_clase==53 && strlen($dato["idmovimientoref"])>0){?> style="cursor: pointer;" onclick="javascript:verComprobante(<?php echo $dato["idmovimientoref"];?>,6);"<?php }?>><?php echo umill($dato[strtolower($value['descripcion'])])?></td>
<?php
	}?>
	<td class="center">
		<div class="col s12">
                    <!--button type="button" style="margin-left: 10px;" class="btn-floating btn-large tooltipped green accent-1" data-position="bottom" data-delay="50" data-tooltip="EDITAR MODO PAGO" onClick="javascript:editarModoPago(<?php echo $dato["idmovimiento"];?>);"><i class="material-icons green-text text-darken-4">edit</i></button-->
	<?php
	reset($datoOperaciones);
    foreach($datoOperaciones as $operacion){
		if($operacion["tipo"]=="C"){
			//print_r($operacion);
                    if($operacion["idoperacion"]==2){
                            $operacion["imagen"] = "edit";
                            $operacion["color"] = "light-green";
                            if($id_clase==46){
                                $operacion["param"] = $dato['idmesa'].",'".$dato['mesa']."'";
                            }else{
                                $operacion["param"] = $dato[1];
                            }
                    }elseif($operacion["idoperacion"]==3){
                            $operacion["imagen"] = "clear";
                            $operacion["color"] = "red";
                            $operacion["param"] = $dato[1];
                    }elseif($operacion["idoperacion"]==4){
                            $operacion["imagen"] = "account_balance";
                            $operacion["color"] = "blue";
                            $operacion["param"] = $dato[1];
                    }elseif($operacion["idoperacion"]==5){
                            $operacion["imagen"] = "content_paste";
                            $operacion["color"] = "purple";
                            if($id_clase==46){
                                $operacion["param"] = $dato[1].",'".$dato['mesa']."'";
                            }else{
                                $operacion["param"] = $dato[1];
                            }
                    }elseif($operacion["idoperacion"]==6){
                            $operacion["imagen"] = "person";
                            $operacion["color"] = "orange";
                            if($id_clase==46){
                                $operacion["param"] = $dato[1].",'".$dato['mesa']."'";
                            }else{
                                $operacion["param"] = $dato[1];
                            }
                    }elseif($operacion["idoperacion"]==7){
                            $operacion["imagen"] = "motorcycle";
                            $operacion["color"] = "teal";
                            $operacion["param"] = $dato[1];
                    }elseif($operacion["idoperacion"]==8){
                            $operacion["imagen"] = "print";
                            $operacion["color"] = "amber";
                            $operacion["param"] = $dato[1];
                    }else{
                        $operacion["param"] = $dato[1];
                    }
                    $ocultaO=explode(',',$_GET['ocultaope']);
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
                    <button <?php echo $operacion["idoperacion"];?> type="button" style="margin-left: 10px;" class="btn-floating btn-large tooltipped <?php echo $operacion["color"];?> accent-1" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion["comentario"]);?>" onClick="javascript:<?php echo umill($operacion["accion"]);?>(<?php echo $operacion["param"];?>);"><i class="material-icons <?php echo $operacion["color"];?>-text text-darken-4"><?php echo $operacion["imagen"];?></i></button>
               <?php }?>
            <?php
			}else{
		?>
			<button <?php echo $operacion["idoperacion"];?> type="button" style="margin-left: 10px;" class="btn-floating btn-large tooltipped <?php echo $operacion["color"];?> accent-1" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion["comentario"]);?>" onClick="javascript:<?php echo umill($operacion["accion"]);?>(<?php echo $operacion["param"];?>);"><i class="material-icons <?php echo $operacion["color"];?>-text text-darken-4"><?php echo $operacion["imagen"];?></i></button>
		<?php
			}
		}
	}
?>
            </div>
        </td>
    </tr>
<?php
}
if($nro_registros_total==0){
	echo "<tr><td colspan=100>Sin Informaci&oacute;n</td></tr>";
}else{
?>
<?php }?>
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
<!--table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td>
<table class="tablaPaginacion">
<tr-->
<div class="col s12 m12 l2 right right-align"><?php if($nro_registros_total==0){echo "No hay registros";}else{echo "Registros del $inicio al $fin (".$mostrar.") de ".$nro_registros_total;}?></div>
<div class="col s12 m12 l8 offset-l2 center">
        <ul class="pagination">
            <li class="disabled"><a href="#!"><i class='material-icons'>chevron_left</i></a></li>
<?php
//$ini = "<td><a href=\"#\" onClick=\"buscarGrilla(";
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
<!--td><div id="cargando"></div></td>
</tr>
</table>
</td><td align="right"-->
<!--table><tr><td width="100%" align="right"><?php if($nro_registros_total==0){echo "No hay registros";}else{echo "Registros del $inicio al $fin (".$mostrar.") de ".$nro_registros_total;}?></td></tr></table-->
<!--/td></tr-->
<?php if($imprimir=='SI'){?><!--tr><td align="center" colspan="2">
<a href="#" onClick="javascript: document.getElementById('frmDatosReporte').submit();"><img src="img/print_f2.png" width="20" height="20">Imprimir</a-->
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
            <button type="button" class="btn-large right green" style="margin-right: 10px;" onClick="javascript:document.getElementById('frmDatosReporte').action='vista/reportes/Reporte<?php echo $tiporeporte;?>Excel.php';document.getElementById('frmDatosReporte').submit();">EXCEL<i class="material-icons right">description</i></button>
        </div>
    </form>
</div>
<?php }?>

<?php //require("tablafooter.php");?>
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