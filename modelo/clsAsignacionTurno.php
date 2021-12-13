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
class clsAsignacionTurno extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarAsignacionTurno($idsucursalpersona, $idpersona, $idturno, $idcaja)
 	{ 	
		$sql = "execute up_AgregarAsignacionTurno ".$this->gIdSucursal.", ".$idpersona.", ".$idsucursalpersona.", ".$idturno.", ".$idcaja;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarAsignacionTurno($id, $idsucursalpersona, $idpersona, $idturno, $idcaja)
 	{
   		$sql = "execute up_ModificarAsignacionTurno $id, ".$this->gIdSucursal.", ".$idsucursalpersona.", ".$idpersona.", ".$idturno.", ".$idcaja;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarAsignacionTurno($id)
 	{
   		$sql = "execute up_EliminarAsignacionTurno $id,".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function activarAsignacionTurno($id)
 	{
   		$sql = "execute up_ActivarAsignacionTurno $id,".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function desactivarAsignacionTurno($id)
 	{
   		$sql = "execute up_DesactivarAsignacionTurno $id,".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarAsignacionTurno($nro_reg, $nro_hoja, $order, $by, $id, $persona='', $turno='', $salon='', $caja='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarAsignacionTurno ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$persona = "%".$persona."%";
			$turno = "%".$turno."%";
			$salon = "%".$salon."%";
			$caja = "%".$caja."%";
			
			$sql = "SELECT IdAsignacionTurno, at.idpersona, at.idsucursalpersona, at.idturno, at.idcaja, c.idsalon, situacion, at.estado, (PM.apellidos || ' ' || PM.nombres) as Persona, t.nombre as turno, c.numero as caja, S.descripcion as salon FROM AsignacionTurno at inner join caja c on at.idcaja=c.idcaja and  AT.IdSucursal = C.IdSucursal INNER JOIN Salon S on C.idsalon=S.idSalon and S.IdSucursal = C.IdSucursal LEFT JOIN Persona P ON P.idpersona=at.idpersona and P.IdSucursal = AT.IdSucursalPersona LEFT JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro inner join turno t on t.idturno=at.idturno and  T.IdSucursal = AT.IdSucursal WHERE 1=1 ";
			$sql = $sql . " AND at.Estado = 'N' and AT.IdSucursal= ".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdAsignacionTurno = " . $id;}
			if($persona <>"" ){$sql = $sql . " AND (PM.apellidos || ' ' || PM.nombres) LIKE '" . $persona . "'";}
			if($turno <>"" ){$sql = $sql . " AND t.nombre LIKE '" . $turno . "'";}
			if($salon <>"" ){$sql = $sql . " AND S.descripcion LIKE '" . $salon . "'";}
			if($caja <>"" ){$sql = $sql . " AND c.numero LIKE '" . $caja . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function buscarAsignacionTurno($id)
 	{
			$sql = "SELECT IdAsignacionTurno, Numero, C.IdSalon, C.Estado, S.descripcion as salon FROM AsignacionTurno C INNER JOIN Salon S on C.idsalon=S.idSalon WHERE 1=1 ";
			$sql = $sql . " AND C.Estado = 'N' and C.IdSucursal= ".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdAsignacionTurno = " . $id;}
			return $this->obtenerDataSQL($sql);

 	}
	
	//PENDIENTE
	function consultarAsignacionTurnoxSalon($idsalon)
 	{
			$sql = "SELECT * FROM AsignacionTurno WHERE 1=1 ";
			$sql = $sql . " AND Estado = 'N' ";
			if($idsalon>0){ $sql = $sql . " AND idsalon = " . $idsalon;}
			if($idsalon==0){ $sql = $sql . " AND idsalon in (select idsalon from salon where estado='N' and idsucursal=".$this->gIdSucursal." limit 1)";}
			//si el salon es cero debo filtrar las mesas del primer salon q pertenecen a la sucursal actual
			$sql = $sql . " Order by numero asc ";
			return $this->obtenerDataSQL($sql);
 	}

}
?>