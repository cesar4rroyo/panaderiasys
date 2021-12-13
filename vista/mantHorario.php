<?php
require("../modelo/clsHorario.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsHorario($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstHorario = $objMantenimiento->obtenerTabla();
if(is_string($rstHorario)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstHorario."</td>";
}else{
	$datoHorario = $rstHorario->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataHorarios = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarHorario(1,1,'2',1,$_GET["Id"],$_GET['idsucursal'],"");
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
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
		getFormData("frmMantHorario");
}
function aceptar(){
	if(setValidar("frmMantHorario")){
		g_ajaxGrabar.setURL("controlador/contHorario.php?ajax=true");
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
<form id="frmMantHorario" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $_GET['idsucursal'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="200" border="0">
<?php
reset($dataHorarios);
foreach($dataHorarios as $value){
?>
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td>
    <select id="cbo<?php echo $value["descripcion"]; if($_GET["accion"]!="ACTUALIZAR") echo '[]';?>" name = "cbo<?php echo $value["descripcion"]; if($_GET["accion"]!="ACTUALIZAR") echo '[]';?>" <?php if($_GET["accion"]!="ACTUALIZAR") echo 'multiple';?>>
    <option value="1 LUNES" <?php if($_GET["accion"]=="ACTUALIZAR") { if($dato[strtolower($value["descripcion"])]=='LUNES') echo 'selected';}?>>LUNES</option>
    <option value="2 MARTES" <?php if($_GET["accion"]=="ACTUALIZAR") { if($dato[strtolower($value["descripcion"])]=='MARTES') echo 'selected';}?>>MARTES</option>
    <option value="3 MIERCOLES" <?php if($_GET["accion"]=="ACTUALIZAR") { if($dato[strtolower($value["descripcion"])]=='MIERCOLES') echo 'selected';}?>>MIERCOLES</option>
    <option value="4 JUEVES" <?php if($_GET["accion"]=="ACTUALIZAR") { if($dato[strtolower($value["descripcion"])]=='JUEVES') echo 'selected';}?>>JUEVES</option>
    <option value="5 VIERNES" <?php if($_GET["accion"]=="ACTUALIZAR") { if($dato[strtolower($value["descripcion"])]=='VIERNES') echo 'selected';}?>>VIERNES</option>
    <option value="6 SABADO" <?php if($_GET["accion"]=="ACTUALIZAR") { if($dato[strtolower($value["descripcion"])]=='SABADO') echo 'selected';}?>>SABADO</option>
    <option value="7 DOMINGO" <?php if($_GET["accion"]=="ACTUALIZAR") { if($dato[strtolower($value["descripcion"])]=='DOMINGO') echo 'selected';}?>>DOMINGO</option>
    </select>
	</td></tr>	<?php }?>
    <?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td>
        <input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>>
    </td></tr>	<?php }?>
    <?php if($value["idcampo"]==5){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>	<?php }?>
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
	<td><a href="#" onClick="javascript:setRun('vista/listHorario','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoHorario->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoHorario->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>