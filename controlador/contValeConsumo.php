<?php
require("../modelo/clsMarca.php");
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
$objMarca = new clsMarca($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
            if(ob_get_length()) ob_clean();
            $correlativo = $objMarca->obtenerDataSQL("SELECT max(correlativo) as ultimo FROM vale WHERE estado <> 'A'")->fetchObject();
            if(!empty($correlativo)){
                $correlativo = $correlativo->ultimo + 1;
            }else{
                $correlativo = 1;
            }
            $rst=$objMarca->ejecutarSQL("INSERT INTO vale (correlativo,valor,propietario,fecha_emision,plazo,idusuario,idcliente) VALUES (".$correlativo.",".$_POST["txtValor"].",'".$_POST["persona"]."','".$_POST["txtFecha"]."',".$_POST["txtPlazo"].",".$_SESSION['R_IdUsuario'].",".$_POST["txtIdPersona"].")");
            if($rst==1){
                echo "ERROR AL INSERTAR";
            }else{
                echo "GUARDADO CORRECTAMENTE";
            }
            break;
	case "ACTUALIZAR" :
            if(ob_get_length()) ob_clean();
            $rst=$objMarca->ejecutarSQL("UPDATE vale SET valor=".$_POST["txtValor"].",propietario='".$_POST["persona"]."',fecha_emision='".$_POST["txtFecha"]."',plazo=".$_POST["txtPlazo"].",idusuario=".$_SESSION['R_IdUsuario'].",idcliente=".$_POST["txtIdPersona"]." WHERE idvale = ".$_POST["txtId"]);
            if($rst==1){
                echo "ERROR AL ACTUALIZAR";
            }else{
                echo "GUARDADO CORRECTAMENTE";
            }
            break;
        case "VENCER" :
            if(ob_get_length()) ob_clean();
            $rst=$objMarca->ejecutarSQL("UPDATE vale SET estado = 'V'  WHERE idvale = ".$_POST["txtId"]);
            if($rst==1){
                echo "ERROR AL ACTUALIZAR";
            }else{
                echo "GUARDADO CORRECTAMENTE";
            }
            break;
	case "ELIMINAR" :
            if(ob_get_length()) ob_clean();
            $rst=$objMarca->ejecutarSQL("UPDATE vale SET estado = 'A'  WHERE idvale = ".$_POST["txtId"]);
            if($rst==1){
                echo "ERROR AL ACTUALIZAR";
            }else{
                echo "GUARDADO CORRECTAMENTE";
            }
            break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>