<?php
require("../modelo/clsPersonaMaestro.php");
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
$objPersonaMaestro = new clsPersonaMaestro($clase,$_SESSION['R_IdCliente'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$rst=$objPersonaMaestro->insertarPersonaMaestro(strtoupper(trim($_POST["txtApellidos"])), strtoupper(trim($_POST["txtNombres"])), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"], $_POST["txtFechaNac"]);
		$dax = $rst->fetchObject();
		$idregistro = $dax->idpersonamaestro;
		//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Apellidos=>'.strtoupper($_POST["txtApellidos"]).'; Nombres=>'.strtoupper($_POST["txtNombres"]).'; TipoPersona=>'.$_POST["cboTipoPersona"].'; NroDoc=>'.$_POST["txtNroDoc"].'; Sexo=>'.strtoupper($_POST["optSexo"]).'; FechaNac=>'.$_POST["txtFechaNac"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"PersonaMaestro","IdPersonaMaestro",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objPersonaMaestro->actualizarPersonaMaestro($_POST["txtId"], strtoupper(trim($_POST["txtApellidos"])), strtoupper(trim($_POST["txtNombres"])), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"], $_POST["txtFechaNac"]);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Apellidos=> De: '. $dax->apellidos.' a: '.strtoupper($_POST["txtApellidos"]).'; Nombres=> De: '. $dax->nombres. ' a: '.strtoupper($_POST["txtNombres"]).'; TipoPersona=> De: '. $dax->tipopersona. ' a: '.strtoupper($_POST["cboTipoPersona"]).'; NroDoc=> De: '. $dax->nrodoc. ' a: '.strtoupper($_POST["txtNroDoc"]).'; Sexo=> De: '. $dax->sexo. ' a: '.strtoupper($_POST["optSexo"]).'; FechaNac=> De: '.$dax->fechanac.' a: '.$_POST["txtFechaNac"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objPersonaMaestro->eliminarPersonaMaestro($_POST["txtId"]);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>