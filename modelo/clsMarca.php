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
class clsMarca extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarMarca($descripcion, $abreviatura, $idsucursal)
 	{ 	
		$sql = "select up_AgregarMarca ('".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($idsucursal)."') as idmarca";
		return $this->obtenerDataSQL($sql);
 	}

	function actualizarMarca($id, $descripcion, $abreviatura, $idsucursal)
 	{
   		$sql = "execute up_ModificarMarca $id, '".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($idsucursal)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarMarca($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarMarca $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarMarca($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $reporte='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarMarca ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			if($reporte <>"" ){
                  $sql = "SELECT 0, IdMarca, IdSucursal as idSucursal, Descripcion, Abreviatura, Estado FROM Marca
                  union select 0, 0 as IdMarca,".$idsucursal." as idSucursal,'NINGUNA' as Descripcion,'NING' as Abreviatura,'N' as Estado from Marca WHERE 1=1 ";
            }else{
				$sql = "SELECT IdMarca, IdSucursal, Descripcion, Abreviatura, Estado FROM Marca WHERE 1=1 ";
			}
			$sql = $sql . " AND Estado LIKE 'N' and IdSucursal= ".$idsucursal." ";
			if($id>0){ $sql = $sql . " AND IdMarca = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			//SE MODIFIKO PARA VER SI ERA PARA UN REPORTE
            if($reporte <>"" ){
                  return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,9,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13));
            }else{
                  return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
            }
			//return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function verificaExisteDescripcion($nombre)
 	{
				
		$sql = "SELECT * FROM Marca WHERE Estado = 'N' ";
		if($nombre !=""){ $sql = $sql." AND Descripcion like '".$nombre."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
}
?>