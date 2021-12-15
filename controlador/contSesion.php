<?php
require_once('../modelo/clsGeneral.php');

$accion = $_POST["accion"];

if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}

switch($accion){
	case "CAMBIARSUCURSAL" :
		if(ob_get_length()) ob_clean();
		session_start();
        $objPermiso = new clsGeneral(0, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
        $rst = $objPermiso->obtenerDataSQL("select idsucursal,ruc,razonsocial,direccion from sucursal where idempresa=".$_SESSION['R_IdEmpresa']." and idsucursal = ".$_POST['cboSucursal']);
        $dato = $rst->fetchObject();
		$_SESSION['R_IdSucursal']=$_POST['cboSucursal'];
        $_SESSION['R_Caja']=$_POST['cboCaja'];
        $_SESSION['R_DireccionSucursal'] = $dato->direccion;
        $_SESSION['R_NombreSucursal'] = $dato->razonsocial;
        $_SESSION['R_RucSucursal'] = $dato->ruc;
		echo "Guardado Correctamente";
		exit();
	case "CAMBIARESTILO" :
		if(ob_get_length()) ob_clean();
		session_start();
		$_SESSION['R_Estilo']='estilo'.strtolower($_POST['cboEstilo']);
		echo "Guardado Correctamente";
		exit();
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>