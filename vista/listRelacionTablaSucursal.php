<?php
require('../modelo/clsRelacionTablaSucursal.php');
$id_clase = $_GET["id_clase"];
$nro_reg = 0;
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
	$by="1";
}

$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = 0;
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = 0;
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
	vValor = "'"+vOrder + "'," + vBy + ", 0, <?php echo $id_cliente;?>, '" + document.getElementById("txtBuscar").value + "'";
	setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=RelacionTablaSucursal&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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
	setRun('vista/mantRelacionTablaSucursal','&accion=ACTUALIZAR&id_empresa=<?php echo $id_empresa;?>&id_sucursal=<?php echo $id_cliente;?>&clase=RelacionTablaSucursal&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contRelacionTablaSucursal.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("txtIdSucursal", <?php echo $id_cliente;?>);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();			
			alert(text);			
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}

function verCampos(id){
	vValor = "<?php echo $id_cliente;?>, " + id + ", 0, 'G', ''";
	setRun('vista/listRelacionCampo','&id_empresa=<?php echo $id_empresa;?>&id_cliente=<?php echo $id_cliente;?>&id_tabla='+id+'&nro_reg=10&nro_hoja=1&clase=RelacionCampo&id_clase=9&filtro=' +vValor, 'frame','frame', 'imgloading01');
}

function verOperaciones(id){
	vValor = "<?php echo $id_cliente;?>, " + id + ", 0, 'T', ''";
	setRun('vista/listRelacionOperacionSucursal','&id_empresa=<?php echo $id_empresa;?>&id_cliente=<?php echo $id_cliente;?>&id_tabla='+id+'&nro_reg=10&nro_hoja=1&clase=RelacionOperacionSucursal&id_clase=14&filtro=' +vValor, 'frame','frame', 'imgloading01');
}

buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsRelacionTablaSucursal($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<div id="operaciones">
<?php

$rstTabla = $objFiltro->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}
$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Tabla</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantRelacionTablaSucursal', 'accion=NUEVO&id_sucursal=<?php echo $id_cliente;?>&id_empresa=<?php echo $id_empresa;?>&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"> 
		<?php
		}
	}
}
?>
</div>
<div id="cargamant"></div>
<div id="busqueda">
<table><tr><td>Buscar :</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" ></td><td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="cargagrilla"></div>
<div id="grilla"></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
    <td><a href="#" onClick="javascript:setRun('vista/listEmpresa','&id_clase=21','frame', 'frame', 'img05')">Empresas</a></td><td>></td>
    <?php
	if($_GET["id_empresa"]>0){
	$rstEmpresa = $objFiltro->obtenerDataSQL("select RazonSocial from Empresa where IdEmpresa = ".$_GET["id_empresa"]);
	if(is_string($rstEmpresa)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstEmpresa."</td>";
	}else{
		$datoEmpresa = $rstEmpresa->fetchObject();
	}
	?>
	<td><a href="#" onClick="javascript:setRun('vista/listSucursal','&id_empresa=<?php echo $id_empresa;?>&id_clase=40&clase=Sucursal','frame', 'frame', 'img05')"><?php echo $datoEmpresa->razonsocial; ?></a></td><td>></td>
    <?php
	}
	if($_GET["id_cliente"]>0){
	$rstCliente = $objFiltro->obtenerDataSQL("select RazonSocial from Sucursal where IdSucursal = ".$_GET["id_cliente"]);
	if(is_string($rstEmpresa)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstCliente."</td>";
	}else{
		$datoCliente= $rstCliente->fetchObject();
	}
	?>
	<td><?php echo $datoCliente->razonsocial; ?></td>
    <?php
	}
	?>
    <td>></td>
	<td><?php echo $datoTabla->descripcion; ?></td>
</tr></table>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
</body>
</HTML>