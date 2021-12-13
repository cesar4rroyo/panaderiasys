<?php
date_default_timezone_set("America/Lima");
session_start();
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
?>
<section>
    <div class="row titulo">
        <h4 class="center">REGISTRAR RESUMEN DE FACTURAS</h4>
    </div>
    <div class="row contenido">
        <form class="frmFiltros" id="frmFiltros" action="controlador/contComprobante.php?funcion=tablaResumenes" onsubmit="return false;">
            <input name="fecini" id="fechaFiltro" type="hidden" value="<?php echo date("Y-m-d");?>">
            <input name="id_empresa" value="<?php echo $_SESSION["empresa"]["id_empresa"];?>" type="hidden">
            <input name="estado" value="'R'" type="hidden">
            <input name="tipodoc" value="'F'" type="hidden">
        </form>
        <form id="frmRegistrarFactura" action="controlador/contComprobante.php?funcion=enviarResumenFacturas">
            <input type="hidden" value="false" id="aplicaDescuento" name="aplicaDescuento">
            <div class="col s10 offset-s1 Factura">
                <div class="row">
                    <div class="col s12 center">
                        <h6 class="razonSocialCabecera"><?php echo $_SESSION["empresa"]["nombre_empresa"];?></h6>
                        <h6 class="dirCabecera"><?php echo $_SESSION["empresa"]["domiciliofiscal_empresa"];?> - <?php echo $_SESSION["empresa"]["distrito_direccion"];?> - <?php echo $_SESSION["empresa"]["provincia_direccion"];?> - <?php echo $_SESSION["empresa"]["departamento_direccion"];?></h6>
                        <h6 class="fechaCabecera"><?php echo $_SESSION["empresa"]["departamento_direccion"];?>, <?php echo date("d");?> de <?php echo $meses[date('n')-1]?> del <?php echo date("Y");?></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col s3">
                        <div class="input-field">
                            <input id="fecref" name="fecref" type="date" value="<?php echo date("Y-m-d");?>" onchange="cargarTablaResumenes();">
                            <label for="fecref" class="active">FECHA DE EMISION DE FACTURAS</label>
                        </div>
                    </div>
                    <?php /*
                    <div class="col s2" hidden="">
                        <div class="input-field">
                            <select id="moneda" name="moneda" onchange="calcularTotal();">
                                <option value="PEN" selected="">SOLES (S/.)</option>
                                <option value="USD">DOLARES ($.)</option>
                            </select>
                            <label for="moneda">MONEDA</label>
                        </div>
                    </div>
                    */?>
                    <input type="hidden" name="moneda" value="PEN">
                </div>
                <div class="row">
                    <table class="striped centered bordered detallesCuerpo">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>SERIE</th>
                                <th></th>
                                <th>CORRELATIVO</th>
                                <th>RUC</th>
                                <th>TOTAL</th>
                                <!--th><i class="material-icons" onclick="/*agregarNuevoDetalle();*/anadirDetalle();">add</i></th-->
                            </tr>
                        </thead>
                        <tbody id="contenidoDetallesCuerpo">
                            <!--tr id="fila_1">
                                <input class="unidadCuerpo" type="hidden" value="" name="unidades[]">
                                <input class="tipoigvCuerpo" type="hidden" value="" name="tipoigv[]">
                                <input class="tipodetalleCuerpo" type="hidden" value="" name="tipodetalle[]">
                                <input class="descuentoxitemCuerpo" type="hidden" value="" name="descuentoxitem[]">
                                <td>1</td>
                                <td class="serieCuerpo"><input type="text" name="serieDetalle[]"></td>
                                <td>-</td>
                                <td class="correlativoCuerpo"><input type="number" step="1" min="0" name="correlativo[]"></td>
                                <td class="dniCuerpo"><input type="text" name="dni[]"></td>
                                <td class="totalCuerpo"><input type="number" step="0.01" min="0" name="total[]" onkeyup="calcularTotal(this);"></td>
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
    //agregarRuta(true,"Registrar Resumen Facturas","frmResumenFacturas.php");
    //agregarOpciones([["../vista/frmEmpresa.php?modo=N","group_add","AGREGAR NUEVO <b>USUARIO</b>"]]);
    agregarOpciones([]);
    
    var firstMenu = $("#slide-out").find("a.pointer")[0];
    $("select").material_select();
    $(".modal").modal();
    //$("#modDetalleFactura").modal('open');
    function agregarNuevoDetalle(){
        $("#modDetalleFactura").modal('open');
    }
    var numero = 0;
    function anadirDetalle(){
        numero = numero + 1;
        var unidadMedida = $("#unidadMedida").val();
        var unidad = $("#unidadMedida option:selected").html();
        var tipoDetalle = $("#tipoDetalle").val();
        var afectacionIGV = $("#afectacionIGV").val();
        var descuentoDetalle = $("#descuentoDetalle").val();
        var html = '<tr id="fila_'+numero+'"><input class="unidadCuerpo" type="hidden" value="'+unidadMedida+'" name="unidades[]"><input class="tipoigvCuerpo" type="hidden" value="'+afectacionIGV+'" name="tipoigv[]"><input class="tipodocCuerpo" type="hidden" value="03" name="tipodoc[]"><input class="tipodetalleCuerpo" type="hidden" value="'+tipoDetalle+'" name="tipodetalle[]"><input class="descuentoxitemCuerpo" type="hidden" value="'+descuentoDetalle+'" name="descuentoxitem[]"><td>'+numero+'</td><td class="codigoCuerpo"><input type="text" name="serieDetalle[]" placeholder="001" maxlength="3"></td><td>-</td><td class="codigoCuerpo"><input type="text" name="correlativo[]" maxlength="8" placeholder="00001595"></td><td class="codigoCuerpo"><input type="text" name="dni[]" maxlength="8" placeholder="72312487"></td><td class="precioCuerpo"><input type="number" value="0" step="0.01" min="0" name="total[]" onkeyup=""></td><td><i class="material-icons" onclick="eliminarDetalle(\'fila_'+numero+'\');">clear</i></td></tr>';
        $("#contenidoDetallesCuerpo").append(html);
        actualizarNumeroItem();
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
        calcularTotalDescuento();
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
    function cargarTablaResumenes(){
        $("#fechaFiltro").val($("#fecref").val());
        cargarTabla_JSON('frmFiltros','contenidoDetallesCuerpo','divPaginacion');
    }
    cargarTablaResumenes();
    //$(firstMenu).trigger("click");
</script>
