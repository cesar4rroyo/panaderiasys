<?
?><?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT p.descripcion as producto,c.descripcion as categoria,
(select sum(cantidad) from detallemovalmacen where idsucursal=p.idsucursal and idproducto=p.idproducto and idmovimiento in (select idmovimiento from movimientohoy where idtipomovimiento=2 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' union all select idmovimiento from movimiento where idtipomovimiento=2 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59')) as ventas,
(select sum(cantidad) from detallemovalmacen where idsucursal=p.idsucursal and idproducto=p.idproducto and idmovimiento in (select idmovimiento from movimientohoy where idtipodocumento=7 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' and motivo in ('','Inventario') union all select idmovimiento from movimiento where idtipodocumento=7 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59'  and motivo in ('','Inventario'))) as ingresos1,
(select sum(cantidad) from detallemovalmacen where idsucursal=p.idsucursal and idproducto=p.idproducto and idmovimiento in (select idmovimiento from movimientohoy where idtipodocumento=7 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59'  and motivo not in ('','Inventario') union all select idmovimiento from movimiento where idtipodocumento=7 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59'  and motivo not in ('','Inventario'))) as ingresos2,
(select sum(cantidad) from detallemovalmacen where idsucursal=p.idsucursal and idproducto=p.idproducto and idmovimiento in (select idmovimiento from movimientohoy where idtipodocumento=8 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' and motivo in ('','Merma') union all select idmovimiento from movimiento where idtipodocumento=8 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' and motivo in ('','Merma'))) as salidas1,
(select sum(cantidad) from detallemovalmacen where idsucursal=p.idsucursal and idproducto=p.idproducto and idmovimiento in (select idmovimiento from movimientohoy where idtipodocumento=8 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' and motivo not in ('','Merma','Inventario') union all select idmovimiento from movimiento where idtipodocumento=8 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' and motivo not in ('','Merma','Inventario'))) as salidas2,
(select sum(cantidad) from detallemovalmacen where idsucursal=p.idsucursal and idproducto=p.idproducto and idmovimiento in (select idmovimiento from movimientohoy where idtipodocumento=8 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' and motivo in ('Inventario') union all select idmovimiento from movimiento where idtipodocumento=8 and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]." and fecha>='".$_GET["txtFechaInicio"]." 00:00:00' and fecha<='".$_GET["txtFechaInicio"]." 23:59:59' and motivo  in ('Inventario'))) as salidas3,
(select saldoactual from kardex where idproducto=p.idproducto and idsucursal=p.idsucursal and fecha<'".$_GET["txtFechaInicio"]." 00:00:00' order by idkardex desc limit 1) as inicial,
(select precioventa from listaunidad where idunidad=p.idunidadbase and idunidadbase=p.idunidadbase and p.idproducto=idproducto and p.idsucursal=idsucursal and idsucursal=idsucursalproducto limit 1) as precioventa
FROM producto p
inner join categoria c on c.idcategoria=p.idcategoria and c.idsucursal=p.idsucursal
WHERE p.estado='N' and p.idsucursal = ".$_SESSION['R_IdSucursal'];
if($_GET["idcategoria"]!=""){
    $sql.=" AND p.idcategoria in (".$_GET["idcategoria"].")";
}
$sql.=" ORDER BY c.descripcion,p.descripcion ASC";
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
                        <th class="center">CATEGORIA</th>
                        <th class="center">PRODUCTO</th>
                        <th class="center" colspan="2">INICIO</th>
                        <th class="center" colspan="2">INGRESOS  X INVENTARIO</th>
                        <th class="center" colspan="2">INGRESOS  X PRODUCCION</th>
                        <th class="center" colspan="2">VENTAS</th>
                        <th class="center" colspan="2">SALIDAS X INVENTARIO</th>
                        <th class="center" colspan="2">SALIDAS X MERMAS</th>
                        <th class="center" colspan="2">SALIDAS X TRASLADO</th>
                        <th class="center" colspan="2">FINAL</th>
                        <!--th class="center">TICKET</th-->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { 
                        $inicial = $inicial + number_format($mostrar["inicial"]*$mostrar["precioventa"],2,'.','');
                        $ingresos = $ingresos + number_format($mostrar["ingresos2"]*$mostrar["precioventa"],2,'.','');
                        $ingresos1 = $ingresos1 + number_format($mostrar["ingresos1"]*$mostrar["precioventa"],2,'.','');
                        $ventas = $ventas + number_format($mostrar["ventas"]*$mostrar["precioventa"],2,'.','');
                        $salidas = $salidas + number_format($mostrar["salidas1"]*$mostrar["precioventa"],2,'.','');
                        $salidas2 = $salidas2 + number_format($mostrar["salidas2"]*$mostrar["precioventa"],2,'.','');
                        $salidas3 = $salidas3 + number_format($mostrar["salidas3"]*$mostrar["precioventa"],2,'.','');
                        $final = $final + number_format(($mostrar["inicial"]-$mostrar["ventas"]+$mostrar["ingresos1"]+$mostrar["ingresos2"]-$mostrar["salidas1"]-$mostrar["salidas2"]-$mostrar["salidas3"])*$mostrar["precioventa"],2,'.','');
                        ?>
                    <tr>
                        <td class="center"><?php echo $mostrar["categoria"]; ?></td>
                        <td class="center"><?php echo $mostrar["producto"]; ?></td>
                        <td class="center" style="background: orange;font-weight: bold;"><?php echo number_format($mostrar["inicial"],2,'.','') ?></td>
                        <td class="center" style="background: orange;font-weight: bold;">S/ <?php echo number_format($mostrar["inicial"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($mostrar["ingresos1"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;">S/ <?php echo number_format($mostrar["ingresos1"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($mostrar["ingresos2"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;">S/ <?php echo number_format($mostrar["ingresos2"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #0000ff45 !important;font-weight: bold;"><?php echo number_format($mostrar["ventas"],2,'.','') ?></td>
                        <td class="center" style="background: #0000ff45 !important;font-weight: bold;">S/ <?php echo number_format($mostrar["ventas"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($mostrar["salidas3"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;">S/ <?php echo number_format($mostrar["salidas3"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($mostrar["salidas1"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;">S/ <?php echo number_format($mostrar["salidas1"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00003e;font-weight: bold;"><?php echo number_format($mostrar["salidas2"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00003e;font-weight: bold;">S/ <?php echo number_format($mostrar["salidas2"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: yellow;font-weight: bold;"><?php echo number_format($mostrar["inicial"]-$mostrar["ventas"]+$mostrar["ingresos1"]+$mostrar["ingresos2"]-$mostrar["salidas1"]-$mostrar["salidas2"]-$mostrar["salidas3"],2,'.','') ?></td>
                        <td class="center" style="background: yellow;font-weight: bold;">S/ <?php echo number_format(($mostrar["inicial"]-$mostrar["ventas"]+$mostrar["ingresos1"]+$mostrar["ingresos2"]-$mostrar["salidas1"]-$mostrar["salidas2"]-$mostrar["salidas3"])*$mostrar["precioventa"],2,'.','') ?></td>
                    </tr>
                    <?php }?>
                </tbody>
                <tfoot>
                    <th class="ceter" colspan="2">TOTALES</th>
                    <th class="center"></th>
                    <td class="center" style="background: orange;font-weight: bold;">S/ <?php echo number_format($inicial,2,'.','') ?></td>
                    <th class="center"></th>
                    <td class="center" style="background: #008000ad !important;font-weight: bold;">S/ <?php echo number_format($ingresos1,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #008000ad !important;font-weight: bold;">S/ <?php echo number_format($ingresos,2,'.','') ?></th>
                    <th class="center"></th>
                    <th class="center" style="background: #0000ff45 !important;font-weight: bold;">S/ <?php echo number_format($ventas,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #ff00006e;font-weight: bold;">S/ <?php echo number_format($salidas3,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #ff00006e;font-weight: bold;">S/ <?php echo number_format($salidas,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #ff00006e;font-weight: bold;">S/ <?php echo number_format($salidas2,2,'.','') ?></th>
                    <th class="center"></th>
                    <th class="center" style="background: yellow;font-weight: bold;">S/ <?php echo number_format($final,2,'.','') ?></th>
                </tfoot>
            </table>
        </div>
        <div class="col s12" id="tablaContenido2" style="display: none;">
            <table class="striped bordered highlight">
                <thead>
                    <tr>
                        <th class="center">CATEGORIA</th>
                        <th class="center">PRODUCTO</th>
                        <th class="center" colspan="2">INICIO</th>
                        <th class="center" colspan="2">INGRESOS  X INVENTARIO</th>
                        <th class="center" colspan="2">INGRESOS  X PRODUCCION</th>
                        <th class="center" colspan="2">VENTAS</th>
                        <th class="center" colspan="2">SALIDAS X INVENTARIO</th>
                        <th class="center" colspan="2">SALIDAS X MERMAS</th>
                        <th class="center" colspan="2">SALIDAS X TRASLADO</th>
                        <th class="center" colspan="2">FINAL</th>
                        <!--th class="center">TICKET</th-->
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $inicial=0;$ingresos1=0;$ingresos=0;$ventas=0;$salidas2=0;$salidas=0;$final=0;
                    $rs = $objProducto->obtenerDataSQL($sql);
                    while ($mostrar = $rs->fetch()) { 
                        $inicial = $inicial + number_format($mostrar["inicial"]*$mostrar["precioventa"],2,'.','');
                        $ingresos = $ingresos + number_format($mostrar["ingresos2"]*$mostrar["precioventa"],2,'.','');
                        $ingresos1 = $ingresos1 + number_format($mostrar["ingresos1"]*$mostrar["precioventa"],2,'.','');
                        $ventas = $ventas + number_format($mostrar["ventas"]*$mostrar["precioventa"],2,'.','');
                        $salidas = $salidas + number_format($mostrar["salidas1"]*$mostrar["precioventa"],2,'.','');
                        $salidas2 = $salidas2 + number_format($mostrar["salidas2"]*$mostrar["precioventa"],2,'.','');
                        $salidas3 = $salidas3 + number_format($mostrar["salidas3"]*$mostrar["precioventa"],2,'.','');
                        $final = $final + number_format(($mostrar["inicial"]-$mostrar["ventas"]+$mostrar["ingresos1"]+$mostrar["ingresos2"]-$mostrar["salidas1"]-$mostrar["salidas2"]-$mostrar["salidas3"])*$mostrar["precioventa"],2,'.','');
                        ?>
                    <tr>
                        <td class="center"><?php echo $mostrar["categoria"]; ?></td>
                        <td class="center"><?php echo $mostrar["producto"]; ?></td>
                        <td class="center" style="background: orange;font-weight: bold;"><?php echo number_format($mostrar["inicial"],2,'.','') ?></td>
                        <td class="center" style="background: orange;font-weight: bold;"><?php echo number_format($mostrar["inicial"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($mostrar["ingresos1"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($mostrar["ingresos1"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($mostrar["ingresos2"],2,'.','') ?></td>
                        <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($mostrar["ingresos2"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #0000ff45 !important;font-weight: bold;"><?php echo number_format($mostrar["ventas"],2,'.','') ?></td>
                        <td class="center" style="background: #0000ff45 !important;font-weight: bold;"><?php echo number_format($mostrar["ventas"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($mostrar["salidas3"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($mostrar["salidas3"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($mostrar["salidas1"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($mostrar["salidas1"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00003e;font-weight: bold;"><?php echo number_format($mostrar["salidas2"],2,'.','') ?></td>
                        <td class="center" style="background: #ff00003e;font-weight: bold;"><?php echo number_format($mostrar["salidas2"]*$mostrar["precioventa"],2,'.','') ?></td>
                        <td class="center" style="background: yellow;font-weight: bold;"><?php echo number_format($mostrar["inicial"]-$mostrar["ventas"]+$mostrar["ingresos1"]+$mostrar["ingresos2"]-$mostrar["salidas1"]-$mostrar["salidas2"]-$mostrar["salidas3"],2,'.','') ?></td>
                        <td class="center" style="background: yellow;font-weight: bold;"><?php echo number_format(($mostrar["inicial"]-$mostrar["ventas"]+$mostrar["ingresos1"]+$mostrar["ingresos2"]-$mostrar["salidas1"]-$mostrar["salidas2"]-$mostrar["salidas3"])*$mostrar["precioventa"],2,'.','') ?></td>
                    </tr>
                    <?php }?>
                </tbody>
                <tfoot>
                    <th class="ceter" colspan="2">TOTALES</th>
                    <th class="center"></th>
                    <td class="center" style="background: orange;font-weight: bold;"><?php echo number_format($inicial,2,'.','') ?></td>
                    <th class="center"></th>
                    <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($ingresos1,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #008000ad !important;font-weight: bold;"><?php echo number_format($ingresos,2,'.','') ?></th>
                    <th class="center"></th>
                    <th class="center" style="background: #0000ff45 !important;font-weight: bold;"><?php echo number_format($ventas,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #ff00006e;font-weight: bold;">S/ <?php echo number_format($salidas,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($salidas,2,'.','') ?></th>
                    <th class="center"></th>
                    <td class="center" style="background: #ff00006e;font-weight: bold;"><?php echo number_format($salidas2,2,'.','') ?></th>
                    <th class="center"></th>
                    <th class="center" style="background: yellow;font-weight: bold;"><?php echo number_format($final,2,'.','') ?></th>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col s12" style="padding-bottom: 10px;">
            <button type="button" class="btn-large right green" style="margin-right: 10px;" onclick="window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#tablaContenido2').html()));">EXCEL<i class="material-icons right">description</i></button>
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