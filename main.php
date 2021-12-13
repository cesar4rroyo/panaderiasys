<?php
session_start();
require_once 'vista/fun.php';
if(strstr($_SERVER['HTTP_USER_AGENT'],'IE')){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
}else{
?>
<!DOCTYPE html>
<?php }?>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>SISREST - Sistema Est&aacute;ndar para Panaderia</title>
        <link rel="shortcut icon" href="img/24 Custom.ico" />
        <link rel="stylesheet" href="css/material-design-iconic-font.min.css">
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
        <link rel="stylesheet" href="css/sweetalert.css">
        <link rel="stylesheet" href="css/style.css">
        <script type="text/javascript" src="js/autocompletar.js"></script>
        <!--CALENDARIO-->
        <script src="calendario/js/jscal2.js"></script>
        <script src="calendario/js/lang/es.js"></script>
        <link rel="stylesheet" type="text/css" href="calendario/css/jscal2.css" />
        <link rel="stylesheet" type="text/css" href="calendario/css/reduce-spacing.css" />
        <link rel="stylesheet" type="text/css" href="calendario/css/steel/steel.css" />
        <!--CALENDARIO-->
        <script src="runtime/lib/aw.js" type="text/javascript"></script>
        <link href="runtime/styles/system/aw.css" rel="stylesheet">
        <script type="text/javascript" src="js/fun.js"></script>
        <script>
        var fechainicio=new Date();
        var g_bandera = null;
        var g_ajaxGrabar = null;
        var cabeceraRuta = [["Inicio","vista/frmCajero","&id_clase=0"]];
        function CancelarMantenimiento(){
            $("#tablaActual").show();
            $("#opciones").show();
            $("#nro_hoj").val(1);
            //buscar();
            /*cabeceraRuta.pop();
            var actual = cabeceraRuta.pop();
            CargarCabeceraRuta([actual],false);*/
            atras();
        }
        function CargarCabeceraRuta(array,nuevo){
            if(nuevo){
                cabeceraRuta = [["Inicio","","&id_clase=0"]];
            }
            var ultimo = cabeceraRuta[cabeceraRuta.length-1];
            $(array).each(function (key,val){
                if(ultimo[0]==val[0]){
                    cabeceraRuta.pop();
                }
                cabeceraRuta.push(val);
            });
            $("#CabeceraRuta").empty();
            $(cabeceraRuta).each(function (key,val){
                $("#CabeceraRuta").append('<a href="#!" onclick="preRun(\''+val[1]+'\',\''+val[2]+'\',\'frame\',\'frame\',\'imgloading\','+key+');" class="breadcrumb">'+val[0]+'</a>');
            });
        }
        function preRun(url, par, div, msj, img,key){
            var limite = cabeceraRuta.length-key;
            var actual;
            for(var i=0;i<limite;i++){
                actual = cabeceraRuta.pop();
            }
            if(cabeceraRuta.length==0){
                cabeceraRuta = [["Inicio","","&id_clase=0"]];
            }
            $("#listaCategorias").remove();
            setRun(url, par, div, msj, img);
            CargarCabeceraRuta([actual],false);
        }
        function setRunCabecera(url, par, div, msj, img, link1, link2, link3){
            var array = [
                [link1,url,par],
                [link2,url,par],
                [link3,url,par]
            ];
            CargarCabeceraRuta(array,true);
            setRun(url, par, div, msj, img);
        }
        function setRun(url, par, div, msj, img){
                var fechainicio=new Date();
                var recipiente = document.getElementById(div);
                var g_ajaxPagina = new AW.HTTP.Request;  
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
                        var fechafin=new Date();
                        loading(false, img);
                        $("select").material_select();
                        $('.material-tooltip').each(function (key,val){
                            $(this).remove();
                        });
                        $('.tooltipped').tooltip({delay: 50});
                        //"Fecha ini "+fechainicio +" fin "+fechafin
                        //alert("Fecha "+fechainicio+" fin " + fechafin);
                        //alert(xform);
                };
                g_ajaxPagina.request();
                loading(true, img, msj, "linea.gif",true);
        }
        function atras(){
            /*url=document.getElementById("url").value;
            par=document.getElementById("par").value;
            div=document.getElementById("div").value;
            msj=document.getElementById("msj").value;
            img=document.getElementById("img").value;    
            if(url=="vista/frmComanda"){
                //verpedido();
                setRun(url,par,div,msj,img);
                document.getElementById("cargagrilla").innerHTML="";
                document.getElementById("url").value="vista/frmCajero";
                document.getElementById("par").value="&id_clase=46";
                document.getElementById("div").value="frame";
                document.getElementById("msj").value="frame";
                document.getElementById("img").value="imgloading";        
            }else{
                if(url=="vista/frmCajero"){
        //            document.getElementById("opciones").style.display="none";
        //            document.getElementById("frame").style.display="";
                    document.getElementById("cargagrilla").innerHTML="";
                    setRun(url,par,div,msj,img);
                }else{
                    setRun(url,par,div,msj,img);
                }
            }*/
            var ultimo = cabeceraRuta[cabeceraRuta.length-2];
            preRun(ultimo[1],ultimo[2],'frame','frame','imgloading',cabeceraRuta.length-2);
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
        function imprimircuenta(){
            if(document.getElementById("txtId").value>0){
                g_ajaxPagina.setURL("vista/ajaxPedido.php");
                g_ajaxPagina.setRequestMethod("POST");
                g_ajaxPagina.setParameter("accion", "imprimir_cuenta");
                g_ajaxPagina.setParameter("idmovimiento",document.getElementById("txtId").value);
                g_ajaxPagina.setParameter("mesa",document.getElementById("txtMesa").value);
                g_ajaxPagina.setParameter("numerocomanda",document.getElementById("txtNumeroComanda").value);
                g_ajaxPagina.setParameter("d",<?php if($_SESSION["R_IdPerfil"]==5) echo "1";else echo "2";?>);
                g_ajaxPagina.response = function(text){
                    //alert("SE ESTA IMPRIMIENDO LA CUENTA");
                    console.log(text);
                <?php if($_SESSION["R_IdPerfil"]=="5") echo "atras();";?>
                };
                g_ajaxPagina.request();
            }else{
                alert("No existe pedido para esta mesa");
            }

        }
        function imprimircomanda(){
           /* //if(document.getElementById("txtId").value>0){
                document.getElementById("divimprimircomanda").style.display='';
                document.getElementById("divimprimircocina").style.display='none';
                document.getElementById("divimprimircuenta").style.display='none';
                verpedido();
                window.print();
                alert("imprimiendo");
           //}else{
              //  alert("No existe pedido para esta mesa");
            //}*/
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
    function seleccionar(idproducto,idsucursalproducto,cantidad){
          var recipiente = document.getElementById('divDetallePedido');
          g_ajaxPagina = new AW.HTTP.Request;
          g_ajaxPagina.setURL("vista/ajaxPedido.php");
          g_ajaxPagina.setRequestMethod("POST");
          g_ajaxPagina.setParameter("accion", "agregarProductoMozo");
          g_ajaxPagina.setParameter("Actual", $("#detalle-producto").attr("modo"));
          g_ajaxPagina.setParameter("IdProducto", idproducto);
          g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
          g_ajaxPagina.setParameter("Cantidad", cantidad);
          g_ajaxPagina.setParameter("class", "zoom12");
          g_ajaxPagina.setParameter("comentario", $("#detalle-producto").val());
          g_ajaxPagina.setParameter("modo", "PlatosPredeterminado");
          var list="";
          for(i=0;i<lista.length;i++){
              list+=lista[i]+"-";
          }
          if(lista.length>0){
              g_ajaxPagina.setParameter("listaDetalle", list.substr(0,list.length-1));
          }else{
              g_ajaxPagina.setParameter("listaDetalle", list);
          }
          g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").value);
          g_ajaxPagina.response = function(text){
            recipiente.innerHTML = text;
            $('#modalPropiedades').closeModal();
            document.getElementById("divTotal").innerHTML="IMPORTE TOTAL S/."+document.getElementById("txtTotal").value;
            document.getElementById("divTotalProducto").innerHTML="NUMERO DE PRODUCTOS: "+document.getElementById("txtTotalProducto").value;
            if($("#inpt_Busq_Producto").val().length>0){
              $("#inpt_Busq_Producto").val("");
              $("#inpt_Busq_Producto").focus();
            }

                //agregarplatos();
          };
          g_ajaxPagina.request();
          document.getElementById("frame").style.display='';

          document.getElementById("url").value="vista/frmCajero";
          document.getElementById("par").value="&id_clase=0";
          document.getElementById("div").value="frame";
          document.getElementById("msj").value="frame";
          document.getElementById("img").value="imgloading";
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
    function generarComprobante(id,nombremesa){
        document.getElementById("url").value="vista/frmComanda";
        document.getElementById("par").value="&idmesa="+document.getElementById("Idmesa").value+"&mesa="+$('#numero').val()+"&salon="+document.getElementById("Salon").value+"&accion=ACTUALIZAR";
        document.getElementById("div").value="frame";
        document.getElementById("msj").value="frame";
        document.getElementById("img").value="imgloading";
        setRun('vista/mantVentaRapida','&accion=NUEVO&clase=Movimiento&id_clase=44&Id=' + id+"&idmesa="+document.getElementById("Idmesa").value+"&mesa="+$('#numero').val()+"&salon="+document.getElementById("Salon").value,"frame","frame","imgloading");
        CargarCabeceraRuta([["Cobrar - "+nombremesa,"vista/mantVentaRapida",'&accion=NUEVO&clase=Movimiento&id_clase=44&Id=' + id]],false);
        
    }

    function cambiar1(){
        $('#cantidad-producto').html('<input type="text" id="cantidad2" value="" onblur="cambiar(this.value);" onkeypress="return validarsolonumerosdecimales(event,this.value);" />');
        $('#cantidad2').focus();
    }

    function cambiar(val){
        $("#cantidad-producto").html(val);
    }        
</script>
    
    </head>
    <body onload="">
        <input type="hidden" id="url" value="vista/frmCajero" />
        <input type="hidden" id="par" value="&id_clase=0" />
        <input type="hidden" id="div" value="frame" />
        <input type="hidden" id="msj" value="frame" />
        <input type="hidden" id="img" value="imgloading" />
        <input type="hidden" id="Nropersonas" value="0" />
        <input type="hidden" id="Idmesa" value="0" />
        <input type="hidden" id="Salon" value="" />
        <div id="blokeador" style="position:absolute;display:none; background-image:url(img/semitransparente.jpg); background-color:#FFFFFF; filter:alpha(opacity=55);opacity:0.55;"></div>
        <?php 
            require('modelo/clsGeneral.php');
            $idtabladefecto=0;
            $acciondefecto="";
        ?>
        
        <nav class="brown darken-3 BarraSuperior">
            <div class="nav-wrapper">
              <a href="#!" data-activates="menu-izquierdo" class="brand-logo"><div><?php echo $_SESSION['R_NombreEmpresa']?></div></a>
              <a href="#" data-activates="mobile-demo" class="right button-collapse"><i class="material-icons">menu</i></a>
              <ul class="right hide-on-med-and-down MenuSuperior">
                  <li><a onclick="atras();">Atrás<i class="material-icons left">reply</i></a></li>
                <li>
                    <a href="#" class="tooltipped btnPerfil" data-position="bottom" data-delay="50" data-tooltip="Perfil" nombre-perfil="<?php echo $_SESSION['R_Perfil']?>"><?php echo $_SESSION['R_NombreUsuario']?><img class="responsive-img right ImagenMenu" src="assets/img/user.png" alt="UserImage">
                  </a>
                </li>
                <li><a class="tooltipped btn-floating btn-large waves-effect waves-light red btnSalir" data-position="bottom" data-delay="50" data-tooltip="Cerrar Sesion"><i class="material-icons">settings_power</i></a></li>
              </ul>
              
              <ul class="side-nav Barra BarraDerecha" id="mobile-demo">
                <li><a onclick="atras();">ATRÁS<i class="material-icons right">reply</i></a></li>
                <li>
                  <a><?php echo $_SESSION['R_NombreUsuario']?><img class="responsive-img right ImagenMenu" src="assets/img/user.png" alt="UserImage">
                  </a>
                </li>
                <li><a href="" class="btnSalir">CERRAR SESION</a></li>
              </ul>
            </div>
        </nav>
        
        <div>
          <ul id="menu-izquierdo" class="side-nav Barra BarraIzquierda">
            <li class="sinFondo white-text">
              <div class="userView">
                <div class="menu-titulo center">SISREST</div>
                <div class="background center">
                  <img class="responsive-img ImagenLogo" src="assets/img/logo.png">
                </div>
                <div class="">
                  <div class="center truncate"><?php echo $_SESSION['R_Perfil']?></div>
                </div>
              </div>
            </li>
            <?php
            $objPermiso = new clsGeneral(0, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
            $rstPermisos = $objPermiso->obtenerPermisos();
            if(is_string($rstPermisos)){
                    echo "<td colspan=100>Sin Permisos</td></tr><tr><td colspan=100>".$rstPermisos."</td>";
            }else{
                    $inicio = 0;
                    $inicio2 = 0;
                    //print "<input type='text' value=\"";
                    while($datoPermisos = $rstPermisos->fetchObject()){
                            //echo utf8_decode($datoPermisos->modulo);
                            if($inicio2!= $datoPermisos->idmodulo){
                                    if($inicio2!=0){
                                            //echo "</ul></li></ul></li>";
                                        echo '</ul></div></li></ul></li></ul></div></li></ul></li>';
                                    }					
                                    //echo '<li><a href="#" class="NavLateral-DropDown  waves-effect waves-light"><i class="zmdi zmdi-widgets zmdi-hc-fw"></i> <i class="zmdi zmdi-chevron-down NavLateral-CaretDown"></i> '.umillmain($datoPermisos->modulo).'</a><ul class="full-width">';
                                    echo '<li class="no-padding"><ul class="collapsible collapsible-accordion"><li><a class="collapsible-header">'.umillmain($datoPermisos->modulo).'<i class="material-icons iconoBarra">arrow_drop_down</i></a><div class="collapsible-body"><ul>';
                                    $inicio2 = $datoPermisos->idmodulo;
                                    $inicio=0;
                            }
                            if($inicio != $datoPermisos->idmenuprincipal){
                                    if($inicio!=0){
                                            //echo '</ul></li><li class="NavLateralDivider"></li>';
                                        echo '</ul></div></li></ul></li>';
                                    }
                                    //echo '<li><a href="#" class="NavLateral-DropDown  waves-effect waves-light"><i class="zmdi zmdi-view-web zmdi-hc-fw"></i> <i class="zmdi zmdi-chevron-down NavLateral-CaretDown"></i> '.umillmain($datoPermisos->menuprincipal).'</a><ul class="full-width">';
                                    echo '<li class="no-padding"><ul class="collapsible collapsible-accordion"><li><a class="collapsible-header">'.umillmain($datoPermisos->menuprincipal).'<i class="material-icons iconoBarra">arrow_drop_down</i></a><div class="collapsible-body"><ul>';
                                    $inicio = $datoPermisos->idmenuprincipal;
                            }
                            //echo '<li><a href="#!" onClick="javascript:setRun(\'vista/'.$datoPermisos->accion.'\',\'&id_clase='.umill($datoPermisos->idtabla).'\',\'frame\',\'carga\',\'imgloading\');" class="waves-effect waves-light"><i class="zmdi zmdi-star zmdi-hc-fw"></i>'.umillmain($datoPermisos->descripcion).'</a></li><li class="NavLateralDivider"></li>';
                            echo '<li><a href="#!" onClick="javascript:setRunCabecera(\'vista/'.$datoPermisos->accion.'\',\'&id_clase='.umill($datoPermisos->idtabla).'\',\'frame\',\'carga\',\'imgloading\',\''.umillmain($datoPermisos->modulo).'\',\''.umillmain($datoPermisos->menuprincipal).'\',\''.umillmain($datoPermisos->descripcion).'\');">'.umillmain($datoPermisos->descripcion).'</a></li>';
                            //OBTENER DATOS DE LA OPCION MENU POR DEFECTO
                            if($_SESSION['R_OpcionMenuDefecto']==$datoPermisos->idopcionmenu){
                                    $idtabladefecto=$datoPermisos->idtabla;
                                    $acciondefecto=$datoPermisos->accion;
                            }
                    }
            }
            echo '</ul></div></li></ul></li></ul></div></li></ul></li>';
            ?>
              <li onclick="javascript:setRunCabecera('vista/listReporteProductoEliminados','&id_clase=11','frame','carga','imgloading','Soporte','Reportes','PRODUCTOS ELIMINADOS');">&nbsp;</li>
              <li onclick="javascript:setRunCabecera('vista/listReportePedidosEliminados','&id_clase=11','frame','carga','imgloading','Soporte','Reportes','PEDIDOS ELIMINADOS');">&nbsp;</li>
              <li onclick="javascript:setRunCabecera('vista/listReporteProductoCortesia','&id_clase=11','frame','carga','imgloading','Soporte','Reportes','PRODUCTOS DE CORTESIA');">&nbsp;</li>
            <li >&nbsp;</li>
          </ul>
        </div>
        
        <div class="container cuerpo" style="width: 100%;">
            <div class="container" style="width: calc(100% - 35px);">
                <div class="row ContenidoCajero">
                      <nav>
                        <div class="nav-wrapper">
                          <div class="col s12" id="CabeceraRuta">
                          </div>
                        </div>
                      </nav>
                    <div class="center" style="padding: 20px;">
                        <div class="row" id="frameTotal">
                            <div id="frame"></div>
                            <div id="cargagrilla"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

                <!--DIV MODAL SOBRE LAS PROPIEDADES DEL PRODUCTO EMPIEZA-->
                
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
                  <div class="col s4">
                    <button onclick="restarUno()" class="btn-floating btn-large lime accent-1"><i class="material-icons black-text" style="font-size: 2rem;">remove</i></button>
                  </div>
                  <div class="col s4 white black-text" id="cantidad-producto" ondblclick="cambiar1()">10</div>
                  <div class="col s4">
                    <button onclick="sumarUno()" class="btn-floating btn-large lime accent-1"><i class="material-icons black-text" style="font-size: 2rem;">add</i></button>
                  </div>
                </div>
                <div class="col s12 m6 l2 center">
                  <button onclick="seleccionar($('#txtIdProducto').val(),$('#txtIdSucursalProducto').val(),$('#cantidad-producto').html());" class="btn-large light-green accent-2 black-text">ACEPTAR</button>
                </div>
                <div class="col s12 m3 l1 center hide-on-med-and-down">
                  <button class="btn-floating btn-large red" onclick="$('#modalPropiedades').closeModal();"><i class="material-icons">clear</i></button>
                </div>
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

        <div class="modalEliminar">
          <div id="modalEliminar" class="modal modal-fixed-footer">
            <div class="modal-content orange lighten-3">
              <div class="white" style="border-radius: 10px;">
                <div class="row">
                  <div class="col s12 center"><h4 id="tituloModalEliminar"></h4></div>
                </div>
                <div class="row">
                  <div class="input-field inline col s12">
                    <input type="hidden" id="inptIdDetalleMov">
                    <input type="hidden" id="inptIdProducto">
                    <input type="hidden" id="inptIdSucursalProducto">
                        <input type="hidden" id="inptIdMovimiento">
                    <textarea id="txaMotivoEliminado" class="materialize-textarea"></textarea>
                    <label for="txaMotivoEliminado">MOTIVO DE ELIMINACION<i class="material-icons left">speaker_notes</i></label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
              <button id="modalEliminarBtnAceptar" class="modal-action modal-close btn light-green accent-1 black-text" type="button">ACEPTAR</button>
            </div>
          </div>
        </div>
                
        <div class="modalCobrar">
          <div id="modalCobrar" class="modal modal-fixed-footer">
            <div class="modal-content orange lighten-3">
              <div class="white" style="border-radius: 10px;">
                <div class="row">
                  <div class="col s12 center"><h4 id="tituloModalEliminar"></h4></div>
                </div>
                <div class="row">
                  <div class="input-field inline col s12">
                    <input type="hidden" id="inptIdDetalleMov">
                    <input type="hidden" id="inptIdProducto">
                    <input type="hidden" id="inptIdSucursalProducto">
                        <input type="hidden" id="inptIdMovimiento">
                    <textarea id="txaMotivoEliminado" class="materialize-textarea"></textarea>
                    <label for="txaMotivoEliminado">MOTIVO DE ELIMINACION<i class="material-icons left">speaker_notes</i></label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
              <button id="modalEliminarBtnAceptar" class="modal-action modal-close btn light-green accent-1 black-text" type="button">ACEPTAR</button>
            </div>
          </div>
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
        <script type="text/javascript" src="js/scripts2.js"></script>
        <script>
        <?php if($_SESSION["R_IdPerfil"]==4){?>
          function aceptarCambioSucursal(idsucursal){
                var g_ajaxGrabar = new AW.HTTP.Request;  
                g_ajaxGrabar.setURL("controlador/contSesion.php");
                g_ajaxGrabar.setRequestMethod("POST");
                g_ajaxGrabar.setParameter("accion", "CAMBIARSUCURSAL");
                g_ajaxGrabar.setParameter("cboSucursal", idsucursal);

                g_ajaxGrabar.response = function(text){
                    loading(false, "loading");
                    //document.getElementById("DivSucursal").style.display='none';
                    //alert(text);			
                    setRun("vista/frmCajero","&id_clase=46","frame","frame","imgloading");
                    //document.getElementById("lblIdSucursal").innerHTML=document.getElementById("cboSucursal").textContent;
                                    //document.getElementById("lblIdSucursal").innerHTML=document.getElementById("cboSucursal").options[document.getElementById("cboSucursal").value].value;
    //                document.getElementById("blokeador").style.display='none';
    //                document.getElementById("blokeador").style.height=document.body.clientHeight+'px';
    //                document.getElementById("blokeador").style.width=document.body.clientWidth+'px';
                };
                g_ajaxGrabar.request();
                loading(true, "loading", "frame", "line.gif",true);
        }
        //aceptarCambioSucursal(1);
        <?php }elseif($_SESSION["R_IdPerfil"]==5){?>
            window.location="mainMozo.php";
        <?php }?>
        </script>
    </body>
</html>