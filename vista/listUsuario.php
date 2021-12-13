<?php
require('../modelo/clsUsuario.php');
require ('fun.php');
$id_clasesucursal = $_GET["id_clasesucursal"];
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
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = $_SESSION['R_IdEmpresa'];
}
$id_cliente = $_GET["IdSucursal"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
$id_persona = $_GET["IdPersona"];
if(!$id_persona){
	$id_persona = 0;
}
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
	vValor = "'"+vOrder + "'," + vBy + ", <?php echo $id_cliente;?>, <?php echo $id_persona;?>, '" + document.getElementById("txtBuscar").value + "'";
	setRun('vista/listGrilla2','&imprimir=SI&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Usuario&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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

function actualizar(id_usuario,id_cliente){
	setRun('vista/mantUsuario','&accion=ACTUALIZAR&clase=Usuario&id_clase=<?php echo $id_clase;?>&id_cliente='+id_cliente+'&id_persona=<?php echo $id_persona;?>&id_usuario='+id_usuario,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id_usuario,id_cliente){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contUsuario.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtIdSucursal", id_cliente);
		g_ajaxGrabar.setParameter("txtId", id_usuario);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
        	
		g_ajaxGrabar.response = function(text){
			alert(text);
			buscar()
			loading(false, "loading");
		};
		g_ajaxGrabar.request();		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}

buscar();
CargarCabeceraRuta([["Usuario",'vista/listUsuario','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
</script>
</head>
<body>
<?php
$objFiltro = new clsUsuario($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
    <!--BOTONERA INICIO-->
    <div class="Botones" id="opciones">
        <div class="row">
<?php

$rstUsuario = $objFiltro->obtenerTabla();
if(is_string($rstUsuario)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstUsuario."</td>";
}else{
	$datoUsuario = $rstUsuario->fetchObject();
}
$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Tabla</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		$rstCantUsu = $objFiltro->consultarUsuario(1,1,'1',1,$id_cliente,$id_persona,'');
		if($rstCantUsu->rowCount()==0){
		?>
            <div class="col s12 m12 l12 center">
            <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                    type="button" data-position="bottom" data-delay="50" 
                    data-tooltip="<?php echo umill($operacion['comentario']);?>" 
                    onClick="javascript:setRun('vista/mantUsuario', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>&id_cliente=<?php echo $id_cliente;?>&id_persona=<?php echo $id_persona;?>', 'cargamant','cargamant', 'img04');"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
        </div>
		<?php
		}
		}
	}
}
?>
        </div>
    </div>
    <!--BOTONERA FIN-->


<div id="cargamant"></div>

<!--FILTROS INICIO-->
<div class="container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar">
                    <label for="txtBuscar">Buscar</label>
                </div>
            </div>
            <!--div class="col s12 m6 l2">
                <div class="input-field inline">
                    <?php
                    try{
                    $objMantenimiento = new clsUsuario($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
                    }catch(PDOException $e) {
                        echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
                    }
                    echo genera_cboGeneralSQL("Select idperfil, descripcion as Descripcion from perfil WHERE estado = 'N' AND idsucursal=(".$_SESSION['R_IdSucursal'].") order by descripcion","perfil",$_SESSION["R_filtrosbusqueda"]["cboperfil"],'',$objMantenimiento,'', 'Todos');
                    ?>
                    <label>Perfil</label>
                </div>
            </div-->
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
            <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2"  onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
                </div>
            </div>
        </div>
    </div>
    <!--FILTROS FINAL-->
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
</body>
</HTML>