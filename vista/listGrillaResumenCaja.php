<?php
session_start();
//Nombre y Codigo de la Clase a Ejecutar
include '../modelo/clsProducto.php';
$objProducto = new clsProducto(19, $_SESSION['R_IdSucursal'], $_SESSION['R_NombreUsuario'], $_SESSION['R_Clave']);
$sql = "SELECT mv.*, us.nombreusuario, (select fecha from movimiento where idconceptopago=2 and idmovimiento>mv.idmovimiento order by idmovimiento asc limit 1) as fechacierre,  (select total from movimiento where idconceptopago=2 and idmovimiento>mv.idmovimiento order by idmovimiento asc limit 1) as montocierre,(select idmovimiento from movimiento where idconceptopago=2 and idmovimiento>mv.idmovimiento order by idmovimiento asc limit 1) as idcierre
FROM (SELECT * FROM movimiento) mv 
LEFT JOIN usuario us ON us.idusuario = mv.idusuario 
WHERE TRUE AND mv.idconceptopago=1 AND mv.idsucursal = ".$_SESSION['R_IdSucursal'];
$fechaInicio = $_GET["txtFechaInicio"]." ".$_GET["txtHoraInicio"];
$fechaFin = $_GET["txtFechaFin"]." ".$_GET["txtHoraFin"];
$usuario = $_GET["cboUsuario"];//UNION SELECT * FROM movimientohoy
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
                        <th class="center">FECHA</th>
                        <th class="center">HORA INICIO</th>
                        <th class="center">HORA FIN</th>
                        <th class="center">INICIO</th>
                        <th class="center">CIERRE</th>
                        <th class="center">DETALLE</th>
                        <th class="center">PDF</th>
                        <th class="center">TICKET</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mostrar = $rs->fetch()) { ?>
                    <tr>
                        <td class="center"><?php echo date("d/m/Y",strtotime($mostrar["fecha"])); ?></td>
                        <td class="center"><?php echo date("H:i:s",strtotime($mostrar["fecha"])); ?></td>
                        <td class="center"><?php echo date("d/m/Y H:i:s",strtotime($mostrar["fechacierre"])); ?></td>
                        <td class="center"><?php echo $mostrar["total"] ?></td>
                        <td class="center"><?php echo $mostrar["montocierre"] ?></td>
                        <td class="center"><button type="button" class="btn-floating right btn-large tooltipped yellow darken-4" data-position="bottom" data-tooltip="Ver Detalle" data-delay="50" onclick="verDetalle('<?=date("d/m/Y H:i:s",strtotime($mostrar["fecha"]));?>','<?=date("H:i:s",strtotime($mostrar["fechacierre"]));?>',<?=$mostrar["idmovimiento"]?>,<?=$mostrar["idcierre"]?>)"><i class="material-icons -text text-darken-4">search</i></button></td>
                        <td class="center"><button type="button" title="PDF" class="btn red lighten-2" onclick="window.open('vista/reportes/ReporteCierreCajaGeneral.php?fechainicio=<?=date("Y-m-d H:i:s",strtotime($mostrar["fecha"]))?>&fechafin=<?=date("Y-m-d H:i:s",strtotime($mostrar["fecha"]))?>','_blank');"><i class="material-icons black-text">print</i></button></td>
                        <td class="center"><button type="button" title="Cierre" class="btn red lighten-2" onclick="reimprimirCierre(<?=$mostrar["idmovimiento"]?>,<?=$mostrar["idcierre"]?>);"><i class="material-icons black-text">print</i></button></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modalDetalle">
          <div id="modalDetalle" class="modal modal-fixed-footer">
            <div class="modal-content orange lighten-3">
              <div class="white" style="border-radius: 10px;">
                <div class="row">
                  <div class="col s12 center"><h4 id="tituloCaja"></h4></div>
                </div>
                <div class="row">
                  <div class="input-field inline col s12" id="divDetalleCaja">
                    
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
              <a href="#!" class="left modal-action modal-close btn red accent-1 black-text">Cerrar<i class="material-icons right">clear</i></a>
            </div>
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
function verDetalle(fechaapertura,fechacierre,idapertura,idcierre){
    $.ajax({
        type: "POST",
        url: "vista/ajaxMovCaja.php",        
        data:"accion=detalleCierreCaja&idapertura="+idapertura+"&idcierre="+idcierre,
        success: function(a) {
            $("#tituloCaja").html("Caja del "+fechaapertura+" - "+fechacierre);
            $("#divDetalleCaja").html(a);
            $("#modalDetalle").openModal('show');
            $('.tabs').tabs();
        }
    });
}
function reimprimirCierre(idapertura,idcierre){
    $.ajax({
        type: "POST",
        url: "http://localhost/lasmusas/vista/ajaxPedido.php",        
        data:"accion=reimprimir_cierre&idapertura="+idapertura+"&idcierre="+idcierre,
        success: function(a) {
            alert("Imprimiendo");
        }
    });
}

function tabResumen(idtab){
    var tabs = $(".tab");
    for(var i=0;i<tabs.length;i++){
        var tab = tabs[i];
        $(tab).removeClass("Tab-activo");
        $(tab).removeClass("Tab-inactivo");
        $(tab).addClass("Tab-inactivo");
    }
    $("#"+idtab).removeClass("Tab-inactivo");
    $("#"+idtab).addClass("Tab-activo");
}
</script>