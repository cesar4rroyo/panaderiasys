<?
session_start();
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function inicio(){
    document.getElementById("url").value="vista/listplatos";
    document.getElementById("par").value="&mesa=<?=$_GET["mesa"]?>&categoria=<?=$_GET["categoria"]?>&idcategoria=<?=$_GET["idcategoria"]?>&clase=Producto&nombre=Producto&id_clase=45&filtro=<?=$_GET["filtro"]?>";
    document.getElementById("div").value="cargagrilla";
    document.getElementById("msj").value="cargagrilla";
    document.getElementById("img").value="imgloading";
}
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
			//document.getElementById("divDatosProductoSeleccionado").style.display="";
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
        g_ajaxPagina.setParameter("class", "zoom");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
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
			eval(text);
			document.getElementById('txtPrecioCompra').value=vpreciocompra;
			document.getElementById('txtPrecioVenta').value=vprecioventa;
		};
		g_ajaxPagina.request();
}

function agregar(){
		var vprecioventa=document.getElementById('txtPrecioVenta').value;
		var vcantidad=document.getElementById('txtCantidad').value;
		var vpreciocompra=document.getElementById('txtPrecioCompra').value;
		
		if(vprecioventa>=0 && vprecioventa!='' && vcantidad>=0 && vcantidad!=''){
        
        document.getElementById("frame").style.display="";
        
		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarProductoMozo");
		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdSucursalProducto", document.getElementById('txtIdSucursalProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdUnidad", document.getElementById('cboUnidad').value);
		g_ajaxPagina.setParameter("Cantidad", vcantidad);
		g_ajaxPagina.setParameter("PrecioVenta", vprecioventa);
		g_ajaxPagina.setParameter("PrecioCompra", vpreciocompra);
        g_ajaxPagina.setParameter("class", "zoom");
        g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").value);
		g_ajaxPagina.setParameter("StockActual", document.getElementById('txtStockActual').value);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			document.getElementById("txtPrecioVenta").value="";
			document.getElementById("lblProducto").innerHTML="";
			document.getElementById("txtIdProductoSeleccionado").value="";
			document.getElementById("txtIdSucursalProductoSeleccionado").value="";
			document.getElementById("txtStockActual").value="";
			document.getElementById("txtPrecioCompra").value="";
            document.getElementById("txtAbreviatura").value="";
			document.getElementById("txtBuscar").select();
		};
		g_ajaxPagina.request();
        document.getElementById("cargagrilla").innerHTML="";
        document.getElementById("cargagrilla").style.display="none";
        
        document.getElementById("url").value="vista/frmMozo";
        document.getElementById("par").value="&id_clase=0";
        document.getElementById("div").value="frame";
        document.getElementById("msj").value="frame";
        document.getElementById("img").value="imgloading";
		}else{
			alert("Los precios y cantidad deben ser números positivos!!!");
		}
}

function ingresarcantidad(numero){
    var campo = document.getElementById("txtCampo").value;
    document.getElementById(campo).value=document.getElementById(campo).value + numero;
    
}
seleccionar('<?=$_GET["idproducto"]?>','<?=$_GET["idsucursalproducto"]?>');
inicio();
</script>
<link href="../css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">
</head>
<body>
<center>
<div id="divDatosProductoSeleccionado"  >
<?php require("fun.php"); ?>
<?php require("tablaheaderzoom.php");?>
<fieldset><legend><strong class="zoom">DATOS DEL PRODUCTO SELECCIONADO:</strong></legend> 
<div id="divProductoSeleccionado" class="zoom">
<input type="hidden" id="txtCampo" value="txtCantidad" />
<table align="center">
<tr>
<td class="zoom">Producto :</td>
<td><input name="txtIdProductoSeleccionado" type="hidden" id="txtIdProductoSeleccionado" value="0">
<input name="txtIdSucursalProductoSeleccionado" type="hidden" id="txtIdSucursalProductoSeleccionado" value="0">
<label class="zoom" id="lblProducto" name="lblProducto">...</label></td>
<td class="zoom">Unidad :</td>
<td class="zoom"><div id="DivUnidad" class="zoom"></div><!--Aca se genera el combo unidades y el link para ver las unidades(ponerle imagen: Archivo: xajax_prueba2.php funcion:genera_cboUnidad())--></td>
<td align="center" style="display: none;"><label class="zoom"><input class="zoom" type="checkbox" id="chkLlevar" name="chkLlevar" onchange='cambiaPrecioUnidad(document.getElementById("cboUnidad").value)' />Para&nbsp;llevar</label></td>
<td style="display:none">Stock Actual :</td>
<td style="display:none">
  <input class="zoom" name="txtStockActual" type="text" id="txtStockActual" value="0" size="10" maxlength="10" disabled></td>
</tr>
<tr>
  <td class="zoom">Precio Ofertado:</td>
  <td><input class="zoom" type="hidden" name="txtPrecioCompra" id="txtPrecioCompra" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"><input class="zoom" type="text" name="txtPrecioVenta" id="txtPrecioVenta" value="" maxlength="10"  size="3" <?php if($_GET["idproducto"]!=219 && $_GET["idproducto"]!=220) echo ''; else echo "onfocus=javascript:document.getElementById('txtCampo').value='txtPrecioVenta'"; ?> onKeyPress="return validarsolonumerosdecimales(event,this.value);" ></td>
  <td class="zoom">Cantidad:</td>
  <td><input class="zoom" type="text" name="txtCantidad" id="txtCantidad" value="" maxlength="10" size="10" onKeyPress="if (event.keyCode==13){agregar();document.getElementById('divDatosProductoSeleccionado').className='oculta';}else{return validarsolonumerosdecimales(event,this.value);}" onfocus="javascript:document.getElementById('txtCampo').value='txtCantidad'"  />
  </td>
  <td valign="middle" align="center"><!--<a href="#" onClick="agregar()">Agregar</a>-->
  <button class="zoom2" type="button" onClick="javascript: agregar();"><img src="img/cart_add.png" align="absbottom" height="30px" width="30px" />&nbsp;Agregar</button>
<!--<button type="button" onClick="javascript: document.getElementById('divDatosProductoSeleccionado').className='oculta';"><img src="img/s_cancel.png" align="absbottom" />&nbsp;Cerrar</button>-->
  </td>
  </tr>
  <tr>
  <td colspan="5" align="center">
<table  align="center">
    <tr>
        <td align="center" colspan="2">
            <? genera_bloqueNumerico("zoom2","txtCantidad","ingresarcantidad"); ?>
        </td>
    </tr>	
</table>
  </td>
  </tr>
</table>
</div>
</fieldset>
<?php require("tablafooterzoom.php");?>
</div>
</center>
</body>