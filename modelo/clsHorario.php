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
class clsHorario extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarHorario($idsucursal, $dia, $horainicio, $horafin)
 	{ 	
		$sql = "execute up_AgregarHorario '".$this->mill($dia)."', '".$this->mill($horainicio)."', '".$this->mill($horafin)."', ".$idsucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarHorario($id, $idsucursal, $dia, $horainicio, $horafin)
 	{
   		$sql = "execute up_ModificarHorario $id, $idsucursal, '".$this->mill($dia)."', '".$this->mill($horainicio)."', '".$this->mill($horafin)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarHorario($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarHorario $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarHorario($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $dia)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarHorario ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$dia = "%".$dia."%";
			
			$sql = "SELECT IdHorario, IdSucursal, Dia, HoraInicio, HoraFin, Estado FROM Horario WHERE 1=1 ";
			$sql = $sql . " AND idsucursal=".$idsucursal." AND Estado = 'N' ";
			if($id>0){ $sql = $sql . " AND IdHorario = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Dia LIKE '" . $dia. "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}

	function buscarHorario($id, $idsucursal, $dia)
 	{
			$dia = "%".$dia."%";
			
			$sql = "SELECT IdHorario, IdSucursal, Dia, HoraInicio, HoraFin, Estado FROM Horario WHERE 1=1 ";
			$sql = $sql . " AND idsucursal=".$idsucursal." AND Estado = 'N' ";
			if(isset($id) or $id>0){ $sql = $sql . " AND IdHorario = " . $id;}
			if(isset($descripcion) or $descripcion <>"" ){$sql = $sql . " AND Dia LIKE '" . $dia. "'";}
			
			return $this->obtenerDataSQL($sql."  order by dia asc");
 	}
}
?>