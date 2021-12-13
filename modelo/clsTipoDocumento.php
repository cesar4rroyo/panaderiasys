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
class clsTipoDocumento extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarTipoDocumento($descripcion, $abreviatura, $stock, $idtipomovimiento)
 	{ 	
		$sql = "execute up_AgregarTipoDocumento '".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($stock)."',".$idtipomovimiento;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarTipoDocumento($id, $descripcion, $abreviatura, $stock, $idtipomovimiento)
 	{
   		$sql = "execute up_ModificarTipoDocumento $id, '".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($stock)."',".$idtipomovimiento;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarTipoDocumento($id)
 	{
   		$sql = "execute up_EliminarTipoDocumento $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarTipoDocumento($nro_reg, $nro_hoja, $order, $by, $id, $descripcion, $reporte='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarTipoDocumento ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT IdTipoDocumento, TD.Descripcion, TD.Abreviatura, Stock, TD.IdTipoMovimiento, TM.Descripcion Movimiento FROM TipoDocumento TD INNER JOIN TipoMovimiento TM on TD.IdTipoMovimiento=TM.IdTipoMovimiento WHERE 1=1 ";
			/*$sql = $sql . " AND Estado LIKE 'N' ";*/
			if($id>0){ $sql = $sql . " AND IdTipoDocumento = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND TD.Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			//SE MODIFIKO PARA VER SI ERA PARA UN REPORTE
            if($reporte<>""){
                  return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13));
            }else{
                  return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
            }
			//return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
}
?>