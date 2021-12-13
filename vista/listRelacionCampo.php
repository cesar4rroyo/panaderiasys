<?php
require('../modelo/clsRelacionCampo.php');
$id_clase = $_GET["id_clase"];
if(!$id_clase){
	$id_clase = 9;
}
$filtro = $_GET["filtro"];
if(!$filtro){
	$filtro = "0, 0, 0, 'G', ''";
}
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = 0;
}
$id_tabla = $_GET["id_tabla"];
if(!$id_tabla){
	$id_tabla = 0;
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = 0;
}
$nro_reg = $_SESSION["R_NroFilaMostrar"];
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
	$nro_hoja = 1;
}
$clase = "RelacionCampo";
$order = $_GET["order"];
if(!$order){
	$order="1";
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
	if(document.getElementById("optTipoG").checked){
		vTipo="G";
	}else{
		vTipo="F";
	}
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", <?php echo $id_cliente;?>, <?php echo $id_tabla;?>, 0, '"+vTipo + "', '" + document.getElementById("txtBuscar").value + "'";
	setRun('vista/listGrilla','&id_tabla=<?php echo $id_tabla;?>&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=<?php echo $clase;?>&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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
	setRun('vista/mantRelacionCampo','&id_tabla=<?php echo $id_tabla;?>&accion=ACTUALIZAR&clase=<?php echo $clase;?>&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("txtIdTabla", <?php echo $id_tabla;?>);
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

function activar(id){
	if(document.getElementById("optTipoG").checked){
		vTipo="G";
	}else{
		vTipo="F";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "ACTIVAR");
	g_ajaxGrabar.setParameter("txtIdCampo", id);
	g_ajaxGrabar.setParameter("txtIdTabla", <?php echo $id_tabla;?>);
	g_ajaxGrabar.setParameter("txtIdSucursal", <?php echo $id_cliente;?>);
	g_ajaxGrabar.setParameter("txtTipo", vTipo);
	g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		
	g_ajaxGrabar.response = function(text){
		loading(false, "loading");
		buscar();		
		alert(text);
	};
	g_ajaxGrabar.request();		
	loading(true, "loading", "grilla", "linea.gif",true);
}

function desactivar(id){
	if(document.getElementById("optTipoG").checked){
		vTipo="G";
	}else{
		vTipo="F";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "DESACTIVAR");
	g_ajaxGrabar.setParameter("txtIdCampo", id);
	g_ajaxGrabar.setParameter("txtIdTabla", <?php echo $id_tabla;?>);
	g_ajaxGrabar.setParameter("txtIdSucursal", <?php echo $id_cliente;?>);
	g_ajaxGrabar.setParameter("txtTipo", vTipo);
	g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		
	g_ajaxGrabar.response = function(text){
		loading(false, "loading");
		buscar();		
		alert(text);
	};
	g_ajaxGrabar.request();		
	loading(true, "loading", "grilla", "linea.gif",true);
}

function subir(id){
	if(document.getElementById("optTipoG").checked){
		vTipo="G";
	}else{
		vTipo="F";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "SUBIR");
	g_ajaxGrabar.setParameter("txtIdCampo", id);
	g_ajaxGrabar.setParameter("txtIdTabla", <?php echo $id_tabla;?>);
	g_ajaxGrabar.setParameter("txtIdSucursal", <?php echo $id_cliente;?>);
	g_ajaxGrabar.setParameter("txtTipo", vTipo);
	g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		
	g_ajaxGrabar.response = function(text){
		loading(false, "loading");
		buscar();		
		//alert(text);
	};
	g_ajaxGrabar.request();		
	loading(true, "loading", "grilla", "linea.gif",true);
}

function bajar(id){
	if(document.getElementById("optTipoG").checked){
		vTipo="G";
	}else{
		vTipo="F";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "BAJAR");
	g_ajaxGrabar.setParameter("txtIdCampo", id);
	g_ajaxGrabar.setParameter("txtIdTabla", <?php echo $id_tabla;?>);
	g_ajaxGrabar.setParameter("txtIdSucursal", <?php echo $id_cliente;?>);
	g_ajaxGrabar.setParameter("txtTipo", vTipo);
	g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		
	g_ajaxGrabar.response = function(text){
		loading(false, "loading");
		buscar();		
		//alert(text);
	};
	g_ajaxGrabar.request();		
	loading(true, "loading", "grilla", "linea.gif",true);
}
buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsRelacionCampo($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<div id="operaciones">
<?php

$rstRelacionCampo = $objFiltro->obtenerTabla();
if(is_string($rstRelacionCampo)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstRelacionCampo."</td>";
}else{
	$datoRelacionCampo = $rstRelacionCampo->fetchObject();
}

$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Tabla</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantRelacionCampo', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>&id_tabla=<?php echo $id_tabla;?>', 'cargamant','cargamant', 'img04');"> 
		<?php
		}
	}
}
?>
</div>
<div id="cargamant"></div>
<div id="busqueda">
<table width="338"><tr><td width="82">Buscar :</td><td width="179"><input type="text" id="txtBuscar" name="txtBuscar" value="" ></td><td width="61"><input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr>
  <tr>
    <td>Tipo</td>
    <td><form name="form1" method="post" action="">
      <label><input type="radio" name="optTipo" id="optTipoG" checked value="G" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();">Grilla</label><br>
      <label><input type="radio" name="optTipo" id="optTipoF" value="F" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();">Formulario</label>
    </form>    </td>
    <td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
  </tr>
</table>
</div>
<div id="cargagrilla"></div>
<div id="grilla"></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
    <td><a href="#" onClick="javascript:setRun('vista/listEmpresa','&id_clase=21','frame', 'frame', 'img05')">Empresas</a></td><td>></td>
    <?php
	if($_GET["id_empresa"]>0){
	$rstEmpresa = $objFiltro->obtenerDataSQL("select RazonSocial from Empresa where IdEmpresa = ".$_GET["id_empresa"]);
	if(is_string($rstEmpresa)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstEmpresa."</td>";
	}else{
		$datoEmpresa = $rstEmpresa->fetchObject();
	}
	?>
	<td><a href="#" onClick="javascript:setRun('vista/listSucursal','&nro_reg=10&id_empresa=<?php echo $id_empresa;?>&id_clase=40&clase=Sucursal&filtro=<?php echo $id_empresa;?>,0,\'%%\'','frame', 'frame', 'img05')"><?php echo $datoEmpresa->razonsocial; ?></a></td><td>></td>
    <?php
	}
	if($_GET["id_cliente"]>0){
	$rstSucursal = $objFiltro->obtenerDataSQL("select RazonSocial from Sucursal where IdSucursal = ".$_GET["id_cliente"]);
	if(is_string($rstEmpresa)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstSucursal."</td>";
	}else{
		$datoSucursal= $rstSucursal->fetchObject();
	}
	?>
	<td><a href="#" onClick="javascript:setRun('vista/listRelacionTablaSucursal','&nro_reg=10&id_cliente=<?php echo $id_cliente;?>&id_empresa=<?php echo $id_empresa;?>&id_clase=42&clase=Tabla&filtro=0,<?php echo $id_cliente;?>,\'%%\'','frame', 'frame', 'img05')"><?php echo $datoSucursal->razonsocial; ?></a></td><td>></td>
    <?php
	}
	if($_GET["id_tabla"]>0){
	$rstTabla = $objFiltro->obtenerDataSQL("select Descripcion from Tabla where IdTabla = ".$_GET["id_tabla"]);
	if(is_string($rstTabla)){
		echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
	}else{
		$datoTabla= $rstTabla->fetchObject();
	}
	?>
	<td><?php echo $datoTabla->descripcion; ?></td>
    <?php
	}
	?>
</tr></table>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
</body>
</HTML>