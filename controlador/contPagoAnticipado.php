<?php
require("../modelo/clsMarca.php");
require("../modelo/clsBitacora.php");
require("../modelo/clsMovCaja.php");
require("../modelo/clsDetalleAlmacen.php");
$accion = $_POST["accion"];
$clase = $_POST["clase"];
if(!$clase){
	$clase = $_GET["id_clase"];	
}
if (!isset($accion)){
	echo "Error: Accion no encontrada.".$action;
	exit();
}
$objMarca = new clsMarca($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objMovimiento = new clsMovCaja($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objMovimientoAlmacen = new clsDetalleAlmacen($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
            if(ob_get_length()) ob_clean();
            $correlativo = $objMarca->obtenerDataSQL("SELECT max(correlativo) as ultimo FROM pagoanticipado WHERE estado <> 'A'")->fetchObject();
            if(!empty($correlativo)){
                $correlativo = $correlativo->ultimo + 1;
            }else{
                $correlativo = 1;
            }
        	$numero = $objMovimiento->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
            if($_POST["txtTipoPago"]=="A"){
                $totalpagado=$_POST["txtPagoEfectivo"];
            }elseif($_POST["txtTipoPago"]=="E"){
                $totalpagado=$_POST["txtValor"];
            }else{
                $totalpagado=0;
            }
            if($_POST["txtTipoPago"]=="T"){
                $idtipotarjeta=$_POST["cboTipoTarjeta"];
            }else{
                $idtipotarjeta="";
            }
            $rst=$objMarca->ejecutarSQL("INSERT INTO pagoanticipado (correlativo,valor,tipopago,fecha,idusuario,idcliente,datosadicionales,saldo,fechaentrega,total,estado) VALUES (".$correlativo.",".$_POST["txtValor"].",'".$_POST["txtTipoPago"]."','".$_POST["txtFecha"]."',".$_SESSION['R_IdUsuario'].",".$_POST["txtIdPersona"].",'".$_POST["txtDatos"]."',".$_POST["txtValor"].",'".$_POST["txtFechaEntrega"]."',".$_POST["txtTotal"].",'".$_POST["txtEntrega"]."')");
            if($rst==1){
                echo "vtext='ERROR AL INSERTAR';vidmovimiento=0;";
            }else{
                $id = $objMarca->obtenerDataSQL("SELECT max(idpagoanticipado) as ultimo FROM pagoanticipado WHERE estado <> 'A'")->fetchObject()->ultimo;
                if($_POST["txtComprobante"]=="S"){
                    //Inserto Documento Venta; editado con el tipo de pago
                    $rst = $objMovimiento->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A',
                                    date("d/m/Y H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtTo"],
                                    0, $_POST["txtTo"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona2"],
                                    $_SESSION['R_IdPersona'], $id, NULL, "Por Pedido:".$_POST["txtDatos"],'N',$_SESSION["R_IdCaja"],$_SESSION['R_IdSucursalUsuario'],
                                    $_POST["txtIdSucursalPersona2"],$_SESSION['R_IdSucursalUsuario'],'',0,$idtipotarjeta,'');
                    $idventa = $rst->fetchObject()->idmovimiento;
                    $objMovimiento->ejecutarSQL("update movimientohoy set manual='N' where idmovimiento=".$idventa);
                    $res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($idventa,269,1,$_POST["txtCant"],0,round($_POST["txtTo"]/$_POST["txtCant"],2),1,$_POST['txtPro']);
                }else{
                    $idventa=0;
                }
                if($_POST["txtTipoPago"]=="T" || $_POST["txtTipoPago"]=="A" || $_POST["txtTipoPago"]=="E"){
                    $rst = $objMovimiento->insertarMovimiento(27, 4, $numero, 9, 'A', date("d/m/Y H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtValor"], 0, $_POST["txtValor"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdUsuario'], $id, NULL, $_POST["txtDatos"],'N',$_SESSION["R_IdCaja"],$_SESSION['R_IdSucursalUsuario'],0,$_SESSION['R_IdSucursalUsuario'],"","",$idtipotarjeta);
                    $datoc=$rst->fetchObject();
                    if($_POST["txtTipoPago"]=="A"){    
                        $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET montotarjeta='1@".$_POST["txtMontoVisa"]."|2@".$_POST["txtMontoMastercard"]."',modopago='".$_POST["txtTipoPago"]."' WHERE idmovimiento=".$datoc->idmovimiento);
                    }else{
                        $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["txtTipoPago"]."' WHERE idmovimiento=".$datoc->idmovimiento);
                    }
                    echo "vtext='GUARDADO CORRECTAMENTE';vidmovimiento='$datoc->idmovimiento';vidventa='$idventa';";
                }else{
                    echo "vtext='GUARDADO CORRECTAMENTE';vidmovimiento='0';vidventa='$idventa';";
                }
                
            }
            break;
	case "ACTUALIZAR" :
            if(ob_get_length()) ob_clean();
            $rst=$objMarca->ejecutarSQL("UPDATE pagoanticipado SET valor=".$_POST["txtValor"].",tipopago='".$_POST["txtTipoPago"]."',fecha='".$_POST["txtFecha"]."',datosadicionales='".$_POST["txtDatos"]."',idusuario=".$_SESSION['R_IdUsuario'].",idcliente=".$_POST["txtIdPersona"].",saldo=".$_POST["txtValor"].",fechaentrega='".$_POST["txtFechaEntrega"]."' WHERE idpagoanticipado = ".$_POST["txtId"]);
            $idmovimiento=$objMovimiento->obtenerDataSQL("select * from movimientohoy where idmovimientoref=".$_POST["txtId"]." and idconceptopago=27")->fetchObject()->idmovimiento;
            if($_POST["txtTipoPago"]=="A"){
                $totalpagado=$_POST["txtPagoEfectivo"];
            }elseif($_POST["txtTipoPago"]=="E"){
                $totalpagado=$_POST["txtValor"];
            }else{
                $totalpagado=0;
            }
            if($_POST["txtTipoPago"]=="T"){
                $idtipotarjeta=$_POST["cboTipoTarjeta"];
            }else{
                $idtipotarjeta="";
            }
            $objMovimiento->ejecutarSQL("update movimientohoy set totalpagado=$totalpagado,idtipotarjeta='$idtipotarjeta' where idmovimiento=".$idmovimiento);
            if($_POST["txtTipoPago"]=="A"){    
                $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET montotarjeta='1@".$_POST["txtMontoVisa"]."|2@".$_POST["txtMontoMastercard"]."',modopago='".$_POST["txtTipoPago"]."' WHERE idmovimiento=".$idmovimiento);
            }else{
                $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["txtTipoPago"]."' WHERE idmovimiento=".$idmovimiento);
            }
            if($rst==1){
                echo "ERROR AL ACTUALIZAR";
            }else{
                echo "GUARDADO CORRECTAMENTE";
            }
            break;
    case "PAGAR" :
            if(ob_get_length()) ob_clean();
            $numero = $objMovimiento->generaNumeroSinSerie(4,9,substr($_SESSION["R_FechaProceso"],3,2));
            if($_POST["txtTipoPago"]=="A"){
                $totalpagado=$_POST["txtPagoEfectivo"];
            }elseif($_POST["txtTipoPago"]=="E"){
                $totalpagado=$_POST["txtPago"];
            }else{
                $totalpagado=0;
            }
            if($_POST["txtTipoPago"]=="T"){
                $idtipotarjeta=$_POST["cboTipoTarjeta"];
            }else{
                $idtipotarjeta="";
            }
            $rst=$objMarca->ejecutarSQL("UPDATE pagoanticipado SET valor=".$_POST["txtPago"]." + valor,datosadicionales='".$_POST["txtDatos"]."',estado='".$_POST["txtEntrega"]."' WHERE idpagoanticipado = ".$_POST["txtId"]);
            if($rst==1){
                echo "ERROR AL ACTUALIZAR";
            }else{
                $id = $_POST["txtId"];
                if($_POST["txtComprobante"]=="S"){
                    //Inserto Documento Venta; editado con el tipo de pago
                    $rst = $objMovimiento->insertarMovimiento(0, 2, $_POST["txtNumero"], $_POST["cboIdTipoDocumento"], 'A',
                                    date("d/m/Y H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtTo"],
                                    0, $_POST["txtTo"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona2"],
                                    $_SESSION['R_IdPersona'], $id, NULL, "Por Pedido:".$_POST["txtDatos"],'N',0,$_SESSION['R_IdSucursalUsuario'],
                                    $_POST["txtIdSucursalPersona2"],$_SESSION['R_IdSucursalUsuario'],'',0,$idtipotarjeta,'');
                    $idventa = $rst->fetchObject()->idmovimiento;
                    $objMovimiento->ejecutarSQL("update movimientohoy set manual='N' where idmovimiento=".$idventa);
                    $res = $objMovimientoAlmacen->insertarDetalleAlmacenOut($idventa,263,1,$_POST["txtCant"],0,round($_POST["txtTo"]/$_POST["txtCant"],2),1,$_POST['txtPro']);
                }else{
                    $idventa=0;
                }
                if($_POST["txtTipoPago"]=="T" || $_POST["txtTipoPago"]=="A" || $_POST["txtTipoPago"]=="E"){
                    $rst = $objMovimiento->insertarMovimiento(27, 4, $numero, 9, 'A', date("d/m/Y H:i:s"), '', '', 0, 0, 'S', 0, $_POST["txtPago"], 0, $_POST["txtPago"], $totalpagado, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $_SESSION['R_IdUsuario'], $id, NULL, "Saldo de Nota de Pedido",'N',0,$_SESSION['R_IdSucursalUsuario'],0,$_SESSION['R_IdSucursalUsuario'],"","",$idtipotarjeta);
                    $datoc=$rst->fetchObject();
                    if($_POST["txtTipoPago"]=="A"){    
                        $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET montotarjeta='1@".$_POST["txtMontoVisa"]."|2@".$_POST["txtMontoMastercard"]."',modopago='".$_POST["txtTipoPago"]."' WHERE idmovimiento=".$datoc->idmovimiento);
                    }else{
                        $objMovimiento->ejecutarSQL("UPDATE movimientohoy SET modopago='".$_POST["txtTipoPago"]."' WHERE idmovimiento=".$datoc->idmovimiento);
                    }
                    echo "vtext='GUARDADO CORRECTAMENTE';vidmovimiento='$datoc->idmovimiento';vidventa='$idventa';";
                }else{
                    echo "vtext='GUARDADO CORRECTAMENTE';vidmovimiento='0';vidventa='$idventa';";
                }
            }
            break;
	case "ELIMINAR" :
            if(ob_get_length()) ob_clean();
            $rst=$objMarca->ejecutarSQL("UPDATE pagoanticipado SET estado = 'A'  WHERE idpagoanticipado = ".$_POST["txtId"]);
            if($rst==1){
                echo "ERROR AL ACTUALIZAR";
            }else{
                $rs=$objMovimiento->obtenerDataSQL("select * from movimientohoy where idmovimientoref=".$_POST["txtId"]." and idconceptopago=27");
                if($rs->rowCount()){
                    $dato=$rs->fetchObject();
                    $objMovimiento->anularMovimiento($dato->idmovimiento);   
                }
                echo "GUARDADO CORRECTAMENTE";
            }
            break;
    case "ENTREGAR" :
        $objMarca->ejecutarSQL("update pagoanticipado set estado='E' where idpagoanticipado=".$_POST["txtId"]);
        echo "Guardado Correctamente";
        break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>