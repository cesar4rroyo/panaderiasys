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
class clsProducto extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarProducto($idsucursal, $codigo, $descripcion, $idcategoria, $idmarca, $idunidadbase, $peso, $idmedidapeso, $fechavencimiento, $stockminimo, $stockmaximo, $stockoptimo, $minimovender, $minimocomprar, $idubicacion, $columna, $fila, $kardex, $compuesto, $comentario, $imagen, $compartido='N', $tipo='P',$abreviatura='',$idimpresora=0)
 	{ 	
		if(trim($fechavencimiento)!='') $fechaven="'".$fechavencimiento."'"; else $fechaven='null';
		$sql = "select up_AgregarProductoout (".$this->mill($idsucursal).", '".$this->mill($codigo)."', '".$this->mill($descripcion)."', ".$this->mill($idcategoria).", ".$this->mill($idmarca).", ".$this->mill($idunidadbase).", ".$this->mill($peso).", ".$this->mill($idmedidapeso).", ".$this->mill($fechaven).", ".$this->mill($stockminimo).", ".$this->mill($stockmaximo).", ".$this->mill($stockoptimo).", ".$this->mill($minimovender).", ".$this->mill($minimocomprar).", ".$this->mill($idubicacion).", ".$this->mill($columna).", ".$this->mill($fila).", '".$this->mill($kardex)."', '".$this->mill($compuesto)."', '".$this->mill($comentario)."', '".$this->mill($imagen)."','".$compartido."','".$tipo."','".$this->mill($abreviatura)."',".$idimpresora.") as idproducto";
		/*$sql = "execute up_AgregarProducto '".$this->mill($idempresa)."', '".$this->mill($idsucursal)."', '".$this->mill($codigo)."', '".$this->mill($descripcion)."', '".$this->mill($idcategoria)."', '".$this->mill($idmarca)."', '".$this->mill($idunidadbase)."', '".$this->mill($peso)."', '".$this->mill($idmedidapeso)."', '".$this->mill($fechavencimiento)."', '".$this->mill($stockminimo)."', '".$this->mill($stockmaximo)."', '".$this->mill($stockoptimo)."', '".$this->mill($minimovender)."', '".$this->mill($minimocomprar)."', '".$this->mill($idubicacion)."', '".$this->mill($columna)."', '".$this->mill($fila)."', '".$this->mill($kardex)."', '".$this->mill($compuesto)."', '".$this->mill($comentario)."', '".$this->mill($imagen)."'";*/
		return $this->obtenerDataSQL($sql);

 	}

	function actualizarProducto($id, $codigo, $descripcion, $idcategoria, $idmarca, $idunidadbase, $peso, $idmedidapeso, $fechavencimiento, $stockminimo, $stockmaximo, $stockoptimo, $minimovender, $minimocomprar, $idubicacion, $columna, $fila, $kardex, $compuesto, $comentario, $imagen, $idsucursal, $compartido='N', $tipo='P',$abreviatura='',$idimpresora=0)
 	{
		if(trim($fechavencimiento)!='') $fechaven="'".$fechavencimiento."'"; else $fechaven='null';
		
   		$sql = "execute up_ModificarProducto $id, '".$this->mill($codigo)."', '".$this->mill($descripcion)."', ".$this->mill($idcategoria).", ".$this->mill($idmarca).", ".$this->mill($idunidadbase).", ".$this->mill($peso).", ".$this->mill($idmedidapeso).", ".$this->mill($fechaven).", ".$this->mill($stockminimo).", ".$this->mill($stockmaximo)." , ".$this->mill($stockoptimo).", ".$this->mill($minimovender).", ".$this->mill($minimocomprar).", ".$this->mill($idubicacion).", ".$this->mill($columna).", ".$this->mill($fila).", '".$this->mill($kardex)."', '".$this->mill($compuesto)."', '".$this->mill($comentario)."', '".$this->mill($imagen)."',".$idsucursal.",'".$compartido."', '".$tipo."','".$abreviatura."',".$idimpresora;
		
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarProducto($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarProducto $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarProducto($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $buscar_categoria=0,$buscar_marca=0,$fechainicio='',$fechafin='', $compartido='', $tipo='', $compuesto='')
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
			
			$sql = "SELECT P.IdProducto, P.IdSucursal, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY') as FechaVencimiento, StockMinimo, StockMaximo, StockOptimo, Minimovender, 
            MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, Comentario, P.Imagen, P.Estado, Compartido, CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END as categoria, 
            CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END as marca, ub.nombre as ubicacion, tipo,P.abreviatura, im.idimpresora,im.nombre as impresora ,P.idproductoref
            FROM Producto P 
            left join MARCA on P.idmarca=marca.idmarca and P.idsucursal=marca.idsucursal 
            left join CATEGORIA on P.idcategoria= categoria.idCategoria and P.idsucursal=categoria.idsucursal 
            left join Ubicacion ub on ub.IdUbicacion= P.IdUbicacion and ub.idsucursal= p.idsucursal
            left join impresora im on im.idimpresora=P.idimpresora and im.idsucursal=P.idsucursal 
            INNER JOIN SUCURSAL s on S.idsucursal=p.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." WHERE 1=1 ";
			$sql = $sql . " AND P.Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND P.IdProducto = " . $id;}
            
			$sql = $sql . " AND (P.IdSucursal = " . $idsucursal. " or (P.idsucursal<>" . $idsucursal. " and compartido='S'))";
            
			if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
			if($buscar_categoria > 0){$sql = $sql . " AND (P.IdCategoria = ".$buscar_categoria." or categoria.idcategoriaref=".$buscar_categoria.")";}
			if($buscar_marca > 0){$sql = $sql . " AND P.IdMarca = '" . $buscar_marca . "'";}
			if($fechainicio<>''){$sql = $sql . " AND (FechaVencimiento >= '" . $fechainicio . " 00:00:00.000' OR FechaVencimiento is null)";}
			if($fechafin<>''){$sql = $sql . " AND (FechaVencimiento <= '" . $fechafin . " 23:59:59.999'  OR FechaVencimiento is null)";}
			if($compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $compartido."'" ;}
			if($tipo <>"" ){$sql = $sql . " AND tipo LIKE '" . $tipo."'" ;}
			if($compuesto <>"" ){$sql = $sql . " AND compuesto LIKE '" . $compuesto."'" ;}
			
            if($orden=="Abreviatura"){
                $order="P.".$order;
            }
            
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	 	
 	}

	function buscarxidproductoyidunidad($idproducto, $idsucursalproducto, $idunidad){
	   $sql = "SELECT producto.idproducto, producto.codigo, producto.descripcion as producto, categoria.descripcion as categoria, marca.descripcion as marca, U.Descripcion as unidad, producto.stockminimo, ub.nombre as ubicacion, producto.columna, producto.fila, producto.kardex, producto.estado, preciocompra, preciomanoobra, precioventa, precioventa2, precioventa3, precioventa4, idlistaunidad,producto.abreviatura, obtenerStock(PRODUCTO.idproducto,PRODUCTO.idunidadbase,".$this->gIdSucursal.", PRODUCTO.IdSucursal) as Stock,
       producto.compuesto,i.idimpresora,i.nombre as impresora,i.ip as ipimpresora,categoria.bar,producto.idproductoref 
       FROM PRODUCTO 
       left join MARCA on producto.idmarca=marca.idmarca and producto.idsucursal=marca.idsucursal 
       left join CATEGORIA on producto.idcategoria= categoria.idCategoria and producto.idsucursal=categoria.idsucursal
       left join IMPRESORA i on i.idimpresora=producto.idimpresora and i.idsucursal=categoria.idsucursal 
       left join Ubicacion ub on ub.IdUbicacion= producto.IdUbicacion and ub.IdSucursal=producto.idsucursal 
       inner join LISTAUNIDAD LU on LU.idproducto= producto.Idproducto and producto.idsucursal=LU.idsucursal 
       INNER JOIN UNIDAD U ON U.idunidad=LU.idunidad 
       INNER JOIN SUCURSAL s on S.idsucursal=producto.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']."
        WHERE 1=1";
	   if(isset($idproducto)){
		$sql = $sql . " AND producto.idproducto = ".$idproducto." AND idsucursalproducto=$idsucursalproducto and producto.idsucursal=".$idsucursalproducto;}
	   if($idunidad!=""){
		$sql = $sql . " AND LU.idunidad =" . $idunidad ;}

	   return $this->obtenerDataSQL($sql);  		 	
	 }

	function consultarProductoInterna($nro_reg, $nro_hoja, $order, $by, $id, $descripcion, $buscar_categoria=0,$buscar_marca=0,$codigo='',$tipo='',$kardex='')
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
			
			$sql = "SELECT P.IdProducto, P.IdSucursal,P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso, FechaVencimiento, StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, Comentario, P.Imagen, P.Estado, CASE WHEN C.descripcion IS NULL THEN 'NINGUNO' ELSE C.descripcion END as categoria, CASE WHEN M.descripcion IS NULL THEN 'NINGUNO' ELSE M.descripcion END as marca, ub.nombre as ubicacion, U.Descripcion as unidad, LU.moneda, LU.preciocompra, LU.precioventa, UM.Descripcion as medida, case when p.idproducto in (69,98,104,101) and p.idsucursal=1 then round(sp.stockbase*lu.precioventa,2) else sp.stockbase end as stock ,P.abreviatura 
            FROM Producto P 
            left JOIN Categoria C ON C.idcategoria=p.idcategoria and p.idsucursal=C.idsucursal 
            LEFT JOIN MARCA M ON M.idmarca=P.idmarca and P.idsucursal=M.idsucursal 
            left join Ubicacion ub on ub.IdUbicacion= P.IdUbicacion and ub.idsucursal= p.idsucursal 
            inner join LISTAUNIDAD LU on LU.idproducto= P.Idproducto  and P.idsucursal=LU.idsucursal and LU.idunidad=P.idunidadbase AND LU.IDSUCURSAL=LU.IDSUCURSALPRODUCTO  
            INNER JOIN UNIDAD U ON P.idunidadbase=U.idunidad 
            left join stockproducto sp on sp.idproducto=p.idproducto and p.idsucursal=sp.idsucursal and p.idunidadbase=sp.idunidad and sp.idsucursalproducto=p.idsucursal
            INNER JOIN UNIDAD UM ON P.idmedidapeso=UM.idunidad where 1=1"; 
            //"WHERE P.idSucursal=".$this->gIdSucursal." and 1=1 "; quitado and LU.idsucursalProducto=".$this->gIdSucursal."
			$sql = $sql . " AND P.Estado = 'N' ";
			if($id>0){ $sql = $sql . " AND P.IdProducto = " . $id;}
            $sql = $sql . " AND (P.IdSucursal = " . $this->gIdSucursal. " or (P.idsucursal<>" . $this->gIdSucursal. " and compartido='S'))";
			if($descripcion <>"" ){$sql = $sql . " AND UPPER(P.Descripcion) LIKE UPPER('" . $descripcion . "')";}
			if($buscar_categoria > 0){$sql = $sql . " AND (P.IdCategoria = ".$buscar_categoria." or C.idcategoriaref=".$buscar_categoria.")";}
			if($buscar_marca > 0){$sql = $sql . " AND P.IdMarca = '" . $buscar_marca . "'";}
			if($codigo <>"" ){$codigo = "%".$codigo."%";$sql = $sql . " AND P.Codigo LIKE '" . $codigo . "'";}
			if($tipo <>"" ){$sql = $sql . " AND P.tipo LIKE '" . $tipo."'" ;}
			if($kardex <>"" ){$sql = $sql . " AND kardex LIKE '" . $kardex."'" ;}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
            //print_R($rst);
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
            
            return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function generaCodigo(){
		$sql="select codigo from producto where idsucursal=".$this->gIdSucursal." ORDER BY producto.idproducto DESC LIMIT 1";
		$registro=$this->obtenerDataSQL($sql);
		if($registro->rowCount()>0){
			$dato=$registro->fetchObject();
			$num= $dato->codigo;
			if($num=='999' or strlen($num)>3){
				$l=strlen($num);
				$num=$num+1;
				$num=str_pad($num,$l,"0",STR_PAD_LEFT);
			}else{
				$num=$num+1;
				$num=str_pad($num,3,"0",STR_PAD_LEFT);
			}
		}else{
			$num="001";
		}
		return $num;
	}
	
	function consultarProductoReporte($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $buscar_categoria=0,$buscar_marca=0,$fechainicio='',$fechafin='', $compartido='', $tipo='', $compuesto='',$jornada=0, $idsalon=0, $comida='N')
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
			
			$sql = "SELECT P.IdProducto, P.IdSucursal, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY') as FechaVencimiento, 
            StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, P.Comentario, P.Imagen, P.Estado, Compartido, CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END as categoria, 
            CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END as marca, ub.nombre as ubicacion, P.tipo, U.descripcion as unidad, SUM(dma.cantidad) as monto,count(1) as veces,ROW_NUMBER() OVER(Order by SUM(dma.cantidad) desc) as Puesto,P.abreviatura 
            FROM Producto P 
            left join MARCA on P.idmarca=marca.idmarca and P.idsucursal=marca.idsucursal 
            left join CATEGORIA on P.idcategoria= categoria.idCategoria and P.idsucursal=categoria.idsucursal 
            left join Ubicacion ub on ub.IdUbicacion= P.IdUbicacion and ub.idsucursal= p.idsucursal 
            INNER JOIN SUCURSAL s on S.idsucursal=p.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." 
            INNER JOIN DetalleMovAlmacen dma on dma.idproducto=P.idproducto and dma.idsucursalproducto=P.idsucursal 
            INNER JOIN (select * from Movimiento union all select * from movimientohoy) M on M.idmovimiento=dma.idmovimiento and M.idsucursal=dma.idsucursal and idtipomovimiento=2 and M.estado='N'
            inner join (select * from detallemovimiento union all select * from detallemovimientohoy) dm on dm.idmovimiento=M.idmovimiento and dm.idsucursal=M.idsucursal and dma.iddetallemovalmacen=dm.iddetallemovalmacen 
            INNER JOIN Unidad U on dma.idunidad=U.idunidad WHERE 1=1 ";
			$sql = $sql . " AND P.Estado LIKE 'N' ";
			if($idsalon!="0" && $jornada==1) $sql.=" and dm.idmovimientoref in 
            (select idmovimiento from movimiento 
            where idmesa in (select idmesa from mesa where idsalon=$idsalon and idsucursal=m.idsucursal)
            and idsucursal=M.idsucursal and estado='N' and situacion='P')";
			/*if($jornada==1){
				require_once 'clsMovCaja.php';
				$objMovCaja = new clsmovCaja($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
				if($idcaja>0){
					//$inicio=$objMovCaja->consultarultimaaperturafecha($fechainicio,$idcaja);
					$inicio=$objMovCaja->consultaranteriorcierrefecha($fechainicio,$idcaja);
					$fin=$objMovCaja->consultarultimocierrefecha($fechainicio,$idcaja);
				}else{
					//$inicio=$objMovCaja->consultarultimaaperturafecha($fechainicio);
					$inicio=$objMovCaja->consultaranteriorcierrefecha($fechainicio,$idcaja);
					$fin=$objMovCaja->consultarultimocierrefecha($fechainicio);
				}
				$sql = $sql . " AND m.idmovimiento >= ".$inicio;
				if($fin>0 and $fin>$inicio) $sql = $sql . " AND m.idmovimiento <=".$fin;
			}else{*/
				if(strlen($fechainicio)>10){
					if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . "' OR m.fecha is null) ";}
				}else{
					if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . " 00:00:00.000' OR m.fecha is null) ";}
				}
				if(strlen($fechafin)>10){
					if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . "'  OR m.fecha is null) ";}
				}else{
					if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . " 23:59:59.999'  OR m.fecha is null) ";}
				}
			//}
				
			if($id>0){ $sql = $sql . " AND P.IdProducto = " . $id;}
			$sql = $sql . " AND (P.IdSucursal = " . $idsucursal. " or (P.idsucursal<>" . $idsucursal. " and compartido='S'))";
			if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
			if($buscar_categoria > 0){$sql = $sql . " AND (P.IdCategoria = ".$buscar_categoria." or categoria.idcategoriaref=".$buscar_categoria.")";}
			if($buscar_marca > 0){$sql = $sql . " AND P.IdMarca = '" . $buscar_marca . "'";}
			if($compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $compartido."'" ;}
			if($tipo <>"" ){$sql = $sql . " AND tipo LIKE '" . $tipo."'" ;}
			if($compuesto <>"" ){$sql = $sql . " AND compuesto LIKE '" . $compuesto."'" ;}
            if($comida <>"N"){$sql = $sql . " AND categoria.comida like '$comida'";}
			
			$sql.=" GROUP BY P.IdProducto, P.IdSucursal, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY'), StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, P.Comentario, P.Imagen, P.Estado, Compartido, CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END, CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END, ub.nombre, P.tipo, U.descripcion";
			
            if($orden=="Abreviatura"){
                $order="P.".$order;
            }
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	 	
 	}
	
	function consultarProductoReporteDetallado($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $buscar_categoria=0,$buscar_marca=0,$fechainicio='',$fechafin='', $compartido='', $tipo='', $compuesto='',$jornada=0, $idsalon=0)
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
			
			$sql = "SELECT P.IdProducto, P.IdSucursal, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY') as FechaVencimiento, StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, P.Comentario, P.Imagen, P.Estado, Compartido, 
            CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END as categoria, 
            CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END as marca, 
            ub.nombre as ubicacion, P.tipo, U.descripcion as unidad, precioventa, SUM(dma.cantidad) as monto, ROUND(precioventa * SUM(dma.cantidad),2) as subtotal,count(1) as veces,ROW_NUMBER() OVER(Order by (precioventa * SUM(dma.cantidad)) desc) as Puesto 
            FROM Producto P 
            left join MARCA on P.idmarca=marca.idmarca and P.idsucursal=marca.idsucursal 
            left join CATEGORIA on P.idcategoria= categoria.idCategoria and P.idsucursal=categoria.idsucursal 
            left join Ubicacion ub on ub.IdUbicacion= P.IdUbicacion and ub.idsucursal= p.idsucursal 
            INNER JOIN SUCURSAL s on S.idsucursal=p.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." 
            INNER JOIN DetalleMovAlmacen dma on dma.idproducto=P.idproducto and dma.idsucursalproducto=P.idsucursal 
            INNER JOIN (select * from Movimiento union all select * from movimientohoy) M on M.idmovimiento=dma.idmovimiento and M.idsucursal=dma.idsucursal and idtipomovimiento=2 and M.estado='N'
            inner join (select * from detallemovimiento union all select * from detallemovimientohoy) dm on dm.idmovimiento=M.idmovimiento and dm.idsucursal=M.idsucursal and dma.iddetallemovalmacen=dm.iddetallemovalmacen 
            INNER JOIN Unidad U on dma.idunidad=U.idunidad 
            WHERE 1=1 ";
			$sql = $sql . " AND P.Estado LIKE 'N' ";
			if($idsalon!="0" && $jornada==1) $sql.=" and dm.idmovimientoref in 
            (select idmovimiento from movimiento 
            where idmesa in (select idmesa from mesa where idsalon=$idsalon and idsucursal=m.idsucursal)
            and idsucursal=M.idsucursal and estado='N' and situacion='P')";

			/*if($jornada==1){
				require_once 'clsMovCaja.php';
				$objMovCaja = new clsmovCaja($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
				if($idcaja>0){
					//$inicio=$objMovCaja->consultarultimaaperturafecha($fechainicio,$idcaja);
					$inicio=$objMovCaja->consultaranteriorcierrefecha($fechainicio,$idcaja);
					$fin=$objMovCaja->consultarultimocierrefecha($fechainicio,$idcaja);
				}else{
					//$inicio=$objMovCaja->consultarultimaaperturafecha($fechainicio);
					$inicio=$objMovCaja->consultaranteriorcierrefecha($fechainicio,$idcaja);
					$fin=$objMovCaja->consultarultimocierrefecha($fechainicio);
				}
				$sql = $sql . " AND m.idmovimiento >= ".$inicio;
				if($fin>0 and $fin>$inicio) $sql = $sql . " AND m.idmovimiento <=".$fin;
			}else{*/
				if(strlen($fechainicio)>10){
					if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . "' OR m.fecha is null) ";}
				}else{
					if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . " 00:00:00.000' OR m.fecha is null) ";}
				}
				if(strlen($fechafin)>10){
					if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . "'  OR m.fecha is null) ";}
				}else{
					if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . " 23:59:59.999'  OR m.fecha is null) ";}
				}
			//}
				
			if($id>0){ $sql = $sql . " AND P.IdProducto = " . $id;}
			$sql = $sql . " AND (P.IdSucursal = " . $idsucursal. " or (P.idsucursal<>" . $idsucursal. " and compartido='S'))";
			if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
			if($buscar_categoria > 0){$sql = $sql . " AND (P.IdCategoria = ".$buscar_categoria." or categoria.idcategoriaref=".$buscar_categoria.")";}
			if($buscar_marca > 0){$sql = $sql . " AND P.IdMarca = '" . $buscar_marca . "'";}
			if($compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $compartido."'" ;}
			if($tipo <>"" ){$sql = $sql . " AND tipo LIKE '" . $tipo."'" ;}
			if($compuesto <>"" ){$sql = $sql . " AND compuesto LIKE '" . $compuesto."'" ;}
			
			$sql.=" GROUP BY P.IdProducto, P.IdSucursal, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY'), StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, P.Comentario, P.Imagen, P.Estado, Compartido, CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END, CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END, ub.nombre, P.tipo, U.descripcion, precioventa";
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	 	
 	}
	
	function consultarProductoReporteStock($nro_reg, $nro_hoja, $order, $by, $id, $descripcion, $buscar_categoria=0,$buscar_marca=0,$codigo='',$tipo='',$kardex='',$idsucursal='0')
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
			if($idsucursal=="0"){
				$idsucursal=$this->gIdSucursal;
			}//obtenerStock(P.idproducto,P.idunidadbase,".$this->gIdSucursal.", P.IdSucursal) as Stock 
			$sql = "SELECT P.IdProducto, P.IdSucursal,P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso, FechaVencimiento, StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, Comentario, P.Imagen, P.Estado, CASE WHEN C.descripcion IS NULL THEN 'NINGUNO' ELSE C.descripcion END as categoria, CASE WHEN M.descripcion IS NULL THEN 'NINGUNO' ELSE M.descripcion END as marca, ub.nombre as ubicacion, U.Descripcion as unidad, LU.moneda, LU.preciocompra, LU.precioventa, UM.Descripcion as medida,case when p.idproducto in (69,98,104,101) and p.idsucursal=1 then round(sp.stockbase*lu.precioventa,2) else sp.stockbase end as stock 
            FROM Producto P 
            left JOIN Categoria C ON C.idcategoria=p.idcategoria and p.idsucursal=C.idsucursal 
            LEFT JOIN MARCA M ON M.idmarca=P.idmarca and P.idsucursal=M.idsucursal 
            left join Ubicacion ub on ub.IdUbicacion= P.IdUbicacion and ub.idsucursal= p.idsucursal 
            inner join LISTAUNIDAD LU on LU.idproducto= P.Idproducto  and P.idsucursal=LU.idsucursal and LU.idunidad=P.idunidadbase and P.idsucursal=LU.idsucursalproducto 
            INNER JOIN UNIDAD U ON P.idunidadbase=U.idunidad 
            left join stockproducto sp on sp.idproducto=p.idproducto and p.idsucursal=sp.idsucursal and p.idunidadbase=sp.idunidad and sp.idsucursalproducto=p.idsucursal
            INNER JOIN UNIDAD UM ON P.idmedidapeso=UM.idunidad 
            WHERE P.idSucursal=".$this->gIdSucursal." and 1=1 ";
			$sql = $sql . " AND P.Estado = 'N' ";
			if($id>0){ $sql = $sql . " AND P.IdProducto = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
			if($buscar_categoria > 0){$sql = $sql . " AND (P.IdCategoria = ".$buscar_categoria." or C.idcategoriaref=".$buscar_categoria.")";}
			if($buscar_marca > 0){$sql = $sql . " AND P.IdMarca = '" . $buscar_marca . "'";}
			if($codigo <>"" ){$codigo = "%".$codigo."%";$sql = $sql . " AND P.Codigo LIKE '" . $codigo . "'";}
			if($tipo <>"" ){$sql = $sql . " AND P.tipo LIKE '" . $tipo."'" ;}
			if($kardex <>"" ){$sql = $sql . " AND kardex LIKE '" . $kardex."'" ;}
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			
		} 	 	
 	}
	
	function consultarProductoReporteDetalladoUtilidad($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $buscar_categoria=0,$buscar_marca=0,$fechainicio='',$fechafin='', $compartido='', $tipo='', $compuesto='',$jornada=0, $idcaja=0)
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
			
			$sql = "SELECT P.IdProducto, P.IdSucursal, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY') as FechaVencimiento, StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, P.Comentario, P.Imagen, P.Estado, Compartido, CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END as categoria, CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END as marca, ub.nombre as ubicacion, P.tipo, U.descripcion as unidad, preciocompra, precioventa, 
            SUM(dma.cantidad) as monto, ROUND(preciocompra * SUM(dma.cantidad),2) as subtotalcompra, ROUND(precioventa * SUM(dma.cantidad),2) as subtotalventa, (ROUND(precioventa * SUM(dma.cantidad),2) - ROUND(preciocompra * SUM(dma.cantidad),2)) as utilidad,count(1) as veces,ROW_NUMBER() OVER(Order by (precioventa * SUM(dma.cantidad)) desc) as Puesto 
            FROM Producto P 
            left join MARCA on P.idmarca=marca.idmarca and P.idsucursal=marca.idsucursal 
            left join CATEGORIA on P.idcategoria= categoria.idCategoria and P.idsucursal=categoria.idsucursal 
            left join Ubicacion ub on ub.IdUbicacion= P.IdUbicacion and ub.idsucursal= p.idsucursal 
            INNER JOIN SUCURSAL s on S.idsucursal=p.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." 
            INNER JOIN DetalleMovAlmacen dma on dma.idproducto=P.idproducto and dma.idsucursalproducto=P.idsucursal 
            INNER JOIN Movimiento M on M.idmovimiento=dma.idmovimiento and M.idsucursal=dma.idsucursal and idtipomovimiento=2 
            INNER JOIN Unidad U on dma.idunidad=U.idunidad WHERE 1=1 ";
			$sql = $sql . " AND P.Estado LIKE 'N' ";
			
			if($jornada==1){
				require_once 'clsMovCaja.php';
				$objMovCaja = new clsmovCaja($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
				if($idcaja>0){
					//$inicio=$objMovCaja->consultarultimaaperturafecha($fechainicio,$idcaja);
					$inicio=$objMovCaja->consultaranteriorcierrefecha($fechainicio,$idcaja);
					$fin=$objMovCaja->consultarultimocierrefecha($fechainicio,$idcaja);
				}else{
					//$inicio=$objMovCaja->consultarultimaaperturafecha($fechainicio);
					$inicio=$objMovCaja->consultaranteriorcierrefecha($fechainicio,$idcaja);
					$fin=$objMovCaja->consultarultimocierrefecha($fechainicio);
				}
				$sql = $sql . " AND m.idmovimiento >= ".$inicio;
				if($fin>0 and $fin>$inicio) $sql = $sql . " AND m.idmovimiento <=".$fin;
			}else{
				if(strlen($fechainicio)>10){
					if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . "' OR m.fecha is null) ";}
				}else{
					if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . " 00:00:00.000' OR m.fecha is null) ";}
				}
				if(strlen($fechafin)>10){
					if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . "'  OR m.fecha is null) ";}
				}else{
					if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . " 23:59:59.999'  OR m.fecha is null) ";}
				}
			}
				
			if($id>0){ $sql = $sql . " AND P.IdProducto = " . $id;}
			$sql = $sql . " AND (P.IdSucursal = " . $idsucursal. " or (P.idsucursal<>" . $idsucursal. " and compartido='S'))";
			if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
			if($buscar_categoria > 0){$sql = $sql . " AND (P.IdCategoria = ".$buscar_categoria." or categoria.idcategoriaref=".$buscar_categoria.")";}
			if($buscar_marca > 0){$sql = $sql . " AND P.IdMarca = '" . $buscar_marca . "'";}
			if($compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $compartido."'" ;}
			if($tipo <>"" ){$sql = $sql . " AND tipo LIKE '" . $tipo."'" ;}
			if($compuesto <>"" ){$sql = $sql . " AND compuesto LIKE '" . $compuesto."'" ;}
			
			$sql.=" GROUP BY P.IdProducto, P.IdSucursal, P.Codigo, P.Descripcion, P.IdCategoria, P.IdMarca, P.IdUnidadBase, Peso, IdMedidaPeso,  to_char(FechaVencimiento,'DD/MM/YYYY'), StockMinimo, StockMaximo, StockOptimo, Minimovender, MinimoComprar, P.IdUbicacion, Columna, Fila, Kardex, Compuesto, P.Comentario, P.Imagen, P.Estado, Compartido, CASE WHEN categoria.descripcion IS NULL THEN 'NINGUNO' ELSE categoria.descripcion END, CASE WHEN Marca.descripcion IS NULL THEN 'NINGUNO' ELSE Marca.descripcion END, ub.nombre, P.tipo, U.descripcion, precioventa, preciocompra";
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	 	
 	}
    
    function consultarProductoReporteTopxDia($nro_reg, $nro_hoja, $order, $by, $idsucursal,$descripcion,$fechainicio='',$fechafin='',$orden=1,$comida='N'){
        if($by==1){
			$by="ASC";
		}else{
			$by="DESC";
		}
		$descripcion = "%".$descripcion."%";
		
		$sql = "select T.dia,T.semana,T.total,T.orden,dia_semana(T.fecha) as nombredia,round(T.cantidad,0) as cantidad,to_char(T.fecha,'DD/MM/YYYY') as fecha,p.descripcion,p.codigo,c.descripcion as categoria,p.abreviatura
         from(
        SELECT to_char(M.fecha,'DD') as dia,to_char(M.fecha,'W') as Semana,to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,
        SUM(dma.cantidad*dma.precioventa) as Total,dma.idproducto,to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha) as SemanaMes,
        ROW_NUMBER() OVER (PARTITION BY to_char(m.fecha,'DD/MM/YYYY') ORDER BY SUM(dma.cantidad*dma.precioventa) desc) AS Orden,cast(to_char(m.fecha,'DD/MM/YYYY') as date) as fecha,
        SUM(dma.cantidad) as cantidad
        FROM (select * from movimiento union all select * from movimientohoy) M 
        inner join detallemovalmacen dma on dma.idmovimiento=m.idmovimiento and dma.idsucursal=m.idsucursal
        inner join producto p on p.idproducto=dma.idproducto and p.idsucursal=$idsucursal
        left join CATEGORIA c on P.idcategoria= c.idCategoria and P.idsucursal=c.idsucursal
        WHERE 1=1 and m.idtipomovimiento=2 and m.idsucursal=$idsucursal and m.estado='N' and m.situacion<>'I'";
		if(strlen($fechainicio)>10){
			if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . "' OR m.fecha is null) ";}
		}else{
			if($fechainicio<>''){$sql = $sql . " AND (m.fecha >= '" . $fechainicio . " 00:00:00.000' OR m.fecha is null) ";}
		}
		if(strlen($fechafin)>10){
			if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . "'  OR m.fecha is null) ";}
		}else{
			if($fechafin<>''){$sql = $sql . " AND (m.fecha <= '" . $fechafin . " 23:59:59.999'  OR m.fecha is null) ";}
		}
        if($comida<>'N'){$sql = $sql . " AND c.comida like '$comida'";}
        $sql.="GROUP BY to_char(m.fecha,'DD/MM/YYYY'),to_char(M.fecha,'DD'),dma.idproducto,to_char(M.fecha,'W'),to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY'), to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha)
        ) T 
        inner join producto p on p.idproducto=T.idproducto and p.idsucursal=$idsucursal
        left join CATEGORIA c on P.idcategoria= c.idCategoria and P.idsucursal=c.idsucursal 
        where T.orden<=$orden";
        if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
        if($comida<>'N'){$sql = $sql . " AND c.comida like '$comida'";}
        //$sql.="order by mes,semanames,dia ";
		
		$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
		$cuenta = $rst->fetchAll();
		$total = COUNT($cuenta);
        if($nro_hoja>0)
            return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		else
            return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13));
    }
}
?>