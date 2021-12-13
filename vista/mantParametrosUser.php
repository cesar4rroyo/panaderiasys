<?php
require("../modelo/clsParametro.php");
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

$rstParametro = $objMantenimiento->obtenerTabla();
if(is_string($rstParametro)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstParametro."</td>";
}else{
	$datoParametro = $rstParametro->fetchObject();
}

$rst = $objMantenimiento->consultarParametroUser(0,$id_empresa,"");
if(is_string($rst)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
}else{
	$dato = $rst;
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "ACTUALIZAR-USER");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmMantParametro");	
}
function aceptar(){
	if(setValidar('frmMantParametro')){
		g_ajaxGrabar.setURL("controlador/contParametro.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			alert(text);			
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}
function vistaPrevia(){
		g_ajaxGrabar.setURL("controlador/contSesion.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
       	g_ajaxGrabar.setParameter("accion", "CAMBIARESTILO");
		g_ajaxGrabar.setParameter("cboEstilo", document.getElementById('txt17').value);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			alert(text);			
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
		window.open('main.php?idclase=18&ruta=vista/mantParametrosUser','_self');		
}
</script>
<?php 
function genera_cboSucursal($seleccionado,$obj=null)
{
	$consulta = $obj->obtenerDataSQL("select idsucursal,razonsocial from sucursal where idempresa=".$_SESSION['R_IdEmpresa']);
	if(isset($onchange)) $onchange="onChange='".$onchange."'";
	echo "<select name='txt9' id='txt9'>";
	if(1==$seleccionado) $seleccionar="selected";
	echo "<option value='1' ".$seleccionar.">SISREST</option>";
	$seleccionar="";
	while($registro=$consulta->fetch())
	{
		$seleccionar="";
		if($registro[0]==$seleccionado) $seleccionar="selected";
		echo "<option value='".$registro[0]."' ".$seleccionar.">".umill($registro[1])."</option>";
	}
	echo "</select>";
}
?>
</head>
<body>
<br>
<div class="titulo"><b>PARAMETROS</b></div>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><?php echo $datoParametro->descripcion; ?></td><td>></td>
	<td><?php echo $datoParametro->descripcionmant; ?></td>
</tr></table>
</div>
<form id="frmMantParametro" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
<?php require("tablaheader.php");?>
<table class="tablaint">
<?php
$categoriatemp='';
$c=0;
while($value=$dato->fetch()){
	if($value["obligatorio"]=='N' or $value["idparametros"]==9){
	$c++;
	if($categoriatemp!=$value["categoria"]){
		$categoriatemp=$value["categoria"];?>
		<tr><td colspan="2" align="center"><b><?php echo $value["categoria"];?></b></td><tr>
	<?php }?>
    <tr class="<?php if($c%2==0) echo 'par'; else echo 'impar';?>">
    <td align=""><?php echo $value["descripcion"];?></td>
    <?php if($value["idparametros"]==9){//SUCURSAL BASE?>
        <td align="center"><?php genera_cboSucursal($_SESSION['R_IdSucursal'],$objMantenimiento);?>        </td>
    <?php }elseif($value["idparametros"]==17){//ESTILO?>
        <td align="center"><select id="txt<?php echo $value["idparametros"];?>" name="txt<?php echo $value["idparametros"];?>">
        <option value="AZUL" <?php if($value["valor"]=='AZUL') echo 'selected';?>>AZUL</option>
        <option value="VERDE" <?php if($value["valor"]=='VERDE') echo 'selected';?>>VERDE</option>
        </select>&nbsp;<img src="img/preview_f2.png" width="20" height="20" onClick="vistaPrevia();" style="cursor:pointer" alt="Vista Previa" title="Vista Previa">
        </td>
    <?php }elseif($value["idparametros"]==18){//CERRAR POR INACTIVIDAD?>
        <td align="center"><select id="txt<?php echo $value["idparametros"];?>" name="txt<?php echo $value["idparametros"];?>">
        <option value="10" <?php if($value["valor"]=='10') echo 'selected';?>>10 Minutos</option>
        <option value="20" <?php if($value["valor"]=='20') echo 'selected';?>>20 Minutos</option>
        <option value="30" <?php if($value["valor"]=='30') echo 'selected';?>>30 Minutos</option>
        <option value="45" <?php if($value["valor"]=='45') echo 'selected';?>>45 Minutos</option>
        </select>
        </td>
	<?php }elseif($value["idparametros"]==19){//TIEMPO DE ESPERA RESERVA?>
        <td align="center"><select id="txt<?php echo $value["idparametros"];?>" name="txt<?php echo $value["idparametros"];?>">
        <option value="5" <?php if($value["valor"]=='5') echo 'selected';?>>5 Minutos</option>
        <option value="10" <?php if($value["valor"]=='10') echo 'selected';?>>10 Minutos</option>
        <option value="15" <?php if($value["valor"]=='15') echo 'selected';?>>15 Minutos</option>
        <option value="20" <?php if($value["valor"]=='20') echo 'selected';?>>20 Minutos</option>
        <option value="25" <?php if($value["valor"]=='25') echo 'selected';?>>25 Minutos</option>
        <option value="30" <?php if($value["valor"]=='30') echo 'selected';?>>30 Minutos</option>
        </select>
        </td>
    <?php }elseif($value["idparametros"]==22){//PRECIOVENTA?>
        <td align="center"><select id="txt<?php echo $value["idparametros"];?>" name="txt<?php echo $value["idparametros"];?>">
        <option value="J" <?php if($value["valor"]=='J') echo 'selected';?>>JUEVES</option>
        <option value="V" <?php if($value["valor"]=='V') echo 'selected';?>>VIERNES</option>
        <option value="S" <?php if($value["valor"]=='S') echo 'selected';?>>SABADO</option>
        <option value="D" <?php if($value["valor"]=='D') echo 'selected';?>>DOMINGO</option>
        </select>
        </td>
	<?php }else{?>
        <td align="center"><input type="checkbox" id="txt<?php echo $value["idparametros"];?>" name="txt<?php echo $value["idparametros"];?>" <?php if($value["valor"]=='S') echo 'checked';?>></td>
   	<?php }?>
    <tr>
	<?php }?>
<?php }?>
	<tr>
	<td colspan="2" align="center"><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()">&nbsp;<input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript: window.open('main.php','_self')"></td>
	</tr>
</table>
<?php require("tablafooter.php");?>
</form>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><?php echo $datoParametro->descripcion; ?></td><td>></td>
	<td><?php echo $datoParametro->descripcionmant; ?></td>
</tr></table>
</div>
</body>
</HTML>