<?php
require("../modelo/clsParametro.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['id_cliente'];
$id_empresa=$_GET['id_empresa'];
//echo $id_clase;
try{
$objMantenimiento = new clsParametro($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstParametro = $objMantenimiento->obtenerTabla();
if(is_string($rstParametro)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstParametro."</td>";
}else{
	$datoParametro = $rstParametro->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataPerfils = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarParametro(1,1,'2',1,$_GET["Id"],$id_empresa,"");
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
	getFormData("frmMantParametro");	
}
function aceptar(){
	if(setValidar('frmMantParametro')){
		g_ajaxGrabar.setURL("controlador/contParametro.php?ajax=true");
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
<form id="frmMantParametro" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
<table width="200" border="1">
<?php
reset($dataPerfils);
foreach($dataPerfils as $value){
?>
	<?php if($value["idcampo"]==2){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> title='Debe indicar la descri&oacute;n de la categoria' style="text-transform:uppercase"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("SELECT IdTabla, Descripcion FROM Tabla WHERE Estado = 'N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("SELECT idtabla, descripcion FROM Tabla WHERE Estado = 'N'",$value["descripcion"],0,'',$objMantenimiento);?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> title='Debe indicar la descri&oacute;n de la categoria' style="text-transform:uppercase"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<tr><td colspan="2" align="center"><label><input type="Checkbox" name="chk<?php echo $value["descripcion"];?>" id="chk<?php echo $value["descripcion"];?>" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>><?php echo $value["comentario"];?>
	</label></td>
    	</tr>
	<?php }?>
    <?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("SELECT Idcategoriaparametro, Descripcion FROM CategoriaParametro WHERE Estado = 'N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("SELECT Idcategoriaparametro, Descripcion FROM CategoriaParametro WHERE Estado = 'N'",$value["descripcion"],0,'',$objMantenimiento);?></td></tr>
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
	<td><a href="#" onClick="javascript:setRun('vista/listParametro','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoParametro->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoParametro->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>