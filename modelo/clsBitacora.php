<?php
ini_set('memory_limit', '512M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB

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
class clsBitacora extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $sucursal, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $sucursal;		
		parent::__construct($user, $pass);
	}

	
	function insertarBitacora($nombreusuario, $perfil, $idtabla, $accion, $registro, $idsucursal, $idregistro, $idusuario, $idsucursal_usuario)
 	{ 	
		$sql = "execute up_agregarbitacora '".$this->mill($nombreusuario)."','".$this->mill($perfil)."', $idtabla, '".$this->mill($accion)."', '".$this->mill($registro)."', $idsucursal, $idregistro, $idusuario, $idsucursal_usuario";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function consultarBitacora($nro_reg, $nro_hoja, $order, $by, $id, $descripcion,$tipo,$idsucursal)
	{
		if(parent::getTipoBD()==1){//AQUI LLAMA PARA SQLSERVER
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarBitacora ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."', $tipo, $idsucursal";
			return $this->obtenerDataSP($sql);
		}else{//AQUI LLAMA PARA POSTGRESQL
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			
			$sql = "SELECT bitacora.IdBitacora,bitacora.nombreusuario, bitacora.perfil, bitacora.fecha,bitacora.idtabla,bitacora.accion,bitacora.registro,bitacora.idsucursal,bitacora.idregistro,bitacora.idusuario,bitacora.idsucursalusuario, t.descripcion as tabla from bitacora inner join sucursal on sucursal.idsucursal = bitacora.idsucursal inner join empresa on sucursal.idempresa = empresa.idempresa inner join tabla t on t.idtabla=bitacora.idtabla WHERE 1=1";
			$sql = $sql . " AND " . $tipo . " LIKE '" . $descripcion . "'";
			if($idsucursal>0){ $sql = $sql . " AND Sucursal.IdSucursal = " . $idsucursal;}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)." ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			if($nro_reg==0){
				$limit="";
			}else{
				if($total%$nro_reg==0){$total_hojas=(int)($total/$nro_reg);}else{$total_hojas=(int)($total/$nro_reg) + 1;}
				if($total_hojas < $nro_hoja){$nro_hoja=1;}
				$limit = " LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg);
			}
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13).$limit);
			//echo $sql;
		}    
	}
	//PENDIENTE
	function consultarBitacora2($idsucursal, $idBitacora)
 	{
   		$sql = "SELECT * FROM Bitacora where idsucursal = $idsucursal and idBitacora = $idBitacora";
		return $this->obtenerDataSQL($sql);
 	
 	}
	//PENDIENTE
	function consultarSecuenciaTabla($idsucursal, $opcion)
 	{
   		$sql = "select * from secuenciacodigo where idsucursal = ". $idsucursal ." and idtabla =" . $opcion;
		return $this->obtenerDataSQL($sql);
 	
 	}
	
	function consultarDatosAntiguos($idsucursal, $tabla, $opcion, $valor)
 	{
   		$sql = "select * from $tabla where $opcion = '" . $valor ."' ";
		if($idsucursal>0){ $sql = $sql . " AND idsucursal = " . $idsucursal;}
		return $this->obtenerDataSQL($sql);
 	
 	}
}
?>