<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
require_once 'clsEmpresa.php';
class clsSucursal extends clsEmpresa
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}

	function insertarSucursal($nombresucursal, $direccion, $ruc, $email, $telefonofijo, $telefonomovil, $fax, $logo, $idempresa)
 	{ 	
		$sql = "execute up_AgregarSucursal ".$idempresa.",'".$nombresucursal."', '".$direccion."', '".$ruc."' , '".$email."', '".$telefonofijo."', '".$telefonomovil."', '".$fax."', '".$logo."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function insertarSucursalOut($nombresucursal, $direccion, $ruc, $email, $telefonofijo, $telefonomovil, $fax, $logo, $idempresa)
 	{ 	
			$sql = "select up_AgregarSucursalOut(".$idempresa.",'".$nombresucursal."', '".$direccion."', '".$ruc."' , '".$email."', '".$telefonofijo."', '".$telefonomovil."', '".$fax."', '".$logo."') as idsucursal";
			return $this->obtenerDataSQL($sql);
 	}
		
	function insertarSucursalParametrizada($nombresucursal, $direccion, $ruc, $email, $telefonofijo, $telefonomovil, $fax, $logo, $idempresa)
 	{ 	
		$sql = "select up_AgregarSucursalParametrizada(".$idempresa.",'".$nombresucursal."', '".$direccion."', '".$ruc."' , '".$email."', '".$telefonofijo."', '".$telefonomovil."', '".$fax."', '".$logo."') as idsucursal";
		return $this->obtenerDataSQL($sql);
 	}

	
	function actualizarSucursal($id, $idempresa, $nombresucursal, $direccion, $ruc, $email, $telefonofijo, $telefonomovil, $fax, $logo)
 	{
   		$sql = "execute up_ModificarSucursal $id, $idempresa, '".$nombresucursal."', '".$direccion."', '".$ruc."' , '".$email."', '".$telefonofijo."', '".$telefonomovil."', '".$fax."', '".$logo."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarSucursal($id)
 	{
   		$sql = "execute up_EliminarSucursal $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarSucursal($nro_reg, $nro_hoja, $order, $by, $id, $buscar_razon_social='', $buscar_ruc='', $buscar_email='', $buscar_telefonos='', $idempresa=0)
 	{
		if(parent::getTipoBD()==1){
			$nombresucursal = "%".$nombresucursal."%";
			$sql = "execute up_BuscarSucursal ".$nro_reg.", $nro_hoja, $order, $by, $id, '".$this->mill($nombresucursal)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$buscar_razon_social = "%".$buscar_razon_social."%";
			$buscar_ruc = "%".$buscar_ruc."%";
			$buscar_email = "%".$buscar_email."%";
			$buscar_telefonos = "%".$buscar_telefonos."%";
			
			$sql = "SELECT *
			FROM Sucursal WHERE 1=1 ";
			$sql = $sql . " AND Sucursal.Estado LIKE 'N' ";
			if($id>0 ){$sql = $sql . " AND Sucursal.IdSucursal = " . $id;}
			if($idempresa>0 ){$sql = $sql . " AND Sucursal.IdEmpresa = " . $idempresa;}
			if($buscar_razon_social <>"" ){$sql = $sql . " AND Sucursal.RazonSocial LIKE '" . $buscar_razon_social . "'";}
			if($buscar_ruc <>"" ){$sql = $sql . " AND Sucursal.ruc LIKE '" . $buscar_ruc . "'";}
			if($buscar_email <>"" ){$sql = $sql . " AND Sucursal.email LIKE '" . $buscar_email . "'";}
			if($buscar_telefonos <>"" ){$sql = $sql . " AND (Sucursal.telefonofijo LIKE '" . $buscar_telefonos . "' OR Sucursal.telefonomovil LIKE '" . $buscar_telefonos . "')";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	
 	}
	
	function consultar($nombresucursal)
 	{
		$nombresucursal = "%".$nombresucursal."%";
   		$sql = "Select * From Sucursal Where RazonSocial like '".$nombresucursal."'";
		return $this->obtenerDataSQL($sql);
 	
 	}
	
	function consultarxId($id)
 	{
   		$sql = "Select * From Sucursal Where IdSucursal = ".$id;
		return $this->obtenerDataSQL($sql);
 	
 	}
	function consultarxIdEmpresa($id)
 	{
   		$sql = "Select * From Sucursal Where IdEmpresa = ".$id;
		return $this->obtenerDataSQL($sql);
 	
 	}
}
?>