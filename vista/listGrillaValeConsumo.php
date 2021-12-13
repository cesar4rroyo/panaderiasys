<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT vl.*, now()::date-vl.fecha_emision as transcurridos, mv.numero as comanda, mv.idmovimiento, us.nombreusuario FROM vale vl 
    LEFT JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) mv ON vl.idmovimiento = mv.idmovimiento
    LEFT JOIN usuario us ON us.idusuario = vl.idusuario 
    WHERE vl.estado <> 'A'";
$fechaInicio = $_GET["txtFechaInicio"];
$fechaFin = $_GET["txtFechaFin"];
$usuario = $_GET["cboUsuario"];
$estado = $_GET["txtEstado"];
$propietario = $_GET["txtBuscar_Apellido_Nombre"];
$sql.=" AND vl.propietario LIKE '%$propietario%'";
if(!empty($fechaInicio)){
    $sql.=" AND vl.fecha_emision >= '$fechaInicio'";
}
if(!empty($fechaFin)){
    $sql.=" AND vl.fecha_emision <= '$fechaFin'";
}
if($usuario>0){
    $sql.=" AND us.idusuario = $usuario";
}
if($estado!="T"){
    $sql.=" AND vl.estado = '$estado'";
}
$sql.=" ORDER BY vl.correlativo ASC";
//echo($sql);
$rs = $objProducto->obtenerDataSQL($sql);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
</head>
<body>
    <div class="row">
        <div class="col s12">
            <table class="bordered highlight">
                <thead>
                    <tr>
                        <th class="center">CORRELATIVO</th>
                        <th class="center">PROPIETARIO</th>
                        <th class="center">VALOR</th>
                        <th class="center">FECHA EMISION</th>
                        <th class="center">PLAZO</th>
                        <th class="center">USUARIO</th>
                        <th class="center">FECHA CONSUMO</th>
                        <th class="center">COMANDA</th>
                        <th class="center">ESTADO</th>
                        <th class="center">OPERACION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { $porcentaje = round($mostrar["transcurridos"]*100/$mostrar["plazo"]);?>
                    <tr class="<?php if($mostrar["estado"]=="N"){if($porcentaje<=70){ echo 'green';}elseif($porcentaje<=95){ echo "yellow";}else{ echo "red";} ;echo " ".$porcentaje;}?>">
                        <td class="center"><?php echo str_pad($mostrar["correlativo"],6,"0",STR_PAD_LEFT); ?></td>
                        <td class="center"><?php echo $mostrar["propietario"] ?></td>
                        <td class="center">S/. <?php echo number_format($mostrar["valor"],2) ?></td>
                        <td class="center"><?php echo $mostrar["fecha_emision"] ?></td>
                        <td class="center"><?php echo $mostrar["plazo"] ?></td>
                        <td class="center"><?php echo $mostrar["nombreusuario"] ?></td>
                        <td class="center"><?php echo $mostrar["fecha_consumo"] ?></td>
                        <td class="center" style="cursor: pointer" onclick="detalleMovimiento(<?php echo $mostrar["idmovimiento"]?>)"><?php echo $mostrar["comanda"] ?></td>
                        <td class="center"><?php if($mostrar["estado"]=="N") {echo "PENDIENTE";}elseif($mostrar["estado"]=="C"){ echo 'CONSUMIDO'; }elseif($mostrar["estado"]=="V"){ echo 'VENCIDO'; } ?></td>
                        <td class="center">
                            <?php if($mostrar["estado"]=="N") {?>
                            <div class="center">
                                <div class="col s4"><button type="button" class="btn btn-floating blue tooltipped" data-position="bottom" data-delay="50" data-tooltip="EDITAR" onclick="javascript:setRun('vista/mantValeConsumo', 'accion=ACTUALIZAR&Id=<?php echo $mostrar["idvale"];?>&id_clase=53', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();$('#tablaActual').hide();"><i class="material-icons">edit</i></button></div>
                                <div class="col s4"><button type="button" class="btn btn-floating pink tooltipped" data-position="bottom" data-delay="50" data-tooltip="VENCER" onclick="javascript:consultaControlador('<?php echo $mostrar["idvale"];?>','VENCER','¿CAMBIAR EL ESTADO?');"><i class="material-icons">play_for_work</i></button></div>
                                <div class="col s4"><button type="button" class="btn btn-floating red tooltipped" data-position="bottom" data-delay="50" data-tooltip="ELIMINAR" onclick="javascript:consultaControlador('<?php echo $mostrar["idvale"];?>','ELIMINAR','¿CAMBIAR EL ESTADO?');"><i class="material-icons">clear</i></button></div>
                            </div>
                            <?php }?>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
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