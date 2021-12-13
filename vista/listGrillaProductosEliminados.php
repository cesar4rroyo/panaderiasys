<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT pd.descripcion as producto,ct.descripcion as categoria, dme.cantidad, dme.precioventa,"
        . " dme.comentario, ms.numero as mesa, us.nombreusuario, dme.fecha, mv.numero as comanda, mv.idmovimiento"
        . " FROM detallemovalmacen_eliminado dme LEFT JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) mv ON mv.idmovimiento=dme.idmovimiento"
        . " LEFT JOIN producto pd ON dme.idproducto=pd.idproducto LEFT JOIN categoria ct ON pd.idcategoria = ct.idcategoria"
        . " LEFT JOIN mesa ms ON ms.idmesa = mv.idmesa LEFT JOIN usuario us ON us.idusuario = mv.idusuario WHERE ms.idsucursal = ".$_SESSION['R_IdSucursal'];
$fechaInicio = $_GET["txtFechaInicio"]." ".$_GET["txtHoraInicio"];
$fechaFin = $_GET["txtFechaFin"]." ".$_GET["txtHoraFin"];
$categoria = $_GET["cbocategoria"];
$producto = $_GET["txtBuscar_Descripcion"];
if(!empty($fechaInicio)){
    $sql.=" AND cast(dme.fecha as date) >= '$fechaInicio'";
}
if(!empty($fechaFin)){
    $sql.=" AND cast(dme.fecha as date) <= '$fechaFin'";
}
if($categoria>0){
    $sql.=" AND ct.idcategoria = $categoria";
}
$sql.=" AND pd.descripcion LIKE '%".  strtoupper($producto)."%'";
$sql.=" ORDER BY dme.fecha ASC";
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
                        <th class="center">PRODUCTO</th>
                        <th class="center">CATEGORIA</th>
                        <th class="center">CANTIDAD</th>
                        <th class="center">PRECIO</th>
                        <th class="center">COMANDA</th>
                        <th class="center">MESA</th>
                        <th class="center">MESERO</th>
                        <th class="center">COMENTARIO</th>
                        <th class="center">FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { ?>
                    <tr>
                        <td class="center"><?php echo $mostrar["producto"] ?></td>
                        <td class="center"><?php echo $mostrar["categoria"] ?></td>
                        <td class="center"><?php echo $mostrar["cantidad"] ?></td>
                        <td class="center"><?php echo $mostrar["precioventa"] ?></td>
                        <td class="center" style="cursor: pointer" onclick="detalleMovimiento(<?php echo $mostrar["idmovimiento"]?>)"><?php echo $mostrar["comanda"] ?></td>
                        <td class="center"><?php echo $mostrar["mesa"] ?></td>
                        <td class="center"><?php echo $mostrar["nombreusuario"] ?></td>
                        <td class="center"><?php echo $mostrar["comentario"] ?></td>
                        <td class="center"><?php echo substr($mostrar["fecha"],0,16) ?></td>
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