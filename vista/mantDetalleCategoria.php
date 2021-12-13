<?php
require("../modelo/clsCategoria.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET["id_cliente"];
$id_categoria=$_GET["idcategoria"];
$idsucursal=$_GET["idsucursal"];
//echo $id_cliente;
try{
$objMantenimiento = new clsCategoria($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['NombreUsuario'],$_SESSION['Clave']);
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
	$rst = $objMantenimiento->obtenerDataSQL("select * from detallecategoria where iddetallecategoria=".$_GET["Id"]);
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetchObject();
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
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>DETALLE");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmMantDetalleCategoria");
}

function aceptar(){
	if(setValidar("frmMantDetalleCategoria")){
	    var idcategoria=<?=$id_categoria?>;
        var idsucursal=<?=$idsucursal?>;
		g_ajaxGrabar.setURL("controlador/contCategoria.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			setRun('vista/listDetalleCategoria','&clase=Categoria&id_clase=<?php echo $id_clase;?>&idcategoria=' + idcategoria + '&IdSucursal=' + idsucursal + '&id_cliente='+<?php echo $id_cliente;?>,'frame','carga','imgloading');
			alert(text);			
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}

function cerrar(){
    setRun('vista/listDetalleCategoria','&clase=Categoria&id_clase=<?php echo $id_clase;?>&idcategoria=' + <?=$_GET["idcategoria"]?> + '&IdSucursal=' + <?=$_GET["idsucursal"]?> + '&id_cliente='+<?php echo $id_cliente;?>,'frame','carga','imgloading');    
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
<form id="frmMantDetalleCategoria" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR') echo $_GET['Id'];?>">
</head>
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtIdCategoria" name = "txtIdCategoria" value = "<?php echo $id_categoria;?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $idsucursal;?>">
<table width="200">
    <tr>
        <td>Descripcion:</td>
        <td><input type="text" id="txtDescripcion" name="txtDescripcion" value="<?php if($_GET["accion"]=="ACTUALIZAR") ECHO $dato->descripcion;?>" /></td>
    </tr>
    <tr>
        <td>Abreviatura:</td>
        <td><input type="text" id="txtAbreviatura" name="txtAbreviatura" value="<?php if($_GET["accion"]=="ACTUALIZAR") ECHO $dato->abrevitura;?>" maxlength="6" size="6" /></td>
    </tr>
</table>
<center><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()">&nbsp;<input id="cmdCancelar" type="button" value="CANCELAR" onClick="cerrar()"></center>

</form>
<?php require("tablafooter.php");?>
</body>
</HTML>