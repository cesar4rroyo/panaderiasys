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
class clsParametro extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarParametro($idempresa, $idsucursal, $idcategoriaparametro, $idtabla, $descripcion, $valor, $obligatorio)
 	{ 	
		$sql = "execute up_AgregarParametro $idempresa, $idsucursal, $idcategoriaparametro, $idtabla,'".trim(strtoupper($descripcion))."', '$valor', '$obligatorio'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarParametro($id, $idempresa, $idsucursal, $idcategoriaparametro, $idtabla, $descripcion, $valor, $obligatorio)
 	{
   		$sql = "execute up_ModificarParametro $id, $idempresa, $idsucursal, $idcategoriaparametro, $idtabla,'".trim(strtoupper($descripcion))."', '$valor', '$obligatorio'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarParametroUser($id, $idempresa, $idsucursal, $valor)
 	{
   		$sql = "execute up_ModificarParametroUser $id, $idempresa, $idsucursal, '$valor'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarParametro($id, $idempresa, $idsucursal)
 	{
   		$sql = "execute up_EliminarParametro $id, $idempresa, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarParametro($nro_reg, $nro_hoja, $order, $by, $id, $idempresa, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarParametro ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT idparametros, idempresa, idsucursal, p.idcategoriaparametro, cp.descripcion as categoria, p.idtabla, t.descripcion as tabla, 
       p.descripcion, valor, obligatorio, p.estado
  FROM parametros p inner join tabla t on p.idtabla=t.idtabla inner join categoriaparametro cp on cp.idcategoriaparametro=p.idcategoriaparametro WHERE 1=1";
			$sql = $sql . " AND p.Estado LIKE 'N' and idempresa= ".$idempresa." ";
			if($id>0){ $sql = $sql . " AND IdParametros = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND p.Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function consultarParametroUser($id, $idempresa, $descripcion)
 	{
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT idparametros, idempresa, idsucursal, p.idcategoriaparametro, cp.descripcion as categoria, p.idtabla, t.descripcion as tabla, 
       p.descripcion, valor, obligatorio, p.estado
  FROM parametros p inner join tabla t on p.idtabla=t.idtabla inner join categoriaparametro cp on cp.idcategoriaparametro=p.idcategoriaparametro WHERE 1=1 ";
			$sql = $sql . " AND p.Estado LIKE 'N' and idempresa= ".$idempresa." ";
			if($id>0){ $sql = $sql . " AND IdParametros = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND p.Descripcion LIKE '" . $descripcion . "'";}
			$sql.=" order by cp.descripcion, idparametros asc";
			return $this->obtenerDataSQL($sql);
 	}
	
	function parametrosUpdate($idsucursalorigen, $idempresadestino, $idsucursaldestino)
 	{
   		$sql = "execute up_actualizarparametrizacion $idsucursalorigen, $idempresadestino, $idsucursaldestino";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
}
?>