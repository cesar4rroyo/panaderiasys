<?php
require('../modelo/clsMovimiento.php');
$id_clase = $_GET["id_clase"];
$nro_reg = 0;
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
	$nro_hoja = 1;
}
$order = $_GET["order"];
if(!$order){
	$order="1";
}
$by = $_GET["by"];
if(!$by){
	$by="1";
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8"/>
</head>
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function aceptarenvio(idalmacen){
    g_ajaxGrabar.setURL("controlador/contAlmacen.php?ajax=true");
    g_ajaxGrabar.setRequestMethod("POST");	
    g_ajaxGrabar.setParameter("accion","ACEPTARENVIO");
    g_ajaxGrabar.setParameter("idalmacen",idalmacen);
    g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			alert(text);
			setRun('vista/listAlmacen','&id_clase=41','frame','carga','imgloading');
	};
	g_ajaxGrabar.request();
	loading(true, "loading", "frame", "line.gif",true);   
}
function verPDF(idalmacen,idsucursal){
    window.open('vista/reportes/ReporteAlmacen.php?idmovimiento='+idalmacen+'&idsucursal='+idsucursal,'_blank');
}
</script>
<body>
<?php
$objFiltro = new clsMovimiento($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$rstMovimiento = $objFiltro->obtenerTabla();
if(is_string($rstMovimiento)){
	echo "<td colspan=100>Error al Obtener datos de Perfil</td></tr><tr><td colspan=100>".$rstMovimiento."</td>";
}else{
	$datoMovimiento = $rstMovimiento->fetchObject();
}
$rst=$objFiltro->buscarMovimiento($_GET['idalmacen'], 3, '');
$detalle = $rst->fetchObject();
?>
<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row">
        <div id="divdiagramaMesa" class="col s12">
            <div class="row">
                <div class="col s12">
                    <div id="cargagrilla"></div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div id="grilla">
                        <div class="row">
                            <div class="col s12">
                                <h5 class="center blue lighten-4 blue-text text-darken-4">Datos del Documento de Almac&eacute;n</h5>
                            </div>
                            <div class="col s12 m6 l4">
                                <div class="col s6">Tipo</div>
                                <div class="col s6"><?php if($detalle->idtipodocumento==7) echo 'Ingreso'; else echo 'Salida';?></div>
                            </div>
                            <div class="col s12 m6 l4">
                                <div class="col s6">Numero</div>
                                <div class="col s6"><?php echo $detalle->numero;?></div>
                            </div>
                            <div class="col s12 m6 l4">
                                <div class="col s6">Fecha</div>
                                <div class="col s6"><?php echo $detalle->fecha;?></div>
                            </div>
                            <div class="col s12 m6 l4">
                                <div class="col s6">Persona</div>
                                <div class="col s6"><?php echo $detalle->persona;?></div>
                            </div>
                            <div class="col s12 m6 l4">
                                <div class="col s6">Responsable</div>
                                <div class="col s6"><?php echo $detalle->responsable;?></div>
                            </div>
                        </div>
                        <div class="row">
                            <h5 class="center blue lighten-4 blue-text text-darken-4">Detalle del Documento de Almac&eacute;n</h5>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="center">C&oacute;digo</th>
                                        <th class="center">Producto</th>
                                        <th class="center">Unidad</th>
                                        <th class="center">Cantidad</th>
                                        <th class="center">Precio Compra</th>	
                                        <th class="center">Precio Venta</th>	
                                        <th class="center">SubTotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rst2=$objFiltro->buscarDetalleProducto($_GET['idalmacen'],"h");
                                    $c=0;
                                    $sum=0;
                                    while($dato=$rst2->fetchObject()){?>
                                    <tr class="hoverable">
                                        <td class="center"><?php echo $dato->codigo;?></td>
                                        <td class="center"><?php echo $dato->producto;?></td>
                                        <td class="center"><?php echo $dato->unidad;?></td>
                                        <td align="right" ><?php echo number_format($dato->cantidad,2,'.',' ');?></td>
                                        <td align="right" ><?php echo number_format($dato->preciocompra,2,'.',' ');?></td>
                                        <td align="right" ><?php echo number_format($dato->precioventa,2,'.',' ');?></td>
                                        <td align="right" ><?php echo number_format($dato->cantidad*$dato->precioventa,2,'.',' ');?></td>
                                        <?php $sum+=$dato->cantidad*$dato->precioventa;?>
                                    </tr>
                                    <?php }?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="right-align">Total</th>
                                        <th class="right-align"><?php echo number_format($sum,2,'.',' ');?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div hidden="" id="tablaContenido">
                            <table border="1">
                                <thead>
                                    <tr>
                                        <th colspan="7">Datos del Documento de Almacen</th>
                                    </tr>
                                    <tr>
                                        <th>Tipo</th>
                                        <td><?php if($detalle->idtipodocumento==7) echo 'Ingreso'; else echo 'Salida';?></td>
                                        <th>Numero</th>
                                        <td><?php echo $detalle->numero;?></td>
                                        <th>Fecha</th>
                                        <td><?php echo $detalle->fecha;?></td>
                                    </tr>
                                    <tr>
                                        <th>Persona</th>
                                        <td colspan="3"><?php echo $detalle->persona;?></td>
                                        <th>Responsable</th>
                                        <td><?php echo $detalle->responsable;?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="7">Detalle del Documento de Almacen</th>
                                    </tr>
                                    <tr>
                                        <th class="center">Codigo</th>
                                        <th class="center">Producto</th>
                                        <th class="center">Unidad</th>
                                        <th class="center">Cantidad</th>
                                        <th class="center">Precio Compra</th>   
                                        <th class="center">Precio Venta</th>    
                                        <th class="center">SubTotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rst2=$objFiltro->buscarDetalleProducto($_GET['idalmacen'],"h");
                                    $c=0;
                                    $sum=0;
                                    while($dato=$rst2->fetchObject()){?>
                                    <tr class="hoverable">
                                        <td class="center"><?php echo $dato->codigo;?></td>
                                        <td class="center"><?php echo $dato->producto;?></td>
                                        <td class="center"><?php echo $dato->unidad;?></td>
                                        <td align="right" ><?php echo number_format($dato->cantidad,2,'.',' ');?></td>
                                        <td align="right" ><?php echo number_format($dato->preciocompra,2,'.',' ');?></td>
                                        <td align="right" ><?php echo number_format($dato->precioventa,2,'.',' ');?></td>
                                        <td align="right" ><?php echo number_format($dato->cantidad*$dato->precioventa,2,'.',' ');?></td>
                                        <?php $sum+=$dato->cantidad*$dato->precioventa;?>
                                    </tr>
                                    <?php }?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="right-align">Total</th>
                                        <th class="right-align"><?php echo number_format($sum,2,'.',' ');?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col s12">
                                <textarea id="txtComentario" name="txtComentario" class="materialize-textarea" readonly=""><?php echo $detalle->comentario;?></textarea>
                                <label for="txtComentario">Comentario</label>
                            </div>
                            <div class="col s2">
                                <button type="button" class="btn right green darken-4" onclick="javascript:verPDF(<?php echo $_GET["idalmacen"]?>,<?php echo $_SESSION["R_IdSucursal"]?>)">PDF<i class="material-icons right">content_paste</i></button>
                            </div>
                            <div class="col s12">
                                <button type="button" class="btn-large right green" style="margin-right: 10px;" onclick="window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#tablaContenido').html()));">EXCEL<i class="material-icons right">description</i></button>
                            </div>
                            <?php if($detalle->situacion=='E' && $detalle->idtipodocumento==7){ ?>
                            <div class="col s12">
                                <button type="button" class="btn right amber darken-4" onclick="javascript:aceptarenvio(<?php echo $_GET["idalmacen"]?>)">CARGAR A STOCK<i class="material-icons right">file_upload</i></button>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>>