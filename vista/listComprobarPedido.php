<?php
require('../modelo/clsProducto.php');
require("../modelo/clsSalon.php");
require ('fun.php');
$id_clase = $_GET["id_clase"];
$nro_reg = $_SESSION["R_NroFilaMostrar"];
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
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = $_SESSION['R_IdEmpresa'];
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
$resta=(strtotime('now') - strtotime(date("Y-m-d")))/(60);
//print_r($resta);
if($resta>30){
    $fechainicio=date("Y-m-d");
    $fechafin=date("Y-m-d",strtotime('now',strtotime("+1 day")));
}else{
    $fechainicio=date("Y-m-d",strtotime('now',strtotime("-1 day")));
    $fechafin=date("Y-m-d",strtotime('now'));
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
vcomida = false;
function buscar(){
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;    
    var formData = $("#frmBusqueda").serialize();
    setRun('vista/listGrillaComprobarPedido',formData, 'grilla', 'grilla', 'img03');
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
function modalDetallePedido(num,id,idventa){
    $('#modalDetallePedido').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      ready: function(modal, trigger) {
          $("#hTitulo").html("Detalle del Pedido "+num);
          $("#txtId").val(id);
          $("#txtIdVenta").val(idventa);
          detalleVenta();
      },
      complete: function() {} // Callback for Modal close
    });
}

function detalleVenta(){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("vista/ajaxVenta.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "detalleVenta");
    g_ajaxPagina3.setParameter("id",$("#txtIdVenta").val());
    g_ajaxPagina3.response = function(text){
        $("#divDetallePedido").html(text);
    };
    g_ajaxPagina3.request();
}

function confirmar(){
    var g_ajaxPagina4 = new AW.HTTP.Request;
    g_ajaxPagina4.setURL("vista/ajaxVenta.php");
    g_ajaxPagina4.setRequestMethod("POST");
    g_ajaxPagina4.setParameter("accion", "confirmarPedido");
    g_ajaxPagina4.setParameter("id",document.getElementById("txtId").value);
    g_ajaxPagina4.setParameter("idventa",document.getElementById("txtIdVenta").value);
    g_ajaxPagina4.response = function(text){
        alert(text);
        buscar();
    };
    g_ajaxPagina4.request();
}
function confirmar2(id,idventa){
    var g_ajaxPagina4 = new AW.HTTP.Request;
    g_ajaxPagina4.setURL("vista/ajaxVenta.php");
    g_ajaxPagina4.setRequestMethod("POST");
    g_ajaxPagina4.setParameter("accion", "confirmarPedido2");
    g_ajaxPagina4.setParameter("id",id);
    g_ajaxPagina4.setParameter("idventa",idventa);
    g_ajaxPagina4.response = function(text){
        alert(text);
        buscar();
    };
    g_ajaxPagina4.request();
}
buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsProducto($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<?php

$rstTabla = $objFiltro->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}
?>
<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <form id="frmBusqueda">
            <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l2">
                <div class="input-field inline" id="inptFechaInicio">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    echo $fechainicio;?>">
                    <input type="time" value="00:00" id="txtHoraInicio" name="txtHoraInicio"/>
                    <label for="txtFechaInicio" class="active">Desde</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    echo $fechafin;?>">
                    <input type="time" value="23:59" id="txtHoraFin" name="txtHoraFin" />
                    <label for="txtFechaFin" class="active">Hasta</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <select id="cboSituacion" name="cboSituacion">
                        <option value="">Todos...</option>
                        <option value="P" selected="">Pendientes</option>
                        <option value="C">Confirmado</option>
                    </select>
                    <label class="black-text">Situacion</label>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="input-field inline">
                    <input id="txtPedido" type="text" name="txtPedido" type="text">
                    <label for="txtPedido">Pedido</label>
                </div>
            </div>
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
            <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
                </div>
            </div>
        </div>
        </form>
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
<div id="modalDetallePedido" class="modal modal-fixed-footer orange lighten-3" style="height: 50%">
    <div class="modal-content">
      <div class="white" style="padding: 10px;border-radius: 10px;">
            <form id="" method="POST" action="">
                <input type="hidden" name="txtId" id="txtId" />
                <input type="hidden" name="txtIdVenta" id="txtIdVenta" />
                <h4 id="hTitulo">Detalle del Pedido</h4>
                <div class="row">
                    <div class="col s12" id="divDetallePedido">
                    </div>
                </div>
            </form>
      </div>
    </div>
    <div class="modal-footer amber lighten-3">
        <button id="btnAceptarModalUsuario" type="button" onclick="confirmar()" class="waves-effect waves-green btn light-green accent-1 black-text">Validar<i class="material-icons right">check</i></button>
    </div>
</div>
</body>
</HTML>
