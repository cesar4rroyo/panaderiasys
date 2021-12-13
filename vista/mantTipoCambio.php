<?php
require("../modelo/clsTipoCambio.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsTipoCambio($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstTipoCambio = $objMantenimiento->obtenerTabla();
if(is_string($rstTipoCambio)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstTipoCambio."</td>";
}else{
	$datoTipoCambio = $rstTipoCambio->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataTipoCambios = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarTipoCambio(1,1,'2',1,$_GET["Id"],"");
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
	g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	/*g_ajaxGrabar.setParameter("txtIdSucursal", document.getElementById("txtIdSucursal").value);
	g_ajaxGrabar.setParameter("txtFecha", document.getElementById("txtFecha").value);*/
	g_ajaxGrabar.setParameter("txtMonto", document.getElementById("txtMonto").value);
	}
function aceptar(){
	if(setValidar("frmTipoCambio")){
		g_ajaxGrabar.setURL("controlador/contTipoCambio.php?ajax=true");
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
<form id="frmTipoCambio" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="200" border="1">
<?php
reset($dataTipoCambios);
foreach($dataTipoCambios as $value){
?>
	<?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
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
	<td><a href="#" onClick="javascript:setRun('vista/listTipoCambio','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoTipoCambio->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoTipoCambio->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>