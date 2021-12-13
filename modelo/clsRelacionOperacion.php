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
class clsRelacionOperacion extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	
	function insertarRelacionOperacion($tabla, $tipo, $descripcion, $comentario, $accion, $imagen, $versi)
 	{ 	
		$sql = "execute up_AgregarRelacionOperacion '".$this->mill($tabla)."', '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."', '".$this->mill($versi)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarRelacionOperacion($id, $tabla, $tipo, $descripcion, $comentario, $accion, $imagen, $versi)
 	{
   		$sql = "execute up_ModificarRelacionOperacion '".$this->mill($tabla)."', $id, '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."', '".$this->mill($versi)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarRelacionOperacion($tabla, $id)
 	{
   		$sql = "execute up_EliminarRelacionOperacion $tabla, $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarRelacionOperacion($nro_reg, $nro_hoja, $order, $by, $id_tabla, $id, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarRelacionOperacion ".$nro_reg.", $nro_hoja, $order, $by, $id_tabla, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT RelacionOperacion.IdOperacion, RelacionOperacion.IdTabla, RelacionOperacion.Descripcion, RelacionOperacion.Comentario, RelacionOperacion.Tipo, RelacionOperacion.Accion, RelacionOperacion.Imagen, RelacionOperacion.Estado, RelacionOperacion.versi
	FROM RelacionOperacion WHERE 1=1 ";
			$sql = $sql . " AND RelacionOperacion.Estado LIKE 'N' ";
			if($id_tabla>0){ $sql = $sql . " AND RelacionOperacion.IdTabla = " . $id_tabla;}
			if($id>0){ $sql = $sql . " AND RelacionOperacion.IdOperacion = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND RelacionOperacion.Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		}
 	}
}
?>