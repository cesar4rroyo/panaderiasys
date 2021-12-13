<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
if($_SESSION['R_origen_ses']=="I"){
if(!$_SESSION['R_IdSucursal']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
if($_SESSION['R_versesadm']!=1){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
}
require_once 'clsTabla.php';
class clsCampo extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	
	function insertarCampo($id_tabla, $descripcion, $comentario, $longitud, $dicc='', $validacion='N', $msgvalidacion='', $longitudreporte='0', $alineacionreporte='C')
 	{ 	
		if($longitud=='') $longitud=0;
		if($longitudreporte=='') $longitudreporte=0;
		$sql = "execute up_AgregarCampo $id_tabla, '".$this->mill($descripcion)."', '".$this->mill($comentario)."', ".$longitud." , '".$this->mill($dicc)."', '$validacion', '$msgvalidacion', $longitudreporte, '$alineacionreporte'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarCampo($id, $id_tabla, $descripcion, $comentario, $longitud, $dicc='', $validacion='N', $msgvalidacion='', $longitudreporte='0', $alineacionreporte='C')
 	{
		if($longitud=='') $longitud=0;
		if($longitudreporte=='') $longitudreporte=0;
   		$sql = "execute up_ModificarCampo $id_tabla, $id, '".$this->mill($descripcion)."', '".$this->mill($comentario)."', ".$longitud.", '".$this->mill($dicc)."' , '$validacion', '$msgvalidacion', $longitudreporte, '$alineacionreporte'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarCampo($id_tabla, $id)
 	{
   		$sql = "execute up_EliminarCampo $id_tabla, $id ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarCampo($nro_reg, $nro_hoja, $order, $by, $id_tabla, $id, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarCampo ".$nro_reg.", $nro_hoja, $order, $by, $id_tabla, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT Campo.IdCampo, Campo.IdTabla, Campo.Descripcion, Campo.Comentario, Campo.Longitud, Campo.Diccionario, validacion, msgvalidacion, longitudreporte, alineacionreporte
			FROM Campo WHERE 1=1 ";
			$sql = $sql." AND Campo.Estado = 'N' ";
			if($id_tabla>0){ $sql = $sql." AND Campo.IdTabla = ".$id_tabla;}
			if($id>0){ $sql = $sql." AND Campo.IdCampo = ".$id;}
			if($descripcion !=""){ $sql = $sql." AND Campo.Descripcion LIKE '".$descripcion."'";}			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	
 	}
}
?>