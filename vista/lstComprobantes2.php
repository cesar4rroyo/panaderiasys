<?php 
session_start();
date_default_timezone_set("America/Lima");
include '../modelo/mdlSolicitud.php';
$mdlSolicitud = new mdlSolicitud();
$idsolicitud = $_GET['idsolicitud'];
$solicitud = $mdlSolicitud->verSolicitud($idsolicitud);
$solicitud["data_solicitud"] = json_decode($solicitud["data_solicitud"],true);
?>
<div class="col s12 container Mesas" >
    <section>
        <div class="row contenido">
            <div class="row">
                <form class="frmFiltros" id="frmFiltros2" action="controlador/contComprobante.php?funcion=tablaComprobantes2" onsubmit="return false;">
                    <input name="npag" value="1" type="hidden">
                    <div class="form-group">
                        
                            <div class="">
                                <h6><b> TIPO: </b>
                                    <?php if($solicitud["tipo_documento"]=="E"){
                                            if($solicitud["data_solicitud"]["tiporesumen"]=="1"){
                                               echo "DECLARACION DE BOLETAS";
                                            }elseif($solicitud["data_solicitud"]["tiporesumen"]=="3"){
                                                echo "ANULACION DE BOLETAS";
                                            }elseif($solicitud["data_solicitud"]["tiporesumen"]=="2"){
                                                echo "MODIFICACION DE BOLETAS";
                                            }
                                        }else{
                                            echo ($solicitud["tipo_documento"]=="A")?("COMUNICACION DE BAJAS"):(($solicitud["tipo_documento"]=="B")?("BOLETA"):(($solicitud["tipo_documento"]=="C")?("NOTA DE CREDITO"):(($solicitud["tipo_documento"]=="D")?("NOTA DE DEBITO"):(($solicitud["tipo_documento"]=="E")?("RESUMEN DE BOLETAS"):(($solicitud["tipo_documento"]=="F")?("FACTURA"):("-"))))));
                                        } ?></h6>
                            
                                <h6> <b>NÚMERO:</b> <?php echo $solicitud["serie"]."-".str_pad($solicitud["correlativo"],8,"0",STR_PAD_LEFT); ?></h6>
                                <h6> <b>TICKET:</b> <?php echo empty($solicitud["ticket_solicitud"])?"-":$solicitud["ticket_solicitud"]; ?></h6>
                            </div>
                            <div class="">
                                <input type="hidden" name="id_solicitud" value="<?php echo $_GET["idsolicitud"];?>">
                            </div>

                    </div>
                </form>
            </div>
            <div class="cols s12">
                <table class="centered striped bordered highlight responsive-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>TIPO</th>
                            <th>NUMERACION</th>
                            <th>DNI/RUC</th>
                            <th>NOMBRE O RAZON SOCIAL</th>
                            <th>TOTAL</th>
                            <!--th>FECHA DE SOLICITUD</th-->
                            <th>FECHA DE EMISION</th>
                            <th>FECHA DE ENVIO</th>
                            <th>FECHA DE RESPUESTA</th>
                            <th>ESTADO</th>
                            
                        </tr>
                    </thead>
                    <tbody id="tblComprobantes2">
                    </tbody>
                </table>
            </div>
            <div class="row" id="divPaginacion2">
            </div>
        </div>
    </section>
</div>
<script>
    $("select").material_select();
    agregarOpciones([]);
    //agregarOpciones([["../vista/frmComprobante.php?modo=N","add","AGREGAR NUEVO <b>MENU</b>"]]);
    function buscar3(){
        cargarTabla_JSON('frmFiltros2','tblComprobantes2','divPaginacion2');
    }
    

    activeLabels();
    buscar3();
</script>