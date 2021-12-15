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
		//si la apertura es != 0 o vacio es por que ya hay apertura
		if($apertura==0){
    		$montosoles= $objCaja->montodeaperturasoles();
    		$num_mov=$objCaja->existenciamov();
    		if($num_mov==0){
    			$objMovimientoAlmacen->iniciarTransaccion();
    			$objBitacora->iniciarTransaccion();
    			$numero = $objCaja->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
    			$rst = $objMovimientoAlmacen->insertarMovimiento(1, 4, $numero, 9, 'A', date("d/m/Y H:i:s"), '', '',
                                    0, 0, 'S', 0, $montosoles, 0, $montosoles, 0, $_SESSION['R_IdUsuario'],
                                    'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Apertura automatica desde el modulo de ventas','N');
    			$dato=$rst->fetchObject();
    			//INICIO BITACORA
    			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$montosoles.'; igv=>0; total=>'.$montosoles.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Apertura automatica desde el modulo de ventas; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    			//FIN BITACORA
    			if(is_string($rst)){
    				$objMovimientoAlmacen->abortarTransaccion(); 
    				$objBitacora->abortarTransaccion(); 
    				if(ob_get_length()) ob_clean();
    				echo "Error de Proceso en Lotes11: ".$objGeneral->gMsg;
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
    				
				}else{
					$objMovimientoAlmacen->finalizarTransaccion(); 
					$objBitacora->finalizarTransaccion(); 
					if(ob_get_length()) ob_clean();
				}	
            }
  		}
		//VENTA
        $iniciaproceso2=date("Y-n-j H:i:s");

        //OBTNEER EL IDMESA
        $rs = $objMovimiento->obtenerDataSQL("SELECT idmesa FROM movimientohoy WHERE idmovimiento = ".$_POST["txtidmov"]);
        $idmesapadre = $rs->fetchObject()->idmesa;

        $objMovimiento->iniciarTransaccion();
        $objMovimientoAlmacen->iniciarTransaccion();
        $objBitacora->iniciarTransaccion(); 
        $objStockProducto->iniciarTransaccion();
        
        //ACTUALIZAR FECHA DE COBRO DE MESA 
        $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET fechafinal = now() WHERE idmovimiento = ".$_POST["txtidmov"]);

        //ACTUALIZO DIRECCION
        $objMovimientoAlmacen->ejecutarSQL("UPDATE persona set direccion='".$_POST["txtDireccion2"]."' where idpersona=".$_POST["txtIdPersona"]);
        
        $tipoVenta = $_POST["tipoVenta"];
        if(empty($tipoVenta)){
            $tipoVenta = $_POST["tipoVenta2"];
        }
        if($tipoVenta=="V"){
            $idvale = $_POST["idvale"];
        }elseif($tipoVenta=="D"){
            $idtrabajador = $_POST["idtrabajador"];
        }elseif($tipoVenta=="A"){
            $idpagoanticipado = $_POST["idpagoanticipado"];
        }elseif($tipoVenta=="C"){
            $_POST["txtComentario"] = "VENTA AL CREDITO";
            $_POST["txtSubcuenta"]="NO";
            $_POST["rdbtnModoPago"]="E";
            $totalpagado=0;
            //$_POST["txtNumero"] = "000-000000-0000";
            $plazo_credito = $_POST["plazo_credito"];
            $objMovimientoAlmacen->ejecutarSQL("INSERT INTO ventacredito (plazo,total,fecha_consumo,idusuario,idcliente,idmovimiento) VALUES (".$plazo_credito.",".$_POST["txtTotal"].",'".$_POST["txtFecha"]."',".$_SESSION['R_IdUsuario'].",".$_POST["txtIdPersona"].",".$_POST["txtidmov"].")");
            $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET situacion = 'P',tipoventa='".$tipoVenta."',idpersona = ".$_POST["txtIdPersona"]." WHERE idmovimiento = ".$_POST["txtidmov"]);
            $objMovimientoAlmacen->ejecutarSQL("UPDATE mesa SET situacion = 'N' WHERE idmesa=".$idmesapadre);
            /*$objMovimientoAlmacen->finalizarTransaccion();
            echo 'vidventa=0';
            exit();*/
        }elseif($tipoVenta=="T"){
            $idscortesia = explode(",", $_POST["idcortesia"]);
            if(is_array($idscortesia) && count($idscortesia)>0){
                foreach ($idscortesia as $idcortesia) {
                    $objMovimientoAlmacen->ejecutarSQL("INSERT INTO movimientoproductocortesia(idproducto,idmovimiento) VALUES (".$idcortesia.",".$_POST["txtidmov"].");");
                }
            }
            $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET situacion = 'P',comentario='".str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["glosa_movimiento"])."',tipoventa='".$tipoVenta."',idpersona = ".$_POST["txtIdPersona"]." WHERE idmovimiento = ".$_POST["txtidmov"]);
            if($_POST["modCortesia"]=="T"){
                $_POST["txtComentario"] = "VENTA CON CORTESIA TOTAL";
                $_POST["txtSubcuenta"]="NO";
                $_POST["rdbtnModoPago"]="E";
                $_POST["txtTotal"]=0;
                $totalpagado=$_POST["txtTotal"];
                //$_POST["txtNumero"] = "000-000000-0000";
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET situacion = 'P',comentario='".str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["comentario"])."',tipoventa='".$tipoVenta."',idpersona = ".$_POST["txtIdPersona"]." WHERE idmovimiento = ".$_POST["txtidmov"]);
                $objMovimientoAlmacen->ejecutarSQL("UPDATE mesa SET situacion = 'N' WHERE idmesa=".$idmesapadre);
                /*$objMovimientoAlmacen->finalizarTransaccion();
                echo 'vidventa=0';
                exit();*/
            }
        }
        
        if($_POST["idventacredito"]>0){
            $objMovimientoAlmacen->ejecutarSQL("UPDATE ventacredito SET estado = 'P',fecha_pago='".$_POST["txtFecha"]."' WHERE idventacredito = ".$_POST["idventacredito"]);
        }
        
        $glosa = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["glosa_movimiento"]);
		
        //Para division de cuenta
        if($_POST["txtSubcuenta"]=="NO"){
            
               
            if($_POST["cboIdTipoDocumento"]!=5){
                $_POST["txtSubtotal"]=$_POST["txtTotal"];
                $_POST["txtIgv"]=0;
            }else{
                if($_POST["chkIgv"]=="S"){
                    $_POST["txtSubtotal"]=$_POST["txtTotal"];
                    $_POST["txtIgv"]=0;
                }
            }
            if($_POST["rdbtnModoPago"]=="E" && $tipoVenta!="C"){
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
                $totalpagado=$_POST["txtPagoEfectivo"];
            }elseif($_POST["rdbtnModoPago"]=="C"){
                $idbanco="";
                $idtipotarjeta="";
                $numerotarjeta="";
                $banco_cheque=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["banco_cheque"]);
                $numero_cheque=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["numero_cheque"]);
                $moneda_cheque=$_POST["moneda_cheque"];
                $totalpagado="0.00";
            }elseif($_POST["rdbtnModoPago"]=="D"){
                $idbanco="";
                $idtipotarjeta="";
                $numerotarjeta="";
                $banco_deposito=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["banco_deposito"]);
                $numero_deposito=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["numero_deposito"]);
                $importe_deposito=$_POST["importe_deposito"];
                $fecha_deposito=$_POST["fecha_deposito"];
                $totalpagado="0.00";
            }
            
            if($tipoVenta=="D"){
                $_POST["txtIdPersona"] = $idtrabajador;
            }
            
            if($tipoVenta=="C"){
                $totalpagado="0.00";
            }
            
            if(($_POST["idventacredito"]+0)==0){
        		//Inserto Documento Venta; editado con el tipo de pago
        		$rst = $objMovimientoAlmacen->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A',
                                date("d/m/Y H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"],
                                $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"],
                                $_SESSION['R_IdPersona'], NULL, NULL, $_POST["txtComentario"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],
                                $_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
        		if($rst->rowCount()==0){
        			$objMovimiento->abortarTransaccion(); 
        			$objMovimientoAlmacen->abortarTransaccion();
        			$objBitacora->abortarTransaccion(); 
        			$objStockProducto->abortarTransaccion(); 
        			if(ob_get_length()) ob_clean();
        			echo "Error de Proceso en Lotes2: ".$objMovimientoAlmacen->gMsg;
        			exit();
        		}
        		$dato=$rst->fetchObject();
                $idmov=$dato->idmovimiento;
                if($_POST["txtFE"]=="S") $objMovimientoAlmacen->ejecutarSQL("update movimientohoy set manual='N' where idmovimiento=".$idmov);
                $idpersona=$_POST["txtIdPersona"];
            }else{
                $idmov=$_POST["txtidmov"];
                $idmov=$objMovimiento->obtenerDataSQL("select idmovimiento from detallemovimientohoy where idmovimientoref=".$idmov." union all select idmovimiento from detallemovimiento where idmovimientoref=".$idmov)->fetchObject()->idmovimiento;
                $idpersona=$objMovimiento->obtenerDataSQL("select * from ventacredito where idventacredito=".$_POST["idventacredito"])->fetchObject()->idcliente;
            }
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>2; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>'.$_POST["optMoneda"].'; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].
            '; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUdato->idmovimientosuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta =>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $idmov ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
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
            if(($_POST["idventacredito"]+0)==0){
                $idconcepto=3;
                $com = 'Documento Venta '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"].' - '.$_POST["glosa_movimiento"];
            }else{
                $idconcepto=31;
                $com="-";
            }
    		$rst = $objMovimientoAlmacen->insertarMovimiento($idconcepto, 4, $numero, 9, 'A', date("d/m/Y H:i:s"), '', '', 0, 0, 
                        $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotal"], $totalpagado, $_SESSION['R_IdUsuario'],
                        'P', $idpersona, $_SESSION['R_IdPersona'], $idmov, $_SESSION['R_IdSucursal'],
                        $com,'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],
                        $_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
            $datoc=$rst->fetchObject();
                
            if($_POST["rdbtnModoPago"]=="C"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."'"
                        . ", nombrebanco='".$banco_cheque."', numerocheque='".$numero_cheque."', monedacheque='".$moneda_cheque."' WHERE idmovimiento=".$datoc->idmovimiento);
            }elseif($_POST["rdbtnModoPago"]=="D"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."'"
                        . ", nombrebanco='".$banco_deposito."', numerooperacion='".$numero_deposito."', importedeposito=".$importe_deposito.",fechadeposito='".$fecha_deposito."' WHERE idmovimiento=".$datoc->idmovimiento);
            }elseif($_POST["rdbtnModoPago"]=="A"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET montotarjeta='1@".$_POST["txtMontoVisa"]."|2@".$_POST["txtMontoMastercard"]."',modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."' WHERE idmovimiento=".$datoc->idmovimiento);
            }else{
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."' WHERE idmovimiento=".$datoc->idmovimiento);
            }
            
            if($tipoVenta=="V"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE vale SET estado = 'C', idmovimiento = ".$datoc->idmovimiento.", fecha_consumo = '".$_POST["txtFecha"]."' WHERE idvale = ".$idvale);
            }elseif($tipoVenta=="A"){
                //$objMovimientoAlmacen->ejecutarSQL("UPDATE pagoanticipado SET estado = 'C', idmovimiento = ".$datoc->idmovimiento.", fecha_consumo = '".$_POST["txtFecha"]."' WHERE idpagoanticipado = ".$idpagoanticipado);
                $ped = $objMovimientoAlmacen->obtenerDataSQL("select * from movimientohoy where idmovimiento=".$_POST["txtidmov"])->fetchObject();
                $objMovimientoAlmacen->ejecutarSQL("insert into detallepagoanticipado(idpagoanticipado,idmovimiento,fechaconsumo,monto) values(".$idpagoanticipado.",".$datoc->idmovimiento.",'".$_POST["txtFecha"]."',".$ped->total.")");
                $objMovimientoAlmacen->ejecutarSQL("UPDATE pagoanticipado SET saldo=saldo-".$ped->total.",idmovimiento = ".$datoc->idmovimiento.", fecha_consumo = '".$_POST["txtFecha"]."' WHERE idpagoanticipado = ".$idpagoanticipado);
            }elseif($tipoVenta=="C"){
                //$objMovimientoAlmacen->ejecutarSQL("INSERT INTO ventacredito (plazo,total,fecha_consumo,idusuario,idcliente,idmovimiento) VALUES (".$plazo_credito.",".$_POST["txtTotal"].",'".$_POST["txtFecha"]."',".$_SESSION['R_IdUsuario'].",".$_POST["txtIdPersona"].",".$datoc->idmovimiento.")");
            }
                
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$idconcepto.'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].' '.date("H:i:s").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>'.$_POST["optMoneda"].'; inicial=>0; subtotal=>'.$_POST["txtSubtotal"].'; igv=>'.$_POST["txtIgv"].'; total=>'.$_POST["txtTotal"].'; totalpagado=>'.$totalpagado.'; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>'.$dato->idmovimiento.'; idsucursalref=>'.$_SESSION['R_IdSucursal'].'; comentario=>Documento Venta Nro: '.$_POST["txtNumero"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; idbanco =>'.$idbanco.'; idtipotarjeta =>'.$idtipotarjeta.'; numerotarjeta=>'.$numerotarjeta, $_SESSION['R_IdSucursal'], $datoc->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    		//FIN BITACORA
    		if(is_string($rst)){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion();
    			$objBitacora->abortarTransaccion();
    			$objStockProducto->abortarTransaccion(); 
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
    			exit();
    		}
    		if(!isset($_SESSION['R_carroVenta']) or $_SESSION['R_carroVenta']==''){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion();
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
    			exit();
    		}
            if(($_POST["idventacredito"]+0)==0){
        		//Inserto Detallde Documento Venta
        		$iniciaproceso3=date("Y-n-j H:i:s");
        		$cuenta=0;
        		$comandas='Estado de Cuenta Nro: ';
        		$nropedidocomanda='';
        		foreach($_SESSION['R_carroVenta'] as $v){
        			//concateno los numeros de pedido que van en el comentario
        			if($v['nropedido']!=$nropedidocomanda){
        				$comandas.=$v['nropedido'].', ';
        				$nropedidocomanda=$v['nropedido'];
        			}
                    if($tipoVenta=="D" && $v['bar']=="N"){
                        $v['precioventa'] = round($v['precioventa']/2, 2);
                    }elseif($tipoVenta=="T" && in_array($v["idproducto"], $idscortesia)){
                        $v['precioventa'] = 0;
                    }
                    if($v['precioventa']==''){ $v['precioventa']=0;}
                    if($v['preciocompra']==''){ $v['preciocompra']=0;}
                    if($_POST["txtDescuento".$v["idproducto"]]!=""){
                        $v["precioventa"]=round($v["precioventa"]*(1-$_POST["txtDescuento".$v["idproducto"]]/100),2);
                    }
        			$res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],$_POST['txtProducto'.$v['idproducto']]);
        			if(is_string($res)){
        				$objMovimiento->abortarTransaccion(); 
        				$objMovimientoAlmacen->abortarTransaccion(); 
        				$objBitacora->abortarTransaccion(); 
        				$objStockProducto->abortarTransaccion();
        				if(ob_get_length()) ob_clean();
        				echo "Error de Proceso en Lotes5: ".$objGeneral->gMsg;
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
        					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
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
        					echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
        					exit();
        				}	
        			}
    			
        			$res = $objMovimiento->insertarDetalleMovimiento($dato->idmovimiento,$v['idpedido'],$dato2->iddetallemovalmacen);
                    //$objMovimiento->ejecutarSQL("update detallemovalmacen set comentario='".$_POST["txtProducto".$v["idproducto"]]."' where iddetallemovalmacen=".$dato2->iddetallemovalmacen." and idproducto=".$v["idproducto"]);
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
        				exit();
        			}
        			
        			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$v['idpedido']);
        			$dax = $rt->fetchObject();
        		
        			$res = $objMovimientoAlmacen->actualizarMontoPagadoMovimiento($v['idpedido'],$v['precioventa']*$v['cantidad']);
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
        			echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
        			exit();
        		}
            }
                
            //if($tipoVenta=="D" || $tipoVenta=="T"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET situacion = 'P' WHERE idmovimiento = ".$_POST["txtidmov"]);
            //}
			$iniciaproceso5=date("Y-n-j H:i:s");		
    		$res=$objMovimientoAlmacen->cambiarSituacionPedido($idmov,'P');$iniciaproceso6=date("Y-n-j H:i:s");		
    		//INICIO BITACORA
    		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimieneto=>'.$idmov.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>P; nota: la situacion hace referencia a los pedidos que pertenecen al documento de venta', $_SESSION['R_IdSucursal'],$idmov ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);$iniciaproceso7=date("Y-n-j H:i:s");		
    		//FIN BITACORA
                    
    		if($res!='Guardado correctamente'){
    			$objMovimiento->abortarTransaccion(); 
    			$objMovimientoAlmacen->abortarTransaccion(); 
    			$objBitacora->abortarTransaccion(); 
    			$objStockProducto->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
    			exit();
    		}
    		
    		if($res=='Guardado correctamente'){
    			$objMovimiento->finalizarTransaccion(); 
    			$objMovimientoAlmacen->finalizarTransaccion(); 
    			$objBitacora->finalizarTransaccion(); 
    			$objStockProducto->finalizarTransaccion();
    			$objMovimientoAlmacen->cambiarSituacionPedido($idmov,'P');
    		}
		
    		if($_POST["cboIdTipoDocumento"]==4){//boleta
    			//echo "imprimir('".$dato->idmovimiento."');setRun('vista/frmComprobanteB','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
                //echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
                if($_POST["txtNumero"] != "000-000000-0000"){
                    echo "vidventa='".$idmov."';";
                }else{
                    echo "vidventa='0';";
                }
    		}else{
    			if($_POST["cboIdTipoDocumento"]==5){//factura
    			//	echo "imprimir('".$dato->idmovimiento."');setRun('vista/frmComprobanteF','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
                    //echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
                    if($_POST["txtNumero"] != "000-000000-0000"){
                        echo "vidventa='".$idmov."';";
                    }else{
                        echo "vidventa='0';";
                    }
    			}else{//ticket
                    //echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
                    echo "vidventa='".$idmov."';";
    				//echo "alert('Guardado Correctamente.".$iniciaproceso."$$".$iniciaproceso2."$$".$iniciaproceso3."$$$".$iniciaproceso4."$$".$iniciaproceso5."$$".$iniciaproceso6."$$".$iniciaproceso7."$$".date("Y-n-j H:i:s")."');document.getElementById('cargamant').innerHTML=''";
    				//echo "imprimir('".$dato->idmovimiento."');alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
    			}
    		}
                
        //FIN 
        }elseif($_POST["txtSubcuenta"]=="SI"){
            
            $modalidadDivision = $_POST["modDivision"];
            
            $_POST["IdPedido"] = $_POST["txtidmov"];
            
            if($_POST["cboIdTipoDocumento"]!=5){
                $_POST["txtSubtotal"]=$_POST["txtTotal"];
                $_POST["txtIgv"]=0;
            }else{
                if($_POST["chkIgv"]=="S"){
                    $_POST["txtSubtotal"]=$_POST["txtTotal"];
                    $_POST["txtIgv"]=0;
                }
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
                $totalpagado=$_POST["txtPagoEfectivo"];
            }elseif($_POST["rdbtnModoPago"]=="C"){
                $idbanco="";
                $idtipotarjeta="";
                $numerotarjeta="";
                $banco_cheque=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["banco_cheque"]);
                $numero_cheque=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["numero_cheque"]);
                $moneda_cheque=$_POST["moneda_cheque"];
                $totalpagado="0.00";
            }elseif($_POST["rdbtnModoPago"]=="D"){
                $idbanco="";
                $idtipotarjeta="";
                $numerotarjeta="";
                $banco_deposito=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["banco_deposito"]);
                $numero_deposito=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["numero_deposito"]);
                $importe_deposito=$_POST["importe_deposito"];
                $fecha_deposito=$_POST["fecha_deposito"];
                $totalpagado="0.00";
            }
            
            if($tipoVenta=="D"){
                $_POST["txtIdPersona"] = $idtrabajador;
            }
            
            if($tipoVenta=="C"){
                $totalpagado="0.00";
            }
            
            $_POST["txtTotalSubcuenta"]=$_POST["txtTotal"];
            //PARA DIVISION DE CUENTA

            //->PRIMERO CREO UN NUEVO PEDIDO CON LOS PRODUCTOS QUE SE VAN A PAGAR
            if(ob_get_length()) ob_clean();
            $objMovimientoAlmacen->iniciarTransaccion();
            $objBitacora->iniciarTransaccion();
            $idsucursalref=NULL;$idmovimientoref=NULL;
            $temp = $objMovimientoAlmacen->consultarMovimiento(20,1,1,1,$_POST["IdPedido"],0);
            $datatemp = $temp->fetchObject();

            $_POST["txtNumeroComanda"] = $objMovimientoAlmacen->generaNumeroxMesero($datatemp->idresponsable,$_SESSION['R_IdSucursalUsuario']);		
            $_POST["txtNumeroComanda"] = str_pad(trim($_POST["txtNumeroComanda"]),6,"0",STR_PAD_LEFT);
            $res = $objMovimientoAlmacen->insertarMovimiento(0, 5, $_POST["txtNumeroComanda"], 11, $_POST['rdbtnModoPago'],
                    'LOCALTIMESTAMP', '', '', 4, 1, 'S', 0, $_POST["txtSubtotal"], $_POST['txtIgv'], $_POST["txtTotal"],
                    $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $idmovimientoref, $idsucursalref,
                    'Pedido por division de cuenta con referencia a la mesa '.$datatemp->mesa ,'O',0,
                    $_SESSION['R_IdSucursalUsuario'],0,$_SESSION['R_IdSucursalUsuario']);
            $dato=$res->fetchObject();
            $idpedido = $dato->idmovimiento;
            //INICIO BITACORA
            date_default_timezone_set('America/Lima');
            $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>5; numero=>'.$_POST["txtNumeroComanda"].'; idtipodocumento=>11; formapago=>; fecha=>'.date("d/m/Y").'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>4; idmesa=>1; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotalSubcuenta"].'; igv=>0; total=>'.$_POST["txtTotalSubcuenta"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>0; idresponsable=>'.$_SESSION['R_IdPersona'].'; idmovimientoref=>; idsucursalref=>; comentario=>'.'Pedido por division de cuenta con referencia a la mesa '.$datatemp->mesa.'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'].'; nombrespersona=>'." ", $_SESSION['R_IdSucursal'], $idpedido ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            //FIN BITACORA
            if(is_string($res)){
                    $objMovimientoAlmacen->abortarTransaccion(); 
                    $objBitacora->abortarTransaccion();
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes1: ".$objMovimientoAlmacen->gMsg;
                    exit();
            }

            if(!isset($_SESSION['R_carroVenta']) or $_SESSION['R_carroVenta']==''){
                    $objMovimientoAlmacen->abortarTransaccion(); 
                    $objBitacora->abortarTransaccion(); 
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes: Las variables de sesi�n se perdieron";
                    exit();
            }		

            if($modalidadDivision=="P"){
                $idsProductos = $_POST["inptHidenId"];
                $cantsProductos = $_POST["inptHidenCant"];
                $cantidadesIndexadas = array();
                $totalventa = 0;
                foreach ($idsProductos as $key=>$idprod) {
                    
                    if($tipoVenta=="D" && $v['bar']=="N"){
                        $v['precioventa'] = round($v['precioventa']/2, 2);
                    }elseif($tipoVenta=="T" && in_array($v["idproducto"], $idscortesia)){
                        $v['precioventa'] = 0;
                    }

                    $cantidad = $cantsProductos[$key];
                    $cantidadesIndexadas[$idprod]=$cantidad;
                    $v = $_SESSION['R_carroVenta'][$idprod];
                    $totalventa = $totalventa + $cantidad*$v['precioventa'];
                    $res = $objMovimientoAlmacen->insertarDetalleAlmacen($idpedido,$v['idproducto'],
                            $v['idunidad'],$cantidad,$v['preciocompra'],
                            $v['precioventa'],$v['idsucursalproducto']);
                    //INICIO BITACORA
                    $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$idpedido.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST['txt'.$v['idproducto']].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                    //FIN BITACORA
                    if($res==1){
                          $objMovimientoAlmacen->abortarTransaccion(); 
                          $objBitacora->abortarTransaccion();
                          if(ob_get_length()) ob_clean();
                          echo "Error de Proceso en Lotes2: ".$objMovimientoAlmacen->gMsg;
                          exit();
                    }
                }
            }else{
                $monto_restante= $_POST["inptHidenMontoRestante"];
                $monto_pago = $_POST["inptHidenMontoDivision"];
                $cantidad = 1;
                $idprod = 1000000000;
                $res = $objMovimientoAlmacen->insertarDetalleAlmacen($idpedido,$idprod,
                        1,$cantidad,$monto_pago,$monto_pago,$_SESSION['R_IdSucursal'],$_SESSION['R_IdSucursal'],
                        "DETALLE FICTICIO PARA DIVIDIR CUENTA POR MONTOS");
                //INICIO BITACORA
                $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro',
                        'idmovimiento=>'.$idpedido.'; idproducto=>'.$idprod.'; idunidad=>1'.
                            '; cantidad=>1; preciocompra=>'.$monto_pago.'; precioventa=>'.
                            $monto_pago.'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.
                            $_SESSION['R_IdSucursal'],
                        $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                //FIN BITACORA
                if($res==1){
                      $objMovimientoAlmacen->abortarTransaccion(); 
                      $objBitacora->abortarTransaccion();
                      if(ob_get_length()) ob_clean();
                      echo "Error de Proceso en Lotes2: ".$objMovimientoAlmacen->gMsg;
                      exit();
                }
            }

            //->

            //-->NUEVO DOCUMENTO DE VENTA DEL PEDIDO CREADO 
         
		/*if($_POST["cboIdTipoDocumento"]!=5){
			$_POST["txtSubtotal"]=$_POST["txtTotalSubcuenta"];
			$_POST["txtIgv"]=0;
		}
            $idbanco="";
            $idtipotarjeta="";
            $numerotarjeta="";
            $totalpagado=$_POST["txtTotalSubcuenta"];*/
                
            //Inserto Documento Venta; editado con el tipo de pago
            $rst = $objMovimientoAlmacen->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A',
                    $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"],
                    $_POST["txtTotalSubcuenta"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'],
                    NULL, NULL, $_POST["txtComentario"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],
                    $_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
            if(is_string($rst)){
                    $objMovimiento->abortarTransaccion(); 
                    $objMovimientoAlmacen->abortarTransaccion();
                    $objBitacora->abortarTransaccion(); 
                    $objStockProducto->abortarTransaccion(); 
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                    exit();
            }
            $dato=$rst->fetchObject();
            if($_POST["txtFE"]=="S") $objMovimientoAlmacen->ejecutarSQL("update movimientohoy set manual='N' where idmovimiento=".$dato->idmovimiento);

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
            $rst = $objMovimientoAlmacen->insertarMovimiento(3, 4, $numero, 9, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0,
                    $_POST["optMoneda"], 0, $_POST["txtSubtotal"], $_POST["txtIgv"], $_POST["txtTotalSubcuenta"], $totalpagado, 
                    $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdPersona'], $dato->idmovimiento, $_SESSION['R_IdSucursal'],
                    'Documento Venta '.$tipodocabreviatura.' Nro: '.$_POST["txtNumero"],'N',$_POST['cboIdCaja'],$_SESSION['R_IdSucursalUsuario'],
                    $_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',$idbanco,$idtipotarjeta,$numerotarjeta);
            $datoc=$rst->fetchObject();
            
            if($_POST["rdbtnModoPago"]=="C"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."'"
                        . ", nombrebanco='".$banco_cheque."', numerocheque='".$numero_cheque."', monedacheque='".$moneda_cheque."' WHERE idmovimiento=".$datoc->idmovimiento);
            }elseif($_POST["rdbtnModoPago"]=="D"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."'"
                        . ", nombrebanco='".$banco_deposito."', numerooperacion='".$numero_deposito."', importedeposito=".$importe_deposito.",fechadeposito='".$fecha_deposito."' WHERE idmovimiento=".$datoc->idmovimiento);
            }elseif($_POST["rdbtnModoPago"]=="A"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET montotarjeta='1@".$_POST["txtMontoVisa"]."|2@".$_POST["txtMontoMastercard"]."',modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."' WHERE idmovimiento=".$datoc->idmovimiento);
            }else{
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["rdbtnModoPago"]."',tipoventa='".$tipoVenta."',glosa='".$glosa."' WHERE idmovimiento=".$datoc->idmovimiento);
            }
            
            if($tipoVenta=="V"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE vale SET estado = 'C', idmovimiento = ".$datoc->idmovimiento.", fecha_consumo = '".$_POST["txtFecha"]."' WHERE idvale = ".$idvale);
            }elseif($tipoVenta=="A"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE pagoanticipado SET estado = 'C', idmovimiento = ".$datoc->idmovimiento.", fecha_consumo = '".$_POST["txtFecha"]."' WHERE idpagoanticipado = ".$idpagoanticipado);
            }elseif($tipoVenta=="C"){
                //$objMovimientoAlmacen->ejecutarSQL("INSERT INTO ventacredito (plazo,total,fecha_consumo,idusuario,idcliente,idmovimiento) VALUES (".$plazo_credito.",".$_POST["txtTotal"].",'".$_POST["txtFecha"]."',".$_SESSION['R_IdUsuario'].",".$_POST["txtIdPersona"].",".$datoc->idmovimiento.")");
            }

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
                    exit();
            }
            if(!isset($_SESSION['R_carroVenta']) or $_SESSION['R_carroVenta']==''){
                    $objMovimiento->abortarTransaccion(); 
                    $objMovimientoAlmacen->abortarTransaccion();
                    $objBitacora->abortarTransaccion(); 
                    $objStockProducto->abortarTransaccion();
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
                    exit();
            }
            //Inserto Detallde Documento Venta
            $iniciaproceso3=date("Y-n-j H:i:s");
            $cuenta=0;
            $comandas='Comanda Nro: ';
            $nropedidocomanda='';
            $carroventa = $_SESSION['R_carroVenta'];
            //print json_encode(array($cantidadesIndexadas,$carroventa));
            //die();
            foreach($_SESSION['R_carroVenta'] as $v){
                $idpedidooriginal = $v["idpedido"];
                if(($cantidadesIndexadas[$v['iddetalle']])>0){
            
                    //concateno los numeros de pedido que van en el comentario
                    if($v['nropedido']!=$nropedidocomanda){
                        $comandas.=$v['nropedido'].', ';
                        $nropedidocomanda=$v['nropedido'];
                    }
                    $res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($dato->idmovimiento,$v['idproducto'],$v['idunidad'],
                            $cantidadesIndexadas[$v['iddetalle']],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],$_POST['txtProducto'.$v['idproducto']]);
                    if(is_string($res)){
                        $objMovimiento->abortarTransaccion(); 
                        $objMovimientoAlmacen->abortarTransaccion(); 
                        $objBitacora->abortarTransaccion(); 
                        $objStockProducto->abortarTransaccion();
                        if(ob_get_length()) ob_clean();
                        echo "Error de Proceso en Lotes5: ".$objGeneral->gMsg;
                        exit();
                    }
                    $dato2=$res->fetchObject();
                    //INICIO BITACORA
                    $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST["txt".$v["idproducto"]].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], $dato2->iddetallemovalmacen,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                    //FIN BITACORA

                    if($v['kardex']=='S'){
                        $res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$cantidadesIndexadas[$v['iddetalle']],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        //INICIO BITACORA
                        $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST["txt".$v["idproducto"]].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        //FIN BITACORA
                        if($res!='Guardado correctamente'){
                            $objMovimiento->abortarTransaccion(); 
                            $objBitacora->abortarTransaccion();
                            $objStockProducto->abortarTransaccion();
                            $objMovimientoAlmacen->abortarTransaccion();
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                            exit();
                        }
                    }elseif($v['kardex']!='S' and $v['compuesto']=='S'){
                        $res=$objStockProducto->insertarcompuesto($_SESSION['R_IdSucursal'],$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$cantidadesIndexadas[$v['iddetalle']],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        //INICIO BITACORA
                        $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST["txt".$v["idproducto"]].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        //FIN BITACORA
                        if($res!='Guardado correctamente'){
                            $objMovimiento->abortarTransaccion(); 
                            $objBitacora->abortarTransaccion();
                            $objStockProducto->abortarTransaccion();
                            $objMovimientoAlmacen->abortarTransaccion();
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
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
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes6: ".$objGeneral->gMsg;
                            exit();
                    }

                    $rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$idpedido);
                    $dax = $rt->fetchObject();

                    $res = $objMovimientoAlmacen->actualizarMontoPagadoMovimiento($idpedido,$v['precioventa']*$cantidadesIndexadas[$v['iddetalle']]);
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
                            exit();
                    }
                    $iniciaproceso4.="$$".date("Y-n-j H:i:s");
                }
            }
            
            $_SESSION['R_carroVenta']=$carroventa ;

            $comandas=substr($comandas,0,strlen($comandas)-2);
            $res=$objMovimientoAlmacen->actualizarComentarioMovimiento($dato->idmovimiento,$comandas);
            if(is_string($res)){
                $objMovimiento->abortarTransaccion(); 
                $objMovimientoAlmacen->abortarTransaccion(); 
                $objBitacora->abortarTransaccion(); 
                $objStockProducto->abortarTransaccion();
                if(ob_get_length()) ob_clean();
                echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
                exit();
            }
            $iniciaproceso5=date("Y-n-j H:i:s");		
            $res=$objMovimiento->cambiarSituacionPedido($dato->idmovimiento,'P');$iniciaproceso6=date("Y-n-j H:i:s");		
            //INICIO BITACORA
            $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimieneto=>'.$dato->idmovimiento.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>P; nota: la situacion hace referencia a los pedidos que pertenecen al documento de venta', $_SESSION['R_IdSucursal'],$idmov ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);$iniciaproceso7=date("Y-n-j H:i:s");		
            //FIN BITACORA
            if($res!='Guardado correctamente'){
                $objMovimiento->abortarTransaccion(); 
                $objMovimientoAlmacen->abortarTransaccion(); 
                $objBitacora->abortarTransaccion(); 
                $objStockProducto->abortarTransaccion();
                if(ob_get_length()) ob_clean();
                echo "Error de Proceso en Lotes8: ".$objGeneral->gMsg;
                exit();
            }

            //if($tipoVenta=="D" || $tipoVenta=="T"){
                $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET situacion = 'P' WHERE idmovimiento = ".$idpedido);
            //}
            
            if($res=='Guardado correctamente'){
                $objMovimiento->finalizarTransaccion(); 
                $objMovimientoAlmacen->finalizarTransaccion(); 
                $objBitacora->finalizarTransaccion(); 
                $objStockProducto->finalizarTransaccion();
                $objMovimientoAlmacen->cambiarSituacionPedido($dato->idmovimiento,'P');
            }

            if($_POST["cboIdTipoDocumento"]==4){//boleta
                //echo "setRun('vista/frmComprobanteB','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
                echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
            }else{
                if($_POST["cboIdTipoDocumento"]==5){//factura
                    //echo "setRun('vista/frmComprobanteF','&idventa=".$dato->idmovimiento."','frame','carga','imgloading');";
                    echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
                }else{//ticket
                    //echo "alert('Guardado Correctamente.".$iniciaproceso."$$".$iniciaproceso2."$$".$iniciaproceso3."$$$".$iniciaproceso4."$$".$iniciaproceso5."$$".$iniciaproceso6."$$".$iniciaproceso7."$$".date("Y-n-j H:i:s")."');document.getElementById('cargamant').innerHTML=''";
                    //echo "alert('Guardado Correctamente.');document.getElementById('cargamant').innerHTML='';";
                    echo "vidventa='".$idmov."';";
                }
            }

            $objMovimientoAlmacen->iniciarTransaccion();
            $objBitacora->iniciarTransaccion(); 
            $res = $objMovimientoAlmacen->eliminarDetalleAlmacen($idpedidooriginal);
            if($res!="Guardado correctamente"){
                $objMovimientoAlmacen->abortarTransaccion(); 
                $objBitacora->abortarTransaccion();
                if(ob_get_length()) ob_clean();
                echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
                exit();
            }

            if($modalidadDivision=="P"){
                foreach($_SESSION['R_carroVenta'] as $k => $v){	
                    $total = $total + ($v['cantidad']-$cantidadesIndexadas[$v['iddetalle']])*$v['precioventa'];
                    if(($v['cantidad']-$cantidadesIndexadas[$v['iddetalle']])>0){
                        $res = $objMovimientoAlmacen->insertarDetalleAlmacen($idpedidooriginal,
                        $v['idproducto'],$v['idunidad'],($v['cantidad']-$cantidadesIndexadas[$v['iddetalle']]),
                                $v['preciocompra'],
                        $v['precioventa'],$v['idsucursalproducto']);
                        //INICIO BITACORA
                        $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro+Division Cuenta', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v['idsucursalproducto'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        //FIN BITACORA
                        if($res==1){
                                $objMovimientoAlmacen->abortarTransaccion(); 
                                $objBitacora->abortarTransaccion();
                                if(ob_get_length()) ob_clean();
                                echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
                                exit();
                        }
                    }
                }
            }else{
                $total = $monto_restante;
                $cantidad = 1;
                $idprod = 1000000000;
                $res = $objMovimientoAlmacen->insertarDetalleAlmacen($idpedidooriginal,
                        $idprod,1,1,$monto_restante,$monto_restante,$_SESSION['R_IdSucursal'],$_SESSION['R_IdSucursal'],
                        "DETALLE FICTICIO PARA DIVIDIR CUENTA POR MONTOS");
                //INICIO BITACORA
                $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro+Division Cuenta',
                        'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$idprod.'; idunidad=>1; cantidad=>1; preciocompra=>'.$monto_restante.
                            '; precioventa=>'.$monto_restante.'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].
                            '; idsucursalproducto=>'.$_SESSION['R_IdSucursal'],
                        $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                //FIN BITACORA
                if($res==1){
                        $objMovimientoAlmacen->abortarTransaccion(); 
                        $objBitacora->abortarTransaccion();
                        if(ob_get_length()) ob_clean();
                        echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
                        exit();
                }
            }

            if($res=="Guardado correctamente"){
                $res = $objMovimientoAlmacen->ejecutarSQL("UPDATE movimientohoy SET subtotal = $total , total = $total WHERE idmovimiento = $idpedidooriginal;");
                if($res==1){
                    $objMovimientoAlmacen->abortarTransaccion(); 
                    $objBitacora->abortarTransaccion();
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes3: ".$objMovimiento->gMsg;
                    exit();
                }else{
                    $objMovimientoAlmacen->finalizarTransaccion(); 
                    $objBitacora->finalizarTransaccion();
                    if(ob_get_length()) ob_clean();
                    //echo "Guardado correctamente";
                    echo "vidventa='".$dato->idmovimiento."';";
                }
            }


        //-->FIN DE SUBCUENTA    

        }

        $res = $objMovimiento->obtenerDataSQL("SELECT * FROM mesaunida WHERE idmesa_padre = $idmesapadre AND idsucursal = ".$_SESSION['R_IdSucursal']);
        while ($fila=$res->fetchObject()) {
            $objMovimientoAlmacen->cambiarSituacionMesa2($fila->idmesa,'N');
        }
        $res = $objMovimiento->ejecutarSQL("DELETE FROM mesaunida WHERE idmesa_padre = $idmesapadre AND idsucursal = ".$_SESSION['R_IdSucursal']);

        exit();
	case "ACTUALIZAR" :
		//PENDIENTE
		if(ob_get_length()) ob_clean();
		//echo umill($objMovimiento->actualizarMovimiento($_POST["txtIdSucursal"], $_POST["txtIdMovimiento"],$_POST["txtIdMovimientoMaestro"],$_POST["cboDist"],$_POST["txtDireccion"],$_POST["txtEmail"],$_POST["txtTelefonoFijo"],$_POST["txtTelefonoMovil"],$_POST["txtImagen"]));
		echo "pendiente";
		exit();
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$_POST["txtId"]);
			$dax = $rt->fetchObject();
            if($dax->estado=='I' || $dax->estado=='A'){
                echo "No se puede anular este documento porque ya esta anulado";
                exit();
            }
			$fechacierre=$dax->fecha;
			$cierre=$objCaja->consultarultimocierrefecha($fechacierre);
			/*echo $fechacierre.' - '.$cierre.' - '.$_POST['txtId'];
			exit();*/
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
			}else{//SI HAY CIERRE
				echo "No se puede eliminar este documento porque la caja esta cerrada !!!";
			}
		exit();
	case "ANULAR" :
		if(ob_get_length()) ob_clean();
            $objMovimiento = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
               
			$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"(select * from movimiento union all select * from movimientohoy) T","IdMovimiento",$_POST["txtId"]);
			$dax = $rt->fetchObject();
            if($dax->estado=='I' || $dax->estado=='A'){
                echo "No se puede anular este documento porque ya esta anulado";
                exit();
            }
			$fechacierre=$dax->fecha;
			$cierre=$objCaja->consultarultimocierrefecha($fechacierre);
			/*echo $fechacierre.' - '.$cierre.' - '.$_POST['txtId'];
			exit();*/
			//SI NO HAY CIERRE
			//echo $cierre;
			//if($cierre<$_POST['txtId']){
				$objMovimiento->iniciarTransaccion();
				$objBitacora->iniciarTransaccion(); 
				$objStockProducto->iniciarTransaccion(); 
				//ANULO MOVIMIENTOS DE CAJA A PARTIR DEL DOC VENTA
				$objMovimiento->anularMovCajaaPartirdeVenta($_POST['txtId']);
				//CAMBIO PEDIDOS REFERENCIADOS A LA SITUACION A->ATENDIDA (OSEA PENDIENTE DE PAGO)
				$objMovimiento->cambiarSituacionPedidoaPartirdeVenta($_POST['txtId']);
				//ANULO VENTA
				$objMovimiento->anularMovimiento($_POST['txtId']);
                $idventa=$_POST["txtId"];
                $rs1=$objMovimiento->buscarDetalleProducto($idventa,"h");
                $carroPedido=array();
                while($dat=$rs1->fetchObject()){
                    $carroPedido[]=array("cantidad"=>$dat->cantidad,"precioventa"=>$dat->precioventa,"abreviatura"=>trim($dat->comentario==""?$dat->abreviatura:$dat->comentario),"preciocompra"=>$dat->preciocompra,"kardex"=>$dat->kardex,"compuesto"=>$dat->compuesto,"idproducto"=>$dat->idproducto,"idsucursalproducto"=>$dat->idsucursalproducto,"idunidad"=>$dat->idunidad);
                }
                //if($_SESSION["R_IdCaja"]=="4" || $_SESSION["R_IdCaja"]=="5"){//
                $idsucursal=14;
                $venta = $objMovimiento->obtenerDataSQL("select T.* from (select * from movimientohoy union all select * from movimiento) T where T.idmovimiento=".$idventa)->fetchObject();
                if($venta->idcaja=="4" || $venta->idcaja=="5"){
                    $idsucursal2="2";
                }elseif($venta->idcaja=="6"){
                    $idsucursal2="3";
                }else{
                    $idsucursal2="1";
                }
                /*}else{
                    $idsucursal=$_SESSION['R_IdSucursal'];
                }*/
                foreach($carroPedido as $v){
                    if($v['kardex']=='S'){
                        $res=$objStockProducto->insertar($idsucursal2,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$idventa,'S',$v["preciocompra"],date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        if($res!='Guardado correctamente'){
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
                            exit();
                        }
                        //$res=$objStockProducto->insertar($idsucursal2,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$idventa,'S',$v["preciocompra"],date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                    }elseif($v['kardex']!='S' and $v['compuesto']=='S'){
                        $res=$objStockProducto->insertarcompuesto($idsucursal2,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$idventa,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        if($res!='Guardado correctamente'){
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
                            exit();
                        }
                        //$res=$objStockProducto->insertarcompuesto($idsucursal2,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$idventa,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                        
                    }
                }
				//RENUEVO STOCK
				/*$res=$objStockProducto->revertir($_SESSION['R_IdSucursal'],$_POST['txtId'],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario'],'I');*/
				//INICIO BITACORA
				//eliminar movimiento
				$res=$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 44, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
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
				echo "No se puede anular este documento porque la caja esta cerrada !!!";
			}*/
		exit();
    case "MODALIDADVENTA":
        $modalidad = $_POST["modalidad"];
        $datos = array();
        if($modalidad=="V"){
            $rst = $objMovimientoAlmacen->obtenerDataSQL("SELECT v.*,us.nombreusuario FROM vale v LEFT JOIN usuario us ON us.idusuario = v.idusuario WHERE v.estado = 'N' ORDER BY v.fecha_emision");
            while ($mostrar = $rst->fetchObject()) {
                $datos[] = array(
                    $mostrar->idvale,
                    str_pad($mostrar->correlativo,6,"0",STR_PAD_LEFT),
                    $mostrar->propietario,
                    number_format($mostrar->valor,2),
                    $mostrar->fecha_emision,
                    $mostrar->nombreusuario
                    );
            }
        }
        if($modalidad=="D"){
            $rst = $objMovimientoAlmacen->obtenerDataSQL("SELECT DISTINCT ps.idpersona,psm.apellidos, psm.nombres, psm.nrodoc,'-' AS usuario FROM rolpersona rp INNER JOIN persona ps ON rp.idpersona = ps.idpersona INNER JOIN personamaestro psm ON ps.idpersonamaestro = psm.idpersonamaestro"
                    . " WHERE rp.idrol=1 and ps.estado = 'N' AND (SELECT count(mv.*) FROM movimiento mv WHERE idpersona = ps.idpersona AND mv.tipoventa = 'D' AND mv.estado = 'N' AND EXTRACT(month FROM mv.fecha::date) = EXTRACT(month FROM now()::date))<2 ORDER BY psm.apellidos, psm.nombres");
            while ($mostrar = $rst->fetchObject()) {
                $datos[] = array(
                    $mostrar->idpersona,
                    $mostrar->apellidos,
                    $mostrar->nombres,
                    $mostrar->nrodoc,
                    $mostrar->nombreusuario
                    );
            }
        }
        if($modalidad=="A"){
            $rst = $objMovimientoAlmacen->obtenerDataSQL("SELECT pa.*,us.nombreusuario,psm.apellidos,psm.nombres 
                FROM pagoanticipado pa 
                LEFT JOIN usuario us ON us.idusuario = pa.idusuario and us.idsucursal=1
                LEFT JOIN persona ps ON ps.idpersona = pa.idcliente and ps.idsucursal=1
                LEFT JOIN personamaestro psm ON psm.idpersonamaestro = ps.idpersonamaestro 
                WHERE pa.estado = 'N' ORDER BY pa.fecha");
            while ($mostrar = $rst->fetchObject()) {
                if($mostrar->tipopago == "E"){
                    $tipopago = "EFECTIVO";
                }
                $datos[] = array(
                    $mostrar->idpagoanticipado,
                    str_pad($mostrar->correlativo,6,"0",STR_PAD_LEFT),
                    $tipopago,
                    $mostrar->apellidos." ".$mostrar->nombres,
                    number_format($mostrar->saldo,2),
                    date("d/m/Y",strtotime($mostrar->fecha)),
                    date("d/m/Y",strtotime($mostrar->fechaentrega)),
                    //$mostrar->nombreusuario
                    $mostrar->datosadicionales
                    );
            }
        }
        if($modalidad=="T"){
            $idpedido=$_POST["IdPedido"];
            $ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
            $rs = $ObjDetalleAlmacen->consultarDetalleAlmacenconAjax($idpedido, 0);	
            while($mostrar=$rs->fetchObject())	{
                $datos[] = array(
                    $mostrar->iddetalle.",".$mostrar->idproducto,
                    $mostrar->numero,
                    $mostrar->codpro,
                    $mostrar->producto,
                    $mostrar->unidad,
                    number_format($mostrar->cantidad,2),
                    number_format($mostrar->precioventa,2),
                    number_format(($mostrar->cantidad*$mostrar->precioventa),2)
                    );
            }
        }
        echo json_encode(array("datos"=>$datos));
        exit();
    case "ACTUALIZARMODOPAGO":
        $idmovimiento = $_POST["txtidmov"];

        $comentario = $_POST["txtComentario"];
        $glosa = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["glosa_movimiento"]);

        if($_POST["rdbtnModoPago"]=="E"){
            $idbanco="";
            $idtipotarjeta="0";
            $numerotarjeta="";
            $banco="";
            $numero_cheque="";
            $moneda_cheque="";
            $totalpagado=$_POST["txtTotal"];
            $numero_deposito="";
            $importe_deposito="0";
            $fecha_deposito="NULL";
            $montotarjeta = "";
        }elseif($_POST["rdbtnModoPago"]=="T"){
            $idbanco="";
            $idtipotarjeta=$_POST["cboTipoTarjeta"];
            $numerotarjeta="";
            $banco="";
            $numero_cheque="";
            $moneda_cheque="";
            $totalpagado="0.00";
            $numero_deposito="";
            $importe_deposito="0";
            $fecha_deposito="NULL";
            $montotarjeta = "";
        }elseif($_POST["rdbtnModoPago"]=="A"){
            $idbanco="";
            $idtipotarjeta="0";
            $numerotarjeta="";
            $banco="";
            $numero_cheque="";
            $moneda_cheque="";
            $totalpagado=$_POST["txtPagoEfectivo"];
            $numero_deposito="";
            $importe_deposito="0";
            $fecha_deposito="NULL";
            $montotarjeta = "1@".$_POST["txtMontoVisa"]."|2@".$_POST["txtMontoMastercard"];
        }elseif($_POST["rdbtnModoPago"]=="C"){
            $idbanco="";
            $idtipotarjeta="0";
            $numerotarjeta="";
            $banco=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["banco_cheque"]);
            $numero_cheque=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["numero_cheque"]);
            $moneda_cheque=$_POST["moneda_cheque"];
            $totalpagado="0.00";
            $numero_deposito="";
            $importe_deposito="0";
            $fecha_deposito="NULL";
            $montotarjeta = "";
        }elseif($_POST["rdbtnModoPago"]=="D"){
            $idbanco="";
            $idtipotarjeta="0";
            $numerotarjeta="";
            $banco=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["banco_deposito"]);
            $numero_cheque="";
            $moneda_cheque="";
            $numero_deposito=str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $_POST["numero_deposito"]);
            $importe_deposito=$_POST["importe_deposito"];
            $fecha_deposito="'".$_POST["fecha_deposito"]."'";
            $totalpagado="0.00";
            $montotarjeta = "";
        }
        $rst = $objMovimiento->ejecutarSQL("UPDATE movimiento SET totalpagado=".$totalpagado.",modopago='".$_POST["rdbtnModoPago"]."',idtipotarjeta=".$idtipotarjeta.",comentario='".$comentario."',glosa='".$glosa."',nombrebanco='".$banco."',numerocheque='".$numero_cheque."',monedacheque='".$moneda_cheque."',numerooperacion='".$numero_deposito."',importedeposito=".$importe_deposito.",fechadeposito=".$fecha_deposito.",montotarjeta='".$montotarjeta."' WHERE idmovimiento=".$idmovimiento);
        $rst = $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET totalpagado=".$totalpagado.",modopago='".$_POST["rdbtnModoPago"]."',idtipotarjeta=".$idtipotarjeta.",comentario='".$comentario."',glosa='".$glosa."',nombrebanco='".$banco."',numerocheque='".$numero_cheque."',monedacheque='".$moneda_cheque."',numerooperacion='".$numero_deposito."',importedeposito=".$importe_deposito.",fechadeposito=".$fecha_deposito.",montotarjeta='".$montotarjeta."' WHERE idmovimiento=".$idmovimiento);
        $rst = $objMovimiento->ejecutarSQL("UPDATE movimiento SET totalpagado=".$totalpagado.",modopago='".$_POST["rdbtnModoPago"]."',idtipotarjeta=".$idtipotarjeta.",comentario='".$comentario."',glosa='".$glosa."',nombrebanco='".$banco."',numerocheque='".$numero_cheque."',monedacheque='".$moneda_cheque."',numerooperacion='".$numero_deposito."',importedeposito=".$importe_deposito.",fechadeposito=".$fecha_deposito.",montotarjeta='".$montotarjeta."' WHERE idmovimientoref=".$idmovimiento);
        $rst = $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET totalpagado=".$totalpagado.",modopago='".$_POST["rdbtnModoPago"]."',idtipotarjeta=".$idtipotarjeta.",comentario='".$comentario."',glosa='".$glosa."',nombrebanco='".$banco."',numerocheque='".$numero_cheque."',monedacheque='".$moneda_cheque."',numerooperacion='".$numero_deposito."',importedeposito=".$importe_deposito.",fechadeposito=".$fecha_deposito.",montotarjeta='".$montotarjeta."' WHERE idmovimientoref=".$idmovimiento);
        $sql = "UPDATE movimientohoy SET totalpagado=".$totalpagado.",modopago=".$_POST["rdbtnModoPago"].",idtipotarjeta=".$idtipotarjeta.",comentario=".$comentario.",glosa=".$glosa.",nombrebanco=".$banco.",numerocheque=".$numero_cheque.",monedacheque=".$moneda_cheque.",numerooperacion=".$numero_deposito.",importedeposito=".$importe_deposito.",fechadeposito=".$fecha_deposito.",montotarjeta=".$montotarjeta." WHERE idmovimiento=".$idmovimiento;
        $res=$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 44, 'Actualizar Modo Pago', $sql, $_SESSION['R_IdSucursal'],$idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
        echo "Guardado correctamente";
        exit();
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>