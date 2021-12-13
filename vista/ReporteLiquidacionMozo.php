<?php
require_once('../modelo/clsMovCaja.php');
$objMovCaja= new clsMovCaja($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$ultimoConcepto = $objMovCaja->consultarultimoconcepto(); 
?>
<script>
<?php if($ultimoConcepto==1){?>
alert("Esta viendo la liquidacion de mozos del turno anterior");
<?php }?>
window.open('vista/reportes/ReporteLiquidacionMozo.php?fecha=<?php echo $_SESSION["R_FechaProceso"]?>','_blank');
</script>
