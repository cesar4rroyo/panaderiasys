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
class clsRelacionOperacionPerfil extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	//PENDIENTE
	function insertarRelacionOperacionPerfil($tabla, $tipo, $descripcion, $comentario, $accion, $imagen)
 	{ 	
		$sql = "execute up_AgregarRelacionOperacionPerfil '".$this->mill($tabla)."', '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//PENDIENTE
	function actualizarRelacionOperacionPerfil($id, $tabla, $tipo, $descripcion, $comentario, $accion, $imagen)
 	{
   		$sql = "execute up_ModificarRelacionOperacionPerfil '".$this->mill($tabla)."', $id, '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//PENDIENTE
	function eliminarRelacionOperacionPerfil($tabla, $id)
 	{
   		$sql = "execute up_EliminarRelacionOperacionPerfil $tabla, $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarRelacionOperacionPerfil($nro_reg, $nro_hoja, $order, $by, $id_cliente=0, $id_tabla=0, $id_operacion=0, $id_perfil=0, $tipo="G", $tabla="", $descripcion="")
 	{
		if(parent::getTipoBD()==1){
			$tabla = "%".$tabla."%";
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarRelacionOperacionPerfil ".$nro_reg.", $nro_hoja, $order, $by, $id_cliente, $id_tabla, $id_operacion, $id_perfil, '$tipo', '".$this->mill($tabla)."', '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$tabla = "%".$tabla."%";
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT ".$id_cliente." as IdSucursal,RelacionOperacion.IdTabla, RelacionOperacion.IdOperacion, Perfil.IdPerfil, RelacionOperacion.Descripcion, 
		RelacionOperacionCliente.Orden, RelacionOperacion.Tipo, CASE WHEN EXISTS(SELECT * FROM RelacionOperacionPerfil WHERE RelacionOperacionPerfil.IdSucursal = ".$id_cliente." AND RelacionOperacionPerfil.IdTabla = RelacionOperacion.IdTabla AND RelacionOperacionPerfil.IdOperacion = RelacionOperacion.IdOperacion AND RelacionOperacionPerfil.IdPerfil = Perfil.IdPerfil) THEN 'N' ELSE 'A' END as Estado, Tabla.Descripcion as tabla
	FROM Perfil, ( RelacionOperacion
		INNER JOIN (SELECT * from RelacionOperacionCliente WHERE IdSucursal =".$id_cliente." ) RelacionOperacionCliente ON RelacionOperacionCliente.IdTabla = RelacionOperacion.IdTabla AND RelacionOperacionCliente.IdOperacion = RelacionOperacion.IdOperacion and RelacionOperacionCliente.estado='N'
		INNER JOIN Tabla ON RelacionOperacion.IdTabla = Tabla.IdTabla) WHERE perfil.idsucursal=".$id_cliente."and 1=1 ";
			if($id_tabla>0){ $sql = $sql . " AND RelacionOperacion.IdTabla = " . $id_tabla;}
			if($id_operacion>0){ $sql = $sql . " AND RelacionOperacion.IdOperacion = " . $id_operacion;}
			if($id_perfil>0){ $sql = $sql . " AND Perfil.IdPerfil = " . $id_perfil;}
			if($tipo <>"" ){$sql = $sql . " AND RelacionOperacion.Tipo = '" . $tipo . "'";}
			if($tabla <>"" ){$sql = $sql . " AND Tabla.Descripcion LIKE '" . $tabla . "'";}
			if($descripcion <>"" ){$sql = $sql . " AND RelacionOperacion.Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
//return $sql;
		} 	
 	}
	
	function activarRelacionOperacionPerfil($id_cliente=0, $id_tabla=0, $id_operacion=0, $id_perfil=0)
 	{
   		$sql = "execute up_ActivarRelacionOperacionPerfil $id_cliente, $id_tabla, $id_operacion, $id_perfil";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function desactivarRelacionOperacionPerfil($id_cliente=0, $id_tabla=0, $id_operacion=0, $id_perfil=0)
 	{
   		$sql = "execute up_DesactivarRelacionOperacionPerfil $id_cliente, $id_tabla, $id_operacion, $id_perfil";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//PENDIENTE
	function subirRelacionOperacionPerfil($id_cliente=0, $id_tabla=0, $id_operacion=0, $tipo="G")
 	{
   		$sql = "execute up_SubirRelacionOperacionPerfil $id_cliente, $id_tabla, $id_operacion";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//PENDIENTE
	function bajarRelacionOperacionPerfil($id_cliente=0, $id_tabla=0, $id_operacion=0, $tipo="G")
 	{
   		$sql = "execute up_BajarRelacionOperacionPerfil $id_cliente, $id_tabla, $id_operacion";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
}
?>