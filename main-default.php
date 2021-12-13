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
<!--<link href="css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">-->
<!--<link href="css/estiloverde/estiloverde.css" rel="stylesheet" type="text/css">-->
<link href="css/<?php echo $_SESSION['R_Estilo'];?>/<?php echo $_SESSION['R_Estilo'];?>.css" rel="stylesheet" type="text/css">
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
        var top=(document.body.clientHeight/4)+"px"; 
        var left1=(document.body.clientWidth/2);
        var left=(left1-parseInt(document.getElementById("DivSucursal").style.width)/2)+"px"; 
        document.getElementById("DivSucursal").style.top=top; 
        document.getElementById("DivSucursal").style.left=left; 
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
<div id="blokeador" style="position:absolute;display:none; background-image:url(img/semitransparente.jpg); background-color:#FFFFFF; filter:alpha(opacity=55);opacity:0.55;"></div>
<?php 
require('modelo/clsGeneral.php');
$idtabladefecto=0;
$acciondefecto="";
?>
<table width="100%" border="0" align="center">
<tr>
    	<td width="200">&nbsp; </td>
    	<td class="titulo">SISREST - Sistema Est&aacute;ndar para Restaurante</td>
</tr>
  	<tr>
    	<td width="200" align="center"><a href="main.php"><?php if($_SESSION['R_Logo']<>''){ ?><img src="img/empresas/<?php echo $_SESSION['R_Logo'];?>" width="200" height="50" alt="Logo Empresa" title="Logo Empresa"/><?php }else{ ?><img src="img/<?php echo 'razon.png';?>" width="200" height="50" alt="Logo Empresa"  title="Logo Empresa"/><?php }?></a></td>
    	<td>
<div id="barramenusup"> <!-- inicio menu superior -->
<ul style="float:left">
   	<li><b>Empresa:</b>&nbsp;<img src="img/empresa.png" alt="perfil" height="16" width="16">&nbsp;<?php echo $_SESSION['R_NombreEmpresa']?></li>
   	<li><b>Establecimiento:</b>&nbsp;<img src="img/sucursal.png" alt="perfil" height="16" width="16">&nbsp;<label id="lblIdSucursal"><?php echo $_SESSION['R_NombreSucursal']?></label></li>
</ul>
<ul style="float:right">
	<li><b>Bienvenido:</b>&nbsp;<img src="img/user_suit.png" alt="usuario" height="16" width="16">&nbsp;<?php echo $_SESSION['R_NombreUsuario']?>&nbsp;<img src="img/down.gif" alt="opciones" onclick="javascript: if(divOpcionesPerfil.style.display==''){divOpcionesPerfil.style.display='none';}else{divOpcionesPerfil.style.display='';}" style="cursor:pointer"></li><div id="divOpcionesPerfil" style="position:absolute; display:none; width:140px"><br /><br /><ul><li style="width:100%"><a href="#" onclick="javascript: setRun('vista/mantPerfilUsuario', '&id_clase=16', 'frame', 'carga', 'imgloading');divOpcionesPerfil.style.display='none';">Mi&nbsp;Perfil</a></li><li style="width:100%"><a href="#" onclick="javascript: setRun('vista/mantUsuarioClave', '&id_clase=16', 'frame', 'carga', 'imgloading');divOpcionesPerfil.style.display='none';">Cambiar&nbsp;clave</a></li></ul></div>
   	<li><b>Perfil:</b>&nbsp;<img src="img/roles.gif" alt="perfil" height="16" width="16">&nbsp;<?php echo $_SESSION['R_Perfil']?></li>
    <li><a href='cerrarSesion.php'>&nbsp;<img src="img/door_in.png" alt="salir" width="16" height="16" longdesc="Cerrar Sesi&oacute;n">&nbsp;Cerrar sesi&oacute;n </a></li>
    <li><a href='cerrarSesion.php?a=SALIR' onclick="javascript: if(confirm('Esta seguro que desea salir de SISREST?')) return true; else return false;">&nbsp;<img src="img/popup_close.gif" alt="salir" width="16" height="16" longdesc="Cerrar Sesi&oacute;n">&nbsp;Salir </a></li>
</ul>
</div><!-- inicio menu superior -->
</td>
  	</tr>
  	<tr>
    	<td valign="top"><div id="menu">
		<?php
		$objPermiso = new clsGeneral(0, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		$rstPermisos = $objPermiso->obtenerPermisos();
		if(is_string($rstPermisos)){
			echo "<td colspan=100>Sin Permisos</td></tr><tr><td colspan=100>".$rstPermisos."</td>";
		}else{
			$inicio = 0;
			$inicio2 = 0;
			echo "<ul>";
			//print "<input type='text' value=\"";
			while($datoPermisos = $rstPermisos->fetchObject()){
				//echo utf8_decode($datoPermisos->modulo);
				if($inicio2!= $datoPermisos->idmodulo){
					if($inicio2!=0){
						echo "</div></div>";
					}					
					echo "<li><a href='javascript:void('0');' onclick=\"muestraEnlaces('enlace".$datoPermisos->idmodulo."')\"><img id='imgenlace".$datoPermisos->idmodulo."' src='img/i_expand.png' border='0' size='16' width='16'>".umillmain($datoPermisos->modulo)."</a></li>";
					if($datoPermisos->expandido=="S"){$ver="muestra";}else{$ver="oculta";}
					echo "<div id='enlace".$datoPermisos->idmodulo."' class='$ver'>";
					$inicio2 = $datoPermisos->idmodulo;
					$inicio=0;
				}
				if($inicio != $datoPermisos->idmenuprincipal){
					if($inicio!=0){
						echo "</div>";
					}
					echo "<li><a href='javascript:void(0)' onclick=\"muestraEnlaces('interno".$inicio2.$datoPermisos->idmenuprincipal."')\">&nbsp;&nbsp;&nbsp;&nbsp;<img id='imginterno".$inicio2.$datoPermisos->idmenuprincipal."' src='img/i_expand.png' border='0' size='16' width='16'>".umillmain($datoPermisos->menuprincipal)."</a></li>";
					if($datoPermisos->menuexpandido=="S"){$verMenu="muestra";}else{$verMenu="oculta";}
					echo "<div id='interno".$inicio2.$datoPermisos->idmenuprincipal."' class='$verMenu'>";
					$inicio = $datoPermisos->idmenuprincipal;
				}
				echo "<li><a href=\"javascript:void('0');\" title =\"".umill($datoPermisos->diccionario)."\" onClick=\"javascript:setRun('vista/".$datoPermisos->accion."','&id_clase=".umill($datoPermisos->idtabla)."','frame','carga','imgloading');\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".umillmain($datoPermisos->descripcion)."</a></li>";
				
				//OBTENER DATOS DE LA OPCION MENU POR DEFECTO
				if($_SESSION['R_OpcionMenuDefecto']==$datoPermisos->idopcionmenu){
					$idtabladefecto=$datoPermisos->idtabla;
					$acciondefecto=$datoPermisos->accion;
				}
			}
			//print ">";
			//echo "</table>";
			echo "</ul>";
			
		}
		?></div></td>
	<td valign="top"><div id="carga"></div>
		<div id="cargagrilla"></div>
		<div id="frame"></div>
	</td>
  	</tr>
 	<tr>
    		<td colspan="2">
<p id="copyright" class="MsoNormal" align="center" style="text-align:center">
<font color="#666666">
<br>Chiclayo - Per&uacute;<br>
<a style="font-size:8pt" href="#" onClick="javascript: if(DivEquipo.style.display!='')DivEquipo.style.display='';else DivEquipo.style.display='none';">Desarrolladores</a>
<div id='DivEquipo' align="center" style="display:none">
<strong>Jefe de Proyecto:</strong><br>
Ing. Martin Ampuero Pasco<br>
<strong>Programador:</strong><br>
Jos&eacute; Alexander Samam&eacute; Nizama<br>
</div>
</span></font></p>
			</td>
	</tr>
</table>
<script>
//var fechafin=new Date();
//"Fecha ini "+fechainicio +" fin "+fechafin
//alert("Fecha "+fechainicio+" fin " + fechafin);
</script>

<br>
<?php
if(isset($_GET['idclase'])){
	$ruta=$_GET['ruta'];
	$idclase=$_GET['idclase'];
	$id=1;?>
	<script>setRun('<?php echo $ruta;?>', '&id_clase=<?php echo $idclase;?>', 'frame', 'carga', 'imgloading');</script>
<?php
}else{
?>
	<?php if($_SESSION['R_Compartido']=='S'){?>
    <script> 
    function aceptarCambioSucursal(){
        //if(setValidar()){
            var g_ajaxGrabar = new AW.HTTP.Request;  
            g_ajaxGrabar.setURL("controlador/contSesion.php");
            g_ajaxGrabar.setRequestMethod("POST");
            g_ajaxGrabar.setParameter("accion", "CAMBIARSUCURSAL");
            g_ajaxGrabar.setParameter("cboSucursal", document.getElementById("cboSucursal").value);
            g_ajaxGrabar.setParameter("cboCaja", document.getElementById("cboCaja").value);
    
            g_ajaxGrabar.response = function(text){
                loading(false, "loading");
                document.getElementById("DivSucursal").style.display='none';
                alert(text);			
                document.getElementById("lblIdSucursal").innerHTML=document.getElementById("cboSucursal").textContent;
				//document.getElementById("lblIdSucursal").innerHTML=document.getElementById("cboSucursal").options[document.getElementById("cboSucursal").value].value;
                document.getElementById("blokeador").style.display='none';
                document.getElementById("blokeador").style.height=document.body.clientHeight+'px';
                document.getElementById("blokeador").style.width=document.body.clientWidth+'px';
            };
            g_ajaxGrabar.request();
            loading(true, "loading", "frame", "line.gif",true);
        <?php if($idtabladefecto!=0){?>
            setRun("vista/<?php echo $acciondefecto;?>","&id_clase=<?php echo $idtabladefecto;?>","frame","carga","imgloading");
        <?php }?>		
        //}
    }
    document.getElementById("blokeador").style.display='';
    document.getElementById("blokeador").style.height=document.body.clientHeight+'px';
    document.getElementById("blokeador").style.width=document.body.clientWidth+'px';
    </script>
    <div id="DivSucursal" style="width:200px;position:absolute;">
    <?php require("vista/tablaheader.php");?>
    <form id="form1" name="form1" method="post" action="">
    <br>
    <br>
    <table>
        <tr>
            <td>Sucursal :</td><td>
            <?php 
            genera_cboGeneralSQL("select idsucursal,razonsocial from sucursal where idempresa=".$_SESSION['R_IdEmpresa'],'Sucursal',$_SESSION['R_IdSucursal'],'',$objPermiso);
            ?></td>
        </tr>
        <tr>
            <td>Caja :</td>
            <td><select id="cboCaja" name="cboCaja">
                <option value="EPSON TM-U220 Receipt(2)">Caja 1</option>
                <option value="EPSON TM-U220 Receipt(3)">Caja 2</option>
                </select>
            </td>
        </tr>
    </table>
    <br>
    <center><input type="button" value="Aceptar" onclick="javascript:aceptarCambioSucursal()"></center>
    </form>
    <?php require("vista/tablafooter.php");?>
    </div>
    <?php }else{?>
        <?php if($idtabladefecto!=0){?>
        <script>
            setRun("vista/<?php echo $acciondefecto;?>","&id_clase=<?php echo $idtabladefecto;?>","frame","carga","imgloading");
        </script>
        <?php }?>
    <?php }?>
<?php }?>
</body>
</HTML>