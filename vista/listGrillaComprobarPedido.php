<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT ds.*,m.numero,m.total,m2.idcaja,m.fecha
        from movimientohoy m
        inner join detallestock ds on ds.idpedido=m.idmovimiento
        inner join (select * from movimientohoy union all select * from movimiento) m2 on ds.idventa=m2.idmovimiento
        where m.situacion<>'A' and m.estado<>'I' and m2.idcaja not in (7,6,4) and ds.estado='N'";
if($_GET["txtFechaInicio"]!=""){
    $sql.=" AND m.fecha >= '".$_GET["txtFechaInicio"]." ".$_GET["txtHoraInicio"]."'";
}
if($_GET["txtPedido"]!=""){
    $sql.=" AND m.numero like '%".$_GET["txtPedido"]."%'";
}
if($_GET["cboSituacion"]!=""){
    $sql.=" AND ds.situacion like '%".$_GET["cboSituacion"]."%'";
}
$sql.=" order by m.numero desc";
//echo($sql);
$rs = $objProducto->obtenerDataSQL($sql);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
</head>
<body>
    <div class="row">
        <div class="col s8" id="tablaContenido">
            <table class="striped bordered highlight">
                <thead>
                    <tr>
                        <th class="center">FECHA</th>
                        <th class="center">COMANDA</th>
                        <th class="center">CAJA</th>
                        <th class="center">TOTAL</th>
                        <th class="center"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { 
                        if($mostrar["idcaja"]==1 || $mostrar["idcaja"]==2 || $mostrar["idcaja"]==3){
                            $color="purple";
                        }elseif($mostrar["idcaja"]==6){//arriba
                            $color="sky-blue";
                        }elseif($mostrar["idcaja"]==7){//entradas
                            $color="green";
                        }else{
                            $color="yellow";
                        }
                    ?>
                    <tr class="<?=$color?>">
                        <td class="center"><?php echo date("d/m/Y H:i",strtotime($mostrar["fecha"])); ?></td>
                        <td class="center"><?php echo $mostrar["numero"] ?></td>
                        <td class="center"><?php echo $mostrar["idcaja"] ?></td>
                        <td class="center"><?php echo number_format($mostrar["total"],2,'.','') ?></td>
                        <?php if($mostrar["situacion"]=="P"){?>
                        <td class="center"><button type="button" class="btn-floating red tooltipped" data-position="left" data-delay="30" data-tooltip="Comprobar" onclick="modalDetallePedido('<?=$mostrar['numero']?>',<?=$mostrar["iddetallestock"]?>,<?=$mostrar["idventa"]?>);"><i class="material-icons">check</i></button></td>
                    <?php }?>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <div class="col s4">
            <table class="striped bordered highlight">
                <thead>
                    <tr>
                        <th class="center">PRODUCTO</th>
                        <th class="center">STOCK</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $rst=$objProducto->consultarProductoReporteStock(1000, 1, 1, 1, 0, '', 0,0,'','','S');
                while($dat=$rst->fetchObject()){
                    echo "<tr>";
                    echo "<td class='center'>".$dat->descripcion."</td>";
                    echo "<td class='center'>".number_format($dat->stock,2,'.','')."</td>";
                    echo "</tr>";
                }
                ?>
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