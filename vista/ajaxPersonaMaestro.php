<?php
$action = $_POST["accion"];
$nrodoc = $_POST["nrodoc"];
$tipo = $_POST["tipo"];
$id_clase=22;
require("../modelo/clsPersonaMaestro.php");
$ObjPersonaMaestro = new clsPersonaMaestro($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
//$nrodoc='444454444';
//$action="BuscaxNroDoc";
if($action=="BuscaxNroDoc"){
	$rst = $ObjPersonaMaestro->consultarxNroDoc($tipo,$nrodoc);
	$registro=$rst->fetchObject();
	if(isset($registro->idpersonamaestro)){
		echo umill("vIdPersonaMaestro=$registro->idpersonamaestro;
		vApellidos='$registro->apellidos';
		vNombres='$registro->nombres';
		vSexo='$registro->sexo';
		vError=0;");
	}else{
		echo umill("vError=1;vMensaje='No se encontro a la persona con el N�mero de Documento: ';");
	}
}
if($action=="verificaNroDoc"){
	$rst = $ObjPersonaMaestro->consultarxNroDoc($tipo,$nrodoc);
	$registro=$rst->fetchAll();
	echo "vCant=".count($registro).";";
}
if($action=="BuscaPersona"){
	$idrol = $_POST["idrol"];
	$nombres = $_POST["nombres"];
	$div = $_POST["div"];
	if(isset($_POST["tipopersona"])){
		$tipopersona=$_POST["tipopersona"];
	}else{
		$tipopersona="";
	}
	
	require("../modelo/clsPersona.php");	
	$ObjPersona = new clsPersona(23,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjPersona->consultarPersonaxRol($idrol,$nombres,$tipopersona);
    
	echo "<table id='tablaPersona'><tr><th>Doc.</th><th>Apellidos y nombres</th><th>Telefono</th><th>Direccion</th></tr>";
	while($registro=$consulta->fetchObject())
	{
	   $registros.= "<tr id='".$registro->idsucursal."-".$registro->idpersona."' class='$estilo' onClick='mostrarPersona(".$registro->idsucursal.",".$registro->idpersona.",&quot;".$div."&quot;)' style='cursor:pointer;'>";
	   $registros.= "<td>".$registro->tipodoc.": ".$registro->nrodoc."</td>";
	   //LO SGTE PARA OBTENER LA PORSION DE TEXTO QUE COINCIDE Y CAMBIARLE DE ESTILO, $cadena2 -> est� variable contiene el valor q coincide, al cual lo ubico en una etiqueta span para cambiarle de estilo.
		$posicion  = stripos($registro->nombres, $nombres);
		if($posicion>-1){
			$cadena1 = substr($registro->nombres, 0, $posicion);
			$cadena2 = substr($registro->nombres, $posicion, strlen($nombres));
			$cadena3 = substr($registro->nombres, ($posicion + strlen($nombres)));
			
			$dato = $cadena1.'<span>'.$cadena2.'</span>'.$cadena3;
			$registros.= "<td>".$dato."</td>";
		}else{
			$registros.= "<td>".$registro->nombres."</td>";
		}
        $registros.="<td>".$registro->telefonofijo." - ".$registro->telefonomovil."</td>";
	    $registros.="<td>".$registro->direccion."</td>";
        $registros.= "</tr>";
	}
	echo $registros;
	echo "</table>";
}
if($action=="BuscaPersonaJSON"){
	$idrol = $_POST["idrol"];
	$nombres = $_POST["nombres"];
	$div = $_POST["div"];
	if(isset($_POST["tipopersona"])){
		$tipopersona=$_POST["tipopersona"];
	}else{
		$tipopersona="";
	}
        if(isset($_POST["modo"])){
		$modo=$_POST["modo"];
	}else{
		$modo=null;
	}
	require("../modelo/clsPersona.php");	
	$ObjPersona = new clsPersona(23,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjPersona->consultarPersonaxRol($idrol,$nombres,'');
        //print_r($consulta);
    $datos = array();
    if($_SESSION["R_IdSucursal"]=="14"){
    	$datos["VARIOS"] = "1|3";
    }else{
    	$datos["VARIOS"] = $_SESSION['R_IdSucursal']."|2";
    }
	while($registro=$consulta->fetchObject())
	{
            if(empty($modo)){
                $datos["$registro->nombres"] = "$registro->idsucursal|$registro->idpersona";
            }else{
                if($modo=="N"){
                    $datos["$registro->nombres"] = "$registro->idsucursal|$registro->idpersona|$registro->nrodoc|$registro->direccion";
                }else{
                    $datos["$registro->nrodoc"] = "$registro->idsucursal|$registro->idpersona";
                }
            }
	}
        echo json_encode(["datos"=>$datos]);
}
if($action=="mostrarPersona"){
	$id = $_POST["id"];
	if(isset($_POST["idsucursal"])){
	$idsucursal = $_POST["idsucursal"];
	}else{$idsucursal=1;}
	
	require("../modelo/clsPersona.php");	
	$ObjPersona = new clsPersona(23,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjPersona->consultarxId($id,$idsucursal);

	while($registro=$consulta->fetchObject())
	{
		echo "vNombres='".$ObjPersona->mill($registro->nombres)."';
			vIdSucursal='$registro->idsucursal';";	  
	}
}
if($action=="BuscaxNroDocPersonaEmpresa"){
	$idempresa = $_POST["idempresa"];
	$rst = $ObjPersonaMaestro->consultarxNroDoc($tipo,$nrodoc);
	$registro=$rst->fetchObject();
	if(isset($registro->idpersonamaestro)){
		$cadena="vIdPersonaMaestro=$registro->idpersonamaestro;
		vApellidos='$registro->apellidos';
		vNombres='$registro->nombres';
		vSexo='$registro->sexo';
		vError=0;";
		require("../modelo/clsPersona.php");	
		$ObjPersona = new clsPersona(23,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		$consulta = $ObjPersona->consultarxIdPersonaMaestroyEmpresa($registro->idpersonamaestro,$idempresa);
		$registro2=$consulta->fetchObject();
		if(isset($registro2->idpersona)){
			$cadena.="vIdPersona=$registro2->idpersona;
			vIdSucursal=$registro2->idsucursal;
			vIdDistrito='$registro2->iddistrito';
			vDireccion='$registro2->direccion';
			vEmail='$registro2->email';
			vTelefonoFijo='$registro2->telefonofijo';
			vTelefonoMovil='$registro2->telefonomovil';			
			";
		}else{
			$cadena.="vIdPersona=0;";
		}
		echo $cadena;
	}else{
		echo umill("vError=1;vMensaje='No se encontro a la persona con el N�mero de Documento: ';");
	}
}
?>