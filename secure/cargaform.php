<?php
include("funcion_secure.php");
$posiciones=rand_pos(0,9,0); 
$columas=3; 
$contador = 1; 
?>
<form action="login.php"  method="POST" name="cuenta"  onSubmit="return validacuenta(cuenta)">
<table width="250" border="0" align="center" cellpadding="1" cellspacing="0">
<tr><td  colspan="2"><div id='error' ></div></td></tr>
  <tr><td  colspan="2"><h2>iniciar session
</h2></td></tr>
  <tr>
    <td rowspan="2" class=txttitulo>Usuario :</td>
    <td></td>
  </tr>
  <tr>
<td><input type=text name=txtusuario id=txtusuario size=15 class="cajausuario" maxlength="15"></td></tr>
  <tr><td colspan="3"></td></tr>
<tr><td  rowspan="3">
<table  width="85" border="0"  align="center" cellpadding="1" cellspacing="1"><tr>
<?
foreach($posiciones as $i){
if ($contador > 3) {
echo "</td></tr><tr>";
$contador = 1;
}
?>
<td class="teclado" width="25px" height="25px" align="center" onmouseover="this.style.cursor='pointer'"
 onmouseout="this.style.cursor='default'" onclick="RetornarValor('txtclave','<? echo $i?>')";><? echo $i ?></td>
<?
$contador++;
}
?>
<td class="limpiar" colspan="2" align="center" onmouseover="this.style.cursor='hand'" onmouseout="this.style.cursor='pointer'"
onclick="Limpiar('txtclave')">Limpiar</td></tr></table>
</td>
 <td width="203" class="txt1">clave (6 digitos):</td></tr>
 <tr><td class="txt1">
<input class="caja" name="txtclave" type="password" maxlength="6" size="15"  id="txtclave" readonly></td>
  <tr><td class="txt1">ingresa tu clave<br> usando el teclado virtual</td></tr>
  <tr><td class="txttitulo" colspan="2">Validar Imagen: </td></tr>
  <tr>
    <td><img src="secure/captcha.php" width="100" height="40" vspace="3"></td>
    <td><input name="CAPTCHA_CODE" type="text" size="15" class="caja"></td></tr>
  <tr><td colspan="2" class="txt1" align="center">Introduzca los numeros/letras de la imagen.</td></tr>
  <tr><td colspan="2" align="center"><input name="submit" type="submit" value="Ingresar" class="boton" id="submit"></td></tr>
  <tr><td></td></tr>
</table>
</form>
