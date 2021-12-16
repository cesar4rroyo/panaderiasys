<?php
session_start();
require("../modelo/clsMovimiento.php");
require("../modelo/clsPersona.php");
require("../modelo/clsSalon.php");
require("fun.php");
$nro_reg = 0;
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
    $nro_hoja = 1;
}
$order = $_GET["order"];
if(!$order){
    $order="descripcion,precioventa";
}
$by = $_GET["by"];
if(!$by){
    $by="1";
}


if(isset($_SESSION['R_carroPedidoMozo']))
    $_SESSION['R_carroPedidoMozo']="";
try{
    $objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objPersona = new clsPersona($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
    exit();
}
if($_GET["accion"]=="ACTUALIZAR"){
    $rst = $objMantenimiento->consultarMovimientoxMesa($_GET["idmesa"],5);
    $dato=$rst->fetchObject();
    $rst2 = $objMantenimiento->obtenerDataSQL("SELECT situacion FROM mesa WHERE idmesa = $dato->idmesa AND idsucursal = $dato->idsucursal");
    $situacion_mesa = $rst2->fetchObject();
    $situacion_mesa = $situacion_mesa->situacion;
    $rst2 = $objMantenimiento->obtenerDataSQL("SELECT count(*) as numero FROM detallemovalmacen_eliminado WHERE idmovimiento = $dato->idmovimiento AND idsucursal = $dato->idsucursal");
    $historial = $rst2->fetchObject();
    $historial = $historial->numero;
    $numero_personas = $objMantenimiento->obtenerDataSQL("SELECT nropersonas FROM movimientohoy WHERE idmovimiento = $dato->idmovimiento AND idsucursal = $dato->idsucursal")->fetchObject()->nropersonas;
}elseif($_GET["accion"]=="NUEVO"){
    $numero_personas = $objMantenimiento->obtenerDataSQL("SELECT nropersonas FROM mesa WHERE idmesa = ".$_GET["idmesa"]." AND idsucursal = ".$_SESSION["R_IdSucursal"])->fetchObject()->nropersonas;
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function inicio(){
    <?php if($_SESSION["R_IdPerfil"]==5){?>
    document.getElementById("url").value="vista/frmMozo";
    <?php }elseif($_SESSION["R_IdPerfil"]==4){?>
    document.getElementById("url").value="vista/frmCajero";
    $("#InptCliente").focus();
    <?php }?>
    document.getElementById("par").value="&id_clase=0";
    document.getElementById("div").value="frame";
    document.getElementById("msj").value="frame";
    document.getElementById("img").value="imgloading";
    document.getElementById("cargagrilla").innerHTML="";
    
    <? if($_GET["accion"]=="ACTUALIZAR"){
            echo "agregarDetalleProducto(".$_GET["idmesa"].");$('#btnCOBRAR').removeAttr('disabled');";
            if($situacion_mesa=="O"){
                echo "$('#btnJUNTAR').removeAttr('disabled');$('#btnUNIR').removeAttr('disabled');$('#btnMOVER').removeAttr('disabled');";
            }
            if($situacion_mesa=="C" && $_SESSION["R_IdPerfil"]==5){
                echo "$('#opciones').hide();$('#btnCOCINA').hide();";
            }
            if($situacion_mesa=="C" && $_SESSION["R_IdPerfil"]==4){
                echo "$('#btnJUNTAR').removeAttr('disabled');$('#btnUNIR').removeAttr('disabled');$('#btnMOVER').removeAttr('disabled');";
            }
            //echo "this.verpedido();";
        }else {?>
            this.generaNumero(<?=$_SESSION['R_IdSucursalUsuario']?>+"-"+<?=$_SESSION['R_IdPersona']?>);
            $('#cargagrilla').show();
            $('#btnCOMANDA').attr("disabled",true);
            $('#btnCUENTA').attr("disabled",true);
     <? }?>
}

function platos(idcategoria,categoria,producto){
    vOrder = document.getElementById("order").value;
    vBy = document.getElementById("by").value;
    vValor = "'"+vOrder + "'," + vBy + ", 0, '"+producto+"',"+ idcategoria + ",'', '','P'";
    setRun('vista/listPlatos<?php if($_SESSION["R_ModoTablet"]=="PREDETERMINADO") echo "Predeterminado";?>','&mesa=<?=$_GET["mesa"]?>&categoria='+categoria+'&idcategoria='+idcategoria+'&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=45&filtro=' + vValor, 'cargagrilla', 'cargagrilla', 'img03');
    document.getElementById("cargagrilla").style.display="";
}

function eliminarMovimiento(idmovimiento){
    $('#modalEliminar').openModal({
        dismissible: true, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        in_duration: 300, // Transition in duration
        out_duration: 200, // Transition out duration
        starting_top: '4%', // Starting top style attribute
        ending_top: '10%', // Ending top style attribute
        ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
            $("#tituloModalEliminar").html("ELIMINAR EL PEDIDO");
            $("#txaMotivoEliminado").val("");
            $("#inptIdMovimiento").val(idmovimiento);
            $('#ButtonName').removeAttr('onclick');
            $('#modalEliminarBtnAceptar').attr('onClick', 'modalEliminarBtnAceptar("PEDIDO");');
        },
        complete: function() { 
            $("#tituloModalMesas").html("");
            $("#inptIdMovimiento").val("");
            $("#txaMotivoEliminado").val("");
            $('#modalEliminarBtnAceptar').removeAttr('onclick');
        }
      }
    );
}

function quitar(idproducto,idsucursalproducto){
    var recipiente = document.getElementById('divDetallePedido');
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "quitarProductoMozo");
    g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").value);
    g_ajaxPagina.setParameter("class","zoom12");
    g_ajaxPagina.setParameter("IdProducto", idproducto);
    g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
    g_ajaxPagina.response = function(text){
        recipiente.innerHTML = text;
        recipiente.focus();
        document.getElementById("divTotal").innerHTML="IMPORTE TOTAL S/."+document.getElementById("txtTotal").value;
        document.getElementById("divTotalProducto").innerHTML="NUMERO DE PRODUCTOS: "+document.getElementById("txtTotalProducto").value;
        $("#tbModalDetalle").html($("#divDetallePedido").html());
        $('#txtDinero').val($('#txtTotal').val());
        calcularVuelto();
    };
    g_ajaxPagina.request();
}
function quitarActual(idproducto,idsucursalproducto,iddetallemovalmacen){
    $('#modalEliminar').openModal({
        dismissible: true, // Modal can be dismissed by clicking outside of the modal
        opacity: .5, // Opacity of modal background
        in_duration: 300, // Transition in duration
        out_duration: 200, // Transition out duration
        starting_top: '4%', // Starting top style attribute
        ending_top: '10%', // Ending top style attribute
        ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
            $("#tituloModalEliminar").html("ELIMINAR UN DETALLE");
            $("#inptIdDetalleMov").val(iddetallemovalmacen);
            $("#txaMotivoEliminado").val("");
            $("#inptIdProducto").val(idproducto);
            $("#inptIdSucursalProducto").val(idsucursalproducto);
            $('#ButtonName').removeAttr('onclick');
            $('#modalEliminarBtnAceptar').attr('onClick', 'modalEliminarBtnAceptar("DETALLE");');
        },
        complete: function() { 
            $("#tituloModalMesas").html("");
            $("#inptIdDetalleMov").val("");
            $("#txaMotivoEliminado").val("");
            $("#inptIdProducto").val("");
            $("#inptIdSucursalProducto").val("");
            $('#ButtonName').removeAttr('onclick');
        }
      }
    );
}

function agregarDetalleProducto(idmesa){
        if(idmesa!=0){
            var recipiente = document.getElementById('divDetallePedido');
            g_ajaxPagina = new AW.HTTP.Request;
            g_ajaxPagina.setURL("vista/ajaxPedido.php");
            g_ajaxPagina.setRequestMethod("POST");
            g_ajaxPagina.setParameter("accion", "agregarDetallesProductoMozo");
            g_ajaxPagina.setParameter("class", "zoom");
            g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").value);
            //g_ajaxPagina.setParameter("idmovimiento", idmovimiento);
            g_ajaxPagina.setParameter("idmesa", idmesa);
            g_ajaxPagina.response = function(text){
                recipiente.innerHTML = text;
                var contenedorTxtId = $('#txtId');
                if($.isArray(contenedorTxtId)){
                    contenedorTxtId = contenedorTxtId[0];
                    console.log("ES ARRAY");
                }
                //contenedorTxtId.val($("#txtidmov").val());
                //alert($("#txtidmov").val());
                $('#txtId').val($("#txtidmov").val());
                document.getElementById("divTotal").innerHTML="IMPORTE TOTAL S/."+document.getElementById("txtTotal").value;
                document.getElementById("divTotalProducto").innerHTML="NUMERO DE PRODUCTOS: "+document.getElementById("txtTotalProducto").value;
                //document.getElementById("divimprimircomanda").style.display="none";
            };
            g_ajaxPagina.request();
        }else{
            alert("Error en mesa");
        }
}

function generaNumero(idmesero){
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "generaNumero");
    g_ajaxPagina.setParameter("IdMesero", idmesero);
    g_ajaxPagina.response = function(text){
        eval(text);
        document.getElementById('nrocomanda').innerHTML="COMANDA NUMERO: "+vnumero;
        document.getElementById('txtNumeroComanda').value=vnumero;
        //asignar();
    };
    g_ajaxPagina.request();
}
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
    <?php if($_SESSION["R_IdPerfil"]==4){?>
    g_ajaxGrabar.setParameter("accion", "NUEVO2");
    g_ajaxGrabar.setParameter("clase", "45");
    g_ajaxGrabar.setParameter("Nropersonas",$('#numeropersonas').val());
    g_ajaxGrabar.setParameter("Idmesa","<?=$_GET["idmesa"]?>");
    g_ajaxGrabar.setParameter("txtNumeroComanda",document.getElementById('txtNumeroComanda').value);
    g_ajaxGrabar.setParameter("txtTotal",document.getElementById('txtTotal').value);
    g_ajaxGrabar.setParameter("txtId",document.getElementById("txtId").value);
    g_ajaxGrabar.setParameter("mesa","<?=$_GET["mesa"]?>");
    g_ajaxGrabar.setParameter("idusuario",$("#slcIdUsuario").val());
    g_ajaxGrabar.setParameter("idpersona",$("#optnSlcIdUsuario_"+$("#slcIdUsuario").val()).attr("id_persona"));
    g_ajaxGrabar.setParameter("cliente",$("#InptCliente").val());
    g_ajaxGrabar.setParameter("comentario",$("#txaComentario").val());
    <?php }else{?>
    g_ajaxGrabar.setParameter("accion", "NUEVO3");
    g_ajaxGrabar.setParameter("clase", "45");
    g_ajaxGrabar.setParameter("Nropersonas",$('#numeropersonas').val());
    g_ajaxGrabar.setParameter("Idmesa","<?=$_GET["idmesa"]?>");
    g_ajaxGrabar.setParameter("txtNumeroComanda",document.getElementById('txtNumeroComanda').value);
    g_ajaxGrabar.setParameter("txtTotal",document.getElementById('txtTotal').value);
    g_ajaxGrabar.setParameter("txtId",document.getElementById("txtId").value);
    g_ajaxGrabar.setParameter("mesa","<?=$_GET["mesa"]?>");
    g_ajaxGrabar.setParameter("idusuario",$("#slcIdUsuario").val());
    g_ajaxGrabar.setParameter("idpersona",$("#optnSlcIdUsuario_"+$("#slcIdUsuario").val()).attr("id_persona"));
    <?php }?>
    getFormData("frmComanda");
}

var cuenta=0;
function enviado() {
    if (cuenta == 0){
        cuenta++;
        return true;
    }else{
        alert("A enviado dos veces guardar, espere un momento");
        return false;
    }
}

function aceptar(){
    var idmov=document.getElementById("txtId").value;
    if(document.getElementById('divDetallePedido').innerHTML!='' && document.getElementById('divDetallePedido').innerHTML!='&nbsp;&nbsp;&nbsp;Debe Agregar platos!!!'){
        if(document.getElementById('txtTotal')){
            if(parseFloat(document.getElementById('txtTotal').value)>0){
                if(enviado()){
                    g_ajaxGrabar.setURL("controlador/contPedidoMozo.php?ajax=true");
                    g_ajaxGrabar.setParameter("cuenta",cuenta);
                    g_ajaxGrabar.setRequestMethod("POST");
                    setParametros();
                    var comanda = $("#nrocomanda").html();
                    g_ajaxGrabar.response = function(text){
                        console.log(text);
                        loading(false, "loading");
                        if(text!='La mesa está ocupada'){
                            eval(text);
                            if(vmsg=="Guardado correctamente"){
                                // imprimirTicket(vidpedido,comanda);
                                setRun("vista/frmComanda","&idmesa=<?=$_GET["idmesa"]?>&mesa=<?=$_GET["mesa"]?>&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
                            }else{
                                alert("Error al momento de registrar el pedido, porfavor vuelva a intentar registrar su pedido");
                            }
                            if(text=="Apertura"){
                                alert("Falta apertura de caja, consulte con su cajero si se realizo la apertura de caja");
                            }
                            //atras();
                        }
                    };
                    g_ajaxGrabar.request();
                }
                loading(true, "loading", "frame", "line.gif",true);
            }else{
               if(parseFloat(document.getElementById('txtTotal').value)==-1){
                  alert("Debe agregar mas platos");
               }else{
                  alert("Debe indicar los productos");  
               }
            }
        }else{
            alert("Debe indicar los productos");
        }
    }else{
            alert("Debe indicar los productos");
    }
}

function aceptarCajero(){
    
    if($('#chbxOTROS').is(":checked")){
        let efectivo = $('#txtMontoEfectivoVarios').val();
        let yape = $('#txtMontoYapeVarios').val();
        let plin = $('#txtMontoPlinVarios').val();
        let visa = $('#txtMontoVisaVarios').val();
        efectivo = efectivo == '' || isNaN(efectivo) ? 0 : efectivo;
        yape = yape == '' || isNaN(yape) ? 0 : yape;
        plin = plin == '' || isNaN(plin) ? 0 : plin;
        visa = visa == '' || isNaN(visa) ? 0 : visa;
        let total = visa + plin + yape + efectivo;
        
        if(isNaN(efectivo) || isNaN(yape) || isNaN(plin) || isNaN(visa)){
            alert("Debe ingresar un valor numerico");
            return false;
        }
        if(parseFloat(efectivo) + parseFloat(yape) + parseFloat(plin) + parseFloat(visa) != $('#txtTotal').val()){
            alert("Los suma de montos son diferentes al total");
            return false;
        }
    }
    var tot2 = parseFloat(document.getElementById('txtTotal').value);
    $("#modalDetalle").closeModal();
    if(document.getElementById('divDetallePedido').innerHTML!='' && document.getElementById('divDetallePedido').innerHTML!='&nbsp;&nbsp;&nbsp;Debe Agregar platos!!!'){
        if(document.getElementById('txtTotal')){
            if($("#tbpaginaweb tr").length>1 && $("#txtIdPersona").val()!="0" && $("#txtIdPersona").val()!="" && $("#txtValidarRef").val()=="S" && validarPago()){
                if(enviado()){
                    g_ajaxGrabar.setURL("controlador/contPedidoMozo.php?ajax=true");
                    g_ajaxGrabar.setParameter("cuenta",cuenta);
                    g_ajaxGrabar.setRequestMethod("POST");
                    setParametros();
                    var mesero = $("#slcIdUsuario option:selected").text();
                    var idtipodocumento = $("#cboIdTipoDocumento").val();
                    g_ajaxGrabar.response = function(text){
                        loading(false, "loading");
                        console.log(text);
                        if(text!='La mesa está ocupada'){
                            if(text=="Apertura"){
                                alert("Falta apertura de caja, realizar apertura de caja");
                                setRun("vista/frmComanda","&idmesa=<?=$_GET["idmesa"]?>&mesa=<?=$_GET["mesa"]?>&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
                                return false;
                            }
                            eval(text);
                            if(vmsg=="Guardado correctamente"){
                                <?php
                                //if($_SERVER['REMOTE_ADDR']!="192.168.1.56" && $_SERVER['REMOTE_ADDR']!="192.168.1.57"){
                                ?>
                                if(tot2>0.9){
                                    // imprimirTicket(vidpedido,vcomanda,idtipodocumento,mesero);
                                }
                                <?php
                                //}
                                ?>
                                if(idtipodocumento!="19"){
                                    // declarar(vidventa,idtipodocumento);
                                }else{
                                    if(tot2>0.9){
                                        // imprimir2(vidventa);
                                    }
                                }
                                setRun("vista/frmComanda","&idmesa=<?=$_GET["idmesa"]?>&mesa=<?=$_GET["mesa"]?>&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
                            }else{
                                alert("Error al momento de registrar el pedido, porfavor vuelva a intentar registrar su pedido");
                            }
                        }
                    };
                    g_ajaxGrabar.request();
                }
                loading(true, "loading", "frame", "line.gif",true);
            }else{
                if(validarPago()){
                    if($("#txtIdPersona").val()=="0" || $("#txtIdPersona").val()==""){
                        alert("Debe indicar una persona");
                    }else if($("#txtValidarRef").val()!="S"){
                        alert("Ref de Visa o Master ya registrado");
                    }else{ 
                        if(parseFloat(document.getElementById('txtTotal').value)==-1){
                            alert("Debe agregar mas platos");
                        }else{
                            alert("Debe indicar los productos");  
                        }
                    }
                }else{
                    alert("Debo ingresar un monto correcto");
                }
            }
        }else{
            alert("Debe indicar los productos");
        }
    }else{
        alert("Debe indicar los productos");
    }
}

function imprimir2(idventa){
    var g_ajaxPagina4 = new AW.HTTP.Request;
    g_ajaxPagina4.setURL("http://localhost/lasmusas78872387/vista/ajaxPedido.php");
    g_ajaxPagina4.setRequestMethod("POST");
    g_ajaxPagina4.setParameter("accion", "imprimir_ventaelectronica");
    g_ajaxPagina4.setParameter("idventa",idventa);
    g_ajaxPagina4.response = function(text){
        //alert("imprimiendo");         
    };
    g_ajaxPagina4.request();
    /*$.ajax({
        //headers: {"X-My-Custom-Header": "OK"},
        url: "http://localhost/lasmusas78872387/vista/ajaxPedido.php",
        type: 'GET',
        data: "accion=imprimir_ventaelectronica&idventa="+idventa,
        crossDomain: true,
        headers: {
            'Access-Control-Allow-Origin': '*',
        },
        beforeSend: function(xhr){
                xhr.withCredentials = true;
          },
        success: function(a) {
            console.log(a);
        }
    });*/
    /*var g_ajaxPagina4 = new AW.HTTP.Request;
    g_ajaxPagina4.setURL("vista/ajaxPedido.php");
    g_ajaxPagina4.setRequestMethod("POST");
    g_ajaxPagina4.setParameter("accion", "EnviarURL");
    g_ajaxPagina4.setParameter("url", "http://localhost/lasmusas78872387/vista/ajaxPedido.php?accion=imprimir_ventaelectronica&idventa="+idventa);
    g_ajaxPagina4.response = function(text){
        console.log(text);
    }
    g_ajaxPagina4.request();*/
}

function declarar(idventa,idtipodocumento){
    if(idtipodocumento==4){
        var vaccion='enviarBoleta';
    }else{
        var vaccion='enviarFactura';
    }
    var g_ajaxPagina2 = new AW.HTTP.Request;
    g_ajaxPagina2.setURL("controlador/contComprobante.php");
    g_ajaxPagina2.setRequestMethod("GET");
    g_ajaxPagina2.setParameter("funcion", vaccion);
    g_ajaxPagina2.setParameter("idventa",idventa);
    g_ajaxPagina2.response = function(text){
        imprimir2(idventa);
        console.log(text);
    };
    g_ajaxPagina2.request();
}

function imprimirTicket(idmov,comanda,idtipodocumento,mesero){
    /*g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "imprimir_ticket");
    g_ajaxPagina.setParameter("mesa","<?=$_GET["mesa"]?>");
    g_ajaxPagina.setParameter("numerocomanda",comanda);   
    g_ajaxPagina.setParameter("txtId",idmov);     
    g_ajaxPagina.response = function(text){
        console.log(text);
        console.log(idtipodocumento);*/
        //if(idtipodocumento!="4"){
            imprimircuenta2(idmov,"<?=$_GET["mesa"]?>",comanda,mesero);
            console.log(idtipodocumento);
        //}
    /*};
    g_ajaxPagina.request();*/
}

function imprimircuenta2(vidpedido,mesa,numero,mesero){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("http://localhost/lasmusas78872387/vista/ajaxPedido.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "imprimir_cuenta");
    g_ajaxPagina3.setParameter("idmovimiento",vidpedido);
    g_ajaxPagina3.setParameter("mesa",mesa);
    g_ajaxPagina3.setParameter("numerocomanda",numero);
    g_ajaxPagina3.setParameter("mesero",mesero);
    g_ajaxPagina3.response = function(text){
      console.log(text);
    };
    g_ajaxPagina3.request();
}

function imprimircuenta3(vidpedido){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("vista/ajaxPedido.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "imprimir_cuenta2");
    g_ajaxPagina3.setParameter("idmovimiento",vidpedido);
    g_ajaxPagina3.response = function(text){
      console.log(text);
    };
    g_ajaxPagina3.request();
}

this.inicio();

//document.getElementById("blokeador").style.display='';
document.getElementById("blokeador").style.height=document.body.clientHeight+'px';
document.getElementById("blokeador").style.width=document.body.clientWidth+'px';

function genera_cboMesas(idsalon){
    var recipiente = document.getElementById('divdiagramaMesa');
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "genera_diagramaMesasMozo");
    g_ajaxPagina.setParameter("IdSalon", idsalon);
    g_ajaxPagina.response = function(text){
        recipiente.innerHTML = text;            
    };
    g_ajaxPagina.request();
}
//genera_cboMesas(1);

function centraDivSucursal(){ 
    if(document.getElementById("txtId").value!=0){
    var top=(document.body.clientHeight/8)+"px"; 
    var left1=(document.body.clientWidth/4);
    var left=(left1-parseInt(document.getElementById("DivSucursal").style.width)/2)+"px"; 
    document.getElementById("DivSucursal").style.top=top; 
    //document.getElementById("DivSucursal").style.left=left;
    document.getElementById("blokeador").style.display='';
    document.getElementById("blokeador").style.height=document.body.clientHeight+'px';
    document.getElementById("blokeador").style.width=document.body.clientWidth+'px';
    document.getElementById("DivSucursal").style.display='';
    //setRun("vista/frmMozo","&id_clase=46","frame","frame","imgloading");
    }else{
        alert("Debe tener productos ya atendidos para cambiar a otra mesa");
    }
} 

function listadoPersona2(){
    $.ajax({
        url: "vista/ajaxPersonaMaestro.php",
        type: 'POST',
        data: "accion=BuscaPersonaJSON&idrol=1,3,4,5&nombres=&tipopersona=DNI&modo="+$("#txtModoPersona").val(),
        success: function(a) {
            a = JSON.parse(a);
            var datos = a.datos;
            $("#txtPersona").autocomplete({
                data: datos
            },selecctionarPersona,"");
        }
    });
}

function selecctionarPersona(dato){
    var ids = dato.split("|");
    $('#txtIdSucursalPersona').val(ids[0]);
    $('#txtIdPersona').val(ids[1]);
    $('#txtPersona').attr("readonly",true);
}

function limpiarCamposPersona(){
    $('#txtIdSucursalPersona').val("");
    $('#txtIdPersona').val("");
    $('#txtPersona').attr("readonly",false);
    $('#txtPersona').val("");
    $('#txtPersona').focus();
}

function asignar(){
    if(document.getElementById('cboIdTipoDocumento').value=="5"){
        document.getElementById('txtIdSucursalPersona').value="";
        document.getElementById('txtIdPersona').value="";
        document.getElementById('txtPersona').value="";
        $('#txtIdSucursalPersona').val("");
        $('#txtIdPersona').val("");
        $('#txtPersona').val("");
        $('#txtPersona').attr("readonly",false);
    }else{
        $('#txtIdSucursalPersona').val("<?php echo $_SESSION['R_IdSucursal']?>");
        $('#txtIdPersona').val("3");
        $('#txtPersona').val("VARIOS");
        $('#txtPersona').attr("readonly",true);
        $('#lblPersona').addClass("active");
    }
}

var selectTipoTarjeta = "<?php echo genera_cboGeneralSQL("select * from tipotarjeta order by idtipotarjeta",'TipoTarjeta','','',$objSalon); ?>"+'<label class="labelSuperior">Tipo de Tarjeta</label>';

listadoPersona2();
asignar();

function verificarmesa(numero,idsalon){
    if(confirm("Desea mover el pedido a la mesa "+numero + " ?")){
        g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "verificarmesa");
		g_ajaxPagina.setParameter("txtMesa",numero);
        g_ajaxPagina.setParameter("idsalon",idsalon);
		g_ajaxPagina.response = function(text){
		    eval(text);
            aceptarCambioMesa(vidmesa);                      
		};
		g_ajaxPagina.request();
    }
}

function aceptarCambioMesa(idmesa){
    var g_ajaxGrabar = new AW.HTTP.Request;  
    g_ajaxGrabar.setURL("controlador/contPedidoMozo.php");
    g_ajaxGrabar.setRequestMethod("POST");
    g_ajaxGrabar.setParameter("accion", "CAMBIARMESA");
    g_ajaxGrabar.setParameter("idmesa", idmesa);
    g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);

    g_ajaxGrabar.response = function(text){
        loading(false, "loading");
        document.getElementById("DivSucursal").style.display='none';
        document.getElementById("blokeador").style.display='none';
        alert(text);
        atras();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "frame", "line.gif",true);
}

<? if($_GET["accion"]=="ACTUALIZAR"){
        if($_SESSION["R_IdPerfil"]==5){
            if($situacion_mesa=="C"){
                echo 'agregarplatosMozo(false);';
            }else{
                echo 'agregarplatosMozo(true);';
            }
        }else{
            echo 'agregarplatos("C");';
        }
        
    }else{
        //echo 'agregarplatos("C");';
    }
?>
    
function modalPropiedades2(id,vval){
    var d = vval.split("@");
    modalPropiedades(d[0],d[1],'',0,d[2],'Nuevo');
    $("#cboCategoria"+id).val('0');
}

function validarMontoEfectivo(){
    if(parseFloat($('#txtTotal').val()) < parseFloat($('#txtPagoEfectivo').val()) || $('#txtPagoEfectivo').val()==''){ 
        alert('MONTO INCORRECTO');
        $('#txtPagoEfectivo').val('0');
    }
}

function generaNumeroVenta(idtipodocumento){
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxVenta.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "generaNumeroElectronico");
    g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
    g_ajaxPagina.response = function(text){
        eval(text);
        document.getElementById('txtNumeroVenta').value=vnumero;
    };
    g_ajaxPagina.request();
}

function asignar(){
    if(document.getElementById('cboIdTipoDocumento').value=="5"){
        $("#divChkImpuesto").hide();
        document.getElementById('txtIdSucursalPersona').value="";
        document.getElementById('txtIdPersona').value="";
        document.getElementById('txtPersona').value="";
        $('#txtIdSucursalPersona').val("");
        $('#txtIdPersona').val("");
        $('#txtPersona').val("");
        $('#txtPersona').attr("readonly",false);
    }else{
        $("#divChkImpuesto").hide();
        $('#txtIdSucursalPersona').val("<?php echo $_SESSION['R_IdSucursal']?>");
        $('#txtIdPersona').val("3");
        $('#txtPersona').val("VARIOS");
        $('#txtPersona').attr("readonly",true);
        $('#lblPersona').addClass("active");
    }
    //calcularVuelto();
}

generaNumeroVenta(19);

function aceptarDirecto(){
    seleccionar($('#txtIdProducto').val(),$('#txtIdSucursalProducto').val(),$('#cantidad-producto').html());
    setTimeout('aceptarCajero()',1000);
}

function setParametrosModalPersona(){
    g_ajaxGrabar.setParameter("accion", "NUEVO");
    g_ajaxGrabar.setParameter("clase", "23");
    //g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
    g_ajaxGrabar.setParameter("txtIdPersona", "");
    g_ajaxGrabar.setParameter("txtIdSucursal", "1");
    g_ajaxGrabar.setParameter("txtIdPersonaMaestro", "");
    g_ajaxGrabar.setParameter("txtDireccion", $("#txtDireccion").val());
    g_ajaxGrabar.setParameter("txtEmail", "");
    g_ajaxGrabar.setParameter("txtTelefonoFijo", "");
    g_ajaxGrabar.setParameter("txtTelefonoMovil", "");
    g_ajaxGrabar.setParameter("cboDpto", "1347");
    g_ajaxGrabar.setParameter("cboProv", "1348");
    g_ajaxGrabar.setParameter("cboDist", "1349");
    g_ajaxGrabar.setParameter("txtImagen", "");
    g_ajaxGrabar.setParameter("chkCompartido", "N");
    g_ajaxGrabar.setParameter("cboIdRol", "5");
    g_ajaxGrabar.setParameter("txtApellidos", $("#txtApellidos").val());
    g_ajaxGrabar.setParameter("txtNombres", $("#txtNombres").val());
    g_ajaxGrabar.setParameter("cboTipoPersona", $("#cboTipoPersona").val());
    g_ajaxGrabar.setParameter("txtNroDoc", $("#txtNroDoc").val());
    if($("#optM").length>1){
        if(document.getElementById("optM").checked){
            g_ajaxGrabar.setParameter("optSexo", "M");
        }
        if(document.getElementById("optF").checked){
            g_ajaxGrabar.setParameter("optSexo", "F");
        }
    }else{
        g_ajaxGrabar.setParameter("optSexo", "");
    }
    g_ajaxGrabar.setParameter("txtFechaNac", "");
}

function aceptarModalPersona(){
    g_ajaxGrabar.setURL("controlador/contPersona.php?ajax=true");
    g_ajaxGrabar.setRequestMethod("POST");
    setParametrosModalPersona();
    g_ajaxGrabar.response = function(text){
        loading(false, "loading");
        alert(text);
        listadoPersona2();
        $('#modalNuevoPersona').closeModal();
        $("#txtPersona").val("");
        $("#txtPersona").removeAttr("readonly");
        $("#txtPersona").focus();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "contenido", "line.gif",true);
}

function verificaNroDoc(nro,tipo){
    var g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "verificaNroDoc");
    g_ajaxPagina.setParameter("nrodoc", nro);
    g_ajaxPagina.setParameter("tipo", tipo);
    g_ajaxPagina.response = function(text){
        eval(text);
        if(vCant>0){
            $("#LabelVerificaNroDoc").show();
            $("#btnAceptarModalPersona").attr("disabled");
        }else{
            $("#LabelVerificaNroDoc").hide();
            $("#btnAceptarModalPersona").removeAttr("disabled");
        }
        console.log(text);
    };
    g_ajaxPagina.request();
}

function consultaRUC(){
    var ruc = $("#txtNroDoc").val();
    $.ajax({
        type: 'GET',
        url: "https://comprobante-e.com/facturacion/buscaCliente/BuscaClienteRuc.php",
        data: "fe=N&token=qusEj_w7aHEpX&ruc="+ruc,
        beforeSend(){
            alert("Consultando...");
        },
        success: function (data, textStatus, jqXHR) {
            data = JSON.parse(data);
            alert("Datos Recibidos");
            $("#txtNombres").val(data.RazonSocial);
            $("#txtDireccion").val(data.Direccion);
            $("#txtNombres").focus();
            $("#txtDireccion").focus();
        }
    });
}

var montos  = [];
function BotonesDinero(monto){
    var actual = Number($('#txtDinero').val());
    var nuevo = Number(monto) + actual;
    montos.push(actual);
    $('#txtDinero').val(nuevo);
}
function MontoAnterior(){
    //console.log(montos);
    if(montos.length>0){
        var ultimo = $('#txtDinero').val();
        while($('#txtDinero').val()==ultimo && montos.length>=0){
            var ultimo = montos.pop();
        }
        $('#txtDinero').val(ultimo);
    }
}


function calcularVuelto(){
    if(document.getElementById("txtDinero").value!=""){
        if($('#chbxAMBOS').is(':checked')){
            var vuelto = parseFloat(document.getElementById("txtDinero").value) + parseFloat(document.getElementById("txtMontoMastercard").value) + parseFloat(document.getElementById("txtMontoVisa").value)  - parseFloat(document.getElementById("txtTotal").value);
            vuelto=Math.round(vuelto*100)/100;
            document.getElementById("txtVuelto").value=vuelto;
        }else{
            if($('#cboIdTipoDocumento').val()==5){
                var vuelto = parseFloat(document.getElementById("txtDinero").value) - parseFloat(document.getElementById("txtTotal").value);
                vuelto=Math.round(vuelto*100)/100;
                document.getElementById("txtVuelto").value=vuelto;
            }else{
                var vuelto = parseFloat(document.getElementById("txtDinero").value) - parseFloat(document.getElementById("txtTotal").value);
                vuelto=Math.round(vuelto*100)/100;
                document.getElementById("txtVuelto").value=vuelto;
            }
        }
    }
}

function validarUsuario(modo){
    $('#modalValidarUsuario').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      ready: function(modal, trigger) {
          
      },
      complete: function() {} // Callback for Modal close
    });
}

function aceptarModalUsuario(inpt){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("vista/ajaxVenta.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "validarUsuario");
    g_ajaxPagina3.setParameter("pass",$("#txtPassword").val());
    g_ajaxPagina3.response = function(text){
        eval(text);
        if(vmsg=="S"){
            $("#"+inpt).val('S');
            $("#txtIdPersona").val('');
            $("#txtPersona").val('');
            $('#txtPersona').attr("readonly",false);
            $("#btnCredito").attr("disabled","true");
            $("#cboIdTipoDocumento").val('19');
            $("#cboIdTipoDocumento").material_select();
            generaNumeroVenta(19);
            alert('Validado');
            $("#modalValidarUsuario").closeModal();
        }else{
            alert('Clave incorrecta');
        }
    };
    g_ajaxPagina3.request();
}

function historicoVenta(){
    $('#modalHistorialVenta').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      ready: function(modal, trigger) {
          
      },
      complete: function() {} // Callback for Modal close
    });
}

function modalNumero(inpt){
    $('#modalNumero').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      ready: function(modal, trigger) {
          $("#txtInpt").val(inpt);
          $("#txtLetraNumero").val('');
      },
      complete: function() {} // Callback for Modal close
    });
}

function ingresar(numero){
    document.getElementById("txtLetraNumero").value=document.getElementById("txtLetraNumero").value + numero;
}

function vaciar(){
    document.getElementById("txtLetraNumero").value="";
}

function enviar(){
    $("#"+$("#txtInpt").val()).val($("#txtLetraNumero").val());
    $("#modalNumero").closeModal();
    if($("#txtInpt").val()=="txtDinero"){
        var din = parseFloat($("#txtDinero").val());
        var tot = parseFloat($("#txtTotal").val());
        if(din<=tot){
            $("#txtPagoEfectivo").val($("#txtDinero").val());
        }
    }
    if($("#txtInpt").val()=="txtDinero" || $("#txtInpt").val()=="txtMontoVisa" || $("#txtInpt").val()=="txtMontoMastercard"){
        var din = parseFloat($("#txtDinero").val());
        var vis = parseFloat($("#txtMontoVisa").val());
        var mas = parseFloat($("#txtMontoMastercard").val());
        var tot = parseFloat($("#txtTotal").val());
        if((din + vis + mas) >= tot){
            var efe = Math.round((tot - vis - mas)*100)/100;
            if(din>0){
                $("#txtPagoEfectivo").val(efe);
            }else{
                $("#txtPagoEfectivo").val('0');
            }
        }else{
            $("#txtPagoEfectivo").val(din);
        }
    }
    calcularVuelto();
}

function validarPago(){
    var din = parseFloat($("#txtDinero").val());
    var vis = parseFloat($("#txtMontoVisa").val());
    var mas = parseFloat($("#txtMontoMastercard").val());
    var tot = Math.round(parseFloat($("#txtTotal").val())*100)/100;
    if(Math.round((din+vis+mas)*100)/100>=tot){
        var efe = Math.round((tot - vis - mas)*100)/100;
        if(din>0){
            $("#txtPagoEfectivo").val(efe);
        }else{
            $("#txtPagoEfectivo").val('0');
        }
        return true;
    }else{
        return true;
    }
}

function aceptarModalVenta(){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("vista/ajaxVenta.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "validarUsuario");
    g_ajaxPagina3.setParameter("pass",$("#txtPassword2").val());
    g_ajaxPagina3.response = function(text){
        eval(text);
        //if(vmsg=="S"){
            var dat = $("#txtHistorialVenta").val();
            dat = dat.split("@");
            alert('Validado');
            if(dat[0]=="T"){//ticket
                imprimircuenta3(dat[1]);
            }else if(dat[0]=="C"){//comprobante
                imprimir2(dat[2]);
            }else if(dat[0]=="A"){//ambos
                imprimircuenta3(dat[1]);
                imprimir2(dat[2]);
            }else if(dat[0]=="E"){//anular
                anularVenta(dat[2]);
                $("#Venta-".dat[2]).css('color','red');
            }
            $("#modalHistorialVenta").closeModal();
            //$("#txtPassword2").val('');
        /*}else{
            alert('Clave incorrecta');
        }*/
    };
    g_ajaxPagina3.request();
}

function anularVenta(idventa){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("controlador/contVenta.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "ANULAR");
    g_ajaxPagina3.setParameter("txtId",idventa);
    g_ajaxPagina3.response = function(text){
        alert(text);
    };
    g_ajaxPagina3.request();
}

function modalDetalle(){
    $('#modalDetalle').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      ready: function(modal, trigger) {
          $("#tbModalDetalle").html($("#tbpaginaweb").html());
      },
      complete: function() {} // Callback for Modal close
    });
}

function modalDescuento(idproducto,producto,precioventa,idsucursalproducto){
    $('#modalDescuento').openModal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      in_duration: 300, // Transition in duration
      out_duration: 200, // Transition out duration
      ready: function(modal, trigger) {
          $("#txtProductoDesc").val(producto);
          $("#txtPrecioVentaDesc").val(precioventa);
          $("#txtIdProductoDesc").val(idproducto);
          $("#txtIdSucursalProductoDesc").val(idsucursalproducto);
      },
      complete: function() {} // Callback for Modal close
    });
}

function aceptarModalDescuento(inpt){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("vista/ajaxVenta.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "validarUsuario");
    g_ajaxPagina3.setParameter("pass",$("#txtPassword3").val());
    g_ajaxPagina3.response = function(text){
        //eval(text);
        //if(vmsg=="S"){
            actualizarPrecio($("#txtIdProductoDesc").val(),$("#txtPrecioVentaDesc").val(),$("#txtIdSucursalProductoDesc").val());
            //alert('Validado');
            $("#modalDescuento").closeModal();
            $("#txtPassword3").val("");
        /*}else{
            alert('Clave incorrecta');
        }*/
    };
    g_ajaxPagina3.request();
}

function actualizarPrecio(idproducto,precioventa,idsucursalproducto){
    var recipiente = document.getElementById('divDetallePedido');
    g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "actualizarProductoMozo");
    g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").value);
    g_ajaxPagina.setParameter("class","zoom12");
    g_ajaxPagina.setParameter("idproducto", idproducto);
    g_ajaxPagina.setParameter("idsucursalproducto", idsucursalproducto);
    g_ajaxPagina.setParameter("precioventa", precioventa);
    g_ajaxPagina.response = function(text){
        recipiente.innerHTML = text;
        recipiente.focus();
        document.getElementById("divTotal").innerHTML="IMPORTE TOTAL S/."+document.getElementById("txtTotal").value;
        document.getElementById("divTotalProducto").innerHTML="NUMERO DE PRODUCTOS: "+document.getElementById("txtTotalProducto").value;
        $("#tbModalDetalle").html($("#divDetallePedido").html());
        $("#txtPagoEfectivo").val($("#txtTotal").val());
        calcularVuelto();
    };
    g_ajaxPagina.request();
}

function validarAP(){
    var g_ajaxPagina3 = new AW.HTTP.Request;
    g_ajaxPagina3.setURL("vista/ajaxVenta.php");
    g_ajaxPagina3.setRequestMethod("POST");
    g_ajaxPagina3.setParameter("accion", "validarAP");
    g_ajaxPagina3.setParameter("visa",$("#txtGlosa").val());
    g_ajaxPagina3.setParameter("master",$("#txtGlosa1").val());
    g_ajaxPagina3.response = function(text){
        eval(text);
        $("#txtValidarRef").val(vmsg);
        if(vmsg=="S"){
            return true;
        }else{
            alert(vmsg);
            return false;
        }
    };
    g_ajaxPagina3.request();
}

function fnCambiarModalidad(){
    var modalidad = $("#tipoVenta").val();
    if(modalidad=="C"){
        $("#divModoPago").hide();
        $("#divEfectivo").hide();
        $("#divEfectivo2").hide();
        $(".anticipo").hide();
        $("#txtAnticipo").val("0");
    }else if(modalidad=="A"){
        $("#divModoPago").show();
        $("#divEfectivo").show();
        $("#divEfectivo2").show();
        $(".anticipo").show();
        $("#txtAnticipo").val("0");
        $("#idpagoanticipado").val("0");
        $.ajax({
            url: "controlador/contVenta.php",
            type: 'POST',
            data: "accion=MODALIDADVENTA&IdPedido=&modalidad="+modalidad,
            success: function(a) {
                a = JSON.parse(a);
                $('#modalModalidadVenta').openModal({
                    dismissible: true, // Modal can be dismissed by clicking outside of the modal
                    opacity: .5, // Opacity of modal background
                    in_duration: 300, // Transition in duration
                    out_duration: 200, // Transition out duration
                    starting_top: '4%', // Starting top style attribute
                    ending_top: '10%', // Ending top style attribute
                    ready: function(modal, trigger) {
                        if(modalidad=="A"){
                            $("#h4ModalidadVenta").html("SELECCIONA EL PAGO ANTICIPADO");
                            var html = '<table class="centered striped bordered highlight"><thead><tr><td class="center">CORRELATIVO</td><td class="center">TIPO PAGO</td><td class="center">PROPIETARIO</td><td class="center">VALOR</td><td class="center">FECHA PAGO</td><td class="center">FECHA ENTREGA</td><td class="center">DETALLE</td><td></td></tr></thead>';
                            var datos = a.datos;
                            $.each(datos,function (key,val){
                                var id = val[0];
                                html = html + '<tr>';
                                html = html + '<td class="center">' + val[1] + '</td>';
                                html = html + '<td class="center">' + val[2] + '</td>';
                                html = html + '<td class="center">' + val[3] + '</td>';
                                html = html + '<td class="center">' + val[4] + '</td>';
                                html = html + '<td class="center">' + val[5] + '</td>';
                                html = html + '<td class="center">' + val[6] + '</td>';
                                html = html + '<td class="center">' + val[7] + '</td>';
                                html = html + '<td class="center"><button type="button" class="modal-action modal-close btn light-green accent-1 black-text" onclick="SeleccionarPago(' + val[0] + ',\''+val[3]+'\','+val[4]+',\''+val[5]+'\');"><i class="material-icons">check</i></button></td>';
                                html = html + '</tr>';
                            });
                            html = html + '</table>';
                            $("#divModalidadVenta").html(html);
                            $("#btnAceptarModalidadVenta").hide();
                        }
                    },
                    complete: function() {
                        var modalidad = $("#tipoVenta").val();
                        if($("#tipoVenta").val()!=$("#tipoVentaSeleccionado").val()){
                            $('#tipoVenta').val('N');
                            $("#tipoVenta").material_select();
                            fnCambiarModalidad();
                            alerta("NO HA SELECCIONADO LOS DATOS CORRECTAMENTE");
                        }
                    } // Callback for Modal close
                });
            }
        });
    }else{
        $("#divModoPago").show();
        $("#divEfectivo").show();
        $("#divEfectivo2").show();
        $(".anticipo").hide();
        $("#txtAnticipo").val("0");
    }
}

function SeleccionarPago(id,propietario,valor,fechapago){
    $('#modalModalidadVenta').closeModal();
    $("#idpagoanticipado").val(id);
    $("#txtAnticipo").val(valor);
    calcularTotalPagoAnticipado();
}

function calcularTotalPagoAnticipado(){
    var anticipo = parseFloat($("#txtAnticipo").val());
    var tot = parseFloat($("#txtTotal").val());
    var tot2 = Math.round((tot - anticipo)*100)/100;
    $("#txtTotal").val(tot2);
    calcularVuelto();
    document.getElementById("divTotal").innerHTML="IMPORTE TOTAL S/."+document.getElementById("txtTotal").value;
}

$('#divEfectivo').show();$('#divTarjeta').hide();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').hide();$('#txtDinero').val('0');$('#txtVuelto').val('0');$('#divSelectTarjeta').html('');$('#divSelectAmbos').html('');
$('.select2').select2({
	theme:"classic"
});
</script>
</head>
<style type="text/css">
    .select{
        font-size: 12px;
    }
</style>
<body onLoad="">
<center>
<form method="post" name="frmComanda" id="frmComanda">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
  <input name="txtId" id="txtId" type="hidden" value="0" />
  <input name="txtidmov" id="txtidmov" type="hidden" value="<?=$dato->idmovimiento?>" />
<div id="blokeador" style="position:absolute;display:none;background-color:#FFFFFF; filter:alpha(opacity=55);opacity:0.55;"></div>

<!--BOTONERA DE ACCESOS RAPIDOS DE LOS MOZOS ACABA-->
<?php if($_SESSION["R_IdPerfil"]==4){?>
<!--BOTONEERA DE CAJEROS-->
<div class="Botones" id="opciones" hidden="">
    <div class="row">
        <div class="col s7 center">
            <div class="col s6 m4 l2 center" hidden="">
              <button id="btnAGREGAR" class="btn-large light-blue darken-4 truncate white-text tooltipped" data-position="bottom" data-delay="50" data-tooltip="AGREGAR PRODUCTOS" type="button" onclick="agregarplatos('C')"><i class="material-icons">add</i></button>
            </div>
            <div class="col s6 m4 l2 center" style="display:none">
              <button id="btnAGREGAR2" class="btn-large green darken-4 truncate white-text tooltipped" data-position="bottom" data-delay="50" data-tooltip="AGREGAR PRODUCTOS" type="button" onclick="agregarplatos('B')"><i class="material-icons">kitchen</i></button>
            </div>
            <div class="col s6 m4 l2 center" style="display:none">
                <button id="btnUNIR" disabled="true" class="btn-large lime accent-1 truncate lime-text text-darken-4 tooltipped" data-position="bottom" data-delay="50" data-tooltip="UNIR MESAS" type="button" onclick="modalMesas('UNIR','<?=$_GET["idmesa"]?>')"><i class="material-icons">dashboard</i></button>
            </div>
            <div class="col s6 m4 l3 center" style="display:none">
              <button id="btnJUNTAR" disabled="true" class="btn-large teal accent-1 truncate teal-text text-darken-4" type="button" onclick="modalMesas('JUNTAR','<?=$_GET["idmesa"]?>')"><i class="material-icons right">compare_arrows</i>JUNTAR</button>
            </div>
            <div class="col s6 m4 l3 center" style="display:none">
              <button id="btnMOVER" disabled="true" class="btn-large orange accent-1 truncate orange-text text-darken-4" type="button" onclick="modalMesas('MOVER','<?=$_GET["idmesa"]?>')"><i class="material-icons right">open_with</i>MOVER</button>
            </div>
        </div>
        <div class="col s5 center">
            <div class="col s6 m4 l4 center" style="display:none">
              <button id="btnCOMANDA" class="btn-large red accent-1 truncate red-text text-darken-4" type="button" onclick="imprimircomanda()"><i class="material-icons right">send</i>COMANDA</button>
            </div>
            <div class="col s6 m4 l4 center" hidden="">
                <button id="btnCUENTA" class="btn-large indigo accent-1 truncate indigo-text text-darken-4" type="button" onclick="imprimircuenta()"><i class="material-icons right">content_copy</i>CUENTA</button>
            </div>
            <div class="col s6 m4 l4 center" hidden="">
                <button id="btnCOBRAR" disabled="" class="btn-large purple accent-1 truncate purple-text text-darken-4" type="button" onclick="generarComprobante('<?php echo $dato->idmovimiento;?>','<?=$_GET["mesa"]?>');"><i class="material-icons right">monetization_on</i>COBRAR</button>
            </div>
        </div>
    </div>
</div>
<?php }?>

<!--DIV IZQUIERDO DEL DETALLE DE LA MESA DONDE LISTA LOS PRODUCTOS AGREGADOS EMPIEZA-->
<input type="hidden" id="numero" value="<?=$_GET["mesa"]?>">
<input type="hidden" name="">
<div class="row">
    <div class="col s7 m7 l7">
        <!--BOTONERA DE ACCESOS RAPIDOS DE LOS MOZOS EMPIEZA-->
        <div class="Botones" id="opciones" style="height: 660px;">
            <div class="row DetalleMesa-Izq" >
                <h4>LISTADO DE PRODUCTOS</h4>
                <div class="col s12 center" style="height: 50px;">
                <?php
                    if($_SESSION["R_PrecioVenta"]=="J"){
                        $precio="";
                    }elseif($_SESSION["R_PrecioVenta"]=="V"){
                        $precio="2";
                    }elseif($_SESSION["R_PrecioVenta"]=="S"){
                        $precio="3";
                    }elseif($_SESSION["R_PrecioVenta"]=="D"){
                        $precio="4";
                    }
                    if($_SERVER['REMOTE_ADDR']=="192.168.1.57"){
                        $rs=$objMantenimiento->obtenerDataSQL("select p.* from producto p inner join listaunidad lu on lu.idproducto=p.idproducto and lu.idunidad=p.idunidadbase and lu.idsucursal=p.idsucursal where p.estado='N' and p.idproducto in (85,86) and lu.precioventa$precio>0 and p.idsucursal=".$_SESSION["R_IdSucursal"]."  order by p.abreviatura asc limit 50");
                        $x=0;
                        while($dat=$rs->fetchObject()){$x++;
                    ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-green darken-4 white-text' style='height: 45px;margin-top: 5px;' onclick="modalPropiedades2(<?=$dat->idcategoria?>,'<?=$dat->idproducto?>@<?=$dat->descripcion?>@<?=$_SESSION["R_IdSucursal"]?>')"><?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                    <?php
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 60px;">';
                        }
                    }else{
                        //BOTONES MAS PRINCIPALES
                        $rs=$objMantenimiento->obtenerDataSQL("select p.* from producto p inner join listaunidad lu on lu.idproducto=p.idproducto and lu.idunidad=p.idunidadbase and lu.idsucursal=p.idsucursal and lu.idsucursalproducto=p.idsucursal where p.estado='N' and p.comentario like 'PRINCIPAL1' and lu.precioventa$precio>0 and p.idsucursal=".$_SESSION["R_IdSucursal"]."  order by p.codigo asc limit 50");
                        $x=0;
                        while($dat=$rs->fetchObject()){
                        	if($dat->idproducto=="69"){
                                $x++;
                    ?>
                    	<div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,0.5);$('#txtAccionPropiedad').val('Nuevo');">0.5<?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                            $x=$x+1;
                        ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,1);$('#txtAccionPropiedad').val('Nuevo');">1.00 <?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                            $x=$x+1;
                        ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,1.5);$('#txtAccionPropiedad').val('Nuevo');">1.5 <?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                            $x=$x+1;
                        ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,2.0);$('#txtAccionPropiedad').val('Nuevo');">2.0 <?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                    <?php 
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                    	}elseif($dat->idproducto=="98"){
                            $x++;
                            //$x=$x+1;
                    ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,1);$('#txtAccionPropiedad').val('Nuevo');">1 <?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                            /*$x=$x+1;
                        ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,2);$('#txtAccionPropiedad').val('Nuevo');">2 <?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php
                            if($x%6==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                            $x=$x+1;
                        ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,4);$('#txtAccionPropiedad').val('Nuevo');">4 <?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php
                            if($x%6==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                            $x=$x+1;
                        ?>
                        <div class="col l2 center">
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="tipo='T';seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,5);$('#txtAccionPropiedad').val('Nuevo');">5 <?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                    <?php  */
                            //if($x%6==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                        }
                        $x++;
                    ?>
                        <div class="col l2 center">
                        	<?php if($dat->idproducto=="69" || $dat->idproducto=="98" || $dat->idproducto=="101" || $dat->idproducto=="104"){ ?>
                        	<button type="button" class='btn <?=$color?> darken-3 white-text' style='height: 45px;margin-top: 5px;' onclick="modalPropiedades2(<?=$dat->idcategoria?>,'<?=$dat->idproducto?>@<?=$dat->descripcion."(".$dat->stock.")"?>@<?=$_SESSION["R_IdSucursal"]?>');"><?=substr($dat->abreviatura,0,16)?></button>
                        	<?php }else{ ?>
                            <button type="button" class='btn light-orange darken-4 black-text' style='font:bold;height: 45px;margin-top: 5px;' onclick="seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,1);$('#txtAccionPropiedad').val('Nuevo');"><?=substr($dat->abreviatura,0,16)?></button>
                        	<?php } ?>
                        </div>
                        <?php
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                        }
                        //ACCESO RAPIDO
                        $rs=$objMantenimiento->obtenerDataSQL("select p.*,lu.precioventa$precio as precio from producto p inner join listaunidad lu on lu.idproducto=p.idproducto and lu.idunidad=p.idunidadbase and lu.idsucursal=p.idsucursal and lu.idsucursalproducto=p.idsucursal inner join categoria ca on ca.idcategoria=p.idcategoria and p.idsucursal=ca.idsucursal where p.estado='N' and p.comentario like 'PRINCIPAL'  and p.idsucursal=".$_SESSION["R_IdSucursal"]."  order by ca.descripcion,p.codigo asc limit 500");
                        /*cambie para hacerlo mas rapido
                            modalPropiedades2(<?=$dat->idcategoria?>,'<?=$dat->idproducto?>@<?=$dat->descripcion?>@<?=$_SESSION["R_IdSucursal"]?>')*/
                        while($dat=$rs->fetchObject()){
                        	if($dat->idcategoria==13){
                        		$color="light-blue";
                        	}elseif($dat->idcategoria==11){
                        		$color="yellow";
                        	}elseif($dat->idcategoria==19){
                        		$color="pink";
                        	}elseif($dat->idcategoria==12){
                        		$color="light-pink";
                        	}elseif($dat->idcategoria==4){
                        		$color="brown";
                        	}else{
                        		$color="light-green";
                        	}
                        	$x++;
                            if($dat->idproducto=="69"){
                    ?>
                        <div class="col l2 center">
                            <button type="button" class='btn <?=$color?> darken-3 white-text' <?php if($dat->precio==0) echo "disabled";?> style='height: 45px;margin-top: 5px;' onclick="modalPropiedades2(<?=$dat->idcategoria?>,'<?=$dat->idproducto?>@<?=$dat->descripcion."(".$dat->stock.")"?>@<?=$_SESSION["R_IdSucursal"]?>');"><?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php
                            }else{
                        ?>
                        <div class="col l2 center">
                            <button type="button" class='btn <?=$color?> darken-3 white-text' <?php if($dat->precio==0) echo "disabled";?> style='height: 45px;margin-top: 5px;' onclick="seleccionar(<?=$dat->idproducto?>,<?=$_SESSION["R_IdSucursal"]?>,1);$('#txtAccionPropiedad').val('Nuevo');"><?=substr($dat->abreviatura,0,16)?></button>
                        </div>
                        <?php

                            }
                            if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                        }
                        //echo '</div><div class="col s12 center" style="height: 50px;">';
                        $sql="Select vIdCategoria, vAbreviatura,vIdCategoriaref,vDescripcion as Descripcion,vimagen from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].") where vnivel=1 and vidcategoria not in (1,11,20,18) order by vDescripcion ASC";
                        $consulta2 = $objMantenimiento->obtenerDataSQL($sql);
                        $d=$consulta2->rowCount();
                        while($dato=$consulta2->fetchObject()){$x++;
                        ?>
                            <div class="col l2 center">
                              <select class='select2 light-blue darken-4 white-text' id="cboCategoria<?=$dato->vidcategoria?>" onchange="modalPropiedades2(<?=$dato->vidcategoria?>,this.value);" style="width: 90%;">
                                  <option value='0' ><?=$dato->descripcion?></option>
                        <?php
                            $rs=$objMantenimiento->obtenerDataSQL("select p.*,obtenerStock(P.idproducto,P.idunidadbase,".$_SESSION["R_IdSucursal"].", P.IdSucursal) as Stock from producto p inner join listaunidad lu on lu.idproducto=p.idproducto and lu.idunidad=p.idunidadbase and lu.idsucursal=p.idsucursal and lu.idsucursalproducto=p.idsucursal where p.estado='N' and p.idcategoria=$dato->vidcategoria and lu.precioventa$precio>0 and p.idsucursal=".$_SESSION["R_IdSucursal"]." and p.comentario not like 'PRINCIPAL' and p.comentario not like 'PRINCIPAL1'  order by p.abreviatura asc limit 40");
                            //print_r($rs);
                            while($dat=$rs->fetchObject()){
                        ?>
                                <option value='<?=$dat->idproducto?>@<?=$dat->descripcion."(".$dat->stock.")"?>@<?=$_SESSION["R_IdSucursal"]?>'><?=substr($dat->abreviatura,0,16)?></option>
                        <?php
                        }
                            echo "</select></div>";

                        if($x%5==0) echo '</div><div class="col s12 center" style="height: 50px;">';
                        }
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col s5 m5 l5">
        <div class="row" id="divDatosDocumento1">
            <div class="col s12 m5 l3">
                <div class="input-field inline">
                    <?php //if($_GET["accion"]=="ACTUALIZARPAGO") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2","IdTipoDocumento",$dato["idtipodocumento"],'',$objMantenimiento,"generaNumeroVenta(this.value)"); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2 order by idtipodocumento desc","IdTipoDocumento",19,'',$objMantenimiento,"generaNumeroVenta(this.value)");?>
                    <input type="hidden" name="cboIdTipoDocumento" id="cboIdTipoDocumento" value="19">
                    <button type="button" id="btnBoleta" class="btn-floating btnDoc" style="margin-right: 5px;" onclick="$('#cboIdTipoDocumento').val('4');generaNumeroVenta($('#cboIdTipoDocumento').val());$('.btnDoc').removeClass('red');$('#btnBoleta').addClass('red');" >Bol.</button>
                    <button type="button" id="btnFactura" class="btn-floating btnDoc" style="margin-right: 5px;" onclick="$('#cboIdTipoDocumento').val('5');generaNumeroVenta($('#cboIdTipoDocumento').val());$('.btnDoc').removeClass('red');$('#btnFactura').addClass('red');">Fact.</button>
                    <button type="button" id="btnTicket" class="btn-floating red btnDoc" style="margin-right: 5px;" onclick="$('#cboIdTipoDocumento').val('19');generaNumeroVenta($('#cboIdTipoDocumento').val());$('.btnDoc').removeClass('red');$('#btnTicket').addClass('red');">Tick.</button>
                    <label class="labelSuperior active">T. Documento</label>
                </div>
            </div>
            <div class="col s12 m6 l3" hidden="">
                <div class="input-field inline">
                    <input id="txtNumeroVenta" name="txtNumeroVenta" readonly="" type="text" value="<?php if($_GET["accion"]=="ACTUALIZARPAGO"){ echo htmlentities(umill($dato["numero"]), ENT_QUOTES, "UTF-8"); }else{ echo $objMantenimiento->generaNumero(2,6,substr($_SESSION["R_FechaProceso"],6,4));}?>" readonly="">
                    <label for="txtNumeroVenta" class="active">Numero</label>
                </div>
            </div>
            <div class="col s12 m6 l3" hidden="">
                <div class="input-field inline">
                    <input type="text" readonly="" id="txtFecha" name="txtFecha" value="<?php if($_GET["accion"]=="ACTUALIZARPAGO"){ echo htmlentities(umill(substr($dato["fecha"],0,10)), ENT_QUOTES, "UTF-8"); }else{ echo $_SESSION["R_FechaProceso"];}?>" readonly="true">
                    <label for="txtFecha" class="active">Fecha</label>
                </div>
            </div>
            <div class="col s12 m7 l9 valign-wrapper" id="divCliente">
                <div class="input-field inline col s9 m9 l9">
                    <input type="hidden" name="txtIdSucursalPersona" id="txtIdSucursalPersona" value="<?php echo $dato["idsucursalpersona"]?>">
                    <input type="hidden" name="txtIdPersona" id="txtIdPersona" value="<?php echo $dato["idpersona"];?>">
                    <input type="hidden" name="txtModoPersona" id="txtModoPersona" value="N">
                    <input id="txtPersona" type="text" readonly="" value="<?php echo $dato["cliente"];?>" class="autocomplete" autocomplete="off">
                    <label id="lblPersona" for="txtPersona" class="active">Cliente</label>
                </div>
                <div class="col s3 m3 l3 center valign-wrapper" style="padding: 0px 0px 0px 0px;">
                    <div class="col s6 right">
                        <button type="button" class="btn-floating red tooltipped" data-position="left" data-delay="30" data-tooltip="BORRAR SELECCION" onclick="limpiarCamposPersona();"><i class="material-icons">close</i></button>
                        <button type="button" onclick="modalNuevoPersona()" class="btn-floating light-green accent-1 tooltipped" data-position="left" data-delay="30" data-tooltip="AGREGAR CLIENTE"><i class="material-icons black-text">add</i></button>
                    </div>
                    <div class="col s6">
                        <button type="button" onclick="cambiarPatronBusqueda()" class="btn-floating amber accent-4 tooltipped" data-position="left" data-delay="30" data-tooltip="CAMBIAR MODO DE BUSQUEDA"><i class="material-icons black-text">cached</i></button>
                    </div>
                </div>
            </div>
            <div class="col s12">
                <div class="input-field inline">
                    <input type="text" id="txtNombreImprimir" name="txtNombreImprimir" value="<?php if($_GET["accion"]=="ACTUALIZARPAGO"){ echo htmlentities(umill(substr($dato["fecha"],0,10)), ENT_QUOTES, "UTF-8"); }else{ echo " ";}?>">
                    <label for="txtNombreImprimir">Nombre Cliente</label>
                </div>
            </div>
            <div class="col s12 m6 l3" id="divModoPago">
                <label style="margin-left: 15px;" class="col s12 left-align labelSuperior">Modo de Pago</label>
                <div id="rbtnModoPago">
                    <div class="row">
                        <div class="col s6">
                            <p class=" input-field inline" style="margin-top: 0px;">
                                <input type="radio" value="E" name="rdbtnModoPago" id="chbxEFECTIVO" checked="" onchange="if(this.checked){$('#divEfectivo').show();$('#divEfectivo2').show();$('#divTarjeta').hide();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').hide();$('#txtDinero').focus();$('#txtDinero').val($('#txtTotal').val());$('#txtVuelto').val('0');$('#divSelectTarjeta').html('');$('#divSelectAmbos').html('');$('#divTransferencia').hide();$('#divOtros').hide();}">
                                <label for="chbxEFECTIVO">EFECTIVO</label>
                            </p>
                            <p class="input-field inline" style="margin-top: 0px;">
                                <input type="radio" value="T" name="rdbtnModoPago" id="chbxTARJERA" onchange="if(this.checked){$('#divEfectivo').hide();$('#divTarjeta').show();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').hide();$('#cboTipoTarjeta').val('1');$('#divSelectTarjeta').html(selectTipoTarjeta);$('#divSelectAmbos').html('');$('select').material_select();$('#divTransferencia').hide();$('#divOtros').hide();$('#divEfectivo2').hide();}">
                                <label for="chbxTARJERA">TARJETA</label>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <p class=" input-field inline" style="margin-top: 0px;">
                                <input type="radio" value="A" name="rdbtnModoPago" id="chbxAMBOS" onchange="if(this.checked){$('#divEfectivo').show();$('#divTarjeta').hide();$('#divAmbos').show();$('#divCheque').hide();$('#divDeposito').hide();$('#txtPagoEfectivo').val('0');$('#txtVuelto').val('0');$('#divSelectTarjeta').html('');$('#divSelectAmbos').html(selectTipoTarjeta);$('select').material_select();$('#divTransferencia').hide();$('#divOtros').hide();}">
                                <label for="chbxAMBOS">EFECTIVO Y TARJETA</label>
                            </p>
                        </div>
                        <div class="col s12">
                            <p class=" input-field inline" style="margin-top: 0px;">
                                <input type="radio" value="TRANS" name="rdbtnModoPago" id="chbxTRANS" onchange="if(this.checked){$('#divEfectivo').hide();$('#divTarjeta').hide();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').hide();$('#divTransferencia').show(); $('#divEfectivo2').hide();$('#divOtros').hide();}">
                                <label for="chbxTRANS">PLIN O YAPE</label>
                            </p>
                        </div>
                        <div class="col s12">
                            <p class=" input-field inline" style="margin-top: 0px;">
                                <input type="radio" value="OTROS" name="rdbtnModoPago" id="chbxOTROS" onchange="if(this.checked){$('#divEfectivo').hide();$('#divTarjeta').hide();$('#divAmbos').hide();$('#divCheque').hide();$('#divDeposito').hide();$('#divTransferencia').hide();$('#divOtros').show();$('#divEfectivo2').hide();}">
                                <label for="chbxOTROS">OTROS</label>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="divEfectivo"  class="col s12 m9 l9">
                <div class="col s12 m9 l9">
                    <button type="button" class="btn col s2 m1 l1 " style="margin-right: 5px;display: none;" onclick="BotonesDinero(5);$('#txtDinero').trigger('keyup');$('#txtDinero').focus();">5</button>
                    <button type="button" class="btn col s2 m2 l2 " style="margin-right: 5px;" onclick="BotonesDinero(10);$('#txtDinero').trigger('keyup');calcularVuelto();">10</button>
                    <button type="button" class="btn col s2 m2 l2 " style="margin-right: 5px;" onclick="BotonesDinero(20);$('#txtDinero').trigger('keyup');calcularVuelto();">20</button>
                    <button type="button" class="btn col s2 m2 l2 " style="margin-right: 5px;" onclick="BotonesDinero(50);$('#txtDinero').trigger('keyup');calcularVuelto();">50</button>
                    <button type="button" class="btn col s2 m2 l2 " style="margin-right: 5px;" onclick="BotonesDinero(100);$('#txtDinero').trigger('keyup');calcularVuelto();">100</button>
                    <button type="button" class="btn col s2 m2 l2 offset-m1 offset-l1  green tooltipped" data-position="bottom" data-delay="30" data-tooltip="MONTO ANTERIOR"  style="margin-right: 3px;" onclick="MontoAnterior();$('#txtDinero').trigger('keyup');"><i class="material-icons">history</i></button>
                </div>
            </div>
            <div hidden id="divOtros" class="row">
                <div class="col s3 m4">  
                    <div class="input-field inline">
                        <input id="txtMontoEfectivoVarios" value="0" name="txtMontoEfectivoVarios" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onfocus="modalNumero('txtMontoEfectivoVarios');" readonly="">
                        <label for="txtMontoEfectivoVarios" class="active">Efectivo</label>
                    </div>
                </div>
                <div class="col s3 m4">
                    <div class="input-field inline">
                        <input id="txtMontoVisaVarios" value="0" name="txtMontoVisaVarios" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onclick="if($(this).val()<=0){$(this).val('0')}" onfocus="modalNumero('txtMontoVisaVarios');" readonly="">
                        <label for="txtMontoVisaVarios" class="active"> VISA</label>
                    </div>
                </div>
                <div class="col s3 m4">
                    <div class="input-field inline">
                        <input id="txtMontoYapeVarios" value="0" name="txtMontoYapeVarios" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onclick="if($(this).val()<=0){$(this).val('0')}" onfocus="modalNumero('txtMontoYapeVarios');" readonly="">
                        <label for="txtMontoYapeVarios" class="active">YAPE</label>
                    </div>
                </div>
                <div class="col s3 m4">
                    <div class="input-field inline">
                        <input id="txtMontoPlinVarios" value="0" name="txtMontoPlinVarios" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onclick="if($(this).val()<=0){$(this).val('0')}" onfocus="modalNumero('txtMontoPlinVarios');" readonly="">
                        <label for="txtMontoPlinVarios" class="active">PLIN</label>
                    </div>
                </div>
            </div>
            <div id="divTransferencia" hidden class="col s12 m4 l4">
                <div class="input-field inline">
                    <select id="txtMonedaCheque" name="moneda_cheque">
                        <option value="Y">YAPE</option>
                        <option value="P">PLIN</option>
                    </select>
                    <label class="labelSuperior">Tipo</label>
                </div>
            </div>
            <div id="divEfectivo2"  class="col s12 m9 l9">
                <div class="col s3 m3 l3" >
                    <div class="input-field inline">
                        <input type="text" class="inptCantidad" id="txtDinero" value="0" readonly="" onfocus="modalNumero('txtDinero');">
                        <label for="txtDinero" class="active">Dinero</label>
                    </div>
                </div>
                <div class="col s4 m4 l4">
                    <div class="input-field inline"> 
                        <input id="txtVuelto" class="inptCantidad" type="text" readonly="">
                        <label for="txtVuelto" class="active">Vuelto</label>
                    </div>
                </div>
            </div>
            <div class="row" id="divAmbos" hidden>
                <div class="col s12 m6 l6">
                    <div class="input-field inline" id="divSelectAmbos" hidden="">
                    </div>
                </div>
                <div class="col s12 m6 l2">
                    <div class="input-field inline">
                        <input id="txtPagoEfectivo" value="0" name="txtPagoEfectivo" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onfocus="modalNumero('txtPagoEfectivo');" readonly="">
                        <label for="txtPagoEfectivo" class="active">Dinero</label>
                    </div>
                    <input type="hidden" id="txtPagoCredito" name="txtPagoCredito">
                </div>
                <div class="col s4 m4 l2">
                    <div class="input-field inline">
                        <input id="txtMontoVisa" value="0" name="txtMontoVisa" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onclick="if($(this).val()<=0){$(this).val('0')}" onfocus="modalNumero('txtMontoVisa');" readonly="">
                        <label for="txtMontoVisa" class="active">Tarjeta VISA</label>
                    </div>
                </div>
                <div class="col s4 m4 l2">
                    <div class="input-field inline">
                        <input id="txtMontoMastercard" value="0" name="txtMontoMastercard" class="inptCantidad" type="text" onKeyPress="return validarsolonumerosdecimales(event,this.value);" onclick="if($(this).val()<=0){$(this).val('0')}" onfocus="modalNumero('txtMontoMastercard');" readonly="">
                        <label for="txtMontoMastercard" class="active">Tarjeta MASTERCARD</label>
                    </div>
                </div>
            </div>
            <div  class="col s12 m6 l6">
                <div class="col s4 m4 l4" hidden="">
                    <div class="input-field inline">
                        <input type="text" class="" id="txtGlosa" name="glosa_movimiento" onblur="validarAP();">
                        <input type="hidden" name="txtValidarRef" id="txtValidarRef" value="S">
                        <label for="txtGlosa">REF AP VISA</label>
                    </div>
                </div>
                <div class="col s4 m4 l4" hidden="">
                    <div class="input-field inline">
                        <input type="text" class="" id="txtGlosa1" name="glosa_movimiento1" onblur="validarAP();">
                        <label for="txtGlosa1">REF AP MASTER</label>
                    </div>
                </div>
            </div>
            <br />
            <div class="" id="divTarjeta" hidden>
                <div class="col s12 l3">
                    <div class="input-field inline" id="divSelectTarjeta">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field inline">
                        <select id="tipoVenta" name="tipoVenta" onchange="fnCambiarModalidad();">
                            <option value="N" selected="">VENTA COMÚN</option>
                            <option value="A">VENTA CON PAGO ANTICIPADO</option>
                            <option value="C">VENTA AL CREDITO</option>
                        </select>
                        <label class="labelSuperior">Modalidad de Venta</label>
                    </div>
                </div>
                <div class="col s12 m6 l3 anticipo" hidden="">
                    <div class="input-field inline">
                        <input type="text" class="" id="txtAnticipo" name="txtAnticipo" value="0" readonly="">
                        <input type="hidden" name="idpagoanticipado" id="idpagoanticipado" value="0">
                        <label for="txtAnticipo" class="active">Pag. Ant.</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m12 l12 center DetalleMesa-Izq">
            <h4><?php if($_SESSION["R_IdSucursal"]==10) echo $_GET["salon"]." / ";?>TOMA PEDIDO<?php if($_SESSION["R_IdPerfil"]==4&&$_GET["accion"]=="ACTUALIZAR"){    echo '<button class="btn-floating red white-text right" onclick="eliminarMovimiento('.$dato->idmovimiento.');" type="button"><i class="material-icons">clear</i></button>';}?></h4>
            <div class="row detalle" style="padding-left: 10px;padding-right: 10px;" hidden="">
                <div class="row" style="margin-bottom: 0px;">
                <div class="col s12 m12 l6" id="nrocomanda" hidden="">COMANDA NUMERO: <? if($_GET["accion"]=="ACTUALIZAR") echo $dato->numero; ?></div>
                <div class="col s12 m12 l6" id="divTotalProducto" hidden="">NUMERO DE PRODUCTOS: 0</div>
                </div>
                <div class="row valign-wrapper" style="margin-bottom: 0px;">
                    <div class="col s12 m12 l12" hidden="">
                        <label class="black-text" for="slcIdUsuario">MESERO<i class="material-icons left">accessibility</i></label>
                        <select id="slcIdUsuario">
                            <?php $acceso= new clsAccesoDatos('','');
                                $acceso->gIdTabla = 46;
                                $acceso->gIdSucursal = 1;
                                $sql = "Select P.idsucursal,us.idusuario, P.IdPersona, Apellidos,Nombres, CASE WHEN tipopersona='VARIOS' THEN 'DNI' ELSE 'RUC' END as tipodoc, nrodoc,us.nombreusuario as usuario 
                                 From Persona P 
                                 inner join PersonaMaestro PM on PM.IdPersonaMaestro=P.IdPersonaMaestro 
                                 inner join rolpersona rp on rp.idpersona=P.idpersona and rp.idsucursal=P.idsucursal 
                                 INNER JOIN SUCURSAL s on s.idsucursal=P.idsucursal
                                 INNER JOIN usuario us on us.idpersona=P.idpersona and us.idsucursal=s.idsucursal   
                                 Where P.estado='N' ";
                                $sql .= " and idrol in (1) and s.idempresa=1 and us.idperfil=5 order by PM.nombres asc";// cast(PM.apellidos as integer)
                                //Emprea 1 por el potrero y perfil 5 por mozos
                                $rst = $acceso->obtenerDataSQL($sql);
                                while($reg=$rst->fetchObject()){?>
                                <option id="optnSlcIdUsuario_<?php echo $reg->idusuario;?>" id_persona="<?php echo $reg->idpersona;?>" value="<?php echo $reg->idusuario;?>"><?php echo "$reg->nombres";?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col s12 m12 l6 valign-wrapper" style="display: none;">
                        <div class="col s8">
                            NUMERO PERSONAS
                        </div>
                        <div class="col s4">
                            <input id="numeropersonas" class="txtImportante" type="number" name="numeropersonas" value="<?php echo $numero_personas;?>">
                        </div>
                    </div>
                </div>
                
                <?php if($_GET["accion"]=="ACTUALIZAR"){?>
                <div class="col s12 m12 l6 txtImportante" id="divNombreMesero">MESERO: <?php echo $dato->responsable;?></div>
                <?php }?>
            </div>
            
            <div class="row detalle">
                <div class="col s12 m12 l12 txtImportante" id="divTotal">IMPORTE TOTAL S/0.00</div>
            </div>
            <div class="row">
                <?php if($historial>=1){?>
                <div class="col s6 left">
                    <button id="btnHISTORIAL" class="btn indigo darken-4 left" type="button" onClick="historialMovimiento(<?php echo $dato->idmovimiento;?>);" >HISTORIAL<i class="material-icons right">description</i></button>
                </div>
                <?php }?>
                <div class="col s6 left">
                    <button id="btnHistorico" class="btn indigo darken-4 left" type="button" onClick="historicoVenta(<?php echo $dato->idmovimiento;?>);" >HISTOR.<i class="material-icons right">description</i></button>
                </div>

                <div class="col s6 left" hidden="">
                    <input type="hidden" name="credito" id="credito" value="N">
                    <button id="btnCredito" class="btn red darken-4 left" type="button" onClick="validarUsuario('credito');" >A CUENTA<i class="material-icons right">feedback</i></button>
                </div>
                <div class="col s6 right">
                    <button id="btnCOCINA" class="btn amber darken-4 right" type="button" onClick="/*modalDetalle();*/aceptarCajero();" >IMPRIMIR<i class="material-icons right">save</i></button>
                </div>
            </div>
            <!-- <input type="hidden" name="InptCliente" value="">
            <input type="hidden" name="txaComentario" value=""> -->
            <?php if($_GET["idsalon"]=="4"){
                        ?>
                            <div class="col s11">
                                <div class="input-field inline">
                                    <input type="hidden" name="txtIdSucursalPersona" id="txtIdSucursalPersona">
                                    <input type="hidden" name="txtIdPersona" id="txtIdPersona">
                                    <input id="InptCliente" autocomplete="off" type="text" value="<?php echo $dato->nombrespersona;?>">
                                    <label for="InptCliente" class="black-text<?php if(strlen($dato->nombrespersona)>0){ echo ' active';}?>">CLIENTE<i class="material-icons left">face</i></label>
                                </div>
                            </div>
                            <div class="col s1">
                                <button type="button" onclick="modalNuevoPersona()" class="btn-floating light-green accent-1 tooltipped" data-position="left" data-delay="30" data-tooltip="AGREGAR CLIENTE"><i class="material-icons black-text">add</i></button>
                            </div>
                            <div class="col s12">
                                <div class="input-field inline">
                                    <textarea id="txaComentario" class="materialize-textarea" style="height: 90px;"><?php echo $dato->comentario;?></textarea>
                                    <label for="txaComentario" class="black-text active">COMENTARIO<i class="material-icons left">font_download</i></label>
                                </div>
                            </div>
                        <?php }else{ ?>
                            <input type="hidden" id="InptCliente" value="<?php echo $dato->nombrespersona;?>">
                            <input type="hidden" id="txaComentario" value="<?php echo $dato->comentario;?>">
                        <?php }?>
            
            <div class="row tabla" id="divDetallePedido">
                <table class="bordered highlight" style="display: none;">
                    <thead>
                        <tr>
                            <th class="center">PRODUCTO</th>
                            <th class="center">CANTIDAD</th>
                            <th class="center">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MANGO C/LECHE</td>
                            <td class="center">1</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>MANGO C/LECHE</td>
                            <td class="center">1</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>MANGO C/LECHE</td>
                            <td class="center">1</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="yellow lighten-2">
                            <td onclick="modalPropiedades(1,'HAMBURGUESA HAWAYANA','CON HARTA MAYONESA Y KETCHUP',7)">HAMBURGUESA HAWAYANA</td>
                            <td class="center">7</td>
                            <td><a style="" class="btn-floating red" onclick="alert('ACCION PARA REMOVER DETALLE')"><i class="material-icons">clear</i></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php if($_SESSION["R_IdPerfil"]==4){?>
            <div class="row" style="padding-top: 20px;">
                
            </div>
            <?php }else{?>
            <div class="row right" style="padding-top: 20px;">
                <div class="col s12"><button id="btnCOCINA" class="btn amber darken-4" type="button" onClick="aceptar();" >ENVIAR<i class="material-icons right">save</i></button></div>
            </div>
            <?php }?>
        </div>
    </div>
<!--DIV IZQUIERDO DEL DETALLE DE LA MESA DONDE LISTA LOS PRODUCTOS AGREGADOS ACABA-->
</div>
<input type="hidden" name="txtMesa" id="txtMesa" value="<?php if($_SESSION["R_IdSucursal"]==10) echo $_GET["salon"]." / ";?><?=$_GET["mesa"]?>" />
<input id="txtNumeroComanda" name="txtNumeroComanda" type="hidden" value="<? if($_GET["accion"]=="ACTUALIZAR") echo $dato->numero; else echo ""; ?>" />
    
    <div id="modalHistorial" class="modal modal-fixed-footer">
        <div class="modal-content orange lighten-3">
          <div class="white" style="border-radius: 10px;">
            <div class="row">
                <div class="col s12 center"><h4 style="background-color: transparent;">HISTORIAL DE ELIMINACIONES</h4></div>
            </div>
              <div class="row tabla" style="padding-bottom: 10px;">
              <table class="bordered highlight">
                <thead>
                    <tr>
                        <th class="center">CANTIDAD</th>
                        <th class="center">PRODUCTO</th>
                        <th class="center">FECHA</th>
                        <th class="center">USUARIO</th>
                        <th class="center">USUARIO</th>
                    </tr>
                </thead>
                <tbody id="tbdymodalHistorial"></tbody>
            </table>
            </div>
          </div>
        </div>
        <div class="modal-footer amber lighten-3">
          <button id="" class="modal-action modal-close btn light-green accent-1 black-text" type="button">CERRAR</button>
        </div>
    </div>
    <div id="DivSucursal" style="width:200px;position:absolute;display:none;"></div>
    <div class="modalNuevoPersona">
        <div id="modalNuevoPersona" class="modal modal-fixed-footer orange lighten-3">
            <div class="modal-content">
              <div class="white" style="padding: 10px;border-radius: 10px;">
                  <form id="frmMantPersonaMaestro" method="POST" action="">
                <div class="row">
                  <div class="col s12" hidden="">
                      <div class="input-field inline">
                        <select id="cboTipoPersona" name="cboTipoPersona" onchange="cambiarTipoPersona('contenido',$(this).val());">
                            <option value="NATURAL">Natural</option>
                            <option value="VARIOS">Varios</option>
                        </select>
                        <label for="monto">Tipo Persona</label>
                      </div>
                  </div>
                  <div class="col s12" id="contenido"></div>
                </div>
                  </form>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
                <button id="btnAceptarModalPersona" disabled="" type="button" onclick="aceptarModalPersona()" class="waves-effect waves-green btn light-green accent-1 black-text">Agregar<i class="material-icons right">add</i></button>
            </div>
        </div>
    </div>

    <div id="modalValidarUsuario" class="modal modal-fixed-footer orange lighten-3" style="height: 50%">
        <div class="modal-content">
          <div class="white" style="padding: 10px;border-radius: 10px;">
                <form id="" method="POST" action="">
                    <h4>Validar Usuario</h4>
                    <div class="row">
                      <div class="col s12">
                          <div class="input-field inline">
                            <input type="password" id="txtPassword" name="txtPassword">
                            <label for="txtPassword">Password</label>
                          </div>
                      </div>
                    </div>
                </form>
          </div>
        </div>
        <div class="modal-footer amber lighten-3">
            <button id="btnAceptarModalUsuario" type="button" onclick="aceptarModalUsuario('credito')" class="waves-effect waves-green btn light-green accent-1 black-text">Validar<i class="material-icons right">check</i></button>
        </div>
    </div>

    <div id="modalHistorialVenta" class="modal modal-fixed-footer orange lighten-3" style="height: 100%;width: 90%;">
        <div class="modal-content">
            <div class="white" style="padding: 10px;border-radius: 10px;">
                <form id="" method="POST" action="">
                    <input type="hidden" id="txtHistorialVenta" name="txtHistorialVenta" value="">
                    <h4>Historial de Ventas</h4>
                    <div class="row">
                        <div class="col s12">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="center">Fecha</th>
                                        <th class="center">Comanda</th>
                                        <th class="center">Comprobante</th>
                                        <th class="center">Total</th>
                                        <th class="center">I. Tick</th>
                                        <th class="center">I. Comp</th>
                                        <th class="center">I. Ambos</th>
                                        <th class="center">Anular</th>
                                        <th></th>
                                    </tr>    
                                </thead>
                                <tbody>
                                    <?php
                                    $rst=$objMantenimiento->obtenerDataSQL("select m.numero,m.total,(select numero from movimientohoy where idmovimiento=ds.idpedido) as numero2,m.estado,ds.idpedido,ds.idventa,m.fecha,m.situacion
                                        from detallestock ds 
                                        inner join movimientohoy m on m.idmovimiento=ds.idventa
                                        where 1=1 and m.idcaja=".$_SESSION["R_IdCaja"]." order by m.fecha desc limit 100");
                                    while($dat=$rst->fetchObject()){
                                        if($dat->estado!="A" && $dat->estado!="I"){
                                            echo "<tr id='Venta-$dat->idventa'>";
                                        }else{
                                            echo "<tr id='Venta-$dat->idventa' style='color:red'>";
                                        }
                                        echo "<td align='center'>".date("d/m/Y H:i",strtotime($dat->fecha))."</td>";
                                        echo "<td align='center'>$dat->numero2</td>";
                                        echo "<td align='center'>".substr($dat->numero,0,13)."</td>";
                                        echo "<td class='right'>".number_format($dat->total,2,'.','')."</td>";
                                        echo '<td><p class="input-field inline" style="margin-top: 0px;" ><input type="radio" onchange="if(this.checked){$(\'#txtHistorialVenta\').val(this.value);}" value="T@'.$dat->idpedido.'@'.$dat->idventa.'" id="rdAccionT'.$dat->idpedido.'" name="rdAccion" /><label for="rdAccionT'.$dat->idpedido.'" id="lblT'.$dat->idpedido.'">Tick</label></p></td>';
                                        echo '<td><p class="input-field inline" style="margin-top: 0px;" ><input type="radio" onchange="if(this.checked){$(\'#txtHistorialVenta\').val(this.value);}" value="C@'.$dat->idpedido.'@'.$dat->idventa.'" id="rdAccionC'.$dat->idpedido.'" name="rdAccion" /><label for="rdAccionC'.$dat->idpedido.'" id="lblC'.$dat->idpedido.'">Comp</label></p></td>';
                                        echo '<td><p class="input-field inline" style="margin-top: 0px;" ><input type="radio" onchange="if(this.checked){$(\'#txtHistorialVenta\').val(this.value);}" value="A@'.$dat->idpedido.'@'.$dat->idventa.'" id="rdAccionA'.$dat->idpedido.'" name="rdAccion" /><label for="rdAccionA'.$dat->idpedido.'" id="lblA'.$dat->idpedido.'">Ambos</label></p></td>';
                                        if($dat->estado!="A" && $dat->estado!="I"){
                                            echo '<td><p class="input-field inline" style="margin-top: 0px;" ><input type="radio" onchange="if(this.checked){$(\'#txtHistorialVenta\').val(this.value);}" value="E@'.$dat->idpedido.'@'.$dat->idventa.'" id="rdAccionE'.$dat->idpedido.'" name="rdAccion" /><label for="rdAccionE'.$dat->idpedido.'" id="lblE'.$dat->idpedido.'">Anular</label></p></td>';
                                        }else{
                                            echo "<td></td>";
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer amber lighten-3" style="height: 100px;">
            <div class="row" >
                <div class="col s6" hidden=''>
                  <div class="input-field inline">
                    <input type="password" id="txtPassword2" name="txtPassword2" value="123456">
                    <label for="txtPassword2" class="active">Password</label>
                  </div>
                </div>
                <div class="col s12">
                    <button id="btnAceptarModalVentas" type="button" onclick="aceptarModalVenta()" class="waves-effect waves-green btn light-green accent-1 black-text">Validar<i class="material-icons right">check</i></button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalNumero" class="modal modal-fixed-footer grey darken-1">
        <div class="modal-content">
            <div class="row">
                <div class="col s12 center">
                    <input class="center white-text" id="txtLetraNumero" type="text" name="" readonly="">
                    <input type="hidden" name="txtInpt" id="txtInpt" value="">
                </div>
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn1" onclick="ingresar(1)">1</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn2" onclick="ingresar(2)">2</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn3" onclick="ingresar(3)">3</button>
                </div>
            </div>
            <div class="row">
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn4" onclick="ingresar(4)">4</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn5" onclick="ingresar(5)">5</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn6" onclick="ingresar(6)">6</button>
                </div>
            </div>
            <div class="row">
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn7" onclick="ingresar(7)">7</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn8" onclick="ingresar(8)">8</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn9" onclick="ingresar(9)">9</button>
                </div>
            </div>
            <div class="row">
                <div class="col s4 m3 offset-m1 l2 offset-l3 center">
                    <button class="btn-large green numero" onclick="enviar()" id="btnc"><i class="material-icons right">check</i></button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large deep-orange darken-4 numero" id="btn0" onclick="ingresar(0)">0</button>
                </div>
                <div class="col s4 m3 l2 center">
                    <button class="btn-large orange numero" onclick="vaciar()" id="btnv"><i class="material-icons right">delete</i></button>
                </div>
            </div>
        </div>
        <div class="modal-footer red darken-2">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat white-text">CERRAR</a>
        </div>
    </div>

    <div id="modalDetalle" class="modal modal-fixed-footer orange lighten-3" style="height: 50%">
        <div class="modal-content">
          <div class="white" style="padding: 10px;border-radius: 10px;">
                <form id="" method="POST" action="">
                    <h4>Confirmar Pedido</h4>
                    <div class="row">
                      <div class="col s12" id="">
                          <table id="tbModalDetalle" class="bordered highlight">
                          </table>
                      </div>
                    </div>
                </form>
          </div>
        </div>
        <div class="modal-footer amber lighten-3">
            <button id="btnAceptarModalDetalle" type="button" onclick="aceptarCajero();" class="waves-effect waves-green btn light-green accent-1 black-text">Enviar<i class="material-icons right">check</i></button>
        </div>
    </div>

    <div id="modalDescuento" class="modal modal-fixed-footer orange lighten-3" style="height: 50%">
        <div class="modal-content">
          <div class="white" style="padding: 10px;border-radius: 10px;">
                <form id="" method="POST" action="">
                    <h4>Confirmar Descuento</h4>
                    <div class="row">
                      <div class="col s12" id="">
                            <table id="" class="bordered highlight">
                                <tr>
                                    <th>Producto</th>
                                    <td><input type="text" name="txtProductoDesc" id="txtProductoDesc" value="" readonly="">
                                        <input type="hidden" name="txtIdProductoDesc" id="txtIdProductoDesc" value="">
                                        <input type="hidden" name="txtIdSucursalProductoDesc" id="txtIdSucursalProductoDesc" value="">
                                    </td>
                                    <th>P. Venta</th>
                                    <td><input type="text" name="txtPrecioVentaDesc" id="txtPrecioVentaDesc" value="" onKeyPress="return validarsolonumerosdecimales(event,this.value);" ></td>
                                </tr>
                            </table>
                      </div>
                    </div>
                </form>
          </div>
        </div>
        <div class="modal-footer amber lighten-3" style="height: 100px;">
            <div class="row">
                <div class="col s6">
                  <div class="input-field inline" hidden="">
                    <input type="password" id="txtPassword3" name="txtPassword3">
                    <label for="txtPassword3" class="active">Password</label>
                  </div>
                </div>
                <div class="col s6">
                    <button id="btnAceptarModalVentas" type="button" onclick="aceptarModalDescuento()" class="waves-effect waves-green btn light-green accent-1 black-text">Validar<i class="material-icons right">check</i></button>
                </div>
            </div>
        </div>
    </div>
     <div class="modalModalidadVenta">
        <div id="modalModalidadVenta" style="width: 85%;" class="modal modal-fixed-footer orange lighten-3">
            <div class="modal-content">
              <div class="white" style="padding: 10px;border-radius: 10px;">
                <div class="row">
                    <h4 class="center" id="h4ModalidadVenta"></h4>
                </div>
                <div class="row">
                  <div class="col s12" id="divModalidadVenta">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
                <a href="#!" onclick="/*$('#tipoVenta').val('N');$('#tipoVenta').material_select();*/" class="left modal-action modal-close btn red accent-1 black-text">Cerrar<i class="material-icons right">clear</i></a>
                <a href="#!" id="btnAceptarModalidadVenta" class="modal-action modal-close btn light-green accent-1 black-text">Aceptar<i class="material-icons right">check</i></a>
            </div>
        </div>
    </div>
</body>
<?
/*
//- modal de historial de ventas de 20 datos y boton para imprimir pedido,venta y ambos y anulacion
//- cada accion debe pedir clave de administrador
- MOSTRAR ENVIOS PENDIENTES DE ACEPTAR A PAMELA
//- al enviar mostrar detalle con mensaje de confirmacion
//- quitar tipo de persona...cambiar RUC/DNI y Apellidos y nombres / Razon Social
//- en vez de glosa va REF AP VISA y REF AP MASTER
//- REPORTE DE INVENTARIO
- STOCK INICIAL / INGRESOS / TRASLADOS / VENTA / REMANENTE(FINAL)
- REPORTE DE GASTOS
//- calcular diferencia al agregar o quitar
- no permitir guardar si no se ingresa datos de dinero o visa
- imprimir forma de pago y vuelto
- si ref de visa y master tiene dato validar monto mayor q cero en tarjeta
- quitar ap cuando es compartido en el efectivo


*/
?>