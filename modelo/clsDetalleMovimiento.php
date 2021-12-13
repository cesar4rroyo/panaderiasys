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
require_once 'clsMovimiento.php';
class clsDetalleMovimiento extends clsMovimiento
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}

	function insertarDetalleMovimiento($idmovimiento, $idmovimientoref, $iddetallemovalmacen)
 	{ 	
		$sql = "execute up_AgregarDetalleMovimiento $idmovimiento, ".$this->gIdSucursal.", $idmovimientoref, $iddetallemovalmacen";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//PENDIENTE
	function eliminarDetalleMovimiento($id)
 	{
   		$sql = "execute up_EliminarDetalleMovimiento $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 	//PENDIENTE
	function consultarDetalleMovimiento($nro_reg, $nro_hoja, $order, $by, $id, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarDetalleMovimiento ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT dm.IdDetalleMovimiento, dm.Descripcion, dm.Estado FROM (select * from detallemovimiento union select * from detallemovimientohoy) as dm WHERE 1=1 ";
			$sql = $sql . " AND dm.Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND dm.IdDetalleMovimiento = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND dm.Descripcion LIKE '" . $descripcion . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	//PENDIENTE
	function consultarDetalleMovimientoconAjax($idmovimiento,$iddetalle)
 	{
					
			$sql = "SELECT unidad.descripcion as unidad,unidad.idunidad as idunidad,producto.descripcion as producto, producto.codigo as codpro,producto.idproducto as idproducto,detallemovalmacen.iddetallemovalmacen as iddetalle,detallemovalmacen.preciocompra ,detallemovalmacen.precioventa,peso.descripcion as peso,detallemovalmacen.cantidad as cantidad,movimientohoy.fecha as fecha,movimientohoy.idusuario as idusuario,movimientohoy.moneda as moneda ,(cantidad*detallemovalmacen.precioventa) as subtotal,movimientohoy.idtipodocumento as tipodoc,movimientohoy.comentario as comentario,movimientohoy.idmovimiento as idmovimiento,movimientohoy.numero 
        		FROM detallemovalmacen 
			inner join producto on detallemovalmacen.idproducto=producto.idproducto 
			inner join unidad on detallemovalmacen.idunidad=unidad.idunidad 
			inner join unidad peso on producto.idmedidapeso=peso.idunidad 
			inner join (select * from detallemovimiento union select * from detallemovimientohoy) as movimientohoy on movimientohoy.idmovimiento=detallemovalmacen.idmovimiento 
			inner join listaunidad on listaunidad.idproducto=producto.idproducto and unidad.idunidad=listaunidad.idunidad WHERE 1=1 ";
			$sql = $sql . " AND detallemovalmacen.Estado = 'N' ";
			if($idmovimiento>0){ $sql = $sql . " AND detallemovalmacen.Idmovimiento = " . $idmovimiento;}
			if($iddetalle>0){ $sql = $sql . " AND detallemovalmacen.iddetallemovalmacen = " . $iddetalle;}
									
			return $this->obtenerDataSQL($sql);
 	}
}
?>