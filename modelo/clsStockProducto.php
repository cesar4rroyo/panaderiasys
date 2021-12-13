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

class clsStockProducto extends clsAccesoDatos
{
	
		// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}
	
	function insertar($idsucursal,$idproducto,$idsucursalproducto,$idunidad,$cantidad,$idmovimiento,$moneda,$PrecioUnidad,$Fecha,$IdUsuario,$idsucursalusuario)
	{
		$sql = "execute up_AgregarStockProducto ".$idsucursal.",".$idproducto.",".$idsucursalproducto.",".$idunidad.",".$cantidad.",".$idmovimiento.",'".$moneda."',".$PrecioUnidad.", LOCALTIMESTAMP,".$IdUsuario.",".$idsucursalusuario;//print_r($sql);
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		} 	 	
	}

	function insertarcompuesto($idsucursal,$idproducto,$idsucursalproducto,$idunidad,$cantidad,$idmovimiento,$moneda,$PrecioUnidad,$Fecha,$IdUsuario,$idsucursalusuario)
	{
		$sql = "execute up_AgregarStockProductoCompuesto ".$idsucursal.",".$idproducto.",".$idsucursalproducto.",".$idunidad.",".$cantidad.",".$idmovimiento.",'".$moneda."',".$PrecioUnidad.", LOCALTIMESTAMP,".$IdUsuario.",".$idsucursalusuario;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		} 	 	
	}
	
	function revertir($idsucursal,$idmovimiento,$IdUsuario,$idsucursalusuario,$operacion)
	{//A->ELIMINADO, I-> ANULDAO	
		$sql = "execute up_RevertirStock ".$idsucursal.",".$idmovimiento.",".$IdUsuario.",".$idsucursalusuario.",'".$operacion."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		} 	 	
	}
	
	function obtenerStock($idproducto,$idunidad,$idsucursal,$idsucursalproducto)
	{
		$sql = "SELECT obtenerStock(".$idproducto.",".$idunidad.",".$idsucursal.",".$idsucursalproducto.") as StockActual";
		$rst = $this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		return $dato->stockactual;
	}
	
	function cambiaCantidad($idproducto,$idunidadorigen,$cantidad,$idunidaddestino)
	{//pendiente
		$sql = "SELECT cambiaCantidad(".$idproducto.",".$idunidadorigen.",".$cantidad.",".$idunidaddestino.") as cantidadCambiada";
		$rst = $this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		return $dato->cantidadcambiada; 		 	
	}
} 
?>