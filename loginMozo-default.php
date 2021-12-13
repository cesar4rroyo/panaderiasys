<?php
session_start();

if(!$_SESSION['R_ini_ses']){
	//echo "Variables de Session no se pudieron crear";
	header("location: indexmozo.php");
}
if(isset($_POST['cmdEnviar'])) {
	$Captcha = (string) $_POST["CAPTCHA_CODE"];
	if($Captcha != $_SESSION["R_CAPTCHA_CODE"]){
		header("location: loginMozo.php");
	}else {
		include("verificaSesion.php");
		exit();  
	}
}

$_SESSION["R_Formulario"]="Mozo";

require_once 'modelo/cado.php';
$acceso= new clsAccesoDatos('','');
$acceso->gIdTabla = 46;
$acceso->gIdSucursal = 1;
$sql = "Select Distinct P.idsucursal, P.IdPersona, Apellidos,Nombres, CASE WHEN tipopersona='VARIOS' THEN 'DNI' ELSE 'RUC' END as tipodoc, nrodoc,us.nombreusuario as usuario 
 From Persona P 
 inner join PersonaMaestro PM on PM.IdPersonaMaestro=P.IdPersonaMaestro 
 inner join rolpersona rp on rp.idpersona=P.idpersona and rp.idsucursal=P.idsucursal 
 INNER JOIN SUCURSAL s on s.idsucursal=P.idsucursal
 INNER JOIN usuario us on us.idpersona=P.idpersona and us.idsucursal=s.idsucursal   
 Where P.estado='N' ";
$sql .= " and idrol in (1) and s.idempresa=1 and us.idperfil=5";
//Emprea 1 por el potrero y perfil 5 por mozos
$rst = $acceso->obtenerDataSQL($sql);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/estiloazul/estiloimprimir.css"/>
<link rel="shortcut icon" href="img/24 Custom.ico" />
<title>SISREST <?php echo $_SESSION['R_Version'];?></title>
<script>
function ingresar(numero){
    document.getElementById("txtClave").value=document.getElementById("txtClave").value + numero;
}
function enviar(){
    frmSesion.submit();
}
function usuario(us){
    document.getElementById("txtUsuario").value=us;
}
</script>
<body >
<center>
<?php require("vista/fun.php"); ?>
<?php require "vista/tablaheaderzoom.php";?>
<button style="display:inline-block;float:right;" onclick="javascript:window.close();"><img src="img/cerrar.png" width="16px" height="16px"></button>
<h1 align="center" style="font-size: xx-large;">Sistema Est&aacute;ndar para Restaurante - Plataforma Mozo</h1>
<form name="frmSesion" method="post" action="verificaSesion.php">
<table class="zoom2">
<tr class="zoom2">
<td class="zoom2">
<input type="hidden" name="Origen" id="Origen" value="Mozo" />
<input type="hidden" id="txtCampo" value="txtClave" />
<table  align="center" class="zoom2">
    <tr>
        <td class="zoom2"></td><td align="right" class="zoom2">Usuario:</td>
        <td class="zoom2"><input class="zoom2" type="text" name="txtUsuario" id="txtUsuario" readonly="true" value="" size="8" maxlength="100" /></td>
    </tr>
	<tr class="zoom2">
		<td rowspan="2" class="zoom2"></td><td align="right"><b class="zoom2">C&oacute;digo:</b></td>
		<td class="zoom2"><input  class="zoom2" type="password" name="txtClave" id="txtClave" size="8" maxlength="10"></td>
	</tr>
    <tr class="zoom2">
        <td class="zoom2"><img src="img/llave.png" width="150px" height="150px" onclick="ingresar()" /></td>
        <td align="center" class="zoom2">
            <? genera_bloqueNumerico("zoom2","txtClave","ingresar","enviar");?>
        </td>
    </tr>
</table>
</td>
<td class="zoom2" style="float:left;width:auto;" style="list-style-position: inherit;">
<div style="width: auto;">
<fieldset class="zoom2">
<legend class="zoom2">Meseros</legend>
<table>
<tr>
<td>
<?php
$cont=1;
echo "<table>";
while($reg=$rst->fetchObject()){
     if(($cont-1)%2==0){
           echo "<tr>";
     }
     $nombre = explode(" ",$reg->nombres);
     $apellido = explode(" ", $reg->apellidos);
     echo "<td><input class='button' type='button' value = '".substr($apellido[0],0,1).". ".substr($nombre[0],0,10)."' onclick=usuario('".$reg->usuario."') /></td>";
     if($cont%2==0)
        echo "</tr>";
     $cont++;    
}
echo "</table>";
?>
</td>
</tr>
</table>
</fieldset>
</div>
</td>
</tr>
</table>
</form>
<?php require "vista/tablafooterzoom.php";?>
</center>
</body>
</head>
</html>