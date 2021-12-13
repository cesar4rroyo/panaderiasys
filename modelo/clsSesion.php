<?php
require_once 'cado.php';
class clsSesion extends clsAccesoDatos
{
	private $usuario;
	private $clave;

	// Constructor de la clase
	function __construct($user, $pass){
		$this->usuario = $user;
		$this->clave = $pass;		
		parent::__construct($user, $pass);
	}
	
	

	function verificaSesion()
 	{ 	
		if(parent::getTipoBD()==1){
   		$sql = "execute up_VerificaSesion '".$this->usuario."', '".$this->clave."'";
		return $this->obtenerDataSP($sql);
		}else{
		$sql = "SELECT S.IdEmpresa as IdEmpresa, E.RazonSocial as NombreEmpresa, Usuario.IdSucursal, S.razonsocial as NombreSucursal, Usuario.IdUsuario, Usuario.NombreUsuario, PMP.Nombres, PMP.Apellidos, Usuario.IdPerfil, Usuario.NroFilaMostrar, OpcionMenuDefecto, Compartido, E.situacion, S.logo, Persona.idpersona, PMP.IdPersonaMaestro, E.ruc,S.Direccion,S.ruc as rucsucursal
	FROM Usuario 
		INNER JOIN Persona ON Usuario.IdPersona = Persona.IdPersona and Persona.idsucursal=Usuario.idsucursal 
        INNER JOIN RolPersona RP1 ON RP1.IdPersona=Persona.IdPersona and RP1.idsucursal=Persona.idsucursal and RP1.IdRol=1 
        INNER JOIN PersonaMaestro PMP ON PMP.IdPersonaMaestro=Persona.IdPersonaMaestro
		INNER JOIN Sucursal S ON Usuario.IdSucursal = S.IdSucursal
		INNER JOIN Empresa E ON S.IdEmpresa = E.IdEmpresa
	WHERE NombreUsuario = '".$this->usuario."' AND clave = '".$this->clave."' AND Usuario.Estado = 'N'";
		return $this->obtenerDataSQL($sql);
		}
 	}
	
	function insertarParametrizacionBase($idempresa, $idsucursal)
 	{ 	
		$sql = "execute up_agregarparametrizacionbase $idempresa, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

}
?>