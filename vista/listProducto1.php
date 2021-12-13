<?php
require('../modelo/clsProducto.php');
require ('fun.php');
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
	$id_empresa = $_SESSION['R_IdEmpresa'];
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFechaInicio", "%d/%m/%Y");
cal.manageFields("btnCalendar2", "txtFechaFin", "%d/%m/%Y");


g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	//vValor = "'"+vOrder + "'," + vBy + ", 0,<?php echo $id_cliente;?>, '" + document.getElementById("txtBuscar").value + "'";
	vValor = "'"+vOrder + "'," + vBy + ", 0,<?php echo $id_cliente;?>, '" + document.getElementById("txtBuscar_Descripcion").value + "','"+ document.getElementById("cbocategoria").value + "','" + document.getElementById("cbomarca").value + "','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "'";
	
	setRun('vista/listGrilla2','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&id_clase=<?php echo $id_clase;?>&imprimir=SI&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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

function actualizar(idproducto, idsucursal){
	setRun('vista/mantProducto','&accion=ACTUALIZAR&clase=Producto&id_clase=<?php echo $id_clase;?>&IdSucursal=' + idsucursal + '&IdProducto=' + idproducto,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id, idsucursal){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contProducto.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("txtIdSucursal", idsucursal);
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

function verpresentacion(idproducto, idsucursal){
	//alert(idproducto);
	setRun('vista/listListaUnidad','&accion=ACTUALIZAR&clase=ListaUnidad&id_clase=5&IdProducto=' + idproducto + '&id_cliente='+idsucursal,'frame','carga','imgloading');
}
buscar();
</script>
</head>
<body>
<br>
<div class="titulo"><b>PRODUCTOS</b></div>
<?php
$objFiltro = new clsProducto($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<div id="operaciones">
<?php

$rstProducto = $objFiltro->obtenerTabla();
if(is_string($rstProducto)){
	echo "<td colspan=100>Error al Obtener datos del Perfil</td></tr><tr><td colspan=100>".$rstProducto."</td>";
}else{
	$datoProducto = $rstProducto->fetchObject();
}
$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Perfil</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantProducto', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"> 
		<?php
		}
	}
}
?>
</div>
<div id="cargamant"></div>
<div id="busqueda">
<table><tr><td><br>Buscar por :</td>
<td>Descripcion/Producto :<br>
<input type="text" id="txtBuscar_Descripcion" name="txtBuscar_Apellido_Nombre" value="" ></td>
<td>Categoria :<br>
<?php
try{
$objMantenimiento = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
}
echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")","categoria",0,'',$objMantenimiento,'', 'Todos');


//genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');
?>

</td>
<td>Marca :<br>
<?php
echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'","marca",0,'',$objMantenimiento, '', 'Todos');
?>
</td>
<td>Fecha Venc. Inicial :<br>
<input type="text" id="txtFechaInicio" name="txtFechaInicio" value="<?php //echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha"> 
  <button id="btnCalendar" type="button" class="boton"><img src="img/date.png" width="16" height="16"> </button></td>
<td>Fecha Venc. Final :<br>
<input type="text" id="txtFechaFin" name="txtFechaFin" value="<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha">
  <button id="btnCalendar2" type="button" class="boton"> <img src="img/date.png" width="16" height="16"></button></td>



  
<td><br><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
  </td>
</tr></table>
</div>
<div id="cargagrilla"></div>
<div id="grilla"></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><?php echo $datoProducto->descripcion; ?></td>
</tr></table>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
</body>
</HTML>