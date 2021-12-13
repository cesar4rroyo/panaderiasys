<?php
require("../modelo/clsMovimiento.php");
require("../modelo/clsPersona.php");
require("../modelo/clsSalon.php");
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

if(isset($_SESSION['R_carroPedido']))
$_SESSION['R_carroPedido']="";

try{
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objPersona = new clsPersona($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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

if($_GET["situacionmesa"]=='O'){
$_GET["accion"]="ACTUALIZAR";
if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarMovimientoxMesa($_GET["idmesa"],5);
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetch();
	}
}
}
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Registro de Pedido de Comensales</title>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<link href="../css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">
<script src="../runtime/lib/aw.js" type="text/javascript"></script>
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
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	/*g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
	getFormData("frmMantPedido");
	
}
function aceptar(){
	if(setValidar("frmMantPedido")){
		if(document.getElementById('divDetallePedido').innerHTML!='' && document.getElementById('divDetallePedido').innerHTML!='Debe Agregar Platos y/o Productos!!!'){
			if(document.getElementById('txtTotal').value>0){
				g_ajaxGrabar.setURL("../controlador/contPedido.php?ajax=true");
				g_ajaxGrabar.setRequestMethod("POST");
				setParametros();
					
				g_ajaxGrabar.response = function(text){
					//loading(false, "loading");
					//buscar();
					alert(text);			
					window.open('mesas.php','_self');
					//cargamant.innerHTML="";
				};
				g_ajaxGrabar.request();
				//loading(true, "loading", "frame", "line.gif",true);
			}else{
				alert("Debe indicar los productos");
			}			
		}else{
			alert("Debe indicar los productos");
		}
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
function centraDivAutorizar(){ 
    var top=(document.body.clientHeight/4)+"px"; 
	var left1=(document.body.clientWidth/2);
	//alert(left1);
	if(left1>=parseInt(document.getElementById("divDatosProductoSeleccionado").style.width)/2){
    	var left=(left1-parseInt(document.getElementById("divDatosProductoSeleccionado").style.width)/2)+"px"; 
	}else{
		var left=0;
	}
	//alert(left);
    document.getElementById("divDatosProductoSeleccionado").style.top=top; 
    document.getElementById("divDatosProductoSeleccionado").style.left=left; 
} 
function buscarProducto(){
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, '" + document.getElementById("txtBuscar").value + "'";
	setRun('listGrillaInterna','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=51&filtro=' + vValor, 'divBusquedaProducto', 'divBusquedaProducto', 'img03');
}
//buscarProducto();
function seleccionarProducto(idproducto){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "seleccionarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			eval(text);
			centraDivAutorizar()
			//document.getElementById("divDatosProductoSeleccionado").style.display="";
			document.getElementById("divDatosProductoSeleccionado").className="muestra";
			document.getElementById("txtPrecioVenta").value=vprecioventa;
			document.getElementById("lblProducto").innerHTML=vproducto;
			document.getElementById("txtIdProductoSeleccionado").value=idproducto;
			document.getElementById("txtStockActual").value=vstockactual;
			document.getElementById("txtPrecioCompra").value=vpreciocompra;
		};
		g_ajaxPagina.request();
}
function seleccionar(idproducto){
		var recipiente = document.getElementById('DivUnidad');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboUnidad");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
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
		
		if(vprecioventa>=0 && vprecioventa!='' && vcantidad>=0 && vcantidad!=''){

		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarProducto");
		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdUnidad", document.getElementById('cboUnidad').value);
		g_ajaxPagina.setParameter("Cantidad", vcantidad);
		g_ajaxPagina.setParameter("PrecioVenta", vprecioventa);
		g_ajaxPagina.setParameter("PrecioCompra", vpreciocompra);
		g_ajaxPagina.setParameter("StockActual", document.getElementById('txtStockActual').value);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			document.getElementById("txtPrecioVenta").value="";
			document.getElementById("lblProducto").innerHTML="";
			document.getElementById("txtIdProductoSeleccionado").value="";
			document.getElementById("txtStockActual").value="";
			document.getElementById("txtPrecioCompra").value="";
			recipiente.focus();
		};
		g_ajaxPagina.request();
		}else{
			alert("Los precios y cantidad deben ser números positivos!!!");
			}
}
function quitar(idproducto){
		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "quitarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			recipiente.focus();
		};
		g_ajaxPagina.request();
}
function agregarDetalleProducto(idmovimiento){
		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarDetallesProducto");
		g_ajaxPagina.setParameter("idmovimiento", idmovimiento);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
		};
		g_ajaxPagina.request();
}
function genera_cboMesas(idsalon,situacion,seleccionado,disabled){
		var recipiente = document.getElementById('divcboMesa');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboMesa");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.setParameter("situacion", situacion);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("disabled", disabled);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
		};
		g_ajaxPagina.request();
}
</script>
<style type="text/css"> 
.oculta {
 display:none;
}

.muestra {
 display:block;
}
</style>
</head>
<body>
<form id="frmMantPedido" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $dato['idmovimiento'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="100%" border="0"><tr><td>
<fieldset><legend><strong>DATOS DEL DOCUMENTO:</strong></legend> 
<table border="0">
<?php
require("../vista/fun.php");
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
	<?php if($value["idcampo"]==5){?>
	<tr><td>N&deg; Pedido</td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $objMantenimiento->generaNumeroSinSerie(5,11,substr($_SESSION["R_FechaProceso"],3,2));
	?>" size="6" maxlength="6" title="Debe indicar el n&uacute;mero de pedido" disabled="disabled"></td>
	<?php }?>
   <?php if($value["idcampo"]==12){?>
	<tr><td>Sal&oacute;n</td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$dato['idsalon'],'',$objSalon,'genera_cboMesas(this.value,"%",'.$dato['idmesa'].',"")'); else echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$_GET['idsalon'],'',$objSalon,'genera_cboMesas(this.value,"N",0,"")');?></td>
	<td><?php echo $value["comentario"];?></td>
    	<td><div id="divcboMesa"></div></td>
	<?php }?>
    <?php if($value["idcampo"]==11){?>
	<tr><td>N&deg; Personas</td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo 1;
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> title='Debe indicar Nro de Personas'></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==21){?>
	<td><?php echo $value["comentario"];?></td><td colspan="3"><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun2("consultarPersonaxRol(1)",$value["descripcion"],$dato['idsucursalresponsable'].'-'.$dato[strtolower($value["descripcion"])],'disabled',$objPersona,''); else echo genera_cboGeneralFun2("consultarPersonaxRol(1)",$value["descripcion"],$_SESSION['R_IdSucursalUsuario'].'-'.$_SESSION['R_IdPersona'],'',$objPersona,'');?></td></tr>
	<?php }?>
<?php }?>
</table>
</fieldset>
</td></tr><tr><td>
<fieldset>
<legend><strong>BUSQUEDA DE PLATOS Y PRODUCTOS:</strong></legend> 
<div id="busquedaProducto">
<table><tr><td>Por descripci&oacute;n :</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" onKeyUp="javascript:buscarProducto()" ></td><td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:buscarProducto()" style="display:none">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="divBusquedaProducto">
</div>
</fieldset>
</td></tr><tr><td>
<div id="divDatosProductoSeleccionado" class="oculta" style="width:600px;position:absolute;">
<?php require("tablaheader.php");?>
<fieldset><legend><strong>DATOS DEL PRODUCTO SELECCIONADO:</strong></legend> 
<div id="divProductoSeleccionado">
<table>
<tr>
<td>Producto :</td>
<td><input name="hidden" type="hidden" id="txtIdProductoSeleccionado" value="0"><label id="lblProducto" name="lblProducto">...</label></td></tr>
<tr>
<td>Unidad :</td>
<td><div id="DivUnidad"></div><!--Aca se genera el combo unidades y el link para ver las unidades(ponerle imagen: Archivo: xajax_prueba2.php funcion:genera_cboUnidad())--></td>
<td>Stock Actual :</td>
<td>
  <input name="txtStockActual" type="text" id="txtStockActual" value="0" size="10" maxlength="10" readonly="readonly"></td>
 </tr>
<tr>
  <td>Precio Ofertado:</td>
  <td><input type="hidden" name="txtPrecioCompra" id="txtPrecioCompra" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"><input type="text" name="txtPrecioVenta" id="txtPrecioVenta" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td>
  <td>Cantidad:</td>
  <td><input type="text" name="txtCantidad" id="txtCantidad" value="1" maxlength="10" size="10" onKeyPress="return validarsolonumerosdecimales(event,this.value);">
  </td>
  </tr>
<tr><td colspan="4" valign="middle" align="center"><!--<a href="#" onClick="agregar()">Agregar</a>-->
  <button type="button" onClick="javascript: agregar();divDatosProductoSeleccionado.className='oculta';"><img src="../img/cart_add.png" align="absbottom" />&nbsp;Agregar</button>
<button type="button" onClick="javascript: divDatosProductoSeleccionado.className='oculta';"><img src="../img/s_cancel.png" align="absbottom" />&nbsp;Cerrar</button>
  </td></tr>
</table>
</div>
</fieldset>
<?php require("tablafooter.php");?>
</div>
</td></tr><tr><td>
<fieldset><legend><strong>DETALLE DEL DOCUMENTO:</strong></legend> 
<div id="divDetallePedido">Debe Agregar Platos y/o Productos!!!</div>
</fieldset>
</td></tr><tr><td>
<fieldset>
<?php
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
    <?php if($value["idcampo"]==24){?>
	<table ><tr><td><?php echo $value["comentario"];?></td>
    	<td><textarea name="txt<?php echo $value["descripcion"];?>" id="txt<?php echo $value["descripcion"];?>" cols="30" rows="3"><?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?></textarea></td></tr>
	<?php }?>
<?php }?>
<tr>
	<td align="center" colspan="2"><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()">&nbsp;<input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript: window.open('mesas.php','_self')"></td>
	</tr>
</table>
</fieldset>
</td></tr></table>
</form>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:window.open('listPedido.php?id_clase=50','_self')"><?php echo $datoMovimiento->descripcion; ?></a></td><td>></td>
    <td><a href="#" onClick="javascript:window.open('mesas.php','_self')">Mesas</a></td><td>></td>
	<td><?php echo $datoMovimiento->descripcionmant; ?></td>
</tr></table>
</div>
<script>
<?php if($_GET['accion']=='ACTUALIZAR'){?>
agregarDetalleProducto(<?php echo $dato['idmovimiento'];?>);
genera_cboMesas(<?php echo $dato['idsalon'];?>,'%',<?php echo $dato['idmesa'];?>,'');
<?php }else{
	if($_GET["situacionmesa"]=='R'){?>
		genera_cboMesas(<?php echo $_GET['idsalon'];?>,'%',<?php echo $_GET['idmesa'];?>,'');
<?php }else{?>
		genera_cboMesas(<?php echo $_GET['idsalon'];?>,'N',<?php echo $_GET['idmesa'];?>,'');
<?php }
}?>
</script>
</body>
</HTML>