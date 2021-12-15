<?php
session_start();
require_once 'vista/fun.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>GesRest - Gesti&oacute;n para Panaderia</title>
    <link rel="stylesheet" href="css/material-design-iconic-font.min.css">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
    <link rel="stylesheet" href="css/sweetalert.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="img/24 Custom.ico" />
    <script type="text/javascript" src="js/autocompletar.js"></script>
    <script src="calendario/js/jscal2.js"></script>
    <script src="calendario/js/lang/es.js"></script>
    <link rel="stylesheet" type="text/css" href="calendario/css/jscal2.css" />
    <link rel="stylesheet" type="text/css" href="calendario/css/reduce-spacing.css" />
    <link rel="stylesheet" type="text/css" href="calendario/css/steel/steel.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script>window.jQuery || document.write('<script src="js/jquery-2.2.0.min.js"><\/script>')</script>
<!--CALENDARIO-->
<script src="runtime/lib/aw.js" type="text/javascript"></script>
<link href="runtime/styles/system/aw.css" rel="stylesheet">
<script type="text/javascript" src="js/fun.js"></script>
<script>
var fechainicio=new Date();
var g_bandera = null;
var g_ajaxGrabar = null;
function setRun(url, par, div, msj, img){
  var fechainicio=new Date();
  var recipiente = document.getElementById(div);
  var g_ajaxPagina = new AW.HTTP.Request;  
  console.log(url);
  g_ajaxPagina.setURL(url + ".php?ajax=true&"+par);
  g_ajaxPagina.setRequestMethod("POST");
  g_ajaxPagina.response = function(xform){
    
    var s = "", r = /<script>([\s\S]+)<\/script>/mi;
    if (xform.match(r)){
      s = RegExp.$1; // extract script
      xform = xform.replace(r, "");
    }
    recipiente.innerHTML = xform; 
    // Creo el nuevo JS
    var etiquetaScript=document.createElement("script");
    document.getElementsByTagName("head")[0].appendChild(etiquetaScript);
    etiquetaScript.text=s;
    $("select").material_select();
    var fechafin=new Date();
    loading(false, img);
    //"Fecha ini "+fechainicio +" fin "+fechafin
    //alert("Fecha "+fechainicio+" fin " + fechafin);
    //alert(xform);
  };
  g_ajaxPagina.request();
  loading(true, img, msj, "linea.gif",true);
}   
function muestraEnlaces(id){
  if(document.getElementById(id).className=="oculta"){
    document.getElementById(id).className = "muestra";
    document.getElementById("img"+id).src = "img/i_colpse.png";
  }else{
    document.getElementById(id).className = "oculta";
    document.getElementById("img"+id).src = "img/i_expand.png";
  }
}
function centraDivSucursal(){ 
    setRun("vista/frmMozo","&id_clase=46","frame","frame","imgloading");    
} 

function atras(){
    url=document.getElementById("url").value;
    par=document.getElementById("par").value;
    div=document.getElementById("div").value;
    msj=document.getElementById("msj").value;
    img=document.getElementById("img").value;    
    if(url=="vista/frmComanda"){
        verpedido();
        document.getElementById("cargagrilla").innerHTML="";
        document.getElementById("url").value="vista/frmMozo";
        document.getElementById("par").value="&id_clase=46";
        document.getElementById("div").value="frame";
        document.getElementById("msj").value="frame";
        document.getElementById("img").value="imgloading";        
    }else{
        if(url=="vista/frmMozo"){
//            document.getElementById("opciones").style.display="none";
//            document.getElementById("frame").style.display="";
            document.getElementById("cargagrilla").innerHTML="";
            setRun(url,par,div,msj,img);
        }else 
            setRun(url,par,div,msj,img);
    }
}
function verpedido(){
//    document.getElementById("frame").style.display='';
//    document.getElementById("cargagrilla").style.display="none";
}
function agregarplatos(tipo){
    var width=parseInt(screen.width);
    cargarclasecollapsibleproductos = true;
    setRun("vista/frmCategorias","&id_clase=46&width="+width+"&tipo="+tipo,"cargagrilla","cargagrilla","imgloading");
}
function agregarplatosMozo(disponible){
    if(disponible){
        var width=parseInt(screen.width);
        cargarclasecollapsibleproductos = true;
        setRun("vista/frmCategorias","&id_clase=46&width="+width,"cargagrilla","cargagrilla","imgloading");
    }
}
function imprimircuenta(){
    if(document.getElementById("txtId").value>0){
        g_ajaxPagina.setURL("vista/ajaxPedido.php");
        g_ajaxPagina.setRequestMethod("POST");
        g_ajaxPagina.setParameter("accion", "imprimir_cuenta");
        g_ajaxPagina.setParameter("idmovimiento",document.getElementById("txtId").value);
        g_ajaxPagina.setParameter("mesa",document.getElementById("txtMesa").value);
        g_ajaxPagina.setParameter("numerocomanda",document.getElementById("txtNumeroComanda").innerHTML);
        g_ajaxPagina.response = function(text){
          console.log(text);
          atras();
        };
        g_ajaxPagina.request();
    }else{
        alert("No existe pedido para esta mesa");
    }

}
function imprimircomanda(){
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "imprimir_comanda");
    g_ajaxPagina.setParameter("mesa",document.getElementById("txtMesa").value);
    g_ajaxPagina.response = function(text){
      //alert("imprimiendo");     
    };
    g_ajaxPagina.request();
}

var lista = new Array();
var tipo = 'C';
function seleccionar(idproducto,idsucursalproducto,cantidad){
  var recipiente = document.getElementById('divDetallePedido');
  g_ajaxPagina = new AW.HTTP.Request;
  g_ajaxPagina.setURL("vista/ajaxPedido.php");
  g_ajaxPagina.setRequestMethod("POST");
  g_ajaxPagina.setParameter("accion", "agregarProductoMozo");
  g_ajaxPagina.setParameter("IdProducto", idproducto);
  g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
  g_ajaxPagina.setParameter("Cantidad", cantidad);
  g_ajaxPagina.setParameter("Tipo", tipo);
  g_ajaxPagina.setParameter("class", "zoom12");
  g_ajaxPagina.setParameter("comentario", $("#detalle-producto").val());
  g_ajaxPagina.setParameter("modo", "PlatosPredeterminado");
  g_ajaxPagina.setParameter("accionPropiedad",document.getElementById("txtAccionPropiedad").value);
  var list="";
  for(i=0;i<lista.length;i++){
      list+=lista[i]+"-";
  }
  if(lista.length>0){
      g_ajaxPagina.setParameter("listaDetalle", list.substr(0,list.length-1));
  }else{
      g_ajaxPagina.setParameter("listaDetalle", list);
  }
  g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").innerHTML);
  g_ajaxPagina.response = function(text){
    recipiente.innerHTML = text;
    $('#modalPropiedades').closeModal();
    document.getElementById("divTotal").innerHTML="IMPORTE TOTAL S/."+document.getElementById("txtTotal").value;
    document.getElementById("divTotalProducto").innerHTML="NUMERO DE PRODUCTOS: "+document.getElementById("txtTotalProducto").value;
    $("#txtPagoEfectivo").val($("#txtTotal").val());
    //console.log($('#txtTotal').val());
      //$('#txtDinero').val($('#txtTotal').val());
    if($("#inpt_Busq_Producto").val()!=undefined && $("#inpt_Busq_Producto").val().length>0){
      $("#inpt_Busq_Producto").val("");
      $("#inpt_Busq_Producto").focus();
    }
    tipo='C';
    //calcularVuelto();
    calcularTotalPagoAnticipado();
        //agregarplatos();
  };
  g_ajaxPagina.request();
  document.getElementById("frame").style.display='';
  
  document.getElementById("url").value="vista/frmMozo";
  document.getElementById("par").value="&id_clase=0";
  document.getElementById("div").value="frame";
  document.getElementById("msj").value="frame";
  document.getElementById("img").value="imgloading";
}

function cambiarTipo(vtipo){
  tipo = vtipo;
}

function detalleCategoria(checked,iddetallecategoria){
    if(checked){
        lista.push(iddetallecategoria);
    }else{
        for(i=0;i<lista.length;i++){
            if(lista[i]==iddetallecategoria){
                lista.splice(i,1);
            }
        }
    }
    //alert(lista);
}
function verificarmesa(numero,idsalon){
        g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "verificarmesa");
    g_ajaxPagina.setParameter("txtMesa",numero);
        g_ajaxPagina.setParameter("idsalon",idsalon);
    g_ajaxPagina.response = function(text){
        eval(text);
            /*document.getElementById("Disponible").value = vdisponible;
            document.getElementById("Idmesa").value = vidmesa;
            document.getElementById("Nropersonas").value = vnropersonas;*/
            enviar(numero,vdisponible,vidmesa);
    };
    g_ajaxPagina.request();
}
function enviar(numero,vdisponible,idmesa){
    if(vdisponible==true){
        setRun("vista/frmComanda","&idmesa="+idmesa+"&mesa="+numero+"&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
    }else{
        g_ajaxPagina = new AW.HTTP.Request;
    g_ajaxPagina.setURL("vista/ajaxPedido.php");
    g_ajaxPagina.setRequestMethod("POST");
    g_ajaxPagina.setParameter("accion", "verificarusuario2");
    g_ajaxPagina.setParameter("IdMesa",document.getElementById("Idmesa").value);
    g_ajaxPagina.response = function(text){
            var text = JSON.parse(text);
            if(text.modificar==true){
                setRun("vista/frmComanda","&idmesa="+idmesa+"&mesa="+numero+"&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
            }else{
                setRun("vista/frmComanda","&idmesa="+idmesa+"&mesa="+numero+"&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
                //alert("ESTA MESA YA ESTA SIENDO ATENDIDA");
            }
     };
        g_ajaxPagina.request();
        //$("#frame").addClass("row");
    }     
}
// verificarmesa('MESA 01','2');
</script>
</head>
<body>
    <input type="hidden" id="url" value="vista/frmMozo" />
    <input type="hidden" id="par" value="&id_clase=0" />
    <input type="hidden" id="div" value="frame" />
    <input type="hidden" id="msj" value="frame" />
    <input type="hidden" id="img" value="imgloading" />
    <input type="hidden" id="Nropersonas" value="0" />
    <input type="hidden" id="Idmesa" value="0" />
    <input type="hidden" id="Salon" value="" />
    
    
    <nav class="brown darken-3 BarraSuperior">
        <div class="nav-wrapper">
          <a href="#!" data-activates="menu-izquierdo" class="brand-logo"><div><?php echo $_SESSION['R_NombreEmpresa']?></div></a>
          <a href="#" data-activates="mobile-demo" class="right button-collapse"><i class="material-icons">menu</i></a>
          <ul class="right hide-on-med-and-down MenuSuperior">
            <!--li><a href="#" onclick="atras();">Atrás<i class="material-icons left">reply</i></a></li-->
            <li>
                <a href="#" class="tooltipped btnPerfil" data-position="bottom" data-delay="50" data-tooltip="Perfil" nombre-perfil="<?php echo $_SESSION['R_Perfil']?>"><?php echo $_SESSION['R_NombreUsuario']?><img class="responsive-img right ImagenMenu" src="assets/img/user.png" alt="UserImage">
              </a>
            </li>
            <li><a class="tooltipped btn-floating btn-large waves-effect waves-light red" data-position="bottom" data-delay="50" data-tooltip="Cerrar Sesion" onclick="window.location='cerrarSesion.php?Origen=Mozo2';"><i class="material-icons">settings_power</i></a></li>
          </ul>

          <ul class="side-nav Barra BarraDerecha" id="mobile-demo">
            <li><a href="#" onclick="atras();">ATRÁS<i class="material-icons right">reply</i></a></li>
            <li>
              <a><?php echo $_SESSION['R_NombreUsuario']?><img class="responsive-img right ImagenMenu" src="assets/img/user.png" alt="UserImage">
              </a>
            </li>
            <li><a onclick="window.location='cerrarSesion.php?Origen=Mozo';">CERRAR SESION</a></li>
          </ul>
        </div>
    </nav>

    <div class="cuerpo">
        
        <div class="container" style="width: 95%;">
            <div class="row">
                <div id="frame"></div>
                <div id="cargagrilla"></div>
            </div>
        </div>

        <div class="PropiedadesProductos">
          <div id="modalPropiedades" class="modal bottom-sheet brown darken-3 white-text" style="height: 50%">
            <div class="modal-content" style="padding: 10px 10px 0px 10px;">
              <div class="row">
                <div class="col s12 m12 l6 center">
                  <div class="col s10 m11 l12 center">
                    <h4 style="font-weight: 500;" id="nombre-producto"></h4>
                  </div>
                  <div class="col s2 m1 hide-on-large-only">
                    <button class="btn-floating red right" onclick="$('#modalPropiedades').closeModal();"><i class="material-icons">clear</i></button>
                  </div>
                </div>
                <div class="col s12 m6 l3 center EstablecerCantidad valign-wrapper">
                  <input type="hidden" name="txtIdProducto" id="txtIdProducto" value="0">
                  <input type="hidden" name="txtIdSucursalProducto" id="txtIdSucursalProducto" value="">
                  <input type="hidden" name="txtAccionPropiedad" id="txtAccionPropiedad" value="" />
                  <div class="col s4">
                    <button onclick="restarUno()" class="btn-floating btn-large lime accent-1"><i class="material-icons black-text" style="font-size: 2rem;">remove</i></button>
                  </div>
                  <div class="col s4 white black-text" id="cantidad-producto" onclick="$('#cantidad-producto').html('<input type=\'text\' id=\'cant-parcial\' value=\'\' onblur=$(\'#cantidad-producto\').html(this.value); >');$('#cant-parcial').focus();">10</div>
                  <div class="col s4">
                    <button onclick="sumarUno()" class="btn-floating btn-large lime accent-1"><i class="material-icons black-text" style="font-size: 2rem;">add</i></button>
                  </div>
                  <div class="col s4">
                    <button onclick="cambiarTipo('T');seleccionar($('#txtIdProducto').val(),$('#txtIdSucursalProducto').val(),$('#cantidad-producto').html());" class="btn-floating btn-large lime accent-1"><i class="material-icons black-text" style="font-size: 2rem;">attach_money</i></button>
                  </div>

                </div>
                <div class="col s12 m6 l2 center">
                  <button onclick="seleccionar($('#txtIdProducto').val(),$('#txtIdSucursalProducto').val(),$('#cantidad-producto').html());" class="btn-large light-green accent-2 black-text">ACEPTAR</button>
                </div>
                <div class="col s12 m3 l1 center hide-on-med-and-down">
                  <button class="btn-floating btn-large red" onclick="$('#modalPropiedades').closeModal();"><i class="material-icons">clear</i></button>
                </div>
              </div>
              <div class="row center">
                  <button value="1" type="button" class="btn btn-large" onclick='$("#cantidad-producto").html("0");'>Borrar</button>
                  <button value="1" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>1</button>
                  <button value="2" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>2</button>
                  <button value="3" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>3</button>
                  <button value="4" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>4</button>
                  <button value="5" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>5</button>
                  <button value="6" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>6</button>
                  <button value="7" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>7</button>
                  <button value="8" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>8</button>
                  <button value="9" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>9</button>
                  <button value="0" type="button" class="btn-floating btn-large" onclick='$("#cantidad-producto").html($("#cantidad-producto").html()+this.value);'>0</button>
                  <!--button value="1" type="button" class="btn btn-large" onclick='aceptarDirecto();'>Imprimir</button-->
              </div>
              <div class="row" style="margin-bottom: 5px;">
                <div class="input-field col s12 valign-wrapper InputDetalleEscrito">
                  <i class="material-icons prefix">speaker_notes</i>
                  <input type="text" id="detalle-producto">
                </div>
              </div>
              <div class="row" id="divPropiedadProducto" style="margin-bottom: 0px;">
                <div class="col s6 m3 l2">
                  <p class="white-text">
                      <input class="filled-in" type="checkbox" id="propiedad-1" checked />
                      <label class="white-text" for="propiedad-1">AJI</label>
                  </p>
                </div>
              </div>    
            </div>
          </div>
        </div>

        <div class="modalMesas">
          <div id="modalMesas" class="modal modal-fixed-footer">
            <div class="modal-content orange lighten-3">
              <div class="white" style="border-radius: 10px;">
                <div class="row">
                  <div class="col s12 center"><h4 id="tituloModalMesas"></h4></div>
                </div>
                <div class="row">
                  <div class="input-field inline col s12">
                    <select id="modalMesasSlcSalon" onchange="modalMesasSlcMesas($(this).val(),$(this).attr('id_mesa_actual'),$(this).attr('situacion'));">
                    </select>
                    <label>ESCOGE EL SALON</label>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field inline col s12">
                    <select id="modalMesasSlcMesas">
                    </select>
                    <label>ESCOGE LA MESA</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
              <button onclick="modalMesasBtnAceptar();" id="modalMesasBtnAceptar" class="modal-action modal-close btn light-green accent-1 black-text" type="button">ACEPTAR</button>
            </div>
          </div>
        </div>
                <!--DIV MODAL SOBRE LAS PROPIEDADES DEL PRODUCTO ACABA-->

    </div>
    
    <!--footer class="page-footer">
      <div class="container">
        <div class="row">
          <div class="col l6 s12">
            <h5 class="white-text">SISREST</h5>
            <p class="grey-text text-lighten-4">
                Sistema Estándar para Restaurantes
            </p>
          </div>
          <div class="col l4 offset-l2 s12">
            <h5 class="white-text">PARTICIPANTES</h5>
            <ul>
                <li><b>Jefe de Proyecto:</b></li>
                <li>Ing. Martin Ampuero Pasco</li>
                <li><b>Programador:</b></li>
                <li>Jos&eacute; Alexander Samam&eacute; Nizama</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="footer-copyright brown darken-4">
        <div class="container">
        © 2016 Copyright Text
        <a class="grey-text text-lighten-4 right">Eduardo Antonio Espinoza Llontop</a>
        </div>
      </div>
    </footer-->
    <script src="js/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/jquery-2.2.0.min.js"></script>
    <script type="text/javascript" src="js/materialize.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
      function aceptarCambioSucursal(idsucursal){
            var g_ajaxGrabar = new AW.HTTP.Request;  
            g_ajaxGrabar.setURL("controlador/contSesion.php");
            g_ajaxGrabar.setRequestMethod("POST");
            g_ajaxGrabar.setParameter("accion", "CAMBIARSUCURSAL");
            g_ajaxGrabar.setParameter("cboSucursal", idsucursal);
    
            g_ajaxGrabar.response = function(text){
                loading(false, "loading");
                setRun("vista/frmMozo","&id_clase=46","frame","frame","imgloading");
            };
            g_ajaxGrabar.request();
            loading(true, "loading", "frame", "line.gif",true);
    }
    aceptarCambioSucursal(1);
    </script>
</body>
</html>