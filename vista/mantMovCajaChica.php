<?php
require("../modelo/clsMovCaja.php");
require("../modelo/clsSalon.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
if($_GET["accion"]=="CIERRE"){$_SESSION["R_IdCaja"]=$_GET["idcaja"];}
try{
$objMantenimiento = new clsMovCaja($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstCaja = $objMantenimiento->obtenerTabla();
if(is_string($rstCaja)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstCaja."</td>";
}else{
	$datoCaja = $rstCaja->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataCaja = $rst->fetchAll();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
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
    $('#persona').attr("readonly",true);
}
function limpiarCamposPersona(){
    $('#txtIdSucursalPersona').val("");
    $('#txtIdPersona').val("");
    $('#persona').attr("readonly",false);
    $('#txtPersona').val("");
    $('#persona').val("");
    $('#persona').focus();
}
listadoPersona2();
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmCaja");
}
function aceptar(){
	if($("#txtIdPersona").length==0 || $("#txtIdPersona").val().length>0){
        if($("#txtTotal").length==0 || $("#txtTotal").val().length>0){
            if($("#txtReal").length==0 || $("#txtReal").val().length>0){
				if(setValidar("frmCaja") <?php if($_GET['accion']=="CIERRE"){?>&& $("#txtReal").val()!="" && parseFloat($("#txtReal").val())>=0 && confirm('EL DESCUADRE PARA EL CIERRE DE CAJA ES: ****. ¿CERRAR CAJA DE TODAS MANERAS?')<?php }?>){//+(parseFloat($("#txtMontoSoles").val()) - parseFloat($("#txtReal").val()))+
                    <?php if($_GET["accion"]=="CIERRE"){
                            echo "imprimirCierre();";
                        }
                    ?>
					g_ajaxGrabar.setURL("controlador/contMovCaja.php?ajax=true");
					g_ajaxGrabar.setRequestMethod("POST");
					setParametros();
			        	
					g_ajaxGrabar.response = function(text){
						loading(false, "loading");
						//buscar();
						alert(text);
						//cargamant.innerHTML="";	
						setRun('vista/listMovCajaChica','&id_clase=<?php echo $_GET['id_clase'];?>','frame','carga','imgloading');	
					};
					g_ajaxGrabar.request();
					loading(true, "loading", "frame", "line.gif",true);
				}
			}else{
                alert("DEBE LLENAR LA CANTIDAD REAL");
            }
        }else{
            alert("DEBE LLENAR UN MONTO");
        }
    }else{
        alert("DEBE SELECCIONAR A UNA PERSONA");
    }

}
function fnCalcularDescuadre(){
    if($("#txtReal").val().length>0){
        $("#txtDescuadre").val(parseFloat($("#txtReal").val()) - parseFloat($("#txtMontoSoles").val()));
    }else{
        $("#txtDescuadre").val(parseFloat($("#txtMontoSoles").val()));
    }
}
<!--LAS SIGUIENTES FUNCIONES LAS USO PARA LLAMAR AL XAJAX Y A LAS FUNCIONES DEL AUTOCOMPLETAR-->
function listadoPersona(div,idrol,nombres){
	var recipiente = document.getElementById(div);
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "BuscaPersona");
	g_ajaxPagina.setParameter("idrol", idrol);
	g_ajaxPagina.setParameter("nombres", nombres);
	g_ajaxPagina.setParameter("div", div);
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
			listadoPersona(div,'1,3,4',document.getElementById('txtPersona').value);
		}else{
			//si presiona retroceso o suprimir
			/*if(keyc == 8 || keyc == 46) {
				document.getElementById('txtIdMadre').value="";
			}
			listadoPersona(div,1,document.getElementById('txtMadre').value);*/
		}
  		eval('document.getElementById("'+div+'")'+'.style.display="";');
		window.setTimeout('document.getElementById("'+div+'")'+'.style.display="";', 300);
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
				document.getElementById('divregistrosPersona').style.display="none";
			}else{
				/*document.getElementById('txtIdMadre').value = id;
				document.getElementById('txtMadre').value = vNombres;
				divregistrosMadre.style.display="none";*/
			}
		};
		g_ajaxPagina.request();
}
function generaNumero(idtipodocumento){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxMovCaja.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "generaNumero");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.response = function(text){
			eval(text);
			document.getElementById('txtNumero').value=vnumero;
			<?php
			if($_GET['accion']=='NUEVO'){?>
			genera_cboConceptoPago(idtipodocumento);
			<?php }?>
		};
		g_ajaxPagina.request();
}

function verificarsituacion(){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxMovCaja.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "pedidospendientes");
		g_ajaxPagina.response = function(text){
			if(text!='ok'){
				alert('Debe atender todos los pedidos pendientes de las mesas');
                setRun("vista/listMovCajaChica","ajax=true&id_clase=53","frame","frame","imgloading");
				//document.getElementById('cargamant').innerHTML='';
				//buscar();
			}
		};
		g_ajaxPagina.request();
}

<?php
if($_GET['accion']=='CIERRE' or $_GET['accion']=='ASIGNAR'){?>
generaNumero(10);
verificarsituacion();
<?php }else{?>
generaNumero(9);
<?php }?>
function genera_cboConceptoPago(idtipodocumento){
		var recipiente = document.getElementById('divcboConceptoPago');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxMovCaja.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboConceptoPago");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.response = function(text){
                        //console.log(text);
			recipiente.innerHTML = text+'<label>Concepto Pago</label>';
                        $('select').material_select();
		};
		g_ajaxPagina.request();
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
            $("#persona").val();
            $("#persona").removeAttr("readonly");
            $("#persona").focus();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "contenido", "line.gif",true);
}
function ImprimirModal(){
    window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#tablaModalDetalleCerrar').html()));
}

function imprimirCierre(){
    g_ajaxPagina.setURL("http://localhost/lasmusas97843874/vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "imprimir_cierre");
    g_ajaxPagina.setParameter("efectivo",$("#tdEfectivo").html());
    g_ajaxPagina.setParameter("credito",$("#tdCredito").html());
    g_ajaxPagina.setParameter("visa",$("#tdVisa").html());
    g_ajaxPagina.setParameter("visa2",$("#tdVisa2").html());   
    g_ajaxPagina.setParameter("master",$("#tdMaster").html());
    g_ajaxPagina.setParameter("gastos",$("#tdGastos").html());
    g_ajaxPagina.setParameter("ingresos",$("#tdIngresos").html());
    g_ajaxPagina.setParameter("anticipado",$("#tdAnticipado").html());
    g_ajaxPagina.setParameter("apertura",$("#tdApertura").html());
    g_ajaxPagina.setParameter("real",$("#txtReal").val());
    g_ajaxPagina.setParameter("final",$("#txtFinal").val());
    g_ajaxPagina.setParameter("detalleGasto",$("#txtDetalleGastos").val());
    g_ajaxPagina.response = function(text){
        console.log(text);
        imprimirStock();
    };
    g_ajaxPagina.request();
}

function imprimirStock(){
    var g_ajaxPagina2 = new AW.HTTP.Request;
    g_ajaxPagina2.setURL("http://localhost/lasmusas784378/vista/ajaxPedido.php");
    g_ajaxPagina2.setRequestMethod("POST");
    g_ajaxPagina2.setParameter("accion", "imprimir_stock");
    g_ajaxPagina2.response = function(text){
        console.log(text);
    };
    g_ajaxPagina2.request();
}

function validarUsuario(){
    $('#modalValidarUsuario').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      ready: function(modal, trigger) {
          
      },
      complete: function() {} // Callback for Modal close
    });
}

function aceptarModalUsuario2(){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("vista/ajaxVenta.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "validarUsuario");
    g_ajaxPagina3.setParameter("pass",$("#txtPassword").val());
    g_ajaxPagina3.response = function(text){
        eval(text);
        if(vmsg=="S"){
            alert('Validado');
            $("#modalValidarUsuario").closeModal();
            imprimirCierre();
        }else{
            alert('Clave incorrecta');
        }
    };
    g_ajaxPagina3.request();
}

<?php if($_GET["accion"]=="ASIGNAR") {?>
genera_cboCaja(0,0,'');
<?php }?>
<?php if($_GET['accion']=="NUEVO"){?>
    CargarCabeceraRuta([["Nuevo","vista/mantMovCajaChica","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }else if($_GET['accion']=="APERTURA"){?>
    CargarCabeceraRuta([["Aperturar Caja","vista/mantMovCajaChica","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }else if($_GET['accion']=="CIERRE"){?>
    CargarCabeceraRuta([["Cerrar Caja","vista/mantMovCajaChica","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
$("#txtReal").focus();
</script>
</head>
<body>
    
<?php /*?>
<!--AUTOCOMPLETAR: LOS ESTILOS SIGUIENTES SON PARA CAMBIAR EL EFECTO AL MOMENTO DE NAVEGAR POR LA LISTA DEL AUTOCOMPLETAR-->
<style type="text/css">    
		.autocompletar tr:hover, .autocompletar .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
		.autocompletar tr span {text-decoration:none; color:#99CCFF; font-weight:bold; }
		.autocompletar {border:1px solid rgb(0, 0, 0); background-color:rgb(255, 255, 255); position:absolute; overflow:hidden; }
    </style>  
<!--AUTOCOMPLETAR-->  
<?php require("tablaheader.php");?>
<?php */?>

    <div class="container Mesas" style="padding-top: 20px;">
        <?php 
        if($_GET['accion']=='CIERRE'){
            $efectivo = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(totalpagado) IS NULL THEN 0 ELSE sum(totalpagado) END FROM movimientohoy WHERE idconceptopago = 3 AND estado='N' AND (modopago='E' OR modopago='A') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_visa_modoT = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total-totalpagado) IS NULL THEN 0 ELSE sum(total-totalpagado) END FROM movimientohoy WHERE idconceptopago in (3) AND estado='N' AND (modopago='T') AND idtipotarjeta = 1 and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_visa_modoA = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) END FROM movimientohoy WHERE idconceptopago in (3) AND estado='N' AND (modopago='A') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_visa = $tarjetas_visa_modoT + $tarjetas_visa_modoA;
            $tarjetas_visa2_modoT = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total-totalpagado) IS NULL THEN 0 ELSE sum(total-totalpagado) END FROM movimientohoy WHERE idconceptopago in (27) AND estado='N' AND (modopago='T') AND idtipotarjeta = 1 and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_visa2_modoA = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) END FROM movimientohoy WHERE idconceptopago in (27) AND estado='N' AND (modopago='A') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_visa2 = $tarjetas_visa2_modoT + $tarjetas_visa2_modoA;
            $tarjetas_mastercard_modoT = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM movimientohoy WHERE idconceptopago = 3 AND estado='N' AND (modopago='T') AND idtipotarjeta = 2 and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_mastercard_modoA = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum((substr(montotarjeta,position('2@' in montotarjeta)+2,length(montotarjeta)-2-position('1@' in montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(montotarjeta,position('2@' in montotarjeta)+2,length(montotarjeta)-2-position('1@' in montotarjeta)))::numeric) END FROM movimientohoy WHERE idconceptopago in (3,27) AND estado='N' AND (modopago='A') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_mastercard = $tarjetas_mastercard_modoT + $tarjetas_mastercard_modoA;
            $tarjetas_modoT = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM movimientohoy WHERE idconceptopago in (3,27) AND estado='N' AND (modopago='T') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas_modoA = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total-totalpagado) IS NULL THEN 0 ELSE sum(total-totalpagado) END FROM movimientohoy WHERE idconceptopago in (3,27) AND estado='N' AND (modopago='A') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $tarjetas = $tarjetas_visa + $tarjetas_mastercard;
            $cheques = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM movimientohoy WHERE idconceptopago = 3 AND estado='N' AND (modopago='C') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $depositos = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM movimientohoy WHERE idconceptopago = 3 AND estado='N' AND (modopago='D') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $saldoinicial = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(total) FROM movimientohoy WHERE idconceptopago = 1 AND estado='N' and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $ingresos = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(totalpagado) as total FROM movimientohoy mh WHERE idtipodocumento = 9 AND idconceptopago NOT IN (1,3,27) AND estado='N' and modopago not in ('C','D') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->total;
            $anticipado = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(totalpagado) as total FROM movimientohoy mh WHERE idtipodocumento = 9 AND idconceptopago IN (27) AND estado='N' and modopago not in ('C','D') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->total;
            $ingresostotalpagado = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(totalpagado) as totalpagado FROM movimientohoy mh WHERE idtipodocumento = 9 AND idconceptopago NOT IN (1,3) AND estado='N' and modopago not in ('C','D') and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->totalpagado;        
            $egresos = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(total) FROM movimientohoy mh WHERE idtipodocumento = 10 AND mh.estado = 'N' and idcaja=".$_SESSION["R_IdCaja"])
                    ->fetchObject()->sum;
            $credito = $objMantenimiento->obtenerDataSQL("select sum(total) from movimientohoy where comentario like '%VENTA AL CREDITO%' and idtipomovimiento=2 AND estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]."")->fetchObject()->sum;
            $datadetalle = $objMantenimiento
                    ->obtenerDataSQL("SELECT * FROM movimientohoy mh WHERE idtipodocumento = 10 AND mh.estado = 'N' and idcaja=".$_SESSION["R_IdCaja"]);
            $detallegastos="";
            while ($det = $datadetalle->fetchObject() ) {
                $detallegastos.=$det->comentario."@".$det->total."|";
            }
            if($detallegastos!=""){
                $detallegastos=substr($detallegastos, 0, strlen($detallegastos)-1);
            }
            ?>
        <div class="row" style="margin-bottom: 0px;">
            <div class="col s12"><h4 class="center blue lighten-4 blue-text text-darken-4">DETALLE DE CIERRE</h4></div>
            <p class="right" style="margin-top: 0px;margin-bottom: 0px;padding-right: 15px; font-size: 1.2rem;"><?php echo date("d/m/Y");?></p>
        </div>
        <div class="row">
            <div class="col s12 m6 l6">
                <h5 class="center blue lighten-4 blue-text text-darken-4">VENTAS</h5>
                <table class="bordered striped highlight" style="font-size: 1.5rem;display: none;">
                    <tbody>
                        <tr>
                            <td class="center">EFECTIVO(A)</td>
                            <td class="center" style="font-weight: 900" id="tdEfectivo"><?php echo number_format($efectivo, 2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('EFECTIVO');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">EFECTIVO PAGO ANTICIPADO(B)</td>
                            <td class="center" style="font-weight: 900" id="tdAnticipado"><?php echo number_format($anticipado, 2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('INGRESOS');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">TARJETA VISA(B)</td>
                            <td class="center" style="font-weight: 900" id="tdVisa"><?php echo number_format($tarjetas_visa,2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('TARJETAVISA');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr  style="display: none;">
                            <td class="center">TARJETA VISA PEDIDO(D)</td>
                            <td class="center" style="font-weight: 900" id="tdVisa2"><?php echo number_format($tarjetas_visa2,2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('TARJETAVISA');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">TARJETA MASTERCARD(C)</td>
                            <td class="center" style="font-weight: 900" id="tdMaster"><?php echo number_format($tarjetas_mastercard,2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('TARJETAMASTERCARD');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">TOTAL TARJETAS(B+C)</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($tarjetas+$tarjetas_visa2,2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('TARJETAS');" class="btn-floating lime accent-1 hide"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">VENTA AL CREDITO</td>
                            <td class="center" style="font-weight: 900" id="tdCredito"><?php echo number_format($credito, 2,'.','');?></td>
                            <td class="center"><button style="display: none;" type="button" onclick="modalDetalleCerrarCaja('EFECTIVO');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr  style="display: none;">
                            <td class="center">CHEQUES(F)</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($cheques,2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('CHEQUES');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr  style="display: none;">
                            <td class="center">DEPOSITOS(G)</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($depositos,2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('DEPOSITOS');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">TOTAL VENTA(A+B+C)</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($efectivo+$tarjetas+$tarjetas_visa2,2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('TOTAL');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col s12 m6 l6">
                <h5 class="center blue lighten-4 blue-text text-darken-4">MOVIMIENTOS</h5>
                <table class="bordered striped highlight" style="font-size: 1.5rem;">
                    <tbody>
                        <tr>
                            <td class="center">CAJA INICIAL(X)</td>
                            <td class="center" style="font-weight: 900" id="tdApertura"><?php echo number_format($saldoinicial, 2,'.','');?></td>
                            <td class="center">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="center">INGRESOS(Y)</td>
                            <td class="center" style="font-weight: 900" id="tdIngresos"><?php echo number_format($ingresos, 2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('INGRESOS');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">GASTOS(Z)<input type="hidden" id="txtDetalleGastos" value="<?=$detallegastos?>"></td>
                            <td class="center" style="font-weight: 900" id="tdGastos"><?php echo number_format($egresos, 2,'.','');?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('GASTOS');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="modalDetalleCerrarCaja" class="modal modalDetalleCerrarCaja" style="min-width: 90%;">
                <div class="col s12" style="padding: 0px;">
                    <h4 style="font-weight: 500;" class="indigo-text text-darken-4" id="tituloModalDetalleCerrar">
                        LISTA DE GASTOS
                        <button type="button" class="btn-floating red right" onclick="ImprimirModal();"><i class="material-icons">clear</i></button>
                        <button type="button" class="btn-floating red right modal-close"><i class="material-icons">clear</i></button>
                    </h4>
                </div>
                <div class="modal-content">
                    <div class="row" style="font-size: 1.5rem;font-weight: 700;">
                        <div class="col s12 m6 l6">
                            <div class="col s6 right-align">Cantidad:</div>
                            <div class="col s6 left-align" id="cantModalDetalleCerrar">11</div>
                        </div>
                        <div class="col s12 m6 l6">
                            <div class="col s6 right-align">Importe Total:</div>
                            <div class="col s6 left-align" id="totalModalDetalleCerrar">436.90</div>
                        </div>
                    </div>
                    <div class="row" id="tablaModalDetalleCerrar">
                        <table>
                            <thead id="headerModalDetalleCerrar">
                                <tr>
                                    <th class="center">N°</th>
                                    <th class="center">Descripcion</th>
                                    <th class="center">Concepto</th>
                                    <th class="center">Monto</th>
                                    <th class="center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="contenidoModalDetalleCerrar">
                                <tr>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12"><h4 class="center blue lighten-4 blue-text text-darken-4">ARQUEO</h4></div>
        </div>
        <?php }?>
        <form id="frmCaja" action="" method="POST">
        <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
        <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<?php
require("fun.php");
reset($dataCaja);
foreach($dataCaja as $value){
?>
	<?php if($value["idcampo"]==2){?>
            <div class="col s12 m6 l6">
                <div class="input-field inline" id="divcboConceptoPago">
                    <input type="hidden" id="cboConceptoPago" name="cboConceptoPago" value="<?php 
                    if($_GET["accion"]=="APERTURA") echo '1'; 
                    elseif($_GET["accion"]=="CIERRE") echo '2'; 
                    elseif($_GET["accion"]=="ASIGNAR") echo '18';?>">
                        <input type="text"  readonly id="inptConceptoPago" value="<?php if($_GET["accion"]=="APERTURA") echo 'APERTURA DE CAJA'; elseif($_GET["accion"]=="CIERRE") echo 'CIERRE DE CAJA'; elseif($_GET["accion"]=="ASIGNAR") echo 'ASIGNAR MONTO CAJA';?>"><label for="inptConceptoPago" class="active">Concepto de Pago</label>
                </div>
            </div>
        </div>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input type="text" id="txt<?php echo $value["descripcion"];?>" readonly name = "txt<?php echo $value["descripcion"];?>" value = "" size="6" maxlength="6" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) return false;">
                    <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                </div>
            </div>
        </div>
    <?php }?>
    <?php if($value["idcampo"]==6){?>
        <div class="row" <?php if($_GET['accion']=='CIERRE'){?>hidden=""<?php }?>>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <?php if($_GET["accion"]=="APERTURA") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=9",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); elseif($_GET["accion"]=="CIERRE" OR $_GET["accion"]=="ASIGNAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=10",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4",$value["descripcion"],0,'',$objMantenimiento,"generaNumero(this.value)");?>
                    <label>Tipo de Documento</label>
                </div>
            </div>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
        <div class="row" <?php if($_GET['accion']=='CIERRE'){?>hidden=""<?php }?>>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input name = "txt<?php echo $value["descripcion"];?>" type="text" readonly id="txt<?php echo $value["descripcion"];?>" value = "<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10">
                    <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                </div>
            </div>
    <?php }?>
    <?php if($value["idcampo"]==13 and $_GET["accion"]=="NUEVO") {?>
            <input name="optMoneda" type="radio" id="optS" value="S" checked="checked" hidden>
	<?php }?>
    <?php if($value["idcampo"]==17){?>
        <?php if($_GET['accion']=='CIERRE'){?><div class="row"><?php }?>
            <div class="col s12 <?php if($_GET['accion']=='CIERRE'){?>m4 l4<?php }else{?>m6 l6<?php }?>">
                <div class="input-field inline" <?=($_GET["accion"]=="CIERRE"?"hidden=''":"")?>>
            <?php 
            if($_GET["accion"]=="NUEVO") {?>
            <input style="font-weight: 900;font-size: 1.5rem;" type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "" maxlength="11" size="15" onKeyPress="return validarsolonumerosdecimales(event,this.value);" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>>
            <label for="txtTotal">Monto (S/)</label>
            <?php }else{?><input style="font-weight: 900;font-size: 1.5rem;" name="txtMontoSoles" type="text" id="txtMontoSoles" 
				value="<?php $read = "";
				if($_GET['accion']=='APERTURA'){
				$rst = $objMantenimiento->montodeaperturasoles($_SESSION['R_IdCaja']);
				//echo $rst;	
				echo "0";
				}elseif($_GET['accion']=='CIERRE'){
				$rst = $objMantenimiento->montodecierresoles($_SESSION['R_FechaProceso'],$_SESSION['R_IdCaja']);$read = "readonly";
				//echo $rst;
                                echo $efectivo+$ingresostotalpagado-$egresos;
				}			
				?>" size="15" maxlength="11" <?php 
				$num = $objMantenimiento->existenciamov();
				if(($_GET['accion']=='APERTURA' && $num==0) or $_GET['accion']=='ASIGNAR'){
				}else{
				//echo "readonly=''";
				}
				echo " $read ";?> onKeyPress='return validarsolonumerosdecimales(event,this.value);' ><label for="monto" class="active">Monto Sistema<?php if($_GET["accion"]=="CIERRE") echo "(A+X+Y-Z)";else echo "(S/)";?></label>
          <?php }?>
                </div>
            </div>
        <?php if($_GET['accion']!='CIERRE'){?></div><?php }else{?>
            <div class="col s12 m2 l2">
                <div class="input-field inline">
                    <input style="font-weight: 900;font-size: 1.5rem;" type="number" id="txtReal" name="txtReal" step="0.01" min="0" onblur="fnCalcularDescuadre();" onkeyup="fnCalcularDescuadre();">
                    <label for="txtReal" class="active">Cantidad Real</label>
                </div>
            </div>
            <div class="col s12 m2 l2" hidden="">
                <div class="input-field inline">
                    <input style="font-weight: 900;font-size: 1.5rem;" type="text" id="txtDescuadre" value="" name="txtDescuadre" readonly="">
                    <label for="txtDescuadre" class="active">Caja Descuadre</label>
                </div>
            </div>
            <div class="col s12 m2 l2">
                <div class="input-field inline">
                    <input style="font-weight: 900;font-size: 1.5rem;" type="number" id="txtFinal" name="txtFinal" step="0.01" min="0">
                    <label for="txtFinal" class="active">Caja Chica Final</label>
                </div>
            </div>
            <div class="col s12 m1 l1" style="display: none;"><button type="button" onclick="/*validarUsuario();*/imprimirCierre();" class="btn-large orange accent-1 truncate orange-text text-darken-4 tooltipped" title="Imprimir Cierre">IMPRIMIR CIERRE<i class="material-icons lime-text text-darken-4">print</i></button></div>
        </div>
        <?php }?>
	<?php }?>
    <?php 
    if($value["idcampo"]==20){?>
        <?php
        if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="ASIGNAR") { ?>
        <div class="row">
            <div class="col s12 m6 l6">
                <div class="input-field inline col s12 m12 l12">
                    <input type="hidden" id="txtIdPersona" name="txtIdPersona" value="<?php echo $_SESSION['R_IdPersona'];?>">
                    <input type="hidden" id="txtIdSucursalPersona" name="txtIdSucursalPersona" value="1">
                    <input type="text" id="persona" class="autocomplete" value="<?php echo $_SESSION['R_NombreSucursal'];?>'" readonly>
                    <label for="persona" class="active">Persona</label>
                </div>
            </div>
        <?php }elseif($_GET["accion"]=="CIERRE"){ ?>
            <input type="hidden" id="txtIdPersona" name="txtIdPersona" value="<?php echo $_SESSION['R_IdPersona'];?>">
            <input type="hidden" id="txtIdSucursalPersona" name="txtIdSucursalPersona" value="1">
        <?php }else{ ?>
        <div class="row">
            <div class="col s12 m6 l6 valign-wrapper">
                <div class="input-field inline col s10 m10 l11">
                    <input type="hidden" id="txtIdPersona" name="txtIdPersona">
                    <input type="hidden" id="txtIdSucursalPersona" name="txtIdSucursalPersona">
                    <input type="text" id="persona" class="autocomplete" ondblclick="limpiarCamposPersona()" autocomplete="off">
                    <label for="persona">Persona</label>
                </div>
                <div class="col s2 m2 l1 center">
                    <button type="button" onclick="modalNuevoPersona()" class="btn-floating light-green accent-1"><i class="material-icons black-text">add</i></button>
                </div>
            </div>
        <?php }?>
    <?php }?>
    <?php if($value["idcampo"]==24){?>
        <div class="row">
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input name = "txtNroOperacion" type="text" id="txtNroOperacion" value = "" size="10" >
                    <label for="txtNroOperacion" class="active">Nro. Comprobante</label>
                </div>
            </div>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <textarea id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" class="materialize-textarea"></textarea>
                    <label for="txt<?php echo $value["descripcion"];?>"><?php echo $value["comentario"];?></label>
                </div>
            </div>
        </div>
        <?php if($_GET["accion"]=="APERTURA" && $_SESSION["R_IdCaja"]=="8") {?>
        <div class="row">
            <div class="col s3 m3 l3">
            	<div class="input-field inline">
            		<label for="cboEGeneral" class="active">E. General</label>
	            	<select name="cboEGeneral" id="cboEGeneral">
	            			<?php
	            			$rest=$objMantenimiento->obtenerDataSQL("select * from color where estado='N'");
	            			while ($var=$rest->fetchObject()) {
	            				echo "<option value='$var->idcolor'>$var->nombre</option>";
	            			}
	            			?>
	            	</select>
	            </div>
	        </div>
	        <div class="col s3 m3 l3">
	        	<div class="input-field inline">
            		<label for="txtEGeneral" class="active">Nro.</label>
            		<input type="text" name="txtEGeneral" id="txtEGeneral" value="">
            	</div>
        	</div>
            <div class="col s3 m3 l3">
            	<div class="input-field inline">
            		<label for="cboEVIPcboEVIP" class="active">E. VIP</label>
	            	<select name="cboEVIP" id="cboEVIP">
	            			<?php
	            			$rest=$objMantenimiento->obtenerDataSQL("select * from color where estado='N'");
	            			while ($var=$rest->fetchObject()) {
	            				echo "<option value='$var->idcolor'>$var->nombre</option>";
	            			}
	            			?>
	            	</select>
	            </div>
	        </div>
	        <div class="col s3 m3 l3">
	        	<div class="input-field inline">
            		<label for="txtEVIP" class="active">Nro.</label>
            		<input type="text" name="txtEVIP" id="txtEVIP" value="">
            	</div>
        	</div>
        </div>
        <?php }?>	
        <?php if($_GET["accion"]=="NUEVO") {?>
        <div class="row">
        	<div class="col s12 m6 l2">
                <div class="input-field inline">
                    <select id="txtTipoPago" name="txtTipoPago" onchange="if(this.value=='T'){$('#divTarjeta').css('display','');}else{$('#divTarjeta').css('display','none');if(this.value=='A'){$('#divAmbos').css('display','');}else{$('#divAmbos').css('display','none');}}">
                        <option <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){if($dato["tipopago"]=="E"){ echo 'selected=""';}}?> value="E">EFECTIVO</option>
                        <option <?php if($_GET['accion']=='ACTUALIZAR' || $_GET["accion"]=="PAGAR"){if($dato["tipopago"]=="T"){ echo 'selected=""';}}?> value="T">TARJETA</option>
                    </select>
                    <label>Tipo de Pago</label>
                </div>
            </div>
            <div class="col s12 m6 l2" id="divTarjeta" style="display: none;">
                <div class="input-field inline" id="cboTipoTarjeta">
                    <select id="cboTipoTarjeta" name="cboTipoTarjeta">
                        <option value="1">VISA</option>
                        <option value="2">MASTER</option>
                    </select>
                    <label for="cboTipoTarjeta" class="active">Tipo Tarjeta</label>
                </div>
            </div>
        </div>
    	<?php }?>
	<?php }?>
    <?php if($_GET["accion"]=="ASIGNAR") {
    if($value["idcampo"]==35){?>
	<tr><td class="alignright">Sal&oacute;n</td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$dato['idsalon'],'disabled',$objSalon,'genera_cboCaja(this.value,0,"")'); else echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',0,'',$objSalon,'genera_cboCaja(this.value,0,"")');?></td></tr>
    <tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><div id="divcboCaja"></div></td>
	<?php }}?>

<?php }?>
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
    <div id="modalValidarUsuario" class="modal modal-fixed-footer orange lighten-3" style="height: 50%">
        <div class="modal-content">
          <div class="white" style="padding: 10px;border-radius: 10px;">
                <form id="" method="POST" action="">
                    <h4>Validar Usuario</h4>
                    <div class="row">
                      <div class="col s12">
                          <div class="input-field inline">
                            <input type="password" id="txtPassword" name="txtPassword">
                            <label for="txtPassword">Password</label>
                          </div>
                      </div>
                    </div>
                </form>
          </div>
        </div>
        <div class="modal-footer amber lighten-3">
            <button id="btnAceptarModalUsuario" type="button" onclick="aceptarModalUsuario2()" class="waves-effect waves-green btn light-green accent-1 black-text">Validar<i class="material-icons right">check</i></button>
        </div>
    </div>
</body>
</html>