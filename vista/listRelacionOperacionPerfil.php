<?php
require('../modelo/clsRelacionOperacionPerfil.php');
$id_clase = $_GET["id_clase"];
if(!$id_clase){
	$id_clase = 12;
}
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = 0;
}
$id_tabla = $_GET["id_tabla"];
if(!$id_tabla){
	$id_tabla = 0;
}
$id_perfil = $_GET["id_perfil"];
if(!$id_perfil){
	$id_perfil = 0;
}

$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
$nro_reg = $_SESSION["R_NroFilaMostrar"];
if(!$nro_reg){
	$nro_reg = 10;
}
$filtro = $_GET["filtro"];
if(!$filtro){
	$filtro = $id_cliente.", 0, 0, ".$id_perfil.", 'T', '', ''";
}
$clase = "RelacionOperacionPerfil";
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
	if(document.getElementById("optTipoT").checked){
		vTipo="T";
	}else{
		vTipo="C";
	}
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ",<?php echo $id_cliente;?>, 0, 0, <?php echo $id_perfil;?>, '"+vTipo + "', '" + document.getElementById("txtBuscarTabla").value + "', '" + document.getElementById("txtBuscar").value + "'";
	setRun('vista/listGrilla4','&id_tabla=<?php echo $id_tabla;?>&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=<?php echo $clase;?>&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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
	setRun('vista/mantRelacionOperacionPerfil','&id_tabla=<?php echo $id_tabla;?>&accion=ACTUALIZAR&clase=<?php echo $clase;?>&id_clase=<?php echo $id_clase;?>&Id=' + id,'frame', 'frame', 'imgloading03');
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
			alert(text);
			//setRun("vista/listGrilla","&nro_reg=10&nro_hoja=1&clase=General&id_clase=<?php echo $id_clase;?>&filtro=2&valor_filtro=%%&order=1","grilla", "grilla", "img03");
			buscar()
			loading(false, "loading");
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
		/*if(g_bandera==true){
			loading(true, "loading", "grilla", "imgActualizando.gif",true);		 	
		}else{
			loading(true, "loading", "grilla", "imgGrabando.gif",true);		 	
		}*/
	//}
}

function activar(idcliente, idtabla, idoperacion, idperfil){
	if(document.getElementById("optTipoT").checked){
		vTipo="T";
	}else{
		vTipo="C";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "ACTIVAR");
	g_ajaxGrabar.setParameter("txtIdOperacion", idoperacion);
	g_ajaxGrabar.setParameter("txtIdTabla", idtabla);
	g_ajaxGrabar.setParameter("txtIdSucursal", idcliente);
	g_ajaxGrabar.setParameter("txtIdPerfil", idperfil);
	g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
	g_ajaxGrabar.response = function(text){
		loading(false, "loading");
		buscar();		
		alert(text);		
	};
	g_ajaxGrabar.request();		
	loading(true, "loading", "grilla", "linea.gif",true);
}

function desactivar(idcliente, idtabla, idoperacion, idperfil){
	if(document.getElementById("optTipoT").checked){
		vTipo="T";
	}else{
		vTipo="C";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "DESACTIVAR");
	g_ajaxGrabar.setParameter("txtIdOperacion", idoperacion);
	g_ajaxGrabar.setParameter("txtIdTabla", idtabla);
	g_ajaxGrabar.setParameter("txtIdSucursal", idcliente);
	g_ajaxGrabar.setParameter("txtIdPerfil", idperfil);
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
	if(document.getElementById("optTipoT").checked){
		vTipo="T";
	}else{
		vTipo="C";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "SUBIR");
	g_ajaxGrabar.setParameter("txtIdOperacion", id);
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
	if(document.getElementById("optTipoT").checked){
		vTipo="T";
	}else{
		vTipo="C";
	}
	g_ajaxGrabar.setURL("controlador/cont<?php echo $clase;?>.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "BAJAR");
	g_ajaxGrabar.setParameter("txtIdOperacion", id);
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
$objFiltro = new clsRelacionOperacionPerfil($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<div id="operaciones">
<?php

$rstRelacionOperacionPerfil = $objFiltro->obtenerTabla();
if(is_string($rstRelacionOperacionPerfil)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstRelacionOperacionPerfil."</td>";
}else{
	$datoRelacionOperacionPerfil = $rstRelacionOperacionPerfil->fetchObject();
}

$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Tabla</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantRelacionOperacionPerfil', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>&id_tabla=<?php echo $id_tabla;?>', 'frame','frame', 'img04');"> 
		<?php
		}
	}
}
?>
</div>
<div id="cargamant"></div>
<div id="busqueda">
<table width="383"><tr>
  <td width="152">Buscar x Operacion:</td><td width="144"><input type="text" id="txtBuscar" name="txtBuscar" value="" ></td><td width="71"><input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
    <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
    <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr>
  <tr>
    <td>Buscara x Tabla</td>
    <td><input type="text" id="txtBuscarTabla" name="txtBuscarTabla" value="" ></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Tipo</td>
    <td><form name="form1" method="post" action="">
      <label><input type="radio" name="optTipo" id="optTipoT" checked value="T" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();">Tabla</label><br>
      <label><input type="radio" name="optTipo" id="optTipoC" value="C" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();">Campo</label>
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
    <td><a href="#" onClick="javascript:setRun('vista/listEmpresa','&id_clase=21','frame', 'frame', 'img05')">Empresa</a></td><td>></td>
    <?php
	if($_GET["id_empresa"]>0){
	$rstEmpresa = $objFiltro->obtenerDataSQL("select razonsocial from Empresa where IdEmpresa = ".$_GET["id_empresa"]);
	if(is_string($rstEmpresa)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstEmpresa."</td>";
	}else{
		$datoEmpresa = $rstEmpresa->fetchObject();
	}
	?>
	<td><a href="#" onClick="javascript:setRun('vista/listSucursal','&nro_reg=10&id_empresa=<?php echo$_GET["id_empresa"];?>&id_clase=40&clase=Sucursal&filtro=<?php echo $id_empresa;?>,0,\'%%\'','frame', 'frame', 'img05')"><?php echo $datoEmpresa->razonsocial; ?></a></td><td>></td>
    <?php
	}
	if($id_cliente>0){
	$rstSucursal = $objFiltro->obtenerDataSQL("select razonsocial from Sucursal where IdSucursal = ".$id_cliente);
	if(is_string($rstEmpresa)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstSucursal."</td>";
	}else{
		$datoSucursal= $rstSucursal->fetchObject();
	}
	?>
	<td><?php echo $datoSucursal->razonsocial; ?></td><td>></td>
    <?php
	}?>
	<td><a href="#" onClick="javascript:setRun('vista/listPerfil','&nro_reg=10&id_empresa=<?php echo $id_empresa;?>&id_clase=34&clase=Sucursal&id_cliente=<?php echo $id_cliente;?>','frame', 'frame', 'img05')">Perfiles</a></td><td>></td>
    <?php
	$rstTabla = $objFiltro->obtenerDataSQL("select Descripcion from Perfil where IdPerfil = ".$id_perfil);
	if(is_string($rstTabla)){
		echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
	}else{
		$datoTabla= $rstTabla->fetchObject();
	}
	?>
	<td><?php echo $datoTabla->descripcion; ?></td><td>></td>
  	<td><?php echo $datoRelacionOperacionPerfil->descripcionmant; ?></td>
</tr></table>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
</body>
</HTML>