<?php
require("../modelo/clsCategoria.php");
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
$objCategoria = new clsCategoria($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		//echo umill($objCategoria->insertarCategoria($_POST['txtIdSucursal'],$_POST["txtDescripcion"], $_POST["txtAbreviatura"], $_POST["cboIdCategoriaRef"], 0, $_POST["txtImagen"], $_SESSION['R_IdSucursal']));
		$existe=$objCategoria->verificaExisteDescripcion($_POST["txtDescripcion"]);
		if($existe==0){
            $rst=$objCategoria->insertarCategoria($_POST['txtIdSucursal'],trim(strtoupper($_POST["txtDescripcion"])), trim(strtoupper($_POST["txtAbreviatura"])), $_POST["cboIdCategoriaRef"], 0, "iconos/3-33.png", $_POST["txtOrden"],($_POST["cboIdImpresora"]==""?0:0),"S","N");
            $dax = $rst->fetchObject();
            $idregistro = $dax->idcategoria;
			//INICIO BITACORA
			echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Descripcion=>'.trim(strtoupper($_POST["txtDescripcion"])).'; Abreviatura=>'.trim(strtoupper($_POST["txtAbreviatura"])).';IdCategoriaRef=>'.$_POST["cboIdCategoriaRef"].';CodigoOrden=>0;Imagen=>'.$_POST["txtImagen"].'; Orden=>'.$_POST["txtOrden"].';IdImpresora=>'.$_POST["cboIdImpresora"], $_SESSION['R_IdSucursal'], $idregistro ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		}else{//Ya exisiste, evio 1, en el javascript, muestro el mensaje
			echo "1";
		}
		break;
	case "ACTUALIZAR" :
		if(ob_get_length()) ob_clean();
		
		$rt = $objBitacora->consultarDatosAntiguos($_POST['txtIdSucursal'],"Categoria","IdCategoria",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		$objCategoria->actualizarCategoria($_POST["txtId"],$_POST['txtIdSucursal'], trim(strtoupper($_POST["txtDescripcion"])), trim(strtoupper($_POST["txtAbreviatura"])), $_POST["cboIdCategoriaRef"],0, "iconos/3-33.png",$_POST["txtOrden"],($_POST["cboIdImpresora"]==""?0:0),"S","N");
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Descripcion=> De: '. $dax->descripcion.' a: '.trim(strtoupper($_POST["txtDescripcion"])).'; Abreviatura=> De: '. $dax->abreviatura. ' a: '.trim(strtoupper($_POST["txtAbreviatura"])). '; IDCategoriaRef=> De: '. $dax->idcategoriaref. ' a: '.$_POST["cboIdCategoriaRef"]. '; CodigoOrden=> De: '. $dax->idcodigoorden. ' a: 0 ; Imagen=> De: '. $dax->imagen. ' a: '.strtoupper($_POST["txtImagen"]).'; Orden=> De: '.$dax->orden.' a: '.$_POST["txtOrden"].';IdImpresora=> De: '.$dax->idimpresora.' a: '.$_POST["cboIdImpresora"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	case "ELIMINAR" :
		if(ob_get_length()) ob_clean();
		$objCategoria->eliminarCategoria($_POST["txtId"],$_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
  	case "NUEVODETALLE" :
		if(ob_get_length()) ob_clean();
		$rst=$objCategoria->insertarDetalleCategoria(0,$_POST["txtIdCategoria"],$_POST['txtIdSucursal'],trim(strtoupper($_POST["txtDescripcion"])), trim(strtoupper($_POST["txtAbreviatura"])));
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Nuevo Registro', 'Descripcion=>'.trim(strtoupper($_POST["txtDescripcion"])).'; Abreviatura=>'.trim(strtoupper($_POST["txtAbreviatura"])).';IdCategoria=>'.$_POST["txtIdCategoria"], $_SESSION['R_IdSucursal'], 0 ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
 	case "ACTUALIZARDETALLE" :
		if(ob_get_length()) ob_clean();
		$rst=$objCategoria->insertarDetalleCategoria($_POST["txtId"],$_POST["txtIdCategoria"],$_POST['txtIdSucursal'],trim(strtoupper($_POST["txtDescripcion"])), trim(strtoupper($_POST["txtAbreviatura"])));
		//INICIO BITACORA
		echo umill($objBitacora->insertarBitacora($_SESSION['R_NombreUsuario'], $_SESSION['R_Perfil'], $clase, 'Actualizar Registro', 'Descripcion=>'.trim(strtoupper($_POST["txtDescripcion"])).'; Abreviatura=>'.trim(strtoupper($_POST["txtAbreviatura"])).';IdCategoria=>'.$_POST["txtIdCategoria"], $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
  	case "ELIMINARDETALLE" :
		if(ob_get_length()) ob_clean();
		$objCategoria->eliminarDetalleCategoria($_POST["txtId"],$_POST['txtIdSucursal']);
		
		echo umill($objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], $clase, 'Eliminar Registro', 'Estado=> De: N a: A', $_SESSION['R_IdSucursal'], $_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']));
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>