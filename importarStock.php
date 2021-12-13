<?php session_start();
require("modelo/clsProducto.php");
require("modelo/clsListaUnidad.php");
require("modelo/clsBitacora.php");
require("modelo/clsCategoria.php");
require("modelo/clsDetalleAlmacen.php");
require("modelo/clsStockProducto.php");
$objListaUnidad = new clsListaUnidad(5,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objCategoria = new clsCategoria(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objMovimiento = new clsDetalleAlmacen(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objStockProducto = new clsStockProducto(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objProducto = new clsProducto(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<!-- http://ProgramarEnPHP.wordpress.com -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>:: Importar de Excel a la Base de Datos ::</title>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/estilosistema.css" rel="stylesheet" type="text/css" />
<body>
<!-- FORMULARIO PARA SOLICITAR LA CARGA DEL EXCEL -->
<form name="importa" method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" >
<input type="hidden" value="upload" name="action" />
<BR />
<center>
    <div class="titulo">IMPORTAR DATOS DEL EXCEL</div>
</center>
<BR />
<table class="tablaint" width=100% >
<tr><td class="zoom2" align="center">Eliga Sucursal :</td>
<td>
    <?php 
    require_once('modelo/clsGeneral.php');
    $objPermiso = new clsGeneral(0, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    echo "<select name='cboSucursal' id='cboSucursal'><option>--SELECCIONE--</option>";
    $rst = $objPermiso->obtenerDataSQL("select idsucursal,razonsocial from sucursal where idempresa=".$_SESSION['R_IdEmpresa'],'Sucursal',$_SESSION['R_IdSucursal']);
    while($dato=$rst->fetchObject()){
        echo "<option value='$dato->idsucursal'>$dato->razonsocial</option>";
    }
    echo "</select>";
    ?></td>
</tr>
<tr>
   <th>Selecciona el archivo a importar:</th>

   <td><input type="file" name="excel" /></td>
</tr>
<tr>
   <th colspan="2"><input type='submit' name='enviar' value="Importar" />
   <input type='button' name='Cerrar' value="Cerrar" onclick=javascript:window.close(); /></th>
</tr>
</form>
<!-- CARGA LA MISMA PAGINA MANDANDO LA VARIABLE upload -->

<?php 
date_default_timezone_set('America/Lima');
global $cnx;
extract($_POST);

if ($action == "upload"){
//cargamos el archivo al servidor con el mismo nombre
//solo le agregue el sufijo bak_ 
	$archivo = $_FILES['excel']['name'];
	$tipo = $_FILES['excel']['type'];
	$destino = "bak_".$archivo;
	if (copy($_FILES['excel']['tmp_name'],$destino)) echo "<strong>Archivo Cargado Con Exito</strong>";
	else echo "<strong style='font-size: 12;' >Error Al Cargar el Archivo</strong><br />";
////////////////////////////////////////////////////////
if (file_exists ("bak_".$archivo)){ 
	/** Clases necesarias */
	require_once('Classes/PHPExcel.php');
	require_once('Classes/PHPExcel/Reader/Excel2007.php');

	// Cargando la hoja de c??lculo
	$objReader = new PHPExcel_Reader_Excel2007();
	$objPHPExcel = $objReader->load("bak_".$archivo);
	$objFecha = new PHPExcel_Shared_Date();       

	// Asignar hoja de excel activa
	$objPHPExcel->setActiveSheetIndex(0);

require_once ('Classes/PHPExcel/Cell/AdvancedValueBinder.php');
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
                  'cantidad'=>$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(),
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
	$res = $objMovimiento->insertarMovimiento(0, 3, $_POST["txtNumero"],7, '', date("Y-m-d"), '', '', 0, 0, 'S', 0, 0, 0, 0, 0, $_SESSION['R_IdUsuario'], 'P', 1, 1, $idmovimientoref, $idsucursalref, "Inventario de Prueba",'N',0,$_SESSION['R_IdSucursalUsuario'],1,1,"");
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
	        }elseif(trim($v["unidad"])=="LT"){
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
                
        		$res=$objStockProducto->insertar($_SESSION['R_IdSucursal'],$dat->idproducto,$dat->idsucursal,$dat->idunidadbase,$v['cantidad'],$dato->idmovimiento,'S',0,date("Y-m-d"),$_SESSION['R_IdUsuario'],$_SESSION['R_IdSucursalUsuario']);
        
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
	/////////////////////////////////////////////////////////////////////////

	echo "<strong><center>ARCHIVO IMPORTADO CON EXITO, EN TOTAL $campo REGISTROS Y $errores ERRORES</center></strong>";
	//una vez terminado el proceso borramos el 
	//archivo que esta en el servidor el bak_
	unlink($destino);		
}
//si por algo no cargo el archivo bak_ 
else{
	echo "<strong style='font-size: 16;'><center>Necesitas primero seleccionar el archivo!!!</strong></center><br />";}
}

?>
</body>
</html>