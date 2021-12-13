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
class clsDetalleCompuesto extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarDetalleCompuesto($idsucursal, $idproducto, $idsucursalproducto, $idunidad, $idingrediente, $idsucursalingrediente, $cantidad)
 	{ 	
		$sql = "execute up_agregarDetalleCompuesto ".$idsucursal.",".$idproducto.",".$idsucursalproducto.",".$idunidad.",".$idingrediente.",".$idsucursalingrediente.",".$cantidad;
		
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}

 	}
//PENDIENTE
	function actualizarDetalleCompuesto($id, $codigo, $descripcion, $idcategoria, $idmarca, $idunidadbase, $peso, $idmedidapeso, $fechavencimiento, $stockminimo, $stockmaximo, $stockoptimo, $minimovender, $minimocomprar, $idubicacion, $columna, $fila, $kardex, $compuesto, $comentario, $imagen, $idsucursal, $compartido='N', $tipo='P')
 	{
		if(trim($fechavencimiento)!='') $fechaven="'".$fechavencimiento."'"; else $fechaven='null';
		
   		$sql = "execute up_ModificarDetalleCompuesto $id, '".$this->mill($codigo)."', '".$this->mill($descripcion)."', ".$this->mill($idcategoria).", ".$this->mill($idmarca).", ".$this->mill($idunidadbase).", ".$this->mill($peso).", ".$this->mill($idmedidapeso).", ".$this->mill($fechaven).", ".$this->mill($stockminimo).", ".$this->mill($stockmaximo)." , ".$this->mill($stockoptimo).", ".$this->mill($minimovender).", ".$this->mill($minimocomprar).", ".$this->mill($idubicacion).", ".$this->mill($columna).", ".$this->mill($fila).", '".$this->mill($kardex)."', '".$this->mill($compuesto)."', '".$this->mill($comentario)."', '".$this->mill($imagen)."',".$idsucursal.",'".$compartido."', '".$tipo."'";
		
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarDetalleCompuesto($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarDetalleCompuesto $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarDetalleCompuesto($nro_reg, $nro_hoja, $order, $by, $idproductocompuesto, $idsucursalproducto, $descripcion)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarProducto ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT iddetallecompuesto, P.IdSucursal, P.IdProducto, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY') as FechaVencimiento, StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, Comentario, P.Imagen, P.Estado, Compartido, CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END as categoria, CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END as marca, ub.nombre as ubicacion, P.tipo, cantidad, U.descripcion as unidad, LU.moneda, LU.preciocompra as precio, round(cantidad*LU.preciocompra,2) as subtotal FROM DetalleCompuesto DC inner join Producto P on DC.idingrediente=P.idproducto and DC.idsucursal=P.idsucursal left join MARCA on P.idmarca=marca.idmarca and P.idsucursal=marca.idsucursal left join CATEGORIA on P.idcategoria= categoria.idCategoria and P.idsucursal=categoria.idsucursal left join Ubicacion ub on ub.IdUbicacion= P.IdUbicacion and ub.idsucursal= p.idsucursal INNER JOIN Unidad U on DC.idunidad=U.idunidad inner join LISTAUNIDAD LU on LU.idproducto= DC.IdIngrediente and DC.idsucursal=LU.idsucursal and LU.idunidad=DC.idunidad INNER JOIN SUCURSAL s on S.idsucursal=p.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." WHERE 1=1 ";
			$sql = $sql . " AND P.Estado LIKE 'N' ";
			if($idproductocompuesto>0){ $sql = $sql . " AND DC.IdProducto = " . $idproductocompuesto;}
			$sql = $sql . " AND (P.IdSucursal = " . $idsucursalproducto. " or (P.idsucursal<>" . $idsucursalproducto. " and compartido='S'))";
			if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
			$sql = $sql . " AND P.tipo LIKE 'I'";
			//$sql = $sql . " AND compuesto LIKE 'N'";
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	 	
 	}
	
	function consultarTotal($idproducto,$idsucursal)
 	{
   		$sql = "select case when sum(round(cantidad*LU.preciocompra,2)) is null then 0 else sum(round(cantidad*LU.preciocompra,2)) end as total from detallecompuesto DC inner join LISTAUNIDAD LU on LU.idproducto= DC.IdIngrediente and DC.idsucursal=LU.idsucursal and DC.idunidad=LU.idunidad where DC.idproducto=".$idproducto." and DC.idsucursal=".$idsucursal;
		//echo $sql;
		$rst = $this->obtenerDataSQL($sql);
		$res=$rst->fetchObject();
		return $res->total;
 	}
}
?>