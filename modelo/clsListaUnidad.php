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
require_once 'clsProducto.php';
class clsListaUnidad extends clsProducto
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}

	function insertarListaUnidad($idsucursal, $idproducto, $idsucursalproducto, $idunidad, $idunidadbase, $formula, $preciocompra, $preciomanoobra, $precioventa, $precioventa2, $moneda, $precioventa3='0', $precioventa4='0')
 	{ 	
		$sql = "execute up_AgregarListaUnidad ".$this->mill($idsucursal).", ".$this->mill($idproducto).", ".$this->mill($idunidad).", ".$this->mill($idunidadbase).", ".$this->mill($formula).", ".$this->mill($preciocompra).", ".$this->mill($preciomanoobra).", ".$this->mill($precioventa).", '".$this->mill($moneda)."', ".$this->mill($precioventa2).", ".$idsucursalproducto.", ".$precioventa3.", ".$precioventa4;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function insertarListaUnidadSucursales($idempresa, $idsucursal, $idproducto, $idsucursalproducto, $idunidad, $idunidadbase, $formula, $preciocompra, $preciomanoobra, $precioventa, $precioventa2, $moneda, $precioventa3=0, $precioventa4=0)
 	{ 	
		$sql = "execute up_agregarlistaunidadsucursales ".$this->mill($idempresa).", ".$this->mill($idsucursal).", ".$this->mill($idproducto).", ".$this->mill($idunidad).", ".$this->mill($idunidadbase).", ".$this->mill($formula).", ".$this->mill($preciocompra).", ".$this->mill($preciomanoobra).", ".$this->mill($precioventa).", '".$this->mill($moneda)."', ".$this->mill($precioventa2).", ".$idsucursalproducto.", ".$precioventa3.", ".$precioventa4;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}


	function actualizarListaUnidad($id, $idsucursal, $idproducto, $idsucursalproducto, $idunidad, $formula, $preciocompra, $preciomanoobra, $precioventa,$precioventa2, $moneda, $precioventa3='0', $precioventa4='0')
 	{
   		$sql = "execute up_ModificarListaUnidad $id, $idsucursal, ".$this->mill($idproducto).", ".$this->mill($idunidad).", ".$this->mill($formula).", ".$this->mill($preciocompra).", ".$this->mill($preciomanoobra).", ".$this->mill($precioventa).", '".$this->mill($moneda)."', ".$this->mill($precioventa2).", ".$idsucursalproducto.", ".$precioventa3.", ".$precioventa4;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function actualizarListaUnidadBase($id, $idsucursal, $idproducto, $idsucursalproducto, $idunidad, $formula, $preciocompra, $preciomanoobra, $precioventa,$precioventa2, $moneda, $precioventa3, $precioventa4)
 	{
   		$sql = "execute up_ModificarListaUnidadBase $id, $idsucursal, ".$this->mill($idproducto).", ".$this->mill($idunidad).", ".$this->mill($formula).", ".$this->mill($preciocompra).", ".$this->mill($preciomanoobra).", ".$this->mill($precioventa).", '".$this->mill($moneda)."', ".$this->mill($precioventa2).", ".$idsucursalproducto.", ".$precioventa3.", ".$precioventa4;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarListaUnidad($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarListaUnidad $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//VTM : 24/04/2011: AGREGUE NUEVOS PARAMETROS $idproducto
	function consultarListaUnidad($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $idproducto, $idsucursalproducto)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarListaUnidad ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT idlistaunidad, idsucursal, idproducto, L.idunidad, U.descripcion as unidad, L.idunidadbase, UB.descripcion as unidadbase, formula, preciocompra, preciomanoobra, precioventa, precioventa2, moneda FROM ListaUnidad L inner join unidad U on L.idunidad = U.idunidad inner join unidad UB on L.idunidadbase = UB.idunidad WHERE 1=1 ";
			$sql = $sql . " AND IdSucursal= ".$idsucursal." ";
			/*$sql = $sql . " AND Estado LIKE 'N' ";*/
			if($id>0){ $sql = $sql . " AND IdListaUnidad = " . $id;}
			//if($id>0){ $sql = $sql . " AND IdListaUnidad = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND U.descripcion LIKE '" . $descripcion . "'";}
			if($idproducto>0){ $sql = $sql . " AND IdProducto = ".$idproducto." AND idsucursalproducto=".$idsucursalproducto;}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function buscarconxajax($idproducto, $idsucursalproducto, $moneda, $tipocambio)
 	{
			$sql = "SELECT listaunidad.idlistaunidad, listaunidad.idproducto, producto.descripcion as producto, unidad.descripcion as unidad, unidadbase.descripcion as unidadbase, listaunidad.formula, ROUND(CASE WHEN moneda='S' THEN CASE WHEN '".$moneda."'='S' THEN listaunidad.precioventa ELSE listaunidad.precioventa/".$tipocambio." END ELSE CASE WHEN '".$moneda."'='D' THEN listaunidad.precioventa ELSE listaunidad.precioventa*".$tipocambio." END END,3) as precioventa, ROUND(CASE WHEN moneda='S' THEN CASE WHEN '".$moneda."'='S' THEN listaunidad.precioventa2 ELSE listaunidad.precioventa2/".$tipocambio." END ELSE CASE WHEN '".$moneda."'='D' THEN listaunidad.precioventa2 ELSE listaunidad.precioventa2*".$tipocambio." END END,3) as precioventa2,
			ROUND(CASE WHEN moneda='S' THEN CASE WHEN '".$moneda."'='S' THEN listaunidad.precioventa3 ELSE listaunidad.precioventa3/".$tipocambio." END ELSE CASE WHEN '".$moneda."'='D' THEN listaunidad.precioventa3 ELSE listaunidad.precioventa3*".$tipocambio." END END,3) as precioventa3,
			ROUND(CASE WHEN moneda='S' THEN CASE WHEN '".$moneda."'='S' THEN listaunidad.precioventa4 ELSE listaunidad.precioventa4/".$tipocambio." END ELSE CASE WHEN '".$moneda."'='D' THEN listaunidad.precioventa4 ELSE listaunidad.precioventa4*".$tipocambio." END END,3) as precioventa4,
            ROUND(CASE WHEN moneda='S' THEN CASE WHEN '".$moneda."'='S' THEN listaunidad.preciocompra ELSE listaunidad.preciocompra/".$tipocambio." END ELSE CASE WHEN '".$moneda."'='D' THEN listaunidad.preciocompra ELSE listaunidad.preciocompra*".$tipocambio." END END,3) as preciocompra,ROUND(CASE WHEN moneda='S' THEN CASE WHEN '".$moneda."'='S' THEN listaunidad.preciomanoobra ELSE listaunidad.preciomanoobra/".$tipocambio." END ELSE CASE WHEN '".$moneda."'='D' THEN listaunidad.preciomanoobra ELSE listaunidad.preciomanoobra*".$tipocambio." END END,2) as preciomanoobra,listaunidad.moneda, unidad.idunidad, producto.idunidadbase, obtenerStock(producto.idproducto,producto.idunidadbase,".$this->gIdSucursal.", PRODUCTO.IdSucursal) as StockActual, producto.compuesto 
            FROM LISTAUNIDAD 
            inner join PRODUCTO on producto.idproducto=listaunidad.idproducto and producto.idsucursal=listaunidad.idsucursalproducto 
            inner join UNIDAD on unidad.idunidad= listaunidad.idunidad 
            inner join UNIDAD as UNIDADBASE on listaunidad.idunidadbase=unidadbase.idunidad 
            WHERE 1=1  and listaunidad.idsucursal=".$this->gIdSucursal;
			if($idproducto>0){ $sql = $sql . " AND listaunidad.idproducto = ".$idproducto." AND listaunidad.idsucursalproducto=".$idsucursalproducto;}
			ECHO $SQL;
			return $this->obtenerDataSQL($sql);	
			echo $sql;
 	}

	//VTM : 24/04/2011: NUEVO
	function buscarUnidadxIdProducto($idproducto,$idsucursalproducto)
	{
		$sql= "SELECT IdUnidadBase, U.Descripcion as UnidadBase FROM Producto P INNER JOIN UNIDAD U on P.IdUnidadBase = U.IdUnidad WHERE IdProducto= $idproducto and P.idsucursal=".$idsucursalproducto;
		return $this->obtenerDataSQL($sql);	
	}
	
	function buscarprecio($idunidad, $idproducto, $idsucursalproducto)
{
   $sql = "SELECT unidad.descripcion as unidad, unidad.idunidad as idunidad, unidadbase.descripcion as unidadbase, unidadbase.idunidad as idunidadbase, listaunidad.formula, listaunidad.preciocompra, listaunidad.precioventa as precio, listaunidad.precioventa2 as precio2, producto.compuesto from LISTAUNIDAD inner join PRODUCTO on producto.idproducto=listaunidad.idproducto and producto.idsucursal=listaunidad.idsucursalproducto inner join UNIDAD on unidad.IdUnidad= listaunidad.idunidad inner join UNIDAD UNIDADBASE on listaunidad.idunidadbase=unidadbase.idunidad  WHERE listaunidad.idsucursal=".$this->gIdSucursal;
  if(isset($idproducto) && $idproducto!=""){
	$sql = $sql . " AND listaunidad.idproducto = ".$idproducto." AND listaunidad.idsucursalproducto=".$idsucursalproducto;}
  if(isset($idunidad)){
	$sql = $sql . " AND unidad.idunidad =" . $idunidad . "";}
	
   return $this->obtenerDataSQL($sql);			 	
   //echo $sql;
}

}
?>