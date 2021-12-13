<?php
require('../modelo/clsPersona.php');
$id_clasesucursal = $_GET["id_clasesucursal"];
if(!$id_clasesucursal){
	$id_clasesucursal = 58;
}
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
	//vValor = "'"+vOrder + "'," + vBy + ",<?php echo $id_cliente;?>, 0, 0, '" + document.getElementById("txtBuscar").value + "',1";
	vValor = "'"+vOrder + "'," + vBy + ",<?php echo $id_cliente;?>, 0, 0, '" + document.getElementById("txtBuscar_Apellido_Nombre").value + "','" + document.getElementById("txtBuscar_NroDoc").value + "','" + document.getElementById("cbosexo").value + "','" + document.getElementById("cbocompartido").value + "',1";
	setRun('vista/listGrilla3','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Persona&id_clase=<?php echo $id_clase;?>&imprimir=SI&filtro=' + vValor + '&titulo=de Personal', 'grilla', 'grilla', 'img03');
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

function actualizar(idpersona, idsucursal, idpersonamaestro){
	setRun('vista/mantPersona','&accion=ACTUALIZAR&clase=Persona&id_clase=<?php echo $id_clase;?>&IdSucursal=' + idsucursal + '&IdPersona=' + idpersona + '&IdPersonaMaestro=' + idpersonamaestro,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(idpersona, idsucursal, idpersonamaestro){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contPersona.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtIdSucursal", idsucursal);
		g_ajaxGrabar.setParameter("txtIdPersona", idpersona);
		g_ajaxGrabar.setParameter("txtIdPersonaMaestro", idpersonamaestro);
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
buscar();
function roles(idpersona, idsucursal, idpersonamaestro){
	setRun('vista/listRolPersona','&accion=ACTUALIZAR&clase=RolPersona&id_clase=15&id_empresa=<?php echo $_GET["id_empresa"];?>&IdSucursal=' + idsucursal + '&IdPersona=' + idpersona + '&IdPersonaMaestro=' + idpersonamaestro,'frame','carga','imgloading');
}
function verusuario(idpersona, idsucursal, idpersonamaestro){
	setRun('vista/listUsuario','&accion=ACTUALIZAR&clase=Usuario&id_clase=16&id_empresa=<?php echo $_GET["id_empresa"];?>&IdSucursal=' + idsucursal + '&IdPersona=' + idpersona + '&IdPersonaMaestro=' + idpersonamaestro+'&id_clasesucursal=<?php echo $id_clasesucursal;?>','frame','carga','imgloading');
}
CargarCabeceraRuta([["Personal",'vista/listPersonal','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
</script>
</head>
<body>
<?php
$objFiltro = new clsPersona($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<!--BOTONERA INICIO-->
    <div class="Botones" id="opciones">
        <div class="row">
<?php

$rstTabla = $objFiltro->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}

$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Tabla</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
            <div class="col s12 m12 l12 center">
            <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                    type="button" data-position="bottom" data-delay="50" 
                    data-tooltip="<?php echo umill($operacion['comentario']);?>" 
                    onClick="javascript:setRun('vista/mantPersona', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
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
                    <label for="txtFechaInicio" class="active">Fecha Nac. Inicial</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaFin" class="active">Fecha Nac. Final</label>
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