<?php
require('../modelo/clsMovimiento.php');
$id_clase = $_GET["id_clase"];
$nro_reg = 0;
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
	$nro_hoja = 1;
}
$order = $_GET["order"];
if(!$order){
	$order="m.idmovimiento";
}
$by = $_GET["by"];
if(!$by){
	$by="0";
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<HTML>
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
	vValor = "'"+vOrder + "'," + vBy + ", 0, 3, '" + document.getElementById("txtBuscar").value + "','','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "',0,0," + document.getElementById("cboIdTipoDocumento").value + ",'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtResponsable").value + "','" + document.getElementById("txtComentario").value + "','',0,'','historico'";
	setRun('vista/listGrilla2','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=de Documentos de Compra&origen=Compra&fechainicio=' + document.getElementById("txtFechaInicio").value + '&fechafin=' + document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
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
	setRun('vista/mantAlmacen','&accion=ACTUALIZAR&clase=Movimiento&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contAlmacen.php?ajax=true");
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

function cargarinicial(){
    g_ajaxGrabar.setURL("controlador/contAlmacen.php?ajax=true");
    g_ajaxGrabar.setRequestMethod("POST");
    g_ajaxGrabar.setParameter("accion", "CARGARINICIAL");
    g_ajaxGrabar.response = function(text){
        loading(false, "loading");
        buscar();
        console.log(text);
    };
    g_ajaxGrabar.request();
    
    loading(true, "loading", "grilla", "linea.gif",true);
}

function verOperacionesPerfil(id){
	setRun('vista/listRelacionOperacionPerfil','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&id_cliente=<?php echo $id_cliente;?>&clase=RelacionOperacionPerfil&id_perfil=' + id,'frame', 'frame', 'imgloading03');
}
function verPermisoUsuario(id){
	setRun('vista/listPermisoUsuario','&nro_reg=<?php echo $nro_reg;?>&id_cliente=<?php echo $id_cliente;?>&nro_hoja=1&clase=PermisoUsuario&id_perfil=' + id,'frame', 'frame', 'imgloading03');
}
function verDetalle(id){
	setRun('vista/listDetalleAlmacen','id_clase=41&nro_reg=<?php echo $nro_reg;?>&id_cliente=<?php echo $id_cliente;?>&nro_hoja=1&clase=movimiento&idalmacen=' + id,'frame','carga','imgloading');
}
function imprimir(id,idsucursal){
    g_ajaxGrabar.setURL("visata/ajaxPedido.php?ajax=true");
    g_ajaxGrabar.setRequestMethod("POST");
    g_ajaxGrabar.setParameter("accion", "imprimirAlmacen");
    g_ajaxGrabar.setParameter("id", id);
    g_ajaxGrabar.setParameter("idsucursal", idsucursal);
    g_ajaxGrabar.response = function(text){
        loading(false, "loading");
        buscar();
        console.log(text);
    };
    g_ajaxGrabar.request();
    
    loading(true, "loading", "grilla", "linea.gif",true);
}
function modalImportar(){
    $('#modalImportar').openModal({
        dismissible: true, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        in_duration: 300, // Transition in duration
        out_duration: 200, // Transition out duration
        starting_top: '4%', // Starting top style attribute
        ending_top: '10%', // Ending top style attribute
        ready: function(modal, trigger) {
        }
    });
}

function importarRequerimiento(formData,vidproducto,files,vidusucrsal){
    var formData = new FormData(document.getElementById("frmCarga"));
    var files = $('#txtFile')[0].files[0];
    formData.append('file',files);
    formData.append('accion','SUBIRREQUERIMIENTO');
    $.ajax({
        url: 'controlador/contAlmacen.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function(){
            alert("Enviando informacion...");
        },
        success: function(response) {
            console.log(response);
            alert("Cargado correctamente");
            buscar();
        }
    });
}
function detalleExcel(){
    window.open('vista/reportes/ReporteDetalleAlmacen.php?fechainicio='+$("#txtFechaInicio").val()+"&fechafin="+$("#txtFechaFin").val()+"&idtipodocumento="+$("#cboIdTipoDocumento").val()+"&persona="+$("#txtPersona").val(),'_blank');
}
function detalleExcel2(){
    window.open('vista/reportes/ReporteDiarioAlmacen.php?fechainicio='+$("#txtFechaInicio").val()+"&fechafin="+$("#txtFechaFin").val()+"&idtipodocumento="+$("#cboIdTipoDocumento").val()+"&persona="+$("#txtPersona").val(),'_blank');
}

buscar();
</script>
</head>
<body>
<?php
require("fun.php");
$objFiltro = new clsMovimiento($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<!--BOTONERA INICIO-->
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
                <div class="col s12 m12 l12 center">
            <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                    type="button" data-position="bottom" data-delay="50" 
                    data-tooltip="<?php echo umill($operacion['comentario']);?>" 
                    onClick="javascript:setRun('vista/mantAlmacen', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
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
<form id="frmCarga"></form>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar">
                    <label for="txtBuscar">Buscar</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <?php echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=3",'IdTipoDocumento',0,'',$objFiltro,"generaNumero(this.value)","TODOS");?>
                    <label>Tipo Documento</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input id="txtPersona" name="txtPersona" type="text">
                    <label for="txtPersona">Proveedor</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtResponsable" name="txtResponsable" type="text">
                    <label for="txtResponsable">Responsable</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
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
                    <label for="txtFechaInicio" class="active">Fecha Inicio</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaFin" class="active">Fecha Fin</label>
                </div>
            </div>
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
            <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            <div class="col s12 m6 l2 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
                    <button title="Importar Req." id="cmdImportar" type="button" class="btn green lighten-2" onClick="javascript:modalImportar();"><i class="material-icons black-text">backup</i></button>
                    <button title="Detalle Doc. Almacen" id="cmdDetalle" type="button" class="btn blue lighten-2" onClick="javascript:detalleExcel();"><i class="material-icons black-text">assignment</i></button>
                    <button title="Diario Doc. Almacen" id="cmdDetalle" type="button" class="btn red lighten-2" onClick="javascript:detalleExcel2();"><i class="material-icons black-text">assignment</i></button>
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
<div id="modalImportar" style="height: 200px" class="modal modal-fixed-footer orange lighten-3">
    <div class="modal-content">
      <div class="white" style="padding: 10px;border-radius: 10px;">
        <div class="row">
          <div class="col s12">
            <input type="file" name="txtFile" id="txtFile">
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer amber lighten-3">
        <a href="#!" class="left modal-action modal-close btn red accent-1 black-text">Cerrar<i class="material-icons right">clear</i></a>
        <a href="#!" onclick="importarRequerimiento();" class="modal-action modal-close btn light-green accent-1 black-text">Aceptar<i class="material-icons right">check</i></a>
    </div>
</div>
</body>
</HTML>