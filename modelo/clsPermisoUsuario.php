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
class clsPermisoUsuario extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	 
	function consultarPermisoUsuario($nro_reg, $nro_hoja, $order, $by, $id_cliente=0, $id_perfil=0, $descripcion="")
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
	   		$sql = "execute up_BuscarPermisoUsuarioP $nro_reg, $nro_hoja, $order, $by, $id_cliente, $id_perfil, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT ".$id_cliente." as IdSucursal, ".$id_perfil." as IdPerfil, OpcionMenu.IdOpcionMenu,  OpcionMenu.Descripcion, CASE WHEN OpcionMenu.IdTabla IS NULL THEN 0 ELSE OpcionMenu.IdTabla END as IdTabla,
	CASE WHEN OpcionMenu.Accion IS NULL THEN '' ELSE OpcionMenu.Accion END as Accion, CASE WHEN CASE WHEN PermisoUsuario1.IdOpcionMenu is null THEN 0 ELSE PermisoUsuario1.IdOpcionMenu END =0 THEN 0 ELSE 1 END as Permiso, MP.descripcion as menuprincipal, M.descripcion as modulo
	FROM OpcionMenu 
	INNER JOIN MenuPrincipal MP on MP.idmenuprincipal=Opcionmenu.idmenuprincipal
	INNER JOIN Modulo M on M.idmodulo=Opcionmenu.idmodulo ";
	if($id_cliente!=1 and $id_perfil!=1) $sql.=" and M.idmodulo <> 1 ";
	$sql.="LEFT JOIN (SELECT IdOpcionMenu FROM PermisoUsuario WHERE IdSucursal = ".$id_cliente." AND IdPerfil = ".$id_perfil.") PermisoUsuario1 ON 
	OpcionMenu.IdOpcionMenu = PermisoUsuario1.IdOpcionMenu  
	WHERE 1=1";
			$sql = $sql." AND OpcionMenu.Estado = 'N' ";
			if($descripcion !=""){ $sql = $sql." AND OpcionMenu.Descripcion LIKE '".$descripcion."'";}			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		}	
 	}
	
	function activarPermisoUsuario($id_cliente=0, $id_perfil=0, $id_opcionmenu=0, $descripcion="", $idtabla =0, $accion="")
 	{
   		$sql = "execute up_ActivarPermisoUsuario $id_cliente, $id_perfil, $id_opcionmenu, '$descripcion', $idtabla, '$accion'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function desactivarPermisoUsuario($id_cliente=0, $id_perfil=0, $id_opcionmenu=0, $descripcion="", $idtabla =0, $accion="")
 	{
   		$sql = "execute up_DesactivarPermisoUsuario $id_cliente, $id_perfil, $id_opcionmenu, '$descripcion', $idtabla, '$accion'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
}
?>