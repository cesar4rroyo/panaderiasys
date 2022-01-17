<?php
require("../modelo/clsPersona.php");
require("../modelo/clsRolPersona.php");
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
$objPersona = new clsPersona($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objRolPersona = new clsRolPersona($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		if($_POST["txtIdPersonaMaestro"]<>''){
			//VERIFICO SI LA PERSONA MAESTRO YA SE REGISTRO PARA ESTA SUCURSAL
			$rstverifica = $objPersona->consultar($_POST["txtIdPersonaMaestro"],$_SESSION['R_IdSucursal']);						
			$datosencontrados=$rstverifica->rowCount();
			if($datosencontrados==0){
				$res = $objPersona->insertarPersonaOut($_POST["txtIdSucursal"],$_POST["txtIdPersonaMaestro"],$_POST["cboDist"],$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],$_POST["txtImagen"],$_POST["cboIdRol"],$_POST['chkCompartido']);
				$dax = $res->fetchObject();
				$idregistro = $dax->idpersona;
				//INICIO BITACORA
				echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'IdSucursal=>'.$_POST["txtIdSucursal"].'; IdPersonaMaestro=>'.$_POST["txtIdPersonaMaestro"].'; IdDistrito=>'.$_POST["cboDist"].'; Direccion=>'.$_POST["txtDireccion"].'; Email=>'.$_POST["txtEmail"].'; TelefonoFijo=>'.$_POST["txtTelefonoFijo"].'; TelefonoMovil=>'.$_POST["txtTelefonoMovil"].'; Imagen=>'.$_POST["txtImagen"].'; Compartido=>'.$_POST["chkCompartido"].'; Estado=>N;', $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
				//FIN BITACORA
			}else{
				if(ob_get_length()) ob_clean();
				echo "Está persona ya se encuentra registrada";
			}
		}else{			
			$objPersona->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
			
			$rst = $objPersona->insertarPersonaMaestroOut(strtoupper(trim($_POST["txtApellidos"])), strtoupper(trim($_POST["txtNombres"])), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"], $_POST["txtFechaNac"]);
						
			$dato=$rst->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 22, 'Nuevo Registro', 'TipoPersona=>'.$_POST["cboTipoPersona"].'; apellidos=>'.$_POST["txtApellidos"].'; nombres=>'.$_POST["txtNombres"].'; NroDoc=>'.$_POST["txtNroDoc"].'; Sexo=>'.$_POST["optSexo"].'; Estado=>N;'.'; FechaNac=>'.$_POST["txtFechaNac"], $_SESSION['R_IdSucursal'], $dato->idpersonamaestro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			
			if(is_string($rst)){
				$objPersona->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				exit();
			}
			
			$res = $objPersona->insertarPersonaOut($_POST["txtIdSucursal"], $dato->idpersonamaestro,$_POST["cboDist"],$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],$_POST["txtImagen"],$_POST["cboIdRol"],$_POST['chkCompartido']);
			$dax = $res->fetchObject();
			$idregistro = $dax->idpersona;
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'IdSucursal=>'.$_POST["txtIdSucursal"].'; IdPersonaMaestro=>'.$dato->idpersonamaestro.'; IdDistrito=>'.$_POST["cboDist"].'; Direccion=>'.$_POST["txtDireccion"].'; Email=>'.$_POST["txtEmail"].'; TelefonoFijo=>'.$_POST["txtTelefonoFijo"].'; TelefonoMovil=>'.$_POST["txtTelefonoMovil"].'; Imagen=>'.$_POST["txtImagen"].'; Compartido=>'.$_POST["chkCompartido"].'; Estado=>N;', $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if(is_string($res)){
				$objPersona->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				exit();
			}
			if(!is_string($res)){
				$objPersona->finalizarTransaccion(); 
				$objBitacora->finalizarTransaccion(); 
				if(ob_get_length()) ob_clean();
				$nombres_completo = $_POST["txtApellidos"]." ".$_POST["txtNombres"];
				echo "Guardado correctamente@@" . $idregistro . "@@" . $_POST["txtIdSucursal"] . "@@" . $nombres_completo;
			}
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		
		$rt = $objBitacora->consultarDatosAntiguos(0,"PersonaMaestro","IdPersonaMaestro",$_POST["txtIdPersonaMaestro"]);
		$dax = $rt->fetchObject();
		
		$objPersona->actualizarPersonaMaestro($_POST["txtIdPersonaMaestro"], strtoupper(trim($_POST["txtApellidos"])), strtoupper(trim($_POST["txtNombres"])), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"], $_POST["txtFechaNac"]);
		
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Apellidos=> De: '. $dax->apellidos.' a: '.strtoupper($_POST["txtApellidos"]).'; Nombres=> De: '. $dax->nombres. ' a: '.strtoupper($_POST["txtNombres"]).'; TipoPersona=> De: '. $dax->tipopersona. ' a: '.strtoupper($_POST["cboTipoPersona"]).'; NroDoc=> De: '. $dax->nrodoc. ' a: '.strtoupper($_POST["txtNroDoc"]).'; Sexo=> De: '. $dax->sexo. ' a: '.strtoupper($_POST["optSexo"]).'; FechaNac=> De: '.$dax->fechanac.' a: '.$_POST["txtFechaNac"], $_SESSION['R_IdSucursal'], $_POST["txtIdPersonaMaestro"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Persona","IdPersona",$_POST["txtIdPersona"]);
		$dax = $rt->fetchObject();

		$objPersona->actualizarPersona($_POST["txtIdSucursal"], $_POST["txtIdPersona"],$_POST["txtIdPersonaMaestro"],$_POST["cboDist"],$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],$_POST["txtImagen"],$_POST['chkCompartido']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'IdSucursal=> De: '. $dax->idsucursal.' a: '.$_POST["txtIdSucursal"].'; IdPersonaMaestro=> De: '. $dax->idpersonamaestro.' a: '.$_POST["txtIdPersonaMaestro"].'; IdDistrito=> De: '. $dax->iddistrito.' a: '.$_POST["cboDist"].'; Direccion=> De: '. $dax->direccion.' a: '.$_POST["txtDireccion"].'; Email=> De: '. $dax->email.' a: '.$_POST["txtEmail"].'; TelefonoFijo=> De: '. $dax->telefonofijo.' a: '.$_POST["txtTelefonoFijo"].'; TelefonoMovil=> De: '. $dax->telefonomovil.' a: '.$_POST["txtTelefonoMovil"].'; Imagen=> De: '. $dax->imagen.' a: '.$_POST["txtImagen"].'; Compartido=> De: '. $dax->compartido.' a: '.$_POST["chkCompartido"], $_SESSION['R_IdSucursal'], $_POST["txtIdPersona"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$existe=$objPersona->verificaExisteUsuario($_POST['txtIdPersona'],$_POST["txtIdSucursal"]);
		if($existe==0){
			$objPersona->eliminarPersona($_POST["txtIdSucursal"], $_POST['txtIdPersona'], $_POST["txtIdPersonaMaestro"]);
			
			echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtIdPersona"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		}else{
			echo "No se puede eliminar a está persona porque tiene una cuenta de usuario registrada";
		}
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>