<?php
require("../modelo/clsCampo.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsCampo($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstCampo = $objMantenimiento->obtenerTabla();
if(is_string($rstCampo)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstCampo."</td>";
}else{
	$datoCampo = $rstCampo->fetchObject();
}

$rst = $objMantenimiento->obtenerCamposMostrar("F");
$dataCampos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarCampo(1,1,'2',1,$id_tabla,$_GET["Id"],"");
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
	/*g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtIdTabla", document.getElementById("txtIdTabla").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtComentario", document.getElementById("txtComentario").value);
	g_ajaxGrabar.setParameter("txtLongitud", document.getElementById("txtLongitud").value);
	g_ajaxGrabar.setParameter("txtDiccionario", document.getElementById("txtDiccionario").value);*/
	getFormData("frmMantCampo");	
}
function aceptar(){
	if(setValidar("frmMantCampo")){
		g_ajaxGrabar.setURL("controlador/contCampo.php?ajax=true");
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
<form id="frmMantCampo" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="200">
<?php
reset($dataCampos);
foreach($dataCampos as $value){
?>
	<?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtDescripcion" name = "txtDescripcion" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtComentario" name = "txtComentario" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtLongitud" name = "txtLongitud" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==7){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtDiccionario" name = "txtDiccionario" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<tr><td colspan="2"><div align="right"><label><?php echo $value["comentario"];?>	      
	        <input type="checkbox" id="chkValidacion" name="chkValidacion" <?php if($_GET["accion"]=="ACTUALIZAR"){
if($dato[strtolower($value["descripcion"])]=="S"){echo 'checked="checked"';}else{echo "";}}else{echo "";}
	?>>      
	</label></div></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="text" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> id="txtMsgValidacion" name = "txtMsgValidacion" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==10){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="text" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> id="txtLongitudReporte" name = "txtLongitudReporte" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==11){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><select id="txtAlineacionReporte" name = "txtAlineacionReporte">
        <option value="L" <?php if($_GET["accion"]=="ACTUALIZAR") {if($dato[strtolower($value["descripcion"])]=='L') echo 'selected';}?>>Izquierda</option>
        <option value="C" <?php if($_GET["accion"]=="ACTUALIZAR") {if($dato[strtolower($value["descripcion"])]=='C') echo 'selected';}else{ echo 'selected';}?>>Centro</option>
        <option value="R" <?php if($_GET["accion"]=="ACTUALIZAR") {if($dato[strtolower($value["descripcion"])]=='R') echo 'selected';}?>>Derecha</option>
        </select>
        </td></tr>
	<?php }?>
<?php }?>
	<tr>
	<td colspan="2" align="center"><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"> <input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
	</tr>
</table>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listCampo','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoCampo->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoCampo->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>