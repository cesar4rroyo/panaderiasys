<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reporte de Pedidos por Mesero</title>
<!--CALENDARIO-->
<script src="../calendario/js/jscal2.js"></script>
    <script src="../calendario/js/lang/es.js"></script>
    <link rel="stylesheet" type="text/css" href="../calendario/css/jscal2.css" />
    <link rel="stylesheet" type="text/css" href="../calendario/css/reduce-spacing.css" />
    <link rel="stylesheet" type="text/css" href="../calendario/css/steel/steel.css" />
<!--CALENDARIO-->
<script src="../runtime/lib/aw.js" type="text/javascript"></script>
<link href="../css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="divmenu" class="titulo"><center>
  REPORTE DE CANTIDAD PEDIDOS POR MESERO
</center><br /></div>
<div><b>Busqueda:</b></div>
<div id="divbusqueda" class="textoazul">
<form action="rptPedidoxMesero.php" method="post">
 Desde: 
<input type='text' readonly="true" name = 'txtFechaInicio' id="txtFechaInicio" value = '<?php if(isset($_POST['txtFechaInicio'])) echo $_POST['txtFechaInicio']; else echo $_SESSION['R_FechaProceso'];?>' size="10" maxlength="10">
<script type="text/javascript">//<![CDATA[
      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });
      cal.manageFields("txtFechaInicio", "txtFechaInicio", "%d/%m/%Y");
    //]]></script>
 Hasta: 
 <input type='text' readonly="true" name = 'txtFechaFin' id="txtFechaFin" value = '<?php if(isset($_POST['txtFechaFin'])) echo $_POST['txtFechaFin']; else echo $_SESSION['R_FechaProceso'];?>' size="10" maxlength="10">
<script type="text/javascript">//<![CDATA[
      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });
      cal.manageFields("txtFechaFin", "txtFechaFin", "%d/%m/%Y");
    //]]></script>
  <input type="submit" value="Buscar">
</form>
</div>
<div id="divregistros"></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td>Reporte de Pedidos por Mesero</td>
</tr></table>
</div>
<script>
function buscar(fechainicio,fechafin){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "reportePedidosxMeseros");
		g_ajaxPagina.setParameter("fechainicio", fechainicio);
		g_ajaxPagina.setParameter("fechafin", fechafin);
		g_ajaxPagina.response = function(text){
			//alert(text);
			document.getElementById("divregistros").innerHTML=text;
		};
		g_ajaxPagina.request();
}
buscar('<?php if(isset($_POST['txtFechaInicio'])) echo $_POST['txtFechaInicio']; else echo $_SESSION['R_FechaProceso'];?>','<?php if(isset($_POST['txtFechaFin'])) echo $_POST['txtFechaFin']; else echo $_SESSION['R_FechaProceso'];?>');
</script>
</body>
</html>