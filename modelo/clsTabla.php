<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
require_once 'cado.php';
class clsTabla extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarTabla($descripcion, $comentario, $multiple, $tipo)
 	{ 	
		$sql = "execute up_AgregarTabla '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '$multiple', '$tipo' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarTabla($id, $descripcion, $comentario, $multiple, $tipo)
 	{
   		$sql = "execute up_ModificarTabla $id, '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '$multiple', '$tipo' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarTabla($id)
 	{
   		$sql = "execute up_EliminarTabla $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarTabla($nro_reg, $nro_hoja, $order, $by, $id, $descripcion,$multiple='',$tipo='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarTabla ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT Tabla.IdTabla, Tabla.Descripcion, Tabla.Comentario, Tabla.Multiple, Tabla.Tipo
			FROM Tabla WHERE 1=1 ";
			$sql = $sql . " AND Tabla.Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND Tabla.IdTabla = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Tabla.Descripcion LIKE '" . $descripcion . "'";}
			if($multiple <>"" ){$sql = $sql . " AND Tabla.Multiple LIKE '" . $multiple . "'";}
			if($tipo <>"" ){$sql = $sql . " AND Tabla.Tipo LIKE '" . $tipo . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function consultarTablas($id, $descripcion,$multiple='',$tipo='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarTabla ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT Tabla.IdTabla, Tabla.Descripcion, Tabla.Comentario, Tabla.Multiple, Tabla.Tipo
			FROM Tabla WHERE 1=1 ";
			$sql = $sql . " AND Tabla.Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND Tabla.IdTabla = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Tabla.Descripcion LIKE '" . $descripcion . "'";}
			if($multiple <>"" ){$sql = $sql . " AND Tabla.Multiple LIKE '" . $multiple . "'";}
			if($tipo <>"" ){$sql = $sql . " AND Tabla.Tipo LIKE '" . $tipo . "'";}
			
			return $this->obtenerDataSQL($sql);
		} 	 	
 	}
	
	function obtenerDatosTabla($tabla, $idsucursal,$idempresa)
 	{
		if($tabla=='Parametros'){
			$sql = "SELECT * FROM ".$tabla." WHERE idempresa=".$idempresa;		
		}else{
			$sql = "SELECT * FROM ".$tabla." WHERE idsucursal=".$idsucursal;	
		}
		return $this->obtenerDataSQL($sql);
 	}
}
?>