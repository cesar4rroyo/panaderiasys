<?php
require('../modelo/clsTipoCambio.php');
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
	vValor = "'"+vOrder + "'," + vBy + ", 0, '" + document.getElementById("txtBuscar").value + "'";
	setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=TipoCambio&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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
	setRun('vista/mantTipoCambio','&accion=ACTUALIZAR&clase=TipoCambio&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contTipoCambio.php?ajax=true");
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

function verOperacionesPerfil(id){
	setRun('vista/listRelacionOperacionPerfil','&nro_reg=<?php echo $nro_reg;?>&nro_hoja=1&id_cliente=<?php echo $id_cliente;?>&clase=RelacionOperacionPerfil&id_perfil=' + id,'frame', 'frame', 'imgloading03');
}
function verPermisoUsuario(id){
	setRun('vista/listPermisoUsuario','&nro_reg=<?php echo $nro_reg;?>&id_cliente=<?php echo $id_cliente;?>&nro_hoja=1&clase=PermisoUsuario&id_perfil=' + id,'frame', 'frame', 'imgloading03');
}
buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsTipoCambio($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<!--BOTONERA INICIO-->
    <div class="Botones" id="opciones">
        <div class="row">
<?php

$rstTipoCambio = $objFiltro->obtenerTabla();
if(is_string($rstTipoCambio)){
	echo "<td colspan=100>Error al Obtener datos del Perfil</td></tr><tr><td colspan=100>".$rstTipoCambio."</td>";
}else{
	$datoTipoCambio = $rstTipoCambio->fetchObject();
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
                    onClick="javascript:setRun('vista/mantTipoCambio', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
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
            <div class="col s12 m6 l11">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar">
                    <label for="txtBuscar">Buscar</label>
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