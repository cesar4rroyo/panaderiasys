<?php session_start();
require("modelo/clsMovimiento.php");
$objMovimiento = new clsMovimiento(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

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
    	$i=3; //celda inicial en la cual empezara a realizar el barrido de la grilla de excel
    	$param=0;
    	$contador=0;
    	while($param==0) //mientras el parametro siga en 0 (iniciado antes) que quiere decir que no ha encontrado un NULL entonces siga metiendo datos
    	{
    	   	if($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue()!=NULL){
                $valor_fecha=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $timestamp=PHPExcel_Shared_Date::ExcelToPHP($valor_fecha);
                $fecha=date("d-m-Y",$timestamp);
                $valor_fecha=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                $timestamp=PHPExcel_Shared_Date::ExcelToPHP($valor_fecha);
                $fecha2=date("d-m-Y",$timestamp);
                
    
                $datos[($i)] = array('fechaemision'=> $fecha,'fecharegistro' => $fecha2,
                'numero' => trim($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue()),
                'tipodocumento' => trim($objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue()),
                'razon'=>$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(),
                'ruc'=>$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue(),
                'subtotal'=>$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue(),
                'igv'=>$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue(),
                'total'=>$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue()
                );
    		}
                 
    		if($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue()==NULL) //pregunto que si ha encontrado un valor null en una columna inicie un parametro en 1 que indicaria el fin del ciclo while
    		{
    			$param=1; //para detener el ciclo cuando haya encontrado un valor NULL
    		}
            $i++;
    		$contador=$contador+1;
    	}
    	$errores=0;
        print_r("datos:".$datos);
    	//recorremos el arreglo multidimensional 
    	//para ir recuperando los datos obtenidos
    	//del excel e ir insertandolos en la BD
        $campo=0;
    	foreach($datos as $k => $v){
            $objMovimiento->iniciarTransaccion();
            if(ob_get_length()) ob_clean();
            if($v["tipodocumento"]=="BOLETA"){
                $idtipodoc="1";
            }elseif($v["tipodocumento"]=="FACTURA"){
                $idtipodoc="2";
            }elseif($v["tipodocumento"]=="TICKET O CINTA"){
                $idtipodoc="3";
            }else{
                $idtipodoc="15";
            }
            $idsucursalref=NULL;$idmovimientoref=NULL;
            $rs=$objMovimiento->obtenerDataSQL("select p.* from personamaestro pm inner join persona p on p.idpersonamaestro=pm.idpersonamaestro where p.estado='N' and pm.nrodoc like '".$v["ruc"]."'");
            if($rs->rowCount()>0){
                $idpersona=$rs->fetchObject()->idpersona;                    
            }else{
                echo "No persona";
                print_r($v);
            }
    	    $rst=$objMovimiento->obtenerDataSQL("select T.* from (select * from movimientohoy union all select * from movimiento) T where T.idpersona=".$idpersona." and idtipodocumento=".$idtipodoc." and numero like '".$v["numero"]."' and idtipomovimiento=1");
    	    if($rst->rowCount()==0){
    	    	$res = $objMovimiento->insertarMovimiento(0, 1, $v["numero"], $idtipodoc, "A", $v["fechaemision"], '', '', 0, 0, 'S', 0, $v["subtotal"], $v["igv"], $v["total"], $v["total"], $_SESSION['R_IdUsuario'], 'P', $idpersona, $_SESSION["R_IdPersona"], $idmovimientoref, $idsucursalref, "Migracion",'C',0,$_SESSION['R_IdSucursalUsuario'],$_SESSION["R_IdSucursalUsuario"],$_SESSION["R_IdSucursalUsuario"]);
        		$dato=$res->fetchObject();
                if(is_string($res)){
        			$objMovimiento->abortarTransaccion(); 
        			if(ob_get_length()) ob_clean();
        			echo "Error de Proceso en Lotes1: ".$objMovimiento->gMsg;
        			break 3;
        		}
    		}
            $objMovimiento->finalizarTransaccion(); 
            // echo $sql;
    	}	
    	/////////////////////////////////////////////////////////////////////////
    
    	echo "<strong><center>ARCHIVO IMPORTADO CON EXITO, EN TOTAL $campo REGISTROS Y $errores ERRORES</center></strong>";
    	//una vez terminado el proceso borramos el 
    	//archivo que esta en el servidor el bak_
    	unlink($destino);		
    }//si por algo no cargo el archivo bak_ 
    else{
	   echo "<strong style='font-size: 16;'><center>Necesitas primero seleccionar el archivo!!!</strong></center><br />";
    }
}

?>
</body>
</html>