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
class clsMesa extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarMesa($numero, $idsalon, $idsucursal, $nropersonas, $comentario, $imagen)
 	{ 	
		$sql = "select up_agregarmesa ('".$this->mill($numero)."', ".$idsalon.",".$idsucursal.", ".$nropersonas.", '".$this->mill($comentario)."', '".$this->mill($imagen)."') as idmesa";
		/*$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}*/
		return $this->obtenerDataSQL($sql);
 	}

	function actualizarmesa($id, $numero, $idsalon, $idsucursal, $nropersonas, $comentario, $imagen)
 	{
   		$sql = "execute up_ModificarMesa $id, '".$this->mill($numero)."', ".$idsalon.", ".$idsucursal.", ".$nropersonas.", '".$this->mill($comentario)."', '".$this->mill($imagen)."'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarMesa($id, $idsucursal)
 	{
   		$sql = "execute up_EliminarMesa $id, $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarMesa($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion,$buscar_persona='',$buscar_salon='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarMesa ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT IdMesa, M.IdSucursal, Numero, M.IdSalon, S.descripcion as salon, NroPersonas, M.Comentario, M.Imagen, M.Situacion, M.Estado 
			FROM Mesa M 
			INNER JOIN SALON S ON M.idsalon=S.idsalon and M.idsucursal=S.idsucursal WHERE M.idsucursal=".$idsucursal." ";
			$sql = $sql . " AND M.Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND IdMesa = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND Numero LIKE '" . $descripcion . "'";}
			if($buscar_persona <>"" ){$sql = $sql . " AND NroPersonas = '" . $buscar_persona . "'";}
			if($buscar_salon <>"" ){$sql = $sql . " AND S.descripcion LIKE '%" . $buscar_salon . "%'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}

	function consultarMesaReporte($nro_reg, $nro_hoja, $order, $by, $id, $idsucursal, $descripcion,$idsalon='0',$fechainicio, $fechafin)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarMesa ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$descripcion = "%".$descripcion."%";
			
			$sql = "SELECT M.IdMesa, M.IdSucursal, M.Numero, M.IdSalon, S.descripcion as salon, M.NroPersonas, M.Comentario, M.Imagen, M.Situacion, M.Estado,count(MOV.idmovimiento) as pedidos,
			sum(MOV.fechafinal-MOV.fecha) as tiempototal,min(MOV.fechafinal-MOV.fecha) as tiempominimo,
			max(MOV.fechafinal-MOV.fecha) as tiempomaximo,avg(MOV.fechafinal-MOV.fecha) as totalpromedio,count(MOV.idmovimiento),
                        ROW_NUMBER() OVER(Order by (count(MOV.idmovimiento)) desc, (avg(MOV.fechafinal-MOV.fecha)) asc) as Puesto
			FROM Mesa M 
			INNER JOIN SALON S ON M.idsalon=S.idsalon and M.idsucursal=S.idsucursal
			LEFT JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) AS MOV ON M.idmesa = MOV.idmesa and M.idsucursal=MOV.idsucursal WHERE M.idsucursal=".$idsucursal." ";
			$sql = $sql . " AND M.Estado LIKE 'N' ";
			if($id>0){ $sql = $sql . " AND M.IdMesa = " . $id;}
			if($descripcion <>"" ){$sql = $sql . " AND M.Numero LIKE '" . $descripcion . "'";}
			//if($buscar_persona <>"" ){$sql = $sql . " AND NroPersonas = '" . $buscar_persona . "'";}
			if($idsalon > 0 ){$sql = $sql . " AND S.idsalon = ". $idsalon;}
                        if($fechainicio <>"" ){$sql = $sql . " AND (MOV.fecha >= '".$fechainicio."' OR MOV.fecha IS NULL)";}
			if($fechafin <>"" ){$sql = $sql . " AND (MOV.fecha <= '".$fechafin."' OR MOV.fecha IS NULL)";}
			$sql = $sql . " GROUP BY M.IdMesa, M.IdSucursal, M.Numero, M.IdSalon, S.descripcion, M.NroPersonas, M.Comentario, M.Imagen, M.Situacion, M.Estado";
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
                        //echo $sql;
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}

	function consultarMesaxSalon($idsalon,$situacion='N')
 	{
			$sql = "SELECT * FROM Mesa WHERE 1=1  and idsucursal=".$this->gIdSucursal." ";
			$sql = $sql . " AND Estado = 'N'  and situacion like '".$situacion."' ";
			if($idsalon>0){ $sql = $sql . " AND idsalon = " . $idsalon;}
			if($idsalon==0){ $sql = $sql . " AND idsalon in (select idsalon from salon where estado='N' and idsucursal=".$this->gIdSucursal.")";}
            //Quite el (limit 1) al final para que pueda acceder a todas las mesas de todos los salones
			//si el salon es cero debo filtrar las mesas del primer salon q pertenecen a la sucursal actual
			$sql = $sql . " Order by numero asc ";
			return $this->obtenerDataSQL($sql);
 	}
	
	function consultarMesaxSalon2($idsalon,$situacion='N')
 	{
 	  //quite a pedido del cliente en el tercer left and m.situacion='O'
			$sql = "select m.idmesa, m.numero, idsalon, m.nropersonas, m.comentario, m.situacion, case when mr.numero is null then mo.numero else mr.numero end as nromovimiento, case when dr.idmovimiento is null then mo.idmovimiento else dr.idmovimiento end as idmovimiento, 
LOCALTIMESTAMP(0) - to_timestamp(case when dr.idmovimiento is null and mo.situacion='O' then to_char(mo.fecha,'YYYY-MM-DD HH24:MI:SS') when dr.idmovimiento is null and mo.situacion='A' then to_char(mo.fechaproximacancelacion,'YYYY-MM-DD HH24:MI:SS') else to_char(mr.fechaproximacancelacion,'YYYY-MM-DD HH24:MI:SS') end,'YYYY-MM-DD HH24:MI:SS') as transcurrido,
mo.situacion as situacionmovimiento,usu.nombreusuario,mo.total,mo.nombrespersona
FROM mesa m 
left join detallereserva dr on dr.idmesa=m.idmesa and dr.idsucursal=m.idsucursal and m.situacion='R'
left join (select * from movimientohoy) mr on mr.idmovimiento=dr.idmovimiento and mr.idsucursal=dr.idsucursal and mr.situacion<>'A' and mr.idtipomovimiento=6 and mr.estado='N' 
left join (select * from movimientohoy) mo on mo.idmesa=m.idmesa and mo.idsucursal=m.idsucursal and mo.situacion<>'P' and mo.idtipomovimiento=5  and mo.estado='N'and mo.situacion<>'A'
left join usuario usu on usu.idusuario = mo.idusuario
WHERE 1=1  and m.idsucursal=".$this->gIdSucursal." ";
			$sql = $sql . " AND m.Estado = 'N'  and m.situacion like '".$situacion."' ";
			if($idsalon>0){ $sql = $sql . " AND idsalon = " . $idsalon;}
			if($idsalon==0){ $sql = $sql . " AND idsalon in (select idsalon from salon where estado='N' and idsucursal=".$this->gIdSucursal." limit 1)";}
			//si el salon es cero debo filtrar las mesas del primer salon q pertenecen a la sucursal actual
			$sql = $sql . " Order by m.numero asc ";
			//echo $sql;
			return $this->obtenerDataSQL($sql);
			//echo $sql;
 	}
	
	function consultarMesaxSalon2xId($idsalon,$idmesa)
 	{
 	  //quite a pedido del cliente en el tercer left and m.situacion='O'
			$sql = "select m.idmesa, m.numero, idsalon, m.nropersonas, m.comentario, m.situacion, 
                            case when mr.numero is null then mo.numero else mr.numero end as nromovimiento, case when dr.idmovimiento is null then mo.idmovimiento else dr.idmovimiento end as idmovimiento, 
LOCALTIMESTAMP(0) - to_timestamp(case when dr.idmovimiento is null and mo.situacion='O' then to_char(mo.fecha,'YYYY-MM-DD HH24:MI:SS') when dr.idmovimiento is null and mo.situacion='A' then to_char(mo.fechaproximacancelacion,'YYYY-MM-DD HH24:MI:SS') else to_char(mr.fechaproximacancelacion,'YYYY-MM-DD HH24:MI:SS') end,'YYYY-MM-DD HH24:MI:SS') as transcurrido,
mo.situacion as situacionmovimiento,usu.nombreusuario,mo.total
FROM mesa m 
left join detallereserva dr on dr.idmesa=m.idmesa and dr.idsucursal=m.idsucursal and m.situacion='R'
left join (select * from movimientohoy) mr on mr.idmovimiento=dr.idmovimiento and mr.idsucursal=dr.idsucursal and mr.situacion<>'A' and mr.idtipomovimiento=6 and mr.estado='N' 
left join (select * from movimientohoy) mo on mo.idmesa=m.idmesa and mo.idsucursal=m.idsucursal and mo.situacion<>'P' and mo.idtipomovimiento=5  and mo.estado='N'and mo.situacion<>'A'
left join usuario usu on usu.idusuario = mo.idusuario
WHERE 1=1  and m.idsucursal=".$this->gIdSucursal." ";
			$sql = $sql . " AND m.Estado = 'N'  and m.situacion like '%' ";
			//if($idsalon>0){ $sql = $sql . " AND idsalon = " . $idsalon;}
			if($idsalon==0){ $sql = $sql . " AND idsalon in (select idsalon from salon where estado='N' and idsucursal=".$this->gIdSucursal." limit 1)";}
			//si el salon es cero debo filtrar las mesas del primer salon q pertenecen a la sucursal actual
			$sql = $sql . " and m.idmesa = $idmesa";
                        $sql = $sql . " Order by m.numero asc ";
			//echo $sql;
			return $this->obtenerDataSQL($sql);
			//echo $sql;
 	}
	
	function cambiarSituacion($id, $idsucursal, $situacion)
 	{
   		$sql = "UPDATE mesa SET Situacion='".$situacion."' where idmesa=".$id." and idsucursal=".$idsucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function verificaSituacion($id)
 	{
   		$sql = "SELECT Situacion FROM MESA where idmesa=".$id." and idsucursal=".$this->gIdSucursal;
		$rst = $this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		return $dato->situacion;
 	}
	
	function verificaExisteNumero($numero)
 	{
				
		$sql = "SELECT * FROM Mesa WHERE Estado = 'N' ";
		if($numero !=""){ $sql = $sql." AND Numero like '".$numero."' AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
	
	function reservarMesas($idsucursal)
 	{
   		$sql = "execute up_reservarMesas $idsucursal";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function verificaMesaLibre($idmesa)
 	{
				
		$sql = "SELECT * FROM salon WHERE Estado = 'N' ";
		if($idmesa !=""){ $sql = $sql." AND idmesalibre=".$idmesa." AND IdSucursal = ".$this->gIdSucursal;}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
}
?>