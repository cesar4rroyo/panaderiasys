<?php
session_start();
require_once 'clsTabla.php';
class clsUsuario extends clsTabla
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	
	function insertarUsuario($id_cliente, $id_usuario, $nombre_usuario, $pass, $id_persona, $id_perfil, $nro_fila, $opcionmenudefecto)
 	{ 	
		$sql = "execute up_AgregarUsuario $id_cliente, '$nombre_usuario', '$pass', $id_persona, $id_perfil, $nro_fila, $opcionmenudefecto";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function actualizarUsuario($id_cliente, $id_usuario, $nombre_usuario, $pass, $id_persona, $id_perfil, $nro_fila, $opcionmenudefecto)
 	{
   		$sql = "execute up_ModificarUsuario $id_cliente, $id_usuario, '$nombre_usuario', '$pass', $id_persona, $id_perfil, $nro_fila, $opcionmenudefecto";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			//return $this->gError[2];
			return 1;
		}
 	}

	function eliminarUsuario($id_tabla, $id)
 	{
   		$sql = "execute up_EliminarUsuario $id_tabla, $id ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarUsuario($nro_reg, $nro_hoja, $order, $by, $id_cliente, $id_persona, $descripcion, $id_usuario=0)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarUsuario ".$nro_reg.", $nro_hoja, $order, $by, $id_cliente, $id_persona, '".$this->mill($descripcion)."', $id_usuario";
			return $this->obtenerDataSP($sql);
 		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT Usuario.IdUsuario,Usuario.IdSucursal, Usuario.NombreUsuario, Usuario.Clave, Usuario.IdPersona, Usuario.IdPerfil, Usuario.Estado, Usuario.NroFilaMostrar, P.descripcion as perfil, opcionmenudefecto, idmodulo, idmenuprincipal
	FROM Usuario INNER JOIN PERFIL P ON P.idperfil=usuario.idperfil and P.idsucursal=usuario.idsucursal LEFT JOIN OpcionMenu OP on OP.idopcionmenu=Usuario.opcionmenudefecto WHERE 1=1";
			$sql = $sql." AND Usuario.Estado = 'N' ";
			if($id_cliente>0){ $sql = $sql . " AND Usuario.IdSucursal =" . $id_cliente;}
			if($id_persona>0){ $sql = $sql . " AND Usuario.IdPersona =" . $id_persona;}
			if($id_usuario>0){ $sql = $sql . " AND Usuario.IdUsuario =" . $id_usuario;}
			if($descripcion !=""){ $sql = $sql." AND Usuario.NombreUsuario LIKE '".$descripcion."'";}			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		}
 	}
	
	function actualizarUsuariodesdePerfil($id_cliente, $id_usuario, $nro_fila)
 	{
   		$sql = "update Usuario set nrofilamostrar=$nro_fila where idsucursal=$id_cliente and idusuario=$id_usuario";
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
	}
	
	function cambiarclaveUsuario($id_cliente, $id_usuario, $clavenueva)
 	{ 	
   		$sql = "execute up_cambiarclaveusuario $id_cliente, $id_usuario, '$clavenueva'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $sql.$this->gError[2];
		}
 	}
	
	function verificaExisteNombreUsuario($nombre)
 	{
				
		$sql = "SELECT Usuario.IdUsuario,Usuario.IdSucursal, Usuario.NombreUsuario, Usuario.Clave, Usuario.IdPersona, Usuario.IdPerfil, Usuario.Estado, Usuario.NroFilaMostrar
FROM Usuario WHERE 1=1";
		$sql = $sql." AND Usuario.Estado = 'N' ";
		if($nombre !=""){ $sql = $sql." AND Usuario.NombreUsuario like '".$nombre."'";}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
}
?>