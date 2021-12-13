<?php 
session_start();
?>
<div class="col s12 container Mesas" >
    <section>
        <div class="row titulo">
            <h4 class="center">LISTADO DE COMPROBANTES</h4>
        </div>
        <div class="row contenido">
            <div class="row">
                <form class="frmFiltros" id="frmFiltros" action="controlador/contComprobante.php?funcion=tablaComprobantes" onsubmit="return false;">
                    <input name="npag" value="1" type="hidden">
                    <div class="row valign-wrapper">
                        <div class="col s12 m11 l11">
                            <div class="input-field col s12 m6 l2">
                                <input id="fecini" name="fecini" type="date" value="<?php if(isset($_SESSION["Filtros"]["tablaComprobantes"]["fecini"]) && $_SESSION["Filtros"]["tablaComprobantes"]["fecini"]!="") echo $_SESSION["Filtros"]["tablaComprobantes"]["fecini"]; else echo date("Y-m-d");?>">
                                <label for="fecini" class="active">Desde</label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <input id="fecfin" name="fecfin" type="date" value="<?php echo $_SESSION["Filtros"]["tablaComprobantes"]["fecfin"];?>">
                                <label for="fecfin" class="active">Hasta</label>
                            </div>
                            <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["empresa"]["id_empresa"];?>">
                            <div class="input-field col s12 m6 l2">
                                <input id="nombre" name="nombre" type="text" value="<?php echo $_SESSION["Filtros"]["tablaComprobantes"]["nombre"];?>">
                                <label for="nombre">Nombre</label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <select name="tipodoc" id="tipodoc">
                                    <option value="">(TODOS)</option>
                                    <option value="F">FACTURAS</option>
                                    <option value="B">BOLETAS</option>
                                    <option value="C">NOTAS DE CREDITO</option>
                                    <option value="D">NOTAS DE DEBITO</option>
                                </select>
                                <label for="tipodoc">Tipo de Documento</label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <select name="estado" id="estado">
                                    <option value="">(TODOS)</option>
                                    <option value="R">AUN NO DECLARADO</option>
                                    <option value="E,P">NO REGISTRADO</option>
                                    <option value="M,T">DECLARADO Y ACTIVO</option>
                                    <option value="B">DECLARADO Y ANULADO</option>
                                    <option value="I">DECLARADO E INCORRECTO</option>
                                    <option value="S">DECLARADO SIN COMPROBAR</option>
                                </select>
                                <label for="estado">Estado</label>
                            </div>
                        </div>
                        <div class="col s12 m1 l2 center hide-on-small-only">
                            <button type="button" onclick="buscar2();" class="btn btn-floating indigo darken-4 btnBuscar"><i class="material-icons yellow-text">search</i></button>
                            <button type="button" onclick="plesunat();" class="btn btn-floating green darken-4"><i class="material-icons yellow-text">print</i></button>
                            <button type="button" title="Enviar Boleta" onclick="enviarPendienteBoleta();" class="btn btn-floating orange darken-4"><i class="material-icons yellow-text">compare_arrows</i></button>
                            <button type="button" title="Enviar Factura" onclick="enviarPendienteFactura();" class="btn btn-floating yellow darken-4"><i class="material-icons yellow-text">compare_arrows</i></button>
                            <button type="button" onclick="actualizarestados2();" class="btn btn-floating orange darken-4" title="Sinzronzar Servidor"><i class="material-icons yellow-text">backup</i></button>
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
                            <th>DNI/RUC</th>
                            <th>NOMBRE O RAZON SOCIAL</th>
                            <th>TOTAL</th>
                            <th>FECHA DE SOLICITUD</th>
                            <th>FECHA DE ENVIO</th>
                            <th>FECHA DE RESPUESTA</th>
                            <th>ESTADO</th>
                            <th></th>
                            <!--th>FICHEROS</th-->
                            <th>ERROR</th>
                            <th>PDF</th>
                            <th>IMPRIMIR</th>
                            <th>EMAIL</th>
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
<script>
    /*$("select").material_select();
    agregarRuta(true,"Comprobantes","lstComprobantes.php");
    agregarOpciones([]);*/
    //agregarOpciones([["../vista/frmComprobante.php?modo=N","add","AGREGAR NUEVO <b>MENU</b>"]]);
    function buscar2(){
        cargarTabla_JSON('frmFiltros','tblComprobantes','divPaginacion');
    }
    

    function plesunat(){
        //window.open("controlador/contComprobante.php?funcion=generarPLE&"+$("#frmFiltros").serialize());
        window.open("controlador/contComprobante.php?funcion=generarResumenComprobantes&"+$("#frmFiltros").serialize());
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
            url: "vista/modalFicheros.php",
            data: "id_solicitud="+id_solicitud,
            success: function (data, textStatus, jqXHR) {
                $("#divContenido").html(data);
            },
            beforeSend: function (xhr) {
            }
        });
    }
    function enviarPendienteBoleta(){
        $.ajax({
            async:true,    
            cache:false,
            type: 'GET',
            url: 'controlador/contComprobante.php?funcion=enviarPendienteBoleta',
            data: $("#frmFiltros").serialize(),
            beforeSend: function(xhr){
                alert("Enviando...");
            },
            success: function (data, textStatus, jqXHR) {
                alert("Enviado");
            },
        });
    }
    function enviarPendienteFactura(){
        $.ajax({
            async:true,    
            cache:false,
            type: 'GET',
            url: 'controlador/contComprobante.php?funcion=enviarPendienteFactura',
            data: $("#frmFiltros").serialize(),
            beforeSend: function(xhr){
                alert("Enviando...");
            },
            success: function (data, textStatus, jqXHR) {
                alert("Enviado");
            },
        });
    }
    function actualizarestados2(){
        $.ajax({
            async:true,    
            cache:false,
            type: 'GET',
            url: 'controlador/contComprobante.php?funcion=actualizarEstadoServidor2',
            data: $("#frmFiltros").serialize(),
            beforeSend: function(){
                alert("Consultando...");
            },
            success: function (data, textStatus, jqXHR) {
                try{
                    var json = JSON.parse(data);
                    var correcto = json.correcto;
                    var url = json.url.toString();
                    if(!correcto){
                        mensajeToast('ERROR',json.error);
                    }else{
                        mensajeToast('',json.mensaje);
                    }
                } catch(err) {
                    console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
                }
            },
        });
    }
    activeLabels();
    buscar2();
    function modalErrores(id){
        //console.log(id);
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
                console.log(id);
                $("#divContenido").html(data);
            },
            beforeSend: function (xhr) {
            }
        });
    }

    function modalEmail(id){
        //console.log(id);
        $("#h2Cabecera").html("ENVIO EMAIL");
        $("#divContenido").html(cargando);
        $("#modTasasDetalle").openModal();
        $.ajax({
            async:true,    
            cache:false,
            type: 'GET',
            url: "vista/frmEmail.php",
            data: "idsolicitud="+id,
            success: function (data, textStatus, jqXHR) {
                console.log(id);
                $("#divContenido").html(data);
            },
            beforeSend: function (xhr) {
            }
        });
    }
</script>