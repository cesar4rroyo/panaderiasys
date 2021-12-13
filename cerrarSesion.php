<?php
session_start();
$versesadm = null;
$IdEmpresa = null;
$NombreEmpresa = null;
$IdSucursal = null;
$NombreSucursal = null;
$IdUsuario = null;
$NombreUsuario = null;
$Clave = null;
$NombresPersona = null;
$ApellidosPersona = null;

$_SESSION['R_versesadm'] = $versesadm;
$_SESSION['R_IdEmpresa'] = $IdEmpresa;
$_SESSION['R_NombreEmpresa'] = $NombreEmpresa;
$_SESSION['R_IdSucursal'] = $IdSucursal;
$_SESSION['R_NombreSucursal'] = $NombreSucursal;
$_SESSION['R_IdUsuario'] = $IdUsuario;
$_SESSION['R_NombreUsuario'] = $NombreUsuario;
$_SESSION['R_Clave'] = $Clave;
$_SESSION['R_NombresPersona'] = $NombresPersona;
$_SESSION['R_ApellidosPersona'] = $ApellidosPersona;
	
session_unset();
session_destroy();
if($_GET['a']=='SALIR'){
echo '<script>window.close();</script>';
}else{
    if($_GET['Origen']=="Mozo" || $_GET['Origen']=="Mozo2"){
        header("Location: loginMozo.php");
    }else {
        header("Location: index.php");        
    }
}
//exit();
?>