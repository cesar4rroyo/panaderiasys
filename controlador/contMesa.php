<?php
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
$objMesa = new clsMesa($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//echo umill($objMesa->insertarMesa($_POST["txtNumero"], $_POST["cboIdSalon"],$_POST['txtIdSucursal'], $_POST["txtNroPersonas"], $_POST["txtComentario"], $_POST["txtImagen"]));
		$existe=$objMesa->verificaExisteNumero($_POST["txtNumero"]);
		if($existe==0){
		$rst=$objMesa->insertarMesa(strtoupper($_POST["txtNumero"]), strtoupper($_POST["cboIdSalon"]), $_POST['txtIdSucursal'], $_POST["txtNroPersonas"], $_POST["txtComentario"], $_POST["txtImagen"]);
		$dax = $rst->fetchObject();
		$idregistro = $dax->idmesa;
			//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Numero=>'.strtoupper($_POST["txtNumero"]).'; IdSalon=>'.$_POST["cboIdSalon"].'; NroPersonas=>'.$_POST["txtNroPersonas"].'; Comentario=>'.strtoupper($_POST["txtComentario"]), $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
			//FIN BITACORA
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Mesa","IdMesa",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objMesa->actualizarmesa($_POST["txtId"], $_POST["txtNumero"], $_POST["cboIdSalon"],$_POST['txtIdSucursal'], $_POST["txtNroPersonas"], $_POST["txtComentario"], $_POST["txtImagen"]);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Numero=> De: '. $dax->numero.' a: '.strtoupper($_POST["txtNumero"]).'; IdSalon=> De: '. $dax->idsalon. ' a: '.$_POST["cboIdSalon"].'; NroPersonas=> De: '. $dax->nropersonas. ' a: '.$_POST["txtNroPersonas"].'; Comentario=> De: '. $dax->comentario. ' a: '.$_POST["txtComentario"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objMesa->eliminarMesa($_POST["txtId"],$_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "LISTARPORSALON":
		$idmesa = $_POST["IdMesa"];
		$situacion = $_POST["situacion"];
		$ObjMesa = new clsMesa(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		$ObjMesa->reservarMesas($_SESSION['R_IdSucursal']);
		$consulta = $ObjMesa->consultarMesaxSalon2($_POST["IdSalon"],'%');
		$datos = array();
		if($consulta->rowCount()>0){
			$numMesas=$consulta->rowCount();
			$numCol=10;
			if($numMesas>=$numCol) {if($numMesas%$numCol==0){$limite=$numMesas/$numCol;}else{$limite=$numMesas/$numCol+1;} }else{ $limite=1;$numCol=$numMesas;}
			for($i=1;$i<=$limite;$i++){
                for($j=1;$j<=$numCol;$j++){
                    if($registro=$consulta->fetchObject()){
                    	if($registro->idmesa!=$idmesa && $situacion==$registro->situacion){
                        	$datos[] = array($registro->idmesa,$registro->nombrespersona);
                        }
                    }
                }
            }
        }
        echo json_encode($datos);
		exit();
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>