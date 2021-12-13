<?php
require("../modelo/clsRolPersona.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsRolPersona($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstRolPersona = $objMantenimiento->obtenerTabla();
if(is_string($rstRolPersona)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstRolPersona."</td>";
}else{
	$datoRolPersona = $rstRolPersona->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataRolPersona = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarRolPersona(1,1,'2',1,$_GET["Id"],"",$_GET['IdSucursal'],$_GET['IdPersona'],$_GET['IdPersonaMaestro']);
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
	getFormData("frmRolPersona");
	/*g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);*/
	//g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);
}
function aceptar(){
	if(setValidar("frmRolPersona")){
		g_ajaxGrabar.setURL("controlador/contRolPersona.php?ajax=true");
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
<form id="frmRolPersona" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtIdPersona" name = "txtIdPersona" value = "<?php echo $_GET["IdPersona"];?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $_GET["IdSucursal"];?>">
<table width="200" border="1">
<?php //echo $_GET["IdPersona"];
require("fun.php");
reset($dataRolPersona);
foreach($dataRolPersona as $value){
?>
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("select * from rol where estado='N' and idrol not in(select idrol from rolpersona where idpersona=".$_GET["IdPersona"]." and idsucursal=".$_GET["IdSucursal"].")",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("select * from rol where estado='N' and idrol not in(select idrol from rolpersona where idpersona=".$_GET["IdPersona"]." and idsucursal=".$_GET["IdSucursal"].")",$value["descripcion"],0,'',$objMantenimiento);?></td></tr>
	<?php }?>
<?php }?>
	<tr>
	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
	</tr>
</table>
</form>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><?php echo $datoRolPersona->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoRolPersona->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>