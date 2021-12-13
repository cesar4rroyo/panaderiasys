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
	   $rst = $objMantenimiento->consultarMovimientoxMesa($_GET["idmesa"],8);
	}else{
	   $rst = $objMantenimiento->consultarMovimiento(1,1,'2',1,$_GET["Id"],8);
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
		if(parseFloat(document.getElementById('txtTotal').value)>0){
			g_ajaxGrabar.setURL("controlador/contOtraCompra.php?ajax=true");
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
			alert("Debe indicar el total");
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
function ocultarResultadoListGrillaInterna(){
	document.getElementById('divBusquedaProducto').style.display='none';
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
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboUnidad");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
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
function quitar(idproducto,idsucursalproducto){
		var recipiente = document.getElementById('divDetalleCompra');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxCompra.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "quitarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
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
<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantOtraCompra','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantOtraCompra','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
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
        g_ajaxGrabar.setParameter("txtIdSucursal", "1");
        g_ajaxGrabar.setParameter("txtIdPersonaMaestro", "");
        g_ajaxGrabar.setParameter("txtDireccion", $("#txtDireccion").val());
        g_ajaxGrabar.setParameter("txtEmail", "");
        g_ajaxGrabar.setParameter("txtTelefonoFijo", "");
        g_ajaxGrabar.setParameter("txtTelefonoMovil", "");
        g_ajaxGrabar.setParameter("cboDpto", "1347");
        g_ajaxGrabar.setParameter("cboProv", "1348");
        g_ajaxGrabar.setParameter("cboDist", "1349");
        g_ajaxGrabar.setParameter("txtImagen", "");
        g_ajaxGrabar.setParameter("chkCompartido", "N");
        g_ajaxGrabar.setParameter("cboIdRol", "5");
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
        $("#tdConcepto").css("display","");
        $("#tdConcepto2").css("display","");
    }else{
        $("#tdDias").css("display","");
        $("#tdDias2").css("display","");        
        $("#tdConcepto").css("display","none");
        $("#tdConcepto2").css("display","none");
    }
}

function calcularIgv(){
    if($("#cboIdTipoDocumento").val()!="17"){
        var igv=0;
        var subtotal=$("#txtTotal").val();
    }else{
        var subtotal=Math.round((parseFloat($("#txtTotal").val())/1.18)*100)/100;
        var igv=Math.round((parseFloat(($("#txtTotal").val()) - subtotal))*100)/100;
    }
    $("#txtIgv").val(igv);
    $("#txtSubtotal").val(subtotal);
}

function calcularTotal(){
    var p = parseFloat($("#txtPercepcion").val());
    var d = parseFloat($("#txtDetraccion").val());
    var r = parseFloat($("#txtRetencion").val());
    var total = parseFloat($("#txtTotal").val());
    var monto = total + p - d - r;
    $("#txtMonto").val(monto);    
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
	<td <?php echo $value["idcampo"]?>><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR")
$num=htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else $num=$objMantenimiento->generaNumero(8,3,substr($_SESSION["R_FechaProceso"],6,4));
	?><input type="hidden" id="txtNumeroTicket" value="<?php echo $num;?>"><input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php echo $num;?>" size="15" maxlength="15" title="Debe indicar el n&uacute;mero de Documento de Compra" <?php if($_GET["accion"]=="ACTUALIZAR")echo 'disabled';?> ></td>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=8",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,"calcularIgv()"); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=8",$value["descripcion"],3,'',$objMantenimiento,"calcularIgv()");?></td>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<td><?php echo $value["comentario"];?></td>
        <td><div class="input-field inline"><input type="date" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo date_format(date_create_from_format("d/m/Y",htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8")),"Y-m-d"); else echo date_format(date_create_from_format("d/m/Y",$_SESSION["R_FechaProceso"]),"Y-m-d");?>" title="Debe indicar la fecha" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>></div></td>
    	<!--td><input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo date_format(date_create_from_format("d/m/Y",$_SESSION["R_FechaProceso"]),"Y-m-d");?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar" type="button" class="boton" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>><img src="img/date.png" width="16" height="16"> </button></td-->
	<?php }?>
    <?php if($value["idcampo"]==20){?>
	<tr><td>Persona</td>
    	<td>
            <div class="col s12 valign-wrapper">
                <div class="input-field inline col s8 m8 l10">
            <input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato['idsucursalpersona']), ENT_QUOTES, "UTF-8"); else echo $_SESSION['R_IdSucursal'];
	?>" title="Debe indicar un cliente">
        <input type="hidden" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo "2";
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
<tr><td><div id="divImpuesto" style="display:none"><label><input name="chkIgv" type="checkbox" onClick="asignar()" value="S" checked id="chkIgv">Incluido IGV</label></div></td></tr>
</table>
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
            <td id="tdConcepto">Concepto</td>
            <td id="tdConcepto2"><?php echo genera_cboGeneralSQL("select * from conceptopago where tipo='E' and estado='N' and idconceptopago<>2",'ConceptoPago','','',$objMantenimiento,"");?></td>
            <td><?php echo $value["comentario"];?></td>
            <td colspan="2"><textarea class="materialize-textarea" name="txt<?php echo $value["descripcion"];?>" id="txt<?php echo $value["descripcion"];?>" cols="30" rows="3"><?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?></textarea></td>
        </tr>
        <tr>
            <td>SubTotal</td>
            <td><input type="text" id="txtSubtotal" name="txtSubtotal" readonly="" /></td>
            <td>Igv</td>
            <td><input type="text" id="txtIgv" name="txtIgv" readonly="" /></td>
            <td>Total</td>
            <td><input type="text" id="txtTotal" name="txtTotal" onKeyPress='return validarsolonumerosdecimales(event,this.value);' onblur="calcularIgv();calcularTotal();" /></td>
        </tr>
        <tr>
            <td>Percepcion</td>
            <td><input type="text" id="txtPercepcion" name="txtPercepcion" value="0" onKeyPress='return validarsolonumerosdecimales(event,this.value);' onblur="calcularIgv();calcularTotal();" /></td>
            <td>Detraccion</td>
            <td><input type="text" id="txtDetraccion" name="txtDetraccion" value="0" onKeyPress='return validarsolonumerosdecimales(event,this.value);' onblur="calcularIgv();calcularTotal();" /></td>
            <td style="display: none;">Retencion</td>
            <td style="display: none;"><input type="text" id="txtRetencion" name="txtRetencion" value="0" onKeyPress='return validarsolonumerosdecimales(event,this.value);' onblur="calcularIgv();calcularTotal();" /></td>
            <td>Monto a Pagar</td>
            <td><input type="text" id="txtMonto" name="txtMonto" value="0" /></td>

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