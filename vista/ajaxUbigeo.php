<?php
$action = $_POST["action"];
$seleccionado = $_POST["seleccionado"];
$iddpto = $_POST["iddpto"];
$idprov = $_POST["idprov"];
if(isset($_POST["Disabled"]))
	$Disabled = $_POST["Disabled"];
else
	$Disabled="";

$id_clase=36;
require("../modelo/clsUbigeo.php");
$oubigeo = new clsUbigeo($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

if($action=="verDpto"){
	$consulta = $oubigeo->consultarUbigeoCombo(NULL,NULL,2);

	$Dptos="<select name='cboDpto' id='cboDpto' onChange='VerProv()' ".$Disabled.">";
	while($registro=$consulta->fetch()){
		$seleccionar="";
		if($registro[0]==$seleccionado) $seleccionar="selected='selected'";
		$Dptos=$Dptos."<option value='".$registro[0]."' ".$seleccionar.">".$registro[1]."</option>";
	}
	$Dptos=$Dptos."</select>";
	$Dptos=umill($Dptos);
	echo $Dptos;
}

if($action=="verProv"){
	$consulta = $oubigeo->consultarUbigeoCombo(NULL,$iddpto,3);

	$Provs="<select name='cboProv' id='cboProv' onChange='VerDist()' ".$Disabled.">";
	while($registro=$consulta->fetch()){
		$seleccionar="";
		if($registro[0]==$seleccionado) $seleccionar="selected='selected'";
		$Provs=$Provs."<option value='".$registro[0]."' ".$seleccionar.">".$registro[1]."</option>";
	}
	$Provs=$Provs."</select>";
	$Provs=umill($Provs);
	echo $Provs;
}

if($action=="verDist"){
	$consulta = $oubigeo->consultarUbigeoCombo(NULL,$idprov,4);

	$Dist="<select name='cboDist' id='cboDist' ".$Disabled.">";
	while($registro=$consulta->fetch()){
		$seleccionar="";
		if($registro[0]==$seleccionado) $seleccionar="selected='selected'";
		$Dist=$Dist."<option value='".$registro[0]."' ".$seleccionar.">".$registro[1]."</option>";
	}
	$Dist=$Dist."</select>";
	$Dist=umill($Dist);
	echo $Dist;
}
?>