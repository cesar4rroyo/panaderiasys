<?php
require("../modelo/clsMovimiento.php");
require("../modelo/clsPersona.php");
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

if(isset($_SESSION['R_carroCompra']))
$_SESSION['R_carroCompra']="";

try{
    $objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objPersona = new clsPersona($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
}
if($_GET["accion"]=="ACTUALIZAR"){
	if($_GET["situacionmesa"]=='O'){	
	   $rst = $objMantenimiento->consultarMovimientoxMesa($_GET["idmesa"],1);
	}else{
	   $rst = $objMantenimiento->consultarMovimiento(1,1,'2',1,$_GET["Id"],1);
	}
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
	/*g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
	getFormData("frmMantCompra");
	
}
function aceptar(){
	if(setValidar("frmMantCompra")){
		if(document.getElementById('divDetalleCompra').innerHTML!='' && document.getElementById('divDetalleCompra').innerHTML!='Debe Agregar Platos y/o Productos!!!'){
			if(parseFloat(document.getElementById('txtTotal').value)>0){
				g_ajaxGrabar.setURL("controlador/contCompra.php?ajax=true");
				g_ajaxGrabar.setRequestMethod("POST");
				setParametros();
					
				g_ajaxGrabar.response = function(text){
					loading(false, "loading");
					alert(text);
					if(text=='Guardado correctamente'){
					   cargamant.innerHTML="";
					   buscar();
					}
				};
				g_ajaxGrabar.request();
				loading(true, "loading", "frame", "line.gif",true);
			}else{
				alert("Debe indicar los productos");
			}			
		}else{
			alert("Debe indicar los productos");
		}
	}
}
function consultaRUC(){
    var ruc = $("#txtNroDoc").val();
    $.ajax({
        type: 'GET',
        url: "SunatPHP/demo.php",
        data: "ruc="+ruc,
        beforeSend(){
        	alert("Consultando...");
        },
        success: function (data, textStatus, jqXHR) {
        	alert("Datos Recibidos");
            $("#txtNombres").val(data.RazonSocial);
            $("#txtDireccion").val(data.Direccion);
            $("#txtNombres").focus();
            $("#txtDireccion").focus();
        }
    });
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
        $("#txtBuscar").focus();
}
function cerrarDatosProductoSeleccionado(){
    document.getElementById('divDatosProductoSeleccionado').className='oculta';
    $('#divDatosProductoSeleccionado').hide();
    $('#txtBuscar').focus();
}
function buscarProducto(e){ 
	if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
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

		vValor = "'"+vOrder + "'," + vBy + ", 0, '" + vDescripcion + "',"+ document.getElementById("cboCategoria").value + "," + document.getElementById("cboMarca").value + ", '" + document.getElementById("txtCodigoBuscar").value + "','','S'";
		setRun('vista/listGrilla2InternaTeclado','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=43&filtro=' + vValor, 'divBusquedaProducto', 'divBusquedaProducto', 'img03');
		document.getElementById('divBusquedaProducto').style.display='';
	}
}
//buscarProducto();
function seleccionarProducto(idproducto,idsucursalproducto){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "seleccionarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			eval(text);
			centraDivAutorizar();
			document.getElementById("divDatosProductoSeleccionado").style.display="";
			document.getElementById("divDatosProductoSeleccionado").className="muestra";
            $('#divDatosProductoSeleccionado').show();
			document.getElementById("txtPrecioVenta").value=vprecioventa;
			document.getElementById("lblProducto").innerHTML=vproducto;
			document.getElementById("txtIdProductoSeleccionado").value=idproducto;
			document.getElementById("txtStockActual").value=vstockactual;
			document.getElementById("txtPrecioCompra").value=vpreciocompra;
			document.getElementById("txtIdSucursalProductoSeleccionado").value=idsucursalproducto;
            $("#txtCantidad").focus();
            document.getElementById("txtCantidad").select();
		};
		g_ajaxPagina.request();
}
function seleccionar(idproducto,idsucursalproducto){
		var recipiente = document.getElementById('DivUnidad');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
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
function agregar(){
		var vprecioventa=document.getElementById('txtPrecioVenta').value;
		var vcantidad=document.getElementById('txtCantidad').value;
		var vpreciocompra=document.getElementById('txtPrecioCompra').value;
		
		if(vprecioventa>=0 && vprecioventa!='' && vcantidad>=0 && vcantidad!=''){

		var recipiente = document.getElementById('divDetalleCompra');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarProducto");
		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdSucursalProducto", document.getElementById('txtIdSucursalProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdUnidad", document.getElementById('cboUnidad').value);
		g_ajaxPagina.setParameter("Cantidad", vcantidad);
		g_ajaxPagina.setParameter("PrecioVenta", vprecioventa);
		g_ajaxPagina.setParameter("PrecioCompra", vpreciocompra);
		g_ajaxPagina.setParameter("StockActual", document.getElementById('txtStockActual').value);
		if(document.getElementById('chkIgv').checked) g_ajaxPagina.setParameter("IncluyeIgv", 'S'); else g_ajaxPagina.setParameter("IncluyeIgv", 'N');
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			document.getElementById("txtPrecioVenta").value="";
			document.getElementById("lblProducto").innerHTML="";
			document.getElementById("txtIdProductoSeleccionado").value="";
			document.getElementById("txtIdSucursalProductoSeleccionado").value="";
			document.getElementById("txtStockActual").value="";
			document.getElementById("txtPrecioCompra").value="";
			document.getElementById("txtBuscar").select();
		};
		g_ajaxPagina.request();
		}else{
			alert("Los precios y cantidad deben ser números positivos!!!");
			}
}
function calcularPrecioCompra(){
    if($("#txtCantidad").val()==""){
        var cant=0;
    }else{
        var cant=parseFloat($("#txtCantidad").val());
    }
    if($("#txtSubTotalBusqueda").val()==""){
        var sub=0;
    }else{
        var sub=parseFloat($("#txtSubTotalBusqueda").val());
    }
    var pre=Math.round((sub/cant)*10000)/10000;
    $("#txtPrecioCompra").val(pre);
}

function quitar(idproducto,idsucursalproducto,preciocompra){
		var recipiente = document.getElementById('divDetalleCompra');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "quitarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("PrecioCompra", preciocompra);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			recipiente.focus();
		};
		g_ajaxPagina.request();
}
function agregarDetalleProducto(idmovimiento){
		var recipiente = document.getElementById('divDetalleCompra');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarDetallesProducto");
		g_ajaxPagina.setParameter("idmovimiento", idmovimiento);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
		};
		g_ajaxPagina.request();
}

function cambiaPrecioUnidad(idunidad){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
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
function cambiaStock(idunidad){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "cambiaStock");
		g_ajaxPagina.setParameter("IdUnidad", idunidad);
		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdSucursalProducto", document.getElementById('txtIdSucursalProductoSeleccionado').value);
		g_ajaxPagina.response = function(text){
				document.getElementById('txtStockActual').value=text;
		};
		g_ajaxPagina.request();
}
function asignar(){
	if(document.getElementById('cboIdTipoDocumento').value=="1" || document.getElementById('cboIdTipoDocumento').value=="2"){		
		document.getElementById('txtIdSucursalPersona').value="";
		document.getElementById('txtIdPersona').value="";
		document.getElementById('txtPersona').value="";
		if(document.getElementById('cboIdTipoDocumento').value=="1"){
			document.getElementById('divImpuesto').style.display="none";
		}else{
			document.getElementById('divImpuesto').style.display="";
		}
		document.getElementById('txtNumero').value=""
	}else{
		document.getElementById('divImpuesto').style.display="none";
		document.getElementById('chkIgv').checked = true;
		document.getElementById('txtIdSucursalPersona').value=<?php echo $_SESSION['R_IdSucursal']?>;
		document.getElementById('txtIdPersona').value=2;
		document.getElementById('txtPersona').value="VARIOS";
		document.getElementById('txtNumero').value=document.getElementById('txtNumeroTicket').value
	}
	if(document.getElementById('divDetalleCompra').innerHTML!='' && document.getElementById('divDetalleCompra').innerHTML!='Debe Agregar Platos y/o Productos!!!'){
		actualizarDetalleCompra();
	}
}
function actualizarDetalleCompra(){
	var recipiente = document.getElementById('divDetalleCompra');
	g_ajaxPagina = new AW.HTTP.Request;
	g_ajaxPagina.setURL("vista/ajaxCompra.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "actualizarDetalleCompra");
	//if(document.getElementById("optS").checked){
	g_ajaxPagina.setParameter("Moneda", "S");
	/*}
	if(document.getElementById("optD").checked){
		g_ajaxPagina.setParameter("Moneda", "D");
	}*/
	if(document.getElementById('chkIgv').checked) {
		g_ajaxPagina.setParameter("IncluyeIgv", 'S');
	}else{
		g_ajaxPagina.setParameter("IncluyeIgv", 'N');
	}
	g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
	g_ajaxPagina.response = function(text){
		recipiente.innerHTML = text;
	};
	g_ajaxPagina.request();
}

function calcularAfecto(check,idproducto){
	var recipiente = document.getElementById('divDetalleCompra');
	g_ajaxPagina = new AW.HTTP.Request;
	g_ajaxPagina.setURL("vista/ajaxCompra.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "actualizarDetalleCompraAfecto");
	g_ajaxPagina.setParameter("check", check);
    g_ajaxPagina.setParameter("idproducto", idproducto);
	g_ajaxPagina.setParameter("Moneda", "S");
	if(document.getElementById('chkIgv').checked) {
		g_ajaxPagina.setParameter("IncluyeIgv", 'S');
	}else{
		g_ajaxPagina.setParameter("IncluyeIgv", 'N');
	}
	g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
	g_ajaxPagina.response = function(text){
		recipiente.innerHTML = text;
	};
	g_ajaxPagina.request();
        
}

<!--LAS SIGUIENTES FUNCIONES LAS USO PARA LLAMAR AL XAJAX Y A LAS FUNCIONES DEL AUTOCOMPLETAR-->
function listadoPersona(div,idrol,nombres,idtipodocumento){
	var recipiente = document.getElementById(div);
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "BuscaPersona");
	g_ajaxPagina.setParameter("idrol", idrol);
	g_ajaxPagina.setParameter("nombres", nombres);
	g_ajaxPagina.setParameter("div", div);
/*	if(idtipodocumento==5){
		g_ajaxPagina.setParameter("tipopersona", "RUC");
	}else{
		g_ajaxPagina.setParameter("tipopersona", "DNI");
	}*/
	g_ajaxPagina.response = function(xform){
		recipiente.innerHTML = xform
	};
	g_ajaxPagina.request();
}

function buscarPersona(e,div){
  if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
    
    if(keyc == 38 || keyc == 40 || keyc == 13) {
		if(document.getElementById(div).innerHTML!=""){
        autocompletar_teclado2(div, 'tablaPersona', keyc);
		}
    }else{
		if(div=='divregistrosPersona'){
			//si presiona retroceso o suprimir
			if(keyc == 8 || keyc == 46) {
				document.getElementById('txtIdPersona').value="";
			}
			listadoPersona(div,'4',document.getElementById('txtPersona').value,0);
		}else{
			//si presiona retroceso o suprimir
			/*if(keyc == 8 || keyc == 46) {
				document.getElementById('txtIdMadre').value="";
			}
			listadoPersona(div,1,document.getElementById('txtMadre').value);*/
		}
  		eval(div+'.style.display="";');
		window.setTimeout(div+'.style.display="";', 300);
  }
}
function mostrarPersona(idsucursal,id,div){
		var g_ajaxPagina = new AW.HTTP.Request;  
		g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "mostrarPersona");
		g_ajaxPagina.setParameter("idsucursal", idsucursal);
		g_ajaxPagina.setParameter("id", id);
		g_ajaxPagina.response = function(xform){
			eval(xform);
			if(div=='divregistrosPersona'){
				document.getElementById('txtIdSucursalPersona').value = idsucursal;
				document.getElementById('txtIdPersona').value = id;
				document.getElementById('txtPersona').value = vNombres;
				divregistrosPersona.style.display="none";
			}else{
				/*document.getElementById('txtIdMadre').value = id;
				document.getElementById('txtMadre').value = vNombres;
				divregistrosMadre.style.display="none";*/
			}
		};
		g_ajaxPagina.request();
}
<?php if($_GET['accion']=='ACTUALIZAR'){?>
agregarDetalleProducto(<?php echo $dato['idmovimiento'];?>);
<?php }else{
}?>
function centraDivAutorizar(){ 
    var top=(document.body.clientHeight/4)+"px"; 
	var left1=(document.body.clientWidth/2);
    var left=(left1-parseInt(document.getElementById("divDatosProductoSeleccionado").style.width)/2)+"px"; 
    document.getElementById("divDatosProductoSeleccionado").style.top=top; 
    document.getElementById("divDatosProductoSeleccionado").style.left=left; 
} 
document.getElementById('txtBuscar').select();
<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantCompra','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantCompra','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
function listadoPersona2(){
    $.ajax({
        url: "vista/ajaxPersonaMaestro.php",
        type: 'POST',
        data: "accion=BuscaPersonaJSON&idrol=1,3,4,5&nombres=&tipopersona=",
        success: function(a) {
            a = JSON.parse(a);
            var datos = a.datos;
            //console.log($(".autocomplete-content"));
            $(".autocomplete-content").remove();
            $("#txtPersona").autocomplete({
                data: datos
            },selecctionarPersona,"");
        }
    });
}
function selecctionarPersona(dato){
    var ids = dato.split("|");
    $('#txtIdSucursalPersona').val(ids[0]);
    $('#txtIdPersona').val(ids[1]);
    $('#txtPersona').attr("readonly",true);
}
function limpiarCamposPersona(){
    $('#txtIdSucursalPersona').val("");
    $('#txtIdPersona').val("");
    $('#txtPersona').attr("readonly",false);
    $('#txtPersona').val("");
    $('#txtPersona').val("");
    $('#txtPersona').focus();
}
listadoPersona2();
function verificaNroDoc(nro,tipo)
{
		var g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "verificaNroDoc");
		g_ajaxPagina.setParameter("nrodoc", nro);
		g_ajaxPagina.setParameter("tipo", tipo);
		g_ajaxPagina.response = function(text){
			eval(text);
			if(vCant>0){
				$("#LabelVerificaNroDoc").show();
                                $("#btnAceptarModalPersona").attr("disabled");
			}else{
				$("#LabelVerificaNroDoc").hide();
                                $("#btnAceptarModalPersona").removeAttr("disabled");
			}
                        console.log(text);
		};
		g_ajaxPagina.request();
}
function setParametrosModalPersona(){
	g_ajaxGrabar.setParameter("accion", "NUEVO");
	g_ajaxGrabar.setParameter("clase", "23");
	//g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
    g_ajaxGrabar.setParameter("txtIdPersona", "");
    g_ajaxGrabar.setParameter("txtIdSucursal", "<?=$_SESSION["R_IdSucursal"]?>");
    g_ajaxGrabar.setParameter("txtIdPersonaMaestro", "");
    g_ajaxGrabar.setParameter("txtDireccion", $("#txtDireccion").val());//alerta("DATOS");
    g_ajaxGrabar.setParameter("txtEmail", "");
    g_ajaxGrabar.setParameter("txtTelefonoFijo", "");
    g_ajaxGrabar.setParameter("txtTelefonoMovil", "");
    g_ajaxGrabar.setParameter("cboDpto", "1347");
    g_ajaxGrabar.setParameter("cboProv", "1348");
    g_ajaxGrabar.setParameter("cboDist", "1349");
    g_ajaxGrabar.setParameter("txtImagen", "");
    g_ajaxGrabar.setParameter("chkCompartido", "S");
    g_ajaxGrabar.setParameter("cboIdRol", "4");
	g_ajaxGrabar.setParameter("txtApellidos", $("#txtApellidos").val());
	g_ajaxGrabar.setParameter("txtNombres", $("#txtNombres").val());
	g_ajaxGrabar.setParameter("cboTipoPersona", $("#cboTipoPersona").val());
	g_ajaxGrabar.setParameter("txtNroDoc", $("#txtNroDoc").val());
	if($("#optM").length>1){
            if(document.getElementById("optM").checked){
                g_ajaxGrabar.setParameter("optSexo", "M");
            }
            if(document.getElementById("optF").checked){
		g_ajaxGrabar.setParameter("optSexo", "F");
            }
        }else{
            g_ajaxGrabar.setParameter("optSexo", "");
        }
	g_ajaxGrabar.setParameter("txtFechaNac", "");
}
function aceptarModalPersona(){
    g_ajaxGrabar.setURL("controlador/contPersona.php?ajax=true");
    g_ajaxGrabar.setRequestMethod("POST");
    setParametrosModalPersona();
    g_ajaxGrabar.response = function(text){
            loading(false, "loading");
            alert(text);
            listadoPersona2();
            $('#modalNuevoPersona').closeModal();
            $("#txtPersona").val();
            $("#txtPersona").removeAttr("readonly");
            $("#txtPersona").focus();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "contenido", "line.gif",true);
}

function validarFormaPago(forma){
    if(forma=="A"){//Contado
        $("#tdDias").css("display","none");
        $("#tdDias2").css("display","none");
        $("#tdConcepto").css("display","none");
        $("#tdConcepto2").css("display","none");
    }else{
        $("#tdDias").css("display","");
        $("#tdDias2").css("display","");        
        $("#tdConcepto").css("display","none");
        $("#tdConcepto2").css("display","none");
    }
}
</script>
</head>
<body>
    <div class="container Mesas">
        <form id="frmMantCompra" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $dato['idmovimiento'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="100%" border="0"><tr><td>
<fieldset><legend><strong>DATOS DEL DOCUMENTO:</strong></legend> 
<table border="0">
<?php
require("fun.php");
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
	<?php if($value["idcampo"]==5){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR")
$num=htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else $num=$objMantenimiento->generaNumero(1,3,substr($_SESSION["R_FechaProceso"],6,4));
	?><input type="hidden" id="txtNumeroTicket" value="<?php echo $num;?>"><input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php echo $num;?>" size="15" maxlength="15" title="Debe indicar el n&uacute;mero de Documento de Compra" <?php if($_GET["accion"]=="ACTUALIZAR")echo 'disabled';?>></td>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=1",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,"asignar()"); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=1",$value["descripcion"],3,'',$objMantenimiento,"asignar()");?></td>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<td><?php echo $value["comentario"];?></td>
        <td><div class="input-field inline"><input type="date" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo date_format(date_create_from_format("d/m/Y",substr($dato[strtolower($value["descripcion"])],0,10)),"Y-m-d"); else echo date_format(date_create_from_format("d/m/Y",$_SESSION["R_FechaProceso"]),"Y-m-d");?>" title="Debe indicar la fecha" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'readonly';?>></div></td>
    	<!--td><input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo date_format(date_create_from_format("d/m/Y",$_SESSION["R_FechaProceso"]),"Y-m-d");?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar" type="button" class="boton" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>><img src="img/date.png" width="16" height="16"> </button></td-->
	<?php }?>
    <?php if($value["idcampo"]==20){?>
	<tr><td>Persona</td>
    	<td>
            <div class="col s12 valign-wrapper">
                <div class="input-field inline col s8 m8 l10">
            <input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato['idsucursalpersona']), ENT_QUOTES, "UTF-8"); else echo "1";
	?>" title="Debe indicar un cliente">
        <input type="hidden" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo "3";
	?>" title="Debe indicar un cliente">
        <input name="txtPersona" autocomplete="off" readonly="" id="txtPersona" value="<?php if($_GET["accion"]=="ACTUALIZAR") echo $dato["cliente"]; else echo 'VARIOS';?>">
                </div>
        <div class="col s2 m2 l1 center">
            <button type="button" class="btn-floating red" onclick="limpiarCamposPersona();"><i class="material-icons">close</i></button>
        </div>
        <div class="col s2 m2 l1 center">
            <button type="button" onclick="modalNuevoPersona()" class="btn-floating light-green accent-1"><i class="material-icons black-text">add</i></button>
        </div>
    </div>
<div id="divregistrosPersona" class="autocompletar" style="display:none"></div>
</td>
	<?php }?>
    <?php if($value["idcampo"]==21){?>
	<td><?php echo $value["comentario"];?></td><td colspan="3"><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun2("consultarPersonaxRol(1)",$value["descripcion"],$dato['idsucursalresponsable'].'-'.$dato[strtolower($value["descripcion"])],'disabled',$objPersona,''); else echo genera_cboGeneralFun2("consultarPersonaxRol(1)",$value["descripcion"],$_SESSION['R_IdSucursalUsuario'].'-'.$_SESSION['R_IdPersona'],'',$objPersona,'');?></td></tr>
	<?php }?>
<?php }?>
<tr><td><div id="divImpuesto" style="display:none"><input name="chkIgv" type="checkbox" onClick="asignar()" value="S" checked id="chkIgv"><label for="chkIgv">Incluido IGV</label></div></td></tr>
</table>
</fieldset>
</td></tr>
<tr <?php if($_GET["accion"]=="ACTUALIZAR") echo "style='display:none;'";?>><td>
<fieldset>
<legend><strong>BUSQUEDA DE PRODUCTOS:</strong></legend> 
<div id="busquedaProducto">
<table><tr><td>Por Descripci&oacute;n :</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" onKeyUp="javascript: if(this.value!=''){buscarProducto(event);}"></td><td>C&oacute;digo :</td><td><input type="text" id="txtCodigoBuscar" name="txtCodigoBuscar" value=""  size="6" maxlength="6" onKeyPress="return validarsolonumeros(event)" onKeyUp="javascript: if(this.value!=''){buscarProducto(event);}"></td><td>Categor&iacute;a :</td><td><?php echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")","Categoria",0,'',$objMantenimiento,'buscarProducto(event)', 'Todos');
?></td><td>Marca :</td><td><?php
echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'","Marca",0,'',$objMantenimiento, 'buscarProducto(event)', 'Todos');
?></td><td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:buscarProducto()" style="display:none">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="divBusquedaProducto" class="autocompletar2">
</div>
</fieldset>
</td></tr><tr><td>
<div id="divDatosProductoSeleccionado" style="display:none;" style="width:600px;position:absolute;">
<div class="container Mesas">
<fieldset><legend><strong>DATOS DEL PRODUCTO SELECCIONADO:</strong></legend> 
<div id="divProductoSeleccionado">
<table>
<tr>
<?php $style = "text-transform: capitalize; color: black; font-size: 1.5rem;";?>
<td>Producto :</td>
<td><input name="txtIdProductoSeleccionado" type="hidden" id="txtIdProductoSeleccionado" value="0"><input name="txtIdSucursalProductoSeleccionado" type="hidden" id="txtIdSucursalProductoSeleccionado" value="0">
    <label id="lblProducto" name="lblProducto" style="<?php echo $style;?>">...</label></td>
<td>Unidad :</td>
<td><div id="DivUnidad"></div><!--Aca se genera el combo unidades y el link para ver las unidades(ponerle imagen: Archivo: xajax_prueba2.php funcion:genera_cboUnidad())--></td>
<td align="center" style="display:none"><label><input type="checkbox" id="chkLlevar" name="chkLlevar" onchange='cambiaPrecioUnidad(cboUnidad.value)'>Para&nbsp;llevar</label></td>
<td>Stock Actual :</td>
<td>
    <input name="txtStockActual" style="<?php echo $style;?>" type="text" id="txtStockActual" value="0" size="10" maxlength="10" disabled></td><td rowspan="2" align="center"><!--<a href="#" onClick="agregar()">Agregar</a>-->
  </td>
</tr>
<tr>
  <td>Precio Compra:</td>
  <td><input type="text" style="<?php echo $style;?>" readonly="" name="txtPrecioCompra" id="txtPrecioCompra" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td>
  <td hidden="">Precio Venta:</td>
  <td hidden=""><input type="text" style="<?php echo $style;?>" name="txtPrecioVenta" id="txtPrecioVenta" value="" maxlength="10" size="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td>
  <td>Cantidad:</td>
  <td><input type="text" style="<?php echo $style;?>" name="txtCantidad" id="txtCantidad" value="1" maxlength="10" size="10" onkeyup="calcularPrecioCompra();" onKeyPress="if (event.keyCode==13){agregar();document.getElementById('divDatosProductoSeleccionado').className='oculta';}else{return validarsolonumerosdecimales(event,this.value);}"></td>
  <td>Sub Total:</td>
  <td><input type="text" style="<?php echo $style?>>" name="txtSubTotalBusqueda" id="txtSubTotalBusqueda" value="1" maxlength="10" size="10" onkeyup="calcularPrecioCompra();" onKeyPress="if (event.keyCode==13){agregar();document.getElementById('divDatosProductoSeleccionado').className='oculta';}else{return validarsolonumerosdecimales(event,this.value);}"/></td>
  </tr>
</table>
    <div class="row col s12">
        <div class="right">
            <button type="button" class="btn green accent-4" onClick="javascript: agregar();cerrarDatosProductoSeleccionado();">Agregar<i class="material-icons right">add</i></button>
            <button type="button" class="btn red accent-3" onClick="javascript: cerrarDatosProductoSeleccionado();">Cerrar<i class="material-icons right">clear</i></button>
        </div>
    </div>
</div>
</fieldset>
</div>
</div>
</td></tr>
<tr <?php if($_GET["accion"]=="ACTUALIZAR") echo "style='display:none;'";?>><td>
<fieldset><legend><strong>DETALLE DEL DOCUMENTO:</strong></legend> 
<div id="divDetalleCompra">Debe Agregar Platos y/o Productos!!!</div>
<div hidden="">
    <table class="striped bordered highlight">
        <thead>
            <th class="center">CODIGO</th>
            <th class="center">PRODUCTO</th>
            <th class="center">UNIDAD</th>
            <th class="center">CANTIDAD</th>
            <th class="center">PRECIO COMPRA</th>
            <th class="center">PRECIO VENTA</th>
            <th class="center">SUBTOTAL</th>
            <th></th>
        </thead>
        <tbody>
            <td class="center">017</td>
            <td class="center">017</td>
            <td class="center">017</td>
            <td class="center">017</td>
            <td class="center">017</td>
            <td class="center">017</td>
            <td class="center">017</td>
            <td class="center"><button class="btn-floating red tiny"><i class="material-icons">clear</i></button></td>
        </tbody>
        <tfoot>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class="center">SUBTOTAL</th>
            <th></th>
            <th></th>
        </tfoot>
    </table>
</div>
</fieldset>
</td></tr><tr><td>
<fieldset>
<?php
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
    <?php if($value["idcampo"]==24){?>
	<table >
        <tr>
            <td>Forma Pago</td>
            <td><select id="cboFormaPago" name="cboFormaPago" onchange="validarFormaPago(this.value)">
                    <option value="A">Contado</option>
                    <option value="B">Credito</option>
                </select>
            </td>
            <td id="tdDias" style="display: none;">Dias</td>
            <td id="tdDias2" style="display: none;"><input type="text" id="txtDias" name="txtDias" /></td>
            <td id="tdConcepto" style="display: none;">Concepto</td>
            <td id="tdConcepto2" style="display: none;"><?php echo genera_cboGeneralSQL("select * from conceptopago where tipo='E' and estado='N' and idconceptopago<>2",'ConceptoPago','','',$objMantenimiento,"");?></td>
            <td><?php echo $value["comentario"];?></td>
            <td colspan="2"><textarea class="materialize-textarea" name="txt<?php echo $value["descripcion"];?>" id="txt<?php echo $value["descripcion"];?>" cols="30" rows="3"><?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?></textarea></td>
	<?php }?>
<?php }?>
	</tr>
</table>
</fieldset>
</td></tr></table>
    <?php include ('./footerMantenimiento.php');?>
        </form>
    </div>
    <div class="modalNuevoPersona">
        <div id="modalNuevoPersona" class="modal modal-fixed-footer orange lighten-3">
            <div class="modal-content">
              <div class="white" style="padding: 10px;border-radius: 10px;">
                  <form id="frmMantPersonaMaestro" method="POST" action="">
                <div class="row">
                  <div class="col s12">
                      <div class="input-field inline">
                        <select id="cboTipoPersona" name="cboTipoPersona" onchange="cambiarTipoPersona('contenido',$(this).val());">
                            <option value="NATURAL">Natural</option>
                            <option value="VARIOS">Varios</option>
                        </select>
                        <label for="monto">Tipo Persona</label>
                      </div>
                  </div>
                  <div class="col s12" id="contenido"></div>
                </div>
                  </form>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
                <button id="btnAceptarModalPersona" disabled="" type="button" onclick="aceptarModalPersona()" class="waves-effect waves-green btn light-green accent-1 black-text">Agregar<i class="material-icons right">add</i></button>
            </div>
        </div>
    </div>
</body>
</HTML>