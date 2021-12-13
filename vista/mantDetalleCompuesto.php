<?php
require("../modelo/clsDetalleCompuesto.php");
require("../modelo/clsListaUnidad.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['IdSucursal'];
//echo $id_clase;
$nro_reg = 10;
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

try{
$objMantenimiento = new clsDetalleCompuesto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstDetalleCompuesto = $objMantenimiento->obtenerTabla();
if(is_string($rstDetalleCompuesto)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstDetalleCompuesto."</td>";
}else{
	$datoDetalleCompuesto = $rstDetalleCompuesto->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataDetalleCompuestos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarDetalleCompuesto(1,1,'2',1,$_GET["IdProductoCompuesto"],$_GET["IdSucursalProducto"],"");
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetch();
	}
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmMantDetalleCompuesto");
	}

function aceptar(){
	if(setValidar("frmMantDetalleCompuesto")){
		g_ajaxGrabar.setURL("controlador/contDetalleCompuesto.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			//buscar();
			alert(text);	
			setRun('vista/listDetalleCompuesto','&id_clase=<?php echo $_GET['id_clase'];?>&IdProducto=<?php echo $_GET["IdProductoCompuesto"];?>&IdSucursalProducto=<?php echo $_GET["IdSucursalProducto"];?>&id_cliente=<?php echo $id_cliente;?>','frame','carga','imgloading');		
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}

function ordenarProducto(id){
	document.getElementById("order").value = id;
	if(document.getElementById("by").value=="1"){
		document.getElementById("by").value = "0";	
	}else{
		document.getElementById("by").value = "1";
	}
	buscarProducto();
}
function ocultarResultadoListGrillaInterna(){
	document.getElementById('divBusquedaProducto').style.display='none';
}
function buscarProducto(e,text){
	if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
	
	if(text!='' || keyc==8){
		//alert(keyc);
		//teclas izquierda, derescha, shift, control
		if(keyc == 37 || keyc == 39 || keyc == 16 || keyc == 17) { return false;}
		if(keyc == 38 || keyc == 40 || keyc == 13) {
			div="divBusquedaProducto";
			if(document.getElementById(div).innerHTML!=""){
			autocompletarProducto_teclado2(div, 'tablaProducto', keyc);
			}
		}else{
	
			vOrder = document.getElementById("order").value;
			vBy = document.getElementById("by").value;
			
			vDescripcion = encodeURI(document.getElementById("txtBuscar").value.replace('\'',''));
	
			vValor = "'"+vOrder + "'," + vBy + ", 0, '" + vDescripcion + "',"+ document.getElementById("cboCategoria").value + "," + document.getElementById("cboMarca").value + ", '" + document.getElementById("txtCodigoBuscar").value + "','I'";
			setRun('vista/listGrilla2InternaTeclado','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=43&filtro=' + vValor, 'divBusquedaProducto', 'divBusquedaProducto', 'img03');
			document.getElementById('divBusquedaProducto').style.display='';
		}
	}
}
//buscarProducto();
function seleccionarProducto(idproducto,idsucursalproducto){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "seleccionarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			eval(text);
			//centraDivAutorizar()
			//document.getElementById("divDatosProductoSeleccionado").style.display="";
            document.getElementById('divBusquedaProducto').style.display='none';
			document.getElementById("divDatosProductoSeleccionado").className="muestra";
			document.getElementById("txtPrecioVenta").value=vprecioventa;
			document.getElementById("lblProducto").innerHTML=vproducto;
			document.getElementById("txtIdProductoSeleccionado").value=idproducto;
			document.getElementById("txtStockActual").value=vstockactual;
			document.getElementById("txtPrecioCompra").value=vpreciocompra;
			document.getElementById("txtIdSucursalProductoSeleccionado").value=idsucursalproducto;
			document.getElementById("txtCantidad").select();
		};
		g_ajaxPagina.request();
}
function seleccionar(idproducto,idsucursalproducto){
		var recipiente = document.getElementById('DivUnidad');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboUnidad");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;		
            $('select').material_select();	
			seleccionarProducto(idproducto,idsucursalproducto);
		};
		g_ajaxPagina.request();
}
function cambiaPrecioUnidad(idunidad){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "cambiaPrecioUnidad");
		g_ajaxPagina.setParameter("IdUnidad", idunidad);
		if(document.getElementById('chkLlevar').checked){
			g_ajaxPagina.setParameter("NroPrecio", 2);		
		}else{
			g_ajaxPagina.setParameter("NroPrecio", 1);		
		}
		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdSucursalProducto", document.getElementById('txtIdSucursalProductoSeleccionado').value);
		g_ajaxPagina.response = function(text){
				document.getElementById('txtPrecioVenta').value=text;
		};
		g_ajaxPagina.request();
}
<?php if($_GET['accion']=='ACTUALIZAR'){?>
seleccionar(<?php echo $_GET['IdProducto'];?>)
<?php }?>
document.getElementById('txtBuscar').focus();
</script>
</head>
<body>
<!--AUTOCOMPLETAR: LOS ESTILOS SIGUIENTES SON PARA CAMBIAR EL EFECTO AL MOMENTO DE NAVEGAR POR LA LISTA DEL AUTOCOMPLETAR-->
<style type="text/css">    
		.autocompletar .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
    </style>  
<!--AUTOCOMPLETAR-->  
<?php require("tablaheader.php");?>
<form id="frmMantDetalleCompuesto" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['IdDetalleCompuesto'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
<table>
<?php /*?><?php
reset($dataDetalleCompuestos);
foreach($dataDetalleCompuestos as $value){
?>	
<?php }?><?php */?>
<tr><td <?php if($_GET["accion"]=="ACTUALIZAR") echo 'style="display:none"';?>>
<fieldset>
<legend><strong>BUSQUEDA DE INGREDIENTES:</strong></legend> 
<div id="busquedaProducto">
<table><tr><td>Por Descripci&oacute;n :</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" onKeyUp="javascript: buscarProducto(event,this.value);"></td><td>C&oacute;digo :</td><td><input type="text" id="txtCodigoBuscar" name="txtCodigoBuscar" value=""  size="6" maxlength="6" onKeyPress="return validarsolonumeros(event)" onKeyUp="javascript: buscarProducto(event,this.value);"></td><td>Categor&iacute;a :</td><td><?php echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")","Categoria",0,'',$objMantenimiento,'buscarProducto(event)', 'Todos');
?></td><td>Marca :</td><td><?php
echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'","Marca",0,'',$objMantenimiento, 'buscarProducto(event)', 'Todos');
?></td><td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:buscarProducto()" style="display:none">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="divBusquedaProducto" class="autocompletar">
</div>
</fieldset>
</td></tr><tr><td>
<div id="divDatosProductoSeleccionado">
<fieldset><legend><strong>DATOS DEL INGREDIENTE:</strong></legend> 
<input name="txtIdSucursalProducto" type="hidden" id="txtIdSucursalProducto" value="<?php echo $_GET["IdSucursalProducto"];?>">
<input name="txtIdProductoCompuesto" type="hidden" id="txtIdProductoCompuesto" value="<?php echo $_GET["IdProductoCompuesto"];?>">
<!--los datos anteriores son del producto principal-->
<div id="divProductoSeleccionado">
<table width="100%">
<tr>
<td>Producto :</td>
<td><!--los datos siguientes son del ingrediente-->
<input name="txtIdProductoSeleccionado" type="hidden" id="txtIdProductoSeleccionado" value="0">
<input name="txtIdSucursalProductoSeleccionado" type="hidden" id="txtIdSucursalProductoSeleccionado" value="0">
<label id="lblProducto" name="lblProducto">...</label></td>
<td>Unidad :</td>
<td><div id="DivUnidad"></div><!--Aca se genera el combo unidades y el link para ver las unidades(ponerle imagen: Archivo: xajax_prueba2.php funcion:genera_cboUnidad())--></td>
<td style="display:none">Stock Actual :</td>
<td style="display:none">
  <input name="txtStockActual" type="text" id="txtStockActual" value="0" size="10" maxlength="10" disabled></td>
  <td>Precio :</td>
  <td><input type="text" name="txtPrecioCompra" id="txtPrecioCompra" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);" disabled><input type="hidden" name="txtPrecioVenta" id="txtPrecioVenta" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);" disabled></td>
  <td>Cantidad:</td>
  <td><input type="text" name="txtCantidad" id="txtCantidad" value="1" maxlength="10" size="10" on onKeyPress="if (event.keyCode==13){agregar();document.getElementById('divDatosProductoSeleccionado').className='oculta';}else{return validarsolonumerosdecimales4(event,this.value);}">
  </td>
  </tr>
  </table>
</div>
</fieldset>
</div>
</td></tr>
<tr>
<td align="center"><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()">&nbsp;<input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
</tr>
<tr>
<td align="center"><b>Nota:</b> Recuerde que todas las cantidades de los ingredientes hacen referencia a un (1) producto compuesto (especificamente a su unidad base).</td>
</tr>
</table>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
    <td><a href="#" onClick="javascript:setRun('vista/listProductoCompuesto','&id_clase=62','frame', 'frame', 'img05')">Productos</a></td><td>></td>
    <?php
	$rstProducto = $objMantenimiento->obtenerDataSQL("select Descripcion from producto where idproducto = ".$_GET["IdProductoCompuesto"]." and idsucursal=".$_GET["IdSucursalProducto"]);
	if(is_string($rstProducto)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstProducto."</td>";
	}else{
		$datoProducto = $rstProducto->fetchObject();
	}
	?>
	<td><?php echo $datoProducto->descripcion; ?></td>
    <td>></td>
	<td><?php echo $datoDetalleCompuesto->descripcion; ?></td>
</tr></table>
</div>
<hr>
</body>
</HTML>