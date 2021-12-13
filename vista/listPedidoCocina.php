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
	$order="2";
}
$by = $_GET["by"];
if(!$by){
	$by="0";
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
var n = 0;
CargarCabeceraRuta([["Movimiento","vista/listPedido","<?php echo $_SERVER['QUERY_STRING'];?>"],["Pedidos Detallados","vista/listPedidoCocina","<?php echo $_SERVER['QUERY_STRING'];?>"]],true);
function buscar(){
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	*/
	if($("#order").length > 0){
		vOrder = document.getElementById("order").value;
		vBy = document.getElementById("by").value;
		vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','"+document.getElementById("txtSituacion").value+"','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "',0,0,0,'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtResponsable").value + "','" + document.getElementById("txtComentario").value + "','" + document.getElementById("txtMesa").value + "'";
		if(document.getElementById("txtSituacion").value=='O'){
			setRun('vista/listGrilla2Niveles','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=NO&tiporeporte=DinamicoResumen&titulo=de Pedidos Consumidos&origen=Pedido&clase2=DetalleAlmacen&id_clase2=3', 'grilla', 'grilla', 'img03');
			setRun('vista/listGrilla2Niveles','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&zoom=SI&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=NO&tiporeporte=DinamicoResumen&titulo=de Pedidos Consumidos&origen=Pedido&clase2=DetalleAlmacen&id_clase2=3', 'grillapopup', 'grillapopup', 'img03');
			setTimeout(function(){
				buscar();
			},30000);
		}
	}
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

function actualizar(idmesa, mesa){
	setRun('vista/frmComanda','&accion=ACTUALIZAR&salon=PR&idmesa='+idmesa+'&mesa='+mesa,'frame', 'frame', 'imgloading');
}
function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contPedido.php?ajax=true");
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
function atender(id){
	//if(setValidar()){
		g_ajaxGrabar.setURL("controlador/contPedido.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "CABIASITUACION");
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
//buscar();
/*
function generarComprobante(id){
	setRun('vista/mantVentaRapida','&accion=NUEVO&clase=Movimiento&id_clase=44&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}*/

document.getElementById('txtSituacion').value='O';
buscar();
/*function autobuscar(){
	setTimeout("buscar('O');autobuscar();",10000);
}
autobuscar();*/

function maximizar(){
	document.getElementById('grillapopup2').style.display='';
}
function minimizar(){
	document.getElementById('grillapopup2').style.display='none';
}
//setInterval("buscar();",10000);
</script>
</head>
<body>
	<div class="Botones" id="opciones">
        <div class="row">
<?php
$objFiltro = new clsMovimiento($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/frmMesas', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
		<div class="col s12 m12 l12 center">
            <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                    type="button" data-position="bottom" data-delay="50" 
                    data-tooltip="<?php echo umill($operacion['comentario']);?>" 
                    onClick="javascript:setRun('vista/frmCajero', '&id_clase=<?php echo $id_clase;?>', 'frame','frame', 'imgloading');"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
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
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtMesa" type="text" name="txtMesa">
                    <label for="txtMesa">Mesa</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtResponsable" type="text" name="txtResponsable">
                    <label for="txtResponsable">Mesero</label>
                </div>
            </div>
            <div class="col s12 m3 l1">
                <div class="input-field inline">
                    <input id="txtPersona" name="txtPersona" type="text">
                    <label for="txtPersona">Cliente</label>
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
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
                </div>
            </div>
            <div class="col s12 m6 l1">
            	<div class="input-field inline">
                    <button type="button" class="btn deep-orange lighten-2" onClick="maximizar()"><i class="material-icons black-text">open_in_new</i></button>
            	</div>
            </div>
        </div>
    </div>
    <!--FILTROS FINAL-->
    <input name="txtSituacion" type="hidden" id="txtSituacion" value="O">
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
                <div class="row">
                    <div id="grillapopup2" style="border: 0px solid #aaa; padding: 0px; position:absolute; top:0; left:0; width:100%; height:100%; display:none; z-index:20; background-color: white;">
                            <div id="grillapopup" style="border: 0px solid #aaa; padding: 0px; width:100%; height:100%;"></div>
                    </div>
                </div>
        </div>
    </div>
</div>
</body>
</html>