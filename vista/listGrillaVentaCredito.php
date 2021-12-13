<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT vc.*, now()::date-vc.fecha_consumo as transcurridos, psm.nombres, psm.apellidos, mv.numero as comanda, mv.idmovimiento, us.nombreusuario FROM ventacredito vc
    LEFT JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) mv ON vc.idmovimiento = mv.idmovimiento
    LEFT JOIN persona ps ON ps.idpersona = vc.idcliente
    LEFT JOIN personamaestro psm ON psm.idpersonamaestro = ps.idpersonamaestro
    LEFT JOIN usuario us ON us.idusuario = vc.idusuario 
    WHERE vc.estado <> 'A'";
$fechaInicio = $_GET["txtFechaInicio"];
$fechaFin = $_GET["txtFechaFin"];
$usuario = $_GET["cboUsuario"];
$estado = $_GET["txtEstado"];
$propietario = $_GET["txtBuscar_Apellido_Nombre"];
$sql.=" AND (psm.nombres LIKE '%$propietario%' OR psm.apellidos LIKE '%$propietario%')";
if(!empty($fechaInicio)){
    $sql.=" AND vc.fecha_consumo >= '$fechaInicio'";
}
if(!empty($fechaFin)){
    $sql.=" AND vc.fecha_consumo <= '$fechaFin'";
}
if($usuario>0){
    $sql.=" AND us.idusuario = $usuario";
}
if($estado!="T"){
    $sql.=" AND vc.estado = '$estado'";
}
$sql.=" ORDER BY vc.fecha_consumo ASC";
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
            <table class="bordered highlight" >
                <thead>
                    <tr>
                        <th class="center">CODIGO</th>
                        <th class="center">PROPIETARIO</th>
                        <th class="center">VALOR</th>
                        <th class="center">PLAZO</th>
                        <th class="center">FECHA CONSUMO</th>
                        <th class="center">USUARIO</th>
                        <th class="center">COMANDA</th>
                        <th class="center">ESTADO</th>
                        <th class="center">OPERACION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { $porcentaje = round($mostrar["transcurridos"]*100/$mostrar["plazo"]);?>
                    <tr class="<?php if($mostrar["estado"]=="N"){if($porcentaje<=70){ echo 'green';}elseif($porcentaje<=95){ echo "yellow";}else{ echo "red";} ;echo " ".$porcentaje;}?>">
                        <td class="center"><?php echo str_pad($mostrar["idventacredito"],6,"0",STR_PAD_LEFT); ?></td>
                        <td class="center"><?php echo $mostrar["apellidos"]." ".$mostrar["nombres"]; ?></td>
                        <td class="center">S/. <?php echo number_format($mostrar["total"],2) ?></td>
                        <td class="center"><?php echo $mostrar["plazo"] ?></td>
                        <td class="center"><?php echo $mostrar["fecha_consumo"] ?></td>
                        <td class="center"><?php echo $mostrar["nombreusuario"] ?></td>
                        <td class="center" style="cursor: pointer" onclick="detalleMovimiento(<?php echo $mostrar["idmovimiento"]?>)"><?php echo $mostrar["comanda"] ?></td>
                        <td class="center"><?php if($mostrar["estado"]=="N") {echo "PENDIENTE";}elseif($mostrar["estado"]=="P"){ echo 'PAGADO'; } ?></td>
                        <td class="center">
                            <?php if($mostrar["estado"]=="N") {?>
                            <div class="center">
                                <div class="col s4"><button type="button" class="btn btn-floating blue tooltipped" data-position="bottom" data-delay="50" data-tooltip="EDITAR" onclick="javascript:setRun('vista/mantValeConsumo', 'accion=ACTUALIZAR&Id=<?php echo $mostrar["idvale"];?>&id_clase=53', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();$('#tablaActual').hide();"><i class="material-icons">edit</i></button></div>
                                <div class="col s4"><button type="button" class="btn btn-floating pink tooltipped" data-position="bottom" data-delay="50" data-tooltip="PAGAR" onclick="javascript:generarComprobante('<?php echo $mostrar["idmovimiento"];?>','CREDITO <?php echo str_pad($mostrar["idventacredito"],6,"0",STR_PAD_LEFT); ?>');"><i class="material-icons">attach_money</i></button></div>
                                <div class="col s4"><button type="button" class="btn btn-floating red tooltipped" data-position="bottom" data-delay="50" data-tooltip="ELIMINAR" onclick="javascript:consultaControlador('<?php echo $mostrar["idvale"];?>','ELIMINAR','¿CAMBIAR EL ESTADO?');"><i class="material-icons">clear</i></button></div>
                            </div>
                            <?php }?>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
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