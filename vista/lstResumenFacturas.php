<?php 
session_start();
?>
<div class="col s12 container Mesas" >
    <section>
        <div class="row titulo">
            <h4 class="center">RESUMENES DE FACTURAS</h4>
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
                            <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["empresa"]["id_empresa"];?>">
                            <div class="input-field col s12 m6 l2">
                                <input id="nombre" name="nombre" type="text" value="<?php echo $_SESSION["Filtros"]["tablaComprobantes"]["nombre"];?>">
                                <label for="nombre">Nombre</label>
                            </div>
                            <input type="hidden" value="E" name="tipodoc">
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
                            <th>FECHA DE SOLICITUD</th>
                            <th>FECHA DE ENVIO</th>
                            <th>FECHA DE RESPUESTA</th>
                            <th>ESTADO</th>
                            <th>FICHEROS</th>
                            <th>COMPROBAR</th>
                        </tr>
                    </thead>
                    <tbody id="tblComprobantes">
                    </tbody>
                </table>
            </div>
            <div class="row" id="divPaginacion">
            </div>
        </div>
    </section>
</div>
<script>
    $("select").material_select();
    //agregarRuta(true,"Resumenes de Facturas","lstResumenFacturas.php");
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
</script>