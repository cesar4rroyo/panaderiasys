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
class clsRolPersona extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarRolPersona($idsucursal,$idpersona,$idrol)
 	{ 	
		$sql = "execute up_AgregarRolPersona $idsucursal,$idpersona,$idrol";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarRolPersona($id, $idsucursal,$idpersona,$idrol)
 	{
   		$sql = "execute up_ModificarRolPersona $id, $idsucursal,$idpersona,$idrol";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarRolPersona($id,$idsucursal)
 	{
   		$sql = "execute up_EliminarRolPersona $id,$idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarRolPersona($nro_reg, $nro_hoja, $order, $by, $id, $descripcion, $idsucursal, $idpersona, $idpersonamaestro)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarRolPersona ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT * FROM RolPersona RP INNER JOIN Persona P ON P.IdPersona=RP.IdPersona and P.idsucursal=RP.idsucursal INNER JOIN Rol R ON R.IdRol=RP.IdRol INNER JOIN SUCURSAL s on S.idsucursal=P.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." WHERE 1=1 ";
			if($id>0){ $sql = $sql . " AND IdRolPersona = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Descripcion LIKE '" . $descripcion . "'";}
			if($idsucursal>0){ $sql = $sql . " AND (P.idsucursal=".$idsucursal." or (P.idsucursal<>".$idsucursal." and compartido='S'))";}
			if($idpersona>0){ $sql = $sql . " AND RP.IdPersona = " . $idpersona;}
			if($idpersonamaestro>0){ $sql = $sql . " AND idpersonamaestro = " . $idpersonamaestro;}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	 	
 	}
}
?>