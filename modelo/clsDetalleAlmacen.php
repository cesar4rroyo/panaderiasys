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
class clsDetalleAlmacen extends clsMovimiento
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}

	function insertarDetalleAlmacen($idmovimiento, $idproducto, $idunidad, $cantidad, $preciocompra, $precioventa, $idsucursalproducto,$idsucursal='',$comentario='')
 	{ 	
 	    if($idsucursal=='') $idsucursal=$this->gIdSucursal;
		$sql = "execute up_AgregarDetalleAlmacen $idmovimiento,".$idsucursal.", $idproducto, $idunidad, $cantidad, $preciocompra, $precioventa, $idsucursalproducto,'$comentario'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	function insertarDetalleAlmacenOut($idmovimiento, $idproducto, $idunidad, $cantidad, $preciocompra, $precioventa, $idsucursalproducto,$comentario='')
 	{ 	
		$sql = "select up_AgregarDetalleAlmacenOut ($idmovimiento,".$this->gIdSucursal.", $idproducto, $idunidad, $cantidad, $preciocompra, $precioventa, $idsucursalproducto,'$comentario') as iddetallemovalmacen";
		return $this->obtenerDataSQL($sql);
 	}

	function eliminarDetalleAlmacen($id)
 	{
   		$sql = "execute up_EliminarDetalleAlmacen $id";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
    
   	function insertarDetalleMovCategoria($idmovimiento, $idsucursal, $idproducto, $iddetallecategoria)
 	{ 	
 	    if($idsucursal=='') $idsucursal=$this->gIdSucursal;
		$sql = "execute up_agregardetallemovcategoria $idmovimiento, $idsucursal, $idproducto, $iddetallecategoria";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 	
	function consultarDetalleAlmacen($nro_reg, $nro_hoja, $order, $by, $id, $descripcion, $idmovimiento)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarDetalleAlmacen ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "select dma.idproducto, codigo, p.descripcion as producto, dma.idunidad, u.descripcion as unidad, cantidad, preciocompra, precioventa,dma.idmovimiento, moneda, p.idsucursal
            from detallemovalmacen dma 
            inner join producto p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal 
            inner join unidad u on u.idunidad=dma.idunidad
            inner join (select idmovimiento,moneda,idsucursal from movimiento union select idmovimiento,moneda,idsucursal from movimientohoy) m on m.idmovimiento=dma.idmovimiento and m.idsucursal=dma.idsucursal  
            where dma.idmovimiento=".$idmovimiento." AND dma.IdSucursal=".$this->gIdSucursal;
			$sql = $sql . " AND dma.Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND dma.IdDetalleAlmacen = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND p.descripcion LIKE '" . $descripcion . "'";}
			if($idmovimiento>0){ $sql = $sql . " AND dma.idmovimiento = " . $idmovimiento;}
			
            //print_R($sql);
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	/*function consultarDetalleAlmacenconAjax($idmovimiento,$iddetalle)
 	{
					
			$sql = "SELECT unidad.descripcion as unidad,unidad.idunidad as idunidad,producto.descripcion as producto, producto.codigo as codpro,producto.idproducto as idproducto,producto.idsucursal as idsucursalproducto,detallemovalmacen.iddetallemovalmacen as iddetalle,detallemovalmacen.preciocompra ,detallemovalmacen.precioventa,peso.descripcion as peso,detallemovalmacen.cantidad as cantidad,movimientohoy.fecha as fecha,movimientohoy.idusuario as idusuario,movimientohoy.moneda as moneda ,(cantidad*detallemovalmacen.precioventa) as subtotal,movimientohoy.idtipodocumento as tipodoc,movimientohoy.comentario as comentario,movimientohoy.idmovimiento as idmovimiento,movimientohoy.numero, kardex, compuesto
             FROM detallemovalmacen 
             inner join producto on detallemovalmacen.idproducto=producto.idproducto and detallemovalmacen.idsucursal=producto.idsucursal 
             inner join unidad on detallemovalmacen.idunidad=unidad.idunidad 
             inner join unidad peso on producto.idmedidapeso=peso.idunidad 
             inner join (select * from movimiento union select * from movimientohoy) as movimientohoy on movimientohoy.idmovimiento=detallemovalmacen.idmovimiento and detallemovalmacen.idsucursal=movimientohoy.idsucursal 
             inner join listaunidad on listaunidad.idproducto=producto.idproducto and unidad.idunidad=listaunidad.idunidad and listaunidad.idsucursal=producto.idsucursal 
             WHERE 1=1 and detallemovalmacen.idsucursal=".$this->gIdSucursal;
			$sql = $sql . " AND producto.idproducto not in (select idproducto from detallemovimientohoy dm inner join movimientohoy v on v.idmovimiento=dm.idmovimiento and v.idsucursal=dm.idsucursal and v.estado='N' inner join detallemovalmacen dma on dm.iddetallemovalmacen=dma.iddetallemovalmacen and dm.idsucursal=dma.idsucursal where dm.idmovimientoref=".$idmovimiento." and dma.idsucursal=".$this->gIdSucursal.")";
			$sql = $sql . " AND detallemovalmacen.Estado = 'N' ";
			if($idmovimiento>0){ $sql = $sql . " AND detallemovalmacen.Idmovimiento = " . $idmovimiento;}
			if($iddetalle>0){ $sql = $sql . " AND detallemovalmacen.iddetallemovalmacen = " . $iddetalle;}
            //echo $sql;									
			return $this->obtenerDataSQL($sql);
			//echo $sql;
 	}*/
        function consultarDetalleAlmacenconAjax($idmovimiento,$iddetalle,$credito='')
 	{
					
			$sql = "SELECT unidad.descripcion as unidad,unidad.idunidad as idunidad,producto.descripcion as producto, producto.codigo as codpro,producto.idproducto as idproducto,
            producto.idsucursal as idsucursalproducto,detallemovalmacen.iddetallemovalmacen as iddetalle,detallemovalmacen.preciocompra ,detallemovalmacen.precioventa,peso.descripcion as peso,
            detallemovalmacen.cantidad as cantidad,movimientohoy.fecha as fecha,movimientohoy.idusuario as idusuario,movimientohoy.moneda as moneda ,(cantidad*detallemovalmacen.precioventa) as subtotal,
            movimientohoy.idtipodocumento as tipodoc,movimientohoy.comentario as comentario,movimientohoy.idmovimiento as idmovimiento,movimientohoy.numero, kardex, compuesto, categoria.bar,producto.idimpresora,
            producto.abreviatura
             FROM detallemovalmacen 
             left join producto on detallemovalmacen.idproducto=producto.idproducto and detallemovalmacen.idsucursal=producto.idsucursal 
             left join categoria on categoria.idcategoria = producto.idcategoria
             left join unidad on detallemovalmacen.idunidad=unidad.idunidad 
             left join unidad peso on producto.idmedidapeso=peso.idunidad 
             left join (select * from movimiento union select * from movimientohoy) as movimientohoy on movimientohoy.idmovimiento=detallemovalmacen.idmovimiento and detallemovalmacen.idsucursal=movimientohoy.idsucursal 
             left join listaunidad on listaunidad.idproducto=producto.idproducto and unidad.idunidad=listaunidad.idunidad and listaunidad.idsucursal=producto.idsucursal 
             WHERE 1=1 and detallemovalmacen.idsucursal=".$this->gIdSucursal;
            if($credito==''){$sql = $sql . " AND producto.idproducto not in (select idproducto from detallemovimientohoy dm inner join movimientohoy v on v.idmovimiento=dm.idmovimiento and v.idsucursal=dm.idsucursal and v.estado='N' inner join detallemovalmacen dma on dm.iddetallemovalmacen=dma.iddetallemovalmacen and dm.idsucursal=dma.idsucursal where dm.idmovimientoref=".$idmovimiento." and dma.idsucursal=".$this->gIdSucursal.")";}
			$sql = $sql . " AND detallemovalmacen.Estado = 'N' ";
			if($idmovimiento>0){ $sql = $sql . " AND detallemovalmacen.Idmovimiento = " . $idmovimiento;}
			if($iddetalle>0){ $sql = $sql . " AND detallemovalmacen.iddetallemovalmacen = " . $iddetalle;}
            //echo $sql;									
			return $this->obtenerDataSQL($sql);
			//echo $sql;
 	}
}
?>