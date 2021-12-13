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
class clsMovCaja extends clsMovimiento
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}

	function consultarapertura($idcaja=0){
	
		$sql="SELECT T.idmovimiento, T.numero, T.total, CONCEPTOPAGO.descripcion
		FROM (select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal,idcaja from movimiento 
        union select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal,idcaja from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND CONCEPTOPAGO.idConceptoPago = 1
		AND T.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		if($idcaja!="0") $sql.=" and T.idcaja=$idcaja";
		//AND date( movimiento.fecha ) = '".$_SESSION['R_FechaProceso']."'
		$sql = $sql . " AND T.idmovimiento >= ".$this->consultarultimaapertura2($idcaja);
		$apertura2=$this->consultarultimaapertura2($idcaja);
		if($apertura2==0){
			$num_row=0;
		}else{
			$sql = $sql . " AND  T.idmovimiento >= ".$this->consultarultimaapertura2();
			$rst=$this->obtenerDataSQL($sql);
			$num_row=$rst->rowCount();
		}
		
		return $num_row;
		//return $this->consultarultimaapertura2();
	}

	function consultarcierre($fecha,$idcaja="0")
	{
		$sql="SELECT MOVIMIENTO.idmovimiento, MOVIMIENTO.numero, MOVIMIENTO.total, CONCEPTOPAGO.descripcion
		FROM (select * from movimiento union select * from movimientohoy) as MOVIMIENTO
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTO.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTO.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTO.estado = 'N'
		AND CONCEPTOPAGO.idConceptoPago = 2
		AND MOVIMIENTO.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		//AND date( movimiento.fecha ) = '".$fecha."'
		if($this->consultarultimocierrefecha($fecha,$idcaja)==0){
			$sql = $sql . " AND date( movimiento.fecha ) = '".substr($fecha,0,10)."'";
		}else{
			$sql = $sql . " AND idmovimiento >= ".$this->consultarultimocierrefecha($fecha,$idcaja);
		}
		if($idcaja!="0"){
			$sql = $sql. " AND MOVIMIENTO.idcaja=".$idcaja;
		}
		
		$rst=$this->obtenerDataSQL($sql);
		$num_row=$rst->rowCount();
		
		//echo $this->consultarultimocierrefecha($fecha);
		//echo $sql;
		return $num_row;
		//return $sql;
	}

	function existenciamov($idcaja=0){
		$sql="SELECT * FROM (select * from MOVIMIENTO union select * from movimientohoy) T WHERE T.estado='N' and T.IdSucursal=".$_SESSION['R_IdSucursal']." AND T.IdTipoMovimiento=4";
		if($idcaja!="0") $sql.=" and T.idcaja=$idcaja";
		$rst=$this->obtenerDataSQL($sql);
		$num=$rst->rowCount();
		return $num;
	}
	
	function montodeaperturasoles($idcaja="0"){	
	
		$sql="SELECT CASE WHEN sum(CASE WHEN tipo='I' THEN (case when MOVIMIENTO.IDCONCEPTOPAGO=1 THEN TOTAL ELSE TOTALPAGADO END) ELSE -1*TOTAL END) IS NULL THEN 0 ELSE sum(CASE WHEN tipo='I' THEN (case when MOVIMIENTO.IDCONCEPTOPAGO=1 THEN TOTAL ELSE TOTALPAGADO END) ELSE -1*TOTAL END) END as total
		FROM (select * from movimiento union select * from movimientohoy) as MOVIMIENTO
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTO.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTO.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTO.estado = 'N'
		AND MOVIMIENTO.IDSUCURSAL=".$_SESSION['R_IdSucursal']."
		AND MOVIMIENTO.MONEDA='S' 
		AND CONCEPTOPAGO.IDCONCEPTOPAGO <> 2";
		if($idcaja!="0") $sql.=" and MOVIMIENTO.idcaja=".$idcaja;
		//AND date( movimiento.fecha ) = (SELECT MAX(DATE(MOVIMIENTO.FECHA)) FROM MOVIMIENTO INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTO.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'	AND MOVIMIENTO.estado = 'N'	AND MOVIMIENTO.IDSUCURSAL=".$_SESSION['R_IdSucursal'].")
		$sql = $sql . " AND idmovimiento >= ".$this->consultarultimaapertura($idcaja);
        //ECHO $sql;
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		return $dato->total;
	}

	function montodeaperturadolares(){
		
		$sql="SELECT sum(CASE WHEN tipo='I' THEN TOTAL ELSE -1*TOTAL END) as total
		FROM (select * from movimiento union select * from movimientohoy) as MOVIMIENTO
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTO.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTO.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTO.estado = 'N'
		AND date( movimiento.fecha ) = (SELECT MAX(DATE(MOVIMIENTO.FECHA)) FROM MOVIMIENTO INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTO.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento WHERE TIPOMOVIMIENTO.descripcion = 'CAJA' AND MOVIMIENTO.estado = 'N' AND MOVIMIENTO.IDSUCURSAL=".$_SESSION['R_IdSucursal'].")
		AND MOVIMIENTO.IDSUCURSAL=".$_SESSION['R_IdSucursal']."
		AND MOVIMIENTO.MONEDA='D' 
		AND CONCEPTOPAGO.IDCONCEPTOPAGO <> 2";
		
		$rst=$this->obtenerDataSQL($sql);		
		$dato=$rst->fetchObject();
		return $dato->total;
	}

	function montodecierre($fecha,$idcaja=0){	
	
		$sql="SELECT sum(CASE WHEN tipo='I' and MOVIMIENTOHOY.idconceptopago<>1 THEN TOTALPAGADO ELSE CASE WHEN MOVIMIENTOHOY.idconceptopago=1 THEN TOTAL ELSE 0 END END) as ingreso,sum(CASE WHEN tipo='E' THEN TOTAL ELSE 0 END) as egreso,sum(CASE WHEN idbanco IS NOT NULL AND idtipotarjeta=2 THEN TOTAL-TOTALPAGADO ELSE 0 END ) as montomaster,sum(CASE WHEN idbanco IS NOT NULL AND idtipotarjeta = 1 THEN TOTAL-TOTALPAGADO ELSE 0 END ) as montovisa
		FROM (select * from movimiento union select * from movimientohoy) as MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTOHOY.estado = 'N'
		AND MOVIMIENTOHOY.IDSUCURSAL=".$_SESSION['R_IdSucursal']."
		AND MOVIMIENTOHOY.MONEDA='S'
		AND MOVIMIENTOHOY.idconceptopago<>2 AND (MOVIMIENTOHOY.idcaja>0 OR MOVIMIENTOHOY.idcaja is null)";
		//AND movimiento.fecha = '".$fecha."'
		$sql = $sql . " AND idmovimiento >= ".$this->consultarultimaapertura($idcaja);
	   //echo $sql;
		return $this->obtenerDataSQL($sql);
	}

	function montodecierrecaja($idcaja,$fecha,$idusuario=0){	
	
		$sql="SELECT sum(CASE WHEN tipo='I' THEN TOTAL ELSE 0 END) as ingreso,sum(CASE WHEN tipo='E' THEN TOTAL ELSE 0 END) as egreso,sum(CASE WHEN idbanco IS NOT NULL AND idtipotarjeta=2 THEN TOTAL ELSE 0 END ) as montomaster,sum(CASE WHEN idbanco IS NOT NULL AND idtipotarjeta = 1 THEN TOTAL ELSE 0 END ) as montovisa
		FROM (select * from movimiento union select * from movimientohoy)  as MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTOHOY.estado = 'N'
		AND MOVIMIENTOHOY.IDSUCURSAL=".$_SESSION['R_IdSucursal']."
		AND MOVIMIENTOHOY.MONEDA='S'
		AND MOVIMIENTOHOY.IDCAJA=".$idcaja." AND MOVIMIENTOHOY.idconceptopago<>18";
		//AND movimiento.fecha = '".$fecha."'
		if($idusuario>0){
			$sql = $sql . " AND MOVIMIENTOHOY.idusuario = " . $idusuario;
			$ultimocierre=$this->consultarultimocierre($idcaja,$idusuario);
		}else{
			$ultimocierre=$this->consultarultimocierre($idcaja);
		}
		$sql = $sql . " AND idmovimiento >".$ultimocierre;
        
        //echo $sql;
		return $this->obtenerDataSQL($sql);
	}

	function montodecierresoles($fecha,$idcaja=0,$idusuario=0){	
	
		$sql="SELECT sum(CASE WHEN tipo='I' THEN TOTAL ELSE -1*TOTAL END) as total
		FROM (select * from movimiento union select * from movimientohoy)  as MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTOHOY.estado = 'N'
                /*AGREGO EDUARDO 11-12-2016*/
                AND ((MOVIMIENTOHOY.idconceptopago <> 3) OR (IDTIPOTARJETA IS NULL))
                /*AGREGO EDUARDO 11-12-2016*/
		AND MOVIMIENTOHOY.IDSUCURSAL=".$_SESSION['R_IdSucursal']."
		AND MOVIMIENTOHOY.MONEDA='S'";
		//AND movimiento.fecha = '".$fecha."'
		$sql = $sql . " AND idmovimiento >= ".$this->consultarultimaapertura();
		if($idcaja>0){
			$sql.=" AND MOVIMIENTOHOY.idcaja=".$idcaja;
		}else{
			$sql.=" AND ((MOVIMIENTOHOY.idconceptopago<>2 AND MOVIMIENTOHOY.idcaja>0) OR (MOVIMIENTOHOY.idconceptopago<>2 AND MOVIMIENTOHOY.idcaja is null))";
		}
		if($idusuario>0) $sql.=" AND MOVIMIENTOHOY.idusuario=".$idusuario;
        //print_r($sql);
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		return $dato->total;
	}

	function montodecierredolares($fecha){
		
		$sql="SELECT sum(CASE WHEN tipo='I' THEN TOTAL ELSE -1*TOTAL END) as total
		FROM MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTOHOY.estado = 'N'
		AND MOVIMIENTOHOY.IDSUCURSAL=".$_SESSION['R_IdSucursal']."
		AND MOVIMIENTOHOY.MONEDA='D'";
		//AND movimiento.fecha = '".$fecha."'
		$sql = $sql . " AND idmovimiento >= ".$this->consultarultimaapertura();
		
		$rst=$this->obtenerDataSQL($sql);		
		$dato=$rst->fetchObject();
		return $dato->total;
	}

	function consultarmaxfecha(){
		$sql="SELECT MAX( DATE( FECHA ) ) FROM MOVIMIENTO WHERE estado='N' AND IDTIPOMOVIMIENTO=4 AND IDSUCURSAL =".$_SESSION['R_IdSucursal'];
		$rst=$this->obtenerDataSQL($sql);
		$valor=$rst->fetch();
		return $valor[0];
	}

	function consultarultimaapertura($idcaja="0"){
	
		$sql="SELECT T.idmovimiento 
        FROM (select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal,fecha,idcaja from movimiento 
        union select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal,fecha,idcaja from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND date( T.fecha ) <= '".$_SESSION['R_FechaProceso']."'
		AND CONCEPTOPAGO.idConceptoPago = 1";
		if($idcaja!="0") $sql.=" and T.idcaja=".$idcaja;
		$sql.=" AND T.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."' ORDER BY 1 DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		
		if(isset($dato->idmovimiento)){
			return $dato->idmovimiento;
		}else{
			return 0;
		}
		//echo $sql;
	}
	
	function consultarultimaaperturacaja($idcaja,$idusuario=0){
	
		$sql="SELECT T.idmovimiento 
        FROM (select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal from movimiento 
        union select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND date( T.fecha ) <= '".$_SESSION['R_FechaProceso']."'
		AND CONCEPTOPAGO.idConceptoPago = 1
		AND T.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		$sql.=" AND T.idcaja=".$idcaja;
		if($idusuario>0) $sql.=" AND T.idusuario=".$idusuario;
		$sql.=" ORDER BY 1 DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		
		if(isset($dato->idmovimiento)){
			return $dato->idmovimiento;
		}else{
			return 0;
		}
		//echo $sql;
	}
	
	function consultarultimaapertura2($idcaja=0){
	
		$sql="SELECT T.idmovimiento 
        from(select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal,fecha,idcaja from movimiento 
        union select idmovimiento,numero,total,idtipomovimiento,idconceptopago,estado,idsucursal,fecha,idcaja from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND date( T.fecha ) <= '".$_SESSION['R_FechaProceso']."'
		AND CONCEPTOPAGO.idConceptoPago = 1
		AND T.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		if($idcaja!="0") $sql.=" and T.idcaja=$idcaja";
		$sql.=" AND T.idmovimiento > ".$this->consultarultimocierre($idcaja);
		$sql.=" ORDER BY 1 DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		
		if(isset($dato->idmovimiento)){
			return $dato->idmovimiento;
		}else{
			return 0;
		}
		//return $this->consultarultimocierre();
	}
	
	function consultarultimocierre($idcaja=0,$idusuario=0){
	
		$sql="SELECT T.idmovimiento 
        from (select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario FROM MOVIMIENTO UNION select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND date( T.fecha ) <= '".$_SESSION['R_FechaProceso']."'
		AND CONCEPTOPAGO.idConceptoPago = 2
		AND T.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		if($idcaja>0) $sql.=" AND T.idcaja=".$idcaja;// else $sql.=" AND T.idcaja is null"; 
		if($idusuario>0) $sql.=" AND T.idusuario=".$idusuario;
		$sql.=" ORDER BY 1 DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		
	   //echo $sql;
		if(isset($dato->idmovimiento)){
			return $dato->idmovimiento;
		}else{
			return 0;
		}		
	}
	
	function consultarultimoconcepto($idcaja=0,$idusuario=0){
	
		$sql="SELECT T.idconceptopago 
        FROM (select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario from movimiento 
        union select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario from movimientohoy) T 
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND date( T.fecha ) <= '".$_SESSION['R_FechaProceso']."'
		AND T.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		if($idcaja>0) $sql.=" AND T.idcaja=".$idcaja; //else $sql.=" AND T.idcaja is null"; 
		if($idusuario>0) $sql.=" AND T.idusuario=".$idusuario;

		$sql.=" ORDER BY T.idmovimiento DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		//echo $sql;
		if(isset($dato->idconceptopago)){
			return $dato->idconceptopago;
		}else{
			return 0;
		}		
	}
	
	function consultarultimaaperturafecha($fecha,$idcaja=0,$idusuario=0){
	
		$sql="SELECT MOVIMIENTO.idmovimiento FROM MOVIMIENTO
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTO.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTO.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTO.estado = 'N'
		AND date( movimiento.fecha ) <= '".$fecha."'
		AND CONCEPTOPAGO.idConceptoPago = 1
		AND MOVIMIENTO.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		if($idcaja>0) $sql.=" AND MOVIMIENTO.idcaja=".$idcaja; else $sql.=" AND MOVIMIENTO.idcaja is null"; 
		if($idusuario>0) $sql.=" AND MOVIMIENTO.idusuario=".$idusuario;
		$sql.=" ORDER BY 1 DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		
		if(isset($dato->idmovimiento)){
			return $dato->idmovimiento;
		}else{
			return 0;
		}
		//echo $sql;
	}
	
	function consultarultimocierrefecha($fecha,$idcaja=0,$idusuario=0){
	
		$sql="SELECT MOVIMIENTOHOY.idmovimiento FROM MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTOHOY.estado = 'N'
		AND ( movimientohoy.fecha ) >= '".$fecha."'
		AND CONCEPTOPAGO.idConceptoPago = 2
		AND MOVIMIENTOHOY.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		if($idcaja>0) $sql.=" AND MOVIMIENTOHOY.idcaja=".$idcaja; else $sql.=" AND MOVIMIENTOHOY.idcaja is null"; 
		if($idusuario>0) $sql.=" AND MOVIMIENTOHOY.idusuario=".$idusuario;
		
		$sql.=" ORDER BY 1 DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		//echo $sql;
		if(isset($dato->idmovimiento)){
			return $dato->idmovimiento;
		}else{
			return 0;
		}
		//echo $sql;
	}
	
	function consultaranteriorcierrefecha($fecha,$idcaja=0,$idusuario=0){
	
		$sql="SELECT MOVIMIENTOHOY.idmovimiento FROM MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTOHOY.estado = 'N'
		AND ( movimientohoy.fecha ) <= '".$fecha."'
		AND CONCEPTOPAGO.idConceptoPago = 2
		AND MOVIMIENTOHOY.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		if($idcaja>0) $sql.=" AND MOVIMIENTOHOY.idcaja=".$idcaja; else $sql.=" AND MOVIMIENTOHOY.idcaja is null"; 
		if($idusuario>0) $sql.=" AND MOVIMIENTOHOY.idusuario=".$idusuario;
		$ultimocierre=$this->consultarultimocierrefecha($fecha,$idcaja,$idusuario);
		if($ultimocierre>0)	$sql.=" AND MOVIMIENTOHOY.idmovimiento<".$ultimocierre;
		$sql.=" ORDER BY 1 DESC LIMIT 1";
		
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		
		if(isset($dato->idmovimiento)){
			return $dato->idmovimiento;
		}else{
			return 0;
		}
		//echo $sql;
	}

	function consultarMovCaja($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero, $fecha, $origencaja='CC',$idcaja=0,$idusuario=0,$idtipodocumento=0,$idconceptopago=0,$persona='',$comentario='',$cajero='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarMovimiento ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$numero = "%".$numero."%";
			if(substr($nro_hoja,0,1)=="h"){
			  $tabla=" ";
              $nro_hoja=substr($nro_hoja,1,strlen($nro_hoja)-1);
			}else {
              $tabla="historico";
			}
            
			$sql = "SELECT T.idmovimiento, T.idconceptopago, cp.descripcion as conceptopago, T.idsucursal, T.idtipomovimiento, T.numero, 
       T.idtipodocumento, td.descripcion as tipodocumento, formapago, to_char(fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, fechaproximacancelacion, fechaultimopago, 
       nropersonas, idmesa, moneda, inicial, subtotal, igv, total, totalpagado, 
       T.idusuario, T.tipopersona, T.idpersona, idresponsable, T.idmovimientoref, 
       idsucursalref, T.comentario, CASE WHEN T.situacion='O' THEN 'Ordenada' WHEN T.situacion='A' THEN 'Atendida' WHEN T.situacion='P' THEN 'Pagada' END as situacion, situacion as situacion2, T.estado, CASE WHEN T.idtipodocumento=9 THEN T.total ELSE 0 END AS ingreso, CASE WHEN T.idtipodocumento=10 THEN T.total ELSE 0 END AS egreso, CASE WHEN T.idtipodocumento=9 THEN CASE WHEN moneda='S' THEN total ELSE round(total*".$_SESSION['R_TipoCambio'].",2) END ELSE 0 END AS ingresos,
       CASE WHEN T.idtipodocumento=10 THEN CASE WHEN moneda='S' THEN total ELSE round(T.total*".$_SESSION['R_TipoCambio'].",2) END ELSE 0 END AS egresos, CASE WHEN T.tipopersona='P' THEN CASE WHEN T.nombrespersona='' THEN (PM.apellidos || ' ' || PM.nombres) ELSE (PM.apellidos || ' ' || PM.nombres || ': ' || T.nombrespersona) END ELSE s.razonsocial END as persona,T.idcaja, C.numero as caja, SC.abreviatura as saloncaja, (PMU.apellidos || ' ' || PMU.nombres) as cajero,
       B.descripcion as banco,TI.descripcion as tipotarjeta,T.numerotarjeta,
       CASE WHEN T.modopago = 'T' AND T.idtipotarjeta=2 THEN T.total-T.totalpagado WHEN T.modopago = 'A' THEN (substr(T.montotarjeta,position('2@' in T.montotarjeta)+2,length(T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric ELSE 0 END as montocredito,
       CASE WHEN T.modopago = 'T' AND T.idtipotarjeta=1 THEN T.total-T.totalpagado WHEN T.modopago = 'A' THEN (substr(T.montotarjeta,position('1@' in T.montotarjeta)+2,position('|' in T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric ELSE 0 END as montodebito,
       T.total-T.totalpagado as montotarjeta,T.montotarjeta as tarjetas,T.modopago,T.idtipotarjeta
       FROM (";
            if($tabla=="historico"){
                $sql.=" select * from movimiento UNION select * from movimientohoy";
            }
            else {
                $sql.=" select * from movimiento UNION select * from movimientohoy";
            }
            $sql.=") T 
            inner join tipodocumento td on td.idtipodocumento=T.idtipodocumento 
            inner join conceptopago cp on cp.idconceptopago=T.idconceptopago 
            LEFT JOIN Persona P ON P.idpersona=T.idpersona AND P.idsucursal=T.idsucursalpersona 
            LEFT JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro 
            LEFT JOIN sucursal s on s.idsucursal=T.idpersona 
            LEFT JOIN Caja C on C.idcaja=T.idcaja AND C.idsucursal=T.idsucursal 
            LEFT JOIN Salon SC on SC.idsalon=C.idsalon AND C.idsucursal=SC.idsucursal 
            LEFT JOIN usuario U on U.idusuario=T.idusuario AND U.idsucursal=T.idsucursalusuario 
            LEFT JOIN Persona PU ON PU.idpersona=U.idpersona AND PU.idsucursal=U.idsucursal 
            LEFT JOIN PersonaMaestro PMU ON PU.idpersonamaestro=PMU.idpersonamaestro
            LEFT JOIN Banco B on B.idbanco = T.idbanco and B.idsucursal=T.idsucursal
            LEFT JOIN Tipotarjeta TI on TI.idtipotarjeta = T.idtipotarjeta WHERE 1=1 ";
			$sql = $sql . " AND T.Estado = 'N' AND T.IdSucursal=".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND T.IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND T.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$sql = $sql . " AND T.numero LIKE '" . $numero . "'";}
			if($idtipodocumento>0){ $sql = $sql . " AND T.idtipodocumento = " . $idtipodocumento;}
			if($idconceptopago>0){ $sql = $sql . " AND T.idconceptopago = " . $idconceptopago;}
			if($persona<>"" ){$sql = $sql . " AND CASE WHEN T.tipopersona='P' THEN CASE WHEN T.nombrespersona='' THEN (PM.apellidos || ' ' || PM.nombres) ELSE (PM.apellidos || ' ' || PM.nombres || ': ' || T.nombrespersona) END ELSE s.razonsocial END LIKE '%" . $persona . "%'";}
			if($comentario<>"" ){$sql = $sql . " AND T.comentario LIKE '%" . $comentario . "%'";}
			if($cajero<>"" ){$sql = $sql . " AND (PMU.apellidos || ' ' || PMU.nombres) LIKE '%" . $cajero . "%'";}
            //if($fecha <>"" ){$sql = $sql . " AND fecha = '".$fecha."'";}
			//CUANDO ORIGENCAJA ES FLUJOS DE CAJA
			if($idcaja>0 and $origencaja=='FC'){ 
				$sql = $sql . " AND T.Idcaja = ".$idcaja." and T.idconceptopago<>18";
				if($idusuario>0){
				    if($tabla!="historico"){
					$sql = $sql . " AND T.idusuario = " . $idusuario;}
                    else $idusuario="0";
					$ultimocierre=$this->consultarultimocierre($idcaja,$idusuario);
				}else{
					$ultimocierre=$this->consultarultimocierre($idcaja);
				}
				    $sql = $sql . " AND T.idmovimiento >".$ultimocierre;
			}
			//CUANDO ORIGENCAJA ES CAJA CHICA
			if($idcaja>0 and $origencaja=='CC'){ 
				$sql = $sql . " AND T.Idcaja = ".$idcaja."";
			}
			if($origencaja=='CC'){ 
				$sql = $sql . " and T.idconceptopago<>17";
				$sql.=" AND T.idmovimiento >= ".$this->consultarultimaapertura($idcaja);
				$sql.=" AND ((T.idconceptopago<>2 AND T.idcaja>0) OR (T.idconceptopago<>2 AND T.idcaja is null))";
			}
			
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			if($order==1) {$order++;$by="DESC";}
            if($nro_hoja>=0){
			     return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
            }else{
                return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13));            
            }
			
		} 	 	
 	}
	
	function consultarMovCajaReporte($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero, $fechainicio, $fechafin, $origencaja='CC',$idcaja=0,$idusuario=0,$idtipodocumento=0,$idconceptopago=0,$persona='',$comentario='',$cajero='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarMovimiento ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$numero = "%".$numero."%";
			
			$sql = "SELECT idmovimiento, m.idconceptopago, cp.descripcion as conceptopago, m.idsucursal, m.idtipomovimiento, m.numero, 
       m.idtipodocumento, td.descripcion as tipodocumento, formapago, to_char(fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, fechaproximacancelacion, fechaultimopago, 
       nropersonas, idmesa, moneda, inicial, subtotal, igv, total, totalpagado,numerooperacion as nrodoc, 
       m.idusuario, m.tipopersona, m.idpersona, idresponsable, idmovimientoref, 
       idsucursalref, comentario, CASE WHEN situacion='O' THEN 'Ordenada' WHEN situacion='A' THEN 'Atendida' WHEN situacion='P' THEN 'Pagada' END as situacion, situacion as situacion2, m.estado, CASE WHEN m.idtipodocumento=9 THEN total ELSE 0 END AS ingreso, CASE WHEN m.idtipodocumento=10 THEN total ELSE 0 END AS egreso, CASE WHEN m.idtipodocumento=9 THEN CASE WHEN moneda='S' THEN total ELSE round(total*".$_SESSION['R_TipoCambio'].",2) END ELSE 0 END AS ingresos, CASE WHEN m.idtipodocumento=10 THEN CASE WHEN moneda='S' THEN total ELSE round(total*".$_SESSION['R_TipoCambio'].",2) END ELSE 0 END AS egresos, CASE WHEN m.tipopersona='P' THEN PM.nombres || PM.apellidos ELSE s.razonsocial END as persona,M.idcaja, C.numero as caja, SC.abreviatura as saloncaja, (PMU.apellidos || ' ' || PMU.nombres) as cajero,
       B.descripcion as banco,TI.descripcion as tipotarjeta,M.numerotarjeta,
       CASE WHEN M.modopago = 'T' AND M.idtipotarjeta=2 THEN M.total-M.totalpagado WHEN M.modopago = 'A' THEN (substr(M.montotarjeta,position('2@' in M.montotarjeta)+2,length(M.montotarjeta)-2-position('1@' in M.montotarjeta)))::numeric ELSE 0 END as montocredito,
       CASE WHEN M.modopago = 'T' AND M.idtipotarjeta=1 THEN M.total-M.totalpagado WHEN M.modopago = 'A' THEN (substr(M.montotarjeta,position('1@' in M.montotarjeta)+2,position('|' in M.montotarjeta)-2-position('1@' in M.montotarjeta)))::numeric ELSE 0 END as montodebito,
       M.total-M.totalpagado as montotarjeta,M.montotarjeta as tarjetas,M.modopago,M.idtipotarjeta
  FROM movimiento M 
  inner join tipodocumento td on td.idtipodocumento=m.idtipodocumento 
  inner join conceptopago cp on cp.idconceptopago=m.idconceptopago 
  LEFT JOIN Persona P ON P.idpersona=m.idpersona AND P.idsucursal=m.idsucursalpersona 
  LEFT JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro 
  LEFT JOIN sucursal s on s.idsucursal=m.idpersona 
  LEFT JOIN Caja C on C.idcaja=M.idcaja AND C.idsucursal=m.idsucursal 
  LEFT JOIN Salon SC on SC.idsalon=C.idsalon AND C.idsucursal=SC.idsucursal 
  LEFT JOIN usuario U on U.idusuario=m.idusuario AND U.idsucursal=m.idsucursalusuario 
  LEFT JOIN Persona PU ON PU.idpersona=U.idpersona AND PU.idsucursal=U.idsucursal 
  LEFT JOIN PersonaMaestro PMU ON PU.idpersonamaestro=PMU.idpersonamaestro 
  LEFT JOIN Banco B on B.idbanco = M.idbanco and B.idsucursal=M.idsucursal
  LEFT JOIN Tipotarjeta TI on TI.idtipotarjeta = M.idtipotarjeta WHERE 1=1 ";
			$sql = $sql . " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '" . $numero . "'";}
                        if(is_numeric($idconceptopago)){
                            if($idconceptopago>0){ $sql = $sql . " AND m.idconceptopago = " . $idconceptopago;}
                        }else{
                            $sql = $sql . " AND cp.descripcion LIKE '" . $idconceptopago . " - %'";
                        }
                        if($idtipodocumento>0){ $sql = $sql . " AND m.idtipodocumento = " . $idtipodocumento;}
			if($persona<>"" ){$sql = $sql . " AND CASE WHEN m.tipopersona='P' THEN CASE WHEN M.nombrespersona='' THEN (PM.apellidos || ' ' || PM.nombres) ELSE (PM.apellidos || ' ' || PM.nombres || ': ' || M.nombrespersona) END ELSE s.razonsocial END LIKE '%" . $persona . "%'";}
			if($comentario<>"" ){$sql = $sql . " AND m.comentario LIKE '%" . $comentario . "%'";}
			if($cajero<>"" ){$sql = $sql . " AND (PMU.apellidos || ' ' || PMU.nombres) LIKE '%" . $cajero . "%'";}
			//if($fecha <>"" ){$sql = $sql . " AND fecha = '".$fecha."'";}
			//CUANDO ORIGENCAJA ES FLUJOS DE CAJA
			if($idcaja>0 and $origencaja=='FC'){ 
				$sql = $sql . " AND m.Idcaja = ".$idcaja." and m.idconceptopago<>18";
				/*if($idusuario>0){ 
					$sql = $sql . " AND m.idusuario = " . $idusuario;
					$idultimaaperturacaja=$this->consultarultimaaperturacaja($idcaja,$idusuario);
					if($idultimaaperturacaja>0){
						$sql.=" AND idmovimiento > ".$idultimaaperturacaja;
					}else{
						$sql.=" AND idmovimiento >= ".$this->consultarultimaapertura();
					}
				}else{
					$idultimaaperturacaja=$this->consultarultimaaperturacaja($idcaja);
					if($idultimaaperturacaja>0){
						$sql.=" AND idmovimiento > ".$idultimaaperturacaja;
					}else{
						$sql.=" AND idmovimiento >= ".$this->consultarultimaapertura();
					}
				}*/
			}
			//CUANDO ORIGENCAJA ES CAJA CHICA (NO TRABAJAMOS POR JORNADAS)
			if($idcaja>0 and $origencaja=='CC'){ 
				$sql = $sql . " AND m.Idcaja = ".$idcaja."";
			}
			if($origencaja=='CC'){ 
				$sql = $sql . " and m.idconceptopago<>17";
				//$sql.=" AND idmovimiento >= ".$this->consultarultimaapertura();
				$sql.=" AND ((m.idconceptopago<>2 AND m.idcaja>0) OR (m.idconceptopago<>2 AND m.idcaja is null))";
			}
			/*if($idusuario>0){
				$fechainicio=$this->consultarultimaaperturafecha($fechainicio,$idcaja,$idusuario);
			}else{
				$fechainicio=$this->consultarultimaaperturafecha($fechainicio);
			}
			$sql = $sql . " AND idmovimiento >= ".$fechainicio;
			if($idusuario>0){
				$fechafin=$this->consultarultimocierrefecha($fechafin,$idcaja,$idusuario);
			}else{
				$fechafin=$this->consultarultimocierrefecha($fechafin);
			}
			if($fechafin!=0 and $fechafin>$fechainicio){$sql = $sql . " AND idmovimiento <= ".$fechafin;}*/
			if($fechainicio <>"" ){$sql = $sql . " AND fecha >= '".$fechainicio."'";}
			if($fechafin <>"" ){$sql = $sql . " AND fecha <= '".$fechafin."'";}
			
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			if($order==1) {$order++;$by="DESC";}
            if($nro_hoja>=0){
			     return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY idmovimiento asc ".chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
            }else{
                 return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY idmovimiento asc ".chr(13));
            }			
		} 	 	
 	}

	function fechasiguiente($f){
	
	$day=substr(trim($f),0,2);
	$mes=substr(trim($f),3,2);
	$year=substr(trim($f),6,4);
		
		if(($year%4) > 0){
			if($mes==2){
				if($day>=28){$mes=$mes+1; $day=1;
				}else{$day=$day+1;}
			}else if($mes==1 || $mes==3 || $mes==5 || $mes==7 || $mes==8 || $mes==10 || $mes==12){
				if($day>=31){$mes=$mes+1; $day=1;
				}else{$day=$day+1;}
			}else if($mes==4 || $mes==6 || $mes==9 || $mes==11){
				if($day>=30){$mes=$mes+1; $day=1;
					}else{$day=$day+1;}
			}			
		}else{
			if($mes==2){
				if($day>=29){$mes=$mes+1; $day=1;
				}else{$day=$day+1;}
			}else if($mes==1 || $mes==3 || $mes==5 || $mes==7 || $mes==8 || $mes==10 || $mes==12){
				if($day>=31){$mes=$mes+1; $day=1;
				}else{$day=$day+1;}
			
			}else if($mes==4 || $mes==6 || $mes==9 || $mes==11){
				if($day>=30){$mes=$mes+1; $day=1;
				}else{$day=$day+1;}
			}		
	
		}
		if($mes==13){$mes=1;$year=$year+1;}
		$cero='00';
		$day=substr($cero,0,2-strlen($day)).$day;
		$mes=substr($cero,0,2-strlen($mes)).$mes;
		$fecha=$day.'/'.$mes.'/'.$year;
	
	return $fecha;
	}
	
    function moverdatos($idcaja="0"){
    	if($idcaja=="0"){
	        $sql = "insert into movimiento select * from movimientohoy where idsucursal=".$_SESSION['R_IdSucursal'];
	        $rst=$this->ejecutarSQL($sql);
	        $sql="delete from movimientohoy where idsucursal=".$_SESSION['R_IdSucursal'];
	        $rst=$this->ejecutarSQL($sql);
	        $sql="insert into detallemovimiento select * from detallemovimientohoy where idsucursal=".$_SESSION['R_IdSucursal'];
	        $rst=$this->ejecutarSQL($sql);        
	        $sql="delete from detallemovimientohoy where idsucursal=".$_SESSION['R_IdSucursal'];
	        $rst=$this->ejecutarSQL($sql);
    	}else{
    		$sql = "insert into movimiento select * from movimientohoy where idsucursal=".$_SESSION['R_IdSucursal']." and idcaja=".$idcaja;
	        $rst=$this->ejecutarSQL($sql);
	        $sql="delete from movimientohoy where idsucursal=".$_SESSION['R_IdSucursal']." and idcaja=".$idcaja;
	        $rst=$this->ejecutarSQL($sql);
	        $sql="insert into detallemovimiento select * from detallemovimientohoy where idsucursal=".$_SESSION['R_IdSucursal'];
	        $rst=$this->ejecutarSQL($sql);        
	        $sql="delete from detallemovimientohoy where idsucursal=".$_SESSION['R_IdSucursal'];
	        $rst=$this->ejecutarSQL($sql);
    	}
    }
    
    function penultimocierre(){
        $sql="SELECT T.idmovimiento 
        from (select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario FROM MOVIMIENTO UNION select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND date( T.fecha ) <= '".$_SESSION['R_FechaProceso']."'
		AND CONCEPTOPAGO.idConceptoPago = 2
		AND T.IDSUCURSAL = '".$_SESSION['R_IdSucursal']."'";
		$sql.=" ORDER BY 1 DESC LIMIT 2";
		$rst=$this->obtenerDataSQL($sql);
        $c=1;
        if($rst->rowCount()>0){
		  while($dato=$rst->fetchObject()){
    		  if($c==2){
	   	         $idpenultimocierre = $dato->idmovimiento;
		      }
              $c++;
		  }
        }else{
            $idpenultimocierre = 0;   
        }
        return $idpenultimocierre;
    }
    
    function consultarCierresLiquidacionGeneral($fechainicio,$fechafin,$idsucursal=0){
        if($idsucursal==0) $idsucursal=$this->gIdSucursal;
        //modifiqe para comparar aperturas
        $sql = "SELECT T.idmovimiento,to_char(date(T.fecha),'dd-MM-yyyy') as fecha 
        from (select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario FROM MOVIMIENTO UNION select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		AND date( T.fecha ) >= '".$fechainicio."'
		and date( T.fecha ) <= '".$fechafin."'
		AND CONCEPTOPAGO.idConceptoPago = 1
		AND T.IDSUCURSAL = ".$idsucursal."
		ORDER BY 1 ASC ";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }

    function consultarCierreSiguiente($idmovimiento,$idsucursal=0,$idcaja=0){
        if($idsucursal==0) $idsucursal=$this->gIdSucursal;
        $sql = "SELECT T.idmovimiento 
        from (select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario FROM MOVIMIENTO UNION select idmovimiento,idtipomovimiento,idconceptopago,estado,fecha,idsucursal,idcaja,idusuario from movimientohoy) T
		INNER JOIN TIPOMOVIMIENTO ON T.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON T.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND T.estado = 'N'
		and idmovimiento>".$idmovimiento;
		if($idcaja!="0") $sql.=" and T.idcaja in ($idcaja)";
		$sql=" AND CONCEPTOPAGO.idConceptoPago = 1
		AND T.IDSUCURSAL = ".$idsucursal."
		order by 1 asc limit 1";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }

    function consultarMovCajaLiquidacionGeneral($idpenultimocierre=0,$ultimocierre=0,$idsucursal=0,$idcaja){
        if($idsucursal==0) $idsucursal=$this->gIdSucursal;
        //$idpenultimocierre = $this->penultimocierre();
        //$ultimocierre=$this->consultarultimocierre($idcaja);
        
        $sql = "select p.descripcion as productos,sum(dma.cantidad) as cantidad,dma.precioventa as preciounitario,round(sum(dma.precioventa*dma.cantidad),2) as preciototal,p.kardex,p.idproducto,c.descripcion as categoria,p.compuesto
        from (select * from movimientohoy union select * from movimiento) as T
        inner join (select * from detallemovimientohoy union select * from detallemovimiento) as D on D.idmovimiento=T.idmovimiento and D.idsucursal=T.idsucursal
        inner join detallemovalmacen dma on dma.idmovimiento=T.idmovimiento and D.iddetallemovalmacen=dma.iddetallemovalmacen and dma.idsucursal=T.idsucursal
        inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
        inner join categoria as c on c.idcategoria=p.idcategoria and p.idsucursal=c.idsucursal
        where T.estado='N' and T.idsucursal=".$idsucursal;
        if($idpenultimocierre>0) $sql .=" and T.idmovimiento>".$idpenultimocierre;
        if($ultimocierre>0) $sql .= "  AND T.idmovimiento <".$ultimocierre;
        if($idcaja!="0") $sql.=" and T.idcaja in ($idcaja)";
//        $sql .= " and T.idmovimiento>75";
        $sql .= " group by p.descripcion,p.idcategoria ,dma.precioventa,p.kardex,p.idproducto,c.descripcion,p.compuesto order by p.descripcion,p.idcategoria,p.kardex asc";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }

    
    function consultarMovCajaLiquidacionDiaria($fecha){
    
        $idpenultimocierre = $this->penultimocierre();
        $ultimocierre=$this->consultarultimocierre($idcaja);
        
        $sql = "select p.descripcion as productos,sum(dma.cantidad) as cantidad,dma.precioventa as preciounitario,round(sum(dma.precioventa*dma.cantidad),2) as preciototal,p.kardex,p.idproducto,c.descripcion as categoria
        from (select * from movimientohoy union select * from movimiento) as T
        inner join (select * from detallemovimientohoy union select * from detallemovimiento) as D on D.idmovimiento=T.idmovimiento and D.idsucursal=T.idsucursal
        inner join detallemovalmacen dma on dma.idmovimiento=T.idmovimiento and D.iddetallemovalmacen=dma.iddetallemovalmacen and dma.idsucursal=T.idsucursal
        inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
        inner join categoria as c on c.idcategoria=p.idcategoria and p.idsucursal=c.idsucursal
        where T.estado='N' and T.idsucursal=".$this->gIdSucursal;
        if($idpenultimocierre>0) $sql .=" and T.idmovimiento>".$idpenultimocierre;
        if($ultimocierre>0) $sql .= "  AND T.idmovimiento <".$ultimocierre;
//        $sql .= " and T.idmovimiento>75";
        $sql .= " group by p.descripcion,p.idcategoria ,dma.precioventa,p.kardex,p.idproducto,c.descripcion order by p.descripcion,p.idcategoria,p.kardex asc";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    function consultarLiquidacionEgresosGeneral($idpenultimocierre=0,$ultimocierre=0,$idsucursal=0){
        if($idsucursal==0) $idsucursal=$_SESSION['R_IdSucursal'];
        //$idpenultimocierre = $this->penultimocierre();
        //$ultimocierre=$this->consultarultimocierre($idcaja);
        $sql = "SELECT sum(TOTAL) as total,conceptopago.descripcion as conceptopago
		FROM MOVIMIENTO
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTO.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTO.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA' and conceptopago.tipo='E'
		AND MOVIMIENTO.estado = 'N'
		AND MOVIMIENTO.IDSUCURSAL=".$idsucursal."
		AND MOVIMIENTO.MONEDA='S'
		AND MOVIMIENTO.idconceptopago<>2 AND (MOVIMIENTO.idcaja>0 OR MOVIMIENTO.idcaja is null)";
		//AND movimiento.fecha = '".$fecha."'
        if($ultimocierre>0)	$sql = $sql . " AND idmovimiento >= ".$ultimocierre;
        if($idpenultimocierre>0) $sql = $sql . " AND idmovimiento<".$idpenultimocierre;
        $sql .= " group by conceptopago.descripcion";
       //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    function consultarLiquidacionEgresos(){
        $idpenultimocierre = $this->penultimocierre();
        $ultimocierre=$this->consultarultimocierre($idcaja);
        $sql = "SELECT sum(TOTAL) as total,conceptopago.descripcion as conceptopago
		FROM (select * from movimientohoy union select * from movimiento)MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA' and conceptopago.tipo='E'
		AND MOVIMIENTOHOY.estado = 'N'
		AND MOVIMIENTOHOY.IDSUCURSAL=".$_SESSION['R_IdSucursal']."
		AND MOVIMIENTOHOY.MONEDA='S'
		AND MOVIMIENTOHOY.idconceptopago<>2 AND (MOVIMIENTOHOY.idcaja>0 OR MOVIMIENTOHOY.idcaja is null)";
		//AND movimiento.fecha = '".$fecha."'
        if($ultimocierre>0)	$sql = $sql . " AND idmovimiento >= ".$ultimocierre;
        if($idpenultimocierre>0) $sql = $sql . " AND idmovimiento<".$idpenultimocierre;
        $sql .= " group by conceptopago.descripcion";
       //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    function montodecierreLiquidacionGeneral($siguientecierre=0,$ultimocierre=0,$idsucursal=0){	
	    if($idsucursal==0) $idsucursal=$_SESSION['R_IdSucursal'];
		$sql="SELECT sum(CASE WHEN tipo='I' THEN TOTALPAGADO ELSE 0 END) as ingreso,sum(CASE WHEN tipo='E' THEN TOTAL ELSE 0 END) as egreso,sum(CASE WHEN idbanco IS NOT NULL AND idtipotarjeta=2 THEN TOTAL-TOTALPAGADO ELSE 0 END ) as montocredito,sum(CASE WHEN idbanco IS NOT NULL AND idtipotarjeta = 1 THEN TOTAL-TOTALPAGADO ELSE 0 END ) as montodebito
		FROM (select * from movimiento union select * from movimientohoy) as MOVIMIENTOHOY
		INNER JOIN TIPOMOVIMIENTO ON MOVIMIENTOHOY.idtipomovimiento = TIPOMOVIMIENTO.idtipomovimiento
		INNER JOIN CONCEPTOPAGO ON MOVIMIENTOHOY.idconceptopago = CONCEPTOPAGO.idconceptopago
		WHERE TIPOMOVIMIENTO.descripcion = 'CAJA'
		AND MOVIMIENTOHOY.estado = 'N'
		AND MOVIMIENTOHOY.IDSUCURSAL=".$idsucursal."
		AND MOVIMIENTOHOY.MONEDA='S'
        and CONCEPTOPAGO.idConceptoPago <> 1
		AND MOVIMIENTOHOY.idconceptopago<>2 AND (MOVIMIENTOHOY.idcaja>0 OR MOVIMIENTOHOY.idcaja is null)";
		//AND movimiento.fecha = '".$fecha."'
		$sql = $sql . " AND idmovimiento>".$ultimocierre;
        if($siguientecierre!=0) $sql .= " and idmovimiento <".$siguientecierre;
        //echo $sql;
		return $this->obtenerDataSQL($sql);
	}

    function consultarStockAperturaGeneral($idproducto,$idpenultimocierre=0,$ultimocierre=0,$idsucursal=0){
        if($idsucursal==0) $idsucursal = $_SESSION['R_IdSucursal'];
    //    $idpenultimocierre = $this->penultimocierre();
    //    $ultimocierre=$this->consultarultimocierre($idcaja);
        $sql = "select * 
        from kardex 
        where 1=1 and idsucursal=".$idsucursal;
        if($idproducto>0) $sql .= " and idproducto=".$idproducto;
       // if($ultimocierre>0)	$sql = $sql . " AND idmovimiento <= ".$ultimocierre;
        if($idpenultimocierre>0) $sql = $sql . " and idmovimiento<=".$idpenultimocierre;
//        $sql .= " and idmovimiento>75";
        $sql .= " order by idkardex desc limit 1 offset 0";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    function consultarStockCierreGeneral($idproducto,$penultimocierre,$ultimocierre=0,$idsucursal=0){
        if($idsucursal==0) $idsucursal = $_SESSION['R_IdSucursal'];
    //    $idpenultimocierre = $this->penultimocierre();
    //    $ultimocierre=$this->consultarultimocierre($idcaja);
        $sql = "select k.* 
        from kardex as k
        where 1=1 and k.idsucursal=".$idsucursal;
        if($idproducto>0) $sql .= " and k.idproducto=".$idproducto;
        //if($penultimocierre>0)	$sql = $sql . " AND k.idmovimiento >= ".$penultimocierre;
        if($ultimocierre>0)	$sql =$sql . " AND k.idmovimiento < ".$ultimocierre;
//        $sql .= " and idmovimiento>75";
        $sql .= " order by k.idkardex desc limit 1 offset 0";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    
    //Para obtener el stock al aperturar la caja
    function consultarStockApertura($idproducto){
        $idpenultimocierre = $this->penultimocierre();
        $ultimocierre=$this->consultarultimocierre($idcaja);
        $sql = "select * 
        from kardex 
        where 1=1 and idsucursal=".$_SESSION['R_IdSucursal'];
        if($idproducto>0) $sql .= " and idproducto=".$idproducto;
        //if($ultimocierre>0)	$sql = $sql . " AND idmovimiento <= ".$ultimocierre;
        if($idpenultimocierre>0) $sql = $sql . " and idmovimiento>".$idpenultimocierre;
//        $sql .= " and idmovimiento>75";
        $sql .= " order by idkardex asc limit 1 offset 0";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    
    function consultarMovCajaLiquidacionMozo($fecha){
        $idpenultimocierre = $this->penultimocierre();
        $ultimocierre=$this->consultarultimocierre($idcaja);
        
        $sql = "SELECT m.idresponsable,m.idsucursalresponsable,PM2.nombres as Responsable,sum(total)  as vendido
FROM ( select * from movimiento) M 
 LEFT JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal 
 LEFT JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro 
 LEFT JOIN mesa Me ON Me.idmesa=M.idmesa and m.idsucursal=Me.idsucursal 
 LEFT JOIN Salon SC on SC.idsalon=Me.idsalon and SC.idsucursal=Me.idsucursal 
 WHERE 1=1 AND m.Estado in ('N','I') AND m.IdSucursal=".$this->gIdSucursal." AND m.IdTipoMovimiento = 5 AND m.situacion = 'P'";
        if($idpenultimocierre>0) $sql .=" and M.idmovimiento>".$idpenultimocierre;
        if($ultimocierre>0) $sql .= "  AND M.idmovimiento <".$ultimocierre;
        
        $sql .= " group by m.idresponsable,PM2.nombres,m.idsucursalresponsable order by PM2.nombres asc";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    function consultarMovCajaLiquidacionGeneralMozo($idpenultimocierre=0,$ultimocierre=0,$idsucursal=0){
        if($idsucursal==0) $idsucursal=$_SESSION['R_IdSucursal'];
        //$idpenultimocierre = $this->penultimocierre();
        //$ultimocierre=$this->consultarultimocierre($idcaja);
        
        $sql = "SELECT m.idresponsable,m.idsucursalresponsable,PM2.nombres as Responsable,sum(total)  as vendido
FROM ( select * from movimiento) M 
 LEFT JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal 
 LEFT JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro 
 LEFT JOIN mesa Me ON Me.idmesa=M.idmesa and m.idsucursal=Me.idsucursal 
 LEFT JOIN Salon SC on SC.idsalon=Me.idsalon and SC.idsucursal=Me.idsucursal 
 WHERE 1=1 AND m.Estado in ('N','I') AND m.IdSucursal=".$idsucursal." AND m.IdTipoMovimiento = 5 AND m.situacion = 'P'";
        if($idpenultimocierre>0) $sql .=" and M.idmovimiento>".$idpenultimocierre;
        if($ultimocierre>0) $sql .= "  AND M.idmovimiento <".$ultimocierre;
        
        $sql .= " group by m.idresponsable,PM2.nombres,m.idsucursalresponsable order by PM2.nombres asc";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    function consultarProductoxMozo($idresponsable,$idsucursalresponsable){
        $idpenultimocierre = $this->penultimocierre();
        $ultimocierre=$this->consultarultimocierre($idcaja);
        
        $sql = "select m.idsucursalresponsable,p.idproducto,p.descripcion as producto,sum(dma.cantidad) as cantidad,dma.precioventa,round(sum(dma.cantidad*dma.precioventa),2) as total,p.kardex,c.descripcion as categoria
FROM ( select * from movimiento) M 
 LEFT JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal 
 LEFT JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro 
inner join detallemovalmacen dma on dma.idmovimiento=M.idmovimiento and dma.idsucursal=M.idsucursal 
inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
inner join categoria as c on c.idcategoria=p.idcategoria and c.idsucursal=p.idsucursal
WHERE 1=1 AND m.Estado in ('N','I') AND m.IdSucursal=".$this->gIdSucursal." AND m.IdTipoMovimiento = 5 AND m.situacion = 'P'  
and m.idresponsable=".$idresponsable." and m.idsucursalresponsable=".$idsucursalresponsable;
        if($idpenultimocierre>0) $sql .=" and M.idmovimiento>".$idpenultimocierre;
        if($ultimocierre>0) $sql .= "  AND M.idmovimiento <".$ultimocierre;
        $sql .=" group by m.idsucursalresponsable,p.idproducto,p.descripcion,dma.precioventa,p.kardex,c.descripcion order by p.descripcion";
        //echo $sql;
        return $this->obtenerDataSQL($sql);
    }
    
    function consultarGeneralProductoxMozo($idresponsable,$idsucursalresponsable,$idpenultimocierre=0,$ultimocierre=0,$idsucursal=0){
        if($idsucursal==0) $idsucursal=$_SESSION['R_IdSucursal'];
        //$idpenultimocierre = $this->penultimocierre();
        //$ultimocierre=$this->consultarultimocierre($idcaja);
        
        $sql = "select m.idsucursalresponsable,p.idproducto,p.descripcion as producto,sum(dma.cantidad) as cantidad,dma.precioventa,round(sum(dma.cantidad*dma.precioventa),2) as total
FROM ( select * from movimiento) M 
 LEFT JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal 
 LEFT JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro 
inner join detallemovalmacen dma on dma.idmovimiento=M.idmovimiento and dma.idsucursal=M.idsucursal 
inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
WHERE 1=1 AND m.Estado in ('N','I') AND m.IdSucursal=".$idsucursal." AND m.IdTipoMovimiento = 5 AND m.situacion = 'P'  
and m.idresponsable=".$idresponsable." and m.idsucursalresponsable=".$idsucursalresponsable;
        if($idpenultimocierre>0) $sql .=" and M.idmovimiento>".$idpenultimocierre;
        if($ultimocierre>0) $sql .= "  AND M.idmovimiento <".$ultimocierre;
        $sql .=" group by m.idsucursalresponsable,p.idproducto,p.descripcion,dma.precioventa order by p.descripcion";
        //print_R($sql);
        return $this->obtenerDataSQL($sql);
    }
}

?>