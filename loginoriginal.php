<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "Variables de Session no se pudieron crear";
	exit();
}
if(isset($_POST['cmdEnviar'])) {
	$Captcha = (string) $_POST["CAPTCHA_CODE"];
	if($Captcha != $_SESSION["R_CAPTCHA_CODE"]){
		header("location: login.php");
	}else {
		include("verificaSesion.php");
		exit();  
	}
}
?>
<HTML>
<head>
<link href="css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">
<meta http-equiv="content-type" content="text/html; charset=utf8">
</head>
<script>
function ResetIPs(){
var args=ResetIPs.arguments;
for (var zxc0=0;zxc0<args.length;zxc0++){
var fld=document.getElementsByName(args[zxc0]);
fld[0].value='';
}}
</script>
<body onload=ResetIPs('CAPTCHA_CODE')>
<BR />
<div class="titulo"><h1>BIENVENIDO A SISREST</h1><BR />Sistema Estandar para Restaurante</div>
<br>
<center>
<?php require "vista/tablaheader.php";?>
<form name="frmSesion" method="post" action="verificaSesion.php">
<table  align="center">
	<tr>
		<td rowspan="2"><img src="img/llave.png" width="83" height="76" /></td><td align="right"><b>Usuario</b></td>
		<td><input type="text" name="txtUsuario" id="txtUsuario"></td>
	</tr>
	<tr>
		<td align="right"><b>Clave</b></td>
		<td><input type="password" name="txtClave" id="txtClave"></td>
	</tr>
	<tr style="display:<?php if($_SESSION['R_ContSecure']>2) echo ''; else echo 'none';?>">
	  <td align="center" colspan="2"><img src="secure/captcha.php"></td>
      <td align="center"><b>Validar Imagen</b><br /><input name="CAPTCHA_CODE" type="text" size="15" class="caja" style="text-transform:uppercase"></td>
	</tr>
	<tr style="display:<?php if($_SESSION['R_ContSecure']>2) echo ''; else echo 'none';?>">
	  <td colspan="3" align="center"><span class="txt1">Introduzca los numeros/letras de la imagen.</span></td>
    </tr>
	<tr>
	  <td colspan="3" align="center"><input type="submit" name="cmdEnviar" id="cmdEnviar" value="Iniciar Sesi&oacute;n"></td>
    </tr>
</table>
</form>
<?php require "vista/tablafooter.php";?>
<br>
<a href="vista/regEmpresa.php">Registre su empresa</a>
</center>
<p id="copyright" class="MsoNormal" align="center" style="text-align:center">
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
</span></font></p>
</body>
</HTML>