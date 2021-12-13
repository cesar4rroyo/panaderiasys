<?php
require('../modelo/clsPersona.php');
require ('fun.php');
$id_clase = $_GET["id_clase"];
$nro_reg = $_SESSION["R_NroFilaMostrar"];
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
	$nro_hoja = 1;
}
$order = $_GET["order"];
if(!$order){
	$order="fechanac";
}
$by = $_GET["by"];
if(!$by){
	$by="2";
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
	vValor = "'"+vOrder + "'," + vBy + ",0, 0, 0, '" + document.getElementById("txtBuscar_Apellido_Nombre").value + "','" + document.getElementById("txtBuscar_NroDoc").value + "','" + document.getElementById("cbosexo").value + "','" + document.getElementById("cbocompartido").value + "',3,0,'" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "'";
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Persona&id_clase=<?php echo $id_clase;?>&funcion=ReporteCumpleanos&filtro=' + vValor + '&imprimir=SI&tiporeporte=Dinamico&titulo=Cumpleaños Clientes&datografico=idpersona&ocultarcampos=direccion-email-telefonofijo-telefonomovil-compartido', 'grilla', 'grilla', 'img03');
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
</script>
</head>
<body>
<?php
$objFiltro = new clsPersona($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input id="txtBuscar_NroDoc" name="txtBuscar_NroDoc" type="text">
                    <label for="txtBuscar_NroDoc">Nro de Doc</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
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
            <div class="col s12 m6 l2">
                <div class="input-field inline" id="inptFechaInicio">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaInicio" class="active">Fecha Inicio</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
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
