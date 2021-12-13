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

class clsKardex extends clsAccesoDatos
{
	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}
	
function consultar($idpro){//PENDIENTE
	$sql="SELECT producto.codigo as codigo, producto.descripcion as pro, DATE_FORMAT(kardex.fecha,'%d/%m/%Y') as fecha, tabla, kardex.saldoanteriorbase as stockanterior ,kardex.estado as estado,kardex.cantidadbase as cantidad, nea.numero as numeronea, pecosa.numero as numeropecosa, documentoalmacen.numero as numerodocalmacen,kardex.saldoactual as stockactual FROM kardex inner join producto on kardex.idproducto=producto.idproducto left join nea on kardex.idmovimiento= nea.idnea left join pecosa on kardex.idmovimiento= pecosa.idpecosa left join documentoalmacen on kardex.idmovimiento= documentoalmacen.iddocalmacen WHERE producto.idproducto=".$idpro;
	return $this->obtenerDataSQL($sql);
}

function consultarkardex($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion, $buscar_categoria=0,$buscar_marca=0,$fechainicio='',$fechafin='', $compartido='', $tipo='', $compuesto='',$tipo2='',$idtipodocumento='0'){
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
            $descripcion = "%".$descripcion."%";
	$sql="SELECT P.codigo as codigopro, P.descripcion as producto, kardex.idkardex, kardex.idsucursal, kardex.idmovimiento, kardex.idproducto, kardex.idunidad, kardex.cantidad, kardex.tipomoneda, kardex.preciounidad, kardex.importe, to_char(kardex.fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, 
    (case when kardex.idproducto in (69,98,101,104) and kardex.idsucursalproducto=1 then round(kardex.saldoanteriorbase*(select precioventa from detallemovalmacen where idmovimiento=kardex.idmovimiento and idproducto=kardex.idproducto and idsucursalproducto=1),2) else kardex.saldoanteriorbase end) as saldoanteriorbase, kardex.idunidadbase, (case when kardex.idproducto in (69,98,101,104) and kardex.idsucursalproducto=1 then round(kardex.cantidadbase*(select precioventa from detallemovalmacen where idmovimiento=kardex.idmovimiento and idproducto=kardex.idproducto and idsucursalproducto=1),2) else kardex.cantidadbase end) as cantidadbase, (case when kardex.idproducto in (69,98,101,104) and kardex.idsucursalproducto=1 then round(kardex.saldoactual*(select precioventa from detallemovalmacen where idmovimiento=kardex.idmovimiento and idproducto=kardex.idproducto and idsucursalproducto=1),2) else kardex.saldoactual end) as saldoactual, kardex.idusuario, kardex.ultimo, kardex.estado, kardex.idsucursalproducto, m.numero as numerodoc, td.abreviatura as tipodocumentoabreviatura,td.descripcion as tipodocumento,tm.descripcion as movimiento, 
    case when (stock='S' and kardex.estado in ('N','A')) or (stock='R' and kardex.estado in ('A','I')) then kardex.cantidadbase end as ingresobase, case when (stock='R' and kardex.estado='N') or (stock='S' and kardex.estado in ('A','I')) then kardex.cantidadbase end as salidabase, 
    case when (stock='S' and kardex.estado in ('N','A')) or (stock='R' and kardex.estado in ('A','I')) then kardex.cantidad end as ingreso, case when (stock='R' and kardex.estado='N') or (stock='S' and kardex.estado in ('A','I')) then kardex.cantidad end as salida, case when m.estado='N' then 'NUEVO' when m.estado='A' then 'ELIMINADO' when m.estado='I' then 'ANULADO' end as operacion,
    U.Descripcion as unidad, UB.Descripcion as unidadbase,td.stock,m.comentario 
    FROM kardex 
    inner join producto P on kardex.idproducto=P.idproducto and P.idsucursal=kardex.idsucursalproducto 
    left join (SELECT * FROM movimiento UNION ALL SELECT * FROM MOVIMIENTOHOY) m on kardex.idmovimiento= m.idmovimiento and m.idsucursal=kardex.idsucursal 
    left join tipodocumento td on td.idtipodocumento=m.idtipodocumento 
    left join tipomovimiento tm on tm.idtipomovimiento=m.idtipomovimiento 
    left join MARCA on P.idmarca=marca.idmarca and P.idsucursal=marca.idsucursal 
    left join CATEGORIA on P.idcategoria= categoria.idCategoria and P.idsucursal=categoria.idsucursal 
    INNER JOIN UNIDAD U ON U.idunidad=kardex.idunidad 
    INNER JOIN UNIDAD UB ON UB.idunidad=kardex.idunidadbase 
    INNER JOIN SUCURSAL s on S.idsucursal=p.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." WHERE 1=1 ";
			if($id>0){ $sql = $sql . " AND P.IdProducto = " . $id;}
			$sql = $sql . " AND (P.IdSucursal = " . $idsucursal. " or (P.idsucursal<>" . $idsucursal. " and compartido='S'))";
			if($descripcion <>"" ){$sql = $sql . " AND P.Descripcion LIKE '" . $descripcion . "'";}
			if($buscar_categoria > 0){$sql = $sql . " AND (P.IdCategoria = ".$buscar_categoria." or categoria.idcategoriaref=".$buscar_categoria.")";}
			if($buscar_marca > 0){$sql = $sql . " AND P.IdMarca = '" . $buscar_marca . "'";}
			if($fechainicio<>''){$sql = $sql . " AND (kardex.fecha >= '" . $fechainicio . " 00:00:00.000' OR kardex.fecha is null)";}
			if($fechafin<>''){$sql = $sql . " AND (kardex.fecha <= '" . $fechafin . " 23:59:59.999'  OR kardex.fecha is null)";}
			if($compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $compartido."'" ;}
			if($tipo <>"" ){$sql = $sql . " AND p.tipo LIKE '" . $tipo."'" ;}
			if($compuesto <>"" ){$sql = $sql . " AND compuesto LIKE '" . $compuesto."'" ;}
			if($tipo2<>""){
				if($tipo2=="S"){
					$sql.=" and kardex.saldoactual>kardex.saldoanteriorbase";
				}else{
					$sql.=" and kardex.saldoactual<kardex.saldoanteriorbase";
				}
			}
			if($idtipodocumento!="0"){
				$sql.=" and m.idtipodocumento=$idtipodocumento";
			}
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
}
}
?>