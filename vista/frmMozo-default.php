<?php
session_start();
$id_clase=46;//clase de las mesas
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function ingresar(numero){
    document.getElementById("txtMesa").value=document.getElementById("txtMesa").value + numero;
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
            document.getElementById("Disponible").value = vdisponible;
            document.getElementById("Idmesa").value = vidmesa;
            document.getElementById("Nropersonas").value = vnropersonas;
            enviar(numero);
		};
		g_ajaxPagina.request();
}
function enviar(numero){
    if(document.getElementById("Disponible").value=="true"){
        setRun("vista/frmComanda","&idmesa="+document.getElementById("Idmesa").value+"&mesa="+numero+"&salon="+document.getElementById("Salon").value+"&accion=NUEVO","frame","frame","imgloading");
        //document.getElementById("frame").style.display="none";
        document.getElementById("opciones").style.display="";
    }else{
        g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "verificarusuario");
		g_ajaxPagina.setParameter("IdMesa",document.getElementById("Idmesa").value);
		g_ajaxPagina.response = function(text){
            //if(text=="ok"){
                setRun("vista/frmComanda","&idmesa="+document.getElementById("Idmesa").value+"&mesa="+numero+"&salon="+document.getElementById("Salon").value+"&accion=ACTUALIZAR","frame","frame","imgloading");
                //document.getElementById("frame").style.display="none";
                document.getElementById("opciones").style.display="";
            //}else{//  -> desabilitado momentaneamente
//               alert(text);
            //} 
	   };
        g_ajaxPagina.request();
    }     
}
function genera_cboMesas(idsalon,abreviatura){
		var recipiente = document.getElementById('divdiagramaMesa');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_diagramaMesasMozo");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
            document.getElementById("Salon").value=abreviatura;	
		};
		g_ajaxPagina.request();
}
genera_cboMesas(1,'<?php if($_SESSION['R_IdSucursal']) echo "PR";else echo "SA01";?>');

</script>
</head>
<body>
<center>
<?php require("fun.php"); ?>
<?php require("tablaheader.php");?>
<form method="post">
<input type="hidden" id="Disponible" value="false" />
<title>Diagrama de Mesas</title>
<body>
<br />
<?php 
//<center><label class="zoom2">Sal&oacute;n :</label>

require("../modelo/clsMovimiento.php");
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$rst = $objMantenimiento->obtenerDataSQL("select * from salon where estado='N' and idsucursal=".$_SESSION['R_IdSucursal']);
$c=0;
echo "<table><tr>";
while($dato=$rst->fetchObject() and $c<6){
    echo '<td align="center"><button class="zoom2" type="button" onclick="javascript:genera_cboMesas(\''.$dato->idsalon.'\',\''.$dato->abreviatura.'\');">'.$dato->abreviatura.'</button></td>';
    $c++;
};
echo "</tr></table>";
?>
</center>
<div id="divdiagramaMesa"></div>
<table width="100%">
<td>
<p align="left" class="zoom2">
<img src="img/ocupado.png" width="50" height="50"/> Ocupado&nbsp;&nbsp;</p>
</td><td>
</td>
</table>
<?php require("tablafooter.php");?>
<?/*<table  align="center">
	<tr class="zoom2">
		<td class="zoom2" rowspan="2"></td><td align="right"><b class="zoom2">Mesa</b></td>
		<td><input type="text" name="txtMesa" id="txtMesa" class="zoom2" maxlength="8" size="8"></td>
	</tr>
    <tr align="center">
        <td align="center">&nbsp;</td>
        <td align="center">
            <? genera_bloqueNumerico("zoom2","txtMesa","ingresar","verificarmesa","document.getElementById('txtMesa').value");?>
        </td>
    </tr>	
</table>*/?>
</form>
</center>
</body>