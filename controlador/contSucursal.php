<?php
require("../modelo/clsSucursal.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}

if($accion!='NUEVA-EMPRESA'){
$objSucursal = new clsSucursal($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}else{
$objSucursal = new clsSucursal($clase,1, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
}
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$rstS=$objSucursal->insertarSucursalParametrizada($_POST["txtNombreSucursal"], $_POST["txtDireccion"], $_POST["txtRuc"], $_POST["txtEmail"], $_POST["txtTelefonoFijo"], $_POST["txtTelefonoMovil"], $_POST["txtFax"], $_POST["txtLogo"], $_POST["txtIdEmpresa"]);
        require("../modelo/clsPersona.php");
		$objPersona = new clsPersona(23,1, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);

		$objPersona->iniciarTransaccion();
        $datoS=$rstS->fetchObject();
		$datoIdSucursal=$datoS->idsucursal;
        $rst = $objPersona->insertarPersonaMaestroOut(utf8_encode(strtoupper(trim("admin"))),utf8_encode(strtoupper(trim("sucursal->".$_POST["txtNombreSucursal"]))), "NATURAL", trim("111111111"), "M","");
			if(is_string($rst)){
				$objPersona->abortarTransaccion(); 
				$objSucursal->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes32: ".$objGeneral->gMsg;
				break 3;
			}
			
			$dato=$rst->fetchObject();
			$rstP = $objPersona->insertarPersonaOut($datoIdSucursal, $dato->idpersonamaestro,1349,$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],"",1,$_POST['chkCompartido']);
			if(is_string($rstP)){
				$objPersona->abortarTransaccion(); 
				$objSucursal->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes4: ".$objGeneral->gMsg;
				break 4;
			}
   
        $datoP=$rstP->fetchObject();
		$datoIdPersona=$datoP->idpersona;
        
        require("../modelo/clsUsuario.php");		
		$objUsuario = new clsUsuario(16,$datoIdSucursal, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
		//$nombreusuario=split(' ',$_POST["txtNombres"]);
		$nombreusuario=trim("admin".$datoIdSucursal);
		$password='123456';
		$objUsuario->iniciarTransaccion();
		$res=$objUsuario->insertarUsuario($datoIdSucursal, 0, $nombreusuario, md5($password), $datoIdPersona, 2, 20, 0);
		if($res==1){
			$objPersona->abortarTransaccion();
			$objSucursal->abortarTransaccion();  
			$objUsuario->abortarTransaccion();  
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes5: ".$objGeneral->gMsg;
			break 5;
		}
		if($res==0){
			$objPersona->finalizarTransaccion();
			$objSucursal->finalizarTransaccion();  
			$objUsuario->finalizarTransaccion();  
			if(ob_get_length()) ob_clean();
        }
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objSucursal->actualizarSucursal($_POST["txtId"], $_POST["txtIdEmpresa"], $_POST["txtNombreSucursal"], $_POST["txtDireccion"], $_POST["txtRuc"], $_POST["txtEmail"], $_POST["txtTelefonoFijo"], $_POST["txtTelefonoMovil"], $_POST["txtFax"], $_POST["txtLogo"]));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		echo umill($objSucursal->eliminarSucursal($_POST["txtId"]));
		break;
	case "NUEVA-EMPRESA" :
		if(ob_get_length()) ob_clean();
		$rstEm=$objSucursal->buscarEmpresaxruc($_POST["txtRucEmpresa"]);
		if($rstEm->rowCount()>0){echo '<script>alert("Ya existe una empresa registrada con este ruc");history.go(-1);</script>';break;}
		$nombre_original=$_FILES['txtLogoEmpresa']['name'];
		$tipo=$_FILES['txtLogoEmpresa']['type'];
		$tam=$_FILES['txtLogoEmpresa']['size'];
		$temporal=$_FILES['txtLogoEmpresa']['tmp_name'];
		$rutaempresa="";
		if(!empty($nombre_original)){
			$nombre_carpeta = "../img/empresas/".$_POST["txtRucEmpresa"]; 
			if(!is_dir($nombre_carpeta)){ 
			@mkdir($nombre_carpeta, 0700); 
			}else{ 
			echo "Ya existe ese directorio\n"; 
			}  
			$rutaempresa=$nombre_carpeta."/logo-empresa-".$_POST["txtNombreEmpresa"].".jpg";
			$rutaempresadb=$_POST["txtRucEmpresa"]."/logo-empresa-".$_POST["txtNombreEmpresa"].".jpg";
			copy($temporal,$rutaempresa);
		}			
		
		$objSucursal->iniciarTransaccion();
		$rstE=$objSucursal->insertarEmpresaOut($_POST["txtNombreEmpresa"], $_POST["txtDireccionEmpresa"], $_POST["txtRucEmpresa"], $_POST["txtEmailEmpresa"], $_POST["txtTelefonoFijoEmpresa"], $_POST["txtTelefonoMovilEmpresa"], $_POST["txtFaxEmpresa"], $rutaempresadb);
		if(is_string($rstE)){
			$objSucursal->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			break 1;
		}

		$datoE=$rstE->fetchObject();
		$datoIdEmpresa=$datoE->idempresa;

		$rstS=$objSucursal->insertarSucursalOut($_POST["txtNombreEmpresa"], $_POST["txtDireccionEmpresa"], $_POST["txtRucEmpresa"], $_POST["txtEmailEmpresa"], $_POST["txtTelefonoFijoEmpresa"], $_POST["txtTelefonoMovilEmpresa"], $_POST["txtFaxEmpresa"], $rutaempresadb, $datoIdEmpresa);
		if(is_string($rstS)){
			$objSucursal->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			break 2;
		}
		$datoS=$rstS->fetchObject();
		$datoIdSucursal=$datoS->idsucursal;

		$nombre_original=$_FILES['txtImagen']['name'];
		$tipo=$_FILES['txtImagen']['type'];
		$tam=$_FILES['txtImagen']['size'];
		$temporal=$_FILES['txtImagen']['tmp_name'];
		$rutafoto="";
		if(!empty($nombre_original)){
			$nombre_carpeta = "../img/empresas/".$_POST["txtRucEmpresa"]; 
			if(!is_dir($nombre_carpeta)){ 
			@mkdir($nombre_carpeta, 0700); 
			}else{ 
			//echo "Ya existe ese directorio\n"; 
			}  
			$rutafoto=$nombre_carpeta."/usuario-".$_POST["txtRucEmpresa"]."-".$_POST["txtApellidos"]."-".$_POST["txtNombres"].".jpg";
			$rutafotodb=$_POST["txtRucEmpresa"]."/usuario-".$_POST["txtRucEmpresa"]."-".$_POST["txtApellidos"]."-".$_POST["txtNombres"].".jpg";
			copy($temporal,$rutafoto);
		}			
		
		require("../modelo/clsPersona.php");
		$objPersona = new clsPersona(23,1, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);

		$objPersona->iniciarTransaccion();
		if($_POST["txtIdPersonaMaestro"]<>''){
			$rstP=$objPersona->insertarPersonaOut($datoIdSucursal,$_POST["txtIdPersonaMaestro"],$_POST["cboDist"],$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],$rutafotodb,1,$_POST['chkCompartido']);
			if(is_string($rstP)){
				$objPersona->abortarTransaccion(); 
				$objSucursal->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes31: ".$objGeneral->gMsg;
				break 3;
			}
		}else{ echo utf8_decode(utf8_encode(strtoupper(trim($_POST["txtApellidos"]))));
			$rst = $objPersona->insertarPersonaMaestroOut(utf8_encode(strtoupper(trim($_POST["txtApellidos"]))),utf8_encode(strtoupper(trim($_POST["txtNombres"]))), $_POST["cboTipoPersona"], trim($_POST["txtNroDoc"]), $_POST["optSexo"]);
			if(is_string($rst)){
				$objPersona->abortarTransaccion(); 
				$objSucursal->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes32: ".$objGeneral->gMsg;
				break 3;
			}
			
			$dato=$rst->fetchObject();
			$rstP = $objPersona->insertarPersonaOut($datoIdSucursal, $dato->idpersonamaestro,$_POST["cboDist"],$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],$rutafotodb,1,$_POST['chkCompartido']);
			if(is_string($rstP)){
				$objPersona->abortarTransaccion(); 
				$objSucursal->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes4: ".$objGeneral->gMsg;
				break 4;
			}
			 //inserto PERSONA VARIOS
			$rstPV = $objPersona->insertarPersonaOut($datoIdSucursal, 45,$_POST["cboDist"],'','','','','',1,$_POST['chkCompartido']);
			if(is_string($rstPV)){
				$objPersona->abortarTransaccion(); 
				$objSucursal->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes4: ".$objGeneral->gMsg;
				break 4;
			}
		}
		$datoP=$rstP->fetchObject();
		$datoIdPersona=$datoP->idpersona;
		
		require("../modelo/clsUsuario.php");
		$objUsuario = new clsUsuario(16,$datoIdSucursal, $_SESSION['R_NombreUsuarioCloud'],$_SESSION['R_ClaveCloud']);
		//$nombreusuario=split(' ',$_POST["txtNombres"]);
		$nombreusuario=trim($_POST["txtUsuario"]);
		
		function randomText($length) {
			$pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			for($i=0;$i<$length;$i++) {
			  $key .= $pattern{rand(0,35)};
			}
			return $key;
		}
		//$password=randomText(6);
		$password='123456';
		$objUsuario->iniciarTransaccion();
		//$res=$objUsuario->insertarUsuario($datoIdSucursal, 0, $nombreusuario[0], $password, $datoIdPersona, 2, 20, 0);
		$res=$objUsuario->insertarUsuario($datoIdSucursal, 0, $nombreusuario, md5($password), $datoIdPersona, 2, 20, 0);
		if($res==1){
			$objPersona->abortarTransaccion();
			$objSucursal->abortarTransaccion();  
			$objUsuario->abortarTransaccion();  
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes5: ".$objGeneral->gMsg;
			break 5;
		}
		if($res==0){
			$objPersona->finalizarTransaccion();
			$objSucursal->finalizarTransaccion();  
			$objUsuario->finalizarTransaccion();  
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
			echo "<META HTTP-EQUIV=Refresh CONTENT='0;URL= ../vista/mensajeRegEmpresa.php'>";
		}
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>