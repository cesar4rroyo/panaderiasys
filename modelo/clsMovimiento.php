<?php session_start();
ini_set('memory_limit', '512M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB

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
class clsMovimiento extends clsAccesoDatos
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($user, $pass);
	}

	function insertarMovimiento($idconceptopago, $idtipomovimiento, $numero, $idtipodocumento, $formapago, $fecha, $fechaproximacancelacion, $fechaultimopago, $nropersonas, $idmesa, $moneda, $inicial, $subtotal, $igv, $total, $totalpagado, $idusuario, $tipopersona, $idpersona, $idresponsable, $idmovimientoref, $idsucursalref, $comentario, $situacion, $idcaja='',$idsucursalusuario='',$idsucursalpersona='',$idsucursalresponsable='',$nombrespersona='',$idbanco='',$idtipotarjeta='',$numerotarjeta='',$idsucursal='',$dinero=0)
 	{ 	
		if($fecha!='LOCALTIMESTAMP') $fecha="'".$fecha."'";
		if($fechaproximacancelacion=='') $fechaproximacancelacion="null";
		if($fechaproximacancelacion!='LOCALTIMESTAMP' and $fechaproximacancelacion!='null') $fechaproximacancelacion="'".$fechaproximacancelacion."'";		
		if($fechaultimopago=='') $fechaultimopago="null";
		if($fechaultimopago!='LOCALTIMESTAMP' and $fechaultimopago!='null') $fechaultimopago="'".$fechaultimopago."'";		
		if(!isset($idmovimientoref)) $idmovimientoref="null";
		if(!isset($idsucursalref)) $idsucursalref="null";
		if(!isset($idcaja) or $idcaja=='') $idcaja="null";
		if(!isset($idsucursalusuario) or $idsucursalusuario=='') $idsucursalusuario=$_SESSION['R_IdSucursalUsuario'];
		if(!isset($idsucursalpersona) or $idsucursalpersona=='') $idsucursalpersona=$this->gIdSucursal;
		if(!isset($idsucursalresponsable) or $idsucursalresponsable=='') $idsucursalresponsable=$_SESSION['R_IdSucursalUsuario'];
		if(!isset($idbanco) or $idbanco=='') $idbanco="null";
        if(!isset($idtipotarjeta) or $idtipotarjeta=='') $idtipotarjeta="null";
        if(!isset($numerotarjeta) or $numerotarjeta=='') $numerotarjeta="null";
        if($idsucursal=='') $idsucursal=$this->gIdSucursal; 
		$sql = "select up_AgregarMovimiento($idconceptopago, ".$idsucursal.", $idtipomovimiento, '$numero', $idtipodocumento, '$formapago', $fecha, $fechaproximacancelacion, $fechaultimopago, $nropersonas, $idmesa, '$moneda', ".$this->miles($inicial).", ".$this->miles($subtotal).", ".$this->miles($igv).", ".$this->miles($total).", ".$this->miles($totalpagado).", $idusuario, '$tipopersona', $idpersona, $idresponsable, $idmovimientoref, $idsucursalref, '$comentario', '$situacion', $idcaja, $idsucursalusuario,$idsucursalpersona,$idsucursalresponsable, UPPER('$nombrespersona'),$idbanco,$idtipotarjeta,'$numerotarjeta',$dinero) as idmovimiento";
        //print_r($this->obtenerDataSQL($sql));
        return $this->obtenerDataSQL($sql);
		//echo $sql;
 	}

	function actualizarMovimiento($id, $idconceptopago, $idtipomovimiento, $numero, $idtipodocumento, $formapago, $fecha, $fechaproximacancelacion, $fechaultimopago, $nropersonas, $idmesa, $moneda, $inicial, $subtotal, $igv, $total, $totalpagado, $idusuario, $tipopersona, $idpersona, $idresponsable, $idmovimientoref, $idsucursalref, $comentario, $situacion, $idcaja='',$idsucursalusuario='',$idsucursalpersona='',$idsucursalresponsable='',$nombrespersona='',$dinero=0)
 	{
		if($fecha!='LOCALTIMESTAMP') $fecha="'".$fecha."'";
		if($fechaproximacancelacion=='') $fechaproximacancelacion="null";
		if($fechaproximacancelacion!='LOCALTIMESTAMP' and $fechaproximacancelacion!='null') $fechaproximacancelacion="'".$fechaproximacancelacion."'";		
		if($fechaultimopago=='') $fechaultimopago="null";
		if($fechaultimopago!='LOCALTIMESTAMP' and $fechaultimopago!='null') $fechaultimopago="'".$fechaultimopago."'";		
		if(!isset($idmovimientoref)) $idmovimientoref="null";
		if(!isset($idsucursalref)) $idsucursalref="null";
		if(!isset($idcaja) or $idcaja=='') $idcaja="null";
		if(!isset($idsucursalusuario) or $idsucursalusuario=='') $idsucursalusuario=$_SESSION['R_IdSucursalUsuario'];
		if(!isset($idsucursalpersona) or $idsucursalpersona=='') $idsucursalpersona=$this->gIdSucursal;
		if(!isset($idsucursalresponsable) or $idsucursalresponsable=='') $idsucursalresponsable=$_SESSION['R_IdSucursalUsuario'];
		
   		$sql = "execute up_ModificarMovimiento $id, $idconceptopago, ".$this->gIdSucursal.", $idtipomovimiento, '$numero', $idtipodocumento, '$formapago', $fecha, $fechaproximacancelacion, $fechaultimopago, $nropersonas, $idmesa, '$moneda', ".$this->miles($inicial).", ".$this->miles($subtotal).", ".$this->miles($igv).", ".$this->miles($total).", ".$this->miles($totalpagado).", $idusuario, '$tipopersona', $idpersona, $idresponsable, $idmovimientoref, $idsucursalref, '$comentario', '$situacion', $idcaja, $idsucursalusuario,$idsucursalpersona,$idsucursalresponsable, upper('$nombrespersona'),$dinero";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function actualizarMontoPagadoMovimiento($id, $monto)
 	{		
   		$sql = "update movimientohoy set totalpagado=totalpagado+".$this->miles($monto)." where idmovimiento=".$id." and idsucursal=".$this->gIdSucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

	function eliminarMovimiento($id,$comentario='')
 	{
 	    if($comentario!='' && isset($comentario))
   		   $sql = "execute up_EliminarMovimiento $id, ".$this->gIdSucursal.", '$comentario'";
        else
           $sql = "execute up_EliminarMovimiento $id, ".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function anularMovimiento($id)
 	{
   		$sql = "execute up_AnularMovimiento $id, ".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function anularMovCajaaPartirdeVenta($id)
 	{
   		$sql = "execute up_anularMovCajaaPartirdeVenta $id, ".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function cambiarSituacionPedidoaPartirdeVenta($id)
 	{
   		$sql = "execute up_cambiarSituacionPedidoaPartirdeVenta $id,".$this->gIdSucursal;
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
 
	function consultarMovimiento($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero='', $situacion='', $fechainicio='',$fechafin='',$idusuario=0,$jornada=0,$idtipodocumento=0,$persona='',$responsable='',$comentario='',$mesa='',$idcaja=0,$estado='',$tabla='')
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
            if($tabla=='' || substr($nro_hoja,0,1)=="h"){
                if(substr($nro_hoja,0,1)=="h"){
    			  $tabla="historico";
                  $nro_hoja=substr($nro_hoja,1,strlen($nro_hoja)-1);
    			}else if($nro_hoja>0) {
                  $tabla="";
    			}
            }
            //
            if($tabla!='' && isset($tabla)){
                $tabla="historico";
            }
            //echo $tabla;
			//$numero = "%".$numero."%";
			if($tipomovimiento==6){//RESERVA
				$sql = "SELECT idmovimiento, M.idtipodocumento, m.idsucursal, m.idtipomovimiento, m.numero, td.abreviatura as tipodocumentoabreviatura,td.descripcion as tipodocumento, to_char(fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, to_char(fechaproximacancelacion,'DD/MM/YYYY HH:mi:ss am') as fechaproximacancelacion, 
                m.nropersonas, m.idmesa, Sa.descripcion as salon, idusuario, m.tipopersona, m.idpersona, m.idresponsable, idmovimientoref, 
                idsucursalref, m.comentario, CASE WHEN m.situacion='N' THEN 'Normal' WHEN m.situacion='O' THEN 'Pedido' WHEN m.situacion='A' THEN 'Atendido' WHEN m.situacion='P' THEN 'Consumido' END as situacion, m.situacion as situacion2, m.estado,(PM.apellidos || ' ' || PM.nombres) as cliente,PM2.nombres as Responsable,Sa.idsalon, idsucursalusuario, idsucursalpersona, idsucursalresponsable, M.nombrespersona
                FROM ("; 
                if($tabla=="historico"){
                    $sql.=" select * from movimiento UNION select * from movimientohoy";
                }
                else {
                    $sql.=" select * from movimientohoy";
                }
                $sql.=") M 
		inner join tipodocumento td on td.idtipodocumento=m.idtipodocumento 
		LEFT JOIN Persona P ON P.idpersona=m.idpersona and m.idsucursalpersona=P.idsucursal 
		LEFT JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro 
		LEFT JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal 
		LEFT JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro 
		LEFT JOIN Salon Sa ON Sa.idsalon=M.idmesa and m.idsucursal=Sa.idsucursal WHERE 1=1 ";
				$sql = $sql . " AND m.Estado in ('N','I') AND m.IdSucursal=".$this->gIdSucursal." ";
				if($persona<>""){$sql = $sql . " AND (PM.apellidos || ' ' || PM.nombres) END LIKE '%" . $persona . "%'";}
				/*if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
				if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '" . $numero . "'";}
				if($situacion<>''){$sql = $sql . " AND m.situacion = '" . $situacion . "'";}
				if($idusuario>0){ $sql = $sql . " AND m.idusuario = " . $idusuario;}*/
				if($fechainicio<>'' and $fechafin<>''){$sql = $sql . " AND ((fecha >= '" . $fechainicio . " 00:00:00.000' AND fecha <= '" . $fechafin . " 23:59:59.999') OR (fechaproximacancelacion >= '" . $fechainicio . " 00:00:00.000' AND fechaproximacancelacion <= '" . $fechafin . " 23:59:59.999'))";}				
			}else{
				$sql = "SELECT M.idmovimiento, M.idtipodocumento, M.idconceptopago, m.idsucursal, m.idtipomovimiento, m.numero, td.abreviatura as tipodocumentoabreviatura,td.descripcion as tipodocumento, M.formapago, to_char(M.fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, to_char(M.fechaproximacancelacion,'DD/MM/YYYY HH:mi:ss am') as fechaproximacancelacion, M.fechaultimopago, 
                m.nropersonas, m.idmesa, Me.numero as mesa, M.moneda, M.inicial, M.subtotal, M.igv, M.total, M.totalpagado, 
		        M.idusuario, m.tipopersona, m.idpersona, m.idresponsable, M.idmovimientoref, 
                M.idsucursalref, m.comentario,CASE WHEN m.situacion='O' and m.estado<>'A' THEN 'Pedido' WHEN m.situacion='A' THEN 'Atendido' WHEN m.situacion='P' THEN 'Consumido' WHEN m.estado='A' THEN 'Anulado' END as situacion, m.situacion as situacion2, m.estado,PM2.nombres as Responsable,Me.idsalon, M.idcaja, C.numero as caja, SC.abreviatura as saloncaja, M.idsucursalusuario,
                 M.idsucursalpersona, M.idsucursalresponsable, mref.numero as numeroref, mref.idpersona as idpersonaref, mref.idsucursalpersona as idsucursalpersonaref, M.nombrespersona, CASE WHEN M.nombrespersona='' THEN (PM.apellidos || ' ' || PM.nombres) ELSE (PM.apellidos || ' ' || PM.nombres || ': ' || M.nombrespersona) END as Cliente, CASE WHEN M.nombrespersona='' THEN (PM.apellidos || ' ' || PM.nombres) ELSE (PM.apellidos || ' ' || PM.nombres || ': ' || M.nombrespersona) END as Proveedor, 
                 (LOCALTIMESTAMP(0)-M.fecha) as tiempotranscurrido,sc.descripcion as salon,PM3.nombres as Mesero,P.telefonomovil,P.telefonofijo,P.direccion,M.dinero,M.comentario,M.modopago,M.montotarjeta,M.tipoventa,M.idtipotarjeta,PM.nrodoc,M.motivo
                FROM (";
                if($tabla=="historico"){
                    $sql.=" select * from movimiento UNION select * from movimientohoy";
                    $union = "select * from movimiento UNION select * from movimientohoy";
                }
                else {
                    $sql.=" select * from movimientohoy";
                    $union = "select * from movimientohoy";
                }
                $sql.=") M 
		inner join tipodocumento td on td.idtipodocumento=m.idtipodocumento 
		LEFT JOIN Persona P ON P.idpersona=m.idpersona and m.idsucursalpersona=P.idsucursal 
		LEFT JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro 
		LEFT JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal 
		LEFT JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro 
		LEFT JOIN mesa Me ON Me.idmesa=M.idmesa and m.idsucursal=Me.idsucursal 
		LEFT JOIN Caja C on C.idcaja=M.idcaja and m.idsucursal=C.idsucursal 
		LEFT JOIN Salon SC on SC.idsalon=Me.idsalon and SC.idsucursal=Me.idsucursal 
		LEFT JOIN ($union) mref on mref.idmovimiento=M.idmovimientoref and mref.idsucursal=M.idsucursalref 
        LEFT JOIN Persona MO ON MO.idpersona=mref.idresponsable and mref.idsucursalresponsable=MO.idsucursal 
		LEFT JOIN PersonaMaestro PM3 ON MO.idpersonamaestro=PM3.idpersonamaestro 
        WHERE 1=1 ";
				$sql = $sql . " AND m.IdSucursal=".$this->gIdSucursal." ";
				if($estado<>''){
                    if($estado=="A")//anulado
                        $sql = $sql . " and m.Estado in ('A')";
                    if($estado=="N")//normal
                        $sql = $sql . " and m.Estado in ('N','I')";
                    if($estado=="I")
                    	$sql = $sql . " and m.estado in ('$estado')";
                }else{
                    $sql = $sql . " AND m.Estado in ('N','I') "; 
                }
                if($persona<>"" and $tipomovimiento<>5){$sql = $sql . " AND CASE WHEN M.nombrespersona='' THEN (PM.apellidos || ' ' || PM.nombres) ELSE (PM.apellidos || ' ' || PM.nombres || ': ' || M.nombrespersona) END LIKE '%" . $persona . "%'";}
				if($persona<>"" and $tipomovimiento==5){$sql = $sql . " AND (M.nombrespersona LIKE '%" . $persona . "%' OR (PM.apellidos || ' ' || PM.nombres) LIKE '%" . $persona . "%')";}
				if($mesa <>"" ){$sql = $sql . " AND Me.numero LIKE '%" . $mesa . "%'";}
/*				if($fechainicio<>''){$sql = $sql . " AND M.fecha >= '" . $fechainicio . " 00:00:00.000'";}
				if($fechafin<>''){$sql = $sql . " AND M.fecha <= '" . $fechafin . " 23:59:59.999'";}*/
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
					$sql = $sql . " AND M.idmovimiento >= ".$inicio;
					if($fin>0 and $fin>$inicio) $sql = $sql . " AND M.idmovimiento <=".$fin;
				}else{
					if(strlen($fechainicio)>10){
						if($fechainicio<>''){$sql = $sql . " AND (M.fecha >= '" . $fechainicio . "' OR M.fecha is null) ";}
					}else{
						if($fechainicio<>''){$sql = $sql . " AND (M.fecha >= '" . $fechainicio . " 00:00:00.000' OR M.fecha is null) ";}
					}
					if(strlen($fechafin)>10){
						if($fechafin<>''){$sql = $sql . " AND (M.fecha <= '" . $fechafin . "'  OR M.fecha is null) ";}
					}else{
						if($fechafin<>''){$sql = $sql . " AND (M.fecha <= '" . $fechafin . " 23:59:59.999'  OR M.fecha is null) ";}
					}
				}				
			}
			if($idcaja>0) $sql.=" and m.idcaja=$idcaja";
			if($id>0){ $sql = $sql . " AND m.IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '%" . $numero . "%'";}
			if($situacion<>''){$sql = $sql . " AND m.situacion = '" . $situacion . "'";}
			if($idusuario>0){ $sql = $sql . " AND m.idusuario = " . $idusuario;}
			if($idtipodocumento>0){ $sql = $sql . " AND m.idtipodocumento = " . $idtipodocumento;}
			if($responsable<>"" ){$sql = $sql . " AND PM2.nombres LIKE '%" . $responsable . "%'";}
			if($comentario<>"" ){$sql = $sql . " AND m.comentario LIKE '%" . $comentario . "%'";}
			
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
            //echo "SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg);
            if($nro_hoja>=0){
                return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
            }else{
          	    return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13));
            }		
		} 	 	
 	}
	
	function consultarMovimientoInterna($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero, $situacion='', $nombrespersona='')
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
									
			$sql = "SELECT idmovimiento, idconceptopago, M.idsucursal, idtipomovimiento, M.numero, 
       idtipodocumento, formapago, to_char(fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, fechaproximacancelacion, fechaultimopago, 
       M.nropersonas, M.idmesa, moneda, inicial, subtotal, igv, total, totalpagado, 
       idusuario, M.tipopersona, M.idpersona, M.idresponsable, idmovimientoref, 
       idsucursalref, M.comentario, CASE WHEN M.situacion='O' THEN 'Pedido' WHEN M.situacion='A' THEN 'Atendido' WHEN M.situacion='P' THEN 'Consumido' END as situacion, M.situacion as situacion2, M.estado, Me.numero as mesa, PM.nombres as responsable, idsucursalusuario, idsucursalpersona, idsucursalresponsable, M.nombrespersona,sc.descripcion as salon
    FROM (select * from movimiento UNION select * from movimientohoy) M 
INNER JOIN mesa Me ON Me.idmesa=M.idmesa and Me.idsucursal=M.idsucursal 
INNER JOIN Persona P ON P.idpersona=M.idresponsable and M.idsucursalresponsable=P.idsucursal 
INNER JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro 
LEFT JOIN Salon SC on SC.idsalon=Me.idsalon and SC.idsucursal=M.idsucursal 
WHERE 1=1 ";
			$sql = $sql . " AND M.Estado = 'N' AND M.IdSucursal=".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$numero = "%".$numero."%";$sql = $sql . " AND M.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND M.situacion = '" . $situacion . "'";}
			if($nombrespersona <>"" ){$nombrespersona = "%".$nombrespersona."%";$sql = $sql . " AND M.nombrespersona LIKE '" . $nombrespersona . "'";}
			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}

	function consultarMovimientoComprobante($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero, $situacion='')
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
			
			$sql = "SELECT M.idmovimiento, M.idconceptopago, M.idsucursal, M.idtipomovimiento, M.numero, 
       M.idtipodocumento, M.formapago, to_char(M.fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, M.fechaproximacancelacion, M.fechaultimopago, 
       M.nropersonas, M.idmesa, M.moneda, M.inicial, M.subtotal, M.igv, M.total, M.totalpagado, 
       M.idusuario, M.tipopersona, M.idpersona, M.idresponsable, M.idmovimientoref, 
       M.idsucursalref, M.comentario, M.estado, PM.nombres as responsable, M.idsucursalusuario, M.idsucursalpersona, M.idsucursalresponsable, pedido.idmovimiento, pedido.nombrespersona
       FROM (select * from movimiento UNION select * from movimientohoy) M left JOIN Persona P ON P.idpersona=M.idresponsable and M.idsucursalPersona=P.idsucursal 
       left JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro 
       left join (select * from detallemovimiento union select * from detallemovimientohoy) dm on dm.idmovimiento=M.idmovimiento and m.idsucursal=dm.idsucursal 
       left join movimientohoy pedido on dm.idmovimientoref=pedido.idmovimiento and dm.idsucursal=pedido.idsucursal WHERE 1=1 ";
			$sql = $sql . "  AND M.IdSucursal=".$this->gIdSucursal." ";//AND M.Estado = 'N'
			if($id>0){ $sql = $sql . " AND M.IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$sql = $sql . " AND M.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND M.situacion = '" . $situacion . "'";}
			
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT distinct ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function consultarMovimientoComprobanteCompra($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero, $situacion='')
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
			
			$sql = "SELECT M.idmovimiento, M.idconceptopago, M.idsucursal, M.idtipomovimiento, M.numero, 
       M.idtipodocumento, M.formapago, to_char(M.fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, M.fechaproximacancelacion, M.fechaultimopago, 
       M.nropersonas, M.idmesa, M.moneda, M.inicial, M.subtotal, M.igv, M.total, M.totalpagado, 
       M.idusuario, M.tipopersona, M.idpersona, M.idresponsable, M.idmovimientoref, 
       M.idsucursalref, M.comentario, M.estado, PM.nombres as responsable, M.idsucursalusuario, M.idsucursalpersona, M.idsucursalresponsable
  FROM (select * from movimiento UNION select * from movimientohoy) M INNER JOIN Persona P ON P.idpersona=M.idresponsable and M.idsucursalPersona=P.idsucursal INNER JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro WHERE 1=1 ";
			$sql = $sql . " AND M.Estado = 'N' AND M.IdSucursal=".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND M.IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$sql = $sql . " AND M.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND M.situacion = '" . $situacion . "'";}
			
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT distinct ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
		} 	 	
 	}
	
	function buscarMovimiento($id, $tipomovimiento, $numero, $situacion='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarMovimiento ".$nro_reg.", $nro_hoja, '$order', $by, $id, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			$numero = "%".$numero."%";
			
			$sql = "SELECT M.idmovimiento, M.idconceptopago, M.idsucursal, M.idtipomovimiento, M.numero, 
       M.idtipodocumento, M.formapago, to_char(M.fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, M.fechaproximacancelacion, M.fechaultimopago, 
       M.nropersonas, M.idmesa, M.moneda, M.inicial, M.subtotal, M.igv, M.total, M.totalpagado, 
       M.idusuario, M.tipopersona, M.idpersona, M.idresponsable, M.idmovimientoref, 
       M.idsucursalref, M.comentario, M.estado,M.situacion, PM.nombres as responsable, M.idsucursalusuario, M.idsucursalpersona, M.idsucursalresponsable, PM2.nombres as persona
  FROM (select * from movimiento UNION select * from movimientohoy) M INNER JOIN Persona P ON P.idpersona=M.idresponsable and M.idsucursalPersona=P.idsucursal INNER JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro INNER JOIN Persona P2 ON P2.idpersona=M.idpersona and M.idsucursalPersona=P2.idsucursal INNER JOIN PersonaMaestro PM2 ON P2.idpersonamaestro=PM2.idpersonamaestro WHERE 1=1 ";
			$sql = $sql . " AND M.Estado = 'N' AND M.IdSucursal=".$this->gIdSucursal." ";
			if($id>0){ $sql = $sql . " AND M.IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$sql = $sql . " AND M.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND M.situacion = '" . $situacion . "'";}
			//print_r($sql);
			return $this->obtenerDataSQL($sql);
			//echo $sql;
		} 	 	
 	}
		
	function buscarDetalleProducto($idmovimiento,$historico='')
 	{
        if($historico==""){
            $tabla="select * from movimientohoy";
        }else{
            $tabla="select * from movimientohoy union all select * from movimiento";
        }
		$sql = "select dma.idproducto, codigo, p.descripcion as producto, dma.idunidad, u.descripcion as unidad, cantidad, preciocompra, precioventa, moneda, p.idsucursal,dma.idsucursalproducto,p.abreviatura,p.idimpresora,dma.comentario,p.kardex,p.compuesto,p.idunidadbase,dma.idmovimiento
        from detallemovalmacen dma 
        inner join producto p on p.idproducto=dma.idproducto and dma.idsucursalproducto=p.idsucursal
        inner join unidad u on u.idunidad=dma.idunidad 
        inner join ($tabla) m on m.idmovimiento=dma.idmovimiento and m.idsucursal=dma.idsucursal where dma.idmovimiento=".$idmovimiento." AND dma.IdSucursal=".$this->gIdSucursal;
        //echo $sql;
		return $this->obtenerDataSQL($sql);
 	}
    
        function buscarDetalleProducto2($idmovimiento)
 	{
		$sql = "select dma.idproducto, codigo, p.descripcion as producto, dma.idunidad, u.descripcion as unidad, cantidad, preciocompra, precioventa, moneda, p.idsucursal,dma.idsucursalproducto,p.abreviatura,p.idimpresora
        from detallemovalmacen dma 
        left join producto p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal 
        left join unidad u on u.idunidad=dma.idunidad 
        left join (select * from movimientohoy) m on m.idmovimiento=dma.idmovimiento and m.idsucursal=dma.idsucursal where dma.idmovimiento=".$idmovimiento." AND dma.IdSucursal=".$this->gIdSucursal;
        //echo $sql;
		return $this->obtenerDataSQL($sql);
 	}
    
    function buscarDetalleProductoxMesa($idmesa)
 	{
		$sql = "select DISTINCT dma.iddetallemovalmacen,dma.idproducto, codigo, p.descripcion as producto, dma.idunidad, u.descripcion as unidad, cantidad, preciocompra, precioventa, moneda, p.idsucursal,dma.idmovimiento,p.abreviatura,
        dma.comentario,p.idimpresora
        from detallemovalmacen dma 
        left join producto p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal 
        left join unidad u on u.idunidad=dma.idunidad 
        left join (select * from movimientohoy) AS m on m.idmovimiento=dma.idmovimiento and m.idsucursal=dma.idsucursal and m.estado<>'A' and m.situacion<>'A'
        left join (select * from detallemovimientohoy) AS dm on dm.idmovimientoref=m.idmovimiento and dm.idmovimientoref=dma.idmovimiento  and dma.idsucursal=dm.idsucursal 
        left join mesa me on me.idmesa=m.idmesa 
        where m.idmesa=".$idmesa." and me.situacion IN ('O','N','C') and dm.idmovimientoref is NULL and dma.estado='N' AND dma.IdSucursal=".$this->gIdSucursal;
        //echo $sql;
		return $this->obtenerDataSQL($sql);
 	}
	/*
    function buscarDetalleProductoxMesa($idmesa)
 	{
		$sql = "select dma.iddetallemovalmacen,dma.idproducto, codigo, p.descripcion as producto, dma.idunidad, u.descripcion as unidad, cantidad, preciocompra, precioventa, moneda, p.idsucursal,dma.idmovimiento,p.abreviatura,dma.comentario
        from detallemovalmacen dma 
        inner join producto p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal 
        inner join unidad u on u.idunidad=dma.idunidad 
        inner join (select * from movimientohoy) AS m on m.idmovimiento=dma.idmovimiento and m.idsucursal=dma.idsucursal and m.estado<>'A' and m.situacion<>'A'
        left join (select * from detallemovimientohoy) AS dm on dm.idmovimientoref=m.idmovimiento and dm.idmovimientoref=dma.idmovimiento  and dma.idsucursal=dm.idsucursal 
        inner join mesa me on me.idmesa=m.idmesa 
        where m.idmesa=".$idmesa." and me.situacion IN ('O','N') and dm.idmovimientoref is NULL and dma.estado='N' AND dma.IdSucursal=".$this->gIdSucursal;
        //echo $sql;
		return $this->obtenerDataSQL($sql);
 	}
	*/
    function buscarMovimientoMesa($idmesa)
 	{
		$sql = "select m.idmovimiento,m.total
        from (select * from movimiento UNION select * from movimientohoy) m 
        inner join mesa me on me.idmesa=m.idmesa 
        left join (select * from detallemovimiento union select * from detallemovimientohoy) dm on dm.idmovimientoref=m.idmovimiento
        where m.idmesa=".$idmesa." and me.situacion='O' and dm.idmovimientoref is NULL AND m.IdSucursal=".$this->gIdSucursal;
        //echo $sql;
		return $this->obtenerDataSQL($sql);
 	}
	//Para el cambio de Mesa
    function actualizarMontoxCambioMesa($total,$idmovimiento){
        $sql = "update (select * from movimiento UNION select * from movimientohoy) m set m.total=$total,m.subtotal=$total where m.idmovimiento=$idmovimiento and m.idsucursal=".$this->gIdSucursal;
        $res = $this->ejecutarSQL($sql);
		if($res==0){
			return 0;
		}else{
			return $this->gError[2];
		}
    }
	function buscarUsuarioxMesa($idmesa){
 		$sql = "select m.idsucursalusuario,m.idusuario,m.idsucursal,m.idmesa
        from (select * from movimiento UNION select * from movimientohoy) m         
        left join (select * from detallemovimientohoy) dm on dm.idmovimientoref=m.idmovimiento
        inner join mesa me on me.idmesa=m.idmesa and m.idsucursal=me.idsucursal
        where me.situacion='O'  and dm.idmovimientoref is null and m.idmesa=".$idmesa." and m.IdSucursal=".$this->gIdSucursal;
        //echo $sql;
		return $this->obtenerDataSQL($sql);
 	
	}
        function buscarUsuarioxMesa2($idmesa){
                    $sql = "select me.situacion,m.idsucursalusuario,m.idusuario,m.idsucursal,m.idmesa
            from (select * from movimiento UNION select * from movimientohoy) m         
            left join (select * from detallemovimientohoy) dm on dm.idmovimientoref=m.idmovimiento
            inner join mesa me on me.idmesa=m.idmesa and m.idsucursal=me.idsucursal
            where me.situacion IN ('O','C') and m.situacion='O' and m.estado='N' and dm.idmovimientoref is null and m.idmesa=".$idmesa." and m.IdSucursal=".$this->gIdSucursal."  order by m.fecha DESC LIMIT 1";
            //echo $sql;
                    return $this->obtenerDataSQL($sql);

        }

	function consultarNombreClientePedido($idmovimiento)
 	{
		$sql = "select m.nombrespersona from (select * from movimiento UNION select * from movimientohoy) as m where m.idmovimiento=".$idmovimiento." AND m.IdSucursal=".$this->gIdSucursal;
		$rst=$this->obtenerDataSQL($sql);
		$dato=$rst->fetchObject();
		return $dato->nombrespersona;
 	}
	
	function actualizarNombresClienteMovimiento($id, $nombrespersona)
 	{		
   		$sql = "update movimientohoy set nombrespersona='".$nombrespersona."' where idmovimiento=".$id." and idsucursal=".$this->gIdSucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return 0;
		}else{
			return $this->gError[2];
		}
 	}
	
	function actualizarComentarioMovimiento($id, $comentario)
 	{		
   		$sql = "update movimientohoy set comentario=('".$comentario.". ' || comentario) where idmovimiento=".$id." and idsucursal=".$this->gIdSucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return 0;
		}else{
			return $this->gError[2];
		}
 	}
	
	function generaNumero($idtipomovimiento,$idtipodocumento,$year,$serie=''){
	   $sql="select m.numero from (select * from movimiento UNION select * from movimientohoy) m where m.idtipomovimiento=".$idtipomovimiento." and m.idtipodocumento=".$idtipodocumento." and m.idsucursal=".$this->gIdSucursal." and m.numero<>'0'";
       if($serie!=""){
           $sql.=" and m.numero like '".$serie."-%'";
       }
       $sql.=" ORDER BY m.idmovimiento DESC LIMIT 1";//echo $sql;
       $registro=$this->obtenerDataSQL($sql);
       if($registro->rowCount()>0){
			$dato=$registro->fetchObject();
			$num= $dato->numero;
			$year2=substr($num,11,4);
			if($year!=$year2){$num='001-000000';}
			$serie=substr($num,0,3)+0;
			if(substr($num,4,6)=='999999'){$serie=$serie+1;$num=0;}
			$serie=str_pad($serie,3,"0",STR_PAD_LEFT);
			$num=substr($num,4,6)+1;
			$num=str_pad($num,6,"0",STR_PAD_LEFT);
			$num=$serie.'-'.$num.'-'.$year;
		}else {
    		$sql="select m.numero from (select * from movimiento UNION select * from movimientohoy) m where m.idtipomovimiento=".$idtipomovimiento." and m.idtipodocumento=".$idtipodocumento." and m.idsucursal=".$this->gIdSucursal." and m.numero<>'0' ";
             if($serie!=""){
                $sql.=" and m.numero like '".$serie."-%'";
            }
            $sql.=" ORDER BY m.idmovimiento DESC LIMIT 1";//echo $sql;
    		$registro=$this->obtenerDataSQL($sql);
    		if($registro->rowCount()>0){
    			$dato=$registro->fetchObject();
    			$num= $dato->numero;
    			$year2=substr($num,11,4);
    			if($year!=$year2){$num='$serie-000000';}
    			$serie=substr($num,0,3)+0;
    			if(substr($num,4,6)=='999999'){$serie=$serie+1;$num=0;}
    			$serie=str_pad($serie,3,"0",STR_PAD_LEFT);
    			$num=substr($num,4,6)+1;
    			$num=str_pad($num,6,"0",STR_PAD_LEFT);
    			$num=$serie.'-'.$num.'-'.$year;
    		}else{
                if($serie=="") $serie="001";
    			$num="$serie-000001-".$year;
    		}
        }
		return $num;
	}
	
	function generaNumeroElectronico($idtipomovimiento,$idtipodocumento,$year,$serie=''){
	   $sql="select m.numero from (select * from movimiento UNION select * from movimientohoy) m where m.manual='N' and m.idtipomovimiento=".$idtipomovimiento." and m.idtipodocumento=".$idtipodocumento." and m.idsucursal=".$this->gIdSucursal." and m.numero<>'0'";
       if($serie!=""){
           $sql.=" and m.numero like '".$serie."-%'";
       }
       $sql.=" ORDER BY m.numero DESC LIMIT 1";//echo $sql;
       $registro=$this->obtenerDataSQL($sql);
       if($registro->rowCount()>0){
			$dato=$registro->fetchObject();
			$num= $dato->numero;
			$serie2=substr($num,1,3)+0;
			//if(substr($num,5,8)=='999999'){$serie2=$serie2+1;$num=0;}
			$serie=substr($serie,0,1).str_pad($serie2,3,"0",STR_PAD_LEFT);
			$num=substr($num,5,8)+1;
			$num=str_pad($num,8,"0",STR_PAD_LEFT);
			$num=$serie.'-'.$num.'-'.$year;
		}else {
    		$sql="select m.numero from (select * from movimiento UNION select * from movimientohoy) m where m.manual='N' and m.idtipomovimiento=".$idtipomovimiento." and m.idtipodocumento=".$idtipodocumento." and m.idsucursal=".$this->gIdSucursal." and m.numero<>'0' ";
             if($serie!=""){
                $sql.=" and m.numero like '".$serie."-%'";
            }
            $sql.=" ORDER BY m.idmovimiento DESC LIMIT 1";//echo $sql;
    		$registro=$this->obtenerDataSQL($sql);
    		if($registro->rowCount()>0){
    			$dato=$registro->fetchObject();
    			$num= $dato->numero;
    			$serie2=substr($num,1,3)+0;
    			//if(substr($num,5,8)=='999999'){$serie2=$serie2+1;$num=0;}
    			$serie=substr($serie,0,1).str_pad($serie2,3,"0",STR_PAD_LEFT);
    			$num=substr($num,5,8)+1;
    			$num=str_pad($num,8,"0",STR_PAD_LEFT);
    			$num=$serie.'-'.$num.'-'.$year;
    		}else{
                if($serie=="") $serie="001";
                if($idtipodocumento==4){//BOLETA
    				$num="$serie-00000001-".$year;
                }elseif($idtipodocumento==5){
    				$num="$serie-00000001-".$year;
                }else{
                	$num="$serie-00000001-".$year;
                }
    		}
        }
		return $num;
	}

	function generaNumeroSinSerie($idtipomovimiento,$idtipodocumento,$mes,$idsucursal=''){
	   if($idsucursal=='') $idsucursal=$this->gIdSucursal;
		$sql="select m.numero, m.fecha from (select * from movimiento UNION select * from movimientohoy) m where m.idtipomovimiento=".$idtipomovimiento." and m.idtipodocumento=".$idtipodocumento." and m.idsucursal=".$idsucursal." and m.numero<>'0' ORDER BY m.idmovimiento DESC LIMIT 1";
        $registro=$this->obtenerDataSQL($sql);
		if($registro->rowCount()>0){
			$dato=$registro->fetchObject();
			$num= $dato->numero;
			$fecha= $dato->fecha;
			$mes2=substr($fecha,5,2);
			if($mes!=$mes2){$num='000000';}
			if($num=='999999'){$num=0;}
			$num=$num+1;
			$num=str_pad($num,6,"0",STR_PAD_LEFT);
		}else {
            $sql="select m.numero, m.fecha from (select * from movimiento UNION select * from movimientohoy) m where m.idtipomovimiento=".$idtipomovimiento." and m.idtipodocumento=".$idtipodocumento." and m.idsucursal=".$idsucursal." and m.numero<>'0' ORDER BY m.idmovimiento DESC LIMIT 1";
    		$registro=$this->obtenerDataSQL($sql);
    		if($registro->rowCount()>0){
    			$dato=$registro->fetchObject();
    			$num= $dato->numero;
    			$fecha= $dato->fecha;
    			$mes2=substr($fecha,5,2);
    			if($mes!=$mes2){$num='000000';}
    			if($num=='999999'){$num=0;}
    			$num=$num+1;
    			$num=str_pad($num,6,"0",STR_PAD_LEFT);
    		}else{
    			$num="000001";
    		}
        }
		return $num;
	}
	
	function generaNumeroxMesero($idmesero,$idsucursalmesero){
	   //QUITE EL and m.IdResponsable=".$idmesero."  and m.IdSucursalResponsable=".$idsucursalmesero."  A PEDIDO DEL CLIENTE
       //cambie la consulta de la numeracio
		//$sql="select m.numero, m.fecha from (select * from movimiento UNION select * from movimientohoy) m where m.idtipomovimiento=5 and m.idtipodocumento=11 and m.idsucursal=".$this->gIdSucursal." and m.numero<>'0' ORDER BY m.idmovimiento DESC LIMIT 1";
        $sql="select max(cast(m.numero as integer)) as numero from (select * from movimiento UNION select * from movimientohoy) m where m.idtipomovimiento=5 and m.idtipodocumento=11 and m.idsucursal=".$this->gIdSucursal." and m.numero<>'0' group by m.idmovimiento ORDER BY m.idmovimiento DESC LIMIT 1";
        //echo ($sql);
        $registro=$this->obtenerDataSQL($sql);
		if($registro->rowCount()>0){
			$dato=$registro->fetchObject();
			$num= $dato->numero;
			//$fecha= $dato->fecha;
			/*$mes2=substr($fecha,5,2);
			if($mes!=$mes2){$num='000000';}*/
			if($num=='999999'){$num=0;}
			$num=$num+1;
			$num=str_pad($num,6,"0",STR_PAD_LEFT);
		}else {
            //$sql="select numero, fecha from (select * from movimiento UNION select * from movimientohoy) as movimiento where movimiento.idtipomovimiento=5 and movimiento.idtipodocumento=11 and idsucursal=".$this->gIdSucursal." and numero<>'0' ORDER BY movimiento.idmovimiento DESC LIMIT 1";
    		$sql="select max(cast(m.numero as integer)) as numero from (select * from movimiento UNION select * from movimientohoy) m where m.idtipomovimiento=5 and m.idtipodocumento=11 and m.idsucursal=".$this->gIdSucursal." and m.numero<>'0' group by m.idmovimiento ORDER BY m.idmovimiento DESC LIMIT 1";
            $registro=$this->obtenerDataSQL($sql);
    		if($registro->rowCount()>0){
    			$dato=$registro->fetchObject();
    			$num= $dato->numero;
    			//$fecha= $dato->fecha;
    			/*$mes2=substr($fecha,5,2);
    			if($mes!=$mes2){$num='000000';}*/
    			if($num=='999999'){$num=0;}
    			$num=$num+1;
    			$num=str_pad($num,6,"0",STR_PAD_LEFT);
    		}else{
    			$num="000001";
    		}
        }
		return $num;
	}

	function cambiarSituacion($id, $situacion)
 	{
   		$sql = "UPDATE (select * from movimiento UNION select * from movimientohoy) m SET m.Situacion='".$situacion."' where m.idmovimiento=".$id." and m.idsucursal=".$this->gIdSucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function cambiarSituacionAntendido($id, $situacion)
 	{
		//la fecha de proxima cancelacion actua como fecha de atencion
   		$sql = "UPDATE (select * from movimiento UNION select * from movimientohoy) m SET m.Situacion='".$situacion."', m.fechaproximacancelacion=LOCALTIMESTAMP where m.idmovimiento=".$id." and m.idsucursal=".$this->gIdSucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function cambiarSituacionPedido($id, $situacion)
 	{
   		$sql = "execute up_cambiarsituacionpedido $id,".$this->gIdSucursal.",'$situacion'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function cambiarSituacionMesa($id, $situacion)
 	{
   		$sql = "UPDATE mesa SET Situacion='".$situacion."' WHERE idmesa = (select idmesa FROM (select * from movimientohoy union select * from movimiento) m WHERE m.idmovimiento=".$id." and m.idsucursal=".$this->gIdSucursal.") and idsucursal=".$this->gIdSucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function cambiarSituacionMesa2($id, $situacion)
 	{
   		$sql = "UPDATE mesa SET Situacion='".$situacion."' WHERE idmesa = ".$id." and idsucursal=".$this->gIdSucursal;
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function consultarMovimientoxMesa($idmesa,$tipomovimiento)
 	{
		$sql = "SELECT m.idmovimiento, m.idconceptopago, m.idsucursal, m.idtipomovimiento, m.numero, 
       M.idtipodocumento, td.abreviatura as tipodocumento, m.formapago, to_char(m.fecha,'DD/MM/YYYY HH:mi:ss am') as fecha, m.fechaproximacancelacion, m.fechaultimopago, 
       m.nropersonas, m.idmesa, Me.numero as mesa, m.moneda, m.inicial, m.subtotal, m.igv, m.total, m.totalpagado, 
       m.idusuario, m.tipopersona, m.idpersona, m.idresponsable, m.idmovimientoref, 
       m.idsucursalref, m.comentario, CASE WHEN m.situacion='O' THEN 'Pedido' WHEN m.situacion='A' THEN 'Atendido' WHEN m.situacion='P' THEN 'Consumido' END as situacion, m.situacion as situacion2, m.estado,(PM.apellidos || ' ' || PM.nombres) as Cliente,PM2.nombres as Responsable,idsalon, mref.numero as numeroref, M.nombrespersona, M.idsucursalusuario, M.idsucursalpersona, M.idsucursalresponsable
  FROM (select * from movimiento UNION select * from movimientohoy) M inner join tipodocumento td on td.idtipodocumento=m.idtipodocumento LEFT JOIN Persona P ON P.idpersona=m.idpersona and m.idsucursalpersona=P.idsucursal LEFT JOIN PersonaMaestro PM ON P.idpersonamaestro=PM.idpersonamaestro LEFT JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal LEFT JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro LEFT JOIN mesa Me ON Me.idmesa=M.idmesa LEFT JOIN movimientohoy mref on mref.idmovimiento=M.idmovimientoref and mref.idsucursal=M.idsucursalref WHERE m.situacion<>'P' ";
		$sql = $sql . " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal;
		if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
		if($idmesa>0){ $sql = $sql . " AND m.idmesa = " . $idmesa;}
           $sql.=" order by m.idmovimiento desc";
		//echo $sql;
		return $this->obtenerDataSQL($sql);
 	}
	

	function reportePedidosxMeseros($fechainicio='',$fechafin='')
 	{
		$sql = "
SELECT m.idresponsable as idmesero, PM2.nombres as mesero, count(m.idresponsable) AS cantidad
  FROM movimiento M INNER JOIN Persona R ON R.idpersona=m.idresponsable and m.idsucursalresponsable=R.idsucursal INNER JOIN PersonaMaestro PM2 ON R.idpersonamaestro=PM2.idpersonamaestro
  WHERE 1=1 ";
		if($fechainicio<>''){$sql = $sql . " AND fecha >= '" . $fechainicio . " 00:00:00.000'";}
		if($fechafin<>''){$sql = $sql . " AND fecha <= '" . $fechafin . " 23:59:59.999'";}
		$sql .= " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal." AND m.IdTipoMovimiento = 5 group by m.idresponsable, PM2.nombres Order by 1 ASC";

		return $this->obtenerDataSQL($sql);
 	}
	
	function consultarMovimientoReportexMesoxSemana($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero='', $situacion='', $fechainicio='',$fechafin='',$idusuario=0,$MesoSemana='S')
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
			if($MesoSemana=='M'){
				$sql = "SELECT to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total FROM (select * from movimiento union all select * from movimientohoy) M WHERE 1=1 ";
			}else{
				$sql = "SELECT to_char(M.fecha,'W') as Semana,to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total, to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha) as SemanaMes FROM (select * from movimiento union all select * from movimientohoy) M WHERE 1=1 ";
			}
			$sql = $sql . " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal." ";
			if($fechainicio<>''){$sql = $sql . " AND M.fecha >= '" . $fechainicio . " 00:00:00.000'";}
			if($fechafin<>''){$sql = $sql . " AND M.fecha <= '" . $fechafin . " 23:59:59.999'";}
			if($id>0){ $sql = $sql . " AND m.IdMovimiento = " . $id;}
			if($tipomovimiento>0){ $sql = $sql . " AND m.IdTipoMovimiento = " . $tipomovimiento;}
			if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND m.situacion = '" . $situacion . "'";}
			if($idusuario>0){ $sql = $sql . " AND m.idusuario = " . $idusuario;}
			if($MesoSemana=='M'){
				$sql.=" GROUP BY to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY')";
				$order1='4,2';
			}else{
				$sql.=" GROUP BY to_char(M.fecha,'W'),to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY'), to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha)";
				$order1='5,3,2';
			}
			//return $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order1)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
			//echo "SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order1)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg);
		} 	 	
 	}
	
	function consultarMovimientoReporteUtilidadxMesoxSemana($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero='', $situacion='', $fechainicio='',$fechafin='',$idusuario=0,$MesoSemana='S')
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
			
			if($MesoSemana=='M'){
				$sql="SELECT T1.numMes, T1.Mes, T1.ano, coalesce(T1.Total,0)-coalesce(T2.Total,0) as Total FROM (";
				$sql.="SELECT to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total FROM movimiento M WHERE 1=1 ";
			}else{
				$sql="SELECT T1.Semana, T1.numMes, T1.Mes, T1.ano, coalesce(T1.Total,0)-coalesce(T2.Total,0) as Total, T1.SemanaMes FROM (";
				$sql.="SELECT to_char(M.fecha,'W') as Semana,to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total, to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha) as SemanaMes FROM movimiento M WHERE 1=1 ";
			}
			$sql = $sql . " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal." ";
			$sql = $sql . " AND m.IdTipoMovimiento = 2";
			if($fechainicio<>''){$sql = $sql . " AND M.fecha >= '" . $fechainicio . " 00:00:00.000'";}
			if($fechafin<>''){$sql = $sql . " AND M.fecha <= '" . $fechafin . " 23:59:59.999'";}
			if($id>0){ $sql = $sql . " AND m.IdMovimiento = " . $id;}
			if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND m.situacion = '" . $situacion . "'";}
			if($idusuario>0){ $sql = $sql . " AND m.idusuario = " . $idusuario;}
			if($MesoSemana=='M'){
				$sql.=" GROUP BY to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY')";
				$order1='4,2';
			}else{
				$sql.=" GROUP BY to_char(M.fecha,'W'),to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY'), to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha)";
				$order1='5,3,2';
			}
			
			$sql.=" ) T1 LEFT JOIN (";
			
			if($MesoSemana=='M'){
				$sql.="SELECT to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total FROM movimiento M WHERE 1=1 ";
			}else{
				$sql.="SELECT to_char(M.fecha,'W') as Semana,to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total, to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha) as SemanaMes FROM movimiento M WHERE 1=1 ";
			}
			$sql = $sql . " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal." ";
			$sql = $sql . " AND m.IdTipoMovimiento = 1";
			if($fechainicio<>''){$sql = $sql . " AND M.fecha >= '" . $fechainicio . " 00:00:00.000'";}
			if($fechafin<>''){$sql = $sql . " AND M.fecha <= '" . $fechafin . " 23:59:59.999'";}
			if($id>0){ $sql = $sql . " AND m.IdMovimiento = " . $id;}
			if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND m.situacion = '" . $situacion . "'";}
			if($idusuario>0){ $sql = $sql . " AND m.idusuario = " . $idusuario;}
			if($MesoSemana=='M'){
				$sql.=" GROUP BY to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY')";
				$order1='4,2';
				$sql.=" ) T2 ON T1.numMes=T2.numMes and T1.ano=T2.ano";
			}else{
				$sql.=" GROUP BY to_char(M.fecha,'W'),to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY'), to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha)";
				$order1='5,3,2';
				$sql.=" ) T2 ON T1.Semana=T2.Semana and T1.numMes=T2.numMes and T1.ano=T2.ano";
			}
			
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order1)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
			//echo "SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order1)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg);
		} 	 	
 	}
		
	function consultarMovimientoReporteUtilidadNetaxMesoxSemana($nro_reg, $nro_hoja, $order, $by, $id, $tipomovimiento, $numero='', $situacion='', $fechainicio='',$fechafin='',$idusuario=0,$MesoSemana='S')
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
			
			if($MesoSemana=='M'){
				$sql="SELECT T1.numMes, T1.Mes, T1.ano, coalesce(T1.Total,0)-coalesce(T2.Total,0) as Total FROM (";
				$sql.="SELECT to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total FROM movimiento M WHERE 1=1 ";
			}else{
				$sql="SELECT T1.Semana, T1.numMes, T1.Mes, T1.ano, coalesce(T1.Total,0)-coalesce(T2.Total,0) as Total, T1.SemanaMes FROM (";
				$sql.="SELECT to_char(M.fecha,'W') as Semana,to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total, to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha) as SemanaMes FROM movimiento M WHERE 1=1 ";
			}
			$sql = $sql . " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal." ";
			$sql = $sql . " AND m.IdTipoMovimiento = 4 AND m.idtipodocumento=9";
			$sql.=" AND ((m.idconceptopago<>2 AND m.idcaja>0) OR (m.idconceptopago<>2 AND m.idcaja is null))";
			if($fechainicio<>''){$sql = $sql . " AND M.fecha >= '" . $fechainicio . " 00:00:00.000'";}
			if($fechafin<>''){$sql = $sql . " AND M.fecha <= '" . $fechafin . " 23:59:59.999'";}
			if($id>0){ $sql = $sql . " AND m.IdMovimiento = " . $id;}
			if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND m.situacion = '" . $situacion . "'";}
			if($idusuario>0){ $sql = $sql . " AND m.idusuario = " . $idusuario;}
			if($MesoSemana=='M'){
				$sql.=" GROUP BY to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY')";
				$order1='4,2';
			}else{
				$sql.=" GROUP BY to_char(M.fecha,'W'),to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY'), to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha)";
				$order1='5,3,2';
			}
			
			$sql.=" ) T1 LEFT JOIN (";
			
			if($MesoSemana=='M'){
				$sql.="SELECT to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total FROM movimiento M WHERE 1=1 ";
			}else{
				$sql.="SELECT to_char(M.fecha,'W') as Semana,to_char(M.fecha,'MM') as numMes,obtenerMes(M.fecha) as Mes,to_char(M.fecha,'YYYY') as ano,SUM(M.total) as Total, to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha) as SemanaMes FROM movimiento M WHERE 1=1 ";
			}
			$sql = $sql . " AND m.Estado = 'N' AND m.IdSucursal=".$this->gIdSucursal." ";
			$sql = $sql . " AND m.IdTipoMovimiento = 4 AND m.idtipodocumento=10";
			$sql.=" AND ((m.idconceptopago<>2 AND m.idcaja>0) OR (m.idconceptopago<>2 AND m.idcaja is null))";
			if($fechainicio<>''){$sql = $sql . " AND M.fecha >= '" . $fechainicio . " 00:00:00.000'";}
			if($fechafin<>''){$sql = $sql . " AND M.fecha <= '" . $fechafin . " 23:59:59.999'";}
			if($id>0){ $sql = $sql . " AND m.IdMovimiento = " . $id;}
			if($numero <>"" ){$sql = $sql . " AND m.numero LIKE '" . $numero . "'";}
			if($situacion<>''){$sql = $sql . " AND m.situacion = '" . $situacion . "'";}
			if($idusuario>0){ $sql = $sql . " AND m.idusuario = " . $idusuario;}
			if($MesoSemana=='M'){
				$sql.=" GROUP BY to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY')";
				$order1='4,2';
				$sql.=" ) T2 ON T1.numMes=T2.numMes and T1.ano=T2.ano";
			}else{
				$sql.=" GROUP BY to_char(M.fecha,'W'),to_char(M.fecha,'MM'),obtenerMes(M.fecha),to_char(M.fecha,'YYYY'), to_char(M.fecha,'W') || '-' || obtenerMes(M.fecha)";
				$order1='5,3,2';
				$sql.=" ) T2 ON T1.Semana=T2.Semana and T1.numMes=T2.numMes and T1.ano=T2.ano";
			}
			
			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order1)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
			//echo "SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order1)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg);
		} 	 	
 	}
	
	function consultarPedidosPendientes(){
		$sql = "select * from movimientohoy where idtipomovimiento=5 and estado='N' and situacion='O' and  idsucursal=".$this->gIdSucursal;
		return	$this->obtenerDataSQL($sql);
	}

    function consultarNumeroComanda($idsucursal){
        $sql = "select numerocomanda from sucursal where idsucursal=$idsucursal";
        return	$this->obtenerDataSQL($sql);
    }
    
    function actualizarNumeroComanda($numero,$idsucursal){
        $sql = "update sucursal set numerocomanda=$numero where idsucursal=$idsucursal";
        return $this->ejecutarSQL($sql);
    }
}
?>