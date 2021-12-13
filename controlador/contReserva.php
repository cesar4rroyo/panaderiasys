<?php
require("../modelo/clsDetalleAlmacen.php");
require("../modelo/clsMesa.php");
require("../modelo/clsBitacora.php");
require("../modelo/clsPersona.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if(isset($_POST["txtIdSucursal"])){
$idsucursal=$_POST["txtIdSucursal"];
}else{
$idsucursal=$_SESSION['R_IdSucursal'];
}

if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
if($_SESSION['R_origen_ses']="E"){
	$objMovimiento = new clsDetalleAlmacen($clase,$idsucursal, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
	$objMesa = new clsMesa($clase,$idsucursal, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
	$objBitacora = new clsBitacora(19,$idsucursal, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
	$objPersona = new clsPersona(23,1, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
}else{
	$objMovimiento = new clsDetalleAlmacen($clase,$idsucursal, $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$objMesa = new clsMesa($clase,$idsucursal, $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$objBitacora = new clsBitacora(19,$idsucursal, $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$objPersona = new clsPersona(23,1, $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$codigo=strtoupper($_POST["CAPTCHA_CODE"]);
		if($codigo!=$_SESSION['R_CAPTCHA_CODE']){
			echo "El código ingresado no coincide con la imagen";
			break 1;
		}
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objPersona->iniciarTransaccion();
		if($_POST["txtIdPersonaMaestro"]<>''){
			if($_POST["txtIdPersona"]==''){
				$rstP=$objPersona->insertarPersonaOut($idsucursal,$_POST["txtIdPersonaMaestro"],1349,'',$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],'',3,'S');

				$datoP=$rstP->fetchObject();
				$datoIdPersona=$datoP->idpersona;
				$IdSucursalPersona=$idsucursal;
				//INICIO BITACORA
				echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 23, 'Nuevo Registro', 'IdSucursal=>'.$idsucursal.'; IdPersonaMaestro=>'.$_POST["txtIdPersonaMaestro"].'; IdDistrito=>1349; Direccion=>; Email=>'.$_POST["txtEmail"].'; TelefonoFijo=>'.$_POST["txtTelefonoFijo"].'; TelefonoMovil=>'.$_POST["txtTelefonoMovil"].'; Imagen=>; Compartido=>N; Estado=>N;', $idsucursal, $datoIdPersona ,0,0));
				//FIN BITACORA
				
				if(is_string($rstP)){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					$objPersona->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 2;
				}
			}else{
				$datoIdPersona=$_POST["txtIdPersona"];
				$IdSucursalPersona=$_POST['txtIdSucursalPersona'];
			}
		}else{
			$rst = $objPersona->insertarPersonaMaestroOut(strtoupper(trim($_POST["txtApellidos"])), strtoupper(trim($_POST["txtNombres"])), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"]);
			
			$dato=$rst->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 22, 'Nuevo Registro', 'TipoPersona=>'.$_POST["cboTipoPersona"].'; apellidos=>'.$_POST["txtApellidos"].'; nombres=>'.$_POST["txtNombres"].'; NroDoc=>'.$_POST["txtNroDoc"].'; Sexo=>'.$_POST["optSexo"].'; Estado=>N;', $idsucursal, $dato->idpersonamaestro ,0,0);
			//FIN BITACORA
			
			if(is_string($rst)){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objPersona->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				break 2;
			}
						
			$rstP = $objPersona->insertarPersonaOut($idsucursal, $dato->idpersonamaestro,1349,'',$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],'',3,'S');
			$datoP=$rstP->fetchObject();
			$datoIdPersona=$datoP->idpersona;
			$IdSucursalPersona=$idsucursal;
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'IdSucursal=>'.$idsucursal.'; IdPersonaMaestro=>'.$dato->idpersonamaestro.'; IdDistrito=>1349; Direccion=>; Email=>'.$_POST["txtEmail"].'; TelefonoFijo=>'.$_POST["txtTelefonoFijo"].'; TelefonoMovil=>'.$_POST["txtTelefonoMovil"].'; Imagen=>; Compartido=>S; Estado=>N;', $idsucursal, $datoIdPersona ,0,0);
			//FIN BITACORA
			if(is_string($rstP)){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objPersona->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				break 3;
			}
		}
		
		$numero = $objMovimiento->generaNumeroSinSerie(6,12,substr($_SESSION["R_FechaProceso"],3,2));
		$res = $objMovimiento->insertarMovimiento(0, 6, $numero, 12, '', 'LOCALTIMESTAMP', $_POST["txtFecha"].' '.$_POST["txtHora"], '', $_POST["txtNroPersonas"], $_POST["cboIdSalon"], 'S', 0, 0, 0, 0, 0, 0, 'P', $datoIdPersona, 0, NULL, NULL, $_POST["txtComentario"],'N',0,$IdSucursalPersona,0,'0');
		$dato=$res->fetchObject();
		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$idsucursal.'; idtipomovimiento=>6; numero=>'.$numero.'; idtipodocumento=>12; formapago=>; fecha=>'.date("d/m/Y").'; fechaproximacancelacion=>'.$_POST["txtFecha"].' '.$_POST["txtHora"].'; fechaultimopago=>; nropersonas=>'.$_POST["txtNroPersonas"].'; idmesa=>'.$_POST["cboIdSalon"].'; moneda=>S; inicial=>0; subtotal=>0; igv=>0; total=>0; totalpagado=>0; idusuario=>0; tipopersona=>P; idpersona=>'.$datoIdPersona.'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$IdSucursalPersona.'; idsucursalresponsable=>0', $idsucursal, $dato->idmovimiento ,0,0);
		//FIN BITACORA
		if(is_string($res)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			$objPersona->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes4: ".$objGeneral->gMsg;
			break 4;
		}else{
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			$objPersona->finalizarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>