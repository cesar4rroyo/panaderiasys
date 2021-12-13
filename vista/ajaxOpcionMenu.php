<?php
session_start();
$action = $_POST["accion"];
require("../modelo/clsOpcionMenu.php");
$ObjOpcionMenu = new clsOpcionMenu(13,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$onchange=$_POST['change'];
if(isset($onchange)) $onchange="onClick='".$onchange."' onChange='".$onchange."'";

if($action=="genera_cboModulo"){

	$consulta = $ObjOpcionMenu->consultarModuloAjax($_POST["idperfil"]);

	$Cadena="<select name='cboIdModulo' id='cboIdModulo' ".$onchange.">";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		if($registro->idmodulo==$_POST['seleccionado']){ 
			$seleccionar="Selected";
		}else{$seleccionar="";}
		$Cadena=$Cadena."<option value='".$registro->idmodulo."' ".$seleccionar.">".$registro->modulo."</option>";
	}
	}else{
		$Cadena=$Cadena."<option value='0'>Ninguna</option>";
	}
	$Cadena=$Cadena."</select>";
	$Cadena=utf8_encode($Cadena);
	echo $Cadena;
}

if($action=="genera_cboMenuPrincipal"){

	$consulta = $ObjOpcionMenu->consultarMenuPrincipalAjax($_POST["idperfil"], $_POST["idmodulo"]);

	$Cadena="<select name='cboIdMenuPrincipal' id='cboIdMenuPrincipal' ".$onchange.">";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		if($registro->idmenuprincipal==$_POST['seleccionado']){ 
			$seleccionar="Selected";
		}else{$seleccionar="";}
		$Cadena=$Cadena."<option value='".$registro->idmenuprincipal."' ".$seleccionar.">".$registro->menuprincipal."</option>";
	}
	}else{
		$Cadena=$Cadena."<option value='0'>Seleccione un Modulo</option>";
	}
	$Cadena=$Cadena."</select>";
	$Cadena=utf8_encode($Cadena);
	echo $Cadena;
}

if($action=="genera_cboOpcionMenu"){

	$consulta = $ObjOpcionMenu->consultarOpcionMenuAjax($_POST["idperfil"], $_POST["idmodulo"],$_POST["idmenuprincipal"]);

	$Cadena="<select name='cboOpcionMenu' id='cboOpcionMenu'>";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		if($registro->idopcionmenu==$_POST['seleccionado']){ 
			$seleccionar="Selected";
		}else{$seleccionar="";}
		$Cadena=$Cadena."<option value='".$registro->idopcionmenu."' ".$seleccionar.">".$registro->descripcion."</option>";
	}
	}else{
		$Cadena=$Cadena."<option value='0'>Seleccione un Men&uacute; Principal</option>";
	}
	$Cadena=$Cadena."</select>";
	$Cadena=utf8_encode($Cadena);
	echo $Cadena;
}
?>