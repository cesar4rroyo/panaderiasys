<?php
require("../modelo/clsMovimiento.php");
require("../modelo/clsSalon.php");
require("fun.php");
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

if(isset($_SESSION['R_carroVenta']))
$_SESSION['R_carroVenta']="";

try{
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarMovimiento(1,1,'2',1,$_GET["Id"],"");
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetch();
	}
}else{
    $rst = $objMantenimiento->consultarMovimiento(1,1,'2',1,$_GET["Id"],"");
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetchObject();//print_r($dato);
	}
}

$tipoVenta = $objMantenimiento->obtenerDataSQL("SELECT T.* FROM (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) AS T WHERE T.idmovimiento = ".$_GET["Id"])->fetchObject();
if(!empty($tipoVenta)){
    $tipoVenta = $tipoVenta->tipoventa;
    $ventaCredito = $objMantenimiento->obtenerDataSQL("SELECT * FROM ventacredito WHERE idmovimiento = ".$_GET["Id"])->fetchObject()->idventacredito;
}else{
    $tipoVenta = "";
}
//print_r($_SESSION['R_carroPedidoMozo']);
?>
<html>
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
	getFormData("frmMantVenta");
	
}
function aceptar(){
    if($('#txtIdSucursalPersona').val()=='' || 
            $('#txtIdPersona').val()=='' ||
            ($("#cboIdTipoDocumento").val()=="5" && $("#txtDireccion2").val()=="") || 
            (($("#tipoVenta").val()!="C" && $("#tipoVenta").val()!="T") &&(($('#txtVuelto').val()<0 || $('#txtVuelto').val()=='') && ($('#chbxAMBOS').is(":checked") || $('#chbxEFECTIVO').is(":checked")))) ||
            ($("#tipoVenta").val()=="C" && ($("#modalidadCampo1").val().trim()=="" || isNaN($("#modalidadCampo1").val()))) ||
            ($("#chbxCHEQUE").is(":checked") && ($("#txtBancoCheque").val().trim()=="" || $("#txtNumeroCheque").val().trim()=="")) ||
            ($("#chbxAMBOS").is(":checked") && ($("#txtMontoVisa").val().trim()=="" ||  isNaN($("#txtMontoVisa").val()) ||  Number($("#txtMontoVisa").val())<0 || $("#txtMontoMastercard").val().trim()=="" ||  isNaN($("#txtMontoMastercard").val()) ||  Number($("#txtMontoMastercard").val())<0 || $("#txtPagoEfectivo").val().trim()=="" ||  isNaN($("#txtPagoEfectivo").val()) ||  Number($("#txtPagoEfectivo").val())<0 ||  Number($("#txtMontoVisa").val()) + Number($("#txtMontoMastercard").val()) + Number($("#txtPagoEfectivo").val())!=$("#txtTotal").val())) ||
            ($("#chbxDEPOSITO").is(":checked") && ($("#txtBancoDeposito").val().trim()=="" || $("#txtNumeroDeposito").val().trim()=="" || $("#txtImporteDeposito").val().trim()=="" || $("#txtFechaDeposito").val().trim()=="" || isNaN($("#txtImporteDeposito").val())))
            ){
        alert("FALTA UNO O MAS CAMPOS");
        console.log($('#txtIdSucursalPersona').val());
        console.log($('#txtIdPersona').val());
        console.log($('#txtVuelto').val());
        console.log($('#chbxEFECTIVO').is(":checked"));
        console.log($('#chbxAMBOS').is(":checked"));
        console.log($("#tipoVenta").val()!="C" &&(($('#txtVuelto').val()<0 || $('#txtVuelto').val()=='') && ($('#chbxAMBOS').is(":checked") || $('#chbxEFECTIVO').is(":checked"))));
        console.log($("#tipoVenta").val()=="C" && ($("#modalidadCampo1").val().trim()=="" || isNaN($("#modalidadCampo1").val())));
        console.log($("#chbxCHEQUE").is(":checked") && ($("#txtBancoCheque").val().trim()=="" || $("#txtNumeroCheque").val().trim()==""));
        console.log($("#chbxAMBOS").is(":checked") && ($("#txtMontoVisa").val().trim()=="" ||  isNaN($("#txtMontoVisa").val()) ||  Number($("#txtMontoVisa").val())<0 || $("#txtMontoMastercard").val().trim()=="" ||  isNaN($("#txtMontoMastercard").val()) ||  Number($("#txtMontoMastercard").val())<0 || $("#txtPagoEfectivo").val().trim()=="" ||  isNaN($("#txtPagoEfectivo").val()) ||  Number($("#txtPagoEfectivo").val())<0 ||  Number($("#txtMontoVisa").val()) + Number($("#txtMontoMastercard").val()) + Number($("#txtPagoEfectivo").val())!=$("#txtTotal").val()));
        console.log($("#chbxDEPOSITO").is(":checked") && ($("#txtBancoDeposito").val().trim()=="" || $("#txtNumeroDeposito").val().trim()=="" || $("#txtImporteDeposito").val().trim()=="" || $("#txtFechaDeposito").val().trim()=="" || isNaN($("#txtImporteDeposito").val())));
        console.log();
        return false;
    }else{
        if(setValidar("frmMantVenta")){
            if(document.getElementById('divDetalleVenta').innerHTML!='' && document.getElementById('divDetalleVenta').innerHTML!='Debe agregar un pedido!!!'){
                if(parseFloat(document.getElementById('txtTotal').value)>=0){
                    var vidmovimiento=document.getElementById("txtId").value;
                    document.getElementById("cmdGrabar").disabled=true;
                    var formData = $("#frmMantVenta").serialize();
                    var bolImprimir = $("#txtImprimir").val(); 
                    var bolConsumo = $("#txtConsumo").val();
                    var glosa = $("#txtGlosa").val();
                    var idtipodocumento = $("#cboIdTipoDocumento").val();
                    var vfe = $("#txtFE").val();
                    $.ajax({
                        type: "POST",
                        url: "controlador/contVenta.php?ajax=true",        
                        data:"accion=<?php echo $_GET['accion'];?>&clase=<?php echo $_GET['id_clase'];?>&"+formData,
                        success: function(a) {
                            eval(a);
                            //if(bolImprimir=="S"){
                                //imprimir(vidventa,bolConsumo,glosa);
                                if(vfe=="S" && idtipodocumento!="19"){
                                	declarar(vidventa,bolConsumo,glosa,idtipodocumento);
                                	imprimir2(vidventa,bolConsumo,glosa);
                                }
                                <?php if(trim($_GET["salon"])=="CLIENTE"){ ?>
                                imprimirTicket(<?=$_GET["Id"]?>);
                                <?php }else{ ?>
                                setRun("vista/frmCajero","&id_clase=46","frame","frame","imgloading");
                                <?php } ?>
                                loading(true, "loading", "frame", "line.gif",true);
                                document.getElementById("url").value="vista/frmCajero";
                                document.getElementById("par").value="&id_clase=0";
                                document.getElementById("div").value="frame";
                                document.getElementById("msj").value="frame";
                                document.getElementById("img").value="imgloading";
                                
                            //}
                            loading(false, "loading");
                        }
                    });
                }else{
                    alert("Debe indicar los productos");
                }	
            }else{
                alert("Debe indicar los productos");
            }
        }
    }
}

function imprimirPrecuenta(){
    var formData = $("#frmMantVenta").serialize();
    $.ajax({
        type: "POST",
        url: "vista/ajaxPedido.php?ajax=true",        
        data:"accion=imprimir_cuenta2&clase=<?php echo $_GET['id_clase'];?>&"+formData,
        success: function(a) {
            console.log(a);
        }
    });
} 

function consultaRUC(){
    var ruc = $("#txtNroDoc").val();
    $.ajax({
        type: 'GET',
        url: "https://comprobante-e.com/facturacion/buscaCliente/BuscaClienteRuc.php",
        data: "fe=N&token=qusEj_w7aHEpX&ruc="+ruc,
        beforeSend(){
        	alert("Consultando...");
        },
        success: function (data, textStatus, jqXHR) {
            data = JSON.parse(data);
        	alert("Datos Recibidos");
            $("#txtNombres").val(data.RazonSocial);
            $("#txtDireccion").val(data.Direccion);
            $("#txtNombres").focus();
            $("#txtDireccion").focus();
        }
    });
}

function cuentaDelivery(id){
		g_ajaxGrabar.setURL("vista/ajaxPedido.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "imprimir_cuenta_delivery");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			//alert("Imprimiendo");			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
}

function imprimir2(idventa,consumo,glosa){
        g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "imprimir_ventaelectronica");
		g_ajaxPagina.setParameter("idventa",idventa);
        g_ajaxPagina.setParameter("consumo",consumo);
        g_ajaxPagina.setParameter("glosa",glosa);
		g_ajaxPagina.response = function(text){
			//alert("imprimiendo");			
		};
		g_ajaxPagina.request();
}

function declarar(idventa,consumo,glosa,idtipodocumento){
    if(idtipodocumento==4){
        var vaccion='enviarBoleta';
    }else{
        var vaccion='enviarFactura';
    }
    g_ajaxPagina.setURL("controlador/contComprobante.php");
	g_ajaxPagina.setRequestMethod("GET");
	g_ajaxPagina.setParameter("funcion", vaccion);
	g_ajaxPagina.setParameter("idventa",idventa);
    g_ajaxPagina.setParameter("consumo",consumo);
    g_ajaxPagina.setParameter("glosa",glosa);
	g_ajaxPagina.response = function(text){
		//imprimir2(idventa,consumo,glosa);
		console.log(text);
	};
	g_ajaxPagina.request();
}

function ordenarPedido(id){
	document.getElementById("order").value = id;
	if(document.getElementById("by").value=="1"){
		document.getElementById("by").value = "0";	
	}else{
		document.getElementById("by").value = "1";
	}
	buscarPedido();
}
function ocultarResultadoListGrillaInterna(){
	document.getElementById('divBusquedaPedido').style.display='none';
}
function buscarPedido(e){
	if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
	//alert(keyc);
	//teclas izquierda, derescha, shift, control
	if(keyc == 37 || keyc == 39 || keyc == 16 || keyc == 17) { return false;}
	if(keyc == 38 || keyc == 40 || keyc == 13) {
		div="divBusquedaPedido";
		if(document.getElementById(div).innerHTML!=""){
        autocompletarPedido_teclado2(div, 'tablaPedido', keyc);
		}
    }else{
		
		vOrder = document.getElementById("order").value;
		vBy = document.getElementById("by").value;
		vCliente = encodeURI(document.getElementById("txtClienteBuscar").value.replace('\'',''));
		
		vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','A','"+vCliente+"'";
		setRun('vista/listGrillaInternaTeclado','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&nombre=Pedido&id_clase=47&filtro=' + vValor, 'divBusquedaPedido', 'divBusquedaPedido', 'img03');
		document.getElementById('divBusquedaPedido').style.display='';
	}
}
//buscarPedido();
/*
		vOrder = document.getElementById("order").value;
		vBy = document.getElementById("by").value;
		vCliente = encodeURI(document.getElementById("txtClienteBuscar").value.replace('\'',''));
		
		vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','A'";
		setRun('vista/listGrillaInternaTeclado','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&nombre=Pedido&id_clase=47&filtro=' + vValor, 'divBusquedaPedido', 'divBusquedaPedido', 'img03');
		document.getElementById('divBusquedaPedido').style.display='';
		*/
function seleccionarpedido(idpedido){
		seleccionarPedidoReserva(idpedido)
		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "listaDetallePedido");
		g_ajaxPagina.setParameter("IdPedido", idpedido);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			document.getElementById('agregar').focus();
		};
		g_ajaxPagina.request();
}
function seleccionarPedidoReserva(idpedido){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "obtenerDatosReserva");
		g_ajaxPagina.setParameter("IdPedido", idpedido);
		g_ajaxPagina.response = function(text){
			eval(text);
			if(vidsucursalpersona!=""){
				if(confirm('Est� pedido tiene datos de reserva. \nDesea asignar estos datos (Cliente) al documento?')){
					mostrarPersona(vidsucursalpersona,vidpersona,'divregistrosPersona');
				}
			}
		};
		g_ajaxPagina.request();
}
function agregar(iddetalle){
		var recipiente = document.getElementById('divDetalleVenta');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarDetalleVenta");
		g_ajaxPagina.setParameter("IdDetalle", iddetalle);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		if(document.getElementById('chkIgv').checked) g_ajaxPagina.setParameter("IncluyeIgv", 'S'); else g_ajaxPagina.setParameter("IncluyeIgv", 'N');
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
            document.getElementById("txtPagoCredito").value=document.getElementById("txtTotal").value;
		};
		g_ajaxPagina.request();
}
var primeravez = true;
function agregartodo(idpedido){
    var recipiente = document.getElementById('divDetalleVenta');
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxVenta.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "agregarTodoDetalleVenta");
    g_ajaxPagina.setParameter("IdPedido", idpedido);
    g_ajaxPagina.setParameter("tipoVenta", $("#tipoVenta").val());
    g_ajaxPagina.setParameter("modalidadCampo1", $("#modalidadCampo1").val());
    g_ajaxPagina.setParameter("modalidadCampo2", $("#modalidadCampo2").val());
    g_ajaxPagina.setParameter("modalidadCampo3", $("#modalidadCampo3").val());
    g_ajaxPagina.setParameter("modalidadCampo4", $("#HdnIdModalidad").val());
    g_ajaxPagina.setParameter("idventacredito", $("#idventacredito").val());
    /*if(document.getElementById("optS").checked){
            g_ajaxPagina.setParameter("Moneda", "S");
    }
    if(document.getElementById("optD").checked){
            g_ajaxPagina.setParameter("Moneda", "D");
    }*/
    g_ajaxPagina.setParameter("Moneda", $("#inptMoneda").val());
    if($("#chkIgv").length > 0 && $("#cboIdTipoDocumento").val()==5) if(document.getElementById('chkIgv').checked) g_ajaxPagina.setParameter("IncluyeIgv", 'S');;
    //g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
    g_ajaxPagina.setParameter("IdTipoDocumento", $("#rbtnModoPago input[type='radio']:checked").val());
    g_ajaxPagina.response = function(text){
        text = JSON.parse(text);
        recipiente.innerHTML = text.registros;
        $("#divModalDividir").empty().html(text.registros2);
        document.getElementById("txtPagoCredito").value=document.getElementById("txtTotal").value;
        <?php if($tipoVenta=="C"){?>
        $('#h4DetalleDocumuento').html('<h4 class="center blue lighten-4 blue-text text-darken-4">DETALLES DEL DOCUMENTO</h4>');
        <?php }else{?>
        $('#h4DetalleDocumuento').html('<h4 class="center blue lighten-4 blue-text text-darken-4"><button type="button" onclick="imprimirPrecuenta();" class="tooltipped btn-floating right blue accent-1" data-position="bottom" data-delay="50" data-tooltip="IMPRIMIR"><i class="material-icons orange-text text-darken-4">print</i></button>DETALLES DEL DOCUMENTO<button type="button" onclick="modalDividirCuenta();" class="tooltipped btn-floating right orange accent-1" data-position="bottom" data-delay="50" data-tooltip="DIVIDIR LA CUENTA"><i class="material-icons orange-text text-darken-4">view_week</i></button></h4>');
        <?php }?>
        $("#chkIgv").removeAttr("disabled");
        $("#pTotal").html("TOTAL: "+$("#thTotalGeneral").html());
        $("#inptTotalVenta").val($("#thTotalGeneral").html());
        if(primeravez){
            $("#inptTotalVenta2").val($("#thTotalGeneral").html());
            primeravez = false;
        }
        $("#txtMontoDivision").attr("max",$("#thTotalGeneral").html());
        calcularVuelto();
    };
    g_ajaxPagina.request();
        
}
function quitar(iddetalle){
		var recipiente = document.getElementById('divDetalleVenta');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "quitarDetalleVenta");
		g_ajaxPagina.setParameter("IdDetalle", iddetalle);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		if(document.getElementById('chkIgv').checked) g_ajaxPagina.setParameter("IncluyeIgv", 'S'); else g_ajaxPagina.setParameter("IncluyeIgv", 'N');
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);

		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
            document.getElementById("txtPagoCredito").value=document.getElementById("txtTotal").value;
		};
		g_ajaxPagina.request();
}
function actualizarDetalleVenta(){
		var recipiente = document.getElementById('divDetalleVenta');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "actualizarDetalleVenta");
                g_ajaxPagina.setParameter("tipoVenta", $("#tipoVenta").val());
                g_ajaxPagina.setParameter("modalidadCampo1", $("#modalidadCampo1").val());
                g_ajaxPagina.setParameter("modalidadCampo2", $("#modalidadCampo2").val());
                g_ajaxPagina.setParameter("modalidadCampo3", $("#modalidadCampo3").val());
                g_ajaxPagina.setParameter("modalidadCampo4", $("#HdnIdModalidad").val());
		/*if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}*/
                g_ajaxPagina.setParameter("Moneda", $("#inptMoneda").val());
                if($("#chkIgv").length > 0 && $("#cboIdTipoDocumento").val()==5){
                    if(document.getElementById('chkIgv').checked) {
                        g_ajaxPagina.setParameter("IncluyeIgv", 'S');
                    }else{
                        g_ajaxPagina.setParameter("IncluyeIgv", 'N');
                    }
                }else{
                    g_ajaxPagina.setParameter("IncluyeIgv", 'S');
                }
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
		g_ajaxPagina.response = function(text){
                    recipiente.innerHTML = text;
                    <?php if($tipoVenta=="C"){?>
        $('#h4DetalleDocumuento').html('<h4 class="center blue lighten-4 blue-text text-darken-4">DETALLES DEL DOCUMENTO</h4>');                    
                    <?php }else{?>
        $('#h4DetalleDocumuento').html('<h4 class="center blue lighten-4 blue-text text-darken-4"><button type="button" onclick="imprimirPrecuenta();" class="tooltipped btn-floating right blue accent-1" data-position="bottom" data-delay="50" data-tooltip="IMPRIMIR"><i class="material-icons orange-text text-darken-4">print</i></button>DETALLES DEL DOCUMENTO<button id="btnDividirCuenta" type="button" onclick="modalDividirCuenta();" class="tooltipped btn-floating right orange accent-1" data-position="bottom" data-delay="50" data-tooltip="DIVIDIR LA CUENTA"><i class="material-icons orange-text text-darken-4">view_week</i></button></h4>');
                    <?php }?>
                    
                    calcularVuelto();
		};
		g_ajaxPagina.request();
}
function imprimirTicket(idmov){
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "imprimir_ticket");
    g_ajaxPagina.setParameter("mesa","<? if(trim($_GET["salon"])=="CLIENTE") echo $dato->nombrespersona;else echo $_GET["mesa"];?>");
    g_ajaxPagina.setParameter("numerocomanda","01");   
    g_ajaxPagina.setParameter("salon","<?=$_GET["salon"]?>");
    <?php if($_GET["accion"]=="ACTUALIZAR"){ ?>
        g_ajaxGrabar.setParameter("txtId",idmov);     
    <?php }?>
        g_ajaxPagina.response = function(text){
            setRun("vista/frmCajero","&id_clase=46","frame","frame","imgloading");
            console.log(text);
    };
    g_ajaxPagina.request();
}
/*
//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFecha", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>
*/

function restablecer(){
    agregartodo(<?php echo $_GET['Id'];?>);
    $('#h4DetalleDocumuento').html('<h4 class="center blue lighten-4 blue-text text-darken-4">DETALLES DEL DOCUMENTO<button type="button" onclick="modalDividirCuenta();" class="tooltipped btn-floating right orange accent-1" data-position="bottom" data-delay="50" data-tooltip="DIVIDIR LA CUENTA"><i class="material-icons orange-text text-darken-4">view_week</i></button></h4>');
    $("#chkIgv").removeAttr("disabled");
    if($("#cboIdTipoDocumento").val()==5){
        $("#chkIgv").prop( "checked", true );
    }
    calcularVuelto();
}
function generaNumeroVenta(idtipodocumento){
	g_ajaxPagina = new AW.HTTP.Request;
	g_ajaxPagina.setURL("vista/ajaxVenta.php");
	g_ajaxPagina.setRequestMethod("POST");
	if(document.getElementById("txtFE").value=="S")
		g_ajaxPagina.setParameter("accion", "generaNumeroElectronico");
	else
		g_ajaxPagina.setParameter("accion", "generaNumero");
	g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
    g_ajaxPagina.setParameter("serie",document.getElementById("txtImprimir").value);
	g_ajaxPagina.response = function(text){
		eval(text);
		document.getElementById('txtNumero').value=vnumero;
		asignar();
	};
	g_ajaxPagina.request();
}
function asignar(){
	if(document.getElementById('cboIdTipoDocumento').value=="5"){
		//document.getElementById('').style.display="";
                $("#divChkImpuesto").hide();
    //    document.getElementById('divTipoPago').style.display="";
		document.getElementById('txtIdSucursalPersona').value="";
		document.getElementById('txtIdPersona').value="";
		document.getElementById('txtPersona').value="";
                $('#txtIdSucursalPersona').val("");
                $('#txtIdPersona').val("");
                $('#txtPersona').val("");
                $('#txtPersona').attr("readonly",false);
	}else{
		//document.getElementById('divChkImpuesto').style.display="none";
                $("#divChkImpuesto").hide();
      //  document.getElementById('divTipoPago').style.display="none";
      //  document.getElementById('divBanco').style.display="none";
                $('#txtIdSucursalPersona').val("<?php echo $_SESSION['R_IdSucursal']?>");
                $('#txtIdPersona').val("3");
                $('#txtPersona').val("VARIOS");
                $('#txtPersona').attr("readonly",true);
                $('#lblPersona').addClass("active");
	}
        
	if(document.getElementById('divDetalleVenta').innerHTML!='' && document.getElementById('divDetalleVenta').innerHTML!='Debe agregar un pedido!!!'){
		actualizarDetalleVenta();
	}
        calcularVuelto();
}
function listadoPersona2(){
    $.ajax({
        url: "vista/ajaxPersonaMaestro.php",
        type: 'POST',
        data: "accion=BuscaPersonaJSON&idrol=1,3,4,5&nombres=&tipopersona=DNI&modo="+$("#txtModoPersona").val(),
        success: function(a) {
            a = JSON.parse(a);
            var datos = a.datos;
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
    $('#txtRUC2').val(ids[2]);
    $('#txtDireccion2').val(ids[3]);
    $('#txtPersona').attr("readonly",true);
}
function limpiarCamposPersona(){
    $('#txtIdSucursalPersona').val("");
    $('#txtIdPersona').val("");
    $('#txtPersona').attr("readonly",false);
    $('#txtPersona').val("");
    $('#txtPersona').focus();
}
function genera_cboCaja(idsalon,seleccionado,disabled){
		var recipiente = document.getElementById('divcboCaja');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboCaja");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("disabled", disabled);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
		};
		g_ajaxPagina.request();
}

function subcuenta(){
    var datos = document.getElementById('txtDatosProductos').value;
    var array = datos.split('/');
    var total = parseFloat(0.00);
    for(var i=0; i< array.length; i++) {
            total = total + parseFloat($('#txtSubtotal'+array[i]).html());
    }
    //document.getElementById("txtTotalSubcuenta").value=total;
    $("#txtTotalSubcuenta").empty().html(total);
}

function calcularVuelto(){
    if(document.getElementById("txtDinero").value!=""){
        if($('#chbxAMBOS').is(':checked')){
            var vuelto = parseFloat(document.getElementById("txtDinero").value) - parseFloat(document.getElementById("txtPagoEfectivo").value);
            vuelto=Math.round(vuelto*100)/100;
            document.getElementById("txtVuelto").value=vuelto;
        }else{
            if($('#cboIdTipoDocumento').val()==5){
                if($('#chkIgv').is(':checked')){
                    var vuelto = parseFloat(document.getElementById("txtDinero").value) - parseFloat(document.getElementById("txtTotal").value);
                    vuelto=Math.round(vuelto*100)/100;
                    document.getElementById("txtVuelto").value=vuelto;
                }else{
                    var vuelto = parseFloat(document.getElementById("txtDinero").value) - parseFloat(document.getElementById("txtTotal").value);
                    vuelto=Math.round(vuelto*100)/100;
                    document.getElementById("txtVuelto").value=vuelto;
                }
            }else{
                var vuelto = parseFloat(document.getElementById("txtDinero").value) - parseFloat(document.getElementById("txtTotal").value);
                vuelto=Math.round(vuelto*100)/100;
                document.getElementById("txtVuelto").value=vuelto;
            }
        }
    }
}

function validarUsuario(){
	g_ajaxGrabar.setURL("controlador/contPedido.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "VALIDAR");
	g_ajaxGrabar.setParameter("password", document.getElementById("txtPassword").value);
	g_ajaxGrabar.response = function(text){
		eval(text);
        if(vmsg=="S"){
            document.getElementById("tdDescuento").style.display="";  
            document.getElementById("DivAutorizar").style.display='none';
            document.getElementById("blokeador").style.display='none';          
        }else{
            alert("Password incorrecto");
        }
	};
	g_ajaxGrabar.request();
	
	loading(true, "loading", "grilla", "linea.gif",true);    
}

function mostrarUsuario(){
    document.getElementById("DivAutorizar").style.display='';
    document.getElementById("blokeador").style.display='';
    document.getElementById("txtPassword").value='';
}

function centraDivSucursal(){ 
        var top=(document.body.clientHeight/4)+"px"; 
        var left1=(document.body.clientWidth/2);
        var left=(left1-parseInt(document.getElementById("DivAutorizar").style.width)/2)+"px"; 
        document.getElementById("DivAutorizar").style.top=top; 
        document.getElementById("DivAutorizar").style.left=left; 
} 

function cerrar(){
    document.getElementById("DivAutorizar").style.display='none';
    document.getElementById("DivAutorizar").style.height=document.body.clientHeight+'px';
    document.getElementById("DivAutorizar").style.width=document.body.clientWidth+'px';
}

function calcularDescuento(){
    if(document.getElementById("txtDescuento").value!=""){
        var descuento = parseFloat(document.getElementById("txtDescuento").value);
        var total = parseFloat(document.getElementById("rTotal2").value);
        total = Math.round((total*(1-descuento/100))*100)/100;
        document.getElementById("txtTotal").value=total;
    }else{
        document.getElementById("txtTotal").value=document.getElementById("rTotal2").value;        
    }    
}

function validarMontoEfectivo(){
    if(parseFloat($('#txtTotal').val()) < parseFloat($('#txtPagoEfectivo').val()) || $('#txtPagoEfectivo').val()==''){ 
        alert('MONTO INCORRECTO');
        $('#txtPagoEfectivo').val('0');
    }
}

function aceptarDividirCuenta(){
    var modalidad = $("#chkModalidadDividir").is(":checked");
    $('#txtSubcuenta').val('SI');
    if(modalidad){
        //alert(Number($("#txtMontoDivision").val()));
        var monto_restante = Number($("#inptTotalVenta2").val());
        /*if($("#tipoVenta").val()=="V"){
            monto_restante = monto_restante + Number($("#modalidadCampo2").val());
        }else if($("#tipoVenta").val()=="V"){
            monto_restante = monto_restante + Number($("#modalidadCampo2").val());
        }*/
        if($("#txtMontoDivision").val().length>0 && Number($("#txtMontoDivision").val())>0 && Number($("#txtMontoDivision").val())<Number($("#inptTotalVenta").val())){
            $('#h4DetalleDocumuento').html('<h4 class="center blue lighten-4 blue-text text-darken-4">DETALLES DEL DOCUMENTO<button type="button" onclick="restablecer();" class="tooltipped btn-floating right indigo accent-1" data-position="bottom" data-delay="50" data-tooltip="REESTABLECER CUENTA"><i class="material-icons indigo-text text-darken-4">undo</i></button></h4>');
            $("#inptModalidadDivision").val("M");
            var monto = Number($("#txtMontoDivision").val());
            monto_restante = monto_restante - monto;
            var TR = $("#tBodyDetalleVenta tr:first-child");
            $(TR).children().each(function(key,val){
                if(key==1){
                    $(val).html("-");
                }
                if(key==2){
                    $(val).html("PRODUCTO DIVISION DE CUENTA POR MONTOS");
                }
                if(key==4){
                    $(val).html("1");
                }
                if(key==6){
                    $(val).html("0");
                }
                if(key==5 || key==7){
                    $(val).html(monto);
                }
            });
            var html = '<tr class="hoverable">'+$(TR).html()+'</tr';
            $("#tBodyDetalleVenta").html(html);
            $("#tBodyDetalleVenta").before('<input type="hidden" name="inptHidenMontoDivision" value="'+monto+'">');
            $("#tBodyDetalleVenta").before('<input type="hidden" name="inptHidenMontoRestante" value="'+monto_restante+'">');
            actualizarDetalleTipoVenta();
            actualizarTotalVenta();
        }
    }else{
        subcuenta();
        if($('#txtTotalSubcuenta').html()>0 && parseFloat($("#txtTotalSubcuenta").html())<parseFloat($("#thTotalGeneral").html())){
            $('#h4DetalleDocumuento').html('<h4 class="center blue lighten-4 blue-text text-darken-4">DETALLES DEL DOCUMENTO<button type="button" onclick="restablecer();" class="tooltipped btn-floating right indigo accent-1" data-position="bottom" data-delay="50" data-tooltip="REESTABLECER CUENTA"><i class="material-icons indigo-text text-darken-4">undo</i></button></h4>');
            $("#inptModalidadDivision").val("P");
            $(".inptCantDetalleDividir").each(function(key,val){
                var cantidad = $(val).val();
                var iddetalle = $(val).attr('iddetalle');
                var idInput = $(val).attr('id');
                var id = idInput.substring(3, idInput.length);
                //console.log($("#trTblDetalle_"+id));
                if(cantidad>0){
                    $("#tdCant_"+id).html((cantidad));
                    $("#tdCant_"+id).before('<input type="hidden" name="inptHidenId[]" value="'+iddetalle+'">');
                    $("#tdSubtotal_"+id).html(($("#tdPrecio_"+id).html()*cantidad));
                    $("#tdSubtotal_"+id).before('<input type="hidden" name="inptHidenCant[]" value="'+cantidad+'">');
                }else{
                    $("#trTblDetalle_"+id).remove();
                }
            });
            actualizarTotalVenta();
        }
    }
}
function verificaNroDoc(nro,tipo){
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
            $("#txtPersona").val("");
            $("#txtPersona").removeAttr("readonly");
            $("#txtPersona").focus();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "contenido", "line.gif",true);
}
function fnCambiarModalidad(){
    var modalidad = $("#tipoVenta").val();
    $("#inptTotalVenta2").val($("#inptTotalVenta").val());
    $("#tipoVentaSeleccionado").val("N");
    $("#divModalidadCampo1").hide();
    $("#divModalidadCampo2").hide();
    $("#divModalidadCampo3").hide();
    if($("#tipoVenta").val()=="N"){
        $('#tipoVenta').prop("disabled",false);
    }else{
        $('#tipoVenta').prop("disabled",true);
    }
    $('#tipoVenta').material_select();
    if(modalidad=="N"){
    }else if(modalidad=="C"){
        $("#divModalidadCampo1").show();
        $("#modalidadCampo1").val("");
        $("#modalidadCampo1").attr("name","plazo_credito");
        $("#modalidadCampo1").prop("readonly",false);
        $("#lblModalidadCampo1").html("PLAZO DEL CREDITO");
        limpiarCamposPersona();
        $("#tipoVentaSeleccionado").val("C");
        $("#divModoPago").hide();
        $("#divDatosPago").hide();
        $("#divDatosDocumento2").hide();
        $("#divModalidadCampo2").html($("#divCliente").html());
        $("#divCliente").empty();
        $("#divModalidadCampo2").show();
        $("#divModalidadCampo3").html($("#divGuardar").html());
        $("#divGuardar").empty();
        $("#cmdGrabar").removeClass("right");
        $("#cmdGrabar").addClass("center");
        $("#divModalidadCampo3").show();
        $("#h4DatosDocumento").hide();
        $("#divDatosDocumento1").hide();
        $("#divDatosDocumento2").hide();
        $("#divDatosDocumento3").hide();
        $("#h4DetalleDocumuento").hide();
        $("#divDetalleDocumento").hide();
        $("#ulAutocomplete_txtPersona").remove();
        listadoPersona2();
    }else{
        $.ajax({
            url: "controlador/contVenta.php",
            type: 'POST',
            data: "accion=MODALIDADVENTA&IdPedido=<?=$_GET["Id"]?>&modalidad="+modalidad,
            success: function(a) {
                a = JSON.parse(a);
                $('#modalModalidadVenta').openModal({
                    dismissible: true, // Modal can be dismissed by clicking outside of the modal
                    opacity: .5, // Opacity of modal background
                    in_duration: 300, // Transition in duration
                    out_duration: 200, // Transition out duration
                    starting_top: '4%', // Starting top style attribute
                    ending_top: '10%', // Ending top style attribute
                    ready: function(modal, trigger) {
                        if(modalidad=="V"){
                            $("#h4ModalidadVenta").html("SELECCIONA EL VALE DE CONSUMO");
                            var html = '<table class="centered striped bordered highlight"><thead><tr><td class="center">CORRELATIVO</td><td class="center">PROPIETARIO</td><td class="center">VALOR</td><td class="center">FECHA EMISION</td><td class="center">USUARIO</td><td></td></tr></thead>';
                            var datos = a.datos;
                            $.each(datos,function (key,val){
                                var id = val[0];
                                html = html + '<tr>';
                                html = html + '<td class="center">' + val[1] + '</td>';
                                html = html + '<td class="center">' + val[2] + '</td>';
                                html = html + '<td class="center">' + val[3] + '</td>';
                                html = html + '<td class="center">' + val[4] + '</td>';
                                html = html + '<td class="center">' + val[5] + '</td>';
                                html = html + '<td class="center"><button type="button" class="modal-action modal-close btn light-green accent-1 black-text" onclick="SeleccionarVale(' + val[0] + ',\''+val[2]+'\','+val[3]+',\''+val[4]+'\');"><i class="material-icons">check</i></button></td>';
                                html = html + '</tr>';
                            });
                            html = html + '</table>';
                            $("#divModalidadVenta").html(html);
                            $("#btnAceptarModalidadVenta").hide();
                        }else if(modalidad=="D"){
                            $("#h4ModalidadVenta").html("SELECCIONA EL USUARIO DEL DESCUENTO");
                            var html = '<table class="centered striped bordered highlight"><thead><tr><td class="center">APELLIDOS</td><td class="center">NOMBRES</td><td class="center">NUMERO DOCUMENTO</td><td class="center">USUARIO</td><td></td></tr></thead>';
                            var datos = a.datos;
                            $.each(datos,function (key,val){
                                var id = val[0];
                                html = html + '<tr>';
                                html = html + '<td class="center">' + val[1] + '</td>';
                                html = html + '<td class="center">' + val[2] + '</td>';
                                html = html + '<td class="center">' + val[3] + '</td>';
                                html = html + '<td class="center">' + val[4] + '</td>';
                                html = html + '<td class="center"><button type="button" class="modal-action modal-close btn light-green accent-1 black-text" onclick="SeleccionarTrabajador(' + val[0] + ',\''+val[1]+'\',\''+val[2]+'\',\''+val[3]+'\');"><i class="material-icons">check</i></button></td>';
                                html = html + '</tr>';
                            });
                            html = html + '</table>';
                            $("#divModalidadVenta").html(html);
                            $("#btnAceptarModalidadVenta").hide();
                        }else if(modalidad=="A"){
                            $("#h4ModalidadVenta").html("SELECCIONA EL PAGO ANTICIPADO");
                            var html = '<table class="centered striped bordered highlight"><thead><tr><td class="center">CORRELATIVO</td><td class="center">TIPO PAGO</td><td class="center">PROPIETARIO</td><td class="center">VALOR</td><td class="center">FECHA PAGO</td><td class="center">USUARIO</td><td></td></tr></thead>';
                            var datos = a.datos;
                            $.each(datos,function (key,val){
                                var id = val[0];
                                html = html + '<tr>';
                                html = html + '<td class="center">' + val[1] + '</td>';
                                html = html + '<td class="center">' + val[2] + '</td>';
                                html = html + '<td class="center">' + val[3] + '</td>';
                                html = html + '<td class="center">' + val[4] + '</td>';
                                html = html + '<td class="center">' + val[5] + '</td>';
                                html = html + '<td class="center">' + val[6] + '</td>';
                                html = html + '<td class="center"><button type="button" class="modal-action modal-close btn light-green accent-1 black-text" onclick="SeleccionarPago(' + val[0] + ',\''+val[3]+'\','+val[4]+',\''+val[5]+'\');"><i class="material-icons">check</i></button></td>';
                                html = html + '</tr>';
                            });
                            html = html + '</table>';
                            $("#divModalidadVenta").html(html);
                            $("#btnAceptarModalidadVenta").hide();
                        }else if(modalidad=="T"){
                            $("#h4ModalidadVenta").html("SELECCIONA LOS PRODUCTOS DE CORTESIA");
                            var html = '<table class="centered striped bordered highlight"><thead><tr><th class="center">NUMERO</th><th class="center">CODIGO</th><th class="center">PRODUCTO</th><th class="center">UNIDAD</th><th class="center">CANTIDAD</th><th class="center">PRECIO VENTA</th><th class="center">SUBTOTAL</th><th class="center white"><input type="checkbox" id="chckCortesiaTodos" onchange="SeleccionarTodosCortesiaDetalles();" /><label for="chckCortesiaTodos">&nbsp;</label></th></tr></thead>';
                            var datos = a.datos;
                            $.each(datos,function (key,val){
                                var id = val[0];
                                html = html + '<tr>';
                                html = html + '<td class="center">' + val[1] + '</td>';
                                html = html + '<td class="center">' + val[2] + '</td>';
                                html = html + '<td class="center">' + val[3] + '</td>';
                                html = html + '<td class="center">' + val[4] + '</td>';
                                html = html + '<td class="center">' + val[5] + '</td>';
                                html = html + '<td class="center">' + val[6] + '</td>';
                                html = html + '<td class="center">' + val[7] + '</td>';
                                html = html + '<td class="center"><input type="checkbox" class="chckCortesiaDetalle" onclick="$(\'#chckCortesiaTodos\').prop(\'checked\',false);" id="chckCortesiaDetalle_' + val[0] + '" iddetalle="' + val[0] + '" /><label for="chckCortesiaDetalle_' + val[0] + '">&nbsp;</label></td>';
                                html = html + '</tr>';
                            });
                            html = html + '</table>';
                            $("#divModalidadVenta").html(html);
                            $("#btnAceptarModalidadVenta").attr("onclick","SeleccionarCortesia();");
                            $("#btnAceptarModalidadVenta").show();
                        }
                    },
                    complete: function() {
                        var modalidad = $("#tipoVenta").val();
                        if($("#tipoVenta").val()!=$("#tipoVentaSeleccionado").val()){
                            $('#tipoVenta').val('N');
                            fnCambiarModalidad();
                            alerta("NO HA SELECCIONADO LOS DATOS CORRECTAMENTE");
                        }
                    } // Callback for Modal close
                });
            }
        });
    }
}
function SeleccionarTodosCortesiaDetalles(){
    var valor = $("#chckCortesiaTodos").is(":checked");
    $('.chckCortesiaDetalle').each(function(key,val){
        $(val).prop("checked",valor);
    });
}
function SeleccionarVale(id,propietario,valor,fechaemision){
    $('#modalModalidadVenta').closeModal();
    $("#HdnIdModalidad").val(id);
    $("#HdnIdModalidad").attr("name","idvale");
    $("#divModalidadCampo1").show();
    $("#modalidadCampo1").val(propietario);
    $("#modalidadCampo1").attr("name","propietario");
    $("#modalidadCampo1").prop("readonly",true);
    $("#lblModalidadCampo1").html("PROPIETARIO DEL VALE");
    $("#lblModalidadCampo1").addClass("active");
    $("#divModalidadCampo2").show();
    $("#modalidadCampo2").val(valor);
    $("#modalidadCampo2").attr("name","valorvale");
    $("#modalidadCampo2").prop("readonly",true);
    $("#lblModalidadCampo2").html("VALOR DEL VALE");
    $("#lblModalidadCampo2").addClass("active");
    $("#divModalidadCampo3").show();
    $("#modalidadCampo3").val(fechaemision);
    $("#modalidadCampo3").attr("name","fechaemisionvale");
    $("#modalidadCampo3").prop("readonly",true);
    $("#lblModalidadCampo3").html("FECHA DE EMISION DEL VALE");
    $("#lblModalidadCampo3").addClass("active");
    $("#tipoVentaSeleccionado").val("V");
    restablecer();
}
function SeleccionarTrabajador(id,apellidos,nombres,nrodoc){
    $('#modalModalidadVenta').closeModal();
    $("#HdnIdModalidad").val(id);
    $("#HdnIdModalidad").attr("name","idtrabajador");
    $("#divModalidadCampo1").show();
    $("#modalidadCampo1").val(apellidos);
    $("#modalidadCampo1").attr("name","apellidostrabajdor");
    $("#modalidadCampo1").prop("readonly",true);
    $("#lblModalidadCampo1").html("APELLIDOS DE TRABAJADOR");
    $("#lblModalidadCampo1").addClass("active");
    $("#divModalidadCampo2").show();
    $("#modalidadCampo2").val(nombres);
    $("#modalidadCampo2").attr("name","nombrestrabajador");
    $("#modalidadCampo2").prop("readonly",true);
    $("#lblModalidadCampo2").html("NOMBRES DE TRABAJADOR");
    $("#lblModalidadCampo2").addClass("active");
    $("#divModalidadCampo3").show();
    $("#modalidadCampo3").val(nrodoc);
    $("#modalidadCampo3").attr("name","nrodoctrabajador");
    $("#modalidadCampo3").prop("readonly",true);
    $("#lblModalidadCampo3").html("NRO DOCUMENTO DE TRABAJADOR");
    $("#lblModalidadCampo3").addClass("active");
    $("#tipoVentaSeleccionado").val("D");
    $("#divCliente").hide();
    restablecer();
}
function SeleccionarPago(id,propietario,valor,fechapago){
    $('#modalModalidadVenta').closeModal();
    $("#HdnIdModalidad").val(id);
    $("#HdnIdModalidad").attr("name","idpagoanticipado");
    $("#divModalidadCampo1").show();
    $("#modalidadCampo1").val(propietario);
    $("#modalidadCampo1").attr("name","propietariopago");
    $("#modalidadCampo1").prop("readonly",true);
    $("#lblModalidadCampo1").html("PROPIETARIO DEL PAGO ANTICIPADO");
    $("#lblModalidadCampo1").addClass("active");
    $("#divModalidadCampo2").show();
    $("#modalidadCampo2").val(valor);
    $("#modalidadCampo2").attr("name","valorpago");
    $("#modalidadCampo2").prop("readonly",true);
    $("#lblModalidadCampo2").html("VALOR DEL PAGO ANTICIPADO");
    $("#lblModalidadCampo2").addClass("active");
    $("#divModalidadCampo3").show();
    $("#modalidadCampo3").val(fechapago);
    $("#modalidadCampo3").attr("name","fechapago");
    $("#modalidadCampo3").prop("readonly",true);
    $("#lblModalidadCampo3").html("FECHA DEL PAGO ANTICIPADO");
    $("#lblModalidadCampo3").addClass("active");
    $("#tipoVentaSeleccionado").val("A");
    restablecer();
}
function SeleccionarCortesia(){
    var todos = $("#chckCortesiaTodos").is(":checked");
    var numeroproductos = $('.chckCortesiaDetalle').length;
    var ids = [];
    var ids2 = [];
    $('.chckCortesiaDetalle').each(function(key,val){
        var data = $(val).attr("iddetalle");
        data = data.split(",");
        //var cantidad_ori = $("#tdCant_"+data[1]).html();
        //var cantidad = 0;
        if($(val).is(":checked")){
            ids.push(data[0]);
            ids2.push(data[1]);
            //$("#tdCant_"+data[1]).html((cantidad)+'<input type="hidden" name="inptHidenId[]" value="'+data[0]+'">');
            //$("#tdSubtotal_"+data[1]).html(($("#tdPrecio_"+data[1]).html()*cantidad)+'<input type="hidden" name="inptHidenCant[]" value="'+cantidad+'">');
            //cantidad_ori = 0;
        }
        //total = total + Number($("#tdPrecio_"+data[1]).html()*cantidad_ori);
        //console.log(Number($("#tdPrecio_"+data[1]).html()*cantidad_ori));
    });
    if(ids.length>0 && numeroproductos>ids.length){
        $("#HdnIdModalidad").val(ids2.join(","));
        $("#HdnIdModalidad").attr("name","idcortesia");
        $("#divModalidadCampo1").show();
        $("#modalidadCampo1").val(numeroproductos);
        $("#modalidadCampo1").attr("name","numeroproductos");
        $("#modalidadCampo1").prop("readonly",true);
        $("#lblModalidadCampo1").html("TOTAL DE PRODUCTOS");
        $("#lblModalidadCampo1").addClass("active");
        $("#divModalidadCampo2").show();
        $("#modalidadCampo2").val(ids.length);
        $("#modalidadCampo2").attr("name","productosmarcados");
        $("#modalidadCampo2").prop("readonly",true);
        $("#lblModalidadCampo2").html("PRODUCTOS DE CORTESIA");
        $("#lblModalidadCampo2").addClass("active");
        $("#divModalidadCampo3").hide();
        $("#tipoVentaSeleccionado").val("T");
        $("#inptModalidadCortesia").val("P");
        restablecer();
    }else if(numeroproductos==ids.length){
        $("#HdnIdModalidad").val(ids2.join(","));
        $("#HdnIdModalidad").attr("name","idcortesia");
        $("#divModalidadCampo1").show();
        $("#modalidadCampo1").val("");
        $("#modalidadCampo1").attr("name","comentario");
        $("#modalidadCampo1").prop("readonly",false);
        $("#lblModalidadCampo1").html("Comentario");
        limpiarCamposPersona();
        $("#tipoVentaSeleccionado").val("T");
        $("#divModoPago").hide();
        $("#divDatosPago").hide();
        $("#divDatosDocumento2").hide();
        $("#divModalidadCampo2").html($("#divCliente").html());
        $("#divCliente").empty();
        $("#divModalidadCampo2").show();
        $("#divModalidadCampo3").html($("#divGuardar").html());
        $("#divGuardar").empty();
        $("#cmdGrabar").removeClass("right");
        $("#cmdGrabar").addClass("center");
        $("#divModalidadCampo3").show();
        $("#h4DatosDocumento").hide();
        $("#divDatosDocumento1").hide();
        $("#divDatosDocumento2").hide();
        $("#divDatosDocumento3").hide();
        $("#h4DetalleDocumuento").hide();
        $("#divDetalleDocumento").hide();
        $("#ulAutocomplete_txtPersona").remove();
        $("#inptModalidadCortesia").val("T");
        listadoPersona2();
    }
}
function actualizarDetalleTipoVenta(){
    var tipoVenta = $("#tipoVenta").val();
    var html = "";
    if(tipoVenta == "V"){
        html = '<tr class="hoverable"><td class="center">-</td><td class="center">-</td><td class="center">DESCUENTO POR VALE</td><td class="center">UNIDAD</td><td class="center">1</td><td class="center">-'+Number($("#modalidadCampo2").val())+'</td><td class="center">-'+Number($("#modalidadCampo2").val())+'</td></tr>';
    }
    if(tipoVenta == "A"){
        html = '<tr class="hoverable"><td class="center">-</td><td class="center">-</td><td class="center">MONTO POR PAGO ANTICIPADO</td><td class="center">UNIDAD</td><td class="center">1</td><td class="center">-'+Number($("#modalidadCampo2").val())+'</td><td class="center">-'+Number($("#modalidadCampo2").val())+'</td></tr>';
    }
    $("#tBodyDetalleVenta").append(html);
    calcularVuelto();
}
function actualizarTotalVenta(){
    total = 0;
    //console.log($("#tBodyDetalleVenta").children("tr"));
    $("#tBodyDetalleVenta").children("tr").each(function(key,val){
        //console.log($(val));
        //console.log($(val).html());
        //console.log($(val).children("td:last-child").html());
        total = total + Number($(val).children("td:last-child").html());
    });
    if(total<0){
        total = 0;
    }
    if($("#cboIdTipoDocumento").val()==5){
        if($('#chkIgv').is(':checked')){
            $("#thSubtotalGeneral").html(redondear(total/1.18,2));
            $("#thIgvGeneral").html(redondear((total/1.18)*0.18,2));
            $("#thTotalGeneral").html(redondear(total,2));
            $("#txtSubtotal").val(redondear(total/1.18,2));
            $("#txtIgv").val(redondear((total/1.18)*0.18,2));
            $("#txtTotal").val(redondear(total,2));
        }else{
            $("#thSubtotalGeneral").html(redondear(total,2));
            $("#thIgvGeneral").html(redondear(total*0.18,2));
            $("#thTotalGeneral").html(redondear(total*1.18,2));
            $("#txtSubtotal").val(redondear(total,2));
            $("#txtIgv").val(redondear(total*0.18,2));
            $("#txtTotal").val(redondear(total*1.18,2));
        }
        $('#chkIgv').attr("disabled",true);
    }else{
        $("#thSubtotalGeneral").html(redondear(total/1.18,2));
        $("#thIgvGeneral").html(redondear((total/1.18)*0.18,2));
        $("#thTotalGeneral").html(redondear(total,2));
        $("#txtSubtotal").val(redondear(total/1.18,2));
        $("#txtIgv").val(redondear((total/1.18)*0.18,2));
        $("#txtTotal").val(redondear(total,2));
    }
    calcularVuelto();
}
function calcularDescuento(idproducto){
    var cant=parseFloat($("#tdCant_"+idproducto).text());
    var precio=parseFloat($("#tdPrecio_"+idproducto).text());
    if($("#txtDescuento"+idproducto).val()==""){
        var descuento=0;        
    }else{
        var descuento=parseFloat($("#txtDescuento"+idproducto).val());    
    }
    var sub=Math.round((cant*precio*(1-descuento/100))*100)/100;
    $("#tdSubtotal_"+idproducto).html(sub);
    actualizarTotalVenta();
}
var montos  = [];
function BotonesDinero(monto){
    var actual = Number($('#txtDinero').val());
    var nuevo = Number(monto) + actual;
    montos.push(actual);
    $('#txtDinero').val(nuevo);
}
function MontoAnterior(){
    //console.log(montos);
    if(montos.length>0){
        var ultimo = $('#txtDinero').val();
        while($('#txtDinero').val()==ultimo && montos.length>=0){
            var ultimo = montos.pop();
        }
        $('#txtDinero').val(ultimo);
    }
}
function cambiarPatronBusqueda(){
    var actual = $("#txtModoPersona").val();
    if(actual == "N"){
        $("#txtModoPersona").val("D");
        alerta("EL MODO DE BUSQUEDA AHORA ES POR NUMERO DE DOCUMENTO DE CLIENTE",8000);
    }else{
        $("#txtModoPersona").val("N");
        alerta("EL MODO DE BUSQUEDA AHORA ES POR NOMBRE DE CLIENTE",8000);
    }
    listadoPersona2();
}
$("#cargagrilla").empty();
$("#txtPagoEfectivo").focus();
$("#txtDinero").focus();
listadoPersona2();
asignar();
//centraDivSucursal();
var selectTipoTarjeta = "<?php echo genera_cboGeneralSQL("select * from tipotarjeta order by idtipotarjeta",'TipoTarjeta','','',$objSalon); ?>"+'<label class="labelSuperior">Tipo de Tarjeta</label>';
<?php if($_GET['accion']=='ACTUALIZAR'){?>
//genera_cboCaja(<?php echo $dato['idsalon'];?>,<?php echo $dato['idcaja'];?>,'disabled');
<?php }else{
if(isset($_SESSION['R_IdSalon'])) $idsalon=$_SESSION['R_IdSalon']; else $idsalon=0;
if(isset($_SESSION['R_IdCaja'])) $idcaja=$_SESSION['R_IdCaja']; else $idcaja=0;
?>
//genera_cboCaja(<?php echo $idsalon;?>,<?php echo $idcaja;?>,'');
<?php }?>

agregartodo(<?php echo $_GET['Id'];?>);
generaNumeroVenta(4);
//document.getElementById('txtDinero').focus();
<?php
    if($tipoVenta=="C"){
?>
    $("#divCliente").hide();
    $("#h4DatosVenta").hide();
    $("#divDatosVenta").hide();
    $(".credito").hide();
    $("#btnDividirCuenta").hide();
<?php 
    }
?>
</script>
</head>
<body>
<form id="frmMantVenta" id="frmMantVenta" action="" method="POST">
    <input name="txtidmov" id="txtId" type="hidden" value="<?=$_GET["Id"]?>" />
    <input type="hidden" id="inptMoneda" value="S">
    <input type="hidden" id="optMoneda" name="optMoneda" value="S">
    <input type="hidden" id="HdnIdModalidad">
    <input type="hidden" id="tipoVentaSeleccionado" name="tipoVenta2">
    <input type="hidden" id="inptTotalVenta">
    <input type="hidden" id="inptTotalVenta2">
    <input type="hidden" id="inptModalidadCortesia" name="modCortesia">
    <input type="hidden" id="inptModalidadDivision" name="modDivision">
    <?php if($tipoVenta=="C"){?>
    <input type="hidden" id="idventacredito" name="idventacredito" value="<?php echo $ventaCredito;?>">
    <?php }?>
    <div class="col s12">
        <div class="Div-Activo">
            <div class="container Mesas frmCobrar">
                <div class="row" id="h4DatosVenta">
                    <div class="col s12"><h4 class="center blue lighten-4 blue-text text-darken-4">DATOS DE LA VENTA</h4></div>
                </div>
                <div class="row" id="divDatosVenta">
                    <div class="col s12 m6 l3">
                        <div class="input-field inline">
                            <select id="tipoVenta" name="tipoVenta" onchange="fnCambiarModalidad();">
                                <option value="N" selected="">VENTA COMÚN</option>
                                <option value="V">VENTA CON VALE DE DESCUENTO</option>
                                <option value="D">VENTA CON DESCUENTO DE PERSONAL</option>
                                <option value="A">VENTA CON PAGO ANTICIPADO</option>
                                <option value="C">VENTA AL CREDITO</option>
                                <option value="T">VENTA CON CORTESIA</option>
                            </select>
                            <label class="labelSuperior">Modalidad de Venta</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l3" hidden="" id="divModalidadCampo1">
                        <div class="input-field inline">
                            <input type="text" id="modalidadCampo1">
                            <label id="lblModalidadCampo1" for="modalidadCampo1">CAMPO 1</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l3" hidden="" id="divModalidadCampo2">
                        <div class="input-field inline">
                            <input type="text" id="modalidadCampo2">
                            <label id="lblModalidadCampo2" for="modalidadCampo2">CAMPO 2</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l3" hidden="" id="divModalidadCampo3">
                        <div class="input-field inline">
                            <input type="text" id="modalidadCampo3">
                            <label id="lblModalidadCampo3" for="modalidadCampo3">CAMPO 3</label>
                        </div>
                    </div>
                </div>
                <div class="row" id="h4DatosDocumento">
                    <div class="col s12"><h4 class="center blue lighten-4 blue-text text-darken-4">DATOS DEL DOCUMENTO</h4></div>
                </div>
                <div class="row" id="divDatosDocumento1">
                    <div class="col s12 m6 l2">
                        <div class="input-field inline credito">
                            <?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2","IdTipoDocumento",$dato[strtolower($value["descripcion"])],'',$objMantenimiento,"generaNumeroVenta(this.value)"); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2","IdTipoDocumento",6,'',$objMantenimiento,"generaNumeroVenta(this.value)");?>
                            <label class="labelSuperior">Tipo de Documento</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l2">
                        <div class="input-field inline credito">
                            <input id="txtNumero" name="txtNumero" type="text" value="<?php if($_GET["accion"]=="ACTUALIZAR"){ echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); }else{ echo $objMantenimiento->generaNumero(2,6,substr($_SESSION["R_FechaProceso"],6,4));}?>" onBlur="if(!validarnumeroconserie(this.value)){alert('El Numero debe tener el siguiente formato 000-000000-0000');this.value='<?php echo $objMantenimiento->generaNumero(2,6,substr($_SESSION["R_FechaProceso"],6,4));?>';}" onKeyUp="mascara(this,'-',new Array(3,6,4),true)">
                            <label for="txtNumero" class="active">Numero</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l2" >
                        <div class="input-field inline">
                            <input type="text" id="txtFecha" name="txtFecha" value="<?php if($_GET["accion"]=="ACTUALIZAR"){ echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); }else{ echo $_SESSION["R_FechaProceso"];}?>" readonly="true">
                            <label for="txtFecha" class="active">Fecha</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l6 valign-wrapper" id="divCliente">
                    	<div class="input-field inline col s9 m9 l2">
                            <input id="txtRUC2" name="txtRUC2" type="text" value="" class="autocomplete" autocomplete="off" readonly="">
                            <label id="lblPersona" for="txtRUC2" class="active">RUC/DNI</label>
                        </div>
                        <div class="input-field inline col s9 m9 l7">
                            <input type="hidden" name="txtIdSucursalPersona" id="txtIdSucursalPersona">
                            <input type="hidden" name="txtIdPersona" id="txtIdPersona">
                            <input type="hidden" name="txtModoPersona" id="txtModoPersona" value="N">
                            <input id="txtPersona" type="text" value="" class="autocomplete" autocomplete="off">
                            <label id="lblPersona" for="txtPersona" class="active">Cliente</label>
                        </div>
                        <div class="col s3 m3 l3 center valign-wrapper" style="padding: 0px 0px 0px 0px;">
                            <div class="col s6 right">
                                <button type="button" class="btn-floating red tooltipped" data-position="left" data-delay="30" data-tooltip="BORRAR SELECCION" onclick="limpiarCamposPersona();"><i class="material-icons">close</i></button>
                                <button type="button" onclick="modalNuevoPersona()" class="btn-floating light-green accent-1 tooltipped" data-position="left" data-delay="30" data-tooltip="AGREGAR CLIENTE"><i class="material-icons black-text">add</i></button>
                            </div>
                            <div class="col s6" hidden="">
                                <button type="button" onclick="cambiarPatronBusqueda()" class="btn-floating amber accent-4 tooltipped" data-position="left" data-delay="30" data-tooltip="CAMBIAR MODO DE BUSQUEDA"><i class="material-icons black-text">cached</i></button>
                            </div>
                        </div>
                        <div class="input-field inline col s9 m9 l8">
                            <input id="txtDireccion2" name="txtDireccion2" type="text" value="" class="autocomplete" autocomplete="off">
                            <label id="lblPersona" for="txtDireccion2" class="active">Direccion</label>
                        </div>
                    </div>
                </div>
                <div class="row" id="divDatosDocumento2">
                    <div class="col s12 m6 l3">
                        <div class="input-field inline">
                            <textarea id="txtComentario" name="txtComentario" class="materialize-textarea"></textarea>
                            <label for="txtComentario">Comentario</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l3" id="divModoPago">
                        <label style="margin-left: 15px;" class="col s12 left-align labelSuperior">Modo de Pago</label>
                        <div id="rbtnModoPago">
                            <div class="row">
                                <div class="col s6">
                                    <p class=" input-field inline" style="margin-top: 0px;">
                                        <input type="radio" value="E" name="rdbtnModoPago" id="chbxEFECTIVO" checked onchange="if(this.checked){$('#divEfectivo').show();$('#divTarjeta').hide();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').hide();$('#txtDinero').focus();$('#txtDinero').val('');$('#txtVuelto').val('');$('#divSelectTarjeta').html('');$('#divSelectAmbos').html('');}">
                                        <label for="chbxEFECTIVO">EFECTIVO</label>
                                    </p>
                                    <p class="input-field inline" style="margin-top: 0px;">
                                        <input type="radio" value="T" name="rdbtnModoPago" id="chbxTARJERA" onchange="if(this.checked){$('#divEfectivo').hide();$('#divTarjeta').show();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').hide();$('#cboTipoTarjeta').val('1');$('#divSelectTarjeta').html(selectTipoTarjeta);$('#divSelectAmbos').html('');$('select').material_select();}">
                                        <label for="chbxTARJERA">TARJETA</label>
                                    </p>
                                </div>
                                <div class="col s6">
                                    <p class=" input-field inline" style="margin-top: 0px;">
                                        <input type="radio" value="C" name="rdbtnModoPago" id="chbxCHEQUE" onchange="if(this.checked){$('#divEfectivo').hide();$('#divTarjeta').hide();$('#divAmbos').hide();$('#divCheque').show();$('#divDeposito').hide();$('#txtBancoCheque').focus();$('#txtBancoCheque').val('');$('#txtNumeroCheque').val('');}">
                                        <label for="chbxCHEQUE">CHEQUE</label>
                                    </p>
                                    <p class=" input-field inline" style="margin-top: 0px;">
                                        <input type="radio" value="D" name="rdbtnModoPago" id="chbxDEPOSITO" onchange="if(this.checked){$('#divEfectivo').hide();$('#divTarjeta').hide();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').show();$('#txtDinero').focus();$('#txtDinero').val('');$('#txtVuelto').val('');$('#divSelectTarjeta').html('');$('#divSelectAmbos').html('');}">
                                        <label for="chbxDEPOSITO">DEPÓSITO</label>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <p class=" input-field inline" style="margin-top: 0px;">
                                        <input type="radio" value="A" name="rdbtnModoPago" id="chbxAMBOS" onchange="if(this.checked){$('#divEfectivo').show();$('#divTarjeta').hide();$('#divAmbos').show();$('#divCheque').hide();$('#divDeposito').hide();$('#txtPagoEfectivo').focus();$('#txtPagoEfectivo').val('');$('#txtVuelto').val('');$('#divSelectTarjeta').html('');$('#divSelectAmbos').html(selectTipoTarjeta);$('select').material_select();}">
                                        <label for="chbxAMBOS">EFECTIVO Y TARJETA</label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m12 l6" id="divDatosPago">
                        <div class="" id="divCheque" hidden>
                            <div class="col s12 m4 l4">
                                <div class="input-field inline">
                                    <input type="text" class="" id="txtBancoCheque" name="banco_cheque">
                                    <label for="txtBancoCheque">Nombre Banco</label>
                                </div>
                            </div>
                            <div class="col s12 m4 l4">
                                <div class="input-field inline">
                                    <input type="text" class="" id="txtNumeroCheque" name="numero_cheque">
                                    <label for="txtNumeroCheque">Numero Cheque</label>
                                </div>
                            </div>
                            <div class="col s12 m4 l4">
                                <div class="input-field inline">
                                    <select id="txtMonedaCheque" name="moneda_cheque">
                                        <option value="S">SOLES</option>
                                        <option value="D">DOLARES</option>
                                    </select>
                                    <label class="labelSuperior">Moneda Cheque</label>
                                </div>
                            </div>
                        </div>
                        <div class="" id="divDeposito" hidden>
                            <div class="col s12 m6">
                                <div class="input-field inline">
                                    <input type="text" class="" id="txtBancoDeposito" name="banco_deposito">
                                    <label for="txtBancoDeposito">Nombre Banco</label>
                                </div>
                            </div>
                            <div class="col s12 m6">
                                <div class="input-field inline">
                                    <input type="text" class="" id="txtNumeroDeposito" name="numero_deposito">
                                    <label for="txtNumeroDeposito">Numero Operacion</label>
                                </div>
                            </div>
                            <div class="col s12 m6">
                                <div class="input-field inline">
                                    <input type="text" class="" id="txtImporteDeposito" name="importe_deposito">
                                    <label for="txtImporteDeposito">Importe</label>
                                </div>
                            </div>
                            <div class="col s12 m6">
                                <div class="input-field inline">
                                    <input type="date" id="txtFechaDeposito" name="fecha_deposito">
                                    <label for="txtFechaDeposito" class="active">Fecha Deposito</label>
                                </div>
                            </div>
                        </div>
                        <div class="" id="divTarjeta" hidden>
                            <div class="col s12">
                                <div class="input-field inline" id="divSelectTarjeta">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="divAmbos" hidden>
                            <div class="col s12 m6 l6">
                                <div class="input-field inline" id="divSelectAmbos" hidden="">
                                </div>
                            </div>
                            <div class="col s12 m6 l4">
                                <div class="input-field inline">
                                    <input id="txtPagoEfectivo" value="0" name="txtPagoEfectivo" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onblur="validarMontoEfectivo();$('#txtDinero').val($(this).val());calcularVuelto();">
                                    <label for="txtPagoEfectivo" class="active">Monto en Efectivo</label>
                                </div>
                                <input type="hidden" id="txtPagoCredito" name="txtPagoCredito">
                            </div>
                            <div class="col s12 m6 l4">
                            	<div class="input-field inline">
                            		<input id="txtMontoVisa" value="0" name="txtMontoVisa" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onblur="validarMontoEfectivo();" onclick="if($(this).val()<=0){$(this).val('')}">
                                    <label for="txtMontoVisa" class="active">Monto en Tarjeta VISA</label>
                            	</div>
                            </div>
                            <div class="col s12 m6 l4">
                            	<div class="input-field inline">
                            		<input id="txtMontoMastercard" value="0" name="txtMontoMastercard" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onblur="validarMontoEfectivo();" onclick="if($(this).val()<=0){$(this).val('')}">
                                    <label for="txtMontoMastercard" class="active">Monto en Tarjeta MASTERCARD</label>
                            	</div>
                            </div>
                        </div>
                        <div class="row" id="divEfectivo">
                            <div class="col s12 m6 l6">
                                <div class="input-field inline">
                                    <input type="text" class="inptCantidad" id="txtDinero" onkeyup="calcularVuelto();" onKeyPress="return validarsolonumerosdecimales(event,this.value);">
                                    <label for="txtDinero" class="active">Dinero</label>
                                </div>
                            </div>
                            <div class="col s12 m6 l6">
                                <div class="input-field inline">
                                    <input id="txtVuelto" class="inptCantidad" type="text" readonly="">
                                    <label for="txtVuelto" class="active">Vuelto</label>
                                </div>
                            </div>
                            <div class="col s12">
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(0.50);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">0.50</button>
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(1);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">1</button>
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(2);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">2</button>
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(5);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">5</button>
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(10);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">10</button>
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(20);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">20</button>
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(50);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">50</button>
                                <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;" onclick="BotonesDinero(100);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">100</button>
                                <button type="button" class="btn col s2 m1 l1 offset-m1 offset-l1  green tooltipped" data-position="bottom" data-delay="30" data-tooltip="MONTO ANTERIOR"  style="margin-right: 3px;" onclick="MontoAnterior();$('#txtDinero').trigger('keyup');$('#txtDinero').focus();"><i class="material-icons">history</i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row valign-wrapper" id="divDatosDocumento3">
                    <div class="col s12 m8 l2 credito" style="padding-bottom: 10px;" hidden="">
                        <div class="input-field inline">
                            <input type="hidden" id="txtImprimir" name="txtImprimir" value="S" />
                            <input type="checkbox" class="" id="chkImprimir" name="chkImprimir" checked="" onclick="if(this.checked){$('#txtImprimir').val('S');}else{$('#txtImprimir').val('N');}generaNumeroVenta($('#cboIdTipoDocumento').val());" />
                            <label for="chkImprimir">Imprimir</label>
                        </div>
                    </div>
                    <div class="col s12 m8 l2 credito" style="padding-bottom: 10px;" hidden="">
                        <div class="input-field inline">
                            <input type="hidden" id="txtConsumo" name="txtConsumo" value="N" />
                            <input type="checkbox" class="" id="chkConsumo" name="chkConsumo" onclick="if(this.checked){$('#txtConsumo').val('S');}else{$('#txtConsumo').val('N');}" />
                            <label for="chkConsumo">Por Consumo</label>
                        </div>
                    </div>
                    <div class="col s12 m8 l2 credito" style="padding-bottom: 10px;">
                        <div class="input-field inline">
                            <input type="hidden" id="txtFE" name="txtFE" value="S" />
                            <input type="checkbox" class="" checked="" id="chkFE" name="chkFE" onclick="if(this.checked){$('#txtFE').val('S');generaNumeroVenta($('#cboIdTipoDocumento').val());}else{$('#txtFE').val('N');generaNumeroVenta($('#cboIdTipoDocumento').val());}" />
                            <label for="chkFE" style="font-size: 30px;">POSTRES DE LA CASA</label>
                        </div>
                    </div>
                    <div class="col s12 m8 l6 credito" style="padding-bottom: 10px;">
                        <div class="input-field inline">
                            <input type="text" class="" id="txtGlosa" name="glosa_movimiento">
                            <label for="txtGlosa">Glosa</label>
                        </div>
                    </div>
                    <div class="col s12 m4 l2" style="padding-bottom: 10px;" id="divGuardar">
                        <button type="button" id="cmdGrabar" class="btn right amber darken-4" onclick="javascript:aceptar();">GUARDAR<i class="material-icons right">save</i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12" style="padding-bottom: 10px;">
                        <p id="pTotal" class="right" style="margin-top: 0px; padding-right: 15px; font-size: 2rem; font-weight: 600;"></p>
                    </div>
                </div>
                <div class="row col s12" id="h4DetalleDocumuento">
                    <h4 class="center blue lighten-4 blue-text text-darken-4">DETALLES DEL DOCUMENTO
                        <button id="btnDividirCuenta" type="button" onclick="modalDividirCuenta();" class="tooltipped btn-floating right orange accent-1" data-position="bottom" data-delay="50" data-tooltip="DIVIDIR LA CUENTA"><i class="material-icons orange-text text-darken-4">view_week</i></button></h4>
                </div>
                <div class="row" id="divDetalleDocumento">
                    <div class="col s12">
                        <p id="divChkImpuesto" hidden class="right" style="margin-top: 0px; padding-right: 15px;"><input type="checkbox" id="chkIgv" name="chkIgv" onClick="asignar();" onchange="calcularVuelto();" value="S" checked class="filled-in" checked="checked" /><label for="chkIgv">INCLUIDO IGV</label></p>
                        <div id="divDetalleVenta">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modalDividirCuenta">
        <div id="modalDividirCuenta" style="width: 85%;" class="modal modal-fixed-footer orange lighten-3">
            <div class="modal-content">
              <div class="white" style="padding: 10px;border-radius: 10px;">
                <div class="row">
                    <h4 class="center">DIVIDIR CUENTA</h4>
                </div>
                <div class="row">
                  <div class="col s12 center">
                      <div class="switch">
                        <label>
                          POR PRODUCTOS
                          <input type="checkbox" id="chkModalidadDividir" onchange="if($(this).is(':checked')){$('#divPorMontos').show();$('#divPorProductos').hide();}else{$('#divPorProductos').show();$('#divPorMontos').hide();}">
                          <span class="lever"></span>
                          POR MONTOS
                        </label>
                      </div>
                  </div>
                </div>
                <div class="row" id="divPorProductos">
                  <div class="col s12" id="divModalDividir">
                  </div>
                </div>
                  <div class="row" id="divPorMontos" hidden="">
                      <div class="input-field col s12 center">
                          <input type="number" min="0.01" step="0.01" id="txtMontoDivision" class="validate" required="" aria-required="true">
                          <label for="txtMontoDivision" data-error="MONTO INCORRECTO" data-success="MONTO CORRECTO">INGRESE EL MONTO QUE DESEA PAGAR</label>
                      </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
                <a href="#!" onclick="aceptarDividirCuenta();" class="modal-action modal-close btn light-green accent-1 black-text">Dividir<i class="material-icons right">content_cut</i></a>
            </div>
        </div>
    </div>
    </form>
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
    <div class="modalModalidadVenta">
        <div id="modalModalidadVenta" style="width: 85%;" class="modal modal-fixed-footer orange lighten-3">
            <div class="modal-content">
              <div class="white" style="padding: 10px;border-radius: 10px;">
                <div class="row">
                    <h4 class="center" id="h4ModalidadVenta"></h4>
                </div>
                <div class="row">
                  <div class="col s12" id="divModalidadVenta">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
                <a href="#!" onclick="/*$('#tipoVenta').val('N');$('#tipoVenta').material_select();*/" class="left modal-action modal-close btn red accent-1 black-text">Cerrar<i class="material-icons right">clear</i></a>
                <a href="#!" id="btnAceptarModalidadVenta" class="modal-action modal-close btn light-green accent-1 black-text">Aceptar<i class="material-icons right">check</i></a>
            </div>
        </div>
    </div>
</body>
</html>