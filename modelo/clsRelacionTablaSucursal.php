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
require_once 'clsTabla.php';
class clsRelacionTablaSucursal extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $sucursal, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $sucursal;		
		parent::__construct($tabla, $sucursal, $user, $pass);
	}

		function insertarRelacionTablaSucursal($id_tabla, $id_sucursal, $descripcion, $descripcionmant)
 	{ 	
		$sql = "execute up_AgregarRelacionTablaSucursal $id_tabla, $id_sucursal, '".$this->mill($descripcion)."', '".$this->mill($descripcionmant)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarRelacionTablaSucursal($id_tabla, $id_sucursal, $descripcion, $descripcionmant)
 	{
   		$sql = "execute up_ModificarRelacionTablaSucursal $id_tabla, $id_sucursal, '".$this->mill($descripcion)."', '".$this->mill($descripcionmant)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarRelacionTablaSucursal($id, $id_sucursal)
 	{
   		$sql = "execute up_EliminarRelacionTablaSucursal $id, $id_sucursal ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarRelacionTablaSucursal($nro_reg, $nro_hoja, $order, $by, $id_tabla, $id_sucursal, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarRelacionTablaSucursal ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT * FROM RelacionTablaSucursal WHERE 1=1 ";
			$sql = $sql . " AND RelacionTablaSucursal.Estado = 'N' ";
			if($id_tabla>0){ $sql = $sql . " AND RelacionTablaSucursal.IdTabla = " . $id_tabla;}
			if($id_sucursal>0){ $sql = $sql . " AND RelacionTablaSucursal.IdSucursal = " . $id_sucursal;}
			if($descripcion <>"" ){$sql = $sql . " AND RelacionTablaSucursal.Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
}
?>