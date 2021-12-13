<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
require_once 'cado.php';
class clsUbigeo extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function consultarUbigeoCombo($IdUbigeo,$IdUbigeoRef,$Tipo){
		$sql="SELECT idubigeo, descripcion, codigo, idubigeo_ref, tipo FROM Ubigeo WHERE 1=1";
		if(isset($IdUbigeo))
			$sql.=" AND IdUbigeo=".$IdUbigeo;
		if(isset($IdUbigeoRef))
			$sql.=" AND IdUbigeo_Ref=".$IdUbigeoRef;
		if(isset($Tipo))
			$sql.=" AND Tipo='".$Tipo."'";
		
		return $this->obtenerDataSQL($sql);
	}
} 
?>