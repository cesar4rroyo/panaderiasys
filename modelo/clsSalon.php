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
class clsSalon extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarSalon($descripcion, $abreviatura, $imagen, $idsucursal)
 	{ 	
		$sql = "select up_AgregarSalon ('".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($imagen)."',".$idsucursal.") as idsalon";
		/*$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}*/
		return $this->obtenerDataSQL($sql);
 	}

	function actualizarSalon($id, $idsucursal, $descripcion, $abreviatura, $imagen, $idmesalibre)
 	{
   		$sql = "execute up_ModificarSalon $id, $idsucursal, '".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($imagen)."', ".$idmesalibre;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarSalon($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarSalon $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarSalon($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarSalon ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT IdSalon, IdSucursal, Descripcion, Abreviatura, Imagen, Estado, IdMesaLibre FROM Salon WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N' and IdSucursal= ".$idsucursal." ";
			if($id>0){ $sql = $sql . " AND IdSalon = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function buscarSalon($id)
 	{
			$sql = "SELECT IdSalon, Descripcion, Abreviatura, Imagen, Estado, IdMesaLibre FROM Salon WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N' and IdSucursal= ".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdSalon = " . $id;}
			
			return $this->obtenerDataSQL($sql);
 	}
	
	function verificaExisteDescripcion($nombre)
 	{
				
		$sql = "SELECT * FROM Salon WHERE Estado = 'N' ";
		if($nombre !=""){ $sql = $sql." AND Descripcion like '".$nombre."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}

}
?>