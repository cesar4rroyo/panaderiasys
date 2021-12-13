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
class clsOpcionMenu extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarOpcionMenu($idmodulo,$descripcion, $idmenuprincipal, $id_tabla, $accion, $dicc, $wap)
 	{ 	
		$sql = "execute up_AgregarOpcionMenu $idmodulo,'".$this->mill($descripcion)."', $idmenuprincipal, $id_tabla, '".$this->mill($accion)."', '".$this->mill($dicc)."', '".$wap."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarOpcionMenu($idopcionmenu, $idmodulo,$descripcion, $idmenuprincipal, $id_tabla, $accion, $dicc, $wap)
 	{
   		$sql = "execute up_ModificarOpcionMenu $idopcionmenu, $idmodulo,'".$this->mill($descripcion)."', $idmenuprincipal, $id_tabla, '".$this->mill($accion)."', '".$this->mill($dicc)."', '".$wap."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
	}else{
			return $this->gError[2];
		}
 	}

	function eliminarOpcionMenu($idopcionmenu)
 	{
   		$sql = "execute up_EliminarOpcionMenu $idopcionmenu";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarOpcionMenu($nro_reg, $nro_hoja, $order, $by, $idopcionmenu, $descripcion)
 	{
		if(parent::getTipoBD()==1){//AQUI LLAMA PARA SQLSERVER
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarOpcionMenu $nro_reg, $nro_hoja, $order, $by, $idopcionmenu, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{//AQUI LLAMA PARA POSTGRESQL
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			
			$sql = "SELECT OpcionMenu.IdOpcionMenu, OpcionMenu.IdModulo, OpcionMenu.Descripcion,OpcionMenu.IdMenuPrincipal,OpcionMenu.Orden, OpcionMenu.IdTabla, OpcionMenu.Accion, Modulo.Descripcion as Modulo, Menu.Descripcion as MenuPrincipal, OpcionMenu.Diccionario, wap
	FROM OpcionMenu inner join Modulo on OpcionMenu.IdModulo = Modulo.IdModulo
		inner join MenuPrincipal as Menu on OpcionMenu.IdMenuPrincipal = Menu.IdMenuPrincipal
	WHERE 1=1";
			$sql = $sql . " AND OpcionMenu.Estado LIKE 'N' ";
			if($idopcionmenu>0){ $sql = $sql . " AND OpcionMenu.IdOpcionMenu =" . $idopcionmenu;}
			$descripcion = "%".$descripcion."%";
			if($descripcion<>""){ $sql = $sql . " AND OpcionMenu.Descripcion LIKE '".$descripcion."'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)." ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)." ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		}	
 	}
	
	function subirOpcionMenu($idopcionmenu=0)
 	{
   		$sql = "execute up_SubirOpcionMenu $idopcionmenu";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function bajarOpcionMenu($idopcionmenu=0)
 	{
   		$sql = "execute up_BajarOpcionMenu $idopcionmenu";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function consultarModuloAjax($idperfil)
 	{
		$sql = "SELECT Distinct opcionmenu.idmodulo, modulo.Descripcion as modulo
		FROM PermisoUsuario 
			INNER JOIN OpcionMenu ON PermisoUsuario.IdOpcionMenu = OpcionMenu.IdOpcionMenu and OpcionMenu.estado='N'
			INNER JOIN Modulo ON OpcionMenu.IdModulo = Modulo.IdModulo
		WHERE PermisoUsuario.IdSucursal = ".$this->gIdSucursal." 
			AND PermisoUsuario.IdPerfil = ".$idperfil;

		return $this->obtenerDataSQL($sql);
 	}

	function consultarMenuPrincipalAjax($idperfil,$idmodulo)
 	{
		$sql = "SELECT Distinct opcionmenu.idmenuprincipal, menuprincipal.Descripcion as menuprincipal
			FROM PermisoUsuario 
			INNER JOIN OpcionMenu ON PermisoUsuario.IdOpcionMenu = OpcionMenu.IdOpcionMenu and OpcionMenu.estado='N'
			INNER JOIN MenuPrincipal ON OpcionMenu.IdMenuPrincipal = MenuPrincipal.IdMenuPrincipal
			WHERE 1=1 ";
		if($idmodulo>0){ $sql = $sql . " AND OpcionMenu.IdModulo =" . $idmodulo;}
		$sql.=" AND PermisoUsuario.IdSucursal = ".$this->gIdSucursal." 
			AND PermisoUsuario.IdPerfil = ".$idperfil;

		return $this->obtenerDataSQL($sql);
 	}

	function consultarOpcionMenuAjax($idperfil,$idmodulo,$idmenuprincipal)
 	{
		$sql = "SELECT permisousuario.idopcionmenu, opcionmenu.idtabla, opcionmenu.descripcion, opcionmenu.accion, opcionmenu.idmenuprincipal, menuprincipal.Descripcion as menuprincipal, menuprincipal.orden as ordenmenu, opcionmenu.idmodulo, modulo.Descripcion as modulo, modulo.orden as moduloorden, modulo.expandido, menuprincipal.expandido as menuexpandido
		FROM PermisoUsuario 
			INNER JOIN OpcionMenu ON PermisoUsuario.IdOpcionMenu = OpcionMenu.IdOpcionMenu and OpcionMenu.estado='N'
			INNER JOIN MenuPrincipal ON OpcionMenu.IdMenuPrincipal = MenuPrincipal.IdMenuPrincipal
			INNER JOIN Modulo ON OpcionMenu.IdModulo = Modulo.IdModulo
		WHERE 1=1 ";
		if($idmodulo>0){ $sql = $sql . " AND OpcionMenu.IdModulo =" . $idmodulo;}
		if($idmenuprincipal>0){ $sql = $sql . " AND OpcionMenu.IdMenuPrincipal = ".$idmenuprincipal;}
		$sql.=" AND PermisoUsuario.IdSucursal = ".$this->gIdSucursal." 
			AND PermisoUsuario.IdPerfil = ".$idperfil."	ORDER BY modulo.orden, OrdenMenu, menuprincipal.Orden";

		return $this->obtenerDataSQL($sql);
 	}
}
?>