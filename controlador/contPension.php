<?php
require("../modelo/clsPension.php");
require("../modelo/clsBitacora.php");
require("fun.php");
$accion = $_POST["accion"];
$operacion = $_POST["operacion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objPension = new clsPension($clase,$_SESSION['IdCliente'], $_SESSION['NombreUsuario'],$_SESSION['Clave']);
$objBitacora = new clsBitacora(34,$_SESSION['IdCliente'], $_SESSION['NombreUsuario'],$_SESSION['Clave']);

switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();

		$filtro="";
		$rtj = $objSeccion->obtenerDataSQL("select * from pension where idcliente = ".$_SESSION['IdCliente']." and idano = ".$_POST["cboIdAno"]." and idnivel = ".$_POST["cboIdNivel"]." and idgrado = ".$_POST["cboIdGrado"].$filtro);
		if($rtj->rowCount()==0){
			echo $objPension->insertarPension($_SESSION['IdCliente'], $_POST["cboIdAno"], $_POST["txtMonto"], $_POST["cboIdGrdo"], $_POST["cboIdNivel"]);
				//BITACORA
				$rt = $objBitacora->consultarSecuenciaTabla($_SESSION['IdCliente'],$clase);
				$dax = $rt->fetchObject();
				$registro = $dax->codigo;
				echo umill($objBitacora->insertarBitacora($_SESSION["NombreUsuario"], $_SESSION['Perfil'], $clase, 'Nuevo Registro', 'Monto:'.$_POST["txtMonto"].', IdGrado:'.$_POST["cboIdGrado"].', IdAno:'.$_POST["cboAno"].', IdNivel:'.$_POST["cboIdNivel"], $_SESSION['IdCliente'], $registro ,$_SESSION['IdUsuario'],$_SESSION['IdCliente']));	
			}
		else{ echo conver("Ya existe pension para estos datos");}
		break;

	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST["txtIdCliente"],"Seccion","IdSeccion",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		echo $objSeccion->actualizarSeccion($_POST["txtIdCliente"], $_POST["txtId"],  $_POST["cboIdAno"], $_POST["txtDescripcion"], $_POST["cboIdGrado"], $_POST["cboIdTurno"], $_POST["cboIdNivel"]);
		//BITACORA
		/*echo umill($objBitacora->insertarBitacora($_SESSION["NombreUsuario"], $_SESSION['Perfil'], $clase, 'Actualizar Registro', 'Descripcion: De '. $dax->descripcion.' a '.$_POST["txtDescripcion"].', IdGrado: De '. $dax->idgrado. ' a '. $_POST["cboIdGrado"].', IdTurno:De '.$dax->idturno.' a '.$_POST["cboIdTurno"].', IdAno:De '. $dax->idano.' a '.$_POST["cboAno"].', IdNivel:De '.$dax->idnivel.' a '.$_POST["cboIdNivel"], $_POST["txtIdCliente"], $_POST["txtId"] ,$_SESSION['IdUsuario'],$_SESSION['IdCliente']));*/
		break;
		
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$rtj = $objSeccion->obtenerDataSQL("select * from DetalleAlumno WHERE EstadoMatricula = 'M' AND IdCliente = ".$_POST["txtIdCliente"]." and IdAno = ".$_POST["txtIdAno"]." and IdSeccion = ".$_POST["txtId"]);
		if($rtj->rowCount()==0){
			if(ob_get_length()) ob_clean();
			echo $objSeccion->eliminarSeccion($_POST["txtIdCliente"],$_POST["txtId"],$_POST["txtIdAno"]);
			//BITACORA
			/*echo umill($objBitacora->insertarBitacora($_SESSION["NombreUsuario"], $_SESSION['Perfil'], $clase, 'Eliminar Registro', 'Estado: de N a A', $_SESSION['IdCliente'], $_POST["txtId"] ,$_SESSION['IdUsuario'],$_SESSION['IdCliente']));*/
		}else{
			echo conver("Sección tiene Alumnos registrados");
		}
		break;
		
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>