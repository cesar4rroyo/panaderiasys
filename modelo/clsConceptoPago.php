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
require_once 'cado.php';
class clsConceptoPago extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarConceptoPago($descripcion, $tipo)
 	{ 	
		$sql = "execute up_AgregarConceptoPago '".$this->mill($descripcion)."', '".$this->mill($tipo)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarConceptoPago($id, $descripcion, $tipo)
 	{
   		$sql = "execute up_ModificarConceptoPago $id, '".$this->mill($descripcion)."', '".$this->mill($tipo)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarConceptoPago($id)
 	{
   		$sql = "execute up_EliminarConceptoPago $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarConceptoPago($nro_reg, $nro_hoja, $order, $by, $id, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarConceptoPago ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT IdConceptoPago, Descripcion, Tipo, Estado FROM ConceptoPago WHERE 1=1 ";
			$sql = $sql . " AND Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND IdConceptoPago = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function consultarConceptoPagoxTipoDocumento($tipodocumento)
 	{
			$sql = "SELECT IdConceptoPago, Descripcion, Tipo, Estado FROM ConceptoPago WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N' ";
			if($tipodocumento<>''){ $sql = $sql . " AND Tipo = '".$tipodocumento."'";}
			
			return $this->obtenerDataSQL($sql);
 	}
}
?>