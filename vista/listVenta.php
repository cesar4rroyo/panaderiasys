<?php
require('../modelo/clsMovimiento.php');
require("../modelo/clsSalon.php");
$id_clase = $_GET["id_clase"];
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
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	//if(!valFecha(document.getElementById('txtFechaInicio').value) && document.getElementById('txtFechaInicio').value!=''){alert('El formato de fecha debe ser: dd/mm/aaaa');document.getElementById('txtFechaInicio').focus();return false;}
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	*/
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, 2, '" + document.getElementById("txtBuscar").value + "','','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaInicio").value + "',0,0," + document.getElementById("cboIdTipoDocumento").value + ",'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtResponsable").value + "','" + document.getElementById("txtComentario").value + "','', 0,'','historico'";
	setRun('vista/listGrilla2','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=de Documentos de Venta&origen=Venta&fechainicio=' + document.getElementById("txtFechaInicio").value + '&fechafin=' + document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
}

function ordenar(id){
	document.getElementById("order").value = id;
	if(document.getElementById("by").value=="1"){
		document.getElementById("by").value = "0";	
	}else{
		document.getElementById("by").value = "1";
	}
	buscar();
}

function actualizar(id){
	setRun('vista/mantVenta','&accion=ACTUALIZAR&clase=Movimiento&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contVenta.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}

function anular(id){
	if(!confirm('Está seguro que desea anular el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contVenta.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ANULAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}

function verComprobante(id, idtipodocumento){//alert(idtipodocumento);
	if(idtipodocumento==4){//boleta
		setRun('vista/frmComprobanteB','&idventa=' + id,'frame','carga','imgloading');
	}else{
		if(idtipodocumento==5){//factura
			setRun('vista/frmComprobanteF','&idventa=' + id,'frame','carga','imgloading');
		}else{//ticket
			//alert("Upps. estamos trabajando.");
			setRun('vista/frmComprobanteT','&idventa=' + id,'frame','carga','imgloading');
		}
	}
}
function genera_cboCaja(idsalon,seleccionado,disabled){
		var recipiente = document.getElementById('divcboCaja');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setAsync(false);
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboCaja");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("disabled", disabled);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text+"<label>Caja</label>";
                        $("select").material_select();
		};
		g_ajaxPagina.request();
}
function modalRetencion(idmov){
    $('#modalRetencion').openModal({
        dismissible: true, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        in_duration: 300, // Transition in duration
        out_duration: 200, // Transition out duration
        starting_top: '4%', // Starting top style attribute
        ending_top: '10%', // Ending top style attribute
        ready: function(modal, trigger) {
            $("#frmRetencion").html(divCargando);
            $("#btnAceptarModalRetencion").prop("disabled",true);
            $.ajax({
                type: "POST",
                url: "vista/ajaxVenta.php?id_clase=5",        
                data:"accion=buscarRetencionMovimiento&idmov="+idmov,
                success: function(a) {
                    $("#frmRetencion").html(a);
                    $("#monto").focus();
                    if($("#Retencionidretencion").val()>0){
                        $("#btnAceptarModalRetencion").hide();
                    }else{
                        $("#btnAceptarModalRetencion").show();
                        $("#btnAceptarModalRetencion").prop("disabled",false);
                    }
                }
            });
        },
        complete: function() {} // Callback for Modal close
    });
}
function aceptarModalRetencion(){
    $("#btnAceptarModalRetencion").hide();
    if(isNaN($("#monto").val()) || $("#monto").val().trim().length==0 || Number($("#monto").val().trim())<=0
            || $("#numero").val().trim().length==0 || $("#fecha").val()==""
            ){
        alerta("UNO O MAS CAMPOS NO SE HAN LLENADO CORRECTAMENTE");
        $("#btnAceptarModalRetencion").show();
    }else{
        $.ajax({
            type: "POST",
            url: "vista/ajaxVenta.php?id_clase=5",        
            data:"accion=guardarRetencionMovimiento&"+$("#frmRetencion").serialize(),
            success: function(a) {
                alerta(a);
                $('#modalRetencion').closeModal();
                setTimeout(function (){modalRetencion($("#Retencionidmovimiento").val());},1000);
            }
        });
    }
    
}

function editarModoPago(idmovimiento){
    setRun('vista/frmEditarModoPago','ajax=true&accion=ACTUALIZAR&clase=Movimiento&id_clase=44&Id=' + idmovimiento,'frame','carga','imgloading');
}

function enviarLote(){
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setAsync(false);
    g_ajaxPagina.setURL("vista/ajaxVenta.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "listaFacturacion");
    g_ajaxPagina.response = function(text){
        lista = text.split('@');
        for(var c=0;c<lista.length;c++){
            var datol = lista[c].split("|");
            //console.log(datol[0]+"-"+datol[1]);
            declarar2(datol[0],datol[1]);
        }
    };
    g_ajaxPagina.request();
}

function declarar2(idventa,idtipodocumento){
    if(idtipodocumento==4){
        var vaccion='enviarBoleta';
    }else{
        var vaccion='enviarFactura';
    }
    g_ajaxPagina1 = new AW.HTTP.Request;
    g_ajaxPagina1.setURL("controlador/contComprobante.php");
    g_ajaxPagina1.setRequestMethod("GET");
    g_ajaxPagina1.setParameter("funcion", vaccion);
    g_ajaxPagina1.setParameter("idventa",idventa);
    g_ajaxPagina1.response = function(text){
        console.log(text);
    };
    g_ajaxPagina1.request();
}

function cambiarComprobante(idventa){
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setAsync(false);
    g_ajaxPagina.setURL("vista/ajaxVenta.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "cambiarComprobante");
    g_ajaxPagina.setParameter("idventa", idventa);
    g_ajaxPagina.response = function(text){
        declarar2(idventa,4);
        buscar();
    };
    g_ajaxPagina.request();
}

<?php 
if(isset($_SESSION['R_IdSalon'])) $idsalon=$_SESSION['R_IdSalon']; else $idsalon=0;
if(isset($_SESSION['R_IdCaja'])) $idcaja=$_SESSION['R_IdCaja']; else $idcaja=0;
?>
genera_cboCaja(<?php echo $idsalon;?>,<?php echo $idcaja;?>,'');
buscar();
</script>
</head>
<body>
<?php
require("fun.php");
$objFiltro = new clsMovimiento($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
    <div class="Botones" id="opciones">
        <div class="row">
<?php
$rstMovimiento = $objFiltro->obtenerTabla();
if(is_string($rstMovimiento)){
	echo "<td colspan=100>Error al Obtener datos de Perfil</td></tr><tr><td colspan=100>".$rstMovimiento."</td>";
}else{
	$datoMovimiento = $rstMovimiento->fetchObject();
}
$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Perfil</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantVenta', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"--> 
                <div class="col s12 m12 l12 center">
            <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                    type="button" data-position="bottom" data-delay="50" 
                    data-tooltip="<?php echo umill($operacion['comentario']);?>" 
                    onClick="javascript:setRun('vista/mantVenta', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
        </div>
		<?php
		}
	}
}
?>
            

		</div>
    </div>
    <!--BOTONERA FIN-->
<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar">
                    <label for="txtBuscar">Numero</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <?php echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2",'IdTipoDocumento',0,'',$objFiltro,"generaNumero(this.value)","TODOS");?>
                    <label>Tipo Documento</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input id="txtPersona" name="txtPersona" type="text">
                    <label for="txtPersona">Cliente</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input id="txtResponsable" name="txtResponsable" type="text">
                    <label for="txtResponsable">Responsable</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input id="txtComentario" name="txtComentario" type="text">
                    <label for="txtComentario">Comentario</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
	                echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaInicio" class="active">Fecha Jornada</label>
                </div>
            </div>
            <div class="col s12 m3 l2" hidden="">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    	$fecha = explode('/',$_SESSION['R_FechaProceso']);
	                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaFin" class="active">Fecha Fin</label>
                </div>
            </div>
            <div class="col s12 m6 l1" hidden="">
                <div class="input-field inline">
                    <?php echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$idsalon,'',$objSalon,'genera_cboCaja(this.value,0,"")');?>
                    <label>Salon</label>
                </div>
            </div>
            <div class="col s12 m6 l1" style="display:none">
                <div class="input-field inline" id="divcboCaja">
                </div>
            </div>
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
            <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
                </div>
                <!--div class="input-field inline">
                    <button id="cmdBuscar2" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;enviarLote();"><i class="material-icons black-text">search</i></button-->
                </div>
            </div>
        </div>
    </div>
    <!--FILTROS FINAL-->
    <div class="row">
        <div id="divdiagramaMesa" class="col s12">
                <div class="row">
                    <div class="col s12">
                        <div id="cargagrilla"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div id="grilla"></div>
                    </div>
                </div>
        </div>
    </div>
</div>
<div class="modalNuevoPersona">
    <div id="modalRetencion" class="modal modal-fixed-footer orange lighten-3">
        <div class="modal-content">
          <div class="white" style="padding: 10px;border-radius: 10px;">
              <h5>RETENCION DEL MOVIMIENTO</h5>
            <form id="frmRetencion" method="POST" action="">
                <input type="hidden" id="idretencion" value="">
                <input type="hidden" id="idmovimiento" value="">
                <div class="row">
                  <div class="col s12">
                      <div class="input-field inline">
                        <input id="monto" type="number" step="0.01" min="0.01" value="">
                        <label for="monto">Monto de Retencion</label>
                      </div>
                  </div>
                  <div class="col s12">
                      <div class="input-field inline">
                        <input id="numero" type="text" value="">
                        <label for="numero">Numero de documento</label>
                      </div>
                  </div>
                  <div class="col s12">
                      <div class="input-field inline">
                        <input id="fecha" type="date" value="">
                        <label class="active">Fecha de Retencion</label>
                      </div>
                  </div>
                </div>
            </form>
          </div>
        </div>
        <div class="modal-footer amber lighten-3">
            <button id="btnAceptarModalRetencion" type="button" onclick="aceptarModalRetencion()" class="waves-effect waves-green btn light-green accent-1 black-text">Guardar<i class="material-icons right">save</i></button>
        </div>
    </div>
</div>
</body>
</html>