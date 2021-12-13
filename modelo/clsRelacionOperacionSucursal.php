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
class clsRelacionOperacionSucursal extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	//PENDIENTE
	function insertarRelacionOperacionSucursal($tabla, $tipo, $descripcion, $comentario, $accion, $imagen)
 	{ 	
		$sql = "execute up_AgregarRelacionOperacionSucursal '".$this->mill($tabla)."', '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//PENDIENTE
	function actualizarRelacionOperacionSucursal($id, $tabla, $tipo, $descripcion, $comentario, $accion, $imagen)
 	{
   		$sql = "execute up_ModificarRelacionOperacionSucursal '".$this->mill($tabla)."', $id, '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//PENDIENTE
	function eliminarRelacionOperacionSucursal($tabla, $id)
 	{
   		$sql = "execute up_EliminarRelacionOperacionSucursal $tabla, $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarRelacionOperacionSucursal($nro_reg, $nro_hoja, $order, $by, $id_cliente=0, $id_tabla=0, $id_operacion=0, $tipo="G", $descripcion="")
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarRelacionOperacionSucursal $nro_reg, $nro_hoja, $order, $by, $id_cliente, $id_tabla, $id_operacion, '$tipo', '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT RelacionOperacionCliente.IdOperacion, RelacionOperacionCliente.IdTabla, RelacionOperacionCliente.IdSucursal, RelacionOperacion.Descripcion, RelacionOperacionCliente.Orden, RelacionOperacion.Tipo, RelacionOperacionCliente.Estado
	FROM RelacionOperacionCliente
		INNER JOIN RelacionOperacion ON RelacionOperacionCliente.IdTabla = RelacionOperacion.IdTabla AND RelacionOperacionCliente.IdOperacion = RelacionOperacion.IdOperacion WHERE 1=1 ";
			if($id_tabla>0){ $sql = $sql . " AND RelacionOperacionCliente.IdTabla = " . $id_tabla;}
			if($id_operacion>0){ $sql = $sql . " AND RelacionOperacionCliente.IdOperacion = " . $id_operacion;}
			if($id_cliente>0){ $sql = $sql . " AND RelacionOperacionCliente.IdSucursal = " . $id_cliente;}
			if($tipo <>"" ){$sql = $sql . " AND RelacionOperacion.Tipo = '" . $tipo . "'";}
			if($descripcion <>"" ){$sql = $sql . " AND RelacionOperacion.Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	
 	}
	
	function activarRelacionOperacionSucursal($id_cliente=0, $id_tabla=0, $id_operacion=0, $tipo="G")
 	{
   		$sql = "execute up_ActivarRelacionOperacionSucursal $id_cliente, $id_tabla, $id_operacion";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function desactivarRelacionOperacionSucursal($id_cliente=0, $id_tabla=0, $id_operacion=0, $tipo="G")
 	{
   		$sql = "execute up_DesactivarRelacionOperacionSucursal $id_cliente, $id_tabla, $id_operacion";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function subirRelacionOperacionSucursal($id_cliente=0, $id_tabla=0, $id_operacion=0, $tipo="G")
 	{
   		$sql = "execute up_SubirRelacionOperacionSucursal $id_cliente, $id_tabla, $id_operacion";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function bajarRelacionOperacionSucursal($id_cliente=0, $id_tabla=0, $id_operacion=0, $tipo="G")
 	{
   		$sql = "execute up_BajarRelacionOperacionSucursal $id_cliente, $id_tabla, $id_operacion";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
}
?>