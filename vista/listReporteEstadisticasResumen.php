<?php
require('../modelo/clsPersona.php');
$id_clase = $_GET["id_clase"];
$nro_reg = 10;
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
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<html>
<head>
<script>
function buscarTopsMeseros(){
	vValor = "'1','1',0, 0, 0, '','','','',1,'<?php echo $_SESSION['R_FechaProceso'];?>','','topsMesero',1";
	setRun('vista/listGrillaSinOperacionTops','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Persona&id_clase=49&funcion=Reporte&filtro=' + vValor + '&idgrilla=1&titulotabla=TOPS DE MESEROS&solografico=SI&tamanografico=S&accionvermas=vermasTopsMeseros()', 'grillaTopsMesero', 'grillaTopsMesero', 'img03');
}
function buscarTopsCajeros(){
	vValor = "'1','1',0, 0, 0, '','','','',1,'<?php echo $_SESSION['R_FechaProceso'];?>','','topsCajero',1";
	setRun('vista/listGrillaSinOperacionTops','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Persona&id_clase=49&funcion=Reporte&filtro=' + vValor + '&idgrilla=2&titulotabla=TOPS DE CAJEROS&sesiongrafico=datagrafico2&solografico=SI&tamanografico=S&accionvermas=vermasTopsCajeros()', 'grillaTopsCajero', 'grillaTopsCajero', 'img03');
}
function buscarTopsClientes(){
	vValor = "'1','1',0, 0, 0, '','','','',3,'<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2)-1,substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)));?>','<?php echo $_SESSION['R_FechaProceso'];?>','topsCliente'";
	setRun('vista/listGrillaSinOperacionTops','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Persona&id_clase=49&funcion=Reporte&filtro=' + vValor + '&idgrilla=3&titulotabla=TOPS DE CLIENTES&sesiongrafico=datagrafico3&solografico=SI&tamanografico=S&datografico=idpersona&accionvermas=vermasTopsClientes()', 'grillaTopsCliente', 'grillaTopsCliente', 'img03');
}
function buscarTopsProductos(){
	vValor = "'1','1', 0,<?php echo $id_cliente;?>, '','','','<?php echo $_SESSION['R_FechaProceso'];?>','','','','',1";
	
	setRun('vista/listGrillaSinOperacionTops','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Producto&id_clase=11&funcion=Reporte&filtro=' + vValor + '&idgrilla=4&titulotabla=TOPS DE PRODUCTOS&sesiongrafico=datagrafico4&solografico=SI&tamanografico=S&datografico=codigo&accionvermas=vermasTopsProductos()', 'grillaTopsProducto', 'grillaTopsProducto', 'img03');
}
function buscarVentasxMes(){
	vValor = "'1','1', 0, 2, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2),substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)-1));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'M'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReportexMesoxSemana&filtro=' + vValor + '&idgrilla=5&titulotabla=VENTAS X MES&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico5&solografico=SI&tamanografico=S&datografico=mes&accionvermas=vermasVentasxMes()', 'grillaVentasxMes', 'grillaVentasxMes', 'img03');
}
function buscarVentasxSemana(){
	vValor = "'1','1', 0, 2, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2)-2,substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'S'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReportexMesoxSemana&filtro=' + vValor + '&idgrilla=6&titulotabla=VENTAS X SEMANA&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico6&solografico=SI&tamanografico=S&datografico=semanames&accionvermas=vermasVentasxSemana()', 'grillaVentasxSemana', 'grillaVentasxSemana', 'img03');
}
function buscarCumpleanosCliente(){
	vValor = "'FechaNac',2,0, 0, 0, '','','','',3,0,'<?php echo date('d/m',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2),substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)));?>','31/12'";
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Persona&id_clase=23&funcion=ReporteCumpleanos&filtro=' + vValor + '&idgrilla=7&titulotabla=CUMPLEA[N]OS CLIENTES&imprimir=SI&tiporeporte=Dinamico&titulo=Cumpleaños Cliente&accionvermas=vermasCumpleanosCliente()&ocultarcampos=idpersona-direccion-email-telefonofijo-telefonomovil-compartido-tipopersona', 'grillaCumpleanosCliente', 'grillaCumpleanosCliente', 'img03');
}
function buscarComprasxMes(){
	vValor = "'1','1', 0, 1, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2),substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)-1));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'M'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReportexMesoxSemana&filtro=' + vValor + '&idgrilla=8&titulotabla=COMPRAS X MES&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico8&solografico=SI&tamanografico=S&datografico=mes&accionvermas=vermasComprasxMes()', 'grillaComprasxMes', 'grillaComprasxMes', 'img03');
}
function buscarComprasxSemana(){
	vValor = "'1','1', 0, 1, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2)-2,substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'S'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReportexMesoxSemana&filtro=' + vValor + '&idgrilla=9&titulotabla=COMPRAS X SEMANA&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico9&solografico=SI&tamanografico=S&datografico=semanames&accionvermas=vermasComprasxSemana()', 'grillaComprasxSemana', 'grillaComprasxSemana', 'img03');
}
function buscarUtilidadxMes(){
	vValor = "'1','1', 0, 1, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2),substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)-1));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'M'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReporteUtilidadxMesoxSemana&filtro=' + vValor + '&idgrilla=10&titulotabla=UTILIDAD BRUTA X MES&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico10&solografico=SI&tamanografico=S&datografico=mes&accionvermas=vermasUtilidadxMes()', 'grillaUtilidadxMes', 'grillaUtilidadxMes', 'img03');
}
function buscarUtilidadxSemana(){
	vValor = "'1','1', 0, 1, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2)-2,substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'S'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReporteUtilidadxMesoxSemana&filtro=' + vValor + '&idgrilla=11&titulotabla=UTIILIDAD BRUTA X SEMANA&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico11&solografico=SI&tamanografico=S&datografico=semanames&accionvermas=vermasUtilidadxSemana()', 'grillaUtilidadxSemana', 'grillaUtilidadxSemana', 'img03');
}
function buscarTopsProveedores(){
	vValor = "'1','1',0, 0, 0, '','','','',4,'<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2)-1,substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)));?>','<?php echo $_SESSION['R_FechaProceso'];?>','topsProveedor'";
	setRun('vista/listGrillaSinOperacionTops','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Persona&id_clase=49&funcion=Reporte&filtro=' + vValor + '&idgrilla=12&titulotabla=TOPS DE PROVEEDORES&sesiongrafico=datagrafico12&solografico=SI&tamanografico=S&datografico=idpersona&accionvermas=vermasTopsProveedores()', 'grillaTopsProveedor', 'grillaTopsProveedor', 'img03');
}
function buscarUtilidadNetaxMes(){
	vValor = "'1','1', 0, 4, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2),substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)-1));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'M'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReporteUtilidadNetaxMesoxSemana&filtro=' + vValor + '&idgrilla=13&titulotabla=UTILIDAD NETA X MES&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico13&solografico=SI&tamanografico=S&datografico=mes&accionvermas=vermasUtilidadNetaxMes()', 'grillaUtilidadNetaxMes', 'grillaUtilidadNetaxMes', 'img03');
}
function buscarUtilidadNetaxSemana(){
	vValor = "'1','1', 0, 4, '','','<?php echo date('d/m/Y',mktime(0,0,0,substr($_SESSION['R_FechaProceso'],3,2)-2,substr($_SESSION['R_FechaProceso'],0,2),substr($_SESSION['R_FechaProceso'],6,4)));?>','<?php echo $_SESSION['R_FechaProceso'];?>',0,'S'";
	//SI SE AGREGA MAS ARGUMENTOS, MODIFICAR EL FILTRO EN ReporteVentaResumen
	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Movimiento&id_clase=64&funcion=ReporteUtilidadNetaxMesoxSemana&filtro=' + vValor + '&idgrilla=14&titulotabla=UTIILIDAD NETA X SEMANA&grafico=SI&linkgrafico=NO&sesiongrafico=datagrafico14&solografico=SI&tamanografico=S&datografico=semanames&accionvermas=vermasUtilidadNetaxSemana()', 'grillaUtilidadNetaxSemana', 'grillaUtilidadNetaxSemana', 'img03');
}
function buscarTopsProductosUtilidad(){
	vValor = "'1','1', 0,<?php echo $id_cliente;?>, '','','','<?php echo $_SESSION['R_FechaProceso'];?>','','','','',1";
	
	setRun('vista/listGrillaSinOperacionTops','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&clase=Producto&id_clase=11&funcion=ReporteDetalladoUtilidad&filtro=' + vValor + '&idgrilla=15&titulotabla=UTILIDAD POR PRODUCTOS&sesiongrafico=datagrafico15&solografico=SI&tamanografico=S&datografico=codigo&accionvermas=vermasTopsProductosUtilidad()', 'grillaTopsProductoUtilidad', 'grillaTopsProductoUtilidad', 'img03');
}
buscarVentasxSemana();
buscarVentasxMes();
buscarComprasxSemana();
buscarComprasxMes();
buscarUtilidadxSemana();
buscarUtilidadxMes();
buscarUtilidadNetaxSemana();
buscarUtilidadNetaxMes();
buscarTopsProductosUtilidad();
buscarTopsProductos();
buscarTopsCajeros();
buscarTopsMeseros();
buscarTopsClientes();
buscarTopsProveedores();
buscarCumpleanosCliente();

function vermasVentasxSemana(){
	setRun('vista/listReporteVentaxSemana','&id_clase=44','frame','carga','imgloading');	
}
function vermasVentasxMes(){
	setRun('vista/listReporteVentaxMes','&id_clase=44','frame','carga','imgloading');	
}
function vermasComprasxSemana(){
	setRun('vista/listReporteCompraxSemana','&id_clase=65','frame','carga','imgloading');	
}
function vermasComprasxMes(){
	setRun('vista/listReporteCompraxMes','&id_clase=65','frame','carga','imgloading');	
}
function vermasUtilidadxSemana(){
	setRun('vista/listReporteUtilidadxSemana','&id_clase=44','frame','carga','imgloading');	
}
function vermasUtilidadxMes(){
	setRun('vista/listReporteUtilidadxMes','&id_clase=4','frame','carga','imgloading');	
}
function vermasUtilidadNetaxSemana(){
	setRun('vista/listReporteUtilidadNetaxSemana','&id_clase=44','frame','carga','imgloading');	
}
function vermasUtilidadNetaxMes(){
	setRun('vista/listReporteUtilidadNetaxMes','&id_clase=4','frame','carga','imgloading');	
}
function vermasTopsProductos(){
	setRun('vista/listReporteTopsProducto','&id_clase=11','frame','carga','imgloading');	
}
function vermasTopsClientes(){
	setRun('vista/listReporteTopsCliente','&id_clase=49','frame','carga','imgloading');	
}
function vermasTopsCajeros(){
	setRun('vista/listReporteTopsCajero','&id_clase=49','frame','carga','imgloading');	
}
function vermasTopsMeseros(){
	setRun('vista/listReporteTopsMesero','&id_clase=49','frame','carga','imgloading');	
}
function vermasCumpleanosCliente(){
	setRun('vista/listReporteCumpleanosCliente','&id_clase=23','frame','carga','imgloading');	
}
function vermasTopsProveedores(){
	setRun('vista/listReporteTopsProveedor','&id_clase=49','frame','carga','imgloading');	
}
function vermasTopsProductosUtilidad(){
	setRun('vista/listReporteTopsProductoUtilidad','&id_clase=11','frame','carga','imgloading');	
}
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
<div class="row">
    <div class="col s12"><div id="cargagrilla"></div></div>
</div>
<div class="row">
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">VENTAS POR MES</h5>
            <div id="grillaVentasxMes"></div>
        </div>
    </div>
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">VENTAS POR SEMANA</h5>
            <div id="grillaVentasxSemana"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">COMPRAS POR MES</h5>
            <div id="grillaComprasxMes"></div>
        </div>
    </div>
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">COMPRAS POR SEMANA</h5>
            <div id="grillaComprasxSemana"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">UTILIDAD BRUTA POR MES</h5>
            <div id="grillaUtilidadxMes"></div>
        </div>
    </div>
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">UTILIDAD BRUTA POR SEMANA</h5>
            <div id="grillaUtilidadxSemana"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">UTILIDAD NETA POR MES</h5>
            <div id="grillaUtilidadNetaxMes"></div>
        </div>
    </div>
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">UTILIDAD NETA POR SEMANA</h5>
            <div id="grillaUtilidadNetaxSemana"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">UTILIDAD POR PRODUCTO</h5>
            <div id="grillaTopsProductoUtilidad"></div>
        </div>
    </div>
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">TOPS DE PRODUCTOS</h5>
            <div id="grillaTopsProducto"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">TOS DE MESEROS</h5>
            <div id="grillaTopsMesero"></div>
        </div>
    </div>
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">TOPS DE CAJEROS</h5>
            <div id="grillaTopsCajero"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">TOPS DE CLIENTES</h5>
            <div id="grillaTopsCliente"></div>
        </div>
    </div>
    <div class="col s12 m6 l6">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">TOPS DE PROVEEDORES</h5>
            <div id="grillaTopsProveedor"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l12">
        <div class="divGraficos">
            <h5 class="blue lighten-4 blue-text text-darken-4">CUMPLEAÑOS CLIENTES</h5>
            <div id="grillaCumpleanosCliente"></div>
        </div>
    </div>
</div>
</body>
</html>