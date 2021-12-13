<?php
require("../modelo/clsDetalleAlmacen.php");
require("../modelo/clsStockProducto.php");
require("../modelo/clsMovCaja.php");
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
$objStockProducto = new clsStockProducto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objCaja = new clsMovCaja(48,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//CAJA
		$iniciaproceso=date("Y-n-j H:i:s");
		$apertura=$objCaja->consultarapertura();
		/*$fechacierre=$objCaja->consultarmaxfecha();
		$cierre=$objCaja->consultarcierre($fechacierre);
		echo "alert('apertura".$apertura."');";
		echo "alert('cierre".$cierre."');";
		break 1;*/
		//si la apertura es != 0 o vacio es por que ya hay apertura
		/*if($apertura==0){
		$montosoles= $objCaja->montodeaperturasoles();
		//$montodolares= $objCaja->montodeaperturadolares();
		$num_mov=$objCaja->existenciamov();
		if($num_mov==0){
			$objCaja->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
			$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
			$rst = $objCaja->insertarMovimiento(1, 4, $numero, 9, 'A', $_POST["txtFecha"], '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de compras','N');
			$dato=$rst->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de compras; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if(is_string($rst)){
				$objCaja->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes11: ".$objGeneral->gMsg;
				break 1;
			}else{
				$objCaja->finalizarTransaccion(); 
				$objBitacora->finalizarTransaccion(); 
				if(ob_get_length()) ob_clean();
			}
		}else{
			$fechacierre=$objCaja->consultarmaxfecha();
			$cierre=$objCaja->consultarcierre($fechacierre);
			//SI NO HAY CIERRE
			if($cierre==0){
				
			}else{
				//SI HAY CIERRE
				$objCaja->iniciarTransaccion();
				$objBitacora->iniciarTransaccion();
				$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
				$rst = $objCaja->insertarMovimiento(1, 4, $numero, 9, 'A', $_POST["txtFecha"], '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de compras '.date("Y-n-j H:i:s"),'N');
				$dato=$rst->fetchObject();
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de compras; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA

				if(is_string($rst)){
					$objCaja->abortarTransaccion();
					$objBitacora->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes12: ".$objGeneral->gMsg;
					break 1;
				}else{
					$objCaja->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion(); 
					if(ob_get_length()) ob_clean();
				}	
				}
		}
		}*/
        if($_POST["cboFormaPago"]=="A"){//Contado
    		//VERIFICO SALDO
            /*$saldoinicial = $objMovimiento->obtenerDataSQL("SELECT sum(total) FROM movimientohoy WHERE idconceptopago = 1 AND estado='N'")
                        ->fetchObject()->sum;
            $ingreso = $objMovimiento->obtenerDataSQL("SELECT sum(total) FROM movimientohoy mh WHERE idtipodocumento = 9 AND idconceptopago NOT IN (1,3) AND estado='N'")
                        ->fetchObject()->sum;
            $egreso = $objMovimiento->obtenerDataSQL("SELECT sum(total) FROM movimientohoy mh WHERE idtipodocumento = 10 AND mh.estado = 'N'")
                        ->fetchObject()->sum;
    		$saldosoles=number_format($ingreso-$egreso+$saldoinicial,2);
    		if($_POST["txtTotal"]>$saldosoles){
    			if(ob_get_length()) ob_clean();
    			echo "No hay saldo en la caja para cubrir la compra./n/nSaldo actual: ".$saldosoles;
    			break 2;
    		}*/
            $situacion='C';//Cancelado
            $fechaultimopago='';
        }else{
            $situacion='P';//Pendiente
            $fechaultimopago=date("Y-m-d",strtotime('+'.$_POST["txtDias"],strtotime($_POST["txtFecha"])));
        }
		//COMPRA
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objStockProducto->iniciarTransaccion();

		$idsucursalref=NULL;$idmovimientoref=NULL;
		
		if($_POST["cboIdTipoDocumento"]!=2){
			$_POST["txtSubtotal"]=$_POST["txtTotal"];
			$_POST["txtIgv"]=0;
            $_POST["txtAfecto"]=0;
            $_POST["txtInafecto"]=0;
		}
		if($_POST["txtIdPersona"]==""){
			$_POST["txtIdPersona"]=2;
		}

		$_POST["txtNumero"]=str_pad(trim($_POST["txtNumero"]),6,"0",STR_PAD_LEFT);
		$datosR=explode('-',$_POST["cboIdResponsable"]);
		$res = $objMovimiento->insertarMovimiento(0, 1, $_POST["txtNumero"], $_POST['cboIdTipoDocumento'], $_POST["cboFormaPago"], $_POST["txtFecha"]." ".date("H:i:s"), '', $fechaultimopago, 0, 0, 'S', 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $_POST["txtTotal"], $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $datosR[1], $idmovimientoref, $idsucursalref, $_POST["txtComentario"],$situacion,0,$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$datosR[0],$_POST["txtNombresPersona"]);
		$dato=$res->fetchObject();
        $objMovimiento->ejecutarSQL("update movimientohoy set afecto=".$_POST["txtAfecto"].",inafecto=".$_POST["txtInafecto"]." where idmovimiento=".$dato->idmovimiento." and idsucursal=".$_SESSION["R_IdSucursal"]);
		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>1; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST['cboIdTipoDocumento'].'; formapago=>'.$_POST["cboFormaPago"].'; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>'.$_POST["txtNroPersonas"].'; idmesa=>'.$_POST["cboMesa"].'; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$_POST["txtTotal"].'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$datosR[1].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$datosR[0].'; nombrespersona=>'.$_POST["txtNombresPersona"], $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($res)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}

		if($_POST["cboIdTipoDocumento"]==1){
			$tipodocabreviatura='B/C';
		}elseif($_POST["cboIdTipoDocumento"]==2){
			$tipodocabreviatura='F/C';
		}if($_POST["cboIdTipoDocumento"]==3){
			$tipodocabreviatura='T/C';
		}
        if($_POST["cboFormaPago"]=="A"){//Contado
    		//Inserto movimiento caja
    		/*$numero = $objMovimiento->generaNumeroSinSerie(4,10,substr($_SESSION["R_FechaProceso"],3,2));
    		$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $numero, 10, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $dato->idmovimiento, $_SESSION['R_IdSucursal'], 'Documento Compra '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"],'N',0,$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario']);
    		$datoc=$rst->fetchObject();
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>10; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>'.$dato->idmovimiento.'; idsucursalref=>'.$_SESSION['R_IdSucursal'].'; comentario=>Documento Compra Nro: '.$_POST["txtNumero"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'], $_SESSION['R_IdSucursal'], $datoc->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    		//FIN BITACORA
    		if(is_string($rst)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion();
    			$objBitacora->abortarTransaccion();
    			$objStockProducto->abortarTransaccion(); 
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
    			break 4;
    		}*/
        }
		
		if(!isset($_SESSION['R_carroCompra']) or $_SESSION['R_carroCompra']==''){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
			exit();
		}		
		
		foreach($_SESSION['R_carroCompra'] as $v){
			$res = $objMovimiento->insertarDetalleAlmacen($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],"",$v["afecto"]);
			$objMovimiento->ejecutarSQL("update listaunidad set preciocompra='".$v["preciocompra"]."' where idproducto=".$v["idproducto"]." and idunidad=".$v["idunidad"]." and idunidadbase=".$v["idunidad"]." and idsucursal=".$v["idsucursalproducto"]);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				exit();
			}
			
			//INGRESO DE STOCK $_SESSION['R_IdSucursal']
			$idsucursal=$_SESSION["R_IdSucursal"];
			$res=$objStockProducto->insertar($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				exit();
			}
		}
		if($res==0){
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			$objStockProducto->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	case "CABIASITUACION" :
		echo 'pendiente'; break 1;
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
		$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$datosR=explode('-',$_POST["cboIdResponsable"]);
		if($_POST["cboFormaPago"]=="A"){//Contado
            $situacion='C';//Cancelado
            $fechaultimopago='';
        }else{
            $situacion='P';//Pendiente
            $fechaultimopago=date("Y-m-d",strtotime('+'.$_POST["txtDias"],strtotime($_POST["txtFecha"])));
        }
		$res = $objMovimiento->actualizarMovimiento($_POST["txtId"],0, 1, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], $_POST["cboFormaPago"], $_POST["txtFecha"].' '.date("H:i:s"),'', $fechaultimopago, 0, 0, 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', 0, $datosR[1], NULL, NULL, $_POST["txtComentario"],'O',0,$_SESSION['R_IdSucursalUsuario'],0,$datosR[0],$_POST["txtNombresPersona"]);

		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idconceptopago=> De: 0 a: 0; idsucursal=> De: '. $dax->idsucursal.' a: '.$_SESSION['R_IdSucursal'].'; idtipomovimiento=> De: 5 a: 5; numero=> De: '. $dax->numero.' a: '.$_POST["txtNumero"].'; idtipodocumento=> De: 11 a: 11; formapago=> De: a: ; fecha=> De: '. $dax->fecha.' a: '.date("d/m/Y").'; fechaproximacancelacion=> De: a: ; fechaultimopago=> De: a: ; nropersonas=> De: '. $dax->nropersonas.' a: '.$_POST["txtNroPersonas"].'; idmesa=> De: '. $dax->idmesa.' a: '.$_POST["cboMesa"].'; moneda=> De: '. $dax->moneda.' a: S; inicial=> De: 0 a: 0; subtotal=> De: '. $dax->subtotal.' a: '.$_POST["txtTotal"].'; igv=> De: 0 a: 0; total=> De: '. $dax->total.' a: '.$_POST["txtTotal"].'; totalpagado=> De: 0 a: 0; idusuario=> De: '. $dax->idusuario.' a: '.$_SESSION['R_IdUsuario'].'; tipopersona=> De: P a: P; idpersona=> De: 0 a: 0; idresponsable=> De: '. $dax->idresponsable.' a: '.$datosR[1].'; idmovimientoref=> De: a: ; idsucursalref=> De: a: ; comentario=> De: '. $dax->comentario.' a: '.$_POST["txtComentario"].'; situacion=> De: O a: O; estado=> De: N a: N; idcaja=> De: 0 a: 0; idsucursalusuario=> De: '. $dax->idsucursalusuario.' a: '.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=> De: 0 a: 0; idsucursalresponsable=> De: '. $dax->idsucursalresponsable.' a: '.$datosR[0].'; nombrespersona=> De:'. $dax->nombrespersona.' a: '.$_POST["txtNombresPersona"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}

		/*$res = $objMovimiento->eliminarDetalleAlmacen($_POST["txtId"]);
		//eliminaar detalle almacen
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			break 3;
		}

		if(!isset($_SESSION['R_carroCompra']) or $_SESSION['R_carroCompra']==''){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
			break;
		}
			
		foreach($_SESSION['R_carroCompra'] as $v){
			$res = $objMovimiento->insertarDetalleAlmacen($_POST["txtId"],$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				break 3;
			}

		}*/
		if($res==0){
			$objMovimiento->finalizarTransaccion();
			$objBitacora->finalizarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	case "ELIMINAR" :
		/*$rst2=$objMovimiento->buscarDetalleProducto($_POST['txtId'],"h");
        $c=0;
        $sum=0;
        while($dato=$rst2->fetchObject()){
			$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dato->idproducto,$_SESSION['R_IdSucursal'],$dato->idunidadbase,$dato->cantidad,$dato->idmovimiento,'S',$dato->preciocompra,date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			echo $res;
		}*/
		//echo 'pendiente'; exit();
		if(ob_get_length()) ob_clean();
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"(select * from movimiento union all select * from movimientohoy) as T","IdMovimiento",$_POST["txtId"]);
			$dax = $rt->fetchObject();
			$fechacierre=$dax->fecha;
			$cierre=$objCaja->consultarultimocierrefecha($fechacierre);
			/*echo $fechacierre.' - '.$cierre.' - '.$_POST['txtId'];
			break 1;*/
			//SI NO HAY CIERRE
			//echo $cierre;
			//if($cierre<$_POST['txtId']){
				$objMovimiento->iniciarTransaccion();
				$objBitacora->iniciarTransaccion(); 
				$objStockProducto->iniciarTransaccion(); 
				//ELIMINAR COMPRA
				$objMovimiento->eliminarMovimiento($_POST['txtId']);
				//RENUEVO STOCK
				$res=$objStockProducto->revertir($_SESSION['R_IdSucursal'],$_POST['txtId'],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario'],'A');
				//INICIO BITACORA
				//eliminar movimiento
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 65, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res!="Guardado correctamente"){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					$objStockProducto->abortarTransaccion();  
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
					exit();
				}
				if($res=="Guardado correctamente"){
					$objMovimiento->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion(); 
					$objStockProducto->finalizarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Guardado correctamente";
				}
				//echo "Guardado Correctamente !!!";
			/*}else{//SI HAY CIERRE
				echo "No se puede eliminar este documento porque la caja esta cerrada !!!";
			}*/
		break;
	case "CORREGIRSTOCKCOMPRA":
		$rst2=$objFiltro->buscarDetalleProducto($_POST['txtId'],"h");
        $c=0;
        $sum=0;
        while($dato=$rst2->fetchObject()){
			$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dato->idproducto,$_SESSION['R_IdSucursal'],$dato->idunidadbase,$dato->cantidad,$dato->idmovimiento,'S',$dato->preciocompra,date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		}
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>