<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('America/Lima');
require("../modelo/clsDetalleAlmacen.php");
require("../modelo/clsStockProducto.php");
require("../modelo/clsProducto.php");
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
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objProducto = new clsProducto($clase,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
switch($accion){
	case "NUEVO" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objStockProducto->iniciarTransaccion();

		$idsucursalref=$_POST["cboBarra"];$idmovimientoref=NULL;
        
		$_POST["txtNumero"]=str_pad(trim($_POST["txtNumero"]),6,"0",STR_PAD_LEFT);
		$datosR=explode('-',$_POST["cboIdResponsable"]);
		$_POST["txtTotal"]=0;
		$res = $objMovimiento->insertarMovimiento(0, 3, $_POST["txtNumero"], $_POST['cboIdTipoDocumento'], '', 'LOCALTIMESTAMP', '', '', 0, 0, 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $datosR[1], $idmovimientoref, $idsucursalref, $_POST["txtComentario"],'N',0,$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$datosR[0],$_POST["txtNombresPersona"]);
		$dato=$res->fetchObject();
		
		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>3; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST['cboIdTipoDocumento'].'; formapago=>; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>'.$_POST["txtNroPersonas"].'; idmesa=>'.$_POST["cboMesa"].'; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotal"].'; igv=>0; total=>'.$_POST["txtTotal"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$datosR[1].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$datosR[0].'; nombrespersona=>'.$_POST["txtNombresPersona"], $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($res)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}
		
        if($_POST["txtIdSucursalDestino"]!="0" && $_POST["cboSucursal"]!="0" && $_POST["cboSucursal"]!=""){
            $numero = $objMovimiento->generaNumeroSinSerie(3,7,substr($_SESSION["R_FechaProceso"],3,2),$_POST["cboSucursal"]);
            //Situacion E->Enviada;
            if($_POST["cboBarra"]=="1"){
            	$almacen="PRINCIPAL";
            }elseif($_POST["cboBarra"]=="2"){
            	$almacen="ESCENARIO";
            }elseif($_POST["cboBarra"]=="3"){
            	$almacen="2DO PISO";
            }else{
            	$almacen="ALMACEN";
            }
            $res = $objMovimiento->insertarMovimiento(0, 3, $numero, 7, '',  'LOCALTIMESTAMP', '', '', 0, 0, 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', $_POST["txtIdPersona"], $datosR[1], $dato->idmovimiento, $_SESSION['R_IdSucursalUsuario'], "Envio desde $almacen del ".$_POST["txtFecha"],'E',0,$_SESSION['R_IdSucursalUsuario'],$_POST["txtIdSucursalPersona"],$datosR[0],$_POST["txtNombresPersona"],'','','',$_POST["cboSucursal"]);
            $dato2=$res->fetchObject();
            if(is_string($res)){
			     $objMovimiento->abortarTransaccion(); 
			     $objBitacora->abortarTransaccion();
			     $objStockProducto->abortarTransaccion();
			     if(ob_get_length()) ob_clean();
			     echo "Error de Proceso en Lotes1.1: ".$objGeneral->gMsg;
			     exit();
            }
        }
        $_POST["lista"]=substr($_POST["lista"],0,strlen($_POST["lista"])-1);
        $lista = explode("@",$_POST["lista"]);
        $total=0;
		for($c=0;$c<count($lista);$c++){
		    $dat = explode("-",$lista[$c]);
		    $listaunidad = $objMovimiento->obtenerDataSQL("select * from listaunidad where idunidad=idunidadbase and idproducto=".$dat[0]." and idsucursal=".$dat[1]." and idsucursalproducto=".$dat[1])->fetchObject();
		    //$precioventa = $listaunidad->precioventa;
		    //$preciocompra = $listaunidad->preciocompra+0;
		    $precioventa = $_POST["txtPrecioVenta".$lista[$c]];
		    $preciocompra = $_POST["txtPrecioCompra".$lista[$c]];
		    $idunidad = $listaunidad->idunidad; 
		    $idproducto=$dat[0];
		    $idsucursalproducto=$dat[1];
		    if(($idproducto=="69" || $idproducto=="98" || $idproducto=="104" || $idproducto=="101") && $idsucursalproducto=="1"){
				$cantidad=$_POST["txtProducto".$lista[$c]]/$precioventa;
			}else{
				$cantidad=$_POST["txtProducto".$lista[$c]];
			}
			$total = $precioventa*$cantidad+$total;

			$res = $objMovimiento->insertarDetalleAlmacen($dato->idmovimiento,$dat[0],$idunidad,$cantidad,$preciocompra,$precioventa,$dat[1]);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$dat[0].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$_POST["txtProducto".$lista[$c]].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$dat[1], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!="Guardado correctamente"){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				exit();
			}
			if($_POST["txtIdSucursalDestino"]!=0 && $_POST["cboSucursal"]!=0 && $_POST["cboIdTipoDocumento"]=="8"){
                $res = $objMovimiento->insertarDetalleAlmacen($dato2->idmovimiento,$dat[0],$idunidad,$cantidad,$preciocompra,$precioventa,$dat[1],$_POST["cboSucursal"]);
                //INICIO BITACORA
			    $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$idunidad.'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_POST["cboSucursal"].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_POST["cboSucursal"], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    			//FIN BITACORA
	       		if($res==1){
		      		$objMovimiento->abortarTransaccion(); 
			     	$objBitacora->abortarTransaccion();
				    $objStockProducto->abortarTransaccion();
				    if(ob_get_length()) ob_clean();
				    echo "Error de Proceso en Lotes2.1: ".$objGeneral->gMsg;
				    exit();
			     }                
            }
            
			if($_POST['cboIdTipoDocumento']==7){//INGRESO
				$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dat[0],$dat[1],$idunidad,$cantidad,$dato->idmovimiento,'S',$precioventa,$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			}else{//SALIDA
				$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dat[0],$dat[1],$idunidad,-$cantidad,$dato->idmovimiento,'S',$precioventa,$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			}
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

		$objMovimiento->ejecutarSQL("update movimientohoy set motivo='".$_POST["cboMotivo"]."',total=$total,subtotal=$total where idmovimiento=".$dato->idmovimiento." and idsucursal=".$_SESSION["R_IdSucursal"]);
        /*
		if(!isset($_SESSION['R_carroAlmacen']) or $_SESSION['R_carroAlmacen']==''){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
			exit();
		}		
		
		foreach($_SESSION['R_carroAlmacen'] as $v){
			$res = $objMovimiento->insertarDetalleAlmacen($dato->idmovimiento,$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto']);
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
			
			if($_POST["txtIdSucursalDestino"]!="0" && $_POST["cboSucursal"]!="0"){
                $res = $objMovimiento->insertarDetalleAlmacen($dato2->idmovimiento,$v["idproducto"],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa'],$v['idsucursalproducto'],$_POST["cboSucursal"]);
                //INICIO BITACORA
			    $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_POST["cboSucursal"].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_POST["cboSucursal"], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
    			//FIN BITACORA
	       		if($res==1){
		      		$objMovimiento->abortarTransaccion(); 
			     	$objBitacora->abortarTransaccion();
				    $objStockProducto->abortarTransaccion();
				    if(ob_get_length()) ob_clean();
				    echo "Error de Proceso en Lotes2.1: ".$objGeneral->gMsg;
				    exit();
			     }                
			     $res=$objStockProducto->insertar($_POST["cboSucursal"],$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$dato2->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
            }
            $idsucursal=$_SESSION["R_IdSucursal"];
			if($_POST['cboIdTipoDocumento']==7){//INGRESO
				//$idsucursal=$_POST["cboBarra"];
				$res=$objStockProducto->insertar($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],$v['cantidad'],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			}else{//SALIDA
				//$idsucursal=$_POST["cboBarra"];
				$res=$objStockProducto->insertar($idsucursal,$v["idproducto"],$v["idsucursalproducto"],$v['idunidad'],-$v['cantidad'],$dato->idmovimiento,'S',$v["preciocompra"],$_POST["txtFecha"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			}
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
		*/

		if($res=="0" || trim($res)=='Guardado correctamente'){
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			$objStockProducto->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;
	case "CABIASITUACION" :
		echo 'pendiente'; exit();
		if(ob_get_length()) ob_clean();
		echo umill($objMovimiento->cambiarSituacionAntendido($_POST['txtId'],'A'));
		//INICIO BITACORA
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Actualizar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		break;
	case "ACTUALIZAR" :
		echo 'pendiente'; exit();
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$rt = $objBitacora->consultarDatosAntiguos($_SESSION['R_IdSucursal'],"Movimiento","IdMovimiento",$_POST["txtId"]);
		$dax = $rt->fetchObject();
		
		//VERIFICO SI CAMBIO DE MESA
		if($dax->idmesa<>$_POST["cboMesa"]){
			//CAMBIO LA SITUACION DE LA MESA ANTERIOR A NORMAL
			$res = $objMesa->cambiarSituacion($dax->idmesa,$_SESSION['R_IdSucursal'],'N');
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$dax->idmesa.'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>N', $_SESSION['R_IdSucursal'],$dax->idmesa ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				exit();
			}
			//CAMBIO LA SITUACION DE LA MESA NUEVA A OCUPADA
			if($_POST["cboMesa"]>1){
				$res = $objMesa->cambiarSituacion($_POST["cboMesa"],$_SESSION['R_IdSucursal'],'O');
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 17, 'Actualizar Registro', 'idmesa=>'.$_POST["cboMesa"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; situacion=>O', $_SESSION['R_IdSucursal'],$_POST["cboMesa"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
				//FIN BITACORA
				if($res==1){
					$objMovimiento->abortarTransaccion(); 
					$objBitacora->abortarTransaccion();
					if(ob_get_length()) ob_clean();
					echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
					exit();
				}
			}
		}
		
		$datosR=explode('-',$_POST["cboIdResponsable"]);
		$res = $objMovimiento->actualizarMovimiento($_POST["txtId"],0, 5, $_POST["txtNumero"], 11, '', 'LOCALTIMESTAMP', '', '', $_POST["txtNroPersonas"], $_POST["cboMesa"], 'S', 0, $_POST["txtTotal"], 0, $_POST["txtTotal"], 0, $_SESSION['R_IdUsuario'], 'P', 0, $datosR[1], NULL, NULL, $_POST["txtComentario"],'O',0,$_SESSION['R_IdSucursalUsuario'],0,$datosR[0],$_POST["txtNombresPersona"]);

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

		$res = $objMovimiento->eliminarDetalleAlmacen($_POST["txtId"]);
		//eliminaar detalle almacen
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			exit();
		}

		if(!isset($_SESSION['R_carroAlmacen']) or $_SESSION['R_carroAlmacen']==''){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes: Las variables de sesión se perdieron";
			exit();
		}
			
		foreach($_SESSION['R_carroAlmacen'] as $v){
			$res = $objMovimiento->insertarDetalleAlmacen($_POST["txtId"],$v['idproducto'],$v['idunidad'],$v['cantidad'],$v['preciocompra'],$v['precioventa']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res==1){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes3: ".$objGeneral->gMsg;
				exit();
			}

		}
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
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
        $objStockProducto->iniciarTransaccion();  
		$res = $objMovimiento->eliminarMovimiento($_POST['txtId']);
		//INICIO BITACORA
		//eliminar movimiento
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'].' ; estado=>A', $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objStockProducto->abortarTransaccion();
            $objBitacora->abortarTransaccion(); 
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}
        $res = $objStockProducto->revertir($_SESSION["R_IdSucursal"],$_POST["txtId"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario'],"A");
		//INICIO BITACORA
		//eliminaar detalle almacen
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Eliminar Registro', 'idmovimiento=>'.$_POST["txtId"].'; idsucursal=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'],$_POST["txtId"] ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if($res==1){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion(); 
            $objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
			exit();
		}
        //$res = $objMovimiento->eliminarDetalleAlmacen($_POST["txtId"]);
		//if($res==0){
		if($res=="Guardado correctamente"){
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion(); 
            $objStockProducto->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;

    case "HISTORIALELIMINADOS":
        $rst = $objMovimiento->obtenerDataSQL("SELECT dma.cantidad,dma.comentario,dma.fecha,us.nombreusuario,p.descripcion FROM detallemovalmacen_eliminado dma LEFT JOIN producto p ON p.idproducto=dma.idproducto LEFT JOIN usuario us ON us.idusuario = dma.idusuario WHERE dma.idmovimiento = ".$_POST["idmovimiento"]." AND dma.idsucursal = ".$_SESSION["R_IdSucursal"]);
        $datos = array();
        while($reg=$rst->fetchObject()){
            $datos[] = array($reg->cantidad,$reg->descripcion,  substr($reg->fecha, 0, 16),$reg->nombreusuario,$reg->comentario);
        }
        echo json_encode($datos);
        break;
        
    case "ACEPTARENVIO" :
        if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objStockProducto->iniciarTransaccion();
        
        $res2 = $objMovimiento->consultarDetalleAlmacen(20,1,1,1,0,'',$_POST["idalmacen"]);
        if(is_string($res2)){
		     $objMovimiento->abortarTransaccion(); 
		     $objBitacora->abortarTransaccion();
		     $objStockProducto->abortarTransaccion();
		     if(ob_get_length()) ob_clean();
		     echo "Error de Proceso en Lotes1.1: ".$objMovimiento->gMsg;
		     exit();
        }
        
        /*while($dato=$res2->fetchObject()){
            $res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dato->idproducto,$_SESSION['R_IdSucursal'],$dato->idunidad,$dato->cantidad,$dato->idmovimiento,'S',$dato->preciocompra,date("d/m/Y"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$dato->idproducto.'; idunidad=>'.$dato->idunidad.'; cantidad=>'.$dato->cantidad.'; preciocompra=>'.$dato->preciocompra.'; precioventa=>'.$dato->precioventa.'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if($res!='Guardado correctamente'){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				break 3;
			}
		}*/
        $objMovimiento->actualizarComentarioMovimiento($_POST["idalmacen"],"Entregado pedido el ".date("d/m/Y"));
        //Situacion R->Recibido;
        $objMovimiento->cambiarSituacion($_POST["idalmacen"],'R');
        
		if($res==0){
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			$objStockProducto->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
        
        break;   

    case "ACEPTARENVIO2" :
        if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objStockProducto->iniciarTransaccion();
        
        $res2 = $objMovimiento->consultarDetalleAlmacen(20,1,1,1,0,'',$_POST["txtId"]);
        if(is_string($res2)){
		     $objMovimiento->abortarTransaccion(); 
		     $objBitacora->abortarTransaccion();
		     $objStockProducto->abortarTransaccion();
		     if(ob_get_length()) ob_clean();
		     echo "Error de Proceso en Lotes1.1: ".$objMovimiento->gMsg;
		     exit();
        }
        
        while($dato=$res2->fetchObject()){
            $res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dato->idproducto,$_SESSION['R_IdSucursal'],$dato->idunidad,$dato->cantidad,$dato->idmovimiento,'S',$dato->preciocompra,date("d/m/Y"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$dato->idproducto.'; idunidad=>'.$dato->idunidad.'; cantidad=>'.$dato->cantidad.'; preciocompra=>'.$dato->preciocompra.'; precioventa=>'.$dato->precioventa.'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
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
        $objMovimiento->actualizarComentarioMovimiento($_POST["idalmacen"],"Entregado pedido el ".date("d/m/Y"));
        //Situacion R->Recibido;
        $objMovimiento->cambiarSituacion($_POST["idalmacen"],'R');
        
		if($res==0){
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			$objStockProducto->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
        
        break;   

    case "CARGARINICIAL" :
        if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objStockProducto->iniciarTransaccion();
        
        $rs=$objMovimiento->obtenerDataSQL("select * from movimientohoy where idtipomovimiento=3 and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='2018-10-21 00:00:00'");print_r($rs);
        while($dat=$rs->fetchObject()){
        	$res2 = $objMovimiento->consultarDetalleAlmacen(20,1,1,1,0,'',$dat->idmovimiento);
	        if(is_string($res2)){
			     $objMovimiento->abortarTransaccion(); 
			     $objBitacora->abortarTransaccion();
			     $objStockProducto->abortarTransaccion();
			     if(ob_get_length()) ob_clean();
			     echo "Error de Proceso en Lotes1.1: ".$objMovimiento->gMsg;
			     exit();
	        }
	        
	        while($dato=$res2->fetchObject()){
	            $res=$objStockProducto->insertar($dat->idsucursalref,$dato->idproducto,$_SESSION['R_IdSucursal'],$dato->idunidad,$dato->cantidad,$dato->idmovimiento,'S',$dato->preciocompra,date("d/m/Y"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);print_r($res);
				//INICIO BITACORA
				$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$dato->idproducto.'; idunidad=>'.$dato->idunidad.'; cantidad=>'.$dato->cantidad.'; preciocompra=>'.$dato->preciocompra.'; precioventa=>'.$dato->precioventa.'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
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
		}
        $objMovimiento->finalizarTransaccion(); 
		$objBitacora->finalizarTransaccion();
		$objStockProducto->finalizarTransaccion();
		if(ob_get_length()) ob_clean();
		echo "Guardado correctamente";
        break;   
    case "STOCKRAPIDO" :
		if(ob_get_length()) ob_clean();
		$objMovimiento->iniciarTransaccion();
		$objBitacora->iniciarTransaccion();
		$objStockProducto->iniciarTransaccion();

		$idsucursalref=NULL;$idmovimientoref=NULL;
        
		$_POST["txtNumero"]=str_pad(trim($_POST["txtNumero"]),6,"0",STR_PAD_LEFT);
		$datosR=explode('-',$_SESSION['R_IdSucursalUsuario'].'-'.$_SESSION['R_IdPersona']);
		$res = $objMovimiento->insertarMovimiento(0, 3, $_POST["txtNumero"], 7, '', $_SESSION["R_FechaProceso"], '', '', 0, 0, 'S', 0, "0.00", 0, "0.00", 0, $_SESSION['R_IdUsuario'], 'P', 2, $datosR[1], $idmovimientoref, $idsucursalref, "Registro Rapido de Stock",'N',0,$_SESSION['R_IdSucursalUsuario'],$_SESSION['R_IdSucursal'],$datosR[0],"VARIOS");
		$dato=$res->fetchObject();
		//INICIO BITACORA
		date_default_timezone_set('America/Lima');
		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>3; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>7; formapago=>; fecha=>'.$_SESSION["R_FechaProceso"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>'.$_POST["txtNroPersonas"].'; idmesa=>'.$_POST["cboMesa"].'; moneda=>S; inicial=>0; subtotal=>0.00; igv=>0; total=>0.00; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>2; idresponsable=>'.$datosR[1].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$datosR[0].'; nombrespersona=>VARIOS', $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		//FIN BITACORA
		if(is_string($res)){
			$objMovimiento->abortarTransaccion(); 
			$objBitacora->abortarTransaccion();
			$objStockProducto->abortarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
			exit();
		}	
        $filtro_str = $_POST["filtro"];
        $filtro = str_replace("\'", "'", $filtro_str);
		eval("\$rst = \$objProducto->consultarProductoInterna(1000000,1,".$filtro.");");
		while($v=$rst->fetchObject()){
		      if($_POST["txtProducto".$v->idproducto]>0){
			     $res = $objMovimiento->insertarDetalleAlmacen($dato->idmovimiento,$v->idproducto,$v->idunidadbase,$_POST["txtProducto".$v->idproducto],$v->preciocompra,$v->precioventa,$_SESSION['R_IdSucursal']);
			     //INICIO BITACORA
			     $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v->idproducto.'; idunidad=>'.$v->idunidadbase.'; cantidad=>'.$_POST["txtProducto".$v->IdProducto].'; preciocompra=>'.$v->preciocompra.'; precioventa=>'.$v->precioventa.'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			     //FIN BITACORA
			     if($res==1 && $res!="Guardado correctamente"){
                    $objMovimiento->abortarTransaccion(); 
				    $objBitacora->abortarTransaccion();
				    $objStockProducto->abortarTransaccion();
				    if(ob_get_length()) ob_clean();
				    echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
				    exit();
			     }
            
			     //INGRESO
				$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$v->idproducto,$_SESSION['R_IdSucursal'],$v->idunidadbase,$_POST["txtProducto".$v->idproducto],$dato->idmovimiento,'S',$v->preciocompra,$_SESSION["R_FechaProceso"],$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			     //INICIO BITACORA
			     $objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v->idproducto.'; idunidad=>'.$v->idunidad.'; cantidad=>'.$_POST["txtProducto".$v->idproducto].'; preciocompra=>'.$v->preciocompra.'; precioventa=>'.$v->precioventa.'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$_SESSION['R_IdSucursal'], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
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
		}
		if($res==0 || $res="Guardado correctamente"){
			$objMovimiento->finalizarTransaccion(); 
			$objBitacora->finalizarTransaccion();
			$objStockProducto->finalizarTransaccion();
			if(ob_get_length()) ob_clean();
			echo "Guardado correctamente";
		}
		break;     
	case "SUBIRREQUERIMIENTO":
		$nombre_original=$_FILES['file']['name'];
		$tipo=$_FILES['file']['type'];
		$tam=$_FILES['file']['size'];
		$temporal=$_FILES['file']['tmp_name'];
		$rutafoto="";
		$rutafotodb="";
		if(!empty($nombre_original)){
			$destino = "bak_".$nombre_original;
			if (copy($_FILES['file']['tmp_name'],$destino)){

			}else{
				echo "Error copiando archivo";
			}
			require_once('../Classes/PHPExcel.php');
			require_once('../Classes/PHPExcel/Reader/Excel2007.php');
			// Cargando la hoja de c??lculo
			$objReader = new PHPExcel_Reader_Excel2007();
			$objPHPExcel = $objReader->load("bak_".$nombre_original);
			$objFecha = new PHPExcel_Shared_Date();       

			// Asignar hoja de excel activa
			$objPHPExcel->setActiveSheetIndex(0);

			require_once ('../Classes/PHPExcel/Cell/AdvancedValueBinder.php');
			PHPExcel_Calculation::getInstance()->setCalculationCacheEnabled(False);

		        // Llenamos el arreglo con los datos  del archivo xlsx
			$i=2; //celda inicial en la cual empezara a realizar el barrido de la grilla de excel
			$param=0;
			$contador=0;
			while($param==0) //mientras el parametro siga en 0 (iniciado antes) que quiere decir que no ha encontrado un NULL entonces siga metiendo datos
			{
			   	if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()!=NULL){
		              if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()!=NULL){
		                  $kardex = "S";
				          $datos[($i)] = array('plato'=> trim($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()),
		                  'cantidad'=>$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),
		                  'unidad'=>$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue());
		              }
				}
		             
				if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()==NULL) //pregunto que si ha encontrado un valor null en una columna inicie un parametro en 1 que indicaria el fin del ciclo while
				{
					$param=1; //para detener el ciclo cuando haya encontrado un valor NULL
				}
		        //if($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()=="CATEGORIA")
		            $i++;
		       /* else
		            $i=$i+2;
		              */
				$contador=$contador+1;
			}
			$errores=0;
		    print_r("datos:".$datos);
			//recorremos el arreglo multidimensional 
			//para ir recuperando los datos obtenidos
			//del excel e ir insertandolos en la BD
		    $campo=0;
		    if(ob_get_length()) ob_clean();
			$objMovimiento->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
			$objStockProducto->iniciarTransaccion();

			$idsucursalref=NULL;$idmovimientoref=NULL;
		    date_default_timezone_set('America/Lima');
			$_POST["txtNumero"]=str_pad(trim("000001"),6,"0",STR_PAD_LEFT);
			$res = $objMovimiento->insertarMovimiento(0, 3, $_POST["txtNumero"],8, '', date("Y-m-d"), '', '', 0, 0, 'S', 0, 0, 0, 0, 0, $_SESSION['R_IdUsuario'], 'P', 1, 1, $idmovimientoref, $idsucursalref, "Salida de productos",'N',0,$_SESSION['R_IdSucursalUsuario'],1,1,"");
			$dato=$res->fetchObject();
			//INICIO BITACORA
			$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 10, 'Nuevo Registro', 'idconceptopago=>0; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idtipomovimiento=>3; numero=>'.$_POST["txtNumero"].'; idtipodocumento=>'.$_POST['cboIdTipoDocumento'].'; formapago=>; fecha=>'.$_POST["txtFecha"].'; fechaproximacancelacion=>; fechaultimopago=>; nropersonas=>'.$_POST["txtNroPersonas"].'; idmesa=>'.$_POST["cboMesa"].'; moneda=>S; inicial=>0; subtotal=>'.$_POST["txtTotal"].'; igv=>0; total=>'.$_POST["txtTotal"].'; totalpagado=>0; idusuario=>'.$_SESSION['R_IdUsuario'].'; tipopersona=>P; idpersona=>'.$_POST["txtIdPersona"].'; idresponsable=>'.$datosR[1].'; idmovimientoref=>; idsucursalref=>; comentario=>'.$_POST["txtComentario"].'; situacion=>O; estado=>N; idcaja=>0; idsucursalusuario=>'.$_SESSION['R_IdSucursalUsuario'].'; idsucursalpersona=>0; idsucursalresponsable=>'.$datosR[0].'; nombrespersona=>'.$_POST["txtNombresPersona"], $_SESSION['R_IdSucursal'], $dato->idmovimiento ,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
			//FIN BITACORA
			if(is_string($res)){
				$objMovimiento->abortarTransaccion(); 
				$objBitacora->abortarTransaccion();
				$objStockProducto->abortarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Error de Proceso en Lotes1: ".$objGeneral->gMsg;
				exit();
			}

			foreach($datos as $k => $v){
		        if(($v["cantidad"]+0)>0){
		        	if(trim($v["unidad"])=="KG"){
			        	$idunidad=3;
			        }elseif(trim($v["unidad"])=="L" || trim($v["unidad"])=="LT"){
			        	$idunidad=10;
			        }elseif(trim($v["unidad"])=="PAQ"){
			        	$idunidad=5;
			        }else{
			        	$idunidad=1;
			        }
		    	    $rs=$objProducto->obtenerDataSQL("select * from producto where descripcion like '".($v["plato"])."' and estado='N' and idunidadbase=".$idunidad." and idsucursal=".$_SESSION["R_IdSucursal"]);
		            if($rs->rowCount()>0){
		                $dat=$rs->fetchObject();
		        		$res = $objMovimiento->insertarDetalleAlmacen($dato->idmovimiento,$dat->idproducto,$dat->idunidadbase,$v['cantidad'],0,0,$dat->idsucursal);
		        		//INICIO BITACORA
		        		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 3, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$idunidad.'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		        		//FIN BITACORA
		        		if($res==1){
		        			$objMovimiento->abortarTransaccion(); 
		        			$objBitacora->abortarTransaccion();
		        			$objStockProducto->abortarTransaccion();
		        			if(ob_get_length()) ob_clean();
		        			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
		        			exit();
		        		}
		                
		        		$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dat->idproducto,$dat->idsucursal,$dat->idunidadbase,(-1)*$v['cantidad'],$dato->idmovimiento,'S',0,date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		        
		        		//INICIO BITACORA
		        		$objBitacora->insertarBitacora($_SESSION["R_NombreUsuario"], $_SESSION['R_Perfil'], 2, 'Nuevo Registro', 'idmovimiento=>'.$dato->idmovimiento.'; idproducto=>'.$v['idproducto'].'; idunidad=>'.$v['idunidad'].'; cantidad=>'.$v['cantidad'].'; preciocompra=>'.$v['preciocompra'].'; precioventa=>'.$v['precioventa'].'; estado=>N; idsucursal=>'.$_SESSION['R_IdSucursal'].'; idsucursalproducto=>'.$v["idsucursalproducto"], $_SESSION['R_IdSucursal'], 0,$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
		        		//FIN BITACORA
		        		if($res!='Guardado correctamente'){
		        			$objMovimiento->abortarTransaccion(); 
		        			$objBitacora->abortarTransaccion();
		        			$objStockProducto->abortarTransaccion();
		        			if(ob_get_length()) ob_clean();
		        			echo "Error de Proceso en Lotes2: ".$objStockProducto->gMsg;
		        			exit();
		        		}
		            }else{
		                echo "<br />No existe el producto ->".print_r($v);
		            }
		        }
			}	
			if($res==0){
				$objMovimiento->finalizarTransaccion(); 
				$objBitacora->finalizarTransaccion();
				$objStockProducto->finalizarTransaccion();
				if(ob_get_length()) ob_clean();
				echo "Guardado correctamente";
			}
		}
		break;
	default:
		echo "Error en el Servidor: Operacion no Implementada.";
		exit();
}
?>