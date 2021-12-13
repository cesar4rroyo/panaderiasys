<?php
require("../modelo/clsMovCaja.php");
require("../modelo/clsSalon.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
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
				if(setValidar("frmCaja") <?php if($_GET['accion']=="CIERRE"){?>&& $("#txtReal").val()!="" && parseFloat($("#txtReal").val())>0 && confirm('EL DESCUADRE PARA EL CIERRE DE CAJA ES: '+(parseFloat($("#txtReal").val()) - parseFloat($("#txtMontoSoles").val()))+'. ¿CERRAR CAJA DE TODAS MANERAS?')<?php }?>){
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
			/*if(text!='ok'){
				alert('Debe atender todos los pedidos pendientes de las mesas');
                                setRun("vista/listMovCajaChica","ajax=true&id_clase=53","frame","frame","imgloading");
				//document.getElementById('cargamant').innerHTML='';
				//buscar();
			}*/
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
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM movimientohoy WHERE idtipotarjeta IS NULL AND idconceptopago = 3")
                    ->fetchObject()->sum;
            $tarjetas = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM movimientohoy WHERE idtipotarjeta IS NOT NULL AND idconceptopago = 3")
                    ->fetchObject()->sum;
            $saldoinicial = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(total) FROM movimientohoy WHERE idconceptopago = 1")
                    ->fetchObject()->sum;
            $ingresos = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(total) FROM movimientohoy mh WHERE idtipodocumento = 9 AND idconceptopago NOT IN (1,3)")
                    ->fetchObject()->sum;
            $egresos = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(total) FROM movimientohoy mh WHERE idtipodocumento = 10")
                    ->fetchObject()->sum;
            ?>
        <div class="row">
            <div class="col s12"><h4 class="center blue lighten-4 blue-text text-darken-4">DETALLE DE CIERRE</h4></div>
        </div>
        <div class="row">
            <div class="col s12 m6 l6">
                <h5 class="center blue lighten-4 blue-text text-darken-4">VENTAS</h5>
                <table class="bordered striped highlight" style="font-size: 1.5rem;">
                    <tbody>
                        <tr>
                            <td class="center">EFECTIVO</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($efectivo, 2);?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('EFECTIVO');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">TARJETAS</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($tarjetas,2);?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('TARJETAS');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">TOTAL</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($efectivo+$tarjetas,2);?></td>
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
                            <td class="center">SALDO INICIAL</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($saldoinicial, 2);?></td>
                            <td class="center">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="center">INGRESOS</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($ingresos, 2);?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('INGRESOS');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                        <tr>
                            <td class="center">GASTOS</td>
                            <td class="center" style="font-weight: 900"><?php echo number_format($egresos, 2);?></td>
                            <td class="center"><button type="button" onclick="modalDetalleCerrarCaja('GASTOS');" class="btn-floating lime accent-1"><i class="material-icons lime-text text-darken-4">visibility</i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="modalDetalleCerrarCaja" class="modal modalDetalleCerrarCaja" style="min-width: 90%;">
                <div class="col s12" style="padding: 0px;">
                    <h4 style="font-weight: 500;" class="indigo-text text-darken-4" id="tituloModalDetalleCerrar">
                        LISTA DE GASTOS
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
                    <div class="row">
                        <table>
                            <thead>
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
            <div class="col s12"><h4 class="center blue lighten-4 blue-text text-darken-4">DATOS DE CIERRE</h4></div>
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
        <div class="row">
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <?php if($_GET["accion"]=="APERTURA") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=9",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); elseif($_GET["accion"]=="CIERRE" OR $_GET["accion"]=="ASIGNAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=10",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4",$value["descripcion"],0,'',$objMantenimiento,"generaNumero(this.value)");?>
                    <label>Tipo de Documento</label>
                </div>
            </div>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
        <div class="row">
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
            <div class="col s12 m6 l6">
                <div class="input-field inline">
            <?php 
            if($_GET["accion"]=="NUEVO") {?>
            <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "" maxlength="11" size="15" onKeyPress="return validarsolonumerosdecimales(event,this.value);" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>>
            <label for="txtTotal">Monto (S/.)</label>
            <?php }else{?><input name="txtMontoSoles" type="text" id="txtMontoSoles" 
				value="<?php $read = "";
				if($_GET['accion']=='APERTURA'){
				$rst = $objMantenimiento->montodeaperturasoles();
				echo $rst;	
				}elseif($_GET['accion']=='CIERRE'){
				$rst = $objMantenimiento->montodecierresoles($_SESSION['R_FechaProceso']);$read = "readonly";
				echo $rst;	
				}			
				?>" size="15" maxlength="11" <?php 
				$num = $objMantenimiento->existenciamov();
				if(($_GET['accion']=='APERTURA' && $num==0) or $_GET['accion']=='ASIGNAR'){
				}else{
				//echo "readonly=''";
				}
				echo " $read ";?> onKeyPress='return validarsolonumerosdecimales(event,this.value);' ><label for="monto" class="active">Monto (S/.)</label>
          <?php }?>
                </div>
            </div>
        <?php if($_GET['accion']!='CIERRE'){?></div><?php }else{?>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input type="number" id="txtReal" name="txtReal" class="autocomplete" step="0.01" min="0">
                    <label for="txtReal" class="active">Cantidad Real</label>
                </div>
            </div>
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
            <div class="col s12">
                <div class="input-field inline">
                    <textarea id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" class="materialize-textarea"></textarea>
                    <label for="txt<?php echo $value["descripcion"];?>"><?php echo $value["comentario"];?></label>
                </div>
            </div>
        </div>
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
</body>
</html>