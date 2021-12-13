<?php
require("../modelo/clsTurno.php");
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
$objTurno = new clsTurno($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//echo umill($objTurno->insertarTurno($_POST["txtNombre"], $_POST["txtAbreviatura"], $_POST["txtHoraInicio"], $_POST["txtHoraFin"]));
		$existe=$objTurno->verificaExisteNombre($_POST["txtNombre"]);
		if($existe==0){
		$rst=$objTurno->insertarTurno(strtoupper($_POST["txtNombre"]), strtoupper($_POST["txtAbreviatura"]), $_POST["txtHoraInicio"], $_POST["txtHoraFin"]);
		$dax = $rst->fetchObject();
		$idregistro = $dax->idturno;
			//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Nombre=>'.strtoupper($_POST["txtNombre"]).'; Abreviatura=>'.strtoupper($_POST["txtAbreviatura"]).'; HoraInicio=>'.$_POST["txtHoraInicio"].'; HoraFin=>'.$_POST["txtHoraFin"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Turno","IdTurno",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objTurno->actualizarTurno($_POST["txtId"], $_POST["txtNombre"], $_POST["txtAbreviatura"], $_POST["txtHoraInicio"], $_POST["txtHoraFin"]);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Nombre=> De: '. $dax->nombre.' a: '.strtoupper($_POST["txtNombre"]).'; Abreviatura=> De: '. $dax->abreviatura. ' a: '.strtoupper($_POST["txtAbreviatura"]).'; HoraInicio=> De: '. $dax->horainicio. ' a: '.$_POST["txtHoraInicio"].'; HoraFin=> De: '. $dax->horafin. ' a: '.strtoupper($_POST["txtHoraFin"]), $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objTurno->eliminarTurno($_POST["txtId"]);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>