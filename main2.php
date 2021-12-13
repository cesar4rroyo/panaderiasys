<?php
session_start();
require_once 'vista/fun.php';
if(strstr($_SERVER['HTTP_USER_AGENT'],'IE')){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "XXXX://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">
<!--<link href="css/estiloverde/estiloverde.css" rel="stylesheet" type="text/css">-->
<meta http-equiv="content-type" content="text/html; charset=utf8">
<!--FUNCIONES AUTOCOMPLETAR: LAS CUALES PODEMOS REUTILIZAR EN DISTINTOS ARCHIVOS-->
<script type="text/javascript" src="js/autocompletar.js"></script>
<!---->
<!--CALENDARIO-->
<script src="calendario/js/jscal2.js"></script>
    <script src="calendario/js/lang/es.js"></script>
    <link rel="stylesheet" type="text/css" href="calendario/css/jscal2.css" />
    <link rel="stylesheet" type="text/css" href="calendario/css/reduce-spacing.css" />
    <link rel="stylesheet" type="text/css" href="calendario/css/steel/steel.css" />
<!--CALENDARIO-->
<script src="runtime/lib/aw.js" type="text/javascript"></script>
<link href="runtime/styles/system/aw.css" rel="stylesheet">
<script type="text/javascript" src="js/fun.js"></script>
<script>
var fechainicio=new Date();
var g_bandera = null;
var g_ajaxGrabar = null;
function setRun(url, par, div, msj, img){
	var fechainicio=new Date();
	var recipiente = document.getElementById(div);
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL(url + ".php?ajax=true&"+par);
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.response = function(xform){
		
		var s = "", r = /<script>([\s\S]+)<\/script>/mi;
		if (xform.match(r)){
			s = RegExp.$1; // extract script
			xform = xform.replace(r, "");
		}
		recipiente.innerHTML = xform;	
		// Creo el nuevo JS
		var etiquetaScript=document.createElement("script");
		document.getElementsByTagName("head")[0].appendChild(etiquetaScript);
		etiquetaScript.text=s;
		var fechafin=new Date();
		loading(false, img);
		//"Fecha ini "+fechainicio +" fin "+fechafin
		//alert("Fecha "+fechainicio+" fin " + fechafin);
		//alert(xform);
	};
	g_ajaxPagina.request();
	loading(true, img, msj, "linea.gif",true);
}		
function muestraEnlaces(id){
	if(document.getElementById(id).className=="oculta"){
		document.getElementById(id).className = "muestra";
		document.getElementById("img"+id).src = "img/i_colpse.png";
	}else{
		document.getElementById(id).className = "oculta";
		document.getElementById("img"+id).src = "img/i_expand.png";
	}
}
</script>
<style type="text/css"> 
.oculta {
 display:none;
}

.muestra {
 display:block;
}
</style>
<title>SISREST - Sistema Est&aacute;ndar para Restaurante</title>
<link rel="shortcut icon" href="img/24 Custom.ico" />
</head>
<body>
<table width="100%" border="1" align="center">
<tr>
    	<td class="titulo">SISREST - Sistema Est&aacute;ndar para Restaurante</td>
</tr>
  	<tr>
    	<td>
  <div id="barramenusup"> <!-- inicio menu superior -->
  <ul style="float:left">
    <li><b>Empresa:</b>&nbsp;<img src="img/empresa.png" alt="perfil" height="16" width="16">&nbsp;<?php echo $_SESSION['R_NombreEmpresa']?></li>
    <li><b>Establecimiento:</b>&nbsp;<img src="img/sucursal.png" alt="perfil" height="16" width="16">&nbsp;<?php echo $_SESSION['R_NombreSucursal']?></li>
  </ul>
  <ul style="float:right">
    <li><b>Bienvenido:</b>&nbsp;<img src="img/user_suit.png" alt="usuario" height="16" width="16">&nbsp;<?php echo $_SESSION['R_NombreUsuario']?></li>
    <li><b>Perfil:</b>&nbsp;<img src="img/roles.gif" alt="perfil" height="16" width="16">&nbsp;<?php echo $_SESSION['R_Perfil']?></li>
    <li><a href='#' onclick="javascript: window.close()">&nbsp;<img src="img/popup_close.gif" alt="cerrar" width="16" height="16" longdesc="Cerrar ventana">&nbsp;Cerrar </a></li>
  </ul>
  </div><!-- inicio menu superior -->
</td>
  	</tr>
  	<tr>
   	  <td valign="top"><div id="carga"></div>
		<div id="cargagrilla"></div>
		<div id="frame"></div>
	</td>
  	</tr>
 	<tr>
    		<td><p id="copyright" class="MsoNormal" align="center" style="text-align:center">
<font color="#666666">
<span style="font-size: 8pt; font-family: Calibri" lang="EN-US">www.aicnet-system.com<br>Chiclayo - Per&uacute;<br>
&nbsp;AICNET System SRL  Copyright &copy; 2011
<br>979368623 - 979974406<br />
<a style="font-size:8pt" href="#" onClick="javascript: if(DivEquipo.style.display!='')DivEquipo.style.display='';else DivEquipo.style.display='none';">Desarrolladores</a>
<div id='DivEquipo' align="center" style="display:none">
<br>
<br>
<strong>Equipo de desarrollo:</strong><br>
Geynen Rossler Montenegro Cochas<br>
Vladimir Franz Torres Mirez<br>
</div>
</span></font></p></td>
	</tr>
</table>
<script>
	setRun("vista/<?php echo $_GET['vista'];?>","&id_clase=<?php echo $_GET['idtablavista'];?>","frame","carga","imgloading");

</script>
</body>
</HTML>