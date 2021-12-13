<?php
require('../modelo/clsRelacionOperacion.php');
$id_clase = $_GET["id_clase"];
$filtro = $_GET["filtro"];
$id_tabla = $_GET["id_tabla"];
$nro_reg = $_SESSION["R_NroFilaMostrar"];
$clase = "RelacionOperacion";
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
	$nro_hoja = 1;
}
$order = $_GET["order"];
if(!$order){
	$order="1";
}
$by = $_GET["by"];
if(!$by){
	$by="2";
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ",<?php echo $id_tabla;?>, 0, '" + document.getElementById("txtBuscar").value + "'";
	setRun('vista/listGrilla','&id_tabla=<?php echo $id_tabla;?>&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=<?php echo $clase;?>&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
}
function ordenar(id){
	document.getElementById("order").value = id;
	if(document.getElementById("by").value=="1"){
		document.getElementById("by").value = "0";	
	}else{
		document.getElementById("by").value = "1";
	}
	buscar();
}

function actualizar(id){
	setRun('vista/mantRelacionOperacion','&id_tabla=<?php echo $id_tabla;?>&accion=ACTUALIZAR&clase=<?php echo $clase;?>&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("txtIdTabla", <?php echo $id_tabla;?>);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");		
			buscar()
			alert(text);
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}
buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsRelacionOperacion($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<div id="operaciones">
<?php

$rstRelacionOperacion = $objFiltro->obtenerTabla();
if(is_string($rstRelacionOperacion)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstRelacionOperacion."</td>";
}else{
	$datoRelacionOperacion = $rstRelacionOperacion->fetchObject();
}

$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Tabla</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantRelacionOperacion', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>&id_tabla=<?php echo $id_tabla;?>', 'cargamant','cargamant', 'img04');"> 
		<?php
		}
	}
}
?>
</div>
<div id="cargamant"></div>
<div id="busqueda">
<table><tr><td>Buscar :</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" ></td><td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>"><input name="by" type="hidden" id="by" value="<?php echo $by;?>"><input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="cargagrilla"></div>
<div id="grilla"></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
    <td><a href="#" onClick="javascript:setRun('vista/listTabla','&id_clase=35','frame', 'frame', 'img02')">Tablas</a></td><td>></td>
    <?php
	$rstRelacionOperacion = $objFiltro->obtenerDataSQL("select Descripcion from Tabla where IdTabla = ".$_GET["id_tabla"]);
	if(is_string($rstRelacionOperacion)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstRelacionOperacion."</td>";
	}else{
		$datoRelacionOperacion = $rstRelacionOperacion->fetchObject();
	}
	?>
	<td><?php echo $datoRelacionOperacion->descripcion; ?></td>
</tr></table>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
</body>
</HTML>