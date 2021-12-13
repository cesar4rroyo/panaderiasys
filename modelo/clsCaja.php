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
class clsCaja extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarCaja($numero, $idsalon, $idsucursal)
 	{ 	
		$sql = "select up_AgregarCaja ('".$this->mill($numero)."', ".$this->mill($idsalon).",'".$this->mill($idsucursal)."') as idcaja";
		return $this->obtenerDataSQL($sql);
		/*if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}*/
 	}

	function actualizarCaja($id, $numero, $idsalon, $idsucursal)
 	{
   		$sql = "execute up_ModificarCaja $id, '".$this->mill($numero)."', ".$this->mill($idsalon).", '".$this->mill($idsucursal)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarCaja($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarCaja $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarCaja($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $numero)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarCaja ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$numero = "%".$numero."%";
			
			$sql = "SELECT IdCaja, C.IdSucursal, Numero, C.IdSalon, C.Estado, S.descripcion as salon FROM Caja C INNER JOIN Salon S on C.idsalon=S.idSalon and C.idsucursal=S.idsucursal WHERE 1=1 ";
			$sql = $sql . " AND C.Estado = 'N' AND C.IdSucursal= ".$idsucursal." ";
			if($id>0){ $sql = $sql . " AND IdCaja = " . $id;}
			if($numero <>"" ){$sql = $sql . " AND numero LIKE '" . $numero . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function buscarCaja($id)
 	{
			$sql = "SELECT IdCaja, Numero, C.IdSalon, C.Estado, S.descripcion as salon FROM Caja C INNER JOIN Salon S on C.idsalon=S.idSalon and C.idsucursal=S.idsucursal WHERE 1=1 ";
			$sql = $sql . " AND C.Estado = 'N' and IdSucursal= ".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdCaja = " . $id;}
			return $this->obtenerDataSQL($sql);

 	}
	
	function consultarCajaxSalon($idsalon)
 	{
			$sql = "SELECT * FROM Caja WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N' and IdSucursal= ".$this->gIdSucursal." ";
			if($idsalon>0){ $sql = $sql . " AND idsalon = " . $idsalon;}
			if($idsalon==0){ $sql = $sql . " AND idsalon in (select idsalon from salon where estado='N' and idsucursal=".$this->gIdSucursal." limit 1)";}
			//si el salon es cero debo filtrar las mesas del primer salon q pertenecen a la sucursal actual
			$sql = $sql . " Order by numero asc ";
			return $this->obtenerDataSQL($sql);
 	}

	function verificaExisteNumero($numero)
 	{
				
		$sql = "SELECT * FROM Caja WHERE Estado = 'N' ";
		if($numero !=""){ $sql = $sql." AND Numero like '".$numero."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
}
?>