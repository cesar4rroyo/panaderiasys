<?php
require("../modelo/clsTabla.php");
$id_clase = $_GET["id_clase"];
//echo $id_clase;
try{
$objMantenimiento = new clsTabla($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstTabla = $objMantenimiento->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}

$rst = $objMantenimiento->obtenerCamposMostrar("F");
$dataCampos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarTabla(1,1,'1',1,$_GET["Id"],"%%");
	$dato = $rst->fetch();
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
	g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtComentario", document.getElementById("txtComentario").value);
	if(document.getElementById("chkMultiple").checked){
		g_ajaxGrabar.setParameter("chkMultiple", "S");
	}else{
		g_ajaxGrabar.setParameter("chkMultiple", "N");
	}	
	if(document.getElementById("optTipoS").checked){
		g_ajaxGrabar.setParameter("optTipo", "S");
	}
	if(document.getElementById("optTipoB").checked){
		g_ajaxGrabar.setParameter("optTipo", "B");
	}
	if(document.getElementById("optTipoP").checked){
		g_ajaxGrabar.setParameter("optTipo", "P");
	}
	
}
function aceptar(){
	if(setValidar("frmMantTabla")){
		g_ajaxGrabar.setURL("controlador/contTabla.php?ajax=true");
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
</script>
</head>
<body>
<?php require("tablaheader.php");?>
<form id="frmMantTabla" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<table width="200" border="0">
<?php
reset($dataCampos);
foreach($dataCampos as $value){
?>
	<?php if($value["idcampo"]==2){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtDescripcion" name = "txtDescripcion" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtComentario" name = "txtComentario" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==4){?>
	<tr><td colspan="2" align="right"><label><?php echo $value["comentario"];?><input type="Checkbox" name="chkMultiple" id="chkMultiple" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>>
	</label></td>
    	</tr>
	<?php }?>
	<?php if($value["idcampo"]==5){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><label><input type="radio" id="optTipoS" name = "optTipo" value = "S" <?php if($dato[strtolower($value["descripcion"])]=="S" || empty($dato[strtolower($value["descripcion"])])){ echo "checked=checked";}?>>Sistema</label><br>
        	<input type="radio" id="optTipoB" name = "optTipo" value = "B" <?php if($dato[strtolower($value["descripcion"])]=="B"){ echo "checked=checked";}?>>Base Datos<br>
            <input type="radio" id="optTipoP" name = "optTipo" value = "P" <?php if($dato[strtolower($value["descripcion"])]=="P"){ echo "checked=checked";}?>>Parametros</label></td></tr>
	<?php }?>
<?php }?>
	<tr>
	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
	</tr>
</table>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listTabla','&id_clase=<?php echo $_GET['id_clase'];?>','frame', 'frame', 'img02')"><?php echo $datoTabla->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoTabla->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>