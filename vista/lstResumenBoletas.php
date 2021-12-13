<?php 
session_start();
?>
<div class="col s12 container Mesas" >
    <section>
        <div class="row titulo">
            <h4 class="center">RESUMENES DE BOLETAS</h4>
        </div>
        <div class="row contenido">
            <div class="row">
                <form class="frmFiltros" id="frmFiltros" action="controlador/contComprobante.php?funcion=tablaComprobantes" onsubmit="return false;">
                    <input name="npag" value="1" type="hidden">
                    <div class="row valign-wrapper">
                        <div class="col s12 m11 l11">
                            <div class="input-field col s12 m6 l2">
                                <input id="fecini" name="fecini" type="date" value="<?php echo date("Y-m-d");?>">
                                <label for="fecini" class="active">Desde</label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <input id="fecfin" name="fecfin" type="date" value="<?php echo date("Y-m-d");?>">
                                <label for="fecfin" class="active">Hasta</label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <input id="fecComp" name="fecComp" type="date" value="">
                                <label for="fecComp" class="active">Fecha Comprobantes</label>
                            </div>
                            <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["empresa"]["id_empresa"];?>">
                            <div class="input-field col s12 m6 l2">
                                <input id="nombre" name="nombre" type="text" value="<?php echo $_SESSION["Filtros"]["tablaComprobantes"]["nombre"];?>">
                                <label for="nombre">Nombre</label>
                            </div>
                            <input type="hidden" value="E" name="tipodoc">
                            <div class="input-field col s12 m6 l2">
                                <select name="estado" id="estado">
                                    <option value="">(TODOS)</option>
                                    <option value="T,C">AUN NO COMPROBADO</option>
                                    <option value="U">COMPROBADO Y ACEPTADO</option>
                                    <option value="V">COMPROBADO Y RECHAZADO</option>
                                    <option value="R">AUN NO ENVIADO</option>
                                </select>
                                <label for="estado">Estado</label>
                            </div>
                        </div>
                        <div class="col s12 m1 l1 center hide-on-small-only">
                            <button type="button" onclick="buscar();" class="btn btn-floating indigo darken-4 btnBuscar"><i class="material-icons yellow-text">search</i></button>
                        </div>
                    </div>
                    <div class="row hide-on-med-and-up center" style="margin-top: -20px;">
                        <button type="button" onclick="buscar();" class="btn btn-floating indigo darken-4 btnBuscar"><i class="material-icons yellow-text">search</i></button>
                    </div>
                </form>
            </div>
            <div class="row">
                <table class="centered striped bordered highlight responsive-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>TIPO</th>
                            <th>NUMERACION</th>
                            <th>USUARIO SUNAT</th>
                            <th>FECHA DE COMPROBANTES</th>
                            <th>FECHA DE SOLICITUD</th>
                            <th>FECHA DE ENVIO</th>
                            <th>FECHA DE RESPUESTA</th>
                            <th>ESTADO</th>
                            <th>COMPROBANTES</th>
                            <th>ERRORES</th>
                            
                            <th>COMPROBAR</th>
                        </tr>
                    </thead>
                    <tbody id="tblComprobantes">
                    </tbody>
                </table>
            </div>
            <div class="row" id="divPaginacion">
            </div>
            <div id="modTasasDetalle" class="modal modal-fixed-footer orange lighten-3">
                <div>
                    <button class="btn btn-floating right red modal-action modal-close boton-close valign-wrapper"><i class="material-icons tiny">clear</i></button>
                </div>
                <div class="modal-content">
                    <div class="white" style="padding: 10px;border-radius: 10px;">
                        <div class="row Header">
                            <h2 id="h2Cabecera">DETALLES DEL REGISTRO SELECCIONADO</h2>
                        </div>
                        <div class="row" id="divContenido">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</div>
<script>
    $("select").material_select();
    //agregarRuta(true,"Resumenes de Boletas","lstResumenBoletas.php");
    agregarOpciones([]);
    //agregarOpciones([["../vista/frmComprobante.php?modo=N","add","AGREGAR NUEVO <b>MENU</b>"]]);
    function buscar(){
        cargarTabla_JSON('frmFiltros','tblComprobantes','divPaginacion');
    }
    
    //selectAJAX_JSON("../controlador/contEmpresa.php","funcion=listarEmpresas","Empresa","id_empresa","nombre_empresa","divEmpresas","id_empresa","slcEmpresa","<?php echo $usuario["id_empresa"]?>","","(TODOS)","",["btnGuardar"]);
    
    function modalDocumentos(id_solicitud){
        $("#h2Cabecera").html("FICHEROS DE LA SOLICITUD");
        $("#divContenido").html(cargando);
        $("#modTasasDetalle").modal('open');
        var ajax_function = $.ajax({
            async:true,    
            cache:false,
            type: 'GET',
            url: "modalFicheros.php",
            data: "id_solicitud="+id_solicitud,
            success: function (data, textStatus, jqXHR) {
                $("#divContenido").html(data);
            },
            beforeSend: function (xhr) {
            }
        });
    }

    activeLabels();
    buscar();
    function modalComprobantes(id){
        console.log(id);
        $("#h2Cabecera").html("LISTA DE COMPROBANTES");
        $("#divContenido").html(cargando);
        $("#modTasasDetalle").openModal();
        $.ajax({
            async:true,    
            cache:false,
            type: 'GET',
            url: "vista/lstComprobantes2.php",
            data: "idsolicitud="+id,
            success: function (data, textStatus, jqXHR) {
                $("#divContenido").html(data);
            },
            beforeSend: function (xhr) {
            }
        });
    }
    function modalErrores(id){
        $("#h2Cabecera").html("LISTA DE ERRORES");
        $("#divContenido").html(cargando);
        $("#modTasasDetalle").openModal();
        $.ajax({
            async:true,    
            cache:false,
            type: 'GET',
            url: "vista/lstErrores.php",
            data: "idsolicitud="+id,
            success: function (data, textStatus, jqXHR) {
                $("#divContenido").html(data);
            },
            beforeSend: function (xhr) {
            }
        });
    }
</script>