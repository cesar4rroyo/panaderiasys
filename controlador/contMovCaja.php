<?php
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
$objMovimiento = new clsMovCaja($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
            if($_POST["txtTipoPago"]=="A"){
                $totalpagado=$_POST["txtPagoEfectivo"];
            }elseif($_POST["txtTipoPago"]=="E"){
                $totalpagado=$_POST["txtTotal"];
            }else{
                $totalpagado=0;
            }
            if($_POST["txtTipoPago"]=="T"){
                $idtipotarjeta=$_POST["cboTipoTarjeta"];
            }else{
                $idtipotarjeta="";
            }
            $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', date("d/m/Y H:i:s"), '', '', 0, 0, 'S', 0, $monto, 0, $monto, $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdUsuario'], NULL, NULL, $_POST["txtComentario"],'N',$_SESSION["R_IdCaja"],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario'],'',0,$idtipotarjeta);
            $dato=$rst->fetchObject();
            $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["txtTipoPago"]."' WHERE idmovimiento=".$dato->idmovimiento);
            //INICIO BITACORA
            $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$monto.'; igv=>0; total=>'.$monto.'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$_SESSION['R_IdUsuario'].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>'.$_POST["txtIdSucursalPersona"].'; idsucursalresponsable=>'.$_SESSION['R_IdSucursalUsuario'], $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
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
                    $objMovimiento->ejecutarSQL("update movimientohoy set numerooperacion='".$_POST["txtNroOperacion"]."' where idmovimiento=".$dato->idmovimiento." and idsucursal=".$_SESSION["R_IdSucursal"]);
                    if(ob_get_length()) ob_clean();
                    echo "Guardado correctamente";
            }
            exit();
    case "NUEVO-CAJERO" :
            if(ob_get_length()) ob_clean();
            $objMovimiento->iniciarTransaccion();
            $objBitacora->iniciarTransaccion();
            if($_POST["optMoneda"]=='D'){
                    $monto=$_POST["txtTotal"]*$_SESSION['R_TipoCambio'];
            }else{
                    $monto=$_POST["txtTotal"];	
            }
            $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $monto, 0, $monto, 0, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdUsuario'], NULL, NULL, $_POST["txtComentario"],'N',$_SESSION['R_IdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario']);
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
            exit();
    case "APERTURA" :
            if(ob_get_length()) ob_clean();
            $num_mov=$objMovimiento->existenciamov($_SESSION["R_IdCaja"]);
            if($num_mov==0){
                $objMovimiento->iniciarTransaccion();
                $objBitacora->iniciarTransaccion();
                $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION["R_IdCaja"]);
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
                }
                /*$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, str_pad($_POST["txtNumero"]+1,6,"0",STR_PAD_LEFT), $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"], '', '', 0, 0, 'D', 0, $_POST["txtMontoDolares"], 0, $_POST["txtMontoDolares"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
                if(is_string($rst)){
                        $objMovimiento->abortarTransaccion(); 
                        if(ob_get_length()) ob_clean();
                        echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                        exit();
                }*/else{
                        $objMovimiento->finalizarTransaccion(); 
                        $objBitacora->finalizarTransaccion();
                        if(ob_get_length()) ob_clean();
                        echo "Guardado correctamente";
                }
            }else{
                $fechacierre=$objMovimiento->consultarmaxfecha();
                $cierre=$objMovimiento->consultarcierre($fechacierre,$_SESSION["R_IdCaja"]);
                //SI NO HAY CIERRE
                if($cierre==0){
                    $objMovimiento->iniciarTransaccion();
                    $objBitacora->iniciarTransaccion();
                    $numero = $objMovimiento->generaNumeroSinSerie(4,10,substr($_SESSION["R_FechaProceso"],3,2));
                    //CERRAMOS CAJA EN SOLES
                    $rst = $objMovimiento->insertarMovimiento(2, 4, $numero, $_POST["cboIdTipoDocumento"], 'A', $fechacierre, '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION["R_IdCaja"]);
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
                    //CERRAMOS CAJA EN DOLARES
                    /*$rst = $objMovimiento->insertarMovimiento(2, 4, str_pad($numero+1,6,"0",STR_PAD_LEFT), $_POST["cboIdTipoDocumento"], 'A', $fechacierre, '', '', 0, 0, 'D', 0, $_POST["txtMontoDolares"], 0, $_POST["txtMontoDolares"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
                    if(is_string($rst)){
                            $objMovimiento->abortarTransaccion(); 
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                            exit();
                    }*/
                    //APERTURAMOS CAJA EN SOLES
                    $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION["R_IdCaja"]);
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
                    }
                    //APERTURAMOS CAJA EN DOLARES
                    /*$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, str_pad($_POST["txtNumero"]+1,6,"0",STR_PAD_LEFT), $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"], '', '', 0, 0, 'D', 0, $_POST["txtMontoDolares"], 0, $_POST["txtMontoDolares"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
                    if(is_string($rst)){
                            $objMovimiento->abortarTransaccion(); 
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                            exit();
                    }*/else{
                            $objMovimiento->finalizarTransaccion(); 
                            $objBitacora->finalizarTransaccion();
                            if(ob_get_length()) ob_clean();
                            echo "Guardado correctamente";
                    }
                }else{
                        //SI HAY CIERRE
                    $objMovimiento->iniciarTransaccion();
                    $objBitacora->iniciarTransaccion();
                    $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION["R_IdCaja"]);
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
                    }
                    /*$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, str_pad($_POST["txtNumero"]+1,6,"0",STR_PAD_LEFT), $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"], '', '', 0, 0, 'D', 0, $_POST["txtMontoDolares"], 0, $_POST["txtMontoDolares"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
                    if(is_string($rst)){
                            $objMovimiento->abortarTransaccion(); 
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                            exit();
                    }*/else{
                            $objMovimiento->finalizarTransaccion(); 
                            $objBitacora->finalizarTransaccion();
                            if(ob_get_length()) ob_clean();
                            echo "Guardado correctamente";
                    }	
                }
                if($_SESSION["R_IdCaja"]=="8"){
                    $objMovimiento->ejecutarSQL("insert into entradacolor(idcolor,idapertura,inicial,final,idcaja,idsucursal,idproducto) values(".$_POST["cboEGeneral"].",".$dato->idmovimiento.",".$_POST["txtEGeneral"].",".$_POST["txtEGeneral"].",".$_SESSION["R_IdCaja"].",".$_SESSION["R_IdSucursal"].",70)");
                    $objMovimiento->ejecutarSQL("insert into entradacolor(idcolor,idapertura,inicial,final,idcaja,idsucursal,idproducto) values(".$_POST["cboEVIP"].",".$dato->idmovimiento.",".$_POST["txtEVIP"].",".$_POST["txtEGeneral"].",".$_SESSION["R_IdCaja"].",".$_SESSION["R_IdSucursal"].",71)");
                }
            }		
            exit();
    case "CIERRE" :
                    if(ob_get_length()) ob_clean();
                    $objMovimiento->iniciarTransaccion();
                    $objBitacora->iniciarTransaccion();
                    $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], $_POST["txtReal"], $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION["R_IdCaja"]);
                    $dato=$rst->fetchObject();
                    $objMovimiento->ejecutarSQL("update movimientohoy set dinero='".($_POST["txtFinal"]+0)."' where idmovimiento=".$dato->idmovimiento);
                    //INICIO BITACORA
                    $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                    //FIN BITACORA
                    if(is_string($rst)){
                            $objMovimiento->abortarTransaccion(); 
                            $objBitacora->abortarTransaccion();
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
                            exit();
                    }
                    /*$rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, str_pad($_POST["txtNumero"]+1,6,"0",STR_PAD_LEFT), $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"], '', '', 0, 0, 'D', 0, $_POST["txtMontoDolares"], 0, $_POST["txtMontoDolares"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
                    if(is_string($rst)){
                            $objMovimiento->abortarTransaccion(); 
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                            exit();
                    }*/
                    /*$numero = $objMovimiento->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
                    //APERTURAMOS DIA SIGUIENTE
                    $rst = $objMovimiento->insertarMovimiento(1, 4, $numero, $_POST["cboIdTipoDocumento"], 'A', $objMovimiento->fechasiguiente($_POST["txtFecha"]), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
                    $dato=$rst->fetchObject();
                    //INICIO BITACORA
                    $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>1; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$objMovimiento->fechasiguiente($_POST["txtFecha"]).'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>0; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
                    //FIN BITACORA
                    if(is_string($rst)){
                            $objMovimiento->abortarTransaccion();
                            $objBitacora->abortarTransaccion(); 
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
                            exit();
                    }*/
                    /*$rst = $objMovimiento->insertarMovimiento(1, 4, str_pad($numero+1,6,"0",STR_PAD_LEFT), $_POST["cboIdTipoDocumento"], 'A', $objMovimiento->fechasiguiente($_POST["txtFecha"]), '', '', 0, 0, 'D', 0, $_POST["txtMontoDolares"], 0, $_POST["txtMontoDolares"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N');
                    if(is_string($rst)){
                            $objMovimiento->abortarTransaccion(); 
                            if(ob_get_length()) ob_clean();
                            echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                            exit();
                    }*/else{
                        $objMovimiento->moverdatos($_SESSION["R_IdCaja"]);
                        $objMovimiento->finalizarTransaccion(); 
                        $objBitacora->finalizarTransaccion();
                        if(ob_get_length()) ob_clean();
                        echo "Guardado correctamenteee";
                    }
                    exit();
    case "CIERRE-CAJERO" :
        if(ob_get_length()) ob_clean();
        $objMovimiento->iniciarTransaccion();
        $objBitacora->iniciarTransaccion();
        $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], $_POST["txtReal"], $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',$_SESSION['R_IdCaja'],$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$_SESSION['R_IdSucursalUsuario']);
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
        exit();
    case "ELIMINAR" ://PENDIENTE
            if(ob_get_length()) ob_clean();
            //echo 'pendiente de implementar';
            //ANULO MOVIMIENTOS DE CAJA A PARTIR DEL DOC VENTA
            echo $objMovimiento->anularMovimiento($_POST['txtId']);
            //echo umill($objMovimiento->eliminarMovimiento($_POST["txtIdSucursal"], $_POST['txtIdMovimiento'], $_POST["txtIdMovimientoMaestro"]));
            exit();
    case "ASIGNAR" :
            if(ob_get_length()) ob_clean();
            $objMovimiento->iniciarTransaccion();
            $objBitacora->iniciarTransaccion();
            //SACO MONTO DE CAJA CHICA
            $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 4, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, 'Asignaci&oacute;n de monto a caja. '.$_POST["txtComentario"],'N',$_POST['cboIdCaja']);
            $dato=$rst->fetchObject();
            //INICIO BITACORA
            $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>'.$_POST["cboConceptoPago"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST["cboIdTipoDocumento"].'; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Asignaci&oacute;n de monto a caja. '.$_POST["txtComentario"].'; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            //FIN BITACORA
            if(is_string($rst)){
                    $objMovimiento->abortarTransaccion(); 
                    $objBitacora->abortarTransaccion();
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
                    exit();
            }

            $numero = $objMovimiento->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
            //ASIGNO MONTO A CAJA
            $rst = $objMovimiento->insertarMovimiento(17, 4, $numero, 9, 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_SESSION['R_IdSucursal'], 0, NULL, NULL, 'Monto asignado desde caja chica','N',$_POST['cboIdCaja']);
            $dato=$rst->fetchObject();
            //INICIO BITACORA
            $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>17; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>4; numero=>'.$numero.'; idtipodocumento=>9; formapago=>A; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>0; idmesa=>0; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtMontoSoles"].'; igv=>0; total=>'.$_POST["txtMontoSoles"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>S; idpersona=>'.$_SESSION['R_IdSucursal'].'; idresponsable=>0; idmovimientoref=>; idsucursalref=>; comentario=>Monto asignado desde caja chica; situacion=>N; estado=>N; idcaja=>'.$_POST['cboIdCaja'].'; idsucursalusuario=>0; idsucursalpersona=>'.$_SESSION['R_IdSucursal'].'; idsucursalresponsable=>0', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            //FIN BITACORA
            if(is_string($rst)){
                    $objMovimiento->abortarTransaccion();
                    $objBitacora->abortarTransaccion(); 
                    if(ob_get_length()) ob_clean();
                    echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
                    exit();
            }else{
                    $objMovimiento->finalizarTransaccion(); 
                    $objBitacora->finalizarTransaccion();
                    if(ob_get_length()) ob_clean();
                    echo "Guardado correctamente";
            }
            exit();
    case "BACKUP" :
        try{
            exec("E:\AppServ\www\palacio\backup.bat");
            echo "Generado Correctamente";
        }catch(exception $e){
            echo "Error".$e;
        }
        exit();
    case "MODALCIERRE":
        if(ob_get_length()) ob_clean();
        $modo = $_POST["modo"];
        $sql = "";
        if($modo=="EFECTIVO"){
            $sql = "SELECT mh.*,(select T.comentario from movimientohoy T where T.idmovimiento=mh.idmovimientoref and T.idsucursal=mh.idsucursal) as numero2,cp.descripcion FROM movimientohoy mh INNER JOIN conceptopago cp ON mh.idconceptopago = cp.idconceptopago WHERE mh.idconceptopago = 3 AND (modopago='E' OR modopago='A') and mh.idcaja=".$_SESSION["R_IdCaja"]." and mh.estado='N' ORDER BY fecha ASC";
        }elseif($modo=="TARJETAS" || $modo=="TARJETAVISA" || $modo=="TARJETAMASTERCARD"){
            $sql = "SELECT mh.*,(select T.comentario from movimientohoy T where T.idmovimiento=mh.idmovimientoref and T.idsucursal=mh.idsucursal) as numero2 FROM movimientohoy mh INNER JOIN conceptopago cp ON mh.idconceptopago = cp.idconceptopago WHERE mh.idconceptopago = 3 AND (modopago='T' OR modopago='A') and mh.idcaja=".$_SESSION["R_IdCaja"]." ORDER BY fecha ASC";
        }elseif($modo=="CHEQUES"){
            $sql = "SELECT *,cp.descripcion FROM movimientohoy mh INNER JOIN conceptopago cp ON mh.idconceptopago = cp.idconceptopago WHERE mh.idtipotarjeta IS NOT NULL AND mh.idconceptopago = 3 AND (modopago='C') ORDER BY fecha ASC";
        }elseif($modo=="DEPOSITOS"){
            $sql = "SELECT *,cp.descripcion FROM movimientohoy mh INNER JOIN conceptopago cp ON mh.idconceptopago = cp.idconceptopago WHERE mh.idtipotarjeta IS NOT NULL AND mh.idconceptopago = 3 AND (modopago='D') ORDER BY fecha ASC";
        }elseif($modo=="TOTAL"){
            $sql = "SELECT *,cp.descripcion FROM movimientohoy mh INNER JOIN conceptopago cp ON mh.idconceptopago = cp.idconceptopago WHERE mh.idconceptopago = 3 ORDER BY fecha ASC";
        }elseif($modo=="INGRESOS"){
            $sql = "SELECT *,cp.descripcion FROM movimientohoy mh INNER JOIN conceptopago cp ON mh.idconceptopago = cp.idconceptopago WHERE mh.idtipodocumento = 9 AND mh.idconceptopago NOT IN (1,3) ORDER BY fecha ASC";
        }elseif($modo=="GASTOS"){
            $sql = "SELECT *,cp.descripcion FROM movimientohoy mh INNER JOIN conceptopago cp ON mh.idconceptopago = cp.idconceptopago WHERE mh.idtipodocumento = 10 ORDER BY fecha ASC";
        }
        $rst = $objMovimiento->obtenerDataSQL($sql);
        $datos = array();
        while ($mostrar = $rst->fetchObject()) {
            if($modo=="TARJETAVISA"){
                if($mostrar->modopago=="T" && $mostrar->idtipotarjeta == 1){
                    $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcio." ".$mostrar->numero2,"",$mostrar->total-$mostrar->total_pagado,  substr($mostrar->fecha, 0, 16));
                }elseif($mostrar->modopago=="A"){
                    $total = $mostrar->montotarjeta;
                    $total = explode("|", $total);
                    $total = explode("@", $total[0]);
                    $total = $total[1];
                    if($total>0){
                        $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcion." ".$mostrar->numero2,"",$total,  substr($mostrar->fecha, 0, 16));
                    }
                }
            }elseif($modo=="TARJETAMASTERCARD"){
                if($mostrar->modopago=="T" && $mostrar->idtipotarjeta == 2){
                    $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcion,$mostrar->total-$mostrar->total_pagado,  substr($mostrar->fecha, 0, 16));
                }elseif($mostrar->modopago=="A"){
                    $total = $mostrar->montotarjeta;
                    $total = explode("|", $total);
                    $total = explode("@", $total[1]);
                    $total = $total[1];
                    if($total>0){
                        $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->numerotarjeta,$total,  substr($mostrar->fecha, 0, 16));
                    }
                }
            }elseif($modo=="EFECTIVO"){
                if($mostrar->totalpagado>0){
                    $datos[] = array($mostrar->numero,$mostrar->comentario." ".$mostrar->numero2,$mostrar->descripcion,$mostrar->totalpagado,  substr($mostrar->fecha, 0, 16));
                }
            }elseif($modo=="TOTAL"){
                $mododpago = "";
                if($mostrar->modopago=="E"){
                    $mododpago = "EFECTIVO";
                }elseif($mostrar->modopago=="T"){
                    $mododpago = "TARJETA";
                }elseif($mostrar->modopago=="A"){
                    $mododpago = "EFECTIVO Y TARJETA";
                }elseif($mostrar->modopago=="C"){
                    $mododpago = "CHEQUE";
                }elseif($mostrar->modopago=="D"){
                    $mododpago = "DEPOSITO";
                }
                if($mostrar->modopago=="T" && $mostrar->idtipotarjeta == 1){
                    $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcion,$mostrar->total,$mododpago,$mostrar->totalpagado,$mostrar->total-$mostrar->totalpagado,0,  substr($mostrar->fecha, 0, 16));
                }elseif($mostrar->modopago=="T" && $mostrar->idtipotarjeta == 2){
                    $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcion,$mostrar->total,$mododpago,$mostrar->totalpagado,0,$mostrar->total-$mostrar->totalpagado,  substr($mostrar->fecha, 0, 16));
                }elseif($mostrar->modopago=="A"){
                    $total = $mostrar->montotarjeta;
                    $total = explode("|", $total);
                    $total_visa = explode("@", $total[0]);
                    $total_visa = $total_visa[1];
                    $total_mastercard = explode("@", $total[1]);
                    $total_mastercard = $total_mastercard[1];
                    $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcion,$mostrar->total,$mododpago,$mostrar->totalpagado,$total_visa,$total_mastercard,  substr($mostrar->fecha, 0, 16));
                }else{
                    $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcion,$mostrar->total,$mododpago,$mostrar->totalpagado,0,0,  substr($mostrar->fecha, 0, 16));
                }
            }else{
                $datos[] = array($mostrar->numero,$mostrar->comentario,$mostrar->descripcion,$mostrar->total,  substr($mostrar->fecha, 0, 16));
            }
        }
        echo json_encode($datos);
        exit();
    case "EXPORTARMODALCIERRE":
        if($_POST["descargar"]=="NO"){
            //echo json_encode("accion=EXPORTARMODALCIERRE&descargar=SI&datos_a_enviar=".$("#tablaModalDetalleCerrar").html());
        }else{
            header("Content-type: application/vnd.ms-excel; name='excel'");
            header("Content-Disposition: filename=ficheroExcel.xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo $_POST['datos_a_enviar'];
        }
    case "APERTURA-GASTO" :
        if(ob_get_length()) ob_clean();
        $num_mov=$objMovimiento->existenciamov(10);
        if($num_mov==0){
            $objMovimiento->iniciarTransaccion();
            $objBitacora->iniciarTransaccion();
            $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',10);
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
            $cierre=$objMovimiento->consultarcierre($fechacierre,10);
            //SI NO HAY CIERRE
            if($cierre==0){
                $objMovimiento->iniciarTransaccion();
                $objBitacora->iniciarTransaccion();
                $numero = $objMovimiento->generaNumeroSinSerie(4,10,substr($_SESSION["R_FechaProceso"],3,2));
                //CERRAMOS CAJA EN SOLES
                $rst = $objMovimiento->insertarMovimiento(2, 7, $numero, $_POST["cboIdTipoDocumento"], 'A', $fechacierre, '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',10);
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
                $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',10);
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
                $rst = $objMovimiento->insertarMovimiento($_POST["cboConceptoPago"], 7, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A', $_POST["txtFecha"].' '.date("H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtMontoSoles"], 0, $_POST["txtMontoSoles"], 0, $_SESSION['R_IdUsuario'], 'S', $_POST["txtIdPersona"], 0, NULL, NULL, $_POST["txtComentario"],'N',10);
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
        exit();
    default:
            echo "Error en el Servidor: Operacion no Implementada.";
            exit();
}
?>