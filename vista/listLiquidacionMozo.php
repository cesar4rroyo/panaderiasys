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
    window.open('vista/reportes/ReporteLiquidacionGeneralMozo.php?fechainicio='+document.getElementById("txtFechaInicio").value+'&fechafin='+document.getElementById("txtFechaFin").value+"&idsucursal="+document.getElementById("cboSucursal").value+"&sucursal="+document.getElementById("cboSucursal").options[document.getElementById("cboSucursal").selectedIndex].text,'_blank');
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

//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFechaInicio", "%d/%m/%Y");
cal.manageFields("btnCalendar2", "txtFechaFin", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>
//buscar();
</script>
</head>
<body>
<br>
<div class="titulo"><b>REPORTE DE LIQUIDACION MOZO</b></div>
<div id="menu">
<table><tr><td>
</td><td>
<ul><li style="float:left; display:none"><b>Tipo Cambio: <?php echo number_format($_SESSION['R_TipoCambio'],2);?></b></li></ul>
</td></tr></table>
</div>
<div id="cargamant"></div>
<div id="busqueda">
<table>
<tr><td>&nbsp;</td><td>Fecha Inicio :</td><td>Fecha Fin :</td><td>Sucursal :</td></tr>
<tr><td>Buscar Por:</td><td><input type="text" id="txtFechaInicio" name="txtFechaInicio" value="<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar" type="button" class="boton"><img src="img/date.png" width="16" height="16"> </button></td><td><input type="text" id="txtFechaFin" name="txtFechaFin" value="<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar2" type="button" class="boton"><img src="img/date.png" width="16" height="16"> </button>
    <td><?genera_cboGeneralSQL("select idsucursal,razonsocial from sucursal where idempresa=".$_SESSION['R_IdEmpresa'],'Sucursal',$_SESSION['R_IdSucursal'],'',$objFiltro);?></td>
    </td>
        <td><input id="cmdBuscar" type="button" value="Reporte" onClick="javascript:document.getElementById('nro_hoj').value=1;reporte();">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="cargagrilla"></div>
<div id="grilla"></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td>Caja Chica</td>
</tr></table>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
</body>
</HTML>