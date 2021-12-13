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
class clsImpresora extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarImpresora($descripcion,$ip, $idsucursal)
 	{ 	
		$sql = "select up_AgregarImpresora('".$this->mill($idsucursal)."','".$this->mill($descripcion)."', '".$this->mill($ip)."') as idimpresora";
		return $this->obtenerDataSQL($sql);
 	}

	function actualizarImpresora($id, $descripcion,$ip, $idsucursal)
 	{
   		$sql = "execute up_ModificarImpresora $id, '".$this->mill($idsucursal)."','".$this->mill($descripcion)."', '".$this->mill($ip)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarImpresora($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarImpresora $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarImpresora($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $ip)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarImpresora ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			$sql = "SELECT IdImpresora, IdSucursal, nombre,ip, Estado FROM Impresora WHERE 1=1 ";
			$sql = $sql . " AND Estado LIKE 'N' and IdSucursal= ".$idsucursal." ";
			if($id>0){ $sql = $sql . " AND IdImpresora = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND nombre LIKE '" . $descripcion . "'";}
            if($ip <>"" ){$sql = $sql . " AND ip LIKE '" . $ip . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
            //echo "SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg);
            return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function verificaExisteDescripcion($nombre)
 	{
				
		$sql = "SELECT * FROM Impresora WHERE Estado = 'N' ";
		if($nombre !=""){ $sql = $sql." AND nombre like '".$nombre."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
}
?>