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
class clsRelacionCampo extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	
	function insertarRelacionCampo($tabla, $tipo, $descripcion, $comentario, $accion, $imagen, $dicc)
 	{ 	
		$sql = "execute up_AgregarRelacionCampo '".$this->mill($tabla)."', '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."', '".$this->mill($dicc)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarRelacionCampo($id, $tabla, $tipo, $descripcion, $comentario, $accion, $imagen)
 	{
   		$sql = "execute up_ModificarRelacionCampo '".$this->mill($tabla)."', $id, '".$this->mill($tipo)."', '".$this->mill($descripcion)."', '".$this->mill($comentario)."', '".$this->mill($accion)."', '".$this->mill($imagen)."', '".$this->mill($dicc)."' ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarRelacionCampo($tabla, $id)
 	{
   		$sql = "execute up_EliminarRelacionCampo $tabla, $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarRelacionCampo($nro_reg, $nro_hoja, $order, $by, $id_cliente=0, $id_tabla=0, $id_campo=0, $tipo="G", $descripcion="")
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarRelacionCampo ".$nro_reg.", $nro_hoja, $order, $by, $id_cliente, $id_tabla, $id_campo, $tipo, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";

			$sql = "SELECT RelacionCampo.IdCampo, RelacionCampo.IdTabla, RelacionCampo.IdSucursal, RelacionCampo.Descripcion, RelacionCampo.Orden, RelacionCampo.Tipo, RelacionCampo.Estado, RelacionCampo.Diccionario, C.descripcion as Campo
	FROM RelacionCampo INNER JOIN CAMPO C ON C.idcampo=Relacioncampo.idcampo and relacioncampo.idtabla=C.idtabla WHERE 1=1 ";
			if($id_tabla>0){ $sql = $sql . " AND RelacionCampo.IdTabla = " . $id_tabla;}
			if($id_campo>0){ $sql = $sql . " AND RelacionCampo.IdCampo = " . $id_campo;}
			if($id_cliente>0){ $sql = $sql . " AND RelacionCampo.IdSucursal = " . $id_cliente;}
			if($tipo <>"" ){$sql = $sql . " AND RelacionCampo.Tipo = '".$tipo."'";}
			if($descripcion <>"" ){$sql = $sql . " AND RelacionCampo.Descripcion LIKE '".$descripcion."'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	
 	}
	
	function activarRelacionCampo($id_cliente=0, $id_tabla=0, $id_campo=0, $tipo="G")
 	{
   		$sql = "execute up_ActivarRelacionCampo $id_cliente, $id_tabla, $id_campo, '$tipo'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function desactivarRelacionCampo($id_cliente=0, $id_tabla=0, $id_campo=0, $tipo="G")
 	{
   		$sql = "execute up_DesactivarRelacionCampo $id_cliente, $id_tabla, $id_campo, '$tipo'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function subirRelacionCampo($id_cliente=0, $id_tabla=0, $id_campo=0, $tipo="G")
 	{
   		$sql = "execute up_SubirRelacionCampo $id_cliente, $id_tabla, $id_campo, '$tipo'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function bajarRelacionCampo($id_cliente=0, $id_tabla=0, $id_campo=0, $tipo="G")
 	{
   		$sql = "execute up_BajarRelacionCampo $id_cliente, $id_tabla, $id_campo, '$tipo'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
}
?>