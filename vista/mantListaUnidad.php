<?php
require("../modelo/clsListaUnidad.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET["id_cliente"];
$id_producto=$_GET["IdProducto"];
$idsucursalproducto=$_GET["IdSucursalProducto"];
//echo $id_cliente;
try{
$objMantenimiento = new clsListaUnidad($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['NombreUsuario'],$_SESSION['Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstListaUnidad = $objMantenimiento->obtenerTabla();
if(is_string($rstListaUnidad)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstListaUnidad."</td>";
}else{
	$datoListaUnidad = $rstListaUnidad->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataListaUnidads = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarListaUnidad(1,1,'2',1,$_GET["Id"],$id_cliente, "", $id_producto, $idsucursalproducto);
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetch();
	}
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmMantListaUnidad");
	}

function aceptar(){
	if(setValidar("frmMantListaUnidad")){
		g_ajaxGrabar.setURL("controlador/contListaUnidad.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}

//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFechaVencimiento", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>
</script>
<body>
<?php require("tablaheader.php");?>
<?php //$_GET['id_cliente'];//echo $_GET['Id'];?>
<form id="frmMantListaUnidad" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $_GET['id_cliente'];?>">
</head>
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtIdProducto" name = "txtIdProducto" value = "<?php echo $id_producto;?>">
<input type="hidden" id="txtIdSucursalProducto" name = "txtIdSucursalProducto" value = "<?php echo $idsucursalproducto;?>">
<table width="200">
<?php
reset($dataListaUnidads);
foreach($dataListaUnidads as $value){
?>
	<?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("select * from unidad where estado='N' and idunidad not in(select idunidad from listaunidad where idproducto=".$_GET['IdProducto']." AND idsucursal=".$_GET['id_cliente']." AND IdUnidad <> ".$dato[strtolower($value["descripcion"])].")",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("select * from unidad where estado='N' and idunidad not in(select idunidad from listaunidad where idproducto=".$_GET['IdProducto']." AND idsucursal=".$_GET['id_cliente'].")",$value["descripcion"],0,'',$objMantenimiento);?></td>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<td>Corresponde&nbsp;a </td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> size="5"></td>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
    	<td><?php
		$rstunidad= $objMantenimiento->buscarUnidadxIdProducto($_GET['IdProducto'],$_GET['IdSucursalProducto']);
		$datounidad= $rstunidad->fetchObject();
		echo $datounidad->unidadbase; ?>
		<input type="hidden" id="txtIdUnidadBase" name="txtIdUnidadBase" value="<?php echo $datounidad->idunidadbase; ?>">
		</td></tr>
	<?php }?>
    <?php if($value["idcampo"]==7){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> size="5"></td>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> size="5"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> size="5"></td>
	<?php }?>
    <?php if($value["idcampo"]==10){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><label><input type="radio" id="optTipoS" name = "optTipo" value = "S" <?php if($dato[strtolower($value["descripcion"])]=="S" || empty($dato[strtolower($value["descripcion"])])){ echo "checked=checked";}?>>S/.</label><br>
        	<input type="radio" id="optTipoD" name = "optTipo" value = "D" <?php if($dato[strtolower($value["descripcion"])]=="D"){ echo "checked=checked";}?>>$<br></label></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==13){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> size="5"></td></tr>
	<?php }?>
<?php }?>
	<tr>
</table>
<center><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()">&nbsp;<input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></center>
	</tr>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listListaUnidad','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoListaUnidad->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoListaUnidad->descripcionmant; ?></td>
</tr></table>
</div>
</body>
</HTML>