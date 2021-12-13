<?php session_start();
require("modelo/clsRolPersona.php");
require("modelo/clsPersona.php");
require("modelo/clsBitacora.php");
$objBitacora = new clsBitacora(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objPersona = new clsPersona(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objRolPersona = new clsRolPersona(19,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

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
    	   	if($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue()!=NULL){
                $datos[($i)] = array('razon'=> ($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue()),
                'cliente'=>$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),
                'proveedor'=>$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(),
                'empleado'=>$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(),
                'ruc'=>$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue(),
                'direccion'=>$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue());
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
  			$objPersona->iniciarTransaccion();
			$objBitacora->iniciarTransaccion();
            $objRolPersona->iniciarTransaccion();

            if(ob_get_length()) ob_clean();
    		$rst = $objPersona->insertarPersonaMaestroOut(strtoupper(trim($v["razon"])), strtoupper(trim("")), "JURIDICA", trim($v["ruc"]), "M", date("Y-m-d"));
    		$dato=$rst->fetchObject();
            if($v["empleado"]=="True"){
                $idrol1=1;
            }else{
                $idrol1=0;
            }
            if($v["cliente"]=="True"){
                $idrol2=3;
            }else{
                $idrol2=0;
            }
            if($v["proveedor"]=="True"){
                $idrol3=4;
            }else{
                $idrol3=0;
            }
            if($idrol1>0){
                $idrol=$idrol1;
            }elseif($idrol2>0){
                $idrol=$idrol2;
            }elseif($idrol3>0){
                $idrol=$idrol3;
            }else{
                $idrol=1;
            }
    		$res = $objPersona->insertarPersonaOut($_POST["cboSucursal"], $dato->idpersonamaestro,1349,$v["direccion"],"","","","",$idrol,"N");
    		$dax = $res->fetchObject();
    		$idregistro = $dax->idpersona;

            /*if($idrol1>0 && $idrol!=$idrol1){
                ($objRolPersona->insertarRolPersona($_POST['cboSucursal'],$idregistro, $idrol1));
            }
            if($idrol2>0 && $idrol!=$idrol2){
                ($objRolPersona->insertarRolPersona($_POST['cboSucursal'],$idregistro, $idrol2));
            }
            if($idrol3>0 && $idrol!=$idrol3){
                ($objRolPersona->insertarRolPersona($_POST['cboSucursal'],$idregistro, $idrol3));
            }*/

            if(is_string($res)){
    			$objPersona->abortarTransaccion(); 
    			$objBitacora->abortarTransaccion(); 
                $objRolPersona->abortarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Error de Proceso en Lotes2: ".$objGeneral->gMsg;
    			break 3;
    		}
    		if(!is_string($res)){
    			$objPersona->finalizarTransaccion(); 
    			$objBitacora->finalizarTransaccion(); 
                $objRolPersona->finalizarTransaccion();
    			if(ob_get_length()) ob_clean();
    			echo "Guardado correctamente";
    		}
            // echo $sql;
    	}	
    	/////////////////////////////////////////////////////////////////////////
    
    	echo "<strong><center>ARCHIVO IMPORTADO CON EXITO, EN TOTAL $campo REGISTROS Y $errores ERRORES</center></strong>";
    	//una vez terminado el proceso borramos el 
    	//archivo que esta en el servidor el bak_
    	unlink($destino);		
    }else{//si por algo no cargo el archivo bak_
	   echo "<strong style='font-size: 16;'><center>Necesitas primero seleccionar el archivo!!!</strong></center><br />";
    }
}

?>
</body>
</html>