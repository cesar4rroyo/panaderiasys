<?php
require("../modelo/clsSalon.php");
require("../modelo/clsBitacora.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objSalon = new clsSalon($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$existe=$objSalon->verificaExisteDescripcion($_POST["txtDescripcion"]);
		if($existe==0){
		$rst= $objSalon->insertarSalon(strtoupper(trim($_POST["txtDescripcion"])), strtoupper(trim($_POST["txtAbreviatura"])), $_POST["txtImagen"],$_POST['txtIdSucursal']);
		$dax = $rst->fetchObject();
		$idregistro = $dax->idsalon;
		//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Descripcion=>'.strtoupper($_POST["txtDescripcion"]).'; Abreviatura=>'.strtoupper($_POST["txtAbreviatura"]).'; Imagen=>'.$_POST["txtImagen"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Salon","IdSalon",$_POST["txtId"]);
		$dax = $rt->fetchObject();

		$objSalon->actualizarSalon($_POST["txtId"],$_POST['txtIdSucursal'], strtoupper(trim($_POST["txtDescripcion"])), strtoupper(trim($_POST["txtAbreviatura"])), $_POST["txtImagen"], ($_POST["cboIdMesaLibre"]?$_POST["cboIdMesaLibre"]:0));
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Descripcion=> De: '. $dax->descripcion.' a: '.strtoupper($_POST["txtDescripcion"]).'; Abreviatura=> De: '. $dax->abreviatura. ' a: '.strtoupper($_POST["txtAbreviatura"]).'; Imagen=> De: '. $dax->imagen. ' a: '.strtoupper($_POST["txtImagen"]).'; IdMesaLibre=> De: '. $dax->idmesalibre. ' a: '.strtoupper($_POST["cboIdMesaLibre"]), $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objSalon->eliminarSalon($_POST["txtId"], $_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "LISTAR":
		require("../modelo/clsMovimiento.php");
		if(ob_get_length()) ob_clean();
		$objMantenimiento = new clsMovimiento(46,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		$rst = $objMantenimiento->obtenerDataSQL("select * from salon where idsalon in (2,1) and estado='N' and idsucursal=".$_SESSION['R_IdSucursal']);
		$data = array();
		while($dato=$rst->fetchObject() and $c<6){
			$data[] = array($dato->idsalon,$dato->abreviatura);
		}
		echo json_encode($data);
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>