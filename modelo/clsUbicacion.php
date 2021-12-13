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
class clsUbicacion extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarUbicacion($codigo, $nombre, $totalcolumnas, $totalfilas, $idsucursal)
 	{ 	
		$sql = "select up_AgregarUbicacion ('".$this->mill($codigo)."', '".$this->mill($nombre)."', '".$this->mill($totalcolumnas)."', '".$this->mill($totalfilas)."', '".$this->mill($idsucursal)."') as idubicacion";
		/*$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}*/
		return $this->obtenerDataSQL($sql);
 	}

	function actualizarUbicacion($id, $codigo, $nombre, $totalcolumnas, $totalfilas, $idsucursal)
 	{
   		$sql = "execute up_ModificarUbicacion $id, '".$this->mill($codigo)."', '".$this->mill($nombre)."', '".$this->mill($totalcolumnas)."', '".$this->mill($totalfilas)."', '".$this->mill($idsucursal)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarUbicacion($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarUbicacion $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarUbicacion($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarUbicacion ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT IdUbicacion, IdSucursal, Codigo, Nombre, TotalColumnas, TotalFilas, Estado FROM Ubicacion WHERE 1=1 ";
			$sql = $sql . " AND Estado LIKE 'N' AND IdSucursal= ".$idsucursal." ";
			if($id>0){ $sql = $sql . " AND IdUbicacion = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Nombre LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function buscarUbicacionxCodigo($idubicacion)
			{
				$sql= "SELECT TotalColumnas, TotalFilas FROM Ubicacion WHERE idubicacion= $idubicacion";
				return $this->obtenerDataSQL($sql);	
			}
			
	function verificaExisteCodigo($nombre)
 	{
				
		$sql = "SELECT * FROM Ubicacion WHERE Estado = 'N' ";
		if($nombre !=""){ $sql = $sql." AND Codigo like '".$nombre."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}

}
?>