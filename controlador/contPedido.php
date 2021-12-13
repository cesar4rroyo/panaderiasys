<?php
require("../modelo/clsDetalleAlmacen.php");
require("../modelo/clsMesa.php");
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
$objMovimiento = new clsDetalleAlmacen($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objMesa = new clsMesa($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		if($objMesa->verificaSituacion($_POST["cboMesa"])=='O'){
			echo 'La mesa est� ocupada';
		}else{		
			$objMovimiento->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
			if(isset($_POST["txtIdReserva"])){
				if($_POST["chkReserva"]=='S'){
					$idsucursalref=$_SESSION['R_IdSucursal'];$idmovimientoref=$_POST["txtIdReserva"];
				}else{
					$idsucursalref=NULL;$idmovimientoref=NULL;
				}
			}else{
				$idsucursalref=NULL;$idmovimientoref=NULL;
			}		
			$_POST["txtNumero"]=str_pad(trim($_POST["txtNumero"]),6,"0",str_pad_left);
			$datosR=split('-',$_POST["cboIdResponsable"]);
            if($_POST["cboMesa"]=="111" || $_POST["cboMesa"]=="112" || $_POST["cboMesa"]=="113" || $_POST["cboMesa"]=="114"){
                $dinero=$_POST["txtDinero"];
                $idcliente=$_POST["txtIdPersona"];
            }else{
                $dinero=0;
                $idcliente=0;
            }
			$res = $objMovimiento->insertarMovimiento(0, 5, $_POST["txtNumero"], 11, '', 'LOCALTIMESTAMP', '', '', $_POST["txtNroPersonas"], $_POST["cboMesa"], 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', $idcliente, $datosR[1], $idmovimientoref, $idsucursalref, $_POST["txtComentario"],'O',0,$_SESSION['R_IdSucursalUsuario'],0,$datosR[0],$_POST["txtNombresPersona"],"","","","",$dinero);
			$dato=$res->fetchObject();
			//INICIO BITACORA
			date_default_timezone_set('America/Lima');
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>5; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>11; formapago=>; fecha=>'.date("d/m/Y").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>'.$_POST["txtNroPersonas"].'; idmesa=>'.$_POST["cboMesa"].'; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotal"].'; igv=>0; total=>'.$_POST["txtTotal"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>0; idresponsable=>'.$datosR[1].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$datosR[0].'; nombrespersona=>'.$_POST["txtNombresPersona"], $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if(is_string($res)){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				break 2;
			}
	
			if(!isset($_SESSION['R_carroPedido']) or $_SESSION['R_carroPedido']==''){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
				break;
			}		
			
			foreach($_SESSION['R_carroPedido'] as $v){
				$res = $objMovimiento->insertarDetalleAlmacen($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
				if(count($v["carroDetalle"])>0){
                    foreach($v["carroDetalle"] as $x => $y){
                        $objMovimiento->insertarDetalleMovCategoria($dato->idmovimiento,$_SESSION["R_IdSucursal"],$v["idproducto"],$y["iddetallecategoria"]);
                    }
                }
                //INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res==1){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;
				}
			}
			$vresp=$objMesa->verificaMesaLibre($_POST["cboMesa"]);
			if($vresp!=1){
				$res = $objMesa->cambiarSituacion($_POST["cboMesa"],$_SESSION['R_IdSucursal'],'O');
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["cboMesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>O', $_SESSION['R_IdSucursal'],$_POST["cboMesa"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res==1){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
					break 4;
				}
			}
			if($res==0){
				$objMovimiento->finalizarTransaccion(); 
				$objBitacora->finalizarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Guardado correctamente";
			}
		}
		break;
        
	case "CABIASITUACION" :
		if(ob_get_length()) ob_clean();
		echo umill($objMovimiento->cambiarSituacionAntendido($_POST['txtId'],'A'));
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objMesa->iniciarTransaccion();
		$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimientohoy","IdMovimiento",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		//VERIFICO SI CAMBIO DE MESA
		if($dax->idmesa!=$_POST["cboMesa"]){
			//CAMBIO LA SITUACION DE LA MESA ANTERIOR A NORMAL
			$res = $objMesa->cambiarSituacion($dax->idmesa,$_SESSION['R_IdSucursal'],'N');
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$dax->idmesa.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>N', $_SESSION['R_IdSucursal'],$dax->idmesa ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objMesa->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				break 4;
			}
			//CAMBIO LA SITUACION DE LA MESA NUEVA A OCUPADA
			if($_POST["cboMesa"]>0){
				$res = $objMesa->cambiarSituacion($_POST["cboMesa"],$_SESSION['R_IdSucursal'],'O');
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["cboMesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>O', $_SESSION['R_IdSucursal'],$_POST["cboMesa"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res==1){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					$objMesa->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
					break 4;
				}
			}
		}
		
		$datosR=split('-',$_POST["cboIdResponsable"]);
        if($_POST["cboMesa"]=="111" || $_POST["cboMesa"]=="112" || $_POST["cboMesa"]=="113" || $_POST["cboMesa"]=="114"){
            $dinero=($_POST["txtDinero"]==''?0:$_POST["txtDinero"]);
            $idcliente=($_POST["txtIdPersona"]==''?0:$_POST["txtIdPersona"]);
        }else{
            $dinero=0;
            $idcliente=0;
        }
        $res = $objMovimiento->actualizarMovimiento($_POST["txtId"],0, 5, $_POST["txtNumero"], 11, '', 'LOCALTIMESTAMP', '', '', $_POST["txtNroPersonas"], $_POST["cboMesa"], 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', $idcliente, $datosR[1], NULL, NULL, $_POST["txtComentario"],'O',0,$_SESSION['R_IdSucursalUsuario'],0,$datosR[0],$_POST["txtNombresPersona"],$dinero);

		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idconceptopago=> De: 0 a: 0; idsucursal=> De: '. $dax->idsucursal.' a: '.$_SESSION['R_IdSucursal'].'; idtipomovimiento=> De: 5 a: 5; numero=> De: '. $dax->numero.' a: '.$_POST["txtNumero"].'; idtipodocumento=> De: 11 a: 11; formapago=> De: a: ; fecha=> De: '. $dax->fecha.' a: '.date("d/m/Y").'; fechaproximacancelacion=> De: a: ; fechaultimopago=> De: a: ; nropersonas=> De: '. $dax->nropersonas.' a: '.$_POST["txtNroPersonas"].'; idmesa=> De: '. $dax->idmesa.' a: '.$_POST["cboMesa"].'; moneda=> De: '. $dax->moneda.' a: S; inicial=> De: 0 a: 0; subtotal=> De: '. $dax->subtotal.' a: '.$_POST["txtTotal"].'; igv=> De: 0 a: 0; total=> De: '. $dax->total.' a: '.$_POST["txtTotal"].'; totalpagado=> De: 0 a: 0; idusuario=> De: '. $dax->idusuario.' a: '.$_SESSION['R_IdUsuario'].'; tipopersona=> De: P a: P; idpersona=> De: 0 a: 0; idresponsable=> De: '. $dax->idresponsable.' a: '.$datosR[1].'; idmovimientoref=> De: a: ; idsucursalref=> De: a: ; comentario=> De: '. $dax->comentario.' a: '.$_POST["txtComentario"].'; situacion=> De: O a: O; estado=> De: N a: N; idcaja=> De: 0 a: 0; idsucursalusuario=> De: '. $dax->idsucursalusuario.' a: '.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=> De: 0 a: 0; idsucursalresponsable=> De: '. $dax->idsucursalresponsable.' a: '.$datosR[0].'; nombrespersona=> De:'. $dax->nombrespersona.' a: '.$_POST["txtNombresPersona"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			$objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			break 2;
		}

		$res = $objMovimiento->eliminarDetalleAlmacen($_POST["txtId"]);
		//eliminaar detalle almacen
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			$objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			break 3;
		}

		if(!isset($_SESSION['R_carroPedido']) or $_SESSION['R_carroPedido']==''){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
			break;
		}
			
		foreach($_SESSION['R_carroPedido'] as $v){
			$res = $objMovimiento->insertarDetalleAlmacen($_POST["txtId"],$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
			if(count($v["carroDetalle"])>0){
                foreach($v["carroDetalle"] as $x => $y){
                    $objMovimiento->insertarDetalleMovCategoria($dato->idmovimiento,$_SESSION["R_IdSucursal"],$v["idproducto"],$y["iddetallecategoria"]);
                }
            }
            //INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objMesa->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				break 3;
			}

		}
		if($res==0){
			$objMovimiento->finalizarTransaccion();
			$objBitacora->finalizarTransaccion(); 
			$objMesa->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion(); 
		$res = $objMovimiento->eliminarMovimiento($_POST['txtId'],$_POST["comentario"]);
		//INICIO BITACORA
		//eliminar movimiento
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				break 2;
			}
		/*$res = $objMovimiento->eliminarDetalleAlmacen($_POST["txtId"]);
		//INICIO BITACORA
		//eliminaar detalle almacen
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			break 3;
		}*/
		$res = $objMovimiento->cambiarSituacionMesa($_POST["txtId"],'N');
		//INICIO BITACORA
		//cambia situacion de mesa
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["cboMesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>N', $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
			break 3;
		}
		//if($res==0){
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		//}
		break;
        
 	case "VALIDAR" :
		if(ob_get_length()) ob_clean();
	    $rs=$objMovimiento->obtenerDataSQL("select * from usuario where idsucursal=".$_SESSION["R_IdSucursal"]." and clave=md5('".$_POST["password"]."') and idperfil in (1,2) and estado='N'");
        if($rs->rowCount()>0){
            echo "vmsg='S';";
        }else{
            echo "vmsg='N';";
        }
        break;
    default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>