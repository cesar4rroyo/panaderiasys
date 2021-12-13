<?php
require("../modelo/clsTablaGenerar.php");
$id_clase = $_GET["id_clase"];
//echo $id_clase;
try{
$objMantenimiento = new clsTablaGenerar($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
	$rst = $objMantenimiento->consultarTablaGenerar(1,1,'1',1,$_GET["Id"],"%%");
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
	//if(setValidar()){
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
	//}
}
</script>
</head>
<body>
<form id="frmMantTabla" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<table width="200" border="1">
<?php
reset($dataCampos);
$rst = $objMantenimiento->obtenerDataSQL("select attnum as IdCampo, attname as Campo from pg_attribute where attrelid = ".$_GET['Id']." and attstattarget=-1");
$dataCampos = $rst->fetchAll();
foreach($dataCampos as $value){
?>
	<?php
		echo $value["idcampo"]."".$value["campo"]."<br>";
	?>
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
	<td><a href="#" onClick="javascript:setRun('vista/listTabla','&id_clase=<?php echo $_GET['id_clase'];?>','frame', 'frame', 'img02')"><?php echo $datoTabla->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoTabla->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>