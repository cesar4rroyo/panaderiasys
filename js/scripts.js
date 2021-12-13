$(document).ready(function(){
    $('.brand-logo').sideNav({
        menuWidth: 350, // Default is 240
        edge: 'left', // Choose the horizontal origin
        closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
        draggable: true // Choose whether you can drag to open on touch screens
      }
    );
    $('.button-collapse').sideNav({
        edge: 'right', // Choose the horizontal origin
        closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
        draggable: true // Choose whether you can drag to open on touch screens
    });
    $('select').material_select();
});
$('.btnSalir').on('click', function(e){
    e.preventDefault();
    swal({ 
        title: "Estas seguro que deseas salir?",   
        text: "La sesión actual se cerrará y abandonará el sistema",   
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "SI",
        animation: "slide-from-top",   
        closeOnConfirm: false,
        cancelButtonText: "Cancelar"
    }, function(){   
        window.location='cerrarSesion.php'; 
    });
}); 
$('.btnPerfil').on('click', function(e){
    e.preventDefault();
    var nombre = $(this).attr("nombre-perfil");
    swal({
        title: nombre,
        text: "<div class='row'><div class='col s12 center'><a class='btn confirm' href='#!'  onclick=\"javascript: swal.close(); setRun('vista/mantPerfilUsuario', '&id_clase=16', 'frame', 'carga', 'imgloading')\";>MI PERFIL</a></div></div><div class='row'><div class='col s12 center'><a class='btn' href='#!' onclick=\"swal.close(); setRun('vista/mantUsuarioClave', '&id_clase=16', 'frame', 'carga', 'imgloading');\">CAMBIAR CONTRASEÑA</a></div></div>",
        imageUrl: "assets/img/user.png",
        confirmButtonColor: "#d50000",
        confirmButtonText: "CERRAR",
        html: true
    });
});

function modalPropiedades(idproducto,nombre,modo,cantidad,idsucursalproducto,accion){
    $.ajax({
        type: "POST",
        url: "vista/ajaxPedido.php",        
        data:"accion=detalleProducto"+modo+"&idproducto="+idproducto,
        success: function(a) {
            a = JSON.parse(a);
            var estructura = a.estructura;
            var comentario = a.comentario;
            $('#modalPropiedades').openModal({ 
                dismissible: true, // Modal can be dismissed by clicking outside of the modal
                opacity: .0, // Opacity of modal background
                in_duration: 300, // Transition in duration
                out_duration: 200, // Transition out duration
                starting_top: '54%', // Starting top style attribute
                ending_top: '50%', // Ending top style attribute
                ready: function(modal, trigger) {
                    lista = a.lista;
                    $("#nombre-producto").html(nombre);
                    $("#cantidad-producto").html(cantidad);
                    $("#detalle-producto").val(comentario);
                    $("#detalle-producto").attr("modo",modo);
                    $("#txtIdSucursalProducto").val(idsucursalproducto);
                    $("#txtIdProducto").val(idproducto);
                    $("#txtAccionPropiedad").val(accion);
                    $("#divPropiedadProducto").html(estructura);
                    tipo = 'C';
                },
                complete: function() {
                    $('.lean-overlay').remove();
                }
            });
        }
    });
}

function modalMesas(tipo,idmesa){
    $.ajax({
        type: "POST",
        url: "controlador/contSalon.php",        
        data:"accion=LISTAR",
        success: function(a) {
            var datos = JSON.parse(a);
            $('#modalMesas').openModal({
                 dismissible: true, // Modal can be dismissed by clicking outside of the modal
                 opacity: .5, // Opacity of modal background
                 in_duration: 300, // Transition in duration
                 out_duration: 200, // Transition out duration
                 starting_top: '4%', // Starting top style attribute
                 ending_top: '10%', // Ending top style attribute
                 ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
                   var situacion = "";
                   if(tipo=="JUNTAR"){
                       $("#tituloModalMesas").html("JUNTAR MESA");
                       situacion = "O";
                   }else if(tipo=="MOVER"){
                       $("#tituloModalMesas").html("MOVER MESA");
                       situacion = "N";
                   }else{
                       $("#tituloModalMesas").html("UNIR MESA");
                       situacion = "N";
                   }
                   $("#modalMesasSlcSalon").material_select('destroy');
                   $("#modalMesasSlcSalon").empty();
                   $("#modalMesasSlcSalon").attr("id_mesa_actual",idmesa);
                   $("#modalMesasSlcSalon").attr("situacion",situacion);
                   $("#modalMesasSlcSalon").attr("tipo",tipo);
                   var idsalon = 0;
                   jQuery.each(datos, function(key, val) {
                       if(idsalon==0){
                           idsalon=val[0];
                       }
                       $("#modalMesasSlcSalon").append('<option value="'+val[0]+'">'+val[1]+'</option>');
                   });
                   $("#modalMesasSlcSalon").material_select();
                   modalMesasSlcMesas(idsalon,idmesa,situacion);
                 },
                 complete: function() { 
                   $("#tituloModalMesas").html("");
                 }
               }
             );
        }
    });
}

function modalMesasSlcMesas(idSalon,idmesa,situacion){
    $.ajax({
        type: "POST",
        url: "controlador/contMesa.php?id_clase=5",        
        data:"accion=LISTARPORSALON&IdMesa="+idmesa+"&situacion="+situacion+"&IdSalon="+idSalon,
        success: function(a) {
            var datos = JSON.parse(a);
            $("#modalMesasSlcMesas").empty();
            var numero = 0;
            jQuery.each(datos, function(key, val) {
                ++numero;
                $("#modalMesasSlcMesas").append('<option value="'+val[0]+'">'+val[1]+'</option>');
            });
            if(numero==0){
                $("#modalMesasSlcMesas").append('<option value="">NINGUNO</option>');
                $("#modalMesasBtnAceptar").attr("disabled",true);
            }else{
                $("#modalMesasBtnAceptar").removeAttr("disabled");
            }
            $("#modalMesasSlcMesas").material_select();
        }
    });
}

function modalMesasBtnAceptar(){
    var situacion = $('#modalMesasSlcSalon').attr('situacion');
    var tipo = $('#modalMesasSlcSalon').attr('tipo');
    var idmesalocal = $("#modalMesasSlcSalon").attr("id_mesa_actual");
    if(tipo=="JUNTAR"){
        //JUNTAR MESA
        var idmovimiento = $("#txtId").val();
        var idmesajunta = $("#modalMesasSlcMesas").val();
        $.ajax({
            type: "POST",
            url: "controlador/contPedidoMozo.php",        
            data:"accion=JUNTARMESA&idmesalocal="+idmesalocal+"&idmovimiento="+idmovimiento+"&idmesajunta="+idmesajunta,
            success: function(a) {
                atras();
            }
        });
    }else if(tipo=="MOVER"){
        ///MOVER MESA
        var idmovimiento = $("#txtId").val();
        var idmesacambio = $("#modalMesasSlcMesas").val();
        $.ajax({
            type: "POST",
            url: "controlador/contPedidoMozo.php",        
            data:"accion=CAMBIARMESA2&idmesalocal="+idmesalocal+"&idmovimiento="+idmovimiento+"&idmesacambio="+idmesacambio,
            success: function(a) {
                atras();
            }
        });
    }else{
        ///UNIR MESAS
        var idmesaunida = $("#modalMesasSlcMesas").val();
        $.ajax({
            type: "POST",
            url: "controlador/contPedidoMozo.php",        
            data:"accion=UNIRMESA&idmesalocal="+idmesalocal+"&idmesaunida="+idmesaunida,
            success: function(a) {
                atras();
            }
        });
    }
}

function modalEliminarBtnAceptar(modo){
    if(modo=="DETALLE"){
        var recipiente = document.getElementById('divDetallePedido');
        g_ajaxPagina = new AW.HTTP.Request;
        g_ajaxPagina.setURL("vista/ajaxPedido.php");
        g_ajaxPagina.setRequestMethod("POST");
        g_ajaxPagina.setParameter("accion", "quitarProductoMozo");
        g_ajaxPagina.setParameter("Actual", "Actual");
        g_ajaxPagina.setParameter("iddetalle", $("#inptIdDetalleMov").val());
        g_ajaxPagina.setParameter("comentario", $("#txaMotivoEliminado").val());
        g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").value);
        g_ajaxPagina.setParameter("class","zoom12");
        g_ajaxPagina.setParameter("IdProducto", $("#inptIdProducto").val());
        g_ajaxPagina.setParameter("IdSucursalProducto", $("#inptIdSucursalProducto").val());
        g_ajaxPagina.response = function(text){
            recipiente.innerHTML = text;
            recipiente.focus();
            document.getElementById("divTotal").innerHTML="IMPORTE TOTAL S/."+document.getElementById("txtTotal").value;
            document.getElementById("divTotalProducto").innerHTML="NUMERO DE PRODUCTOS: "+document.getElementById("txtTotalProducto").value;
            aceptarCajero();
        };
        g_ajaxPagina.request();
    }else if(modo=="PEDIDO"){
        $.ajax({
            type: "POST",
            url: "controlador/contPedido.php",        
            data:"accion=ELIMINAR&txtId="+$("#inptIdMovimiento").val()+"&comentario="+$("#txaMotivoEliminado").val(),
            success: function(a) {
                if(a=="Guardado correctamente"){
                    atras();
                }else{
                    alert(a);
                }
            }
        });
    }
}

function historialMovimiento(idmovimiento){
    $.ajax({
        type: "POST",
        url: "controlador/contAlmacen.php?id_clase=5",        
        data:"accion=HISTORIALELIMINADOS&idmovimiento="+idmovimiento,
        success: function(a) {
            a=(JSON.parse(a));
            $('#modalHistorial').openModal({
                dismissible: true, // Modal can be dismissed by clicking outside of the modal
                opacity: .5, // Opacity of modal background
                in_duration: 300, // Transition in duration
                out_duration: 200, // Transition out duration
                starting_top: '4%', // Starting top style attribute
                ending_top: '10%', // Ending top style attribute
                ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
                  $("#tbdymodalHistorial").html("");
                  $(a).each(function (key,val){
                      //console.log(val);
                      $("#tbdymodalHistorial").append("<tr class='yellow lighten-2'><td>"+val[0]+"</td><td class='center'>"+val[1]+"</td><td class='center'>"+val[2]+"</td><td class='center'>"+val[3]+"</td><td class='center'>"+val[4]+"</td></tr>");
                  });
                },
                complete: function() { 
                    $("#tbdymodalHistorial").html("");
                }
              }
            );
        }
    });
}

function detalleMovimiento(idmovimiento){
    $.ajax({
        type: "POST",
        url: "vista/ajaxVenta.php?id_clase=5",        
        data:"accion=buscarDetalleProducto&idmov="+idmovimiento,
        success: function(a) {
            $('#modalDetalle').openModal({
                dismissible: true, // Modal can be dismissed by clicking outside of the modal
                opacity: .5, // Opacity of modal background
                in_duration: 300, // Transition in duration
                out_duration: 200, // Transition out duration
                starting_top: '4%', // Starting top style attribute
                ending_top: '10%', // Ending top style attribute
                ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
                    $("#tblDetalle").html(a);
                },
                complete: function() { 
                    $("#tblDetalle").html("");
                }
              }
            );
        }
    });
}

function sumarUno(){
    var cantidad = parseInt($("#cantidad-producto").html());
    cantidad++;
    $("#cantidad-producto").html(cantidad);
}
function restarUno(){
    var cantidad = parseInt($("#cantidad-producto").html());
    if(cantidad>1){
        cantidad--;
        $("#cantidad-producto").html(cantidad);
    }
}
function guardarPropiedadesProducto(){
    $('#modalPropiedades').closeModal();
    //LA FUNCION DE AGREGAR EL PRODUCTO
}
setInterval(function(){
    $(".Mesa-tiempo").each(function(){
        var tiempo = $(this).html();
        if(tiempo.length>=5){
            var res = tiempo.split(":");
            var segundos = 0;
            var minutos = 0;
            var horas = 0;
            var strsegundos;
            var strminutos;
            var strhoras;
            if(res.length == 2){
                segundos = parseInt(res[1]);
                minutos = parseInt(res[0]);
            }else{
                segundos = parseInt(res[2]);
                minutos = parseInt(res[1]);
                horas = parseInt(res[0]);
            }
            segundos++;
            if(segundos==60){
                segundos=0;
                minutos++;
            }
            if(minutos==60){
                minutos=0;
                horas++;
            }
            if(segundos<10){
                strsegundos = "0"+segundos;
            }else{
                strsegundos = ""+segundos;
            }
            if(minutos<10){
                strminutos = "0"+minutos;
            }else{
                strminutos = ""+minutos;
            }
            if(horas<10){
                strhoras = "0"+horas;
            }else{
                strhoras = ""+horas;
            }
            if(horas==0){
                $(this).html(strminutos+":"+strsegundos);
            }else{
                $(this).html(strhoras+":"+strminutos+":"+strsegundos);
            }
        }
    });
},1000);

setInterval(function(){
    //$(".descripcion_card_producto").each(function(key,val){console.log(val);});
},500);
var cargarclasecollapsibleproductos = false;
setInterval(function(){
    if(cargarclasecollapsibleproductos && $("#FINALDIVPRODUCTOS").length>0){
        $('.collapsible').collapsible();
        cargarclasecollapsibleproductos = false;
        var datos = {};
        $(".descripcion_card_producto").each(function(key,val){
            datos[$(val).html()]=$(val).attr("data-funcion");
        });
        $("#inpt_Busq_Producto").autocomplete({
            data: datos
        },ejecutarModalPropiedades,"autocomplete-content-margin");
        $('select').material_select();
    }
},500);
function ejecutarModalPropiedades(data){
    console.log();
    eval("modalPropiedades("+data+");");
}
function modalNuevoPersona(){
    $('#modalNuevoPersona').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      starting_top: '4%', // Starting top style attribute
      ending_top: '10%', // Ending top style attribute
      ready: function(modal, trigger) {
          cambiarTipoPersona("contenido","NATURAL");
      },
      complete: function() {} // Callback for Modal close
    }
  );
}

function cambiarTipoPersona(clase,id){
    var formulario = $("#"+clase);
    var i = -1;
    var html = "";
    if(id=="JURIDICA"){
        i = 0;
        html = '<input type="hidden" id="txtId" name="txtId" value=""><div class="row"><div class="col l6"><div class="input-field inline"><input type="text" id="txtNroDoc" name="txtNroDoc" value="" size="11" maxlength="11" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) return false;" onblur="verificaNroDoc(this.value,$(\'#cboTipoPersona\').val());" title="Debe ingresar un número de documento"><label for="txtNroDoc">Numero RUC</label></div><label id="LabelVerificaNroDoc" hidden="" class="left" style="color: #003399">El Número de Documento ya existe</label></div><button type="button" id="btnBuscarRuc" onclick="consultaRUC();" class="btn btn-floating left"><i class="material-icons">find_replace</i></button></div><div class="row"><div class="col s12"><div class="input-field inline"><input type="text" id="txtNombres" name="txtNombres" value="" style="text-transform:uppercase" title="Debe ingresar un nombre o razón social"><label for="txtNombres">Razon Social</label></div></div></div><div class="row"><div class="col s12"><div class="input-field inline"><input type="text" id="txtDireccion" name="txtDireccion" value="" style="text-transform:uppercase" title="Direccion"><label for="txtDireccion">Direccion</label></div></div></div>';
    }else if(id=="NATURAL"){
        i = 1;
        html = '<input type="hidden" id="txtId" name="txtId" value=""><div class="row"><div class="col l6"><div class="input-field inline"><input type="text" id="txtNroDoc" name="txtNroDoc" value="" size="11" maxlength="11" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) return false;" onblur="verificaNroDoc(this.value,$(\'#cboTipoPersona\').val());" title="Debe ingresar un número de documento"><label for="txtNroDoc">Numero RUC / DNI</label></div><label id="LabelVerificaNroDoc" hidden="" class="left" style="color: #003399">El Número de Documento ya existe</label></div><button type="button" id="btnBuscarRuc" onclick="consultaRUC();" class="btn btn-floating left"><i class="material-icons">find_replace</i></button></div><div class="row"><div class="col s12"><div class="input-field inline"><input type="text" id="txtNombres" name="txtNombres" value="" style="text-transform:uppercase" title="Debe ingresar un nombre o razón social"><label for="txtNombres">Apellidos y Nombres / Razon Social</label></div></div></div><div class="row" hidden=""><div class="col s12"><div class="input-field inline"><input type="text" id="txtApellidos" name="txtApellidos" value="" style="text-transform:uppercase"><label for="txtApellidos">Apellidos</label></div></div></div><div class="row" hidden=""><div class="col s4 m2 l2"><p>SEXO</p></div><div class="col s4 m5 l5"><p><input name="optSexo" type="radio" id="optM" value="M" checked="checked"><label for="optM">MASCULINO</label></p></div><div class="col s4 m5 l5"><p><input type="radio" name="optSexo" value="F" id="optF"><label for="optF">FEMENINO</label></p></div></div><div class="row"><div class="col s12"><div class="input-field inline"><input type="text" id="txtDireccion" name="txtDireccion" value="" style="text-transform:uppercase" title="Direccion"><label for="txtDireccion">Direccion</label></div></div></div><input type="hidden" id="txtFechaNac" name="txtFechaNac" value="" size="10" maxlength="10">';
    }else if(id=="VARIOS"){
        i = 2;
        html = '<input type="hidden" id="txtId" name="txtId" value=""><div class="row"><div class="col l6"><div class="input-field inline"><input type="text" id="txtNroDoc" name="txtNroDoc" value="" size="11" maxlength="11" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) return false;" onblur="verificaNroDoc(this.value,$(\'#cboTipoPersona\').val());" title="Debe ingresar un número de documento"><label for="txtNroDoc">Numero DNI</label></div><label id="LabelVerificaNroDoc" hidden="" class="left" style="color: #003399">El Número de Documento ya existe</label></div><button type="button" id="btnBuscarRuc" onclick="consultaRUC();" class="btn btn-floating left"><i class="material-icons">find_replace</i></button></div><div class="row"><div class="col s12"><div class="input-field inline"><input type="text" id="txtNombres" name="txtNombres" value="" style="text-transform:uppercase" title="Debe ingresar un nombre o razón social"><label for="txtNombres">Nombres</label></div></div></div><div class="row"><div class="col s12"><div class="input-field inline"><input type="text" id="txtApellidos" name="txtApellidos" value="" style="text-transform:uppercase"><label for="txtApellidos">Apellidos</label></div></div></div><div class="row" hidden=""><div class="col s4 m2 l2"><p>SEXO</p></div><div class="col s4 m5 l5"><p><input name="optSexo" type="radio" id="optM" value="M" checked="checked"><label for="optM">MASCULINO</label></p></div><div class="col s4 m5 l5"><p><input type="radio" name="optSexo" value="F" id="optF"><label for="optF">FEMENINO</label></p></div></div><div class="row"><div class="col s12"><div class="input-field inline"><input type="text" id="txtDireccion" name="txtDireccion" value="" style="text-transform:uppercase" title="Direccion"><label for="txtDireccion">Direccion</label></div></div></div><input type="hidden" id="txtFechaNac" name="txtFechaNac" value="" size="10" maxlength="10">';
    }
    formulario.html(html);
}
function modalDividirCuenta(){
    $('#modalDividirCuenta').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      starting_top: '4%', // Starting top style attribute
      ending_top: '10%', // Ending top style attribute
      ready: function(modal, trigger) {},
      complete: function() {} // Callback for Modal close
    }
  );
}
function modalDetalleCerrarCaja(modo){
    $("#contenidoModalDetalleCerrar").empty();
    $("#tituloModalDetalleCerrar").empty();
    $("#cantModalDetalleCerrar").empty();
    $("#totalModalDetalleCerrar").empty();
    $.ajax({
        type: "POST",
        url: "controlador/contMovCaja.php?id_clase=5",        
        data:"accion=MODALCIERRE&modo="+modo,
        success: function(a) {
            var data=(JSON.parse(a));
            var tabla = "";
            var numero = 0;
            var total = 0;
            $(data).each(function (key,val){
                numero++;
                total = total + parseFloat(val[3]);
                tabla = tabla + "<tr>";
                $(val).each(function (key2,val2){
                    tabla = tabla + "<td class='center'>"+val2+"</td>";
                });
                /*tabla = tabla + "<td class='center'>"+val[0]+"</td>";
                tabla = tabla + "<td class='center'>"+val[1]+"</td>";
                tabla = tabla + "<td class='center'>"+val[2]+"</td>";
                tabla = tabla + "<td class='center'>"+val[3]+"</td>";
                tabla = tabla + "<td class='center'>"+val[4]+"</td>";*/
                tabla = tabla + "</tr>";
            });
            $("#contenidoModalDetalleCerrar").html(tabla);
            $("#cantModalDetalleCerrar").html(numero);
            $("#totalModalDetalleCerrar").html(total);
            $('#modalDetalleCerrarCaja').openModal({
                dismissible: true, // Modal can be dismissed by clicking outside of the modal
                opacity: .5, // Opacity of modal background
                in_duration: 300, // Transition in duration
                out_duration: 200, // Transition out duration
                starting_top: '4%', // Starting top style attribute
                ending_top: '10%', // Ending top style attribute
                ready: function(modal, trigger) {
                    var boton = '<button type="button" class="btn-floating red right modal-close" onclick="$(\'#modalDetalleCerrarCaja\').closeModal();"><i class="material-icons">clear</i></button><button type="button" class="btn-floating green right" onclick="ImprimirModal();"><i class="material-icons">print</i></button>';
                    var titulo = "";
                    $("#headerModalDetalleCerrar").html('<tr><th class="center">N°</th><th class="center">Descripcion</th><th class="center">Concepto</th><th class="center">Monto</th><th class="center">Fecha</th></tr>');
                    if(modo == "EFECTIVO"){
                        titulo = "LISTADO DE VENTAS EN EFECTIVO";
                    }else if(modo == "TARJETAS"){
                        titulo = "LISTADO DE VENTAS CON TARJETA";
                    }else if(modo == "TOTAL"){
                        titulo = "LISTADO TOTAL DE VENTAS";
                        $("#headerModalDetalleCerrar").html('<tr><th class="center">N°</th><th class="center">Descripcion</th><th class="center">Concepto</th><th class="center">Total</th><th class="center">Modo de pago</th><th class="center">Efectivo</th><th class="center">Visa</th><th class="center">Mastercard</th><th class="center">Fecha</th></tr>');
                    }else if(modo == "INGRESOS"){
                        titulo = "LISTADO DE INGRESOS";
                    }else if(modo == "GASTOS"){
                        titulo = "LISTADO DE GASTOS";
                    }
                    $("#tituloModalDetalleCerrar").html(titulo+boton);
                },
                complete: function() {} // Callback for Modal close
            });
        }
    });
}
function alerta(texto,tiempo,modo,funcion){
    if(tiempo==null){
        tiempo=4000;
    }
    if(modo==null){
        modo="";
    }
    if(funcion==null){
        funcion = function(){};
    }
    Materialize.toast(texto, tiempo,modo,funcion);
}
function redondear(numero,decimales){
    return parseFloat(numero).toFixed(decimales);
}
var divCargando='<div class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div>';