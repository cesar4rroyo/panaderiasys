<?php
require("../modelo/clsMovimiento.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['id_cliente'];
//echo $id_clase;
try{
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR"){
    $rst = $objMantenimiento->obtenerDataSQL("SELECT pa.*,psm.apellidos,psm.nombres FROM pagoanticipado pa LEFT JOIN persona ps ON ps.idpersona = pa.idcliente
    LEFT JOIN personamaestro psm ON psm.idpersonamaestro = ps.idpersonamaestro WHERE idpagoanticipado = ".$_GET["Id"]);
	//$rst = $objMantenimiento->consultarMarca(1,1,'2',1,$_GET["Id"],$id_cliente,"");
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
	g_ajaxGrabar.setParameter("txtIdSucursal", document.getElementById("txtIdSucursal").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
	getFormData("frmMantMarca");	
}
function aceptar(){
    if($("#txtIdPersona").val()>0 && 
            !isNaN($("#txtValor").val()) && $("#txtValor").val().trim().length>0 && Number($("#txtValor").val())>0 &&
            $("#txtFecha").val().length>0 && $("#txtDatos").val().trim().length>0 && $("#txtTotal").val()!=""){
        if(setValidar("frmMantMarca")){
            var vidtipodocumento = $("#cboIdTipoDocumento").val();
    		g_ajaxGrabar.setURL("controlador/contPagoAnticipado.php?ajax=true");
    		g_ajaxGrabar.setRequestMethod("POST");
    		setParametros();
    		g_ajaxGrabar.response = function(text){
                eval(text);
    			loading(false, "loading");
    			if(vtext==1){
    				alert('La descripción de la marca no esta disponible, intente con otra descripción.');						
    			}else{
        			alert(vtext);
                    if(vidmovimiento!="0") imprimirEgreso(vidmovimiento);
                    if(vidtipodocumento!="19"){
                        if(vidventa!="0"){
                            declarar2(vidventa,vidtipodocumento);
                            setTimeout("imprimir3('"+vidventa+"')", 3000);
                        }
                    }
    			}
    		};
    		g_ajaxGrabar.request();
    		loading(true, "cargamant", "frame", "line.gif",true);
    	}
    }else{
        alert("CAMPOS LLENADOS INCORRECTAMENTE");
        $(this).stopImmediatePropagation();
    }
}

function declarar2(idventa,idtipodocumento){
    if(idtipodocumento==4){
        var vaccion='enviarBoleta';
    }else{
        var vaccion='enviarFactura';
    }
    g_ajaxPagina.setURL("controlador/contComprobante.php");
    g_ajaxPagina.setRequestMethod("GET");
    g_ajaxPagina.setParameter("funcion", vaccion);
    g_ajaxPagina.setParameter("idventa",idventa);
    g_ajaxPagina.response = function(text){
        //imprimir2(idventa,consumo,glosa);
        console.log(text);
    };
    g_ajaxPagina.request();
}

function imprimir3(idventa){
    g_ajaxPagina.setURL("http://localhost/lasmusas/vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "imprimir_ventaelectronica");
    g_ajaxPagina.setParameter("idventa",idventa);
    g_ajaxPagina.response = function(text){
        //alert("imprimiendo");         
    };
    g_ajaxPagina.request();
}

function listadoPersona2(){
    $.ajax({
        url: "vista/ajaxPersonaMaestro.php",
        type: 'POST',
        data: "accion=BuscaPersonaJSON&idrol=1,3,4,5&nombres=&tipopersona=DNI",
        success: function(a) {
            a = JSON.parse(a);
            var datos = a.datos;
            //console.log($(".autocomplete-content"));
            $(".autocomplete-content").remove();
            $("#persona").autocomplete({
                data: datos
            },selecctionarPersona,"");
        }
    });
}
function selecctionarPersona(dato){
    var ids = dato.split("|");
    $('#txtIdSucursalPersona').val(ids[0]);
    $('#txtIdPersona').val(ids[1]);
    $('#persona').prop("readonly",true);
}
function limpiarCamposPersona(){
    $('#txtIdSucursalPersona').val("");
    $('#txtIdPersona').val("");
    $('#persona').prop("readonly",false);
    $('#txtPersona').val("");
    $('#persona').val("");
    $('#persona').focus();
}
listadoPersona2();
<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR"){?>
CargarCabeceraRuta([["<?=$_GET["accion"]?> - <?php echo umill(str_pad($dato["correlativo"],6,"0",STR_PAD_LEFT));?>",'vista/mantPagoAnticipado','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantPagoAnticipado','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();

function aceptarModalPersona(){
    g_ajaxGrabar.setURL("controlador/contPersona.php?ajax=true");
    g_ajaxGrabar.setRequestMethod("POST");
    setParametrosModalPersona();
    g_ajaxGrabar.response = function(text){
            loading(false, "loading");
            alert(text);
            listadoPersona2();
            listadoPersona3();
            $('#modalNuevoPersona').closeModal();
            $("#persona").val();
            $("#persona").removeAttr("readonly");
            $("#persona").focus();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "contenido", "line.gif",true);
}
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
                                //$("#btnAceptarModalPersona").attr("disabled");
			}else{
				$("#LabelVerificaNroDoc").hide();
                                //$("#btnAceptarModalPersona").removeAttr("disabled");
			}
                        console.log(text);
		};
		g_ajaxPagina.request();
}
function imprimirEgreso(id){
    g_ajaxGrabar.setURL("http://localhost/lasmusas/vista/ajaxPedido.php");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "imprimir_egreso");
	g_ajaxGrabar.setParameter("id", id);
	g_ajaxGrabar.response = function(text){
        eval(text);
		loading(false, "loading");
		buscar();
		alert(vmsg);			
	};
	g_ajaxGrabar.request();
	
	loading(true, "loading", "grilla", "linea.gif",true);	
}
function generaNumeroVenta(idtipodocumento){
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxVenta.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "generaNumeroElectronico");
    g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
    g_ajaxPagina.response = function(text){
        eval(text);
        document.getElementById('txtNumero').value=vnumero;
    };
    g_ajaxPagina.request();
}
function listadoPersona3(){
    $.ajax({
        url: "vista/ajaxPersonaMaestro.php",
        type: 'POST',
        data: "accion=BuscaPersonaJSON&idrol=1,3,4,5&nombres=&tipopersona=DNI&modo="+$("#txtModoPersona2").val(),
        success: function(a) {
            a = JSON.parse(a);
            var datos = a.datos;
            $("#txtPersona2").autocomplete({
                data: datos
            },selecctionarPersona2,"");
        }
    });
}
function selecctionarPersona2(dato){
    var ids = dato.split("|");
    $('#txtIdSucursalPersona').val(ids[0]);
    $('#txtIdPersona2').val(ids[1]);
    $('#txtRUC2').val(ids[2]);
    $('#txtDireccion2').val(ids[3]);
    $('#txtPersona2').attr("readonly",true);
}
function limpiarCamposPersona2(){
    $('#txtIdSucursalPersona2').val("");
    $('#txtIdPersona2').val("");
    $('#txtPersona2').attr("readonly",false);
    $('#txtPersona2').val("");
    $('#txtPersona2').focus();
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
generaNumeroVenta(4);
listadoPersona3();
</script>
</head>
<body>
    <div class="container Mesas">
        <form id="frmMantMarca" action="" method="POST">
            <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR")echo $_GET['Id'];?>">
            <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
            <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
            <table width="200" border="0">
                <div class="row">
                    <div class="col s12 m6 l6 valign-wrapper">
                        <div class="input-field inline col s10 m10 l10">
                            <input type="hidden" id="txtIdPersona" name="txtIdPersona" value="<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR"){ echo $dato["idcliente"];}?>">
                            <input type="hidden" id="txtIdSucursalPersona" name="txtIdSucursalPersona" value="<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR"){ echo $_SESSION["R_IdSucursalUsuario"];}?>">
                            <input type="text" name="persona" id="persona" class="autocomplete" ondblclick="limpiarCamposPersona()" autocomplete="off" <?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR"){ echo 'readonly="" value="'.$dato["apellidos"]." ".$dato["nombres"].'"';}?>>
                            <label for="persona" <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){ echo 'class="active"';}?>>Propietario</label>
                        </div>
                        <div class="col s2 m2 l2 center">
                            <button type="button" onclick="limpiarCamposPersona()" <?if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR") echo "disabled"; ?> class="btn-floating red accent-1"><i class="material-icons black-text">clear</i></button>
                            <button type="button" onclick="modalNuevoPersona()" <?if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR") echo "disabled"; ?> class="btn-floating light-green accent-1"><i class="material-icons black-text">add</i></button>
                        </div>
                    </div>
                    <div class="col s12 m6 l2">
                        <div class="input-field inline">
                            <input type="text" id="txtTotal" name = "txtTotal" <?if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR") echo "readonly"; ?> value = "<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR")echo htmlentities(umill($dato["total"]), ENT_QUOTES, "UTF-8");?>" onkeypress="return validarsolonumerosdecimales(event,this.value);">
                            <label for="txtTotal" <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){ echo 'class="active"';}?>>Total</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l2">
                        <div class="input-field inline">
                            <input type="text" id="txtValor" name = "txtValor" <?if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR") echo "readonly"; ?> value = "<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR")echo htmlentities(umill($dato["valor"]), ENT_QUOTES, "UTF-8");?>" onkeypress="return validarsolonumerosdecimales(event,this.value);" onkeyup="$('#txtTo').val(this.value);">
                            <label for="txtValor" <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){ echo 'class="active"';}?>>A Cta.</label>
                        </div>
                    </div>
                    <? if($_GET["accion"]=="PAGAR"){?>
                    <div class="col s12 m6 l2">
                        <div class="input-field inline">
                            <input type="text" id="txtPago" name = "txtPago" value = "" onkeypress="return validarsolonumerosdecimales(event,this.value);">
                            <label for="txtPago" <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){ echo 'class="active"';}?>>Pago</label>
                        </div>
                    </div>
                    <? }?>
                </div>
                <div class="row">
                    <div class="col s12 m6 l4">
                        <div class="input-field inline">
                            <select id="txtTipoPago" name="txtTipoPago" onchange="if(this.value=='T'){$('#divTarjeta').css('display','');}else{$('#divTarjeta').css('display','none');if(this.value=='A'){$('#divAmbos').css('display','');}else{$('#divAmbos').css('display','none');}}">
                                <option <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){if($dato["tipopago"]=="E"){ echo 'selected=""';}}?> value="E">EFECTIVO</option>
                                <option <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){if($dato["tipopago"]=="C"){ echo 'selected=""';}}?> value="C">CHEQUE</option>
                                <option <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){if($dato["tipopago"]=="D"){ echo 'selected=""';}}?> value="D">DEPOSITO</option>
                                <option <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){if($dato["tipopago"]=="T"){ echo 'selected=""';}}?> value="T">TARJETA</option>
                                <option <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){if($dato["tipopago"]=="T"){ echo 'selected=""';}}?> value="A">EFECTIVO Y TARJETA</option>
                            </select>
                            <label>Tipo de Pago</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l2">
                        <div class="input-field inline" id="inptEntrega">
                            <input type="hidden" name="txtEntrega" id="txtEntrega" value="N">
                            <input type="checkbox" id="chkEntrega" name="chkEntrega" onclick="if(this.checked){$('#txtEntrega').val('E');}else{$('#txtEntrega').val('N');}">
                            <label for="chkEntrega" class="active" style="font-size: 20px;">Entrega Automatica</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l6">
                        <div class="input-field inline">
                            <input type="text" id="txtDatos" name = "txtDatos" value = "<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR")echo htmlentities(umill($dato["datosadicionales"]), ENT_QUOTES, "UTF-8");?>">
                            <label for="txtDatos" <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){ echo 'class="active"';}?>>Datos Adicionales al Tipo Pago</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6 l2">
                        <div class="input-field inline" id="inptFechaInicio">
                            <input type="date" id="txtFecha" name="txtFecha" <?if($_GET["accion"]=="PAGAR") echo "readonly"; ?> value="<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR"){ echo $dato["fecha"];}elseif($_GET["accion"]=="NUEVO"){$fecha = explode('/',$_SESSION['R_FechaProceso']); echo $fecha[2]."-".$fecha[1]."-".$fecha[0];}?>">
                            <label for="txtFecha" class="active">Fecha de Pago</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l2">
                        <div class="input-field inline" id="inptFechaEntrega">
                            <input type="date" id="txtFechaEntrega" name="txtFechaEntrega" <?if($_GET["accion"]=="PAGAR") echo "readonly"; ?> value="<?php if($_GET["accion"]=="ACTUALIZAR" || $_GET["accion"]=="PAGAR"){ echo $dato["fechaentrega"];}elseif($_GET["accion"]=="NUEVO"){$fecha = explode('/',$_SESSION['R_FechaProceso']); echo $fecha[2]."-".$fecha[1]."-".$fecha[0];}?>">
                            <label for="txtFechaEntrega" class="active">Fecha de Entrega</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l2">
                        <div class="input-field inline" id="inptComprobante">
                            <input type="hidden" name="txtComprobante" id="txtComprobante" value="N">
                            <input type="checkbox" id="chkComprobante" name="chkComprobante" onclick="if(this.checked){$('#txtComprobante').val('S');$('.comprobante').css('display','');}else{$('#txtComprobante').val('N');$('.comprobante').css('display','none');}">
                            <label for="chkComprobante" class="active" style="font-size: 20px;">Comprobante</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l6" id="divTarjeta" style="display: none;">
                        <div class="input-field inline" id="cboTipoTarjeta">
                            <select id="cboTipoTarjeta" name="cboTipoTarjeta">
                                <option value="1">VISA</option>
                                <option value="2">MASTER</option>
                            </select>
                            <label for="cboTipoTarjeta" class="active">Tipo Tarjeta</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l6" id="divAmbos" style="display: none;">
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
                </div>
                <div class="row comprobante" id="divDatosDocumento1" style='display: none;'>
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
                            <input type="text" id="txtFecha2" name="txtFecha2" value="<?php if($_GET["accion"]=="ACTUALIZAR"){ echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); }else{ echo $_SESSION["R_FechaProceso"];}?>" readonly="true">
                            <label for="txtFecha2" class="active">Fecha</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l6 valign-wrapper" id="divCliente">
                        <div class="input-field inline col s9 m9 l2">
                            <input id="txtRUC2" name="txtRUC2" type="text" value="" class="autocomplete" autocomplete="off" readonly="">
                            <label id="lblPersona" for="txtRUC2" class="active">RUC/DNI</label>
                        </div>
                        <div class="input-field inline col s9 m9 l7">
                            <input type="hidden" name="txtIdSucursalPersona2" id="txtIdSucursalPersona2">
                            <input type="hidden" name="txtIdPersona2" id="txtIdPersona2">
                            <input type="hidden" name="txtModoPersona2" id="txtModoPersona2" value="N">
                            <input id="txtPersona2" type="text" value="" class="autocomplete" autocomplete="off">
                            <label id="lblPersona" for="txtPersona2" class="active">Cliente</label>
                        </div>
                        <div class="col s3 m3 l3 center valign-wrapper" style="padding: 0px 0px 0px 0px;">
                            <div class="col s6 right">
                                <button type="button" class="btn-floating red tooltipped" data-position="left" data-delay="30" data-tooltip="BORRAR SELECCION" onclick="limpiarCamposPersona2();"><i class="material-icons">close</i></button>
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
                <div class="row col s12 comprobante" id="h4DetalleDocumuento" style='display: none;'>
                    <h4 class="center blue lighten-4 blue-text text-darken-4">DETALLES DEL DOCUMENTO</h4>
                    <table style='display: none;' class="comprobante">
                        <thead>
                            <tr>
                                <th class="center">Cantidad</th>
                                <th class="center">Descripcion</th>
                                <th class="center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="txtCant" id="txtCant" value="1" style="width: : 100px" onkeypress="return validarsolonumerosdecimales(event,this.value);"></td>
                                <td><textarea id="txtPro" name="txtPro"></textarea></td>
                                <td><input type="text" name="txtTo" id="txtTo" value="1" onkeypress="return validarsolonumerosdecimales(event,this.value);"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
                <button id="btnAceptarModalPersona" type="button" onclick="aceptarModalPersona()" class="waves-effect waves-green btn light-green accent-1 black-text">Agregar<i class="material-icons right">add</i></button>
            </div>
        </div>
    </div>
</body>
</HTML>