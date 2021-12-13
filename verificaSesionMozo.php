<?php
session_start();
if(!$_SESSION['R_ini_ses']){
        echo "<script>alert('Variables de Session no se pudieron crear; presione aceptar');window.open('loginmozo.php','_self')</script>";
	//echo "Variables de Session no se pudieron crear";
	
	exit();
}
$codusu=$_POST["txtCodigoUsuario"];
$codigo=strtoupper($_POST["CAPTCHA_CODE"]);
$_SESSION['R_Inactividad']=600;
require_once 'modelo/clsSesion.php';

try {
	$objSesion = new clsSesion($usu, $cla);
}catch(PDOException $e) {
	echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}
if(!($usu && $cla)){
	$_SESSION['R_ContSecure']=$_SESSION['R_ContSecure']+1;
	echo "<script>alert('Usuario y Clave obligatorios');history.go(-1);</script>";
	exit();
}
if($_SESSION['R_ContSecure']>2){
	if($codigo!=$_SESSION['R_CAPTCHA_CODE']){
		echo "<script>alert('El codigo ingresado no coincide con la imagen');history.go(-1);</script>";
		exit();
	}
}

$rst = $objSesion->verificaSesion();
if(is_string($rst)){
	echo $rst;
}
$datoSesion = $rst->fetchObject();
if(!empty($datoSesion)){
	$_SESSION['R_ContSecure'] = 0;
	$_SESSION['R_versesadm'] = 1;
	$_SESSION['R_origen_ses']="I"; //I->INTERNO (quiere decir que se logeo); E->Externo (solo prar usuarios externos)
	$_SESSION['R_IdEmpresa'] = $datoSesion->idempresa;
	$_SESSION['R_NombreEmpresa'] = $datoSesion->nombreempresa;
	$_SESSION['R_RucEmpresa'] = $datoSesion->ruc;
	$_SESSION['R_IdSucursal'] = $datoSesion->idsucursal;
	$_SESSION['R_NombreSucursal'] = $datoSesion->nombresucursal;
	$_SESSION['R_IdUsuario'] = $datoSesion->idusuario;
	$_SESSION['R_NombreUsuario'] = $usu;
	$_SESSION['R_Clave'] = $cla;
	$_SESSION['R_IdSucursalUsuario'] = $datoSesion->idsucursal;
	$_SESSION['R_IdPersonaMaestro'] = $datoSesion->idpersonamaestro;
	$_SESSION['R_IdPersona'] = $datoSesion->idpersona;
	$_SESSION['R_NombresPersona'] = $datoSesion->nombres;
	$_SESSION['R_ApellidosPersona'] = $datoSesion->apellidos;
	$_SESSION['R_NroFilaMostrar'] = $datoSesion->nrofilamostrar;
	$_SESSION['R_OpcionMenuDefecto'] = $datoSesion->opcionmenudefecto;
	$_SESSION['R_Logo'] = $datoSesion->logo;
	$_SESSION['R_Compartido'] = $datoSesion->compartido;
    $_SESSION['R_DireccionSucursal'] = $datoSesion->direccion;
    $_SESSION['R_RucSucursal'] = $datoSesion->rucsucursal;
	//INICIA SITUACION, SI SU SITUACION ES INACTIVA, ACTIVO EMPRESA Y REALIZO PARAMETRIZACION BASE
	$situacionempresa=$datoSesion->situacion;
	if($situacionempresa=='I'){
		$objSesion->insertarParametrizacionBase($_SESSION['R_IdEmpresa'],$_SESSION['R_IdSucursal']);
	}
	//FIN SITUACION
	date_default_timezone_set('America/Lima');
	$_SESSION['R_FechaProceso'] = date("d/m/Y");
	$_SESSION['R_IGV'] = 18;
	$_SESSION['R_TipoCambio'] = 2.8;
	$rst = $objSesion->obtenerDataSQL("SELECT U.idperfil, descripcion FROM Usuario U INNER JOIN PERFIL P ON P.idperfil=U.idperfil WHERE U.IdSucursal = ".$datoSesion->idsucursal." AND IdUsuario = ".$datoSesion->idusuario);
	$datoPerfil = $rst->fetchObject();
	$_SESSION['R_IdPerfil'] = $datoPerfil->idperfil;
	$_SESSION['R_Perfil'] = $datoPerfil->descripcion;
	
	$rst = $objSesion->obtenerDataSQL("SELECT AT.idcaja, c.numero as caja, T.idturno, T.nombre as turno, C.idsalon, S.descripcion as salon FROM Usuario 
		INNER JOIN Persona ON Usuario.IdPersona = Persona.IdPersona and Usuario.IdSucursal=Persona.IdSucursal INNER JOIN RolPersona RP1 ON RP1.IdPersona=Persona.IdPersona and Persona.IdSucursal=RP1.IdSucursal and RP1.IdRol=1 INNER JOIN PersonaMaestro PMP ON PMP.IdPersonaMaestro=Persona.IdPersonaMaestro
		LEFT JOIN AsignacionTurno AT on AT.idpersona=Persona.idpersona and AT.IdSucursalPersona=Persona.IdSucursal INNER JOIN Turno T on T.idturno=AT.idturno and T.IdSucursal=AT.IdSucursal and horainicio<='".date("H:m:s")."' and horafin>='".date("H:m:s")."' INNER JOIN CAJA C ON C.idcaja=AT.idcaja and C.IdSucursal=AT.IdSucursal INNER JOIN Salon S ON S.idsalon=C.idsalon and S.idsucursal=C.idsucursal WHERE Usuario.IdSucursal = ".$_SESSION['R_IdSucursal']." AND IdUsuario = ".$_SESSION['R_IdUsuario']." and AT.situacion='N'");
	$datoAsignacionTurno = $rst->fetchObject();
	$_SESSION['R_IdCaja'] = $datoAsignacionTurno->idcaja;
	$_SESSION['R_IdSalon'] = $datoAsignacionTurno->idsalon;
	$_SESSION['R_IdTurno'] = $datoAsignacionTurno->idturno;
	$_SESSION['R_Caja'] = $datoAsignacionTurno->caja;
	$_SESSION['R_Salon'] = $datoAsignacionTurno->salon;
	$_SESSION['R_Turno'] = $datoAsignacionTurno->turno;
	
	//APARIENCIA
	require_once "modelo/clsParametro.php";
	$objParametro = new clsParametro(18,$_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$rst = $objParametro->consultarParametroUser(0,$_SESSION['R_IdEmpresa'],"");
	while($datoparam=$rst->fetchObject()){
		//estilo
		if($datoparam->idparametros==17){$_SESSION['R_Estilo']='estilo'.strtolower($datoparam->valor);}
		//cerrar por inactividad
		if($datoparam->idparametros==18){$_SESSION['R_Inactividad']=$datoparam->valor*60;}
	}

	header("Location: main.php");
}else{
	$_SESSION['R_ContSecure']=$_SESSION['R_ContSecure']+1;
	echo "<script>alert('Cuenta no esta activa, consulte con el Administrador del Sistema');window.open('login.php','_self')</script>";
	exit();
}
?>