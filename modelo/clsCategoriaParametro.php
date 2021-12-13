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
class clsCategoriaParametro extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarCategoriaParametro($descripcion)
 	{ 	
		$sql = "execute up_AgregarCategoriaParametro '".$this->mill($descripcion)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarCategoriaParametro($id, $descripcion)
 	{
   		$sql = "execute up_ModificarCategoriaParametro $id, '".$this->mill($descripcion)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarCategoriaParametro($id)
 	{
   		$sql = "execute up_EliminarCategoriaParametro $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function subirCategoriaParametro($idCategoriaParametro=0)
 	{
   		$sql = "execute up_SubirCategoriaParametro $idCategoriaParametro";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function bajarCategoriaParametro($idCategoriaParametro=0)
 	{
   		$sql = "execute up_BajarOpcionMenu $idCategoriaParametro";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarCategoriaParametro($nro_reg, $nro_hoja, $order, $by, $id, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarCategoriaParametro ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT IdCategoriaParametro, Descripcion, Orden, Estado FROM CategoriaParametro WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N'";
			if($id>0){ $sql = $sql . " AND IdCategoriaParametro = " . $id;}
			if($numero <>"" ){$sql = $sql . " AND descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function buscarCategoriaParametro($id)
 	{
			$sql = "SELECT IdCategoriaParametro, Descripcion, Orden, Estado FROM CategoriaParametro WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N'";
			if($id>0){ $sql = $sql . " AND IdCategoriaParametro = " . $id;}
			return $this->obtenerDataSQL($sql);
 	}
	
}
?>