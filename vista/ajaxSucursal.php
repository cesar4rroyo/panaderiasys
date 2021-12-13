<?php
$action = $_POST["accion"];

$id_clase=58;
require("../modelo/clsSucursal.php");
if(isset($_SESSION['R_IdSucursal']))
	$ObjSucursal = new clsSucursal($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
else{
	$ObjSucursal = new clsSucursal($id_clase,1,$_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
}
require("../modelo/clsUsuario.php");
if(isset($_SESSION['R_IdSucursal']))
	$ObjUsuario = new clsUsuario($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
else{
	$ObjUsuario = new clsUsuario($id_clase,1,$_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
}
//$nrodoc='444454444';
//$action="BuscaxNroDoc";
if($action=="verificaEmpresaxRuc"){
	$ruc = $_POST["ruc"];
	$rst = $ObjSucursal->buscarEmpresaxRuc($ruc);
	$registro=$rst->fetchAll();
	echo "vCant=".count($registro).";";
}
elseif($action=="verificaNombreUsuario"){
	$nombreusuario = trim($_POST["nombreusuario"]);
	$existe = $ObjUsuario->verificaExisteNombreUsuario($nombreusuario);
	echo "vCant=".$existe.";";
}
elseif($action=="genera_cboSucursal"){
	$seleccionado=$_POST['seleccionado'];
	$disabled=$_POST['disabled'];
	$nombre=$_POST['nombre'];
	
	$consulta = $ObjSucursal->consultarxIdEmpresa($_POST["IdEmpresa"]);

	$txt="<select name='cbo".$nombre."' id='cbo".$nombre."' title='Debe indicar una sucursal' ".$disabled.">";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		$seleccionar="";
		if($registro->idmesa==$seleccionado) $seleccionar="selected";
		$txt=$txt."<option value='".$registro->idsucursal."' ".$seleccionar.">".$registro->razonsocial."</option>";
	}
	}else{
		$txt=$txt."<option value='0'>No hay sucursales disponibles</option>";
	}
	$txt=$txt."</select>";
	$txt=utf8_encode($txt);
	echo $txt;
}
?>