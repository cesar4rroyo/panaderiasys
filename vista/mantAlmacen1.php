<?php
require("../modelo/clsMovimiento.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
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

if(isset($_SESSION['R_carroAlmacen']))
$_SESSION['R_carroAlmacen']="";

try{
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstMovimiento = $objMantenimiento->obtenerTabla();
if(is_string($rstMovimiento)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstMovimiento."</td>";
}else{
	$datoMovimiento = $rstMovimiento->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataMovimientos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarMovimiento(1,1,'2',1,$_GET["Id"],"");
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
	g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);
	
}
function aceptar(){
	//if(setValidar()){
		g_ajaxGrabar.setURL("controlador/contAlmacen.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	//}
}

function buscarProducto(){
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, '" + document.getElementById("txtBuscar").value + "'";
	setRun('vista/listGrillaInterna','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&id_clase=43&filtro=' + vValor, 'divBusquedaProducto', 'divBusquedaProducto', 'img03');
}
buscarProducto();
function seleccionarProducto(idproducto){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxAlmacen.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "seleccionarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		g_ajaxPagina.response = function(text){
			eval(text);
			document.getElementById("txtPrecioVenta").value=vprecioventa;
			document.getElementById("lblProducto").innerHTML=vproducto;
			document.getElementById("txtIdProductoSeleccionado").value=idproducto;
			document.getElementById("txtStockActual").value=vstockactual;
			//document.getElementById("txtPrecioManoObra").value=vpreciomanoobra;
			document.getElementById("txtPrecioCompra").value=vpreciocompra;
		};
		g_ajaxPagina.request();
}
function seleccionar(idproducto){
		var recipiente = document.getElementById('DivUnidad');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxAlmacen.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboUnidad");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
			seleccionarProducto(idproducto);
		};
		g_ajaxPagina.request();
}
function agregar(){
		var vprecioventa=document.getElementById('txtPrecioVenta').value;
		var vcantidad=document.getElementById('txtCantidad').value;
		var vpreciocompra=document.getElementById('txtPrecioCompra').value;
		
		if(vprecioventa>=0 && vprecioventa!='' && vcantidad>=0 && vcantidad!='' && vpreciocompra>=0 && vpreciocompra!=''){

		var recipiente = document.getElementById('divDetalleAlmacen');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxAlmacen.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarProducto");
		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdUnidad", document.getElementById('cboUnidad').value);
		g_ajaxPagina.setParameter("Cantidad", vcantidad);
		g_ajaxPagina.setParameter("PrecioVenta", vprecioventa);
		g_ajaxPagina.setParameter("StockActual", document.getElementById('txtStockActual').value);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
//		g_ajaxPagina.setParameter("PrecioManoObra", document.getElementById('txtPrecioManoObra').value);
		g_ajaxPagina.setParameter("PrecioCompra", vpreciocompra);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			document.getElementById("txtPrecioVenta").value="";
			document.getElementById("lblProducto").innerHTML="";
			document.getElementById("txtIdProductoSeleccionado").value="";
			document.getElementById("txtStockActual").value="";
			//document.getElementById("txtPrecioManoObra").value="";
			document.getElementById("txtPrecioCompra").value="";
			recipiente.focus();
		};
		g_ajaxPagina.request();
		}else{
			alert("Los precios y cantidad deben ser números positivos!!!");
			}
}
</script>
</head>
<body>
<form id="frmMantMovimiento" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<fieldset><legend><strong>DATOS DEL DOCUMENTO:</strong></legend> 
<table border="1">
<?php
require("fun.php");
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
	<?php if($value["idcampo"]==5){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=3",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=3",$value["descripcion"],0,'',$objMantenimiento);?></td>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td>
	<?php }?>
    <?php if($value["idcampo"]==13){?>
	<td><?php echo $value["comentario"];?></td>
    	<td>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optS" value="S" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}}else{ echo "checked=checked";}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    S/.</label>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optD" value="D" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="D"){ echo "checked=checked";}}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    $</label></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==20){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
<?php }?>
</table>
</fieldset>
<fieldset><legend><strong>BUSQUEDA DE PRODUCTOS E INGREDIENTES:</strong></legend> 
<div id="busquedaProducto">
<table><tr><td>Buscar :</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" ></td><td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:buscarProducto()">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="divBusquedaProducto">
</div>
</fieldset>
<fieldset><legend><strong>DATOS DEL PRODUCTO SELECCIONADO:</strong></legend> 
<div id="divProductoSeleccionado">
<table>
<tr>
<td>Producto :</td>
<td><input name="hidden" type="hidden" id="txtIdProductoSeleccionado" value="0"><label id="lblProducto" name="lblProducto">...</label></td>
<td>Unidad :</td>
<td><div id="DivUnidad"></div><!--Aca se genera el combo unidades y el link para ver las unidades(ponerle imagen: Archivo: xajax_prueba2.php funcion:genera_cboUnidad())--></td>
<td>Stock Actual :</td>
<td>
  <input name="txtStockActual" type="text" id="txtStockActual" value="0" size="10" maxlength="10" readonly="readonly"></td><td rowspan="2" valign="bottom"><a href="#" onClick="agregar()">Agregar</a></p></td>
</tr>
<tr>
  <td> Precio Compra:</td>
  <td><input type="text" name="txtPrecioCompra" id="txtPrecioCompra" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td>
  <td>Precio Venta:</td>
  <td><input type="text" name="txtPrecioVenta" id="txtPrecioVenta" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td>
  <td>Cantidad:</td>
  <td><input type="text" name="txtCantidad" id="txtCantidad" value="1" maxlength="10" size="10" onKeyPress="return validarsolonumerosdecimales(event,this.value);">
  </td>
  </tr>
</table>
</div>
</fieldset>
<fieldset><legend><strong>DETALLE DEL DOCUMENTO:</strong></legend> 
<div id="divDetalleAlmacen">
</div>
</fieldset>
<fieldset><legend><strong></strong></legend> 
<?php
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
<?php if($value["idcampo"]==15){?>
	<?php echo $value["comentario"];?> <input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" disabled>
	<?php }?>
    <?php if($value["idcampo"]==16){?>
	<?php echo $value["comentario"];?> <input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" disabled>
	<?php }?>
    <?php if($value["idcampo"]==17){?>
	<?php echo $value["comentario"];?> <input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" disabled></br>
	<?php }?>
    <?php if($value["idcampo"]==24){?>
	<table border="1"><tr><td><?php echo $value["comentario"];?></td>
    	<td><textarea name="txt<?php echo $value["descripcion"];?>" id="txt<?php echo $value["descripcion"];?>" cols="30" rows="3"><?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?></textarea></td>
	<?php }?>
<?php }?>

	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:cargamant.innerHTML='';buscar();"></td>
	</tr>
</table>
</fieldset>
</form>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listAlmacen','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoMovimiento->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoMovimiento->descripcionmant; ?></td>
</tr></table>
</div>
<hr>
</body>
</HTML>