<?php
require('../modelo/clsMovCaja.php');
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
<?php
require("fun.php");
$objFiltro = new clsMovCaja($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
if($_SESSION['R_IdCaja']!=0 || !isset($_SESSION['R_IdCaja'])) $_SESSION['R_IdCaja']=0;
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
/*function buscar(){
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	<?php if($id_clase==48){?>//Flujos de caja
	<?php if($_SESSION['R_IdPerfil']!=4){?>
	vIdCaja=document.getElementById('cboIdCaja').value;
	<?php }else{?>
	vIdCaja='<?php echo $_SESSION['R_IdCaja'];?>';
	<?php }?>
	vValor = "'"+vOrder + "'," + vBy + ", 0, 4, '" + document.getElementById("txtBuscar").value + "','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "','FC'," + vIdCaja + ",0," + document.getElementById("cboIdTipoDocumento").value + "," + document.getElementById("cboConceptoPago").value + ",'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtComentario").value + "','" + document.getElementById("txtCajero").value + "'";
	vTitulo='Flujos de Caja';
	vorigen='FC';
	<?php }else{?>
	vValor = "'"+vOrder + "'," + vBy + ", 0, 4, '" + document.getElementById("txtBuscar").value + "','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "','CC',0,0," + document.getElementById("cboIdTipoDocumento").value + "," + document.getElementById("cboConceptoPago").value + ",'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtComentario").value + "','" + document.getElementById("txtCajero").value + "'";
	vTitulo='Caja Chica';
	vorigen='CC';
	<?php }?>

	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=MovCaja&id_clase=<?php echo $id_clase;?>&funcion=Reporte&filtro=' + vValor + '&imprimir=SI&tiporeporte=CajaAgrupadoApertura&titulo=de '+vTitulo+' por Jornadas&origen='+vorigen+'&fechainicio=' + document.getElementById("txtFechaInicio").value + '&fechafin=' + document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
}
*/

function reporte(){
    window.open('vista/reportes/ReporteLiquidacionGeneral.php?fechainicio='+document.getElementById("txtFechaInicio").value+'&fechafin='+document.getElementById("txtFechaFin").value+"&idsucursal="+document.getElementById("cboSucursal").value+"&sucursal="+document.getElementById("cboSucursal").options[document.getElementById("cboSucursal").selectedIndex].text+'&idbarra='+document.getElementById('cboBarra').value,'_blank');
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

//buscar();
</script>
</head>
<body>
<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
	                echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaInicio" class="active">Fecha Mov. Inicial</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    	$fecha = explode('/',$_SESSION['R_FechaProceso']);
	                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaFin" class="active">Fecha Mov. Final</label>
                </div>
            </div>
            <div class="col s12 m6 l7" hidden="">
                <div class="input-field inline">
                    <?php genera_cboGeneralSQL("select idsucursal,razonsocial from sucursal where idempresa=".$_SESSION['R_IdEmpresa'],'Sucursal',$_SESSION['R_IdSucursal'],'',$objFiltro);?>
                    <label>Sucursal</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                	<select id="cboBarra" name="cboBarra">
                		<option value="1">Principal</option>
                		<option value="2">Escenario</option>
                		<option value="3">2do Piso</option>
                	</select>
                    <label>Barra</label>
                </div>
            </div>
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
            <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;reporte();"><i class="material-icons black-text">search</i></button>
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