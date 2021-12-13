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
class clsEmpresa extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarEmpresa($nombreempresa, $direccion, $ruc, $email, $telefonofijo, $telefonomovil, $fax, $logo)
 	{ 	
		$sql = "execute up_AgregarEmpresa '".$nombreempresa."', '".$direccion."', '".$ruc."' , '".$email."', '".$telefonofijo."', '".$telefonomovil."', '".$fax."', '".$logo."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function insertarEmpresaOut($nombreempresa, $direccion, $ruc, $email, $telefonofijo, $telefonomovil, $fax, $logo)
 	{ 	
			$sql = "select up_AgregarEmpresaOut('".$nombreempresa."', '".$direccion."', '".$ruc."' , '".$email."', '".$telefonofijo."', '".$telefonomovil."', '".$fax."', '".$logo."') as idempresa";
			return $this->obtenerDataSQL($sql);
 	}

	function actualizarEmpresa($id, $nombreempresa, $direccion, $ruc, $email, $telefonofijo, $telefonomovil, $fax, $logo)
 	{
   		$sql = "execute up_ModificarEmpresa $id, '".$nombreempresa."', '".$direccion."', '".$ruc."' , '".$email."', '".$telefonofijo."', '".$telefonomovil."', '".$fax."', '".$logo."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarEmpresa($id)
 	{
   		$sql = "execute up_EliminarEmpresa $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarEmpresa($nro_reg, $nro_hoja, $order, $by, $id, $buscar_razon_social='', $buscar_ruc='', $buscar_email='', $buscar_telefonos='')
 	{
		if(parent::getTipoBD()==1){
			$nombreempresa = "%".$nombreempresa."%";
			$sql = "execute up_BuscarEmpresa ".$nro_reg.", $nro_hoja, $order, $by, $id, '".$this->mill($nombreempresa)."'";
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
			FROM Empresa WHERE 1=1 ";
			$sql = $sql . " AND Empresa.Estado LIKE 'N' ";
			if($id>0 ){$sql = $sql . " AND Empresa.IdEmpresa = " . $id;}
			if($buscar_razon_social <>"" ){$sql = $sql . " AND Empresa.RazonSocial LIKE '" . $buscar_razon_social . "'";}
			if($buscar_ruc <>"" ){$sql = $sql . " AND Empresa.ruc LIKE '" . $buscar_ruc . "'";}
			if($buscar_email <>"" ){$sql = $sql . " AND Empresa.email LIKE '" . $buscar_email . "'";}
			if($buscar_telefonos <>"" ){$sql = $sql . " AND (Empresa.telefonofijo LIKE '" . $buscar_telefonos . "' OR Empresa.telefonomovil LIKE '" . $buscar_telefonos . "')";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	
 	}
	
	function consultar($nombreempresa)
 	{
		$nombreempresa = "%".$nombreempresa."%";
   		$sql = "Select * From Empresa Where RazonSocial like '".$nombreempresa."'";
		return $this->obtenerDataSQL($sql);
 	
 	}
	
	function buscarEmpresaxRuc($ruc)
 	{
   		$sql = "Select * From Empresa Where RUC = '".$ruc."'";
		return $this->obtenerDataSQL($sql);
 	
 	}

}
?>