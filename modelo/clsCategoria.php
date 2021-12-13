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
class clsCategoria extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarCategoria($idsucursal, $descripcion, $abreviatura, $idcategoriaref, $codigoorden, $imagen,$orden,$idimpresora='0',$comida='N',$bar='N')
 	{ 	
		/*$sql = "execute up_agregarCategoria $idsucursal,'".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($idcategoriaref)."', '".$this->mill($codigoorden)."', '".$this->mill($imagen)."'";
		$res = $this->ejecutarSP($sql);*/
		$sql = "select up_agregarCategoria (".$this->mill($idsucursal).", '".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', ".$this->mill($idcategoriaref).", '".$this->mill($codigoorden)."', '".$this->mill($imagen)."','".$this->mill($orden)."','".$this->mill($idimpresora)."','S','$comida','$bar') as idcategoria";
		return $this->obtenerDataSQL($sql);
	}

	function actualizarCategoria($id, $idsucursal, $descripcion, $abreviatura, $idcategoriaref, $codigoorden, $imagen,$orden,$idimpresora='0',$comida='N',$bar='N')
 	{
   		$sql = "execute up_ModificarCategoria $id , $idsucursal, '".$this->mill($descripcion)."', '".$this->mill($abreviatura)."', '".$this->mill($idcategoriaref)."', '".$this->mill($codigoorden)."', '".$this->mill($imagen)."','".$this->mill($orden)."','".$this->mill($idimpresora)."','S','$comidad','$bar'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarCategoria($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarCategoria $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarCategoria($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $reporte='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarCategoria ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			//SE MODIFIKO PARA VER SI ERA PARA UN REPORTE
            if($reporte<>""){
                  $sql = "SELECT 0, vIdCategoria as IdCategoria, ".$idsucursal." as idSucursal, vDescripcion as Descripcion,vAbreviatura as Abreviatura, vIdCategoriaRef as IdCategoriaRef,vDescripcionRef as DescripcionRef,vImagen as Imagen,vNivel as Nivel,vCodigoOrden as CodigoOrden,vSecuencia as Secuencia,vOrden as Orden,vComida as Comida from up_buscarcategoriaproductoarbol(".$idsucursal.")
                   UNION SELECT 0, 0 as IdCategoria, ".$idsucursal." as idSucursal, 'NIGUNO' as Descripcion,'N' as Abreviatura,0 as IdCategoriaRef,'' as DescripcionRef,'' as Imagen,1 as Nivel,'0' as CodigoOrden,0 as Secuencia,0 as Orden,'N' as Comida from up_buscarcategoriaproductoarbol(".$idsucursal.") WHERE 1=1";
                  //$sql = $sql . " AND Estado LIKE 'N' and IdSucursal= ".$idsucursal." ";
            }else{
				$sql = "SELECT vIdCategoria as IdCategoria, ".$idsucursal.", vDescripcion as Descripcion,vAbreviatura as Abreviatura, vIdCategoriaRef as IdCategoriaRef, vDescripcionRef as DescripcionRef, vImagen as Imagen, vNivel as Nivel,vCodigoOrden as CodigoOrden,vSecuencia as Secuencia,vOrden as Orden,vIdImpresora as IdImpresora,vImpresora as Impresora,vComida as Comida from up_buscarcategoriaproductoarbol(".$idsucursal.") WHERE 1=1 ";
			}
			//$sql = $sql . " AND Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND vIdCategoria = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND vDescripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			//SE MODIFIKO PARA VER SI ERA PARA UN REPORTE
            if($reporte<>""){
                  return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,9,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13));
            }else{
                  return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
            }
			//return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
			} 	 	
 	}

	function verificaExisteDescripcion($nombre)
 	{
				
		$sql = "SELECT * FROM Categoria WHERE Estado = 'N' ";
		if($nombre !=""){ $sql = $sql." AND Descripcion like '".$nombre."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
     
    function insertarDetalleCategoria($id,$idcategoria,$idsucursal,$descripcion, $abreviatura)
 	{ 	
		$sql = "execute up_agregardetalleCategoria $id,$idcategoria,".$this->mill($idsucursal).", '".$this->mill($descripcion)."', '".$this->mill($abreviatura)."'";
		$res = $this->ejecutarSP($sql);
        if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarDetalleCategoria($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarDetalleCategoria $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}	
}
?>