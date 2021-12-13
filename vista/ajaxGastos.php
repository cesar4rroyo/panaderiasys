<?php
session_start();
require("../modelo/clsDetalleAlmacen.php");
$accion = $_POST["accion"];
switch($accion){
case "generaNumero" :
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$numero = $ObjDetalleAlmacen->generaNumeroSinSerie(7,$_POST["IdTipoDocumento"],substr($_SESSION["R_FechaProceso"],3,2));

	echo "vnumero='".$numero."';";
	break;
case "genera_cboConceptoPago" :
	if($_POST["IdTipoDocumento"]==13) $tipo='I';
	if($_POST["IdTipoDocumento"]==14) $tipo='E';
	require("../modelo/clsConceptoPago.php");
	$ObjConceptoPago = new clsconceptoPago(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjConceptoPago->consultarConceptoPagoxTipoDocumento($tipo);

	$cadena="<select name='cboConceptoPago' id='cboConceptoPago'>";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		if($registro->idconceptopago!=1 and $registro->idconceptopago!=2 and $registro->idconceptopago!=17 and $registro->idconceptopago!=18){
		$cadena=$cadena."<option value='".$registro->idconceptopago."' ".$seleccionar.">".$registro->descripcion."</option>";
		}
	}
	}else{
		$cadena=$cadena."<option value='0'>No hay Conceptos de Pago</option>";
	}
	$cadena=$cadena."</select>";
	$cadena=utf8_encode($cadena);
	echo $cadena;
	break;
case "genera_cboConceptoPago2" :
	if($_POST["IdTipoDocumento"]==13) $tipo='I';
	if($_POST["IdTipoDocumento"]==14) $tipo='E';
	require("../modelo/clsConceptoPago.php");
	$ObjConceptoPago = new clsconceptoPago(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjConceptoPago->consultarConceptoPagoxTipoDocumento($tipo);

	$cadena="<select name='cboConceptoPago' id='cboConceptoPago'>";
	if(isset($_POST["todos"])) $cadena.="<option value='0'>".$_POST["todos"]."</option>";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		//if($registro->idconceptopago!=1 and $registro->idconceptopago!=2 and $registro->idconceptopago!=17 and $registro->idconceptopago!=18){
		$cadena=$cadena."<option value='".$registro->idconceptopago."' ".$seleccionar.">".$registro->descripcion."</option>";
		//}
	}
	}else{
		$cadena=$cadena."<option value='0'>No hay Conceptos de Pago</option>";
	}
	$cadena=$cadena."</select>";
	$cadena=utf8_encode($cadena);
	echo $cadena;
	break;

default:
	echo "Error en el Servidor: Operacion no Implementada.";
	exit();
}
?>