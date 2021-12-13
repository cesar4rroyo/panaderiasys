<?php
require('../modelo/clsDetalleCompuesto.php');
require ('fun.php');
$id_clase = 4;
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
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	
	vValor = "'"+vOrder + "'," + vBy + ", <?php echo $_GET["IdProducto"];?>,<?php echo $_GET["IdSucursalProducto"];?>, '" + document.getElementById("txtBuscar_Descripcion").value + "'";
	
	setRun('vista/listGrilla2','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=DetalleCompuesto&id_clase=<?php echo $id_clase;?>&imprimir=SI&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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

function actualizar(iddetallecompuesto, idproducto){
	alert('pendiente');return false;
	setRun('vista/mantDetalleCompuesto','&accion=ACTUALIZAR&clase=DetalleCompuesto&id_clase=<?php echo $id_clase;?>&IdProductoCompuesto=<?php echo $_GET["IdProducto"];?>&IdSucursalProducto=<?php echo $_GET["IdSucursalProducto"];?>&IdProducto=' + idproducto + '&IdDetalleCompuesto=' + iddetallecompuesto,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id, idsucursal){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contDetalleCompuesto.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("txtIdSucursal", idsucursal);
		g_ajaxGrabar.setParameter("txtIdProductoCompuesto", <?php echo $_GET["IdProducto"];?>);
		g_ajaxGrabar.setParameter("txtIdSucursalProductoCompuesto", <?php echo $_GET["IdSucursalProducto"];?>);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);	
			setRun('vista/listDetalleCompuesto','&id_clase=<?php echo $_GET['id_clase'];?>&IdProducto=<?php echo $_GET["IdProducto"];?>&IdSucursalProducto=<?php echo $_GET["IdSucursalProducto"];?>&id_cliente=<?php echo $id_cliente;?>','frame','carga','imgloading');			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}

function actualizarProducto(idproducto, idsucursal){
	setRun('vista/mantProducto','&accion=ACTUALIZAR&clase=Producto&id_clase=11&IdSucursal=' + idsucursal + '&IdProducto=' + idproducto,'cargamant', 'cargamant', 'imgloading03');
}


function verpresentacion(idproducto, idsucursal){
	setRun('vista/listListaUnidad','&clase=ListaUnidad&id_clase=5&IdProducto=' + idproducto + '&IdSucursalProducto=' + idsucursal + '&id_cliente='+<?php echo $id_cliente;?>,'frame','carga','imgloading');
}
buscar();
</script>
</head>
<body>
<br>
<?php
$objFiltro = new clsDetalleCompuesto($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<?php
	$rstProducto = $objFiltro->obtenerDataSQL("select p.descripcion, p.idunidadbase, u.descripcion as Unidad, preciomanoobra, precioventa from producto p inner join unidad u on p.idunidadbase=u.idunidad inner join LISTAUNIDAD LU on LU.idproducto= p.idproducto and p.idsucursal=LU.idsucursal and LU.idunidad=p.idunidadbase  where p.idproducto = ".$_GET["IdProducto"]." and p.idsucursal=".$_GET["IdSucursalProducto"]);
	if(is_string($rstProducto)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstProducto."</td>";
	}else{
		$datoProducto = $rstProducto->fetchObject();
	}
	?>
<p align="center"><label class="titulo">PRODUCTO:</label> <?php echo $datoProducto->descripcion;?></p>
<p align="center"><label class="titulo">UNIDAD BASE:</label> <?php echo $datoProducto->unidad;?></p>
<br>
<div class="titulo"><b>INGREDIENTES</b></div>
<div id="operaciones">
<?php

$rstDetalleCompuesto = $objFiltro->obtenerTabla();
if(is_string($rstDetalleCompuesto)){
	echo "<td colspan=100>Error al Obtener datos del Perfil</td></tr><tr><td colspan=100>".$rstProducto."</td>";
}else{
	$datoDetalleCompuesto = $rstDetalleCompuesto->fetchObject();
}
$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Perfil</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantDetalleCompuesto', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>&IdProductoCompuesto=<?php echo $_GET["IdProducto"];?>&IdSucursalProducto=<?php  echo $_GET["IdSucursalProducto"];?>', 'cargamant','cargamant', 'img04');"> 
		<?php
		}
	}
}
?>
</div>
<div id="cargamant"></div>
<div id="busqueda">
<table><tr><td><br>Buscar por :</td>
<td>Descripci&oacute;n del Ingrediente :<br>
<input type="text" id="txtBuscar_Descripcion" name="txtBuscar_Apellido_Nombre" value="" ></td>
<td><br><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
  </td>
</tr></table>
</div>
<div id="cargagrilla"></div>
<div id="grilla"></div>
<table border="0">
<tr class="par">
<th width="200" height="20">SubTotal Ingredientes:</th><td width="100" height="20" align="right"><?php echo $subtotal=$objFiltro->consultarTotal($_GET["IdProducto"],$_GET["IdSucursalProducto"]);?></td></tr>
<tr class="par">  
<th width="200" height="20">Precio de mano de obra:</th><td width="100" height="20" align="right"><?php echo $datoProducto->preciomanoobra;?></td><td><a href="#" onClick="javascript: actualizarProducto(<?php echo $_GET["IdProducto"];?>,<?php echo $_GET["IdSucursalProducto"];?>);">cambiar</a></td></tr>
<tr class="par"><th colspan="2"></th></tr>
<tr class="par">
<th width="200" height="20">Precio de coste estimado:</th><td width="100" height="20" align="right"><?php echo number_format($subtotal+$datoProducto->preciomanoobra,2);?></td></tr>
<tr class="par">
<th width="200" height="20">Precio de venta actual:</th><td width="100" height="20" align="right"><?php echo number_format($datoProducto->precioventa,2);?></td><td><a href="#" onClick="javascript: verpresentacion(<?php echo $_GET["IdProducto"];?>,<?php echo $_GET["IdSucursalProducto"];?>);">cambiar</a></td></tr>
<tr class="par"><th colspan="2"></th></tr>
<tr class="par">
<th width="200" height="20">Utilidad:</th><td width="100" height="20" align="right"><?php echo number_format($datoProducto->precioventa-($subtotal+$datoProducto->preciomanoobra),2);?></td></tr>
<tr class="par">
<th width="200" height="20">Utilidad porcentaje:</th><td width="100" height="20" align="right">%&nbsp;<?php echo number_format(($datoProducto->precioventa-($subtotal+$datoProducto->preciomanoobra))*100/$datoProducto->precioventa,2);?></td></tr>
</table>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
    <td><a href="#" onClick="javascript:setRun('vista/listProductoCompuesto','&id_clase=62','frame', 'frame', 'img05')">Productos</a></td><td>></td>
	<td><?php echo $datoProducto->descripcion; ?></td>
    <td>></td>
	<td><?php echo $datoDetalleCompuesto->descripcion; ?></td>
</tr></table>
</div>
</body>
</HTML>