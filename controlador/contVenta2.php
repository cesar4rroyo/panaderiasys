<?php
require("../modelo/clsDetalleMovimiento.php");
require("../modelo/clsDetalleAlmacen.php");
require("../modelo/clsMesa.php");
require("../modelo/clsMovCaja.php");
require("../modelo/clsStockProducto.php");
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
$objMovimiento = new clsDetalleMovimiento($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objMovimientoAlmacen = new clsDetalleAlmacen($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objMesa = new clsMesa($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objCaja = new clsMovCaja(48,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objStockProducto = new clsStockProducto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
		if($apertura==0){
		$montosoles= $objCaja->montodeaperturasoles();
		//$montodolares= $objCaja->montodeaperturadolares();
		$num_mov=$objCaja->existenciamov();
		if($num_mov==0){
			$objCaja->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
			$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
			$rst = $objCaja->insertarMovimiento(1, 4, $numero, 9, 'A', $_SESSION['R_FechaProceso'], '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas','N');
			$dato=$rst->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de ventas; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if(is_string($rst)){
				$objCaja->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes11: ".$objGeneral->gMsg;
				break 1;
			/*}
			$rst = $objCaja->insertarMovimiento(1, 4, str_pad($numero+1,6,"0",STR_PAD_LEFT), 9, 'A', $_POST["txtFecha"], '', '', 0, 0, 'D', 0, $montodolares, 0, $montodolares, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas','N');
			if(is_string($rst)){
				$objCaja->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				break 3;*/
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
				/*$objCaja->iniciarTransaccion();
				$objBitacora->iniciarTransaccion(); 
				$numero = $objCaja->generaNumeroSinSerie(4,10,substr($_SESSION["R_FechaProceso"],3,2));
				//CERRAMOS CAJA EN SOLES
				$rst = $objCaja->insertarMovimiento(2, 4, $numero, 10, 'A', $fechacierre, '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Cierre automatica desde el modulo de ventas','N');
				$dato=$rst->fetchObject();
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>2; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>10; formapago=>A; fecha=>'.$fechacierre.'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Cierre automatica desde el modulo de ventas; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if(is_string($rst)){
					$objCaja->abortarTransaccion(); 
					$objBitacora->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
					break 2;
				}
				//CERRAMOS CAJA EN DOLARES
				/*$rst = $objCaja->insertarMovimiento(2, 4, str_pad($numero+1,6,"0",STR_PAD_LEFT), 10, 'A', $fechacierre, '', '', 0, 0, 'D', 0, $montodolares, 0, $montodolares, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Cierre automatica desde el modulo de ventas','N');
				if(is_string($rst)){
					$objCaja->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;
				}*/
				/*$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
				//APERTURAMOS CAJA EN SOLES
				$rst = $objCaja->insertarMovimiento(1, 4, $numero, 9, 'A', $_POST["txtFecha"], '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas','N');
				$dato=$rst->fetchObject();
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de ventas; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if(is_string($rst)){
					$objCaja->abortarTransaccion(); 
					$objBitacora->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 2;
				/*}
				//APERTURAMOS CAJA EN DOLARES
				$rst = $objCaja->insertarMovimiento(1, 4, str_pad($numero+1,6,"0",STR_PAD_LEFT), 9, 'A', $_POST["txtFecha"], '', '', 0, 0, 'D', 0, $montodolares, 0, $montodolares, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas','N');
				if(is_string($rst)){
					$objCaja->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;*/
				/*}else{
					$objCaja->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion(); 
					if(ob_get_length()) ob_clean();
				}*/
			}else{
				//SI HAY CIERRE
				$objCaja->iniciarTransaccion();
				$objBitacora->iniciarTransaccion();
				$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
				$rst = $objCaja->insertarMovimiento(1, 4, $numero, 9, 'A', $_POST["txtFecha"], '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas '.date("Y-n-j H:i:s"),'N');
				$dato=$rst->fetchObject();
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de ventas; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA

				if(is_string($rst)){
					$objCaja->abortarTransaccion();
					$objBitacora->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes12: ".$objGeneral->gMsg;
					break 1;
/*				}
				$rst = $objCaja->insertarMovimiento(1, 4, str_pad($numero+1,6,"0",STR_PAD_LEFT), 9, 'A', $_POST["txtFecha"], '', '', 0, 0, 'D', 0, $montodolares, 0, $montodolares, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas','N');
				if(is_string($rst)){
					$objCaja->abortarTransaccion(); 
					$objBitacora->abortarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;*/
				}else{
					$objCaja->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion(); 
					if(ob_get_length()) ob_clean();
				}	
				}
		}
		}
		//VENTA
		$iniciaproceso2=date("Y-n-j H:i:s");
		$objMovimiento->iniciarTransaccion();
		$objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion(); 
		$objStockProducto->iniciarTransaccion(); 
		
        //Para division de cuenta
        if($_POST["txtSubcuenta"]=="NO"){
               
		if($_POST["cboIdTipoDocumento"]!=5){
			$_POST["txtSubtotal"]=$_POST["txtTotal"];
			$_POST["txtIgv"]=0;
		}
        if($_POST["optTipoPago"]=="Efectivo"){
            $idbanco="";
            $idtipotarjeta="";
            $numerotarjeta="";
            $totalpagado=$_POST["txtTotal"];
        }else{
            $idbanco=$_POST["cboBanco"];
            $idtipotarjeta=$_POST["cboTipoTarjeta"];
            $numerotarjeta=$_POST["txtNumeroTarjeta"];
            $totalpagado=$_POST["txtPagoEfectivo"];
        }
        
		//Inserto Documento Venta; editado con el tipo de pago
		$rst = $objMovimiento->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], NULL, NULL, $_POST["txtComentario"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			break 2;
		}
		$dato=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>2; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>'.$_POST["optMoneda"].'; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta =>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($_POST["cboIdTipoDocumento"]==4){
			$tipodocabreviatura='B/V';
		}elseif($_POST["cboIdTipoDocumento"]==5){
			$tipodocabreviatura='F/V';
		}if($_POST["cboIdTipoDocumento"]==6){
			$tipodocabreviatura='T/V';
		}
		//Inserto movimiento caja; editado con el tipo de pago
		$numero = $objMovimiento->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
		$rst = $objMovimiento->insertarMovimiento(3, 4, $numero, 9, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $dato->idmovimiento, $_SESSION['R_IdSucursal'], 'Documento Venta '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
		$datoc=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>3; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>'.$_POST["optMoneda"].'; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>'.$dato->idmovimiento.'; idsucursalref=>'.$_SESSION['R_IdSucursal'].'; comentario=>Documento Venta Nro: '.$_POST["txtNumero"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta=>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $datoc->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion();
			$objStockProducto->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
			break 3;
		}
		if(!isset($_SESSION['R_carroVenta']) or $_SESSION['R_carroVenta']==''){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
			break;
		}
		//Inserto Detallde Documento Venta
		$iniciaproceso3=date("Y-n-j H:i:s");
		$cuenta=0;
		$comandas='Comanda Nro: ';
		$nropedidocomanda='';
		foreach($_SESSION['R_carroVenta'] as $v){
			if($_POST['txtPersona']=='VARIOS'){
				if($cuenta==0){
					$nombrespersona=$objMovimiento->consultarNombreClientePedido($v['idpedido']);
					//cambio nombre estatico del cliente en doc venta
					$res=$objMovimiento->actualizarNombresClienteMovimiento($dato->idmovimiento,$nombrespersona);
					if(is_string($res)){
						$objMovimiento->abortarTransaccion(); 
						$objMovimientoAlmacen->abortarTransaccion(); 
						$objBitacora->abortarTransaccion(); 
						$objStockProducto->abortarTransaccion();
						if(ob_get_length()) ob_clean();
						echo "Error de Proceso en Lotes4.1: ".$objGeneral->gMsg;
						break 5;
					}
					//cambio nombre estatico del cliente en mov caja
					$res=$objMovimiento->actualizarNombresClienteMovimiento($datoc->idmovimiento,$nombrespersona);
					if(is_string($res)){
						$objMovimiento->abortarTransaccion(); 
						$objMovimientoAlmacen->abortarTransaccion(); 
						$objBitacora->abortarTransaccion(); 
						$objStockProducto->abortarTransaccion();
						if(ob_get_length()) ob_clean();
						echo "Error de Proceso en Lotes4.2: ".$objGeneral->gMsg;
						break 5;
					}
					$cuenta++;
				}
			}
			//concateno los numeros de pedido que van en el comentario
			if($v['nropedido']!=$nropedidocomanda){
				$comandas.=$v['nropedido'].', ';
				$nropedidocomanda=$v['nropedido'];
			}
			$res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
			if(is_string($res)){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes5: ".$objGeneral->gMsg;
				break 5;
			}
			$dato2=$res->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], $dato2->iddetallemovalmacen,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			
			if($v['kardex']=='S'){
				$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res!='Guardado correctamente'){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;
				}
			}elseif($v['kardex']!='S' and $v['compuesto']=='S'){
				$res=$objStockProducto->insertarcompuesto($_SESSION['R_IdSucursal'],$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res!='Guardado correctamente'){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;
				}	
			}
			
			$res = $objMovimiento->insertarDetalleMovimiento($dato->idmovimiento,$v['idpedido'],$dato2->iddetallemovalmacen);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 59, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idmovimientoref=>'.$v['idpedido'].'; iddetallemovimientoalmacen=>'.$dato2->iddetallemovalmacen.'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion();
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes6: ".$objGeneral->gMsg;
				break 6;
			}
			
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$v['idpedido']);
			$dax = $rt->fetchObject();
		
			$res = $objMovimiento->actualizarMontoPagadoMovimiento($v['idpedido'],$v['precioventa']*$v['cantidad']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$v['idpedido'].'; totalpagado=> De: '.$dax->totalpagado.' a: '.$v['precioventa']*$v['cantidad'].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], $v['idpedido'],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion();
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes7: ".$objGeneral->gMsg;
				break 7;
			}
			$iniciaproceso4.="$$".date("Y-n-j H:i:s");
		}
			
		$comandas=substr($comandas,0,strlen($comandas)-2);
		$res=$objMovimiento->actualizarComentarioMovimiento($dato->idmovimiento,$comandas);
		if(is_string($res)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
			break 5;
		}
			$iniciaproceso5=date("Y-n-j H:i:s");		
		$res=$objMovimiento->cambiarSituacionPedido($dato->idmovimiento,'P');$iniciaproceso6=date("Y-n-j H:i:s");		
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimieneto=>'.$dato->idmovimiento.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>P; nota: la situacion hace referencia a los pedidos que pertenecen al documento de venta', $_SESSION['R_IdSucursal'],$dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);$iniciaproceso7=date("Y-n-j H:i:s");		
		//FIN BITACORA
		if($res!='Guardado correctamente'){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
			break 5;
		}
		
		if($res=='Guardado correctamente'){
			$objMovimiento->finalizarTransaccion(); 
			$objMovimientoAlmacen->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion(); 
			$objStockProducto->finalizarTransaccion();
		}
		
		if($_POST["cboIdTipoDocumento"]==4){//boleta
			echo "setRun('vista/frmComprobanteB','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
		}else{
			if($_POST["cboIdTipoDocumento"]==5){//factura
				echo "setRun('vista/frmComprobanteF','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
			}else{//ticket
				//echo "alert('Guardado Correctamente.".$iniciaproceso."$$".$iniciaproceso2."$$".$iniciaproceso3."$$$".$iniciaproceso4."$$".$iniciaproceso5."$$".$iniciaproceso6."$$".$iniciaproceso7."$$".date("Y-n-j H:i:s")."');document.getElementById('cargamant').innerHTML=''";
				echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
			}
		}
        
        
        //FIN 
        }else{   
            
        //PARA DIVISION DE CUENTA
        
        //->PRIMERO CREO UN NUEVO PEDIDO CON LOS PRODUCTOS QUE SE VAN A PAGAR
        if(ob_get_length()) ob_clean();
		    $objMovimientoAlmacen->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
			$idsucursalref=NULL;$idmovimientoref=NULL;
            $temp = $objMovimientoAlmacen->consultarMovimiento(20,1,1,1,$_POST["IdPedido"],0);
            $datatemp = $temp->fetchObject();
            
            $_POST["txtNumeroComanda"] = $objMovimientoAlmacen->generaNumeroxMesero($datatemp->idresponsable,$_SESSION['R_IdSucursalUsuario']);		
			$_POST["txtNumeroComanda"] = str_pad(trim($_POST["txtNumeroComanda"]),6,"0",str_pad_left);
			$res = $objMovimientoAlmacen->insertarMovimiento(0, 5, $_POST["txtNumeroComanda"], 11, '', 'LOCALTIMESTAMP', '', '', 4, $datatemp->idmesa, 'S', 0, $_POST["txtTotalSubcuenta"], 0, $_POST["txtTotalSubcuenta"], 0, $_SESSION['R_IdUsuario'], 'P', 0, $_SESSION['R_IdPersona'], $idmovimientoref, $idsucursalref, '-' ,'O',0,$_SESSION['R_IdSucursalUsuario'],0,$_SESSION['R_IdSucursalUsuario']);
            $dato=$res->fetchObject();
            $idpedido = $dato->idmovimiento;
			//INICIO BITACORA
			date_default_timezone_set('America/Lima');
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>5; numero=>'.$_POST["txtNumeroComanda"].'; idtipodocumento=>11; formapago=>; fecha=>'.date("d/m/Y").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>4; idmesa=>'.$datatemp->idmesa.'; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotalSubcuenta"].'; igv=>0; total=>'.$_POST["txtTotalSubcuenta"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>0; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>'."-".'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; nombrespersona=>'." ", $_SESSION['R_IdSucursal'], $idpedido ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if(is_string($res)){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objMovimientoAlmacen->gMsg;
				break 2;
			}
	
			if(!isset($_SESSION['R_carroVenta']) or $_SESSION['R_carroVenta']==''){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
				break;
			}		
			
			foreach($_SESSION['R_carroVenta'] as $v){
			    if($_POST['txt'.$v['idproducto']]>=0){
			         $totalventa = $totalventa + $_POST['txt'.$v['idproducto']]*$v['precioventa'];
				     $res = $objMovimientoAlmacen->insertarDetalleAlmacen($idpedido,$v['idproducto'],$v['idunidad'],$_POST['txt'.$v['idproducto']],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
				     //INICIO BITACORA
				     $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$idpedido.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST['txt'.$v['idproducto']].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				     //FIN BITACORA
				     if($res==1){
					   $objMovimientoAlmacen->abortarTransaccion(); 
					   $objBitacora->abortarTransaccion();
					   if(ob_get_length()) ob_clean();
					   echo "Error de Proceso en Lotes2: ".$objMovimientoAlmacen->gMsg;
					   break 3;
				     }
                }
			}
        
        //->
         
        //-->NUEVO DOCUMENTO DE VENTA DEL PEDIDO CREADO 
         
		if($_POST["cboIdTipoDocumento"]!=5){
			$_POST["txtSubtotal"]=$_POST["txtTotalSubcuenta"];
			$_POST["txtIgv"]=0;
		}
            $idbanco="";
            $idtipotarjeta="";
            $numerotarjeta="";
            $totalpagado=$_POST["txtTotalSubcuenta"];
                
		//Inserto Documento Venta; editado con el tipo de pago
		$rst = $objMovimiento->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotalSubcuenta"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], NULL, NULL, $_POST["txtComentario"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			break 2;
		}
		$dato=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>2; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>'.$_POST["optMoneda"].'; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotalSubcuenta"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta =>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($_POST["cboIdTipoDocumento"]==4){
			$tipodocabreviatura='B/V';
		}elseif($_POST["cboIdTipoDocumento"]==5){
			$tipodocabreviatura='F/V';
		}if($_POST["cboIdTipoDocumento"]==6){
			$tipodocabreviatura='T/V';
		}
		//Inserto movimiento caja; editado con el tipo de pago
		$numero = $objMovimiento->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
		$rst = $objMovimiento->insertarMovimiento(3, 4, $numero, 9, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotalSubcuenta"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $dato->idmovimiento, $_SESSION['R_IdSucursal'], 'Documento Venta '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
		$datoc=$rst->fetchObject();
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>3; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>'.$_POST["optMoneda"].'; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotalSubcuenta"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>'.$dato->idmovimiento.'; idsucursalref=>'.$_SESSION['R_IdSucursal'].'; comentario=>Documento Venta Nro: '.$_POST["txtNumero"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta=>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $datoc->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion();
			$objStockProducto->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
			break 3;
		}
		if(!isset($_SESSION['R_carroVenta']) or $_SESSION['R_carroVenta']==''){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
			break;
		}
		//Inserto Detallde Documento Venta
		$iniciaproceso3=date("Y-n-j H:i:s");
		$cuenta=0;
		$comandas='Comanda Nro: ';
		$nropedidocomanda='';
		foreach($_SESSION['R_carroVenta'] as $v){
		  $idpedidooriginal = $v["idpedido"];
			if($_POST['txtPersona']=='VARIOS'){
				if($cuenta==0){
					$nombrespersona=$objMovimiento->consultarNombreClientePedido($idpedido);
					//cambio nombre estatico del cliente en doc venta
					$res=$objMovimiento->actualizarNombresClienteMovimiento($dato->idmovimiento,$nombrespersona);
					if(is_string($res)){
						$objMovimiento->abortarTransaccion(); 
						$objMovimientoAlmacen->abortarTransaccion(); 
						$objBitacora->abortarTransaccion(); 
						$objStockProducto->abortarTransaccion();
						if(ob_get_length()) ob_clean();
						echo "Error de Proceso en Lotes4.1: ".$objGeneral->gMsg;
						break 5;
					}
					//cambio nombre estatico del cliente en mov caja
					$res=$objMovimiento->actualizarNombresClienteMovimiento($datoc->idmovimiento,$nombrespersona);
					if(is_string($res)){
						$objMovimiento->abortarTransaccion(); 
						$objMovimientoAlmacen->abortarTransaccion(); 
						$objBitacora->abortarTransaccion(); 
						$objStockProducto->abortarTransaccion();
						if(ob_get_length()) ob_clean();
						echo "Error de Proceso en Lotes4.2: ".$objGeneral->gMsg;
						break 5;
					}
					$cuenta++;
				}
			}
            
            if($_POST["txt".$v["idproducto"]]>0){
            
			//concateno los numeros de pedido que van en el comentario
			if($v['nropedido']!=$nropedidocomanda){
				$comandas.=$v['nropedido'].', ';
				$nropedidocomanda=$v['nropedido'];
			}
			$res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$_POST["txt".$v["idproducto"]],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
			if(is_string($res)){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes5: ".$objGeneral->gMsg;
				break 5;
			}
			$dato2=$res->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST["txt".$v["idproducto"]].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], $dato2->iddetallemovalmacen,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			
			if($v['kardex']=='S'){
				$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$_POST["txt".$v["idproducto"]],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST["txt".$v["idproducto"]].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res!='Guardado correctamente'){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;
				}
			}elseif($v['kardex']!='S' and $v['compuesto']=='S'){
				$res=$objStockProducto->insertarcompuesto($_SESSION['R_IdSucursal'],$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$_POST["txt".$v["idproducto"]],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST["txt".$v["idproducto"]].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res!='Guardado correctamente'){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					$objStockProducto->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
					break 3;
				}	
			}
			
			$res = $objMovimiento->insertarDetalleMovimiento($dato->idmovimiento,$idpedido,$dato2->iddetallemovalmacen);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 59, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idmovimientoref=>'.$idpedido.'; iddetallemovimientoalmacen=>'.$dato2->iddetallemovalmacen.'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion();
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes6: ".$objGeneral->gMsg;
				break 6;
			}
			
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$idpedido);
			$dax = $rt->fetchObject();
		
			$res = $objMovimiento->actualizarMontoPagadoMovimiento($idpedido,$v['precioventa']*$_POST["txt".$v["idproducto"]]);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$idpedido.'; totalpagado=> De: '.$dax->totalpagado.' a: '.$v['precioventa']*$_POST["txt".$v["idproducto"]].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], $v['idpedido'],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion();
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes7: ".$objGeneral->gMsg;
				break 7;
			}
			$iniciaproceso4.="$$".date("Y-n-j H:i:s");
            
             $v["cantidad"]=$v["cantidad"]-$_POST["txt".$v["idproducto"]];
             if($v["cantidad"]==0){
                $idproducto = $v["idproducto"];
                unset($_SESSION['R_carroVenta'][($idproducto)]);
             }
		}
		
        }
        	
		$comandas=substr($comandas,0,strlen($comandas)-2);
		$res=$objMovimiento->actualizarComentarioMovimiento($dato->idmovimiento,$comandas);
		if(is_string($res)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
			break 5;
		}
			$iniciaproceso5=date("Y-n-j H:i:s");		
		$res=$objMovimiento->cambiarSituacionPedido($dato->idmovimiento,'P');$iniciaproceso6=date("Y-n-j H:i:s");		
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimieneto=>'.$dato->idmovimiento.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>P; nota: la situacion hace referencia a los pedidos que pertenecen al documento de venta', $_SESSION['R_IdSucursal'],$dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);$iniciaproceso7=date("Y-n-j H:i:s");		
		//FIN BITACORA
		if($res!='Guardado correctamente'){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
			break 5;
		}
		
		if($res=='Guardado correctamente'){
			$objMovimiento->finalizarTransaccion(); 
			$objMovimientoAlmacen->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion(); 
			$objStockProducto->finalizarTransaccion();
		}
		
		if($_POST["cboIdTipoDocumento"]==4){//boleta
			echo "setRun('vista/frmComprobanteB','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
		}else{
			if($_POST["cboIdTipoDocumento"]==5){//factura
				echo "setRun('vista/frmComprobanteF','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
			}else{//ticket
				//echo "alert('Guardado Correctamente.".$iniciaproceso."$$".$iniciaproceso2."$$".$iniciaproceso3."$$$".$iniciaproceso4."$$".$iniciaproceso5."$$".$iniciaproceso6."$$".$iniciaproceso7."$$".date("Y-n-j H:i:s")."');document.getElementById('cargamant').innerHTML=''";
				echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
			}
		}
        
        $objMovimientoAlmacen->iniciarTransaccion();
        $objBitacora->iniciarTransaccion(); 
        $res = $objMovimientoAlmacen->eliminarDetalleAlmacen($idpedidooriginal);
        if($res=="Guardado Correctamente"){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
				break 3;
		}
            
        foreach($_SESSION['R_carroVenta'] as $k => $v){	
            $total = $total + $v['cantidad']*$v['precioventa'];
            $res = $objMovimientoAlmacen->insertarDetalleAlmacen($idpedidooriginal,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro+Division Cuenta', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v['idsucursalproducto'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
				break 3;
			}
        }
        
        
            
        //-->FIN DE SUBCUENTA    
            
        }
        
		break;
	case "ACTUALIZAR" :
		//PENDIENTE
		if(ob_get_length()) ob_clean();
		//echo umill($objMovimiento->actualizarMovimiento($_POST["txtIdSucursal"], $_POST["txtIdMovimiento"],$_POST["txtIdMovimientoMaestro"],$_POST["cboDist"],$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],$_POST["txtImagen"]));
		echo "pendiente";
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$_POST["txtId"]);
			$dax = $rt->fetchObject();
			$fechacierre=$dax->fecha;
			$cierre=$objCaja->consultarultimocierrefecha($fechacierre);
			/*echo $fechacierre.' - '.$cierre.' - '.$_POST['txtId'];
			break 1;*/
			//SI NO HAY CIERRE
			//echo $cierre;
			if($cierre<$_POST['txtId']){
				$objMovimiento->iniciarTransaccion();
				$objBitacora->iniciarTransaccion(); 
				$objStockProducto->iniciarTransaccion(); 
				//ANULO MOVIMIENTOS DE CAJA A PARTIR DEL DOC VENTA
				$objMovimiento->anularMovCajaaPartirdeVenta($_POST['txtId']);
				//CAMBIO PEDIDOS REFERENCIADOS A LA SITUACION A->ATENDIDA (OSEA PENDIENTE DE PAGO)
				$objMovimiento->cambiarSituacionPedidoaPartirdeVenta($_POST['txtId']);
				//ELIMINAR VENTA
				$objMovimiento->eliminarMovimiento($_POST['txtId']);
				//RENUEVO STOCK
				$res=$objStockProducto->revertir($_SESSION['R_IdSucursal'],$_POST['txtId'],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario'],'A');
				//INICIO BITACORA
				//eliminar movimiento
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 44, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res!="Guardado correctamente"){
						$objMovimiento->abortarTransaccion(); 
						$objBitacora->abortarTransaccion();
						$objStockProducto->abortarTransaccion();  
						if(ob_get_length()) ob_clean();
						echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
						break 2;
					}
				if($res=="Guardado correctamente"){
					$objMovimiento->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion(); 
					$objStockProducto->finalizarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Guardado correctamente";
				}
				//echo "Guardado Correctamente !!!";
			}else{//SI HAY CIERRE
				echo "No se puede eliminar este documento porque la caja esta cerrada !!!";
			}
		break;
	case "ANULAR" :
		if(ob_get_length()) ob_clean();
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$_POST["txtId"]);
			$dax = $rt->fetchObject();
			$fechacierre=$dax->fecha;
			$cierre=$objCaja->consultarultimocierrefecha($fechacierre);
			/*echo $fechacierre.' - '.$cierre.' - '.$_POST['txtId'];
			break 1;*/
			//SI NO HAY CIERRE
			//echo $cierre;
			if($cierre<$_POST['txtId']){
				$objMovimiento->iniciarTransaccion();
				$objBitacora->iniciarTransaccion(); 
				$objStockProducto->iniciarTransaccion(); 
				//ANULO MOVIMIENTOS DE CAJA A PARTIR DEL DOC VENTA
				$objMovimiento->anularMovCajaaPartirdeVenta($_POST['txtId']);
				//CAMBIO PEDIDOS REFERENCIADOS A LA SITUACION A->ATENDIDA (OSEA PENDIENTE DE PAGO)
				$objMovimiento->cambiarSituacionPedidoaPartirdeVenta($_POST['txtId']);
				//ANULO VENTA
				$objMovimiento->anularMovimiento($_POST['txtId']);
				//RENUEVO STOCK
				$res=$objStockProducto->revertir($_SESSION['R_IdSucursal'],$_POST['txtId'],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario'],'I');
				//INICIO BITACORA
				//eliminar movimiento
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 44, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res!="Guardado correctamente"){
						$objMovimiento->abortarTransaccion(); 
						$objBitacora->abortarTransaccion();
						$objStockProducto->abortarTransaccion();  
						if(ob_get_length()) ob_clean();
						echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
						break 2;
					}
				if($res=="Guardado correctamente"){
					$objMovimiento->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion(); 
					$objStockProducto->finalizarTransaccion(); 
					if(ob_get_length()) ob_clean();
					echo "Guardado correctamente";
				}
				//echo "Guardado Correctamente !!!";
			}else{//SI HAY CIERRE
				echo "No se puede anular este documento porque la caja esta cerrada !!!";
			}
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          