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
class clsTurno extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarTurno($nombre, $abreviatura, $horainicio, $horafin)
 	{ 	
		$sql = "select  up_AgregarTurno (".$this->gIdSucursal.",'".$this->mill($nombre)."', '".$this->mill($abreviatura)."', '".$horainicio."', '".$horafin."') as idturno";
		return $this->obtenerDataSQL($sql);
		/*$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}*/
 	}

	function actualizarTurno($id, $nombre, $abreviatura, $horainicio, $horafin)
 	{
   		$sql = "execute up_ModificarTurno $id, ".$this->gIdSucursal.",'".$this->mill($nombre)."', '".$this->mill($abreviatura)."', '".$horainicio."', '".$horafin."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarTurno($id)
 	{
   		$sql = "execute up_EliminarTurno $id, ".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarTurno($nro_reg, $nro_hoja, $order, $by, $id, $nombre)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarTurno ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$nombre = "%".$nombre."%";
			
			$sql = "SELECT idturno, idsucursal, nombre, abreviatura, horainicio, horafin, estado FROM turno WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N' and IdSucursal= ".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdTurno = " . $id;}
			if($nombre <>"" ){$sql = $sql . " AND nombre LIKE '" . $nombre . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	 	
 	}
	
	function buscarTurno($id)
 	{
			$sql = "SELECT idturno, nombre, abreviatura, horainicio, horafin, estado, idsucursal FROM turno WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N' and IdSucursal= ".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdTurno = " . $id;}
			return $this->obtenerDataSQL($sql);

 	}
	
	function verificaExisteNombre($nombre)
 	{
				
		$sql = "SELECT * FROM Turno WHERE Estado = 'N' ";
		if($nombre !=""){ $sql = $sql." AND Nombre like '".$nombre."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
}
?>