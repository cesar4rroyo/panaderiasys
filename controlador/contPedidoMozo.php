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
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objCaja = new clsMovCaja(48,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objStockProducto = new clsStockProducto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

switch($accion){
	case "NUEVO" :
		if($_POST["cuenta"]!=1) {echo "Espera";exit();}
		$apertura=$objCaja->consultarapertura();
		if($apertura==0){ echo "Apertura"; exit();}
		if(ob_get_length()) ob_clean();
	    $objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
        $objMesa->iniciarTransaccion();
		$idsucursalref=NULL;$idmovimientoref=NULL;		
        $numero = $objMovimientoAlmacen->generaNumeroxMesero(6,$_SESSION['R_IdSucursal']);
        $_POST["txtNumeroComanda"]=$numero;
		$_POST["txtNumeroComanda"]=str_pad(trim($_POST["txtNumeroComanda"]),6,"0",STR_PAD_LEFT);
		//$datosR=split('-',$_POST["cboIdResponsable"]);
        if($_POST["Nropersonas"]==""){
            $_POST["Nropersonas"]=0;
        }
        if($_POST["Idmesa"]=="10"){
            $nombrespersona=$_POST["txtNombrePersona"];
            $rs=$objMovimientoAlmacen->obtenerDataSQL("select * from mesa where idsucursal=".$_SESSION["R_IdSucursal"]." and situacion='N' and idsalon=1 order by idsalon,idmesa asc");
            if($rs->rowCount()>0){
                $_POST["Idmesa"]=$rs->fetchObject()->idmesa;
            }else{
                $objMovimientoAlmacen->abortarTransaccion(); 
                $objBitacora->abortarTransaccion();
                $objMesa->abortarTransaccion();
                if(ob_get_length()) ob_clean();
                echo "Mesa no esta libre";
                exit();
            }
        }else{
            $nombrespersona="";
        }
        if(trim($objMesa->verificaSituacion($_POST["Idmesa"]))!="N"){
            $objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
            $objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Mesa no esta libre";
			exit();
        }
		$res = $objMovimientoAlmacen->insertarMovimiento(0, 5, $_POST["txtNumeroComanda"], 11, '', 'LOCALTIMESTAMP', '', '', $_POST["Nropersonas"], $_POST["Idmesa"], 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', 0, $_SESSION['R_IdPersona'], $idmovimientoref, $idsucursalref, '-' ,'O',0,$_SESSION['R_IdSucursalUsuario'],0,$_SESSION['R_IdSucursalUsuario'],$nombrespersona);
		$dato=$res->fetchObject();
        $idpedido = $dato->idmovimiento;
		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>5; numero=>'.$_POST["txtNumeroComanda"].'; idtipodocumento=>11; formapago=>; fecha=>'.date("d/m/Y").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>'.$_POST["Nropersonas"].'; idmesa=>'.$_POST["Idmesa"].'; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotal"].'; igv=>0; total=>'.$_POST["txtTotal"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>0; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>'."-".'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; nombrespersona=>'." ", $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($res)){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
            $objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objMovimientoAlmacen->gMsg;
			exit();
		}

		if(!isset($_SESSION['R_carroPedidoMozo']) or $_SESSION['R_carroPedidoMozo']==''){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
            $objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
			exit();
		}		
		
		foreach($_SESSION['R_carroPedidoMozo'] as $v){
            $totalventa = $totalventa + $v['cantidad']*$v['precioventa'];
            $v['comentario']=str_replace("'","",$v['comentario']);
			$res = $objMovimientoAlmacen->insertarDetalleAlmacen($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],'',$v["comentario"]);
			if(count($v["carroDetalle"])>0){
                foreach($v["carroDetalle"] as $x => $y){
                    //$objMovimientoAlmacen->insertarDetalleMovCategoria($dato->idmovimiento,$_SESSION["R_IdSucursal"],$v["idproducto"],$y["iddetallecategoria"]);
                    $objMovimientoAlmacen->ejecutarSQL("INSERT INTO detallemovcategoria(idmovimiento,idsucursal,idproducto,iddetallecategoria,numeroplato) VALUES (".$dato->idmovimiento.",".$_SESSION["R_IdSucursal"].",".$v["idproducto"].",".$y["iddetallecategoria"].",".$y["numeroplato"].");");
                }
            }
            //INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
                $objMesa->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objMovimientoAlmacen->gMsg;
				exit();
			}
		}
		$vresp=$objMesa->verificaMesaLibre($_POST["Idmesa"]);
		if($vresp!=1){
			$res = $objMesa->cambiarSituacion($_POST["Idmesa"],$_SESSION['R_IdSucursal'],'O');
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["Idmesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>O', $_SESSION['R_IdSucursal'],$_POST["Idmesa"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
                $objMesa->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objMesa->gMsg;
				exit();
			}
		}
        
        //Para venta en barra
        if($_POST["Idmesa"]==0){
            if(ob_get_length()) ob_clean();
    		//CAJA
    		$iniciaproceso=date("Y-n-j H:i:s");
    		$apertura=$objCaja->consultarapertura();
    		//si la apertura es != 0 o vacio es por que ya hay apertura
    		if($apertura==0){
    		$montosoles= $objCaja->montodeaperturasoles();
    		//$montodolares= $objCaja->montodeaperturadolares();
    		$num_mov=$objCaja->existenciamov();
    		if($num_mov==0){
    			$objMovimientoAlmacen->iniciarTransaccion();
    			$objBitacora->iniciarTransaccion();
    			$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
    			$rst = $objMovimientoAlmacen->insertarMovimiento(1, 4, $numero, 9, 'A', $_SESSION["R_FechaProceso"], '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas','N');
    			$dato=$rst->fetchObject();
    			//INICIO BITACORA
    			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_SESSION['R_FechaProceso'].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de ventas; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    			//FIN BITACORA
    			if(is_string($rst)){
    				$objMovimientoAlmacen->abortarTransaccion(); 
    				$objBitacora->abortarTransaccion(); 
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes11: ".$objCaja->gMsg;
    				exit();
    			}else{
    				$objMovimientoAlmacen->finalizarTransaccion(); 
    				$objBitacora->finalizarTransaccion();
    				if(ob_get_length()) ob_clean();
    			}
    		}else{
    			$fechacierre=$objCaja->consultarmaxfecha();
    			$cierre=$objCaja->consultarcierre($fechacierre);
    			//SI NO HAY CIERRE
    			if($cierre==0){
    			//NAA XD!
    			}else{
    				//SI HAY CIERRE
    				$objMovimientoAlmacen->iniciarTransaccion();
    				$objBitacora->iniciarTransaccion();
    				$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
    				$rst = $objMovimientoAlmacen->insertarMovimiento(1, 4, $numero, 9, 'A', $_SESSION['R_FechaProceso'], '', '', 0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas '.date("Y-n-j H:i:s"),'N');
    				$dato=$rst->fetchObject();
    				//INICIO BITACORA
    				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_SESSION['R_FechaProceso'].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de ventas; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    				//FIN BITACORA
    
    				if(is_string($rst)){
    					$objMovimientoAlmacen->abortarTransaccion();
    					$objBitacora->abortarTransaccion(); 
    					if(ob_get_length()) ob_clean();
    					echo "Error de Proceso en Lotes12: ".$objCaja->gMsg;
    					exit();
    				}else{
    					$objMovimientoAlmacen->finalizarTransaccion(); 
    					$objBitacora->finalizarTransaccion(); 
    					if(ob_get_length()) ob_clean();
    				}	
    				}
    		}
    		}
    		//VENTA
    		$iniciaproceso2=date("Y-n-j H:i:s");
    		$objMovimiento->iniciarTransaccion();
    		//$objMovimientoAlmacen->iniciarTransaccion();
    		$objBitacora->iniciarTransaccion(); 
    		$objStockProducto->iniciarTransaccion(); 
    		
            $_POST["txtFecha"]=$_SESSION["R_FechaProceso"];
            $_POST['txtPersona']='VARIOS';
            $_POST["cboIdTipoDocumento"]=6;//venta con ticket
            $_POST["txtTotal"]=$totalventa;
            $_POST["optMoneda"]='S';
            $_POST["txtComentario"]="Venta Rapida de la barra desde plataforma mozo";
            $_POST['cboIdCaja']=1;
            $_POST["txtIdPersona"]= $_SESSION['R_IdPersona'];
    		if($_POST["cboIdTipoDocumento"]!=5){
    			$_POST["txtSubtotal"]=$_POST["txtTotal"];
    			$_POST["txtIgv"]=0;
    		}
                $idbanco="";
                $idtipotarjeta="";
                $numerotarjeta="";
                $totalpagado=$_POST["txtTotal"];
            
    		//Inserto Documento Venta; editado con el tipo de pago
            $_POST["txtNumero"]=$objMovimiento->generaNumero(2,6,substr($_SESSION["R_FechaProceso"],6,4));
    		//$rst = $objMovimiento->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_SESSION['R_IdPersona'], $_SESSION['R_IdPersona'], NULL, NULL, "Venta Rapida de la barra desde plataforma mozo",'N','1',$_SESSION['R_IdSucursalUsuario'],$_SESSION['R_IdSucursalUsuario'],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
            $rst = $objMovimientoAlmacen->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], NULL, NULL, $_POST["txtComentario"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);		
            if(is_string($rst)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion();
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion(); 
                            $objMesa->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes2: ".$objMovimiento->gMsg;
    			exit();
    		}
    		$dato=$rst->fetchObject();
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>2; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_SESSION['R_IdPersona'].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>Venta rapida de la barra desde la plataforma mozo; situacion=>N; estado=>N; idcaja=>1; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta =>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
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
    		$rst = $objMovimientoAlmacen->insertarMovimiento(3, 4, $numero, 9, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $dato->idmovimiento, $_SESSION['R_IdSucursal'], 'Documento Venta '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"],'N',1,$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
    		$datoc=$rst->fetchObject();
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>3; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_SESSION['R_IdPersona'].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>'.$dato->idmovimiento.'; idsucursalref=>'.$_SESSION['R_IdSucursal'].'; comentario=>Documento Venta Nro: '.$_POST["txtNumero"].'; situacion=>N; estado=>N; idcaja=>1; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta=>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $datoc->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    		//FIN BITACORA
    		if(is_string($rst)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion();
    			$objBitacora->abortarTransaccion();
    			$objStockProducto->abortarTransaccion();
                            $objMesa->abortarTransaccion(); 
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
    			exit();
    		}
    		//Inserto Detalle de Documento Venta
    		$iniciaproceso3=date("Y-n-j H:i:s");
    		$cuenta=0;
    		$comandas='Comanda Nro: ';
    		$nropedidocomanda='';
    		foreach($_SESSION['R_carroPedidoMozo'] as $v){
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
    			                        $objMesa->abortarTransaccion();
    						if(ob_get_length()) ob_clean();
    						echo "Error de Proceso en Lotes4.1: ".$objMovimiento->gMsg;
    						exit();
    					}
    					//cambio nombre estatico del cliente en mov caja
    					$res=$objMovimiento->actualizarNombresClienteMovimiento($datoc->idmovimiento,$nombrespersona);
    					if(is_string($res)){
    						$objMovimiento->abortarTransaccion(); 
    						$objMovimientoAlmacen->abortarTransaccion(); 
    						$objBitacora->abortarTransaccion(); 
    						$objStockProducto->abortarTransaccion();
    			                        $objMesa->abortarTransaccion();
    						if(ob_get_length()) ob_clean();
    						echo "Error de Proceso en Lotes4.2: ".$objMovimiento->gMsg;
    						exit();
    					}
    					$cuenta++;
    				}
    			}
    				$comandas.=$_POST['txtNumeroComanda'].', ';
    				$nropedidocomanda=$_POST["txtNumeroComanda"];
    			
    			$res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
    			if(is_string($res)){
    				$objMovimiento->abortarTransaccion(); 
    				$objMovimientoAlmacen->abortarTransaccion(); 
    				$objBitacora->abortarTransaccion(); 
    				$objStockProducto->abortarTransaccion();
    	                        $objMesa->abortarTransaccion();
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes5: ".$objMovimientoAlmacen->gMsg;
    				exit();
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
    		                        $objMesa->abortarTransaccion();
    					if(ob_get_length()) ob_clean();
    					echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
    					exit();
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
    					$objMovimientoAlmacen->abortarTransaccion();
    		                        $objMesa->abortarTransaccion();
    					if(ob_get_length()) ob_clean();
    					echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
    					exit();
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
    	                        $objMesa->abortarTransaccion();
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes6: ".$objMovimiento->gMsg;
    				exit();
    			}
    			
    			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$idpedido);
    			$dax = $rt->fetchObject();
    		
    			$res = $objMovimientoAlmacen->actualizarMontoPagadoMovimiento($idpedido,$v['precioventa']*$v['cantidad']);
    			//INICIO BITACORA
    			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$idpedido.'; totalpagado=> De: '.$dax->totalpagado.' a: '.$v['precioventa']*$v['cantidad'].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], $idpedido,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    			//FIN BITACORA
    			if($res!='Guardado correctamente'){
    				$objMovimiento->abortarTransaccion(); 
    				$objMovimientoAlmacen->abortarTransaccion();
    				$objBitacora->abortarTransaccion(); 
    				$objStockProducto->abortarTransaccion();
    	                        $objMesa->abortarTransaccion();
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes7: ".$objMovimiento->gMsg;
    				exit();
    			}
    			$iniciaproceso4.="$$".date("Y-n-j H:i:s");            
    		}
    			
    		$comandas=substr($comandas,0,strlen($comandas)-2);
    		$res=$objMovimientoAlmacen->actualizarComentarioMovimiento($dato->idmovimiento,$comandas);
    		if(is_string($res)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion(); 
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion();
                            $objMesa->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes8: ".$objMovimiento->gMsg;
    			exit();
    		}
    			$iniciaproceso5=date("Y-n-j H:i:s");		
    		$res=$objMovimientoAlmacen->cambiarSituacionPedido($dato->idmovimiento,'P');$iniciaproceso6=date("Y-n-j H:i:s");
            $objMovimiento->cambiarSituacionPedido($dato->idmovimiento,'P');		
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimieneto=>'.$dato->idmovimiento.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>P; nota: la situacion hace referencia a los pedidos que pertenecen al documento de venta', $_SESSION['R_IdSucursal'],$dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);$iniciaproceso7=date("Y-n-j H:i:s");		
    		//FIN BITACORA
    		if($res!='Guardado correctamente'){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion(); 
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion();
                            $objMesa->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes8: ".$objMovimiento->gMsg;
    			exit();
    		}
    		
    		if($res=='Guardado correctamente' || $res==0 || !is_string($rst)){
    			$objMovimiento->finalizarTransaccion(); 
    			$objMovimientoAlmacen->finalizarTransaccion(); 
    			$objBitacora->finalizarTransaccion(); 
    			$objStockProducto->finalizarTransaccion();
                            $objMesa->finalizarTransaccion();
                $objMovimiento->cambiarSituacionPedido($dato->idmovimiento,'P');
                if(ob_get_length()) ob_clean();
    		    echo "Guardado correctamente";
                exit();
    		}
            //Fin de venta rapida
            
        }else {
            if($res==0 || $res=='Guardado correctamente'){
			    $objMovimientoAlmacen->finalizarTransaccion(); 
			    $objBitacora->finalizarTransaccion();
                $objMesa->finalizarTransaccion();
			    if(ob_get_length()) ob_clean();
			    echo "Guardado correctamente";
                exit();
		     }
        }
		exit();
    case "NUEVO2" :
		// print_r($_POST);
		// return 1;
		if($_POST["cuenta"]!=1) {echo "Espera";exit();}
		$apertura=$objCaja->consultarapertura($_SESSION["R_IdCaja"]);
		if($apertura==0){ echo "Apertura"; exit();}
		if(ob_get_length()) ob_clean();
	    $objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
        $objMesa->iniciarTransaccion();
		$idsucursalref=NULL;$idmovimientoref=NULL;		
		//$_POST["txtNumeroComanda"]=str_pad(trim($_POST["txtNumeroComanda"]),6,"0",STR_PAD_LEFT);
        $_POST["txtNumeroComanda"] = $objMovimiento->generaNumeroxMesero(6,$_SESSION['R_IdSucursal']);
		//$datosR=split('-',$_POST["cboIdResponsable"]);
        if($_POST["Nropersonas"]==""){
            $_POST["Nropersonas"]=0;
        }
        /*if(trim($objMesa->verificaSituacion($_POST["Idmesa"]))!="N"){
            $objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
            $objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Mesa no esta libre";
			exit();
        }*/
        $_POST["cliente"]=$_POST["txtNombrePersona"];
		$res = $objMovimientoAlmacen->insertarMovimiento(0, 5, $_POST["txtNumeroComanda"], 11, '', 'LOCALTIMESTAMP', '', '', $_POST["Nropersonas"], $_POST["Idmesa"], 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_POST['idusuario'], 'P', 0, $_POST['idpersona'], $idmovimientoref, $idsucursalref, $_POST["comentario"] ,'O',0,$_SESSION['R_IdSucursalUsuario'],0,$_SESSION['R_IdSucursalUsuario'],$_POST["cliente"]);
		$dato=$res->fetchObject();
	    $idpedido = $dato->idmovimiento;
		//ACTUALIZAR CON NOMBRE DE IMPRESION
		$objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy set nombrespersona='".$_POST["txtNombreImprimir"]."' where idmovimiento=".$idpedido);
		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>5; numero=>'.$_POST["txtNumeroComanda"].'; idtipodocumento=>11; formapago=>; fecha=>'.date("d/m/Y").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>'.$_POST["Nropersonas"].'; idmesa=>'.$_POST["Idmesa"].'; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotal"].'; igv=>0; total=>'.$_POST["txtTotal"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>0; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>'."-".'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; nombrespersona=>'." ", $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($res)){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
            $objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objMovimientoAlmacen->gMsg;
			exit();
		}

		if(!isset($_SESSION['R_carroPedidoMozo']) or $_SESSION['R_carroPedidoMozo']==''){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
            $objMesa->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
			exit();
		}		
		$totalcomanda=0;
		foreach($_SESSION['R_carroPedidoMozo'] as $v){
            $totalventa = $totalventa + $v['cantidad']*$v['precioventa'];
            $v['comentario']=str_replace("'","",$v['comentario']);
            $totalcomanda = $totalcomanda + $v["cantidad"]*$v["precioventa"];
			$res = $objMovimientoAlmacen->insertarDetalleAlmacen($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],'',$v["comentario"]);
			if(count($v["carroDetalle"])>0){
                foreach($v["carroDetalle"] as $x => $y){
                    //$iddetallemovalmacen=$objMovimientoAlmacen->insertarDetalleMovCategoria($dato->idmovimiento,$_SESSION["R_IdSucursal"],$v["idproducto"],$y["iddetallecategoria"]);
                    $objMovimientoAlmacen->ejecutarSQL("INSERT INTO detallemovcategoria(idmovimiento,idsucursal,idproducto,iddetallecategoria,numeroplato) VALUES (".$dato->idmovimiento.",".$_SESSION["R_IdSucursal"].",".$v["idproducto"].",".$y["iddetallecategoria"].",".$y["numeroplato"].");");
                }
            }
            //INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
                $objMesa->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objMovimientoAlmacen->gMsg;
				exit();
			}
		}
        if($_POST["tipoVenta"]!="A"){
            $_POST["txtTotal"] = $totalcomanda;
        }
		/*$vresp=$objMesa->verificaMesaLibre($_POST["Idmesa"]);
		if($vresp!=1){
			$res = $objMesa->cambiarSituacion($_POST["Idmesa"],$_SESSION['R_IdSucursal'],'O');
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["Idmesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>O', $_SESSION['R_IdSucursal'],$_POST["Idmesa"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
                $objMesa->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objMesa->gMsg;
				exit();
			}
		}*/
            if(ob_get_length()) ob_clean();
    		//CAJA
    		$iniciaproceso=date("Y-n-j H:i:s");
    		//VENTA
    		$iniciaproceso2=date("Y-n-j H:i:s");
    		$objMovimiento->iniciarTransaccion();
    		//$objMovimientoAlmacen->iniciarTransaccion();
    		$objBitacora->iniciarTransaccion(); 
    		$objStockProducto->iniciarTransaccion(); 
    		
            $_POST["txtFecha"]=$_SESSION["R_FechaProceso"];
            $_POST['txtPersona']='VARIOS';
            //$_POST["cboIdTipoDocumento"]=19;//venta con ticket
            if($_POST["tipoVenta"]=="A"){//PAGO ANTICIPADO
                $_POST["txtTotal"]=$totalventa;
            }
            $_POST["optMoneda"]='S';
            $_POST["txtComentario"]="@".trim($_POST["glosa_movimiento"]);
            if($_SERVER['REMOTE_ADDR']=="192.168.0.51"){//caja2
                $_POST['cboIdCaja']=3;
            /*}elseif($_SERVER['REMOTE_ADDR']=="192.168.1.52"){//caja2
                $_POST['cboIdCaja']=2;
            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.53"){//caja3
                $_POST['cboIdCaja']=3;
            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.54"){//caja4
                $_POST['cboIdCaja']=4;
            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.58"){//caja5
                $_POST['cboIdCaja']=5;
            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.56"){//caja6
                $_POST['cboIdCaja']=6;
            }elseif($_SERVER['REMOTE_ADDR']=="192.168.1.57"){//boleteria
                $_POST['cboIdCaja']=7;*/
            }else{
                $_POST['cboIdCaja']=2;
            }

            if($_POST["tipoVenta"]=="C"){
                $tipoVenta="C";
            }elseif($_POST["tipoVenta"]=="A"){
                $tipoVenta="A";
                $idpagoanticipado = $_POST["idpagoanticipado"];
            }else{
                $tipoVenta="N";
            }

            if($tipoVenta=="C"){
                $_POST["txtComentario"] = "VENTA AL CREDITO";
                $_POST["txtSubcuenta"]="NO";
                $_POST["rdbtnModoPago"]="E";
                $totalpagado=0;
                //$_POST["txtNumero"] = "000-000000-0000";
                $objMovimientoAlmacen->ejecutarSQL("INSERT INTO ventacredito (plazo,total,fecha_consumo,idusuario,idcliente,idmovimiento) VALUES (30,".$_POST["txtTotal"].",'".$_POST["txtFecha"]."',".$_SESSION['R_IdUsuario'].",".$_POST["txtIdPersona"].",".$idpedido.")");
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET situacion = 'P',tipoventa='".$tipoVenta."',idpersona = ".$_POST["txtIdPersona"]." WHERE idmovimiento = ".$idpedido);
            }

            //$_POST["txtIdPersona"]= $_SESSION['R_IdPersona'];
    		if($_POST["cboIdTipoDocumento"]!=5){
    			$_POST["txtSubtotal"]=$_POST["txtTotal"];
    			$_POST["txtIgv"]=0;
    		}elseif($_POST["cboIdTipoDocumento"]=="5"){
                $_POST["txtSubtotal"]=round($_POST["txtTotal"]/1.18,2);
                $_POST["txtIgv"]=$_POST["txtTotal"]-$_POST["txtSubtotal"];
            }
            if($_POST["rdbtnModoPago"]=="E"){
                $idbanco="";
                $idtipotarjeta="";
                $numerotarjeta="";
                $totalpagado=$_POST["txtTotal"];
            }elseif($_POST["rdbtnModoPago"]=="T"){
                $idbanco="";
                $idtipotarjeta=$_POST["cboTipoTarjeta"];
                $numerotarjeta="";
                $totalpagado="0.00";
            }elseif($_POST["rdbtnModoPago"]=="A"){
                $idbanco="";
                //$idtipotarjeta=$_POST["cboTipoTarjeta"];
                $idtipotarjeta=0;
                $numerotarjeta="";
                $totalpagado=$_POST["txtTotal"] - $_POST["txtMontoVisa"] - $_POST["txtMontoMastercard"];
                //$totalpagado=$_POST["txtPagoEfectivo"];
            }elseif($_POST["rdbtnModoPago"]=="OTROS"){
                $idbanco="";
                $idtipotarjeta=1;
                $numerotarjeta="";
                $totalpagado=$_POST["txtMontoEfectivoVarios"];
				$importe_deposito = $_POST["txtMontoYapeVarios"] + $_POST["txtMontoPlinVarios"];
				$_POST["txtMontoVisa"] = $_POST["txtMontoVisaVarios"];
            }elseif($_POST["rdbtnModoPago"]=="TRANS"){
				$idbanco="";
                $idtipotarjeta="";
                $numerotarjeta="";
				$numero_deposito="";
                $totalpagado=0;
				$fecha_deposito='';
				$importe_deposito = $_POST["txtTotal"];
				$banco_deposito = $_POST["moneda_cheque"];
			}
            $numerotarjeta=$_POST["glosa_movimiento1"];//MASTER
    		//Inserto Documento Venta; editado con el tipo de pago
            if($_POST["cboIdTipoDocumento"]==4){
                $serie="B00".$_POST["cboIdCaja"];
            }elseif($_POST["cboIdTipoDocumento"]==5){
                $serie="F00".$_POST["cboIdCaja"];
            }else{
                $serie="T00".$_POST["cboIdCaja"];
            }
            $_POST["txtNumeroVenta"] = $objMovimientoAlmacen->generaNumeroElectronico(2,$_POST["cboIdTipoDocumento"],substr($_SESSION["R_FechaProceso"],6,4),$serie);

            $_POST["txtNumero"]=$_POST["txtNumeroVenta"];
    		
            $rst = $objMovimientoAlmacen->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], NULL, NULL, $_POST["txtComentario"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);		
            if(is_string($rst)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion();
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion(); 
                $objMesa->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes2: ".$objMovimiento->gMsg;
    			exit();
    		}
    		$dato=$rst->fetchObject();
            $objMovimientoAlmacen->ejecutarSQL("update movimientohoy set manual='N' where idmovimiento=".$dato->idmovimiento." and idsucursal=".$_SESSION["R_IdSucursal"]);
			// //ACTUALIZAR CON NOMBRE DE IMPRESION
			// $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy set nombrespersona='".$_POST["txtNombreImprimir"]."' where idmovimiento=".$dato->idmovimiento);
            
            $objMovimientoAlmacen->ejecutarSQL("insert into detallestock(idventa,idpedido,idsucursal,situacion,estado) values(".$dato->idmovimiento.",".$idpedido.",".$_SESSION["R_IdSucursal"].",'P','N')");

    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>2; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_SESSION['R_IdPersona'].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>Venta rapida de la barra desde la plataforma mozo; situacion=>N; estado=>N; idcaja=>1; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta =>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    		//FIN BITACORA
            if($_POST["tipoVenta"]=="A"){//PAGO ANTICIPADO
                $_POST["txtTotal"]=$totalventa - $_POST["txtAnticipo"];
                if($_POST["rdbtnModoPago"]=="E"){
                    $totalpagado = $_POST["txtTotal"];
                }
            }
    		if($_POST["cboIdTipoDocumento"]==4){
    			$tipodocabreviatura='B/V';
    		}elseif($_POST["cboIdTipoDocumento"]==5){
    			$tipodocabreviatura='F/V';
    		}if($_POST["cboIdTipoDocumento"]==6){
    			$tipodocabreviatura='T/V';
    		}
            if($tipoVenta=="C"){
                $_POST["txtTotal"]=0;
                $totalpagado=0;
                $_POST["txtSubtotal"]=0;
                $_POST["txtIgv"]=0;
            }

    		//Inserto movimiento caja; editado con el tipo de pago
    		$numero = $objMovimiento->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
    		$rst = $objMovimientoAlmacen->insertarMovimiento(3, 4, $numero, 9, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $dato->idmovimiento, $_SESSION['R_IdSucursal'], 'Documento Venta '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"],'N',$_POST["cboIdCaja"],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
    		$datoc=$rst->fetchObject();
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>3; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_SESSION['R_IdPersona'].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>'.$dato->idmovimiento.'; idsucursalref=>'.$_SESSION['R_IdSucursal'].'; comentario=>Documento Venta Nro: '.$_POST["txtNumero"].'; situacion=>N; estado=>N; idcaja=>1; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta=>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $datoc->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            if($tipoVenta=="A"){
                $objMovimientoAlmacen->ejecutarSQL("insert into detallepagoanticipado(idpagoanticipado,idmovimiento,fechaconsumo,monto) values(".$idpagoanticipado.",".$datoc->idmovimiento.",'".$_POST["txtFecha"]."',".($_POST["txtTotal"]+$_POST["txtAnticipo"]).")");
                $objMovimientoAlmacen->ejecutarSQL("UPDATE pagoanticipado SET saldo=saldo-".($_POST["txtTotal"]+$_POST["txtAnticipo"]).",idmovimiento = ".$datoc->idmovimiento.", fecha_consumo = '".$_POST["txtFecha"]."',estado='E' WHERE idpagoanticipado = ".$idpagoanticipado);
            }

            $glosa='';
            if($_POST["rdbtnModoPago"]=="C"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."'"
                        . ", nombrebanco='".$banco_cheque."', numerocheque='".$numero_cheque."', monedacheque='".$moneda_cheque."' WHERE idmovimiento=".$datoc->idmovimiento);
            }elseif($_POST["rdbtnModoPago"]=="TRANS"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."'"
                        . ", nombrebanco='".$banco_deposito."', numerooperacion='".$numero_deposito."', importedeposito=".$importe_deposito." WHERE idmovimiento=".$datoc->idmovimiento);
            }elseif($_POST["rdbtnModoPago"]=="A"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET montotarjeta='1@".$_POST["txtMontoVisa"]."|2@".$_POST["txtMontoMastercard"]."',modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."' WHERE idmovimiento=".$datoc->idmovimiento);
            }elseif($_POST["rdbtnModoPago"]=="OTROS"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET montotarjeta='1@".$_POST["txtMontoVisa"]."',modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."', importedeposito=".$importe_deposito." WHERE idmovimiento=".$datoc->idmovimiento);
            }else{
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."' WHERE idmovimiento=".$datoc->idmovimiento);
            }
    		//FIN BITACORA
    		if(is_string($rst)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion();
    			$objBitacora->abortarTransaccion();
    			$objStockProducto->abortarTransaccion();
                $objMesa->abortarTransaccion(); 
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
    			exit();
    		}
    		//Inserto Detalle de Documento Venta
    		$iniciaproceso3=date("Y-n-j H:i:s");
    		$cuenta=0;
    		$comandas='Comanda Nro: ';
    		$nropedidocomanda='';
    		foreach($_SESSION['R_carroPedidoMozo'] as $v){
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
    			                        $objMesa->abortarTransaccion();
    						if(ob_get_length()) ob_clean();
    						echo "Error de Proceso en Lotes4.1: ".$objMovimiento->gMsg;
    						exit();
    					}
    					//cambio nombre estatico del cliente en mov caja
    					$res=$objMovimiento->actualizarNombresClienteMovimiento($datoc->idmovimiento,$nombrespersona);
    					if(is_string($res)){
    						$objMovimiento->abortarTransaccion(); 
    						$objMovimientoAlmacen->abortarTransaccion(); 
    						$objBitacora->abortarTransaccion(); 
    						$objStockProducto->abortarTransaccion();
    			                        $objMesa->abortarTransaccion();
    						if(ob_get_length()) ob_clean();
    						echo "Error de Proceso en Lotes4.2: ".$objMovimiento->gMsg;
    						exit();
    					}
    					$cuenta++;
    				}
    			}
				$comandas.=$_POST['txtNumeroComanda'].', ';
				$nropedidocomanda=$_POST["txtNumeroComanda"];
    			
    			$res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
    			if(is_string($res)){
    				$objMovimiento->abortarTransaccion(); 
    				$objMovimientoAlmacen->abortarTransaccion(); 
    				$objBitacora->abortarTransaccion(); 
    				$objStockProducto->abortarTransaccion();
    	                        $objMesa->abortarTransaccion();
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes5: ".$objMovimientoAlmacen->gMsg;
    				exit();
    			}
                if(($v["precioventa"]+0)==0){//inserto cortesia
                    $objMovimientoAlmacen->ejecutarSQL("INSERT INTO movimientoproductocortesia(idproducto,idmovimiento) VALUES (".$v['idproducto'].",".$idpedido.");");
                }

    			$dato2=$res->fetchObject();
                /*if($v["idproducto"]==70 || $v["idproducto"]==71){//ENTRADAS
                    $rst1=$objMovimiento->obtenerDataSQL("select * from entradacolor where idcaja=".$_SESSION["R_IdCaja"]." and idproducto=".$v["idproducto"]." and estado='N' and situacion='A' and idsucursal=".$_SESSION["R_IdSucursal"]." and idapertura=(select idmovimiento from movimientohoy where idconceptopago=1 and idcaja=".$_SESSION["R_IdCaja"].")");
                    if($rst1->rowCount()>0){
                        $dato1=$rst1->fetchObject();
                        $objMovimiento->ejecutarSQL("update entradacolor set final=final+".$v["cantidad"]." where identradacolor=".$dato1->identradacolor);
                    }
                }*/
    			//INICIO BITACORA
    			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], $dato2->iddetallemovalmacen,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    			//FIN BITACORA
    			
                if($_SESSION["R_IdCaja"]=="4" || $_SESSION["R_IdCaja"]=="5"){//ESCENARIO
                    $idsucursal=2;
                }elseif($_SESSION["R_IdCaja"]=="6"){//2DO PISO
                    $idsucursal=3;
                }else{
                    $idsucursal=$_SESSION['R_IdSucursal'];
                }
    			if($v['kardex']=='S'){                    
    				$res=$objStockProducto->insertar($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    				//INICIO BITACORA
    				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    				//FIN BITACORA
    				if($res!='Guardado correctamente'){
    					$objMovimiento->abortarTransaccion(); 
    					$objBitacora->abortarTransaccion();
    					$objStockProducto->abortarTransaccion();
    					$objStockProducto->abortarTransaccion();
    		            $objMesa->abortarTransaccion();
    					if(ob_get_length()) ob_clean();
    					echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
    					exit();
    				}
    			}elseif($v['kardex']!='S' and $v['compuesto']=='S'){
    				$res=$objStockProducto->insertarcompuesto($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    				//INICIO BITACORA
    				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    				//FIN BITACORA
    				if($res!='Guardado correctamente'){
    					$objMovimiento->abortarTransaccion(); 
    					$objBitacora->abortarTransaccion();
    					$objStockProducto->abortarTransaccion();
    					$objMovimientoAlmacen->abortarTransaccion();
                        $objMesa->abortarTransaccion();
    					if(ob_get_length()) ob_clean();
    					echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
    					exit();
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
    	                        $objMesa->abortarTransaccion();
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes6: ".$objMovimiento->gMsg;
    				exit();
    			}
    			
    			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$idpedido);
    			$dax = $rt->fetchObject();
    		
    			$res = $objMovimientoAlmacen->actualizarMontoPagadoMovimiento($idpedido,$v['precioventa']*$v['cantidad']);
    			//INICIO BITACORA
    			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$idpedido.'; totalpagado=> De: '.$dax->totalpagado.' a: '.$v['precioventa']*$v['cantidad'].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], $idpedido,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    			//FIN BITACORA
    			if($res!='Guardado correctamente'){
    				$objMovimiento->abortarTransaccion(); 
    				$objMovimientoAlmacen->abortarTransaccion();
    				$objBitacora->abortarTransaccion(); 
    				$objStockProducto->abortarTransaccion();
    	                        $objMesa->abortarTransaccion();
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes7: ".$objMovimiento->gMsg;
    				exit();
    			}
    			$iniciaproceso4.="$$".date("Y-n-j H:i:s");            
    		}

            //$objMovimiento->ejecutarSQL("update detallestock set fecha='".date('Y-m-d H:i:s')."',idusuario=".$_SESSION["R_IdUsuario"].",situacion='C',idsucursalstock=".$idsucursal." where idventa=".$dato->idmovimiento." and idpedido=".$idpedido);
    			
    		$comandas=substr($comandas,0,strlen($comandas)-2);
    		$res=$objMovimientoAlmacen->actualizarComentarioMovimiento($dato->idmovimiento,$comandas);
    		if(is_string($res)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion(); 
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion();
                            $objMesa->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes8: ".$objMovimiento->gMsg;
    			exit();
    		}
    		$iniciaproceso5=date("Y-n-j H:i:s");		
    		$res=$objMovimientoAlmacen->cambiarSituacionPedido($dato->idmovimiento,'P');$iniciaproceso6=date("Y-n-j H:i:s");
            $objMovimiento->cambiarSituacionPedido($dato->idmovimiento,'P');		
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimieneto=>'.$dato->idmovimiento.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>P; nota: la situacion hace referencia a los pedidos que pertenecen al documento de venta', $_SESSION['R_IdSucursal'],$dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);$iniciaproceso7=date("Y-n-j H:i:s");		
    		//FIN BITACORA
    		if($res!='Guardado correctamente'){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion(); 
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion();
                $objMesa->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes8: ".$objMovimiento->gMsg;
    			exit();
    		}
    		
    		if($res=='Guardado correctamente' || $res==0 || !is_string($rst)){
    			$objMovimiento->finalizarTransaccion(); 
    			$objMovimientoAlmacen->finalizarTransaccion(); 
    			$objBitacora->finalizarTransaccion(); 
    			$objStockProducto->finalizarTransaccion();
                $objMesa->finalizarTransaccion();
                $objMovimiento->cambiarSituacionPedido($dato->idmovimiento,'P');
                $_SESSION["R_carroPedidoMozo2"]=$_SESSION["R_carroPedidoMozo"];
                if(ob_get_length()) ob_clean();
    		    echo "vmsg='Guardado correctamente';vidpedido='$idpedido';vidventa='$dato->idmovimiento';vcomanda='".$_POST["txtNumeroComanda"]."';";
                exit();
    		}
    	
            //Fin de venta rapida
		exit();
	case "CABIASITUACION" :
		if(ob_get_length()) ob_clean();
		echo umill($objMovimientoAlmacen->cambiarSituacionAntendido($_POST['txtId'],'A'));
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		exit();
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimientohoy","IdMovimiento",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		/*Lo pui�se para evitar el cambio de mesa
		//VERIFICO SI CAMBIO DE MESA
		if($dax->idmesa<>$_POST["Idmesa"]){
			//CAMBIO LA SITUACION DE LA MESA ANTERIOR A NORMAL
			$res = $objMesa->cambiarSituacion($dax->idmesa,$_SESSION['R_IdSucursal'],'N');
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$dax->idmesa.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>N', $_SESSION['R_IdSucursal'],$dax->idmesa ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				exit();
			}
			//CAMBIO LA SITUACION DE LA MESA NUEVA A OCUPADA
			if($_POST["Idmesa"]>1){
				$res = $objMesa->cambiarSituacion($_POST["Idmesa"],$_SESSION['R_IdSucursal'],'O');
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["Idmesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>O', $_SESSION['R_IdSucursal'],$_POST["Idmesa"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res==1){
					$objMovimientoAlmacen->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
					exit();
				}
			}
		}*/
		
		//$datosR=split('-',$_POST["cboIdResponsable"]);
        if($_POST["txtNombrePersona"]!="undefined") $dax->nombrespersona=$_POST["txtNombrePersona"];
		$res = $objMovimientoAlmacen->actualizarMovimiento($_POST["txtId"],0, 5, $_POST["txtNumeroComanda"], 11, '', 'LOCALTIMESTAMP', '', '', $_POST["Nropersonas"], $_POST["Idmesa"], 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $dax->idusuario, 'P', 0, $dax->idresponsable, NULL, NULL, $dax->comentario,'O',0,$dax->idsucursalusuario,0,$dax->idsucursalusuario,$dax->nombrespersona);

		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
        $regitro = 'idconceptopago=> De: 0 a: 0; idsucursal=> De: '. $dax->idsucursal.' a: '.$_SESSION['R_IdSucursal'].'; idtipomovimiento=> De: 5 a: 5; numero=> De: '. $dax->numero.' a: '.$_POST["txtNumeroComanda"].'; idtipodocumento=> De: 11 a: 11; formapago=> De: a: ; fecha=> De: '. $dax->fecha.' a: '.date("d/m/Y").'; fechaproximacancelacion=> De: a: ; fechaultimopago=> De: a: ; nropersonas=> De: '. $dax->nropersonas.' a: '.$_POST["Nropersonas"].'; idmesa=> De: '. $dax->idmesa.' a: '.$_POST["Idmesa"].'; moneda=> De: '. $dax->moneda.' a: S; inicial=> De: 0 a: 0; subtotal=> De: '. $dax->subtotal.' a: '.$_POST["txtTotal"].'; igv=> De: 0 a: 0; total=> De: '. $dax->total.' a: '.$_POST["txtTotal"].'; totalpagado=> De: 0 a: 0; idusuario=> De: '. $dax->idusuario.' a: '.$_SESSION['R_IdUsuario'].'; tipopersona=> De: P a: P; idpersona=> De: 0 a: 0; idresponsable=> De: '. $dax->idresponsable.' a: '.$_SESSION['R_IdPersona'];
        $regitro.='; idmovimientoref=> De: a: ; idsucursalref=> De: a: ; comentario=> De: '. $dax->comentario.' a: "-"; situacion=> De: O a: O; estado=> De: N a: N; idcaja=> De: 0 a: 0; idsucursalusuario=> De: '. $dax->idsucursalusuario.' a: '.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=> De: 0 a: 0; idsucursalresponsable=> De: '. $dax->idsucursalresponsable.' a: '.$_SESSION['R_IdSucursalUsuario'].'; nombrespersona=> De:'. $dax->nombrespersona.' a: "-"';
        $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', $regitro, $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORORA
		if($res==1){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}

		$res = $objMovimientoAlmacen->eliminarDetalleAlmacen($_POST["txtId"]);
		//eliminaar detalle almacen
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		if($res==1){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			exit();
		}

		if(!isset($_SESSION['R_carroPedidoMozo']) or $_SESSION['R_carroPedidoMozo']==''){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
			exit();
		}
        //Guardo los platos para la impresion de agregados
        $dataplato=$_SESSION['R_carroPedidoMozo'];
        //>>>Para agregar el total de nuevos platos
        $carrotemp='';$carro2='';
        foreach($_SESSION['R_carroPedidoMozo'] as $k){
            $band=false;
            if($k['estado']=='actual'){
                 foreach($_SESSION['R_carroPedidoMozo'] as $j){
                    if($k['idproducto']==$j['idproducto'] && $k['idsucursalproducto']==$j['idsucursalproducto'] && $j['estado']=='nuevo'){
                        $cantidad = $k['cantidad']+$j['cantidad'];
                        $j['cantidad']=$cantidad;
                        $_SESSION['R_carroPedidoMozo'][$j['idproducto'].'-'.$j['idsucursalproducto']]['estado']='agregado';
                        $carrotemp[$j['idproducto'].'-'.$j['idsucursalproducto']]=$j;
                        $band=true;$idtemp=$j['idproducto'];
                        $carro2[$idtemp]=$idtemp;
                    }
                }
            }
            if($band==false && $k['estado']!='agregado' && $carro2[$k["idproducto"]]!=$k["idproducto"]){
               $carrotemp[$k['idproducto'].'-'.$k['idsucursalproducto']]=$k;
            }
        }
        //<<<<<
        $_SESSION['R_carroPedidoMozo']=$carrotemp;
		foreach($_SESSION['R_carroPedidoMozo'] as $v){
            $v['comentario']=str_replace("'","",$v['comentario']);
			$res = $objMovimientoAlmacen->insertarDetalleAlmacen($_POST["txtId"],$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],'',$v["comentario"]);
			if(count($v["carroDetalle"])>0){
                foreach($v["carroDetalle"] as $x => $y){
                    //$objMovimientoAlmacen->insertarDetalleMovCategoria($_POST["txtId"],$_SESSION["R_IdSucursal"],$v["idproducto"],$y["iddetallecategoria"]);
                    $objMovimientoAlmacen->ejecutarSQL("INSERT INTO detallemovcategoria(idmovimiento,idsucursal,idproducto,iddetallecategoria,numeroplato) VALUES (".$_POST["txtId"].",".$_SESSION["R_IdSucursal"].",".$v["idproducto"].",".$y["iddetallecategoria"].",".$y["numeroplato"].");");
                }
            }
            //INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				exit();
			}
		}
		if($res==0){
			$objMovimientoAlmacen->finalizarTransaccion();
			$objBitacora->finalizarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
        $_SESSION['R_carroPedidoMozo']=$dataplato;
		exit();
    case "ACTUALIZAR2" :
		if(ob_get_length()) ob_clean();
		$objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimientohoy","IdMovimiento",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		/*Lo pui�se para evitar el cambio de mesa
		//VERIFICO SI CAMBIO DE MESA
		if($dax->idmesa<>$_POST["Idmesa"]){
			//CAMBIO LA SITUACION DE LA MESA ANTERIOR A NORMAL
			$res = $objMesa->cambiarSituacion($dax->idmesa,$_SESSION['R_IdSucursal'],'N');
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$dax->idmesa.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>N', $_SESSION['R_IdSucursal'],$dax->idmesa ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				exit();
			}
			//CAMBIO LA SITUACION DE LA MESA NUEVA A OCUPADA
			if($_POST["Idmesa"]>1){
				$res = $objMesa->cambiarSituacion($_POST["Idmesa"],$_SESSION['R_IdSucursal'],'O');
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["Idmesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>O', $_SESSION['R_IdSucursal'],$_POST["Idmesa"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res==1){
					$objMovimientoAlmacen->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
					exit();
				}
			}
		}*/
		
		//$datosR=split('-',$_POST["cboIdResponsable"]);
        if($_POST["txtNombrePersona"]!="UNDEFINED") $_POST["cliente"]=$_POST["txtNombrePersona"];
		$res = $objMovimientoAlmacen->actualizarMovimiento($_POST["txtId"],0, 5, $_POST["txtNumeroComanda"], 11, '', 'LOCALTIMESTAMP', '', '', $_POST["Nropersonas"], $_POST["Idmesa"], 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $dax->idusuario, 'P', 0, $dax->idresponsable, NULL, NULL, $_POST["comentario"],'O',0,$dax->idsucursalusuario,0,$dax->idsucursalusuario,$_POST["cliente"]);

		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
        $regitro = 'idconceptopago=> De: 0 a: 0; idsucursal=> De: '. $dax->idsucursal.' a: '.$_SESSION['R_IdSucursal'].'; idtipomovimiento=> De: 5 a: 5; numero=> De: '. $dax->numero.' a: '.$_POST["txtNumeroComanda"].'; idtipodocumento=> De: 11 a: 11; formapago=> De: a: ; fecha=> De: '. $dax->fecha.' a: '.date("d/m/Y").'; fechaproximacancelacion=> De: a: ; fechaultimopago=> De: a: ; nropersonas=> De: '. $dax->nropersonas.' a: '.$_POST["Nropersonas"].'; idmesa=> De: '. $dax->idmesa.' a: '.$_POST["Idmesa"].'; moneda=> De: '. $dax->moneda.' a: S; inicial=> De: 0 a: 0; subtotal=> De: '. $dax->subtotal.' a: '.$_POST["txtTotal"].'; igv=> De: 0 a: 0; total=> De: '. $dax->total.' a: '.$_POST["txtTotal"].'; totalpagado=> De: 0 a: 0; idusuario=> De: '. $dax->idusuario.' a: '.$_SESSION['R_IdUsuario'].'; tipopersona=> De: P a: P; idpersona=> De: 0 a: 0; idresponsable=> De: '. $dax->idresponsable.' a: '.$_SESSION['R_IdPersona'];
        $regitro.='; idmovimientoref=> De: a: ; idsucursalref=> De: a: ; comentario=> De: '. $dax->comentario.' a: "-"; situacion=> De: O a: O; estado=> De: N a: N; idcaja=> De: 0 a: 0; idsucursalusuario=> De: '. $dax->idsucursalusuario.' a: '.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=> De: 0 a: 0; idsucursalresponsable=> De: '. $dax->idsucursalresponsable.' a: '.$_SESSION['R_IdSucursalUsuario'].'; nombrespersona=> De:'. $dax->nombrespersona.' a: "-"';
        $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', $regitro, $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORORA
		if($res==1){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}

		$res = $objMovimientoAlmacen->eliminarDetalleAlmacen($_POST["txtId"]);
		//eliminaar detalle almacen
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		if($res==1){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			exit();
		}

		if(!isset($_SESSION['R_carroPedidoMozo']) or $_SESSION['R_carroPedidoMozo']==''){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
			exit();
		}
        //Guardo los platos para la impresion de agregados
        $dataplato=$_SESSION['R_carroPedidoMozo'];
        //>>>Para agregar el total de nuevos platos
        $carrotemp='';$carro2='';
        foreach($_SESSION['R_carroPedidoMozo'] as $k){
            $band=false;
            if($k['estado']=='actual'){
                 foreach($_SESSION['R_carroPedidoMozo'] as $j){
                    if($k['idproducto']==$j['idproducto'] && $k['idsucursalproducto']==$j['idsucursalproducto'] && $j['estado']=='nuevo'){
                        $cantidad = $k['cantidad']+$j['cantidad'];
                        $j['cantidad']=$cantidad;
                        $_SESSION['R_carroPedidoMozo'][$j['idproducto'].'-'.$j['idsucursalproducto']]['estado']='agregado';
                        $carrotemp[$j['idproducto'].'-'.$j['idsucursalproducto']]=$j;
                        $band=true;$idtemp=$j['idproducto'];
                        $carro2[$idtemp]=$idtemp;
                    }
                }
            }
            if($band==false && $k['estado']!='agregado' && $carro2[$k["idproducto"]]!=$k["idproducto"]){
               $carrotemp[$k['idproducto'].'-'.$k['idsucursalproducto']]=$k;
            }
        }
        //<<<<<
        $carrooriginal = $_SESSION['R_carroPedidoMozo'];
        $_SESSION['R_carroPedidoMozo']=$carrotemp;
        foreach($_SESSION['R_carroPedidoMozo'] as $key => $v){
            $detalleoriginal = $carrooriginal[$key."-actual"];
            if(!empty($detalleoriginal) && $detalleoriginal["iddetallemovalmacen"]!=$v["iddetallemovalmacen"]){
                if(strlen($detalleoriginal["comentario"])>0){
                    $v["comentario"] = $detalleoriginal["comentario"].",".$v["comentario"];
                }
                if(is_array($detalleoriginal["carroDetalle"])){
                    foreach($detalleoriginal["carroDetalle"] as $x => $y){
                        $v["carroDetalle"][$x] = $y;
                    }
                }
            }
            $v['comentario']=str_replace("'","",$v['comentario']);
            $res = $objMovimientoAlmacen->insertarDetalleAlmacen(
                    $_POST["txtId"],$v['idproducto'],$v['idunidad'],$v['cantidad'],
                    $v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],
                    '',$v["comentario"]);
            if(count($v["carroDetalle"])>0){
                foreach($v["carroDetalle"] as $x => $y){
                    //$objMovimientoAlmacen->insertarDetalleMovCategoria($_POST["txtId"],$_SESSION["R_IdSucursal"],$v["idproducto"],$y["iddetallecategoria"]);
                    $objMovimientoAlmacen->ejecutarSQL("INSERT INTO detallemovcategoria(idmovimiento,idsucursal,idproducto,iddetallecategoria,numeroplato) VALUES (".$_POST["txtId"].",".$_SESSION["R_IdSucursal"].",".$v["idproducto"].",".$y["iddetallecategoria"].",".$y["numeroplato"].");");
                }
            }
            //INICIO BITACORA
            $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            //FIN BITACORA
            if($res==1){
                $objMovimientoAlmacen->abortarTransaccion(); 
                $objBitacora->abortarTransaccion();
                if(ob_get_length()) ob_clean();
                echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
                exit();
            }
        }
		if($res==0){
			$objMovimientoAlmacen->finalizarTransaccion();
			$objBitacora->finalizarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
        $_SESSION['R_carroPedidoMozo']=$dataplato;
		exit();
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion(); 
		$res = $objMovimientoAlmacen->eliminarMovimiento($_POST['txtId']);
		//INICIO BITACORA
		//eliminar movimiento
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				exit();
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
			exit();
		}*/
		$res = $objMovimientoAlmacen->cambiarSituacionMesa($_POST["txtId"],'N');
		//INICIO BITACORA
		//cambia situacion de mesa
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["Idmesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>N', $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
			exit();
		}
		//if($res==0){
			$objMovimientoAlmacen->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion(); 
			if(ob_get_length()) ob_clean();
	           $_SESSION["R_carroPedidoMozo2"]=$_SESSION["R_carroPedidoMozo"];
			echo "Guardado correctamente";
		//}
		exit();
	case "CAMBIARMESA2":
		$idmesalocal = $_POST["idmesalocal"];
		$idmovimiento = $_POST["idmovimiento"];
		$idmesacambio = $_POST["idmesacambio"];
        if(trim($idmesalocal)!="" && trim($idmesacambio)!="" && trim($idmovimiento)!=""){
            $res = $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET idmesa = ".$idmesacambio." WHERE idmovimiento = ".$idmovimiento." AND idsucursal = ".$_SESSION['R_IdSucursal']);
            $res = $objMovimiento->ejecutarSQL("UPDATE mesaunida SET idmesa_padre = $idmesacambio WHERE idmesa_padre = $idmesalocal AND idsucursal = ".$_SESSION['R_IdSucursal']);
            $res = $objMovimientoAlmacen->cambiarSituacionMesa2($idmesalocal,'N');
            $res = $objMovimientoAlmacen->cambiarSituacionMesa2($idmesacambio,'O');
        }else{
            echo "Error";
        }
		exit();
	case "UNIRMESA":
		$idmesalocal = $_POST["idmesalocal"];
		$idmesaunida = $_POST["idmesaunida"];
		$res = $objMovimiento->ejecutarSQL("INSERT INTO mesaunida VALUES ($idmesaunida,$idmesalocal,".$_SESSION['R_IdSucursal'].")");
		$res = $objMovimientoAlmacen->cambiarSituacionMesa2($idmesaunida,'U');
		exit();
	case "JUNTARMESA":
		$idmesalocal = $_POST["idmesalocal"];
		$idmovimiento = $_POST["idmovimiento"];
		$idmesajunta = $_POST["idmesajunta"];
		$rs = $objMovimiento->buscarDetalleProductoxMesa($idmesajunta);	
	    while($reg=$rs->fetchObject()){	
			$idproducto=$reg->idproducto;
			$idsucursalproducto=$reg->idsucursal;
			$producto=$reg->producto;
			$codigo=$reg->codigo;
			$idunidad=$reg->idunidad;
			$unidad=$reg->unidad;
			$cantidad=$reg->cantidad;
			$precioventa=$reg->precioventa;
			$moneda=$reg->moneda;
	        $abreviatura=$reg->abreviatura;
			//$preciomanoobra=$reg->preciomanoobra;
			$preciocompra=$reg->preciocompra;
		    $idmovimiento=$reg->idmovimiento;	
			$carroPedido[($idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$codigo,'producto'=>$producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda,'abreviatura'=>$abreviatura,'estado'=>'actual');
		}
		$rs = $objMovimiento->buscarDetalleProductoxMesa($idmesalocal);	
	    while($reg=$rs->fetchObject()){	
			$idproducto=$reg->idproducto;
			$idsucursalproducto=$reg->idsucursal;
			$producto=$reg->producto;
			$codigo=$reg->codigo;
			$idunidad=$reg->idunidad;
			$unidad=$reg->unidad;
			$cantidad=$reg->cantidad;
			$precioventa=$reg->precioventa;
			$moneda=$reg->moneda;
	        $abreviatura=$reg->abreviatura;
			//$preciomanoobra=$reg->preciomanoobra;
			$preciocompra=$reg->preciocompra;
		    $idmovimiento=$reg->idmovimiento;	
			$carroPedido2[($idproducto.'-'.$idsucursalproducto)]=array('idproducto'=>($idproducto),'idsucursalproducto'=>($idsucursalproducto),'codigo'=>$codigo,'producto'=>$producto,'cantidad'=>$cantidad,'idunidad'=>$idunidad, 'unidad'=>$unidad, 'precioventa'=>$precioventa,'precioventaoriginal'=>$precioventa ,'preciomanoobra'=>$preciomanoobra, 'preciocompra'=>$preciocompra,'moneda'=>$moneda,'abreviatura'=>$abreviatura,'estado'=>'actual');
		}
		
		$carrolocal = $carroPedido2;
		foreach($carroPedido as $k => $v){
			if(isset($carroPedido2[$k])){
				$carroPedido2[$k]["cantidad"] = $carroPedido2[$k]["cantidad"] + $carroPedido[$k]["cantidad"];
			}else{
				$carroPedido2[$k] = $carroPedido[$k];
			}
		}
		//echo json_encode(["carrojuntar"=>$carroPedido,"carrolocal"=>$carrolocal,"carrofinal"=>$carroPedido2]);
		$res = $objMovimiento->obtenerDataSQL("SELECT idmovimiento FROM movimientohoy WHERE idmesa = ".$idmesajunta." AND situacion = 'O' AND estado<>'A' AND idsucursal = ".$_SESSION["R_IdSucursal"]." ORDER BY idmovimiento DESC LIMIT 1");
		$idmovimientomesajunta = $res->fetchObject()->idmovimiento;
		$res = $objMovimientoAlmacen->eliminarMovimiento($idmovimientomesajunta);
		$res = $objMovimientoAlmacen->cambiarSituacionMesa($idmovimientomesajunta,'N');
		$res = $objMovimientoAlmacen->iniciarTransaccion();
		$res = $objMovimientoAlmacen->eliminarDetalleAlmacen($idmovimiento);
		//eliminaar detalle almacen
		if($res==1){
			$objMovimientoAlmacen->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			exit();
		}
		$total = 0;
		foreach($carroPedido2 as $v){$total = $total + round($v['precioventa'] * $v['cantidad'],2);
			$res = $objMovimientoAlmacen->insertarDetalleAlmacen($idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
			if(count($v["carroDetalle"])>0){
                foreach($v["carroDetalle"] as $x => $y){
                    $objMovimientoAlmacen->insertarDetalleMovCategoria($idmovimiento,$_SESSION["R_IdSucursal"],$v["idproducto"],$y["iddetallecategoria"]);
                }
            }
            if($res!="Guardado correctamente"){
				$objMovimientoAlmacen->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				exit();
			}
		}
		if($res=="Guardado correctamente"){
			$objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET total = ".$total." WHERE idmovimiento = ".$idmovimiento." AND idsucursal = ".$_SESSION["R_IdSucursal"]);
			$objMovimientoAlmacen->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		exit();
    case "CAMBIARMESA" :
		if(ob_get_length()) ob_clean();
		$objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion(); 
		$res = $objMovimientoAlmacen->eliminarMovimiento($_POST['txtId'],"Se Elimino por Cambio de Mesa");
		//INICIO BITACORA
		//eliminar movimiento
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Eliminar Registro Por cambio de Mesa', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion(); 
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				exit();
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
			exit();
		}*/
		$res = $objMovimientoAlmacen->cambiarSituacionMesa($_POST["txtId"],'N');
		//INICIO BITACORA
		//cambia situacion de mesa
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["Idmesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>N', $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			exit();
		}
        //Obtengo los productos de la mesa eliminada
        $rs = $objMovimientoAlmacen->buscarDetalleProducto($_POST["txtId"]);
        //Obtengo el movimiento de la mesa que se movera los platos
        $rs2 = $objMovimientoAlmacen->buscarMovimientoMesa($_POST['idmesa']);
        $data = $rs2->fetchObject();
        //Obtengo los productos de la mesa a la que se desea mover
        $rs3 = $objMovimientoAlmacen->buscarDetalleProducto($data->idmovimiento);
        $rtemp = $rs3;
        $carro='';$c=0;
        //REVISAR!!!!
        while($dat=$rs->fetchObject()){
            $band=false;
            $i=0;
            echo "entro arriba->".$c;
            while($dat2=$rs3->fetchObject()){
                echo "Linea de bucle".$i."->";
                if($dat->idproducto==$dat2->idproducto){
                     $cantidad = $dat->cantidad+$dat2->cantidad;
                     $carro[($dat->idproducto)]=array('idproducto'=>$dat->idproducto,'idunidad'=>$dat->idunidad,'cantidad'=>$cantidad,'preciocompra'=>$dat->preciocompra,'precioventa'=>$dat->precioventa,'idsucursalproducto'=>$dat->idsucursalproducto);
                     $band=true;
                     echo $dat->idproducto."++".$i.$c."++";
                }else{
                     if($band==false && (!isset($carro[($dat->idproducto)]) || $carro[($dat->idproducto)]["idproducto"]=="")){
                            $carro[($dat->idproducto)]=array('idproducto'=>$dat->idproducto,'idunidad'=>$dat->idunidad,'cantidad'=>$dat->cantidad,'preciocompra'=>$dat->preciocompra,'precioventa'=>$dat->precioventa,'idsucursalproducto'=>$dat->idsucursalproducto);
                            echo $dat->idproducto."+".$i.$c."+";
                            if(!isset($carro[($dat2->idproducto)]) || $carro[($dat2->idproducto)]["idproducto"]==""){        
                                $carro[($dat2->idproducto)]=array('idproducto'=>$dat2->idproducto,'idunidad'=>$dat2->idunidad,'cantidad'=>$dat2->cantidad,'preciocompra'=>$dat2->preciocompra,'precioventa'=>$dat2->precioventa,'idsucursalproducto'=>$dat2->idsucursalproducto);
                                echo $dat2->idproducto."-".$i.$c."-";
                            }
                     }else{
                        if(!isset($carro[($dat2->idproducto)]) || $carro[($dat2->idproducto)]["idproducto"]==""){        
                            $carro[($dat2->idproducto)]=array('idproducto'=>$dat2->idproducto,'idunidad'=>$dat2->idunidad,'cantidad'=>$dat2->cantidad,'preciocompra'=>$dat2->preciocompra,'precioventa'=>$dat2->precioventa,'idsucursalproducto'=>$dat2->idsucursalproducto);
                            echo $dat2->idproducto."-".$i.$c."-";
                        }
                     }
                }$i++;
            }$rs3="";
            $dat2="";
            $rs3 = $objMovimientoAlmacen->buscarDetalleProducto($data->idmovimiento);
            /*$rs3=$rtemp;
            $rs3;
            reset($rs3);*/
            $c++;
        }
        
        //Elimino el detalle del movimiento de la mesa para poder agregar el total
        $rs4 = $objMovimientoAlmacen->eliminarDetalleAlmacen($data->idmovimiento);
        //Elimino el detalle de la mesa actual
        $rs5 = $objMovimientoAlmacen->eliminarDetalleAlmacen($_POST["txtId"]);
        
        foreach($carro as $k => $v){	
            $total = $total + $v['cantidad']*$v['precioventa'];
            $res = $objMovimientoAlmacen->insertarDetalleAlmacen($data->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro+Cambio Mesa', 'idmovimiento=>'.$data->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v['idsucursalproducto'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
				exit();
			}
        }
        $res = $objMovimientoAlmacen->actualizarMontoxCambioMesa($total,$data->idmovimiento);
        if($res==1){
				$objMovimientoAlmacen->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes4: ".$objMovimiento->gMsg;
				exit();
			}
			$objMovimientoAlmacen->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		
		exit();
	case "LIBERAR":
		$iniciaproceso2=date("Y-n-j H:i:s");
		$objMovimiento->iniciarTransaccion();
		$objMovimientoAlmacen->iniciarTransaccion();
		$objBitacora->iniciarTransaccion(); 
		$objStockProducto->iniciarTransaccion(); 
		   
		 $_POST["cboIdTipoDocumento"]=4;              
		 $_POST["optTipoPago"]="Efectivo";
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
        
		$_POST["txtNumero"]=$objMovimiento->generaNumero(2,4,date("Y"));
		$_POST["txtFecha"]=date("d-m-Y");
		$_POST["optMoneda"]="S";
		$_POST["txtComentario"]="Pago automatico desde plataforma mozo";
		$_POST["txtIdPersona"]="1";
		$_POST["txtIdSucursalPersona"]=$_SESSION['R_IdSucursal'];
		$_POST['cboIdCaja']=1;
		//Inserto Documento Venta; editado con el tipo de pago
		
		$rst = $objMovimientoAlmacen->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], NULL, NULL, $_POST["txtComentario"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
		if(is_string($rst)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "text='Error de Proceso en Lotes2: ".$objGeneral->gMsg."';";
			exit();
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
		$rst = $objMovimientoAlmacen->insertarMovimiento(3, 4, $numero, 9, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $dato->idmovimiento, $_SESSION['R_IdSucursal'], 'Documento Venta '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
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
			echo "text='Error de Proceso en Lotes3: ".$objGeneral->gMsg."';";
			exit();
		}
		if(!isset($_SESSION['R_carroPedidoMozo']) or $_SESSION['R_carroPedidoMozo']==''){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion();
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "text='Error de Proceso en Lotes: Las variables de sesión se perdieron';";
			exit();
		}
		//Inserto Detallde Documento Venta
		$iniciaproceso3=date("Y-n-j H:i:s");
		$cuenta=0;
		$comandas='Comanda Nro: ';
		$nropedidocomanda='';
		foreach($_SESSION['R_carroPedidoMozo'] as $v){
			if($_POST['txtPersona']=='VARIOS'){
				if($cuenta==0){
					$nombrespersona=$objMovimiento->consultarNombreClientePedido($_POST["txtId"]);
					//cambio nombre estatico del cliente en doc venta
					$res=$objMovimientoAlmacen->actualizarNombresClienteMovimiento($dato->idmovimiento,$nombrespersona);
					if(is_string($res)){
						$objMovimiento->abortarTransaccion(); 
						$objMovimientoAlmacen->abortarTransaccion(); 
						$objBitacora->abortarTransaccion(); 
						$objStockProducto->abortarTransaccion();
						if(ob_get_length()) ob_clean();
						echo "text='Error de Proceso en Lotes4.1: ".$objGeneral->gMsg."';";
						exit();
					}
					//cambio nombre estatico del cliente en mov caja
					$res=$objMovimientoAlmacen->actualizarNombresClienteMovimiento($datoc->idmovimiento,$nombrespersona);
					if(is_string($res)){
						$objMovimiento->abortarTransaccion(); 
						$objMovimientoAlmacen->abortarTransaccion(); 
						$objBitacora->abortarTransaccion(); 
						$objStockProducto->abortarTransaccion();
						if(ob_get_length()) ob_clean();
						echo "text='Error de Proceso en Lotes4.2: ".$objGeneral->gMsg."';";
						exit();
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
				echo "text='Error de Proceso en Lotes5: ".$objGeneral->gMsg."'";
				exit();
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
					$objMovimientoAlmacen->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "text='Error de Proceso en Lotes2: ".$objGeneral->gMsg."';";
					exit();
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
					$objMovimientoAlmacen->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "text='Error de Proceso en Lotes2: ".$objGeneral->gMsg."';";
					exit();
				}	
			}
			
			$res = $objMovimiento->insertarDetalleMovimiento($dato->idmovimiento,$_POST["txtId"],$dato2->iddetallemovalmacen);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 59, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idmovimientoref=>'.$v['idpedido'].'; iddetallemovimientoalmacen=>'.$dato2->iddetallemovalmacen.'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion();
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "text='Error de Proceso en Lotes6: ".$objGeneral->gMsg."';";
				exit();
			}
			
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$_POST["txtId"]);
			$dax = $rt->fetchObject();
		
			$res = $objMovimientoAlmacen->actualizarMontoPagadoMovimiento($_POST["txtId"],$v['precioventa']*$v['cantidad']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$v['idpedido'].'; totalpagado=> De: '.$dax->totalpagado.' a: '.$v['precioventa']*$v['cantidad'].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], $_POST["txtId"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objMovimientoAlmacen->abortarTransaccion();
				$objBitacora->abortarTransaccion(); 
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "text='Error de Proceso en Lotes7: ".$objGeneral->gMsg."';";
				exit();
			}
			$iniciaproceso4.="$$".date("Y-n-j H:i:s");
		}
			
		$comandas=substr($comandas,0,strlen($comandas)-2);
		$res=$objMovimientoAlmacen->actualizarComentarioMovimiento($dato->idmovimiento,$comandas);
		if(is_string($res)){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "text='Error de Proceso en Lotes8: ".$objGeneral->gMsg."'";
			exit();
		}
			$iniciaproceso5=date("Y-n-j H:i:s");		
		$res=$objMovimientoAlmacen->cambiarSituacionPedido($dato->idmovimiento,'P');$iniciaproceso6=date("Y-n-j H:i:s");		
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimieneto=>'.$dato->idmovimiento.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>P; nota: la situacion hace referencia a los pedidos que pertenecen al documento de venta', $_SESSION['R_IdSucursal'],$dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);$iniciaproceso7=date("Y-n-j H:i:s");		
		//FIN BITACORA
		if($res!='Guardado correctamente'){
			$objMovimiento->abortarTransaccion(); 
			$objMovimientoAlmacen->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "text='Error de Proceso en Lotes8: ".$objGeneral->gMsg."';";
			exit();
		}
		
		if($res=='Guardado correctamente'){
			$objMovimiento->finalizarTransaccion(); 
			$objMovimientoAlmacen->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion(); 
			$objStockProducto->finalizarTransaccion();
			$objMovimientoAlmacen->cambiarSituacionPedido($dato->idmovimiento,'P');
		}
		
		if($_POST["cboIdTipoDocumento"]==4){//boleta
			//echo "imprimir('".$dato->idmovimiento."');";
			echo "text='Guardado correctamente';";
		}else{
			if($_POST["cboIdTipoDocumento"]==5){//factura
				//echo "imprimir('".$dato->idmovimiento."');";
				echo "text='Guardado correctamente';";
			}else{//ticket
				echo "imprimir('".$dato->idmovimiento."');alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
			}
		}
        
		exit();	
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>