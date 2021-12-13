<?php
require("../modelo/clsGastos.php");
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
$objMovimiento = new clsGastos($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		if($_POST["optMoneda"]=='D'){
			$monto=$_POST["txtTotal"]*$_SESSION['R_TipoCambio'];
		}else{
			$monto=$_POST["txtTotal"];	
		}
		$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $monto, 0, $monto, $monto, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdUsuario'], NULL, NULL, $_POST["txtComentario"],'N',0,$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario']);
		$dato=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>7; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$monto.'; igv=>0; total=>'.$monto.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdUsuario'].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'], $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}else{
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	case "NUEVO-CAJERO" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		if($_POST["optMoneda"]=='D'){
			$monto=$_POST["txtTotal"]*$_SESSION['R_TipoCambio'];
		}else{
			$monto=$_POST["txtTotal"];	
		}
		$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $monto, 0, $monto, 0, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdUsuario'], NULL, NULL, $_POST["txtComentario"],'N',$_SESSION['R_IdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario']);
		$dato=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$monto.'; igv=>0; total=>'.$monto.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdUsuario'].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>'.$_SESSION['R_IdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'], $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}else{
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	case "APERTURA" :
		if(ob_get_length()) ob_clean();
		$num_mov=$objMovimiento->existenciamov();
		if($num_mov==0){
			$objMovimiento->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
			$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
			$dato=$rst->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if(is_string($rst)){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				exit();
			}else{
				$objMovimiento->finalizarTransaccion(); 
				$objBitacora->finalizarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Guardado correctamente";
			}
		}else{
			$fechacierre=$objMovimiento->consultarmaxfecha();
			$cierre=$objMovimiento->consultarcierre($fechacierre);
			//SI NO HAY CIERRE
			if($cierre==0){
				$objMovimiento->iniciarTransaccion();
				$objBitacora->iniciarTransaccion();
				$numero = $objMovimiento->generaNumeroSinSerie(4,10,substr($_SESSION["R_FechaProceso"],3,2));
				//CERRAMOS CAJA EN SOLES
				$rst = $objMovimiento->insertarMovimiento(2, 7, $numero, $_POST["cboIdTipoDocumento"], 'A', $fechacierre, '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
				$dato=$rst->fetchObject();
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>2; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$fechacierre.'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if(is_string($rst)){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
					exit();
				}
				//APERTURAMOS CAJA EN SOLES
				$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
				$dato=$rst->fetchObject();
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if(is_string($rst)){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
					exit();
				}else{
					$objMovimiento->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Guardado correctamente";
				}
			}else{
				//SI HAY CIERRE
				$objMovimiento->iniciarTransaccion();
				$objBitacora->iniciarTransaccion();
				$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
				$dato=$rst->fetchObject();
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if(is_string($rst)){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
					exit();
				}else{
					$objMovimiento->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Guardado correctamente";
				}	
			}
		}		
		break;
    case "CIERRE" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION['R_IdCaja']);
		$dato=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}else{
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamenteee";
		}
		break;
    case "CIERRE-CAJERO" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION['R_IdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario']);
		$dato=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}else{
            $objMovimiento->moverdatos();
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	case "ELIMINAR" ://PENDIENTE
		if(ob_get_length()) ob_clean();
        $rs=$objMovimiento->obtenerDataSQL("select * from (select * from movimientohoy union select * from movimiento) T where idmovimiento=".$_POST["txtId"])->fetchObject();
        if($rs->idconceptopago>2){        
		  echo umill($objMovimiento->eliminarMovimiento($_POST["txtId"]));
		}else{
		  echo "No puede eliminar la apertura o cierre";
		}
        break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>