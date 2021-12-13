<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT mv.*, ms.numero as mesa, us.nombreusuario  FROM (SELECT * FROM movimiento 
UNION SELECT * FROM movimientohoy) mv 
LEFT JOIN mesa ms ON ms.idmesa = mv.idmesa LEFT JOIN usuario us ON us.idusuario = mv.idusuario 
WHERE TRUE AND mv.idtipomovimiento = 5 AND mv.estado = 'A' AND ms.idsucursal = ".$_SESSION['R_IdSucursal'];
$fechaInicio = $_GET["txtFechaInicio"]." ".$_GET["txtHoraInicio"];
$fechaFin = $_GET["txtFechaFin"]." ".$_GET["txtHoraFin"];
$usuario = $_GET["cboUsuario"];
if(!empty($fechaInicio)){
    $sql.=" AND mv.fecha >= '$fechaInicio'";
}
if(!empty($fechaFin)){
    $sql.=" AND mv.fecha <= '$fechaFin'";
}
if($usuario>0){
    $sql.=" AND us.idusuario = $usuario";
}
$sql.=" ORDER BY mv.fecha ASC";
//echo($sql);
$rs = $objProducto->obtenerDataSQL($sql);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
</head>
<body>
    <div class="row">
        <div class="col s12" id="tablaContenido">
            <table class="striped bordered highlight">
                <thead>
                    <tr>
                        <th class="center">COMANDA</th>
                        <th class="center">SUBTOTAL</th>
                        <th class="center">TOTAL</th>
                        <th class="center">MESA</th>
                        <th class="center">MESERO</th>
                        <th class="center">COMENTARIO</th>
                        <th class="center">FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { ?>
                    <tr>
                        <td class="center" style="cursor: pointer" onclick="detalleMovimiento(<?php echo $mostrar["idmovimiento"]?>)"><?php echo $mostrar["numero"] ?></td>
                        <td class="center"><?php echo $mostrar["subtotal"] ?></td>
                        <td class="center"><?php echo $mostrar["total"] ?></td>
                        <td class="center"><?php echo $mostrar["mesa"] ?></td>
                        <td class="center"><?php echo $mostrar["nombreusuario"] ?></td>
                        <td class="center"><?php echo $mostrar["comentario"] ?></td>
                        <td class="center"><?php echo $mostrar["fecha"] ?></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col s12" style="padding-bottom: 10px;">
            <button type="button" class="btn-large right green" style="margin-right: 10px;" onclick="window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#tablaContenido').html()));">EXCEL<i class="material-icons right">description</i></button>
        </div>
    </div>
</body>
</html>
<script>
function buscarGrilla(nro_hoja){
	if(document.getElementById("nro_hoj")){
		document.getElementById("nro_hoj").value = nro_hoja;
	}
	buscar();
}
</script>