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
	$order="1";
}
$by = $_GET["by"];
if(!$by){
	$by="1";
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
require("fun.php");
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	*/
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, 2, '" + document.getElementById("txtBuscar").value + "','','" + document.getElementById("txtFechaInicio").value + " " + document.getElementById("txtHoraInicio").value + "','" + document.getElementById("txtFechaFin").value + " " + document.getElementById("txtHoraFin").value + "',0,0," + document.getElementById("cboIdTipoDocumento").value + ",'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtResponsable").value + "','" + document.getElementById("txtComentario").value + "','',"+document.getElementById('cboCaja').value+",'"+document.getElementById('txtEstado').value+"','historico'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=de Documentos de Venta&origen=Venta&fechainicio='+document.getElementById("txtFechaInicio").value+'&fechafin='+document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
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

buscar();
//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFechaInicio", "%d/%m/%Y %H:%M:%S");
cal.manageFields("btnCalendar2", "txtFechaFin", "%d/%m/%Y %H:%M:%S");
//"%Y-%m-%d %H:%M:%S"
//]]>

function verDetalle(){
    window.open("vista/reportes/ReporteDetalleVenta.php?idcategoria="+document.getElementById("cboCategoria").value+"&fechainicio="+document.getElementById("txtFechaInicio").value + " " + document.getElementById("txtHoraInicio").value+"&fechafin="+document.getElementById("txtFechaFin").value + " " + document.getElementById("txtHoraFin").value+"&idcaja="+document.getElementById('cboCaja').value,"_blank");
}
function verDetalleExcel(){
    window.open("vista/reportes/ReporteDetalleVentaExcel.php?idcategoria="+document.getElementById("cboCategoria").value+"&fechainicio="+document.getElementById("txtFechaInicio").value + " " + document.getElementById("txtHoraInicio").value+"&fechafin="+document.getElementById("txtFechaFin").value + " " + document.getElementById("txtHoraFin").value+"&idcaja="+document.getElementById('cboCaja').value,"_blank");
}
function verDetalleGeneral(){
    window.open("vista/reportes/ReporteDetalleGeneralExcel.php?idcategoria="+document.getElementById("cboCategoria").value+"&fechainicio="+document.getElementById("txtFechaInicio").value + " " + document.getElementById("txtHoraInicio").value+"&fechafin="+document.getElementById("txtFechaFin").value + " " + document.getElementById("txtHoraFin").value+"&idcaja="+document.getElementById('cboCaja').value,"_blank");
}
function verVentaPago(){
    window.open("vista/reportes/ReporteVentaFormaPagoExcel.php?fechainicio="+document.getElementById("txtFechaInicio").value+ " " + document.getElementById("txtHoraInicio").value+"&fechafin="+document.getElementById("txtFechaFin").value+ " " + document.getElementById("txtHoraFin").value,"_blank");
}
function verVentaDiaria(){
    window.open("vista/reportes/ReporteVentaDiariaExcel.php?fechainicio="+document.getElementById("txtFechaInicio").value+ " " + document.getElementById("txtHoraInicio").value+"&fechafin="+document.getElementById("txtFechaFin").value+ " " + document.getElementById("txtHoraFin").value,"_blank");
}
function verDetalle2(){
    window.open("vista/reportes/ReporteDetalleVenta2.php?idcategoria="+document.getElementById("cboCategoria").value+"&fechainicio="+document.getElementById("txtFechaInicio").value + " " + document.getElementById("txtHoraInicio").value+"&fechafin="+document.getElementById("txtFechaFin").value + " " + document.getElementById("txtHoraFin").value+"&idcaja="+document.getElementById('cboCaja').value,"_blank");
}
</script>
</head>
<body>
<?php
$objFiltro = new clsMovimiento($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<?php

$rstMovimiento = $objFiltro->obtenerTabla();
if(is_string($rstMovimiento)){
	echo "<td colspan=100>Error al Obtener datos de Perfil</td></tr><tr><td colspan=100>".$rstMovimiento."</td>";
}else{
	$datoMovimiento = $rstMovimiento->fetchObject();
}
?>

<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar">
                    <label for="txtBuscar">N&uacute;mero</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <?php echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2",'IdTipoDocumento',0,'',$objFiltro,"generaNumero(this.value)","TODOS");?>
                    <label class="black-text">Tipo documento</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input id="txtPersona" name="txtPersona" type="text">
                    <label for="txtPersona">Cliente</label>
                </div>
            </div>
            <div class="col s12 m3 l1" hidden="">
                <div class="input-field inline">
                    <input id="txtResponsable" name="txtResponsable" type="text">
                    <label for="txtResponsable">Responsable</label>
                </div>
            </div>
            <div class="col s12 m3 l1" hidden="">
                <div class="input-field inline">
                    <input id="txtComentario" name="txtComentario" type="text">
                    <label for="txtComentario">Comentario</label>
                </div>
            </div>
            <div class="col s12 m2 l1">
                <div class="input-field inline">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaInicio" class="active">Fecha Inicio</label>
                    <input type="time" value="00:00" id="txtHoraInicio" />
                    <label for="txtFechaInicio" class="active">Fecha Inicio</label>
                </div>
            </div>
            <div class="col s12 m2 l1">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaFin" class="active">Fecha Fin</label>
                    <input type="time" value="23:59" id="txtHoraFin" />
                    <label for="txtFechaFin" class="active">Fecha Fin</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <select id="cboCategoria" name="cboCategoria">
                    <option value="">Todos</option>
                        <?php
                        $rs = $objFiltro->obtenerDataSQL("select * from categoria where idsucursal=".$_SESSION["R_IdSucursal"]." and estado='N' order by descripcion asc");
                        while($dat=$rs->fetchObject()){
                            echo "<option value='$dat->idcategoria'>$dat->descripcion</option>";
                        }
                        ?>
                    </select>
                    <label for="cboCategoria">Categoria</label>
                    <input type="hidden" id="txtEstado" name="txtEstado" value="N" />
                    <input id="chkLumbra" type="checkbox" onchange="if(this.checked){$('#txtEstado').val('I');}else{$('#txtLumbra').val('N');}" />
                    <label for="chkLumbra">Anulado</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <select id="cboCaja" name="cboCaja">
                        <option value="0">Todos</option>
                        <option value="1">Caja 1</option>
                        <option value="2">Caja 2</option>
                        <option value="3">Caja 3</option>
                        <option value="4">Caja 4</option>
                        <option value="5">Caja 5</option>
                        <option value="5">Caja 6</option>
                        <option value="5">Boleteria</option>
                    </select>
                    <label for="cboCaja">Caja</label>
                </div>
            </div>
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
            <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();" title="Buscar"><i class="material-icons black-text">search</i></button>
                </div>
            </div>
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdDetalle" type="button" class="btn yellow lighten-2" onClick="javascript:verDetalleGeneral();" title="Detalle General"><i class="material-icons black-text">description</i></button>
                </div>
            </div>
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdDetalle" type="button" class="btn blue lighten-2" onClick="javascript:verDetalle();" title="Detalle"><i class="material-icons black-text">open_in_browser</i></button>
                </div>
            </div>
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdDetalle2" type="button" class="btn light-green lighten-2" onClick="javascript:verDetalleExcel();" title="Detalle Excel"><i class="material-icons black-text">assignment</i></button>
                </div>
            </div>
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdDetalle2" type="button" class="btn light-red lighten-2" onClick="javascript:verVentaPago();" title="Venta Forma Pago"><i class="material-icons black-text">attach_money</i></button>
                </div>
            </div>
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdDetalle2" type="button" class="btn red " onClick="javascript:verVentaDiaria();" title="Venta Diaria"><i class="material-icons black-text">description</i></button>
                </div>
            </div>
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdDetalle" type="button" class="btn blue lighten-2" onClick="javascript:verDetalle2();" title="Detalle Venta"><i class="material-icons black-text">assignment</i></button>
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
</body>
</HTML>