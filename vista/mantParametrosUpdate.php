<?php
require("../modelo/clsParametro.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = $_SESSION['R_IdEmpresa'];
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
//echo $id_clase;
try{
$objMantenimiento = new clsParametro($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "PARAMETROS-UPDATE");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmMantParametro");	
}
function aceptar(){
	if(setValidar('frmMantParametro')){
		if(document.getElementById('cboIdSucursalOrigen').value!=document.getElementById('cboIdSucursalDestino').value){
			g_ajaxGrabar.setURL("controlador/contParametro.php?ajax=true");
			g_ajaxGrabar.setRequestMethod("POST");
			setParametros();
				
			g_ajaxGrabar.response = function(text){
				loading(false, "loading");
				alert(text);			
			};
			g_ajaxGrabar.request();
			loading(true, "loading", "frame", "line.gif",true);
		}else{
			alert('La sucursal de destino debe ser distinta a la de origen!!!');
		}
	}
}
function genera_cboSucursal(idempresa,seleccionado,nombre,disabled){
		var recipiente = document.getElementById('div'+nombre);
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setAsync(false);
		g_ajaxPagina.setURL("vista/ajaxSucursal.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboSucursal");
		g_ajaxPagina.setParameter("IdEmpresa", idempresa);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("disabled", disabled);
		g_ajaxPagina.setParameter("nombre", nombre);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
		};
		g_ajaxPagina.request();
}
genera_cboSucursal(1,0,"IdSucursalOrigen")
genera_cboSucursal(1,0,"IdSucursalDestino")
</script>
</head>
<body>
<br>
<div class="titulo"><b>PARAMETROS UPDATE</b></div>
<form id="frmMantParametro" action="" method="POST">
<center>
<?php require("tablaheader.php");?>
<table class="tablaint">
	<tr>
    <td>Empresa Origen</td><td>Sucursal Origen</td>
    </tr>
    <tr><td><?php echo genera_cboGeneralSQL("select * from empresa where estado='N' order by 2 asc","IdEmpresaOrigen",1,'',$objMantenimiento,'genera_cboSucursal(this.value,0,"IdSucursalOrigen")');?></td><td><div id="divIdSucursalOrigen"></div></td>
    </tr>
    <tr><td colspan="2"><br></td></tr>
    <tr>
    <td>Empresa Destino</td><td>Sucursal Destino</td>
    </tr>
    <tr><td><?php echo genera_cboGeneralSQL("select * from empresa where estado='N' order by 2 asc","IdEmpresaDestino",1,'',$objMantenimiento,'genera_cboSucursal(this.value,0,"IdSucursalDestino")');?></td><td><div id="divIdSucursalDestino"></div></td>
    </tr> 
    <tr><td colspan="2"><br></td></tr>   
	<tr>
	<td colspan="2" align="center"><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()">&nbsp;<input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript: window.open('main.php','_self')"></td>
	</tr>
</table>
<?php require("tablafooter.php");?>
</center>
</form>
</body>
</HTML>