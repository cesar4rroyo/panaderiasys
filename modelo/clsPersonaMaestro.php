<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
require_once 'cado.php';
class clsPersonaMaestro extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarPersonaMaestro($Apellidos, $Nombres, $TipoPersona, $NroDoc, $sexo, $fechanac)
 	{ 	
		if(trim($fechanac)!='') $fechanac="'".$fechanac."'"; else $fechanac='null';
		$sql = "select up_AgregarPersonaMaestroOut ('$Apellidos', '$Nombres', '$TipoPersona', '$NroDoc', '$sexo', $fechanac) as idpersonamaestro";
		return $this->obtenerDataSQL($sql);
	}
	
	function insertarPersonaMaestroOut($Apellidos, $Nombres, $TipoPersona, $NroDoc, $sexo, $fechanac)
 	{ 	
		if(trim($fechanac)!='') $fechanac="'".$fechanac."'"; else $fechanac='null';
		
		if(parent::getTipoBD()==1){//AQUI LLAMA PARA SQLSERVER
			$sql = "execute up_AgregarPersonaMaestroOut '$Apellidos', '$Nombres', '$TipoPersona', '$NroDoc', '$sexo', $fechanac";
			return $this->obtenerDataSP($sql);
		}else{
			$sql = "select up_AgregarPersonaMaestroOut('$Apellidos', '$Nombres', '$TipoPersona', '$NroDoc', '$sexo', $fechanac) as idpersonamaestro";
			return $this->obtenerDataSQL($sql);
			//echo $sql;
		}
 	}

	function actualizarPersonaMaestro($id, $Apellidos, $Nombres, $TipoPersona, $NroDoc, $sexo, $fechanac)
 	{
		if(trim($fechanac)!='') $fechanac="'".$fechanac."'"; else $fechanac='null';
   		$sql = "execute up_ModificarPersonaMaestro $id, '$Apellidos', '$Nombres', '$TipoPersona', '$NroDoc', '$sexo', $fechanac";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarPersonaMaestro($id)
 	{
   		$sql = "execute up_EliminarPersonaMaestro $id ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarPersonaMaestro($nro_reg, $nro_hoja, $order, $by, $id_personamaestro, $buscar_apellido_nombre='', $buscar_nrodoc='', $buscar_sexo='', $buscar_tipopersona='')
	{
		if(parent::getTipoBD()==1){//AQUI LLAMA PARA SQLSERVER
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarAlumnoMaestro ".$nro_reg.", $nro_hoja, '$order', $by, $id_personamaestro, '".$this->mill($Nombres)."'";
			return $this->obtenerDataSP($sql);
		}else{//AQUI LLAMA PARA POSTGRESQL
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$buscar_apellido_nombre = "%".$buscar_apellido_nombre."%";
			$buscar_nrodoc = "%".$buscar_nrodoc."%";
			$buscar_sexo = "%".$buscar_sexo."%";
			$buscar_tipopersona = "%".$buscar_tipopersona."%";
			
			$sql = "SELECT idpersonamaestro, tipopersona, nombres, apellidos, nrodoc, sexo, to_char(FechaNac,'DD/MM/YYYY') as FechaNac, estado
			FROM PersonaMaestro WHERE estado='N'";
			if($id_personamaestro>0){ $sql = $sql . " AND PersonaMaestro.IdPersonaMaestro = " . $id_personamaestro;}
			if($buscar_apellido_nombre <>"" ){$sql = $sql . " AND Apellidos || ' ' || Nombres  LIKE '" . $buscar_apellido_nombre . "'";}
			if($buscar_nrodoc <>"" ){$sql = $sql . " AND nrodoc LIKE '" . $buscar_nrodoc."'" ;}
			if($buscar_sexo <>"" ){$sql = $sql . " AND sexo LIKE '" . $buscar_sexo."'" ;}
			if($buscar_tipopersona <>"" ){$sql = $sql . " AND tipopersona LIKE '" . $buscar_tipopersona."'" ;}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)." ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)." ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		}    
	}
	
	function consultarxNroDoc($tipo,$nrodoc)
	{
		$sql = "Select IdPersonaMaestro, Apellidos, Nombres, TipoPersona, NroDoc, Sexo, to_char(FechaNac,'DD/MM/YYYY') as FechaNac From PersonaMaestro Where Estado='N' ";
		if(isset($tipo) and isset($nrodoc))
		$sql .= " and TipoPersona='".$tipo."' and NroDoc='".$nrodoc."'";
		
		return $this->obtenerDataSQL($sql);
	
	}
}
?>