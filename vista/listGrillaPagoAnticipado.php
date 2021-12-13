<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT pa.*, psm.nombres, psm.apellidos, mv.numero as comanda, mv.idmovimiento, us.nombreusuario FROM pagoanticipado pa
    LEFT JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) mv ON pa.idmovimiento = mv.idmovimiento and mv.idsucursal=1
    LEFT JOIN persona ps ON ps.idpersona = pa.idcliente and ps.idsucursal=1
    LEFT JOIN personamaestro psm ON psm.idpersonamaestro = ps.idpersonamaestro
    LEFT JOIN usuario us ON us.idusuario = pa.idusuario and us.idsucursal=1
    WHERE pa.estado <> 'A'";
$fechaInicio = $_GET["txtFechaInicio"];
$fechaFin = $_GET["txtFechaFin"];
$usuario = $_GET["cboUsuario"];
$estado = $_GET["txtEstado"];
$propietario = $_GET["txtBuscar_Apellido_Nombre"];
$descripcion = $_GET["txtDescripcion"];
$sql.=" AND (psm.nombres LIKE '%$propietario%' OR psm.apellidos LIKE '%$propietario%')";
if(!empty($fechaInicio)){
    $sql.=" AND pa.fecha >= '$fechaInicio'";
}
if(!empty($fechaFin)){
    $sql.=" AND pa.fecha <= '$fechaFin'";
}
if($usuario>0){
    $sql.=" AND us.idusuario = $usuario";
}
if($estado!="T"){
    $sql.=" AND pa.estado = '$estado'";
}
if($descripcion!=""){
    $sql.=" AND pa.datosadicionales like '%$descripcion%'";
}
$sql.=" ORDER BY pa.correlativo ASC";
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
            <table class="bordered highlight striped">
                <thead>
                    <tr>
                        <th class="center">CORRELATIVO</th>
                        <th class="center">PROPIETARIO</th>
                        <th class="center">A CTA.</th>
                        <th class="center">TOTAL</th>
                        <th class="center">FECHA EMISION</th>
                        <th class="center">FECHA ENTREGA</th>
                        <th class="center">TIPO DE PAGO</th>
                        <th class="center">DATOS PAGO</th>
                        <th class="center">USUARIO</th>
                        <!--th class="center">SALDO</th-->
                        <!--th class="center">FECHA CONSUMO</th-->
                        <!--th class="center">COMANDA</th-->
                        <th class="center">ESTADO</th>
                        <th class="center">OPERACION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { ?>
                    <tr>
                        <td class="center"><?php echo str_pad($mostrar["correlativo"],6,"0",STR_PAD_LEFT); ?></td>
                        <td class="center"><?php echo $mostrar["apellidos"]." ".$mostrar["nombres"]; ?></td>
                        <td class="center">S/. <?php echo number_format($mostrar["valor"],2) ?></td>
                        <td class="center">S/. <?php echo number_format($mostrar["total"],2) ?></td>
                        <td class="center"><?php echo date("d/m/Y",strtotime($mostrar["fecha"])) ?></td>
                        <td class="center"><?php echo date("d/m/Y",strtotime($mostrar["fechaentrega"])) ?></td>
                        <td class="center"><?php if($mostrar["tipopago"]=="E") {echo "EFECTIVO";}elseif($mostrar["tipopago"]=="C"){ echo 'CHEQUE'; }elseif($mostrar["tipopago"]=="D"){ echo 'DEPOSITO'; }elseif($mostrar["tipopago"]=="T"){ echo 'TARJETA'; } ?></td>
                        <td class="center"><?php echo $mostrar["datosadicionales"] ?></td>
                        <td class="center"><?php echo $mostrar["nombreusuario"] ?></td>
                        <!--td class="center"><?php echo number_format($mostrar["saldo"],2,'.','') ?></td-->
                        <!--td class="center"><?php echo $mostrar["fecha_consumo"] ?></td-->
                        <!--td class="center" style="cursor: pointer" onclick="detalleMovimiento(<?php echo $mostrar["idmovimiento"]?>)"><?php echo $mostrar["comanda"] ?></td-->
                        <td class="center"><?php if($mostrar["estado"]=="N") {echo "PENDIENTE";}elseif($mostrar["estado"]=="C"){ echo 'CONSUMIDO'; }elseif($mostrar["estado"]=="E"){ echo 'ENTREGADO'; } ?></td>
                        <td class="center">
                            <div class="center">
                                <?php if($mostrar["estado"]!="E"){?>
                                <div class="col s3"><button type="button" class="btn btn-floating green tooltipped" data-position="bottom" data-delay="50" data-tooltip="ENTREGAR" onclick="entregar('<?php echo $mostrar["idpagoanticipado"];?>');"><i class="material-icons">check</i></button></div>
                                <div class="col s3"><button type="button" class="btn btn-floating blue tooltipped" data-position="bottom" data-delay="50" data-tooltip="PAGAR" onclick="javascript:setRun('vista/mantPagoAnticipado', 'accion=PAGAR&Id=<?php echo $mostrar["idpagoanticipado"];?>&id_clase=53', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();$('#tablaActual').hide();"><i class="material-icons">open_in_new</i></button></div>
                                <?php 
                                    $rst=$objProducto->obtenerDataSQL("select * from movimientohoy where idconceptopago=27 and idmovimientoref=".$mostrar["idpagoanticipado"]." and estado='N'");
                                    if($rst->rowCount()>0){
                                ?>
                                <div class="col s3"><button type="button" class="btn btn-floating blue tooltipped" data-position="bottom" data-delay="50" data-tooltip="ACTUALIZAR" onclick="javascript:setRun('vista/mantPagoAnticipado', 'accion=ACTUALIZAR&Id=<?php echo $mostrar["idpagoanticipado"];?>&id_clase=53', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();$('#tablaActual').hide();"><i class="material-icons">edit</i></button></div>
                                <?php
                                        } 
                                    }?>
                                <?php if($mostrar["estado"]=="N"){?>
                                <div class="col s3"><button type="button" class="btn btn-floating red tooltipped" data-position="bottom" data-delay="50" data-tooltip="ELIMINAR" onclick="javascript:consultaControlador('<?php echo $mostrar["idpagoanticipado"];?>','ELIMINAR','¿ELIMINA EL PAGO ANTICIPADO?');"><i class="material-icons">clear</i></button></div>
                                <?php }?>
                            </div>
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