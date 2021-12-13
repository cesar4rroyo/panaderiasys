<?php 
session_start();
$id_clase = $_GET["id_clase"];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Diagrama de Mesas</title>
<script>
function genera_cboMesas(idsalon){
		var recipiente = document.getElementById('divdiagramaMesa');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_diagramaMesas");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
		};
		g_ajaxPagina.request();
}
genera_cboMesas(0);
</script>
</head>

<body>
<br />
<?php require("tablaheader.php");?>
<center>Sal&oacute;n
<?php 
require("fun.php");
require("../modelo/clsMovimiento.php");
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
echo genera_cboGeneralSQL("select * from salon where estado='N' and idsucursal=".$_SESSION['R_IdSucursal'],'IdSalon',0,'',$objMantenimiento,'genera_cboMesas(this.value)');?>
</center>
<div id="divdiagramaMesa"></div>
<table width="100%">
<td>
<p align="left">
<img src="img/ocupado.png" width="16" height="16"/> Ocupado&nbsp;&nbsp;<img src="img/reservado.png" width="16" height="16"/> Reservado
</p>
</td><td>
<p align="right">
<input type="button" name="button2" id="button2" value="Cancelar" onClick="javascript:document.getElementById('cargamant').innerHTML='';buscar();">
</p>
</td>
</table>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/frmMesas', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');">Pedido</a></td><td>></td>
    <td>Mesas</td>
</tr></table>
</div>
<hr>
</body>
</html>