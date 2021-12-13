<?php
date_default_timezone_set("America/Lima");
session_start();
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
include_once '../modelo/mdlSerie.php';
$mdlSerie = new mdlSerie();
$serieHoy = date("Ymd");
$series = $mdlSerie->verSeries3($_SESSION["empresa"]["id_empresa"], "E",$serieHoy);
$correlativo = $series["correlativo_serie"];
$correlativo = $correlativo + 1;
$correlativo = str_pad($correlativo, 5, "0", STR_PAD_LEFT);
?>
<section>
    <div class="row titulo">
        <h4 class="center">REGISTRAR COMUNICACION DE BAJAS</h4>
    </div>
    <div class="row contenido">
        <form id="frmRegistrarFactura" action="controlador/contComprobante.php?funcion=enviarResumenBoletasAnuladas">
            <input type="hidden" name="tipoResumen" value="3">
            <input type="hidden" name="tipoDocumento" value="E">
            <input type="hidden" name="moneda" value="PEN">
            <input type="hidden" value="false" id="aplicaDescuento" name="aplicaDescuento">
            <div class="col s10 offset-s1 Factura">
                <div class="row">
                    <div class="col s8 center">
                        <h6 class="razonSocialCabecera"><?php echo $_SESSION["empresa"]["nombre_empresa"];?></h6>
                        <h6 class="dirCabecera"><?php echo $_SESSION["empresa"]["domiciliofiscal_empresa"];?> - <?php echo $_SESSION["empresa"]["distrito_direccion"];?> - <?php echo $_SESSION["empresa"]["provincia_direccion"];?> - <?php echo $_SESSION["empresa"]["departamento_direccion"];?></h6>
                        <h6 class="fechaCabecera"><?php echo $_SESSION["empresa"]["departamento_direccion"];?>, <?php echo date("d");?> de <?php echo $meses[date('n')-1]?> del <?php echo date("Y");?></h6>
                    </div>
                    <div class="col s4 center datosEmisor">
                        <h6 class="rucCabecera">RUC.: <?php echo $_SESSION["empresa"]["ruc_empresa"];?></h6>
                        <h6 class="tipoDocCabecera">COMUNICACION DE BAJAS</h6>
                        <h6 class="numCabecera valign-wrapper">
                            <div class="col s1 input-field inline">N°&nbsp;</div>
                            <div class="col s6 input-field inline" style="padding-left: 10px; padding-right: 0px;">
                                <input type="hidden" name="id_serie" value="">
                                <input style="text-align: center;" type="text" name="serie" value="<?php echo $serieHoy;?>" readonly="">
                            </div>
                            <div class="col s1 input-field inline">-</div>
                            <div class="col s4 input-field inline" style="padding-left: 0px; padding-right: 0px;">
                                <input class="center" id="numfac" name="numfac" type="text" value="<?php echo $correlativo;?>" readonly="">
                            </div>
                        </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col s3">
                        <div class="input-field">
                            <input id="fecref" name="fecref" type="date" value="<?php echo date("Y-m-d");?>">
                            <label for="fecref" class="active">FECHA DE EMISION DE DOCUMENTOS</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="striped centered bordered detallesCuerpo">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>TIPO</th>
                                <th>NUMERACION</th>
                                <th>MOTIVO</th>
                                <th><i class="material-icons" onclick="/*agregarNuevoDetalle();*/anadirDetalle();">add</i></th>
                            </tr>
                        </thead>
                        <tbody id="contenidoDetallesCuerpo">
                            <!--tr id="fila_1">
                                <input class="unidadCuerpo" type="hidden" value="" name="unidades[]">
                                <input class="tipoigvCuerpo" type="hidden" value="" name="tipoigv[]">
                                <input class="descuentoxitemCuerpo" type="hidden" value="" name="descuentoxitem[]">
                                <td>1</td>
                                <td class="tipoCuerpo"><select name="tipodetalle[]"><option value="01">FACTURA</option><option value="03">BOLETA</option></select></td>
                                <td class="serieCuerpo"><input type="text" name="serieDetalle[]"></td>
                                <td>-</td>
                                <td><div id="numeracion_1"></div></td>
                                <td class="correlativoCuerpo"><input type="number" step="1" min="0" name="correlativo[]"></td>
                                <td class="dniCuerpo"><input type="text" name="dni[]"></td>
                                
                                <td><i class="material-icons" onclick="$('#fila_1').remove();">clear</i></td>
                            </tr-->
                        </tbody>
                    </table>
                </div>
                <div class="row" hidden="">
                    <div class="col s6 center Documentos">
                        <h6 class="">DOCUMENTOS ANEXOS</h6>
                        <table class="striped centered bordered detallesDocumentos">
                            <thead>
                                <tr>
                                    <th>CODIGO</th>
                                    <th>TIPO DOCUMENTO</th>
                                    <th>NUMERO</th>
                                    <th><i class="material-icons">add</i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09</td>
                                    <td>GUIA REMISION</td>
                                    <td>0001-8845522</td>
                                    <td><i class="material-icons">clear</i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col s6 offset-s6 center Opciones">
                        <div class="col s12 center">
                            <h6>OTRAS OPCIONES</h6>
                        </div>
                        <div class="col s6">
                            <button type="button" class="btn green" onclick="descuentoGlobal(true);">DESCUENTO</button>
                        </div>
                        <div class="col s6" hidden="">
                            <button type="button" class="btn green">PERCEPCIÓN</button>
                        </div>
                    </div>
                </div>
                <div class="row right-align" id="divGuardar">
                    <button type="button" class="btn btn-submit" onclick="enviarForm('frmRegistrarFactura','divGuardar')">GUARDAR<i class="material-icons left white-text">save</i></button>
                </div>
            </div>
        </form>
    </div>
    <div id="modDetalleFactura" class="modal modal-fixed-footer">
        <div>
            <button class="btn btn-floating right red modal-action modal-close boton-close valign-wrapper"><i class="material-icons tiny">clear</i></button>
        </div>
        <div class="modal-content">
            <div class="row Header">
                <h2>NUEVO DETALLE DE FACTURA</h2>
            </div>
            <div class="row Content">
                <div class="col s10 offset-s1 white">
                    <div class="col s12 input-field">
                        <select id="unidadMedida">
                            <option value="NIU" selected="">UNIDADES</option>
                        </select>
                        <label for="unidadMedida">UNIDAD DE MEDIDA</label>
                    </div>
                    <div class="col s12 input-field">
                        <select id="tipoDetalle">
                            <option value="V" selected="">DETALLE DE VENTA</option>
                            <option value="R">REGALO O BONIFICACION</option>
                        </select>
                        <label for="tipoDetalle">TIPO DE DETALLE</label>
                    </div>
                    <div class="col s12 input-field">
                        <select id="afectacionIGV">
                            <option value="10" selected="">GRAVADO - OPERACIÓN ONEROSA</option>
                            <option value="20">EXONERADO - OPERACIÓN ONEROSA</option>
                            <option value="30">INAFECTO - OPERACIÓN ONEROSA</option>
                        </select>
                        <label for="afectacionIGV">AFECTACIÓN DEL IGV</label>
                    </div>
                    <div class="col s12 input-field">
                        <input type="number" step="0.01" id="descuentoDetalle" value="0.00">
                        <label for="descuentoDetalle">DESCUENTO POR VALOR UNITARIO (%)</label>
                    </div>
                    <!--div class="col s12 input-field">
                        <input type="number" step="0.01" id="tasaISC" value="0.00">
                        <label for="tasaISC">TASA DE ISC (%)</label>
                    </div>
                    <div class="col s12 input-field">
                        <select id="aplicacionISC">
                            <option value="1" selected="">VALOR UNITARIO</option>
                            <option value="2">PRECIO UNITARIO</option>
                            <option value="3">PRECIO SUGERIDO</option>
                        </select>
                        <label for="aplicacionISC">APLICACION DEL ISC</label>
                    </div-->
                    <div class="col s12 input-field right-align" style="padding-bottom: 20px;">
                        <button type="button" class="btn btn-submit" onclick="anadirDetalle();">AÑADIR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    //agregarRuta(true,"Registrar Comunicacion Bajas","frmComunicacionBajasB.php");
    //agregarOpciones([["../vista/frmEmpresa.php?modo=N","group_add","AGREGAR NUEVO <b>USUARIO</b>"]]);
    //agregarOpciones([]);
    
    var firstMenu = $("#slide-out").find("a.pointer")[0];
    $("select").material_select();
    $(".modal").modal();
    //$("#modDetalleFactura").modal('open');
    function agregarNuevoDetalle(){
        $("#modDetalleFactura").modal('open');
    }
    var numero = 0;
    
    function activarFecha(){
        var items = $("#contenidoDetallesCuerpo").children("tr").length;
        if(items>0){
            $("#fecref").prop("readonly",true);
        }else{
            $("#fecref").prop("readonly",false);
        }
    }
    
    function anadirDetalle(){
        numero = numero + 1;
        var unidadMedida = $("#unidadMedida").val();
        var unidad = $("#unidadMedida option:selected").html();
        var tipoDetalle = $("#tipoDetalle").val();
        var afectacionIGV = $("#afectacionIGV").val();
        var descuentoDetalle = $("#descuentoDetalle").val();
        var html = '<tr id="fila_'+numero+'"><input class="unidadCuerpo" type="hidden" value="'+unidadMedida+'" name="unidades[]"><input class="tipodocCuerpo" type="hidden" value="03" name="tipodoc[]"><input class="tipoigvCuerpo" type="hidden" value="'+afectacionIGV+'" name="tipoigv[]"><input class="tipodetalleCuerpo" type="hidden" value="'+tipoDetalle+'" name="tipodetalle[]"><input class="descuentoxitemCuerpo" type="hidden" value="'+descuentoDetalle+'" name="descuentoxitem[]"><td>'+numero+'</td><td class="codigoCuerpo"><select name="tipodocumento[]" id="slcTipoDoc_'+numero+'" onchange="actualizarDocumento('+numero+');"><!--option value="01">FACTURA</option--><option value="03">BOLETA</option></select></td><input type="hidden" name="idDetalle[]" id="idDetalle_'+numero+'"><input type="hidden" name="idDetalleServidor[]" id="idDetalleServidor_'+numero+'"><input type="hidden" id="serieDetalle_'+numero+'" name="serieDetalle[]" placeholder="001" maxlength="3"><input type="hidden" id="corrDetalle_'+numero+'" name="correlativo[]" maxlength="8" placeholder="00001595"><input type="hidden" id="dniDetalle_'+numero+'" name="dni[]"><input type="hidden" id="totalDetalle_'+numero+'" value="0" name="total[]"><td><div id="divNumeracion_'+numero+'"></div></td><td class="codigoCuerpo"><input type="text" name="motivo[]" placeholder=""></td><td><i class="material-icons" onclick="eliminarDetalle(\'fila_'+numero+'\');">clear</i></td></tr>';
        $("#contenidoDetallesCuerpo").append(html);
        $("select").material_select();
        actualizarNumeroItem();
        actualizarDocumento(numero);
    }
    
    function actualizarDocumento(fila){
        var fecharef = $("#fecref").val();
        var tipoDoc = $("#slcTipoDoc_"+fila).val();
        autocompleteAJAX_JSON("controlador/contComprobante.php","funcion=listarComunicacion&estado=C,M&id_empresa=<?php echo $_SESSION["empresa"]["id_empresa"];?>&fecini="+fecharef+"&tipodoc="+tipoDoc,"",["serie + '|' + '"+numero+"'","correlativo","id_solicitud","doc_cliente","total_doc","id_solicitud_servidor"],"numeracion","divNumeracion_"+numero,"numeracion[]","numeracion_"+numero,seleccionarDocumento,"","autocomplete-content3","");
    }
    
    function seleccionarDocumento(iddocumento){
        var dataMostrar = iddocumento;
        dataMostrar = dataMostrar.toString().split("|");
        var fila = dataMostrar[1];
        var serie = dataMostrar[0];
        var corr = dataMostrar[2];
        var idsol = dataMostrar[3];
        var docCli = dataMostrar[4];
        var total = dataMostrar[5];
        var idsol2 = dataMostrar[6];
        $("#serieDetalle_"+fila).val(serie);
        $("#corrDetalle_"+fila).val(corr);
        $("#idDetalle_"+fila).val(idsol);
        $("#dniDetalle_"+fila).val(docCli);
        $("#totalDetalle_"+fila).val(total);
        $("#idDetalleServidor_"+fila).val(idsol2);
        $("#numeracion_"+fila).attr("last_selected",iddocumento);
        $("#numeracion_"+fila).prop("readonly",true);
        $('#divNumeracion_'+fila).addClass("select-wrapper");
        $("#numeracion_"+fila).before('<span style="margin-top: 10px;" class="caret pointer" onclick="$(\'#serieDetalle_'+fila+'\').val(\'\');$(\'#corrDetalle_'+fila+'\').val(\'\');$(\'#idDetalle_'+fila+'\').val(\'\');$(\'#dniDetalle_'+fila+'\').val(\'\');$(\'#totalDetalle_'+fila+'\').val(\'\');limpiarAutocomplete(\'numeracion_'+fila+'\',\'id_numeracion\',$(this));"><i class="material-icons red-text" style="">clear</i></span>');
        //calcularSubtotal($("#iptPrecio_"+fila));
    }
    
    function eliminarDetalle(fila){
        $('#'+fila).remove();
        //var numero = $("#contenidoDetallesCuerpo").children("tr").length;
        actualizarNumeroItem();
        calcularTotalDescuento();
    }
    function actualizarNumeroItem(){
        $("#contenidoDetallesCuerpo").children("tr").each(function (key,val){
            var columnas = $(val).children("td");
            var columna = columnas[0];
            $(columna).html(key+1);
        });
        activarFecha();
    }
    function calcularSubtotal(input){
        var TR = $(input).parents("tr");
        var tipoIGV = $(TR).find(".tipoigvCuerpo").val();
        var tipodetalle = $(TR).find(".tipodetalleCuerpo").val();
        var descuentoxitem = $(TR).find(".descuentoxitemCuerpo").val();
        var cantidad = $(TR).find(".cantidadCuerpo").children("input").val();
        if(cantidad.toString().trim().length==0){
            cantidad = 0.0;
        }
        var precio = $(TR).find(".precioCuerpo").children("input").val();
        if(precio.toString().trim().length==0){
            precio = 0.0;
        }
        var valorunitario = parseFloat(precio)/1.18;
        if(tipoIGV >= 20 && tipoIGV<30){
            valorunitario = parseFloat(precio);
        }
        var valorventabruto = parseFloat(valorunitario*cantidad);
        var descuento = 0.0;
        if(descuentoxitem > 0){
            descuento = (parseFloat(valorventabruto * descuentoxitem))/100;
        }
        var valorventaxitem = parseFloat(valorventabruto - descuento);
        var igv = 0.0;
        if(tipoIGV >= 10 && tipoIGV<20){
            igv = parseFloat(0.18 * valorventaxitem);
        }
        var subtotal = 0.0;
        if(tipodetalle=="V"){
            subtotal = parseFloat(valorventaxitem + igv);
        }
        console.log(valorunitario,valorventabruto,descuento,tipoIGV,igv,valorventaxitem);
        $(TR).find(".subtotalCuerpo").html(subtotal.toFixed(2));
        //calcularTotalDescuento();
    }
    function calcularTotalDescuento(){
        calcularDescuentoGlobal();
        calcularTotal();
    }
    function calcularTotal(){
        var total = 0.0;
        $("#contenidoDetallesCuerpo").find(".subtotalCuerpo").each(function (key,val){
            var subtotal = $(val).html();
            var number = parseFloat(subtotal);
            total = total + number;
        });
        var descuentoglobal = $("#totalDescuentoGlobal").html();
        total = total - descuentoglobal;
        $("#totalFactura").html(total.toFixed(2));
        var moneda = $("#moneda").val();
        if(moneda=="PEN"){
            moneda = "NUEVOS SOLES";
        }else if(moneda=="USD"){
            moneda = "DOLARES";
        }
        $("#letrasTotal").html(NumeroALetras(total.toFixed(2),moneda));
    }
    function descuentoGlobal(valor){
        $("#aplicaDescuento").val(valor);
        if(valor){
            $("#trDescuento").show();
            $("#descuentoglobal").val("0.0");
            $("#totalDescuentoGlobal").html("0.0");
            $("#descuentoglobal").focus();
        }else{
            $("#trDescuento").hide();
            $("#descuentoglobal").val("0.0");
            $("#totalDescuentoGlobal").html("0.0");
        }
    }
    function calcularDescuentoGlobal(){
        var total = 0.0;
        $("#contenidoDetallesCuerpo").find(".subtotalCuerpo").each(function (key,val){
            var subtotal = $(val).html();
            var number = parseFloat(subtotal);
            total = total + number;
        });
        var porcentaje = $("#descuentoglobal").val();
        if(porcentaje.toString().trim().length==0){
            porcentaje = 0.0;
        }
        var descuento = parseFloat(porcentaje*total) / 100;
        $("#totalDescuentoGlobal").html(descuento.toFixed(2));
    }
    descuentoGlobal(false);
    //$(firstMenu).trigger("click");
</script>
