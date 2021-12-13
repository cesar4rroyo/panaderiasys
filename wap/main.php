<?php
session_start();
require_once '../vista/fun.php';
require('../modelo/clsGeneral.php');
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/estiloazul/estiloazul.css" rel="stylesheet" type="text/css">
<script>
function muestraEnlaces(id){
	if(document.getElementById(id).className=="oculta"){
		document.getElementById(id).className = "muestra";
		document.getElementById("img"+id).src = "../img/i_colpse.png";
	}else{
		document.getElementById(id).className = "oculta";
		document.getElementById("img"+id).src = "../img/i_expand.png";
	}
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
<title>SISPAMER - Sistema Estandar Parametrizable para Restaurante - WAP</title>
<link rel="shortcut icon" href="../img/24 Custom.ico" />
</head>

<body>
<table width="100%" align="center">
<tr>
    	<td class="titulo">SISPAMER - Sistema Estandar Parametrizable para Restaurante</td>
</tr>
<tr><td id="barramenusup">
<div id="barramenusup" align="center"> <!-- inicio menu superior -->
<img src="../img/empresa.png" alt="perfil" height="16" width="16">&nbsp;<font size="+1"><?php echo $_SESSION['R_NombreEmpresa']?>&nbsp;-&nbsp;<?php echo $_SESSION['R_NombreSucursal']?></font>
  <ul>
    <li><b>Bienvenido:</b>&nbsp;<img src="../img/user_suit.png" alt="usuario" height="16" width="16">&nbsp;<?php echo $_SESSION['R_NombreUsuario']?>&nbsp;-&nbsp;<img src="../img/roles.gif" alt="perfil" height="16" width="16">&nbsp;<?php echo $_SESSION['R_Perfil']?></li>
    <li><a href='cerraSesion.php' title="Cerrar Sesi&oacute;n">&nbsp;<img src="../img/door_in.png" alt="salir" width="16" height="16" longdesc="Cerrar Sesi&oacute;n"></a></li>
  </ul>
  </div><!-- inicio menu superior -->
</td></tr>
  	<tr>
   	  <td valign="top"><div id="menu">
      <?php require "tablaheader.php";?>
   	    <?php
		$objPermiso = new clsGeneral(0, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
		$rstPermisos = $objPermiso->obtenerPermisos('S');
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
					echo "<li><a href='#' onclick=\"muestraEnlaces('enlace".$datoPermisos->idmodulo."')\"><img id='imgenlace".$datoPermisos->idmodulo."' src='../img/i_expand.png' border='0' size='16' width='16'>".umillmain($datoPermisos->modulo)."</a></li>";
					if($datoPermisos->expandido=="S"){$ver="muestra";}else{$ver="oculta";}
					echo "<div id='enlace".$datoPermisos->idmodulo."' class='$ver'>";
					$inicio2 = $datoPermisos->idmodulo;
					$inicio=0;
				}
				if($inicio != $datoPermisos->idmenuprincipal){
					if($inicio!=0){
						echo "</div>";
					}
					echo "<li><a href='#' onclick=\"muestraEnlaces('interno".$inicio2.$datoPermisos->idmenuprincipal."')\">&nbsp;&nbsp;&nbsp;&nbsp;<img id='imginterno".$inicio2.$datoPermisos->idmenuprincipal."' src='../img/i_expand.png' border='0' size='16' width='16'>".umillmain($datoPermisos->menuprincipal)."</a></li>";
					if($datoPermisos->menuexpandido=="S"){$verMenu="muestra";}else{$verMenu="oculta";}
					echo "<div id='interno".$inicio2.$datoPermisos->idmenuprincipal."' class='$verMenu'>";
					$inicio = $datoPermisos->idmenuprincipal;
				}
				echo "<li><a title =\"".umill($datoPermisos->diccionario)."\" href=\"".$datoPermisos->accion.".php?id_clase=".umill($datoPermisos->idtabla)."\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".umillmain($datoPermisos->descripcion)."</a></li>";
				
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
		?>
        <?php require "tablafooter.php";?>
   	    </div>
  	  </td>
  	</tr>
</table>
</body>
</html>