<?php
require('../modelo/clsMovimiento.php');
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
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Listado de Pedidos de Comensales</title>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<link href="../css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">
<!--CALENDARIO-->
<script src="../calendario/js/jscal2.js"></script>
    <script src="../calendario/js/lang/es.js"></script>
    <link rel="stylesheet" type="text/css" href="../calendario/css/jscal2.css" />
    <link rel="stylesheet" type="text/css" href="../calendario/css/reduce-spacing.css" />
    <link rel="stylesheet" type="text/css" href="../calendario/css/steel/steel.css" />
<!--CALENDARIO-->
<script src="../runtime/lib/aw.js" type="text/javascript"></script>
<link href="../runtime/styles/system/aw.css" rel="stylesheet">
<script type="text/javascript" src="../js/fun.js"></script>
<script>
var g_bandera = null;
var g_ajaxGrabar = null;
function setRun(url, par, div, msj, img){
	var recipiente = document.getElementById(div);
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL(url + ".php?ajax=true&"+par);
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.response = function(xform){
		
		var s = "", r = /<script>([\s\S]+)<\/script>/mi;
		if (xform.match(r)){
			s = RegExp.$1; // extract script
			xform = xform.replace(r, "");
		}
		recipiente.innerHTML = xform;	
		// Creo el nuevo JS
		var etiquetaScript=document.createElement("script");
		document.getElementsByTagName("head")[0].appendChild(etiquetaScript);
		etiquetaScript.text=s;
		//loading(false, img);
		//"Fecha ini "+fechainicio +" fin "+fechafin
		//alert("Fecha "+fechainicio+" fin " + fechafin);
		//alert(xform);
	};
	g_ajaxPagina.request();
	//loading(true, img, msj, "linea.gif",true);
}		
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	*/
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','"+document.getElementById("txtSituacion").value+"','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "'"+",<?php echo $_SESSION['R_IdUsuario'];?>";
	if(document.getElementById("txtSituacion").value=='P'){
		setRun('listGrillaSinOperacionWap','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
	}else{
		if(document.getElementById("txtSituacion").value=='O'){
			setRun('listGrillaWap','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
		}else{
			setRun('listGrillaWap','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&ocultaope=2,4&filtro=' + vValor, 'grilla', 'grilla', 'img03');
		}
	}
	document.getElementById("contenidolist").style.display="";
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
	setRun('mantPedido','&accion=ACTUALIZAR&clase=Movimiento&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}
function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("../controlador/contPedido.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		//loading(true, "loading", "grilla", "linea.gif",true);
	//}
}
function atender(id){
	//if(setValidar()){
		g_ajaxGrabar.setURL("../controlador/contPedido.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "CABIASITUACION");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			//loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		//loading(true, "loading", "grilla", "linea.gif",true);
	//}
}
//buscar();
</script>
</head>
<body>
<br>
    <p class="titulo">PEDIDO DE COMENSALES</p>
<?php
$objFiltro = new clsMovimiento($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<div id="operaciones" align="right">
<button type="button" onclick="javascript: mostrarbusqueda();">B&uacute;squeda</button>
<?php

$rstMovimiento = $objFiltro->obtenerTabla();
if(is_string($rstMovimiento)){
	echo "<td colspan=100>Error al Obtener datos de Perfil</td></tr><tr><td colspan=100>".$rstMovimiento."</td>";
}else{
	$datoMovimiento = $rstMovimiento->fetchObject();
}
$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Perfil</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:window.open('mesas.php','_self')"> 
		<?php
		}
	}
}
?>
</div>
<div id="cargamant"></div>
<div id="contenidolist">
<div id="busqueda" style="display:none;">
<fieldset><legend>Criterios de B&uacute;squeda:</legend>
<table><tr><td>N&uacute;mero :</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" ></td></tr>
<tr><td>Fecha Inicio :</td><td><input type="text" id="txtFechaInicio" name="txtFechaInicio" value="<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar" type="button" class="boton"><img src="../img/date.png" width="16" height="16"> </button></td></tr>
<tr><td>Fecha Fin :</td><td><input type="text" id="txtFechaFin" name="txtFechaFin" value="<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar2" type="button" class="boton"><img src="../img/date.png" width="16" height="16"> </button></td><td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:buscar()"></td>
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</fieldset>
</div>
<div id="cargagrilla"></div>
<div id="tabs">
  <input name="txtSituacion" type="hidden" id="txtSituacion" value="O">
</div>
<span id="myTabs"></span>
<div id="grilla" style="border: 1px solid #aaa;"></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><?php echo $datoMovimiento->descripcion; ?></td>
</tr></table>
</div>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<script>
function mostrarbusqueda(){
	if(document.getElementById("busqueda").style.display==""){
		document.getElementById("busqueda").style.display="none";
	}else{
		document.getElementById("busqueda").style.display="";
	}
}
//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFechaInicio", "%d/%m/%Y");
cal.manageFields("btnCalendar2", "txtFechaFin", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>
//INICIO CODIGO TABS
	var names = ["Pedido", "Atendido", "Consumido"];
	var values = ["txtSituacion.value='O';buscar('O');", "txtSituacion.value='A';buscar('A');", "txtSituacion.value='P';buscar('P');"];
 
	var tabs = new AW.UI.Tabs;
	tabs.setId("myTabs");
	tabs.setItemText(names);
	tabs.setItemValue(values); // store ids of content DIVs
	tabs.setItemCount(3);
	tabs.refresh();

	tabs.onSelectedItemsChanged = function(selected){
 
		var index = selected[0];
		var value = this.getItemValue(index);
		eval(value);
		/*if(index==0) {txtSituacion.value='O';buscar('O');}
		if(index==1) {txtSituacion.value='A';buscar('A');}
		if(index==2) {txtSituacion.value='P';buscar('P');}*/
		//window.status = index;
	}
 
	tabs.setSelectedItems([0]); // load the first page.
//FIN CODIGO TABS
</script>
</body>
</HTML>