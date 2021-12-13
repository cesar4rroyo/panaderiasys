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
class clsGeneral extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}
	//PENDIENTE
	function insertar($descripcion, $abreviatura)
 	{ 	
		$sql = "execute up_AgregarGestion '$descripcion', '$abreviatura' ";
		return $this->ejecutarSP($sql);
 	}
	//PENDIENTE
	function actualizar($idGestion, $descripcion, $abreviatura)
 	{
   		$sql = "execute up_ModificarGestion $idGestion, '$descripcion', '$abreviatura'";
		return $this->ejecutarSP($sql);
 	}
	//PENDIENTE
	function eliminar($idGestion)
 	{
   		$sql = "execute up_EliminarGestion $idGestion";
		return $this->ejecutarSP($sql);
 	}
	//PENDIENTE
	function consultar($nro_reg, $nro_hoja, $campo, $valor, $order=1, $by="ASC", $id_cliente = 0)
 	{
		if(parent::getTipoBD()==1){
			if($id_cliente==0){
				$id_cliente = $this->gIdSucursal;
			}
			/*$valor = utf8_decode($valor);
			$valor = str_replace("\\\\" ,"\\", $valor);
			$valor = str_replace("\\\"" ,"\"", $valor);
			$valor = str_replace("'" ,"''''", $valor);*/
			if($campo>1){
				$valor = "'%".($valor)."%'";
			}else{
				$valor = "'".($valor)."'";
			}
			$sql = "execute up_BuscarTablaGrilla ".$id_cliente.", ".$this->gIdTabla.", ".$nro_reg.", $nro_hoja, $campo, ".$valor.", $order, '$by'";
			return $this->obtenerDataSP($sql);
		}else{
			$rst = $this->obtenerDataSQL("SELECT descripcion FROM Campo WHERE IdTabla = ".$this->gIdTabla." AND IdCampo = " . $campo . "");
			$filtro = $rst->fetch();
			$campo_filtro = $filtro["descripcion"].".".$filtro["descripcion"];
			if($campo>1){
				$valor = "'%".($valor)."%'";
			}else{
				$valor = "'".($valor)."'";
			}
			$rst = $this->obtenerDataSQL("SELECT campo.idcampo, campo.descripcion
											FROM Campo
											WHERE Campo.IdTabla = ".$this->gIdTabla."
											ORDER BY Campo.IdCampo");
			$data = $rst->fetchAll();
			$cuenta = 0;
			foreach($data as $registro){
				$cuenta = $cuenta  + 1;	
				if($cuenta == 1){
					$cid1 = $registro["idcampo"];
					$c1 = $registro["descripcion"];
				}
				if($cuenta == 2){
					$cid2 = $registro["idcampo"];
					$c2 = $registro["descripcion"];
				}
				if($cuenta > 2){
					$campos = $campos.", ".$registro["descripcion"];
					$campos_select = $campos_select.", ISNULL(".$registro["descripcion"].",'') as ".$registro["descripcion"];
					$inner = $inner.chr(13)."left join (select idregistro, registro as ".$registro["descripcion"]." from Registro where IdTabla = ".$this->gIdTabla." and IdCampo = ".$registro["idcampo"]." and (IdSucursal = ".$this->gIdSucursal." OR IdSucursal = IdSucursal - ".$this->gIdSucursal.") ) ".$registro["descripcion"]." on Registro.IdRegistro = ".$registro["descripcion"].".IdRegistro";
				}	
			}
			
			if($cuenta == 1){
			$cadena = "select Registro.IdRegistro as " . $c1 . "
			from Registro 
			where (IdSucursal = " . $this->gIdSucursal . " OR IdSucursal = IdSucursal - " . $this->gIdSucursal . ") and IdTabla = " . $this->gIdTabla . " and Registro.IdCampo = " . $cid1;
			}
			if($cuenta == 2){
			$cadena = "select Registro.IdRegistro as " . $c1 . ", Registro.Registro as " . $c2 . "
			from Registro
			where (IdSucursal = " . $this->gIdSucursal . " OR IdSucursal = IdSucursal - " . $this->gIdSucursal . ") and IdTabla = " . $this->gIdTabla . " and Registro.IdCampo = " . $cid2;
			}
			
			if($cuenta >2){
			$cadena = "select Registro.IdRegistro as " . $c1 . ", Registro.Registro as " . $c2 . $campos . "
			from Registro " . $inner . "
			where Estado = " . chr(39) . "N" . chr(39) . " and  (IdSucursal = " . $this->gIdSucursal . " OR IdSucursal = IdSucursal - " . $this->gIdSucursal . ") and IdTabla = " . $this->gIdTabla . " and Registro.IdCampo = " . $cid2;
			}
			
			if($campo > 0){
				if($campo == 1){
					$cadena = $cadena.chr(13)."	AND Registro.IdRegistro = " . $valor;
				}
				if($campo == 2){
					$cadena = $cadena.chr(13)."	AND Registro.Registro LIKE ".$valor."";
				}
				
				if($campo >2){
					$cadena = $cadena.chr(13)."	AND ISNULL(" . $campo_filtro . ",'') LIKE ".$valor."";
				}
			
			}	
						
			$rst = $this->obtenerDataSQL($cadena.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($cadena,7,strlen($cadena)-7)." ".chr(13)."	ORDER BY " . ($order+1) . " " . $by . chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		}
 	}
}
?>