<?php
require("../modelo/clsParametro.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objParametro = new clsParametro($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		echo umill($objParametro->insertarParametro($_SESSION['R_IdEmpresa'],0,$_POST['cboIdCategoriaParametro'],$_POST['cboIdTabla'],$_POST['txtDescripcion'],$_POST['txtValor'],$_POST['chkObligatorio']));
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objParametro->actualizarParametro($_POST["txtId"], $_SESSION['R_IdEmpresa'],0,$_POST['cboIdCategoriaParametro'],$_POST['cboIdTabla'],$_POST['txtDescripcion'],$_POST['txtValor'],$_POST['chkObligatorio']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objParametro->eliminarParametro($_POST["txtId"], $_POST['txtIdEmpresa'], $_POST['txtIdSucursal']));
		break;
	case "ACTUALIZAR-USER" :
		if(ob_get_length()) ob_clean();
		$rst = $objParametro->consultarParametroUser(0,$_SESSION['R_IdEmpresa'],"");
		while($dato=$rst->fetchObject()){
			if(isset($_POST["txt".$dato->idparametros])){
				$objParametro->actualizarParametroUser($dato->idparametros, $_SESSION['R_IdEmpresa'],0,$_POST["txt".$dato->idparametros]);
			}
		}
		echo "Guardado correctamente";
		break;
	case "PARAMETROS-UPDATE" :
		if(ob_get_length()) ob_clean();
		echo umill($rst = $objParametro->parametrosUpdate($_POST['cboIdSucursalOrigen'],$_POST['cboIdEmpresaDestino'],$_POST['cboIdSucursalDestino']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>