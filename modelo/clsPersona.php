<?php
session_start();
if(!$_SESSION['R_ini_ses']){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}
require_once 'clsPersonaMaestro.php';
class clsPersona extends clsPersonaMaestro
{

	// Constructor de la clase
	function __construct($tabla, $cliente, $user, $pass){
		$this->gIdTabla = $tabla;
		$this->gIdSucursal = $cliente;		
		parent::__construct($tabla, $cliente, $user, $pass);
	}
	
	function insertarPersona($IdSucursal, $IdPersonaMaestro, $iddistrito, $direccion, $email, $telefonofijo, $telefonomovil, $imagen, $idrol, $compartido)
 	{ 	
		$sql = "execute up_AgregarPersona $IdSucursal, $IdPersonaMaestro, $iddistrito, '$direccion', '$email', '$telefonofijo', '$telefonomovil', '$imagen', $idrol, '$compartido'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	
	function insertarPersonaOut($IdSucursal, $IdPersonaMaestro, $iddistrito, $direccion, $email, $telefonofijo, $telefonomovil, $imagen, $idrol, $compartido)
 	{ 	
			$sql = "select up_AgregarPersonaOut($IdSucursal, $IdPersonaMaestro, $iddistrito, '$direccion', '$email', '$telefonofijo', '$telefonomovil', '$imagen', $idrol, '$compartido') as idpersona";
			return $this->obtenerDataSQL($sql);
 	}

	function actualizarPersona($IdSucursal, $IdPersona, $IdPersonaMaestro, $iddistrito, $direccion, $email, $telefonofijo, $telefonomovil, $imagen, $compartido)
 	{
   		$sql = "execute up_ModificarPersona $IdSucursal, $IdPersona, $IdPersonaMaestro, $iddistrito, '$direccion', '$email', '$telefonofijo', '$telefonomovil', '$imagen', '$compartido'";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}

function actualizarPersonadesdePerfil($IdPersona, $IdCliente, $IdPersonaMaestro, $Direccion, $Telefono, $Celular, $Email, $img_foto)
 	{
   		$sql = "Update Persona set direccion='$Direccion', telefonofijo='$Telefono', telefonomovil='$Celular', email='$Email' where idsucursal=$IdCliente and idpersonamaestro=$IdPersonaMaestro and idpersona=$IdPersona";
		$res = $this->ejecutarSQL($sql);
		if($res==0){
			if($img_foto!=""){
				$sql="update persona set            
				imagen='$img_foto'
				where idpersona=$IdPersona and idsucursal = $IdCliente and idpersonamaestro=$IdPersonaMaestro";
				$res = $this->ejecutarSQL($sql);
				
				if($res==0){
					//$data = base64_encode($img_foto);
					//$data = base64_decode($data);
					/*echo "$sql<script> alert('Guaradado corrtectamente');</script>";*/
					/*$im = imagecreatefromstring($img_foto);
					if ($im !== false) {
						header("Content-type: image/png"); 
						imagepng($im); 
						imagedestroy($im);
					}
					else {
						return 'An error occurred.';
					}*/
					//return $sql."Guardado correctamente";
				}else{
					return $sql.$this->gError[2];
					
				}
			}else{
				return "Guardado correctamente";
			}
		}else{
			return $sql.$this->gError[2];
		}
	}
	
	function eliminarPersona($IdSucursal, $IdPersona, $IdPersonaMaestro)
 	{
   		$sql = "execute up_EliminarPersona $IdSucursal, $IdPersona, $IdPersonaMaestro ";
		$res = $this->ejecutarSP($sql);
		if($res==0){
			return "Guardado correctamente";
		}else{
			return $this->gError[2];
		}
 	}
	//GMC: 27/04/11: Modifique 
	function consultarPersona($nro_reg, $nro_hoja, $order, $by, $idsucursal, $IdPersona,$IdPersonaMaestro, $buscar_apellido_nombre='', $buscar_nrodoc='', $buscar_sexo='', $buscar_compartido='', $idrol='', $idtipopersona='', $fechainicio='', $fechafin='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarPersona $nro_reg, $nro_hoja, $order, $by, $idsucursal, $IdPersona, $IdPersonaMaestro, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$buscar_apellido_nombre = "%".$buscar_apellido_nombre."%";
			$buscar_nrodoc = "%".$buscar_nrodoc."%";
			$buscar_sexo = "%".$buscar_sexo."%";
			$buscar_compartido = "%".$buscar_compartido."%";
			
			$sql = "SELECT persona.idpersona, Persona.idsucursal, persona.idpersonamaestro, iddistrito, persona.direccion, 
       persona.email, persona.telefonofijo, persona.telefonomovil, imagen, PM.apellidos, PM.nombres, PM.tipopersona, PM.nrodoc, PM.sexo, pm.apellidos  || ' ' || pm.nombres as personamaestro,persona.Iddistrito,
Dist.codigo as ubigeo, Dist.descripcion as distrito, Prov.IdUbigeo as IdProvincia, Prov.descripcion as provincia, Dpto.IdUbigeo as IdDepartamento, Dpto.descripcion as departamento,compartido, to_char(FechaNac,'DD/MM/YYYY') as fechanac FROM persona INNER JOIN PersonaMaestro PM ON Persona.IdPersonaMaestro=PM.IdPersonaMaestro
		left join Ubigeo Dist On Dist.IdUbigeo=persona.IdDistrito 
		left join Ubigeo Prov On Prov.IdUbigeo=Dist.IdUbigeo_Ref
		left join Ubigeo Dpto On Dpto.IdUbigeo=Prov.IdUbigeo_Ref 
		INNER JOIN SUCURSAL s on S.idsucursal=persona.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." ";
		if($idrol<>'') $sql.=" INNER JOIN rolpersona rp on rp.idpersona=persona.idpersona and rp.idsucursal=persona.idsucursal and idrol in (".$idrol.")";
			$sql.=" WHERE persona.Estado='N' ";
			if($idsucursal>0){ $sql = $sql . " AND (Persona.idsucursal=".$idsucursal." or (Persona.idsucursal<>".$idsucursal." and compartido='S'))";}
			if($IdPersona>0){ $sql = $sql . " AND Persona.IdPersona = " . $IdPersona;}
			if($IdPersonaMaestro>0){ $sql = $sql . " AND Persona.IdPersonaMaestro = " . $IdPersonaMaestro;}
			if($buscar_apellido_nombre <>"" ){$sql = $sql . " AND pm.apellidos || ' ' || pm.nombres LIKE '" . $buscar_apellido_nombre . "'";}
			if($buscar_nrodoc <>"" ){$sql = $sql . " AND PM.nrodoc LIKE '" . $buscar_nrodoc."'" ;}
			if($buscar_sexo <>"" ){$sql = $sql . " AND PM.sexo LIKE '" . $buscar_sexo."'" ;}
			if($buscar_compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $buscar_compartido."'" ;}
			if($idtipopersona <>"" ){$sql = $sql . " AND tipopersona = '".strtoupper($idtipopersona)."'" ;}
			if($fechainicio<>''){$sql = $sql . " AND (FechaNac >= '" . $fechainicio . " 00:00:00.000' OR FechaNac is null)";}
			if($fechafin<>''){$sql = $sql . " AND (FechaNac <= '" . $fechafin . " 23:59:59.999'  OR FechaNac is null)";}

			
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	
 	}
	
	function consultar($IdPersonaMaestro, $id_cliente)
 	{
   		$sql = "Select * From Persona Where Estado='N' ";
		if(isset($IdPersonaMaestro))
		   		$sql .= " and IdPersonaMaestro =".$IdPersonaMaestro;
		if(isset($id_cliente))
		   		$sql .= " and IdSucursal =".$id_cliente;
		
		return $this->obtenerDataSQL($sql);
 	
 	}
	//GMC: 20/03/11: Modifique 
	function consultarxTipo($IdTipoPersona, $Nombres)
 	{
		if(parent::getTipoBD()==1){
			$sql = "Select IdPersona, (ApellidoPaterno +' '+ ApellidoMaterno + ' ' + Nombre) as Nombres, TD.Registro as TipoDoc, DNI From Persona F inner join PersonaMaestro P on P.IdPersonaMaestro=F.IdPersonaMaestro inner join dbo.obtenerTabla(9,3,".$this->gIdSucursal.") TD on Td.IdRegistro=IdDocumentoIdentidad Where 1=1  AND IdSucursal= ".$this->gIdSucursal;
			if(isset($IdTipoPersona))
					$sql .= " and IdTipoPersona =".$IdTipoPersona;
			if(isset($Nombres)){
					$Nombres='%'.$Nombres.'%';
					$sql .= " and (ApellidoPaterno +' '+ ApellidoMaterno + ' ' + Nombre like '".$Nombres."' or DNI like '".$Nombres."')";}
		}elseif(parent::getTipoBD()==3){
			$sql = "Select IdPersona, (ApellidoPaterno ||' '|| ApellidoMaterno || ' ' || Nombre) as Nombres, TD.Registro as TipoDoc, DNI From Persona F inner join PersonaMaestro P on P.IdPersonaMaestro=F.IdPersonaMaestro inner join obtenerTabla(9,3,".$this->gIdSucursal.") TD on Td.IdRegistro=IdDocumentoIdentidad INNER JOIN SUCURSAL s on S.idsucursal=F.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." Where 1=1  AND (F.idsucursal=".$this->gIdSucursal." or (F.idsucursal<>".$this->gIdSucursal." and compartido='S'))";
			if(isset($IdTipoPersona))
					$sql .= " and IdTipoPersona =".$IdTipoPersona;
			if(isset($Nombres)){
					$Nombres='%'.$Nombres.'%';
					$sql .= " and ((ApellidoPaterno ||' '|| ApellidoMaterno || ' ' || Nombre) like '".$Nombres."' or DNI like '".$Nombres."')";}
		}
		
		return $this->obtenerDataSQL($sql);
 	
 	}
	//GMC: 20/03/11: Modifique
	function consultarxId($IdPersona,$idsucursal=NULL)
 	{
		if(!isset($idsucursal)) $idsucursal=$this->gIdSucursal;
		$sql = "Select P.Idsucursal,P.IdPersona, (Apellidos ||' '|| Nombres) as Nombres, CASE WHEN tipopersona='VARIOS' THEN 'DNI' ELSE 'RUC' END as tipodoc, nrodoc, direccion From Persona P inner join PersonaMaestro PM on PM.IdPersonaMaestro=P.IdPersonaMaestro inner join rolpersona rp on rp.idpersona=P.idpersona and rp.idsucursal=P.idsucursal Where 1=1 ";
		if(isset($IdPersona)) $sql .= " and P.IdPersona =".$IdPersona." and P.idsucursal=".$idsucursal;

		return $this->obtenerDataSQL($sql);
 	
 	}
	
	function consultarPersonaxRol($idrol, $nombres='', $tipopersona='')
 	{
		$sql = "Select Distinct P.idsucursal, P.IdPersona, (Apellidos ||' '|| Nombres) as Nombres, CASE WHEN tipopersona='VARIOS' THEN 'DNI' ELSE 'RUC' END as tipodoc, nrodoc,P.telefonofijo,P.telefonomovil,P.direccion 
        From Persona P 
        inner join PersonaMaestro PM on PM.IdPersonaMaestro=P.IdPersonaMaestro 
        inner join rolpersona rp on rp.idpersona=P.idpersona and rp.idsucursal=P.idsucursal 
        INNER JOIN SUCURSAL s on S.idsucursal=P.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." Where P.estado='N' ";
		if($idrol>0) $sql .= " and idrol in (".$idrol.") and (P.idsucursal=".$this->gIdSucursal." or (P.idsucursal<>".$this->gIdSucursal." and compartido='S'))";
		if($nombres<>'') $sql .= " and ((Apellidos ||' '|| Nombres) like '%".$nombres."%' or nrodoc like '%".$nombres."%' or P.telefonofijo like '%$nombres%' or P.telefonomovil like '%$nombres%')";
		if($tipopersona<>'') {
			if($tipopersona=='DNI')	$sql .= " and (tipopersona = 'VARIOS' or tipopersona = 'NATURAL')";
			if($tipopersona=='RUC')	$sql .= " and tipopersona <> 'VARIOS'";
		}
			//PRINT_R($sql);	
		return $this->obtenerDataSQL($sql);
		//echo $sql;
 	}
	
	//Modificado por: Sixto: fecha: 05-08-11: Descripcion: xxxxxx
	//Modificado por: Geynen: fecha: 20-10-11: Descripcion: xxxxxx

	function consultarPersonaReporte($nro_reg, $nro_hoja, $order, $by, $idsucursal, $IdPersona,$IdPersonaMaestro, $buscar_apellido_nombre='', $buscar_nrodoc='', $buscar_sexo='', $buscar_compartido='', $idrol='',$fechainicio='',$fechafin='',$tops='',$jornada=0, $idcaja=0)
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarPersona $nro_reg, $nro_hoja, $order, $by, $idsucursal, $IdPersona, $IdPersonaMaestro, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$buscar_apellido_nombre = "%".$buscar_apellido_nombre."%";
			$buscar_nrodoc = "%".$buscar_nrodoc."%";
			$buscar_sexo = "%".$buscar_sexo."%";
			$buscar_compartido = "%".$buscar_compartido."%";
			if($tops<>''){
            	$sql = "SELECT persona.idpersona, Persona.idsucursal, persona.idpersonamaestro, iddistrito, persona.direccion, persona.email, persona.telefonofijo, persona.telefonomovil, imagen, PM.apellidos, PM.nombres, PM.tipopersona, PM.nrodoc, PM.sexo, pm.apellidos  || ' ' || pm.nombres as personamaestro,persona.Iddistrito, Dist.codigo as ubigeo, Dist.descripcion as distrito, Prov.IdUbigeo as IdProvincia, Prov.descripcion as provincia, Dpto.IdUbigeo as IdDepartamento, Dpto.descripcion as departamento,compartido, SUM(mov.total) as monto,count(1) as veces,ROW_NUMBER() OVER(Order by SUM(mov.total) desc) as Puesto FROM persona";
              	if($tops<>'topsCliente' and $tops<>'topsProveedor'){
                	$sql.=" left JOIN Usuario Us ON Us.IdPersona=Persona.IdPersona and Us.IdSucursal=Persona.IdSucursal and Us.estado='N'";
              	}
	            $sql.=" INNER JOIN PersonaMaestro PM ON Persona.IdPersonaMaestro=PM.IdPersonaMaestro left join Ubigeo Dist On Dist.IdUbigeo=persona.IdDistrito left join Ubigeo Prov On Prov.IdUbigeo=Dist.IdUbigeo_Ref left join Ubigeo Dpto On Dpto.IdUbigeo=Prov.IdUbigeo_Ref	INNER JOIN SUCURSAL s on S.idsucursal=persona.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." ";
				if($idrol<>'') $sql.=" INNER JOIN rolpersona rp on rp.idpersona=persona.idpersona and rp.idsucursal=persona.idsucursal and idrol in (".$idrol.") ";
				
				if($tops=='topsMesero'){
					$sql.=" INNER JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) AS mov on mov.idresponsable=persona.idpersona and mov.idsucursalresponsable=persona.idsucursal and mov.estado='N' and mov.tipopersona='P'";
				}elseif($tops=='topsCajero'){
					$sql.=" INNER JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) AS mov on mov.idusuario=Us.idusuario and mov.idsucursalusuario=Us.idsucursal and mov.estado='N' and mov.tipopersona='P'";		
				}else{
					$sql.=" INNER JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) AS mov on mov.idpersona=persona.idpersona and mov.idsucursalpersona=persona.idsucursal and mov.estado='N' and mov.tipopersona='P'";
				}
				if($tops<>'topsCliente' and $tops<>'topsProveedor'){
					 if($tops=='topsCajero'){
						$sql.=" and mov.idtipomovimiento in(4)  and mov.idtipodocumento in (9) ";
					 }else{
						$sql.=" and mov.idtipomovimiento in(5) and mov.idtipodocumento in (11) ";
					 }
				}elseif($tops<>'topsCliente'){
					$sql.=" and mov.idtipomovimiento in(1)";
				}else{
					$sql.=" and mov.idtipomovimiento in(2)";
				}
				
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
					$sql = $sql . " AND mov.idmovimiento >= ".$inicio;
					if($fin>0 and $fin>$inicio) $sql = $sql . " AND mov.idmovimiento <=".$fin;
				}else{
					if(strlen($fechainicio)>10){
						if($fechainicio<>''){$sql = $sql . " AND (mov.fecha >= '" . $fechainicio . "' OR mov.fecha is null) ";}
					}else{
						if($fechainicio<>''){$sql = $sql . " AND (mov.fecha >= '" . $fechainicio . " 00:00:00.000' OR mov.fecha is null) ";}
					}
					if(strlen($fechafin)>10){
						if($fechafin<>''){$sql = $sql . " AND (mov.fecha <= '" . $fechafin . "'  OR mov.fecha is null) ";}
					}else{
						if($fechafin<>''){$sql = $sql . " AND (mov.fecha <= '" . $fechafin . " 23:59:59.999'  OR mov.fecha is null) ";}
					}
				}
		
				$sql.=" WHERE persona.Estado='N' ";
				if($idsucursal>0){ $sql = $sql . " AND (Persona.idsucursal=".$idsucursal." or (Persona.idsucursal<>".$idsucursal." and compartido='S'))";}
				if($IdPersona>0){ $sql = $sql . " AND Persona.IdPersona = " . $IdPersona;}
				if($IdPersonaMaestro>0){ $sql = $sql . " AND Persona.IdPersonaMaestro = " . $IdPersonaMaestro;}
				if($buscar_apellido_nombre <>"" ){$sql = $sql . " AND pm.apellidos || ' ' || pm.nombres LIKE '" . $buscar_apellido_nombre . "'";}
				if($buscar_nrodoc <>"" ){$sql = $sql . " AND PM.nrodoc LIKE '" . $buscar_nrodoc."'" ;}
				if($buscar_sexo <>"" ){$sql = $sql . " AND PM.sexo LIKE '" . $buscar_sexo."'" ;}
				if($buscar_compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $buscar_compartido."'" ;}
	
				$sql.=" GROUP BY persona.idpersona, Persona.idsucursal, persona.idpersonamaestro, iddistrito, persona.direccion, persona.email, persona.telefonofijo, persona.telefonomovil, imagen, PM.apellidos, PM.nombres, PM.tipopersona, PM.nrodoc, PM.sexo, pm.apellidos  || ' ' || pm.nombres,persona.Iddistrito, Dist.codigo, Dist.descripcion, Prov.IdUbigeo, Prov.descripcion, Dpto.IdUbigeo , Dpto.descripcion,compartido ";			
            }else{
				$sql = "SELECT persona.idpersona, Persona.idsucursal, persona.idpersonamaestro, iddistrito, persona.direccion, 
		   persona.email, persona.telefonofijo, persona.telefonomovil, imagen, PM.apellidos, PM.nombres, PM.tipopersona, PM.nrodoc, PM.sexo, pm.apellidos  || ' ' || pm.nombres as personamaestro,persona.Iddistrito,
	Dist.codigo as ubigeo, Dist.descripcion as distrito, Prov.IdUbigeo as IdProvincia, Prov.descripcion as provincia, Dpto.IdUbigeo as IdDepartamento, Dpto.descripcion as departamento,compartido FROM persona INNER JOIN PersonaMaestro PM ON Persona.IdPersonaMaestro=PM.IdPersonaMaestro
			left join Ubigeo Dist On Dist.IdUbigeo=persona.IdDistrito 
			left join Ubigeo Prov On Prov.IdUbigeo=Dist.IdUbigeo_Ref
			left join Ubigeo Dpto On Dpto.IdUbigeo=Prov.IdUbigeo_Ref 
			INNER JOIN SUCURSAL s on S.idsucursal=persona.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." ";
				if($idrol<>'') $sql.=" INNER JOIN rolpersona rp on rp.idpersona=persona.idpersona and rp.idsucursal=persona.idsucursal and idrol in (".$idrol.")";
				$sql.=" WHERE persona.Estado='N' ";
				if($idsucursal>0){ $sql = $sql . " AND (Persona.idsucursal=".$idsucursal." or (Persona.idsucursal<>".$idsucursal." and compartido='S'))";}
				if($IdPersona>0){ $sql = $sql . " AND Persona.IdPersona = " . $IdPersona;}
				if($IdPersonaMaestro>0){ $sql = $sql . " AND Persona.IdPersonaMaestro = " . $IdPersonaMaestro;}
				if($buscar_apellido_nombre <>"" ){$sql = $sql . " AND pm.apellidos || ' ' || pm.nombres LIKE '" . $buscar_apellido_nombre . "'";}
				if($buscar_nrodoc <>"" ){$sql = $sql . " AND PM.nrodoc LIKE '" . $buscar_nrodoc."'" ;}
				if($buscar_sexo <>"" ){$sql = $sql . " AND PM.sexo LIKE '" . $buscar_sexo."'" ;}
				if($buscar_compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $buscar_compartido."'" ;}

            }

			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));
			//echo $sql;
		} 	
 	}
	
	//GMC: 03/09/11: Agregue
	function consultarxIdPersonaMaestroyEmpresa($idpersonamaestro,$idempresa)
 	{
		$sql = "Select P.Idsucursal, idpersona, idpersonamaestro, P.iddistrito, P.direccion, 
       P.email, P.telefonofijo, P.telefonomovil, P.imagen, P.estado, compartido From Persona P inner join sucursal s on s.idsucursal=P.idsucursal Where 1=1 ";
		if(isset($idpersonamaestro))	$sql .= " and idpersonamaestro='".$idpersonamaestro."'";
		if(isset($idempresa)) $sql .= " and idempresa=".$idempresa;

		return $this->obtenerDataSQL($sql);
 	
 	}
	
	//GMC: 19/11/11: Agregue
	function verificaExisteUsuario($idpersona,$idsucursal)
 	{
				
		$sql = "SELECT * FROM Usuario WHERE 1=1";
		$sql = $sql." AND Usuario.Estado = 'N' ";
		if($idpersona !=""){ $sql = $sql." AND Usuario.idpersona = ".$idpersona."";}			
		if($idsucursal !=""){ $sql = $sql." AND Usuario.idsucursal = ".$idsucursal."";}			
		$rst = $this->obtenerDataSQL($sql);
		return $rst->rowCount();
 	}
	
	//GMC: 25/05/12: Agregue 
	function consultarPersonaReporteCumpleanos($nro_reg, $nro_hoja, $order, $by, $idsucursal, $IdPersona,$IdPersonaMaestro, $buscar_apellido_nombre='', $buscar_nrodoc='', $buscar_sexo='', $buscar_compartido='', $idrol='', $idtipopersona='', $fechainicio='', $fechafin='')
 	{
		if(parent::getTipoBD()==1){
			$descripcion = "%".$descripcion."%";
			$sql = "execute up_BuscarPersona $nro_reg, $nro_hoja, $order, $by, $idsucursal, $IdPersona, $IdPersonaMaestro, '".$this->mill($descripcion)."'";
			return $this->obtenerDataSP($sql);
		}else{
			if($by==1){
				$by="ASC";
			}else{
				$by="DESC";
			}
			$buscar_apellido_nombre = "%".$buscar_apellido_nombre."%";
			$buscar_nrodoc = "%".$buscar_nrodoc."%";
			$buscar_sexo = "%".$buscar_sexo."%";
			$buscar_compartido = "%".$buscar_compartido."%";
			
			$sql = "SELECT persona.idpersona, Persona.idsucursal, persona.idpersonamaestro, iddistrito, persona.direccion, 
       persona.email, persona.telefonofijo, persona.telefonomovil, imagen, PM.apellidos, PM.nombres, PM.tipopersona, PM.nrodoc, PM.sexo, pm.apellidos  || ' ' || pm.nombres as personamaestro,persona.Iddistrito,
Dist.codigo as ubigeo, Dist.descripcion as distrito, Prov.IdUbigeo as IdProvincia, Prov.descripcion as provincia, Dpto.IdUbigeo as IdDepartamento, Dpto.descripcion as departamento,compartido, to_char(FechaNac,'DD/MM/YYYY') as fechanac FROM persona INNER JOIN PersonaMaestro PM ON Persona.IdPersonaMaestro=PM.IdPersonaMaestro
		left join Ubigeo Dist On Dist.IdUbigeo=persona.IdDistrito 
		left join Ubigeo Prov On Prov.IdUbigeo=Dist.IdUbigeo_Ref
		left join Ubigeo Dpto On Dpto.IdUbigeo=Prov.IdUbigeo_Ref 
		INNER JOIN SUCURSAL s on S.idsucursal=persona.idsucursal and idempresa=".$_SESSION['R_IdEmpresa']." ";
		if($idrol<>'') $sql.=" INNER JOIN rolpersona rp on rp.idpersona=persona.idpersona and rp.idsucursal=persona.idsucursal and idrol in (".$idrol.")";
			$sql.=" WHERE persona.Estado='N' ";
			if($idsucursal>0){ $sql = $sql . " AND (Persona.idsucursal=".$idsucursal." or (Persona.idsucursal<>".$idsucursal." and compartido='S'))";}
			if($IdPersona>0){ $sql = $sql . " AND Persona.IdPersona = " . $IdPersona;}
			if($IdPersonaMaestro>0){ $sql = $sql . " AND Persona.IdPersonaMaestro = " . $IdPersonaMaestro;}
			if($buscar_apellido_nombre <>"" ){$sql = $sql . " AND pm.apellidos || ' ' || pm.nombres LIKE '" . $buscar_apellido_nombre . "'";}
			if($buscar_nrodoc <>"" ){$sql = $sql . " AND PM.nrodoc LIKE '" . $buscar_nrodoc."'" ;}
			if($buscar_sexo <>"" ){$sql = $sql . " AND PM.sexo LIKE '" . $buscar_sexo."'" ;}
			if($buscar_compartido <>"" ){$sql = $sql . " AND compartido LIKE '" . $buscar_compartido."'" ;}
			if($idtipopersona <>"" ){$sql = $sql . " AND tipopersona = '".strtoupper($idtipopersona)."'" ;}
			//if($fechainicio<>''){$sql = $sql . " AND (FechaNac >= '" . $fechainicio . " 00:00:00.000' OR FechaNac is null)";}
			//if($fechafin<>''){$sql = $sql . " AND (FechaNac <= '" . $fechafin . " 23:59:59.999'  OR FechaNac is null)";}
			if($fechainicio<>''){$sql = $sql . " AND to_char(FechaNac,'MM/DD') >= '".substr($fechainicio,3,2)."/".substr($fechainicio,0,2)."'";}
			if($fechafin<>''){$sql = $sql . " AND to_char(FechaNac,'MM/DD') <= '".substr($fechafin,3,2)."/".substr($fechafin,0,2)."'";}

			//echo $sql;
			$rst = $this->obtenerDataSQL($sql.chr(13)."	ORDER BY " . $order . " " . $by . chr(13));
			$cuenta = $rst->fetchAll();
			$total = COUNT($cuenta);
			return $this->obtenerDataSQL("SELECT ".$total." as NroTotal, ".substr($sql,7,strlen($sql)-7)." ".chr(13)."	ORDER BY " . ($order)." ".$by.chr(13)." LIMIT ".$nro_reg." OFFSET ".($nro_reg*$nro_hoja - $nro_reg));			
		} 	
 	}
}
?>