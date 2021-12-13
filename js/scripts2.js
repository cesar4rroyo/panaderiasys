$(document).ready(function(){
    $('.parallax').parallax();
    $('.scrollspy').scrollSpy();
    $(".button-collapse").sideNav();
    $(".dropdown-button").dropdown({
        hover:true,
        belowOrigin: true
    });
    $('.modal').modal({
        dismissible: true, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        inDuration: 300, // Transition in duration
        outDuration: 200, // Transition out duration
        startingTop: '4%', // Starting top style attribute
        endingTop: '10%', // Ending top style attribute
        ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
          //alert("Ready");
          //console.log(modal, trigger);
        },
        complete: function() {
            //SOLO PARA EL CASO DE MODALES QUE DEJEN UN TR CON CLASE ACTIVE
            var tr = $("table").find("tr.active");
            if(tr!=null){
                if(tr.length>0){
                    //tr = tr[0];
                    $(tr).removeClass("active");
                }
            }
        } // Callback for Modal close
      }
    );
    
    $(".menu-lateral").sideNav({
        menuWidth: 300, // Default is 300
        edge: 'left', // Choose the horizontal origin
        closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
        draggable: true 
    });
    
    $('#modTasasDetalle').modal({
        dismissible: true, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        inDuration: 300, // Transition in duration
        outDuration: 200, // Transition out duration
        startingTop: '4%', // Starting top style attribute
        endingTop: '10%', // Ending top style attribute
        ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.

        },
        complete: function() {

        } // Callback for Modal close
      }
    );
 
    
    //FRAMEWORK DE DEFINICION REGLAS CSS A TRAVES DE PROPIEDADES DE ELEMENTOS
    //var ElementQueries = require('ElementQueries');

    // attaches to DOMLoadContent and does anything for you
    //ElementQueries.listen();

    // or if you want to trigger it yourself:
    // 'init' parses all available CSS and attach ResizeSensor to those elements which
    // have rules attached (make sure this is called after 'load' event, because
    // CSS files are not ready when domReady is fired.
    // Use this function if you have dynamically created HTMLElements
    // (through ajax calls or something)
    //ElementQueries.init();
});

var AJAX = [];
/*
$(document).keyup(function (e) {
    if($(".btnToastAceptar").length>0 && (e.keyCode == 13)){
        $(".btnToastAceptar").first().click();
    }else if ($(".frmFiltros").length>0 && (e.keyCode == 13)) {
        if($(".frmFiltros").find("button.btnBuscar").length>0){
            $(".frmFiltros").find("button.btnBuscar").first().click();
        }
    }else if ($("input:not(.autocomplete)").is(":focus") && (e.keyCode == 13)) {
        var formulario = $("input:focus").parents("form");//console.log(formulario);
        var button = formulario.find(".divEnviar")[0];
        formulario = formulario[0];
        if($(formulario).attr("validation-function")!=null && $(formulario).attr("validation-function").toString().trim().length>0){
            try{
                eval($(formulario).attr("validation-function"));
            } catch(err) {
                console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
            }
        }else{
            if($(button).css("display")!="none"){
                enviarForm($(formulario).attr("id"),$(button).attr("id"));
            }else{
                mensajeToast("UN MOMENTO","Debe esperar que termine de cargar los campos.");
            }
        }
    }
});
*/

$( document ).ajaxStart(function() {
    //console.log($(this));
});

$( document ).ajaxStop(function() {
    $(".material-tooltip").remove();
    $('.tooltipped').tooltip({delay: 50});   
    $("tr").dblclick(function (){
        var availableDetails = $(this).attr("available-details");
        if(availableDetails!=null){
            if(availableDetails=="true"){
                var data = $(this).attr("data-details");
                if(data!=null){
                    if(data.toString().trim().length>0){
                        try {
                            $(this).parents("tbody").children("tr.active").removeClass("active");
                            $(this).addClass("active");
                            data = decodeURIComponent(data);
                            data = JSON.parse(data);
                            var html = "";
                            $(data).each(function (key,val){
                                html = html + "<tr>";
                                $(val).each(function (key2,val2){
                                    html = html + "<td>" + val2 + "</td>";
                                });
                                html = html + "</tr>";
                            });
                            $("#tbodyDetallesRegistro").html(html);
                            $("#modDetallesTabla").modal('open');
                        } catch(err) {
                            console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
                        }
                    }
                }
            }
        }
    });
});

function alerta(texto,tiempo,modo,funcion){
    if(tiempo==null){
        tiempo=((texto.length)/8)*1000;
    }
    if(modo==null){
        modo="";
    }
    if(funcion==null){
        funcion = function(){};
    }
    if(tiempo<5000){
        tiempo = 5000;
    }
    Materialize.toast(texto, tiempo,modo,funcion);
}

function alert(texto){
    alerta(texto);
}
function AlertaEliminar(contenido,url,parametros,ejecutar){
    alertaToast("ADVERTENCIA",contenido,"enviarURL('"+url+"','"+parametros+"','"+ejecutar+"');");
}

function AlertaForm(formulario,contenido,fnCancelar){
    if(fnCancelar==null){
        fnCancelar = "";
    }
    var fnValidacion = $("#"+formulario).attr("validation-function");
    alertaToast("ADVERTENCIA",contenido,fnValidacion,fnCancelar);
}

function AlertaFuncion(contenido,funcion){
    alertaToast("ADVERTENCIA",contenido,funcion);
}

var timeToast = 300000;

function cerrarToast(toast){
    $(toast).fadeOut(1000);
    setTimeout(function (){ 
        $(toast).remove();
        if($("#toast-container").children().length==0){$("#toast-container").removeClass("z-depth-5");}
    },500);
}

function mensajeToast(titulo,contenido){
    if(titulo.toString().length==0){
        titulo = "INFORMACION";
    }
    contenido = decodeURIComponent(contenido);
    var d = new Date();
    var identificador = d.getTime();
    Materialize.toast('<div class="" style="width:100%;"><div class="row toastHeader"><h2>'+titulo+'</h2></div><div class="row toastContent"><div class="col s12"><p class="center" id="'+identificador+'"></p></div><div class="col s12 center"><div class="col s12"><div class="center"><button class="btn btnToastAceptar" onclick="cerrarToast($(this).parent().parent().parent().parent().parent().parent());">ACEPTAR</button></div></div></div></div></div>', timeToast, '', 'if($("#toast-container").children().length==0){$("#toast-container").removeClass("z-depth-5");}');
    $(".toast").each(function(key,val){
        $(val).css("padding","0px");
        $(val).css("width","100%");
        $(val).addClass("z-depth-5");
    });
    $("#"+identificador).html(contenido);
    try {
        var jsonvalor = JSON.parse(contenido);
        console.log(jsonvalor);
        if(jsonvalor!=null){
            var mensaje2 = jsonvalor.mensaje;
            var xdebug_message = mensaje2.xdebug_message;
            console.log(xdebug_message);
            $("#"+identificador).html(xdebug_message);
        }
    } catch (e) {
        
    }
    $("#toast-container").css("display","block");
    $("#toast-container").removeClass("z-depth-5");
    $("#toast-container").addClass("z-depth-5");
}

function alertaToast(titulo,contenido,funcion,funcionCancelar){
    if(funcionCancelar==null){
        funcionCancelar = "";
    }
    contenido = decodeURIComponent(contenido);
    Materialize.toast('<div class="" style="width:100%;"><div class="row toastHeader"><h2>'+titulo+'</h2></div><div class="row toastContent"><div class="col s12"><p class="center">'+contenido+'</p></div><div class="col s12 center"><div class="col s6"><div class=""><button class="btn btnToastAceptar" onclick="$(this).parent().parent().parent().parent().parent().parent().remove();'+funcion+'">ACEPTAR</button></div></div><div class="col s6"><div class=""><button class="btn btnToastCancelar" onclick="cerrarToast($(this).parent().parent().parent().parent().parent().parent());'+funcionCancelar+'">CANCELAR</button></div></div></div></div></div>', timeToast, '', 'if($("#toast-container").children().length==0){$("#toast-container").removeClass("z-depth-5");}');
    $(".toast").each(function(key,val){
        $(val).css("padding","0px");
        $(val).css("width","100%");
        $(val).addClass("z-depth-5");
    });
    $("#toast-container").css("display","block");
    $("#toast-container").removeClass("z-depth-5");
    $("#toast-container").addClass("z-depth-5");
}

var cargando = '<div class="preloader-wrapper small active"><div class="spinner-layer spinner-green-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>';

function redireccionar(vista, parametros){
    $("#divPrincipal").html('<div class="center" style="height:80%;">'+cargando+'</div>');
    $(".material-tooltip").html("");
    if($("#btnOpciones").css("overflow")=="visible"){
        $("#btnOpciones").trigger("click");
    }
    var ajax_function = $.ajax({
        async:true,    
        cache:false,
        type: 'GET',
        url: vista,
        data: parametros,
        success: function (data, textStatus, jqXHR) {
            $("#divPrincipal").html(data);
            $('.tooltipped').tooltip({delay: 50});
        },
        beforeSend: function (xhr) {
            $(".material-tooltip").remove();
        }
    });
    //AJAX.push(ajax_function);
}

var ruta_array = [];

function recargar(pos,url){
    var nuevo_array = [];
    $(ruta_array).each(function (key,val){
        if(key<pos){
            nuevo_array.push(val);
        }
    });
    ruta_array = nuevo_array;
    actualizarRuta();
    redireccionar(url);
}

function recargarPorURL(url,parametros){
    var nuevo_array = [];
    var valido = true;
    $(ruta_array).each(function (key,val){
        if(val[0].includes(url)){
            valido = false;
        }
        if(valido){
            nuevo_array.push(val);
        }
    });
    ruta_array = nuevo_array;
    actualizarRuta();
    if(parametros.toString().length>0){
        redireccionar(url+"?"+parametros);
    }else{
        redireccionar(url);
    }
}

function enviarForm(form,button){
    var boton = $("#"+button).html();
    $("#"+button).html(cargando);
    var formData = new FormData(document.getElementById(form));
    var ajax_function = $.ajax({
        /*async:true,    
        cache:false,
        contentType: false,
        processData: false,
        type: 'POST',
        url: $("#"+form).attr("action"),
        data: $("#"+form).serialize(),*/
        type: "POST",
        url: $("#"+form).attr("action"),
        data: formData,
        contentType:false,
        processData: false,
        cache:false,
        success: function (data, textStatus, jqXHR) {
            try {
                var json = JSON.parse(data);
                var correcto = json.correcto;
                var url = json.url.toString();
                if(!correcto){
                    mensajeToast('ERROR',json.error);
                }else{
                    if(json.mensaje!=null && json.mensaje.toString().trim().length>0){
                        mensajeToast('',json.mensaje);
                    }
                }
                var ejecutar = json.ejecutar;
                if(ejecutar!=null && ejecutar.toString().trim().length>0){
                    eval(ejecutar);
                    console.log(ejecutar);
                }
                if(url.trim().length>0){
                    window.location = url;
                }else{
                    if(correcto && $("#modTasasDetalle").length>0 && $("#modTasasDetalle").hasClass("open")){
                        $("#modTasasDetalle").modal("close");
                    }else{
                        var vista = json.vista;
                        var parametros = json.parametros;
                        if(vista.trim().length>0){
                            recargarPorURL(vista,parametros);
                        }else{
                            $("#"+button).html(boton);
                        }
                    }
                }
            } catch(err) {
                console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
            }
        },
        beforeSend: function (xhr) {

        }
    });
    //AJAX.push(ajax_function);
}

function enviarURL(url,parametros,ejecutar){
    var ajax_function = $.ajax({
        async:true,    
        cache:false,
        type: 'POST',
        url: url,
        data: parametros,
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
                if(url.trim().length>0){
                    window.location = url;
                }else{
                    var vista = json.vista;
                    var parametros = json.parametros;
                    if(vista.trim().length>0){
                        recargarPorURL(vista,parametros);
                    }else{
                        if(ejecutar!=null){
                            eval(ejecutar);
                        }
                    }
                }
            } catch(err) {
                console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
            }
        },
        beforeSend: function (xhr) {

        }
    });
    //AJAX.push(ajax_function);
}

function agregarRuta(nuevo,nombre,url){
    if(nuevo){
        ruta_array = [];
    }
    ruta_array.push([url,nombre]);
    actualizarRuta();
}

function actualizarRuta(){
    var array_actual = ruta_array;
    $("#lstSecuencia").html("");
    ruta_array = [];
    $(array_actual).each(function (key,val){
        $("#lstSecuencia").append('<a href="#!" onclick="recargar(\''+key+'\',\''+val[0]+'\');" class="breadcrumb">'+val[1]+'</a>');
        ruta_array.push(val);
    });
}

function agregarOpciones(opciones){
    var colores = ["red","yellow darken-4","green","blue","purple","orange","deep-purple","cyan","teal","light-blue","pink","lime","light-green","amber","deep-orange","brown","grey"];
    $("#ulOpciones").html("");
    var opciones_html = "";
    $(opciones).each(function (key,val){
        var color = colores[Math.floor((Math.random() * colores.length))];
        opciones_html = opciones_html + '<li><a class="pointer btn-floating '+color+' tooltipped" data-html="true" data-position="left" data-delay="50" data-tooltip="'+val[2]+'" onclick="redireccionar(\''+val[0]+'\',\'\')"><i class="material-icons white-text">'+val[1]+'</i></a></li>';
    });
    $("#ulOpciones").html(opciones_html);
    $('.tooltipped').tooltip({delay: 50});
}

function cargarTabla_JSON(formulario,tBody,divPaginacion,href){
    $("#"+tBody).empty().html('<tr><td colspan="100"><div class="center">'+cargando+'</div></td></tr>');
    $("#"+divPaginacion).empty();
    var url = "";
    var data = "";
    if(formulario.toString().length>0){
        url = $("#"+formulario).attr("action");
        data = $("#"+formulario).serialize();
    }
    if(href!=null){
        url = href;
        data = "";
    }
    var ajax_function = $.ajax({
        async:true,    
        cache:false,
        type: "GET",   
        url: url,
        data: data, 
        success:  function(a){
            try{
                if(a!=null){
                    a = JSON.parse(a);
                    var html = '';
                    var tabla = a.datos;
                    var detalles = a.detalles;
                    var npag = Number(a.npag);
                    var href = a.href;
                    var clases = a.clases;
                    var styles = a.styles;
                    var Npaginas = Number(a.Npaginas);
                    var Npaginacion = Number(a.Npaginacion);
                    var Nlateral = Number((Npaginacion%2==0)?(Npaginacion/2):((Npaginacion-1)/2));
                    if(tabla.length>0){
                        $(tabla).each(function (key,val){
                            html = html + '<tr ';
                            if(clases!=null){
                                html = html + clases[key];
                            }else if(styles!=null){
                                html = html + ' style="'+styles[key]+'"';
                            }
                            if(detalles!=null){
                                html = html + ' available-details="true" data-details="'+detalles[key]+'"';
                            }
                            html = html + '>';
                            $(val).each(function (key2,val2){
                                html = html + '<td>'+val2+'</td>';
                            });
                            html = html + '</tr>';
                        });
                        $("#"+tBody).html(html);
                        $("#"+tBody).addClass("pointer");
                        html = '';
                        html = html + '<div class="col s12">';
                        html = html + '    <ul class="pagination center">';
                        html = html + '      <li>';
                        if(npag>1){
                            html = html + '<a href="#" onclick="cargarTabla_JSON(\'\',\''+tBody+'\',\''+divPaginacion+'\',\''+href+'npag='+(npag-1).toString()+'\')"><i class="material-icons">chevron_left</i></a>';
                        }else{
                            html = html + '<a class="disabled"><i class="material-icons">chevron_left</i></a>';
                        }
                        html = html + '      </li>';
                        var begin = 0;
                        var end = 0;
                        if(Npaginas<=Npaginacion){ begin = 1;
                        }else{ if(npag-Nlateral<1){ begin = 1;
                        }else{ if(npag-Nlateral>Npaginas-Npaginacion+1){ begin = Npaginas-Npaginacion+1;
                        }else{ begin = npag-Nlateral;}}}
                        if(Npaginas<=Npaginacion){ end = Npaginas;
                        }else{ if(npag+Nlateral<Npaginacion){ end = Npaginacion;
                        }else{ if(npag+Nlateral>Npaginas){ end = Npaginas;
                        }else{ end = npag+Nlateral;}}}
                        for (var i=begin; i<=end; i++){
                            html = html + '<li class="';
                            if(npag == i){
                                html = html + 'active';
                            }else{
                                html = html + 'waves-effect';
                            }
                            html = html + '"><a href="#" onclick="cargarTabla_JSON(\'\',\''+tBody+'\',\''+divPaginacion+'\',\''+href+'npag='+i+'\')">'+i+'</a></li>';
                        }
                        html = html + '      <li>';
                        if(npag < Npaginas){
                            html = html + '<a href="#" onclick="cargarTabla_JSON(\'\',\''+tBody+'\',\''+divPaginacion+'\',\''+href+'npag='+(npag + 1).toString()+'\')"><i class="material-icons">chevron_right</i></a>';
                        }else{
                            html = html + '<a class="disabled"><i class="material-icons">chevron_right</i></a>';
                        }
                        html = html + '    </ul>';
                        html = html + '</div>';
                        $("#"+divPaginacion).html(html);
                        $('.tooltipped').tooltip({delay: 50});
                    }else{
                        $("#"+tBody).empty().html('<tr><td colspan="100">No se encontro ninguna coincidencia</td></tr>');
                    }
                }else{
                    window.location='/Selgestiun/';
                }
            } catch(err) {
                console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
            }
        },
        beforeSend:function(){},
        error:function(objXMLHttpRequest){}
    });
    //AJAX.push(ajax_function);
}

function comprobarAjaxPendientes(ejecutar){
    setTimeout(function (){
        if(jQuery.active>0){
            comprobarAjaxPendientes(ejecutar);
        }else{
            eval(ejecutar);
        }
    },1000);
}

function selectAJAX_JSON(url,parametros,label,propID,propOption,div,name,id,seleccionado,funcion,todos,ejecutar,bloquear,arrayMostrar){
    if(seleccionado==null || seleccionado.trim().length==0){
        seleccionado = "0";
    }
    $("#"+div).empty().html('<div class="center">'+cargando+'<input type="hidden" name="'+name+'" value="'+seleccionado+'"></div>');
    var optionTodos = "";
    if(todos==null){
        todos=true;
        optionTodos = '(TODOS)';
    }else{
        if(typeof(todos)=="string"){
            optionTodos = todos;
            todos=true;
        }else{
            optionTodos = '(TODOS)';
        }
    }
    //console.log(bloquear);
    if(bloquear!=null){
        $(bloquear).each(function (key,val){
            //console.log(val);
            $("#"+val).hide();
        });
    }
    var ajax_function = $.ajax({
        async:true,    
        cache:false,
        type: 'GET',   
        url: url,
        data: parametros, 
        success:  function(a){
            try{
                a = JSON.parse(a);
                //console.log(a);
                var html = '<select name="'+name+'" id="'+id+'"  onchange="'+funcion+'">';
                var cantidad = 0;
                if(todos){
                    html = html + '<option value="0">'+optionTodos+'</option>';
                }
                var dataMostrar = '';
                var js2 = '';
                if(arrayMostrar!=null){
                    var js2 = 'dataMostrar = ';
                    $(arrayMostrar).each(function (key2,val2){
                        js2 = js2 + "val."+val2+"+'|'+";
                    });
                    js2 = js2 + "'';";
                }
                $(a).each(function (key,val){
                    cantidad++;
                    //html = html + '<option value="'+val.Id+'" ';
                    try{
                        eval(js2);
                    } catch(err) {
                        console.log(err);
                        console.log(js2);
                        dataMostrar = "";
                    }
                    dataMostrar = encodeURI(dataMostrar);
                    eval("html = html + '<option value=\"'+val."+propID+"+'\" data_mostrar=\"'+dataMostrar+'\" ';");
                    eval("if(seleccionado==val."+propID+"){html = html + ' selected=\"\"';}");
                    //html = html + '>'+val.Abreviatura+'</option>';
                    eval("html = html + '>'+val."+propOption+"+'</option>';");
                });
                html = html + '</select><label>'+label+'</label>';
                $("#"+div).empty().html(html);
                $("select").material_select();
                if(ejecutar!=null){
                    eval(ejecutar);
                }
                if(bloquear!=null){
                    var mostrar='';
                    $(bloquear).each(function (key,val){
                        mostrar = mostrar + '$("#'+val+'").show();';
                    });
                    comprobarAjaxPendientes(mostrar);
                }
            } catch(err) {
                console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
            }
        },
        beforeSend:function(){},
        error:function(objXMLHttpRequest){}
    });
    //AJAX.push(ajax_function);
}

function autocompleteAJAX_JSON(url,parametros,label,propID,propOption,div,name,id,funcionClick,funcionBlur,claseEspecial,ejecutar,bloquear){
    $("#"+div).empty().html('<div class="center">'+cargando+'</div>');
    if(bloquear!=null){
        $(bloquear).each(function (key,val){
            //console.log(val);
            $("#"+val).hide();
        });
    }
    $.ajax({
        async:true,    
        cache:false,
        type: 'GET',   
        url: url,
        data: parametros, 
        success:  function(a){
            try{
                a = JSON.parse(a);
                //console.log(a);
                var html = '<input type="text" autocomplete="off" class="autocomplete" name="'+name+'" id="'+id+'" onblur="'+funcionBlur+'" last_selected=""><label for="'+id+'">'+label+'</label>';
                $("#"+div).empty().html(html);
                var datos = '';
                var js = 'var datos = {';
                $(a).each(function (key,val){
                    var dataMostrar = '';
                    var js2 = 'var dataMostrar = ';
                    $(propID).each(function (key2,val2){
                        js2 = js2 + "val."+val2+"+'|'+";
                    });
                    js2 = js2 + "'';";
                    eval(js2);
                    dataMostrar = dataMostrar.substring(0, dataMostrar.length - 1);
                    eval("js = js + '\"'+val."+propOption+"+'\": \"'+dataMostrar+'\",';");
                });
                js = js + '};';
                eval(js);
                $("#"+id).autocomplete({
                        data: datos
                    },funcionClick,claseEspecial);
                if(ejecutar!=null){
                    eval(ejecutar);
                }
                if(bloquear!=null){
                    var mostrar='';
                    $(bloquear).each(function (key,val){
                        mostrar = mostrar + '$("#'+val+'").show();';
                    });
                    comprobarAjaxPendientes(mostrar);
                }
            } catch(err) {
                console.log(err);alerta("OCURRIO UN ERROR EN EL SISTEMA, REVISE");
            }
        },
        beforeSend:function(){},
        error:function(objXMLHttpRequest){}
    });
}

function limpiarAutocomplete(inptTxt,inptHdd,spanClear){
    $("#"+inptTxt).val("");
    $("#"+inptTxt).attr("last_selected","");
    $("#"+inptHdd).val("0");
    $(spanClear).parent().removeClass("select-wrapper");
    $("#"+inptTxt).prop("readonly",false);
    $(spanClear).remove();
    $("#"+inptTxt).focus();
}

function activeLabels(){
    $("label").each(function (key,val){
        var id_input = $(val).attr("for");
        if(id_input!=null){
            if(($("#"+id_input).attr("type")=="text" || $("#"+id_input).attr("type")=="number" || $("#"+id_input).attr("type")=="password") && $("#"+id_input).val().toString().trim().length>0){
                $(val).addClass("active");
            }
        }
    });
}

function modalTasas(modo,idcomptipopoli,eliminar){
    if(eliminar==null){
        eliminar = false;
    }
    var titulo = "";//console.log(modo);
    if(modo=="COMISION"){
        titulo = "DETALLE DE LAS COMISIONES";
    }else{
        titulo = "DETALLE DE LAS PRIMAS NETAS";
    }//console.log(titulo);
    $("#h2Cabecera").html(titulo);
    $("#divContenido").html(cargando);
    $("#modTasasDetalle").modal('open');
    var ajax_function = $.ajax({
        async:true,    
        cache:false,
        type: 'GET',
        url: "modalTasas.php",
        data: "modo="+modo+"&id_compania_tipopoliza="+idcomptipopoli+"&eliminar="+eliminar,
        success: function (data, textStatus, jqXHR) {
            $("#divContenido").html(data);
        },
        beforeSend: function (xhr) {
        }
    });
}

function download(parametros){
    window.location="../controlador/contDocumento.php?funcion=descargarFormato&"+parametros;
}

function imprimir(idsolicitud){
    $.ajax({
        async:true,    
        cache:false,
        type: 'GET',
        url: "../controlador/contImprimir.php?accion=ImprimirVenta",
        data: "id_solicitud="+idsolicitud,
        success: function (data, textStatus, jqXHR) {
            alert("Imprimiendo");
        },
        beforeSend: function (xhr) {
        }
    });
}
