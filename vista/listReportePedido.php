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
    if(document.getElementById("cboSituacion").value=="A"){
        estado='A';
        situacion='';
    }else{
        if(document.getElementById("cboSituacion").value=="P"){
            estado='N';
            situacion='';
        }else{
            estado='T';
            situacion='';   
        }
    }
	vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','"+situacion+"','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "',0,0,0,'','" + document.getElementById("txtResponsable").value + "','','',0,'"+estado+"'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=de Documentos de Venta&origen=Venta&historico=SI', 'grilla', 'grilla', 'img03');
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
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar">
                    <label for="txtBuscar">N&uacute;mero</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input id="txtResponsable" type="text" name="txtResponsable">
                    <label for="txtResponsable">Responsable</label>
                </div>
            </div>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
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
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <select id="cboSituacion" name="cboSituacion">
                        <option value="">Todos...</option>
                        <option value="P">Normal</option>
                        <option value="A">Anulados</option>
                    </select>
                    <label class="black-text">Situacion</label>
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
