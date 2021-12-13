<?php
require('../modelo/clsPersona.php');
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
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	//vValor = "'"+vOrder + "'," + vBy + ",0, 0, 0, '" + document.getElementById("txtBuscar").value + "'";
	vValor = "'"+vOrder + "'," + vBy + ",0, 0, 0, '" + document.getElementById("txtBuscar_Apellido_Nombre").value + "','" + document.getElementById("txtBuscar_NroDoc").value + "','" + document.getElementById("cbosexo").value + "','" + document.getElementById("cbocompartido").value + "',1,'" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "','topsCajero'," + document.getElementById('chkJornada').checked + "," + document.getElementById('cboIdCaja').value;
	setRun('vista/listGrillaSinOperacionTops','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Persona&id_clase=<?php echo $id_clase;?>&funcion=Reporte&filtro=' + vValor + '&imprimir=SI&tiporeporte=Tops&titulo=Cajero', 'grilla', 'grilla', 'img03');
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
function cambiaJornada(check){
	if(check==true){
            $("#divJornadaSi").show();
            $("#divJornadaNo").hide();
            $("#inpFechaJornda").html('<input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php $fecha = explode('/',$_SESSION['R_FechaProceso']);echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>"><label for="txtFechaInicio" class="active">Fecha Jornada</label>');
            /*document.getElementById('txtFechaInicio').value=document.getElementById('txtFechaFin').value;
            document.getElementById('lblFecha').innerHTML='Fecha de Jornada';
            document.getElementById('tdFechaFin').style.display='none';
            document.getElementById('tdSalon').style.display='';
            document.getElementById('tdCaja').style.display='';*/
	}else{
            $("#divJornadaSi").hide();
            $("#divJornadaNo").show();
            $("#inptFechaInicio").html('<input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php $fecha = explode('/',$_SESSION['R_FechaProceso']);echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>"><label for="txtFechaInicio" class="active">Fecha Inicio</label>');
            /*document.getElementById('lblFecha').innerHTML='Fecha Inicio';
            document.getElementById('tdFechaFin').style.display='';
            document.getElementById('tdSalon').style.display='none';
            document.getElementById('tdCaja').style.display='none';*/
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
			recipiente.innerHTML = text+'<label class="black-text">Caja</label>';			
                        $('select').material_select();			
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
$objFiltro = new clsPersona($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input id="txtBuscar_Apellido_Nombre" type="text" name="txtBuscar_Apellido_Nombre">
                    <label for="txtBuscar_Apellido_Nombre">Apellidos y Nombres</label>
                </div>
            </div>
            <div class="col s12 m3 l1">
                <div class="input-field inline">
                    <input id="txtBuscar_NroDoc" name="txtBuscar_NroDoc" type="text">
                    <label for="txtBuscar_NroDoc">Nro de Doc</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <select name="cbosexo" id="cbosexo">
                        <option value="">Todos</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                    <label class="black-text">Sexo</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <select name="cbocompartido" id="cbocompartido">
                        <option value="">Todos</option>
                        <option value="S">Si</option>
                        <option value="N">No</option>
                    </select>
                    <label class="black-text">Tipo/Compartido</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <p>
                        <input type="checkbox" class="filled-in" id="chkJornada" name="chkJornada" onchange="cambiaJornada(this.checked)"/>
                        <label for="chkJornada">Por Jornada</label>
                    </p>
                </div>
            </div>
            <div class="col s12 m6 l4" id="divJornadaNo">
                <div class="col s12 m6 l6">
                    <div class="input-field inline" id="inptFechaInicio">
                        <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                        $fecha = explode('/',$_SESSION['R_FechaProceso']);
                        echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                        <label for="txtFechaInicio" class="active">Fecha Inicio</label>
                    </div>
                </div>
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                        $fecha = explode('/',$_SESSION['R_FechaProceso']);
                        echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                        <label for="txtFechaFin" class="active">Fecha Fin</label>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l4" id="divJornadaSi" hidden="">
                <div class="col s12 m12 l4">
                    <div class="input-field inline" id="inpFechaJornda">
                        <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                        $fecha = explode('/',$_SESSION['R_FechaProceso']);
                        echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                        <label for="txtFechaInicio" class="active">Fecha Inicio</label>
                    </div>
                </div>
                <div class="col s12 m12 l4">
                    <div class="input-field inline">
                        <?php echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$idsalon,'',$objSalon,'genera_cboCaja(this.value,0,"")');?>
                        <label class="black-text">Sal&oacute;n</label>
                    </div>
                </div>
                <div class="col s12 m12 l4">
                    <div class="input-field inline" id="divcboCaja"></div>
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
