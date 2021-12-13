<?php
session_start();
require_once 'vista/fun.php';
if(strstr($_SERVER['HTTP_USER_AGENT'],'IE')){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="css/<?php echo $_SESSION['R_Estilo'];?>/<?php echo $_SESSION['R_Estilo'];?>.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/estiloazul/estiloimprimir.css" media="print"/>
<link rel="shortcut icon" href="img/24 Custom.ico" />
<meta http-equiv="content-type" content="text/html; charset=utf8">
<!--FUNCIONES AUTOCOMPLETAR: LAS CUALES PODEMOS REUTILIZAR EN DISTINTOS ARCHIVOS-->
<script type="text/javascript" src="js/autocompletar.js"></script>
<!---->
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
<link rel="stylesheet" href="css/style.css">
<script>
var fechainicio=new Date();
var g_bandera = null;
var g_ajaxGrabar = null;
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
   /* var top=(document.body.clientHeight/6)+"px"; 
    var left1=(document.body.clientWidth/2);
    var left=(left1-parseInt(document.getElementById("DivSucursal").style.width)/2)+"px"; 
    document.getElementById("DivSucursal").style.top=top; 
    document.getElementById("DivSucursal").style.left=left; */
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
            document.getElementById("opciones").style.display="none";
            document.getElementById("frame").style.display="";
            document.getElementById("cargagrilla").innerHTML="";
            setRun(url,par,div,msj,img);
        }else 
            setRun(url,par,div,msj,img);
    }
}

function verpedido(){
    document.getElementById("frame").style.display='';
    document.getElementById("cargagrilla").style.display="none";
}
function agregarplatos(){
    //document.getElementById("frame").style.display="none";
    document.getElementById("cargagrilla").style.display="";
    var width=parseInt(screen.width);
    setRun("vista/frmCategorias","&id_clase=46&width="+width,"cargagrilla","cargagrilla","imgloading");
}
function imprimircuenta(){
    if(document.getElementById("txtId").value>0){
        g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "imprimir_cuenta");
		g_ajaxPagina.setParameter("mesa",document.getElementById("txtMesa").value);
        g_ajaxPagina.setParameter("numerocomanda",document.getElementById("txtNumeroComanda").value);
		g_ajaxPagina.response = function(text){
			//alert("imprimiendo");			
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
</script>
<style type="text/css"> 
.oculta {
 display:none;
}

.muestra {
 display:block;
}
</style>
<title>SISREST - Sistema Est&aacute;ndar para Restaurante</title>
<link rel="shortcut icon" href="img/24 Custom.ico" />
</head>
<body onload="centraDivSucursal()">
<div id="blokeador" style="position:absolute;display:none; background-image:url(../img/semitransparente.jpg); background-color:#FFFFFF; filter:alpha(opacity=55);opacity:0.55;"></div>
<input type="hidden" id="url" value="vista/frmMozo" />
<input type="hidden" id="par" value="&id_clase=0" />
<input type="hidden" id="div" value="frame" />
<input type="hidden" id="msj" value="frame" />
<input type="hidden" id="img" value="imgloading" />
<input type="hidden" id="Nropersonas" value="0" />
<input type="hidden" id="Idmesa" value="0" />
<input type="hidden" id="Salon" value="" />  
<div id="blokeador" style="position:absolute;display:none; background-image:url(img/semitransparente.jpg); background-color:#FFFFFF; filter:alpha(opacity=55);opacity:0.55;"></div>
<table width="100%" border="0" align="center" id="tbprincipal">
    <tr id="titulo">
        <td class="titulo" style="font-size: xx-large;" colspan="2">SISREST - Sistema Est&aacute;ndar para Restaurante</td>
    </tr>
  	<tr id="menusup">
    <!--<td width="130" align="center"><a href="mainmozo.php"><?php if($_SESSION['R_Logo']<>''){ ?><img src="img/empresas/<?php echo $_SESSION['R_Logo'];?>" width="130" height="50" alt="Logo Empresa" title="Logo Empresa"/><?php }else{ ?><img src="img/<?php echo 'razon.png';?>" width="120" height="50" alt="Logo Empresa"  title="Logo Empresa"/><?php }?></a></td>-->
   	<td>
        <div id="barramenusup" style="height: 45px;width: auto;" class="zoom"> <!-- inicio menu superior -->
        <ul class="zoom" style="float:left">
        	<li><b>Bienvenido:</b>&nbsp;<img src="img/user_suit.png" alt="usuario" height="30" width="30">&nbsp;<?php echo substr($_SESSION["R_ApellidosPersona"],0,1).". ".SUBSTR($_SESSION['R_NombresPersona'],0,15);?> </li>
           	<li><b>Perfil:</b>&nbsp;<img src="img/roles.gif" alt="perfil" height="30" width="30">&nbsp;<?php echo $_SESSION['R_Perfil']?></li>
        </ul>
        <ul class="zoom" style="float:right">
            <li><a style="font-size: 25px;" href="#" onclick="atras();"><b>&nbsp;<img src="img/atras.png" alt="atras" width="30" height="30" longdesc="Atr&aacute;s" >&nbsp;Atr&aacute;s</b></a></li>
            <li><a style="font-size: 25px;" href='cerrarSesion.php?Origen=Mozo'><b>&nbsp;<img src="img/door_in.png" alt="salir" width="30" height="30" longdesc="Cerrar Sesi&oacute;n">&nbsp;Cerrar sesi&oacute;n </b></a></li>    
        </ul>
        </div><!-- inicio menu superior -->
    </td>
  	</tr>
    <tr>
    <td colspan="2" width="100%">
        <div id="opciones" class="zoom2" style="display: none;" align="center">
        <table>
        <tr>
            <td style="display: none;"><input class="button" type="button" value="VER PEDIDO" onclick="verpedido()" /></td>
            <td><input class="button" type="button" value="AGREGAR" onclick="agregarplatos()" /></td>
            <td><input class="button" type="button" value="COMANDA" onclick="imprimircomanda()" /></td>
            <td><input class="button" type="button" value="CUENTA" onclick="imprimircuenta()" /></td>
        </tr>
        </table>
        </div>
        <table width="100%">
            <tr>
                <td width="35%" style="display: inline;">
                    <div id="frame" class="zoom"></div>
                </td>
                <td width="65%">
                    <div id="cargagrilla" class="zoom"></div>
                </td>
            </tr>
        </table>
    </td>
    </tr>
</table>
<br>
<script> 
    function aceptarCambioSucursal(idsucursal){
            var g_ajaxGrabar = new AW.HTTP.Request;  
            g_ajaxGrabar.setURL("controlador/contSesion.php");
            g_ajaxGrabar.setRequestMethod("POST");
            g_ajaxGrabar.setParameter("accion", "CAMBIARSUCURSAL");
            g_ajaxGrabar.setParameter("cboSucursal", idsucursal);
    
            g_ajaxGrabar.response = function(text){
                loading(false, "loading");
                document.getElementById("DivSucursal").style.display='none';
                alert("VISTA MESAS");			
                setRun("vista/frmMozo","&id_clase=46","frame","frame","imgloading");
                //document.getElementById("lblIdSucursal").innerHTML=document.getElementById("cboSucursal").textContent;
				//document.getElementById("lblIdSucursal").innerHTML=document.getElementById("cboSucursal").options[document.getElementById("cboSucursal").value].value;
                document.getElementById("blokeador").style.display='none';
                document.getElementById("blokeador").style.height=document.body.clientHeight+'px';
                document.getElementById("blokeador").style.width=document.body.clientWidth+'px';
            };
            g_ajaxGrabar.request();
            loading(true, "loading", "frame", "line.gif",true);
    }
    document.getElementById("blokeador").style.display='';
    document.getElementById("blokeador").style.height=document.body.clientHeight+'px';
    document.getElementById("blokeador").style.width=document.body.clientWidth+'px';
    //aceptarCambioSucursal(1);
    </script>
    <div id="DivSucursal" style="width:200px;position:absolute;">
    <?php require("vista/tablaheader.php");?>
    <form id="form1" name="form1" method="post" action="">
    <br>
    <br>
    <table><tr><td colspan="2" class="zoom2" align="center">Eliga Sucursal :</td></tr>
    <?php 
    require_once('modelo/clsGeneral.php');
    $objPermiso = new clsGeneral(0, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $rst = $objPermiso->obtenerDataSQL("select idsucursal,razonsocial from sucursal where idsucursal<>14 and idempresa=".$_SESSION['R_IdEmpresa'],'Sucursal',$_SESSION['R_IdSucursal']);
    while($dato=$rst->fetchObject()){
        echo "<tr><td align='center'><input type='button' class='zoom2' value='".substr($dato->razonsocial,0,30)."' onclick=\"javascript:aceptarCambioSucursal('".$dato->idsucursal."');\" /></td></tr>";
    }
    ?></table>
    <br>    
    </form>
    <?php require("vista/tablafooter.php");?>
    </div>
    
</body>
</HTML>