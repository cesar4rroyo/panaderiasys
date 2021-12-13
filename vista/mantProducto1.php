<?php
require("../modelo/clsProducto.php");
require("../modelo/clsListaUnidad.php");
require("../modelo/clsUbicacion.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['IdSucursal'];
//echo $id_clase;
try{
$objMantenimiento = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objUbicacion = new clsUbicacion($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstProducto = $objMantenimiento->obtenerTabla();
if(is_string($rstProducto)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstProducto."</td>";
}else{
	$datoProducto = $rstProducto->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataProductos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarProducto(1,1,'2',1,$_GET["IdProducto"],$id_cliente,"");
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetch();
	}
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmMantProducto");
	}

function aceptar(){
	if(setValidar("frmMantProducto")){
		g_ajaxGrabar.setURL("controlador/contProducto.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}

//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFechaVencimiento", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>

function asignaUbi(idubicacion){
    if(idubicacion==0){
		document.getElementById('divUbi').style.display="none";
	}else{
			document.getElementById('divUbi').style.display="";
			var recipiente = document.getElementById('divUbi');
			g_ajaxPagina = new AW.HTTP.Request;
			g_ajaxPagina.setURL("vista/ajaxUbicacion.php");
			g_ajaxPagina.setRequestMethod("POST");
			g_ajaxPagina.setParameter("accion", "genera_cboColFila");
			g_ajaxPagina.setParameter("IdUbicacion", idubicacion);
			g_ajaxPagina.response = function(text){
				recipiente.innerHTML = text;			
			};
			g_ajaxPagina.request();
	}
}
document.getElementById('txtDescripcion').focus()
</script>
</head>
<body>
<?php require("tablaheader.php");?>
<form id="frmMantProducto" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['IdProducto'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
<table width="400" >
<?php
reset($dataProductos);
foreach($dataProductos as $value){
?>
	<?php if($value["idcampo"]==4){?>
	<tr><td width="120"><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $objMantenimiento->generaCodigo();
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" style="text-transform:uppercase" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")", $value["descripcion"], $dato[strtolower($value["descripcion"])],'',$objMantenimiento,'', 'Ninguna'); else echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")",$value["descripcion"],0,'',$objMantenimiento,'', 'Ninguna');?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==7){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, '', 'Ninguna'); else echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<?php 
		if($_GET["accion"]=="ACTUALIZAR"){
		$rstListaunidad1 = $objMantenimiento->buscarxidproductoyidunidad($_GET["IdProducto"],NULL);
		$CantListaUnidad=$rstListaunidad1->rowCount();
		$rstListaunidad = $objMantenimiento->buscarxidproductoyidunidad($_GET["IdProducto"],$dato[strtolower($value["descripcion"])]);
		$datosListaUnidad=$rstListaunidad->fetchObject();
		}
		?>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") { if($CantListaUnidad==1){ echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, '');}else{echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'disabled',$objMantenimiento, '');} }else{ echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '');}?></td></tr>
        <tr><td>Precio Compra</td><td><input type="hidden" id="txtIdListaUnidad" name = "txtIdListaUnidad" value = "<?php if($_GET["accion"]=="ACTUALIZAR") echo $datosListaUnidad->idlistaunidad;
	?>"><input type="Text" id="txtPrecioCompra" name = "txtPrecioCompra" value = "<?php if($_GET["accion"]=="ACTUALIZAR") echo $datosListaUnidad->preciocompra;
	?>" size="10" maxlength="10" title="Debe indicar un precio de compra" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
    <tr><td>Precio Mano Obra</td><td><input type="Text" id="txtPrecioManoObra" name = "txtPrecioManoObra" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->preciomanoobra;
	?>" size="10" maxlength="10" title="Debe indicar un precio de obra" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
    <tr><td>Precio Venta</td><td><input type="Text" id="txtPrecioVenta" name = "txtPrecioVenta" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->precioventa;
	?>" size="10" maxlength="10" title="Debe indicar un precio de venta" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
    <tr><td>Precio Venta para Llevar</td><td><input type="Text" id="txtPrecioVenta2" name = "txtPrecioVenta2" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->precioventa2;
	?>" size="10" maxlength="10" title="Debe indicar un precio de venta para levar" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==10){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N' AND tipo='M'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, ''); else echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N' AND tipo='M'",$value["descripcion"],0,'',$objMantenimiento, '');?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==11){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $_SESSION["FechaProceso"];?>" size="10" maxlength="10" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?>><button id="btnCalendar" type="button" class="boton" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>><img src="img/date.png" width="16" height="16"> </button></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==12){?>
	<tr><td width="120"><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==13){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==14){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==15){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==16){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr></table></div>
            <table  width="400">
	<?php }?>
    <?php if($value["idcampo"]==17){?>
	<tr><td width="120"><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Ubicacion Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, "asignaUbi(this.value)", 'Ninguna'); else echo genera_cboGeneralSQL("Select * from Ubicacion Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, "asignaUbi(this.value)", 'Ninguna');?></td></tr></table>
        <div id="divUbi" style="display:none"></div>
            <table  width="400">
	   <?php }?>

	<?php if($value["idcampo"]==20){?>
    <tr><td><?php echo $value["comentario"];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><input type="Checkbox" name="chkKardex" id="chkKardex" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?> onClick="javascript: if(document.getElementById('divKardex').style.display=='') {document.getElementById('divKardex').style.display='none'; }else{document.getElementById('divKardex').style.display='';}">
	</td></tr></table>
	<div id="divKardex" style="display:none"><table  width="400">
	<?php }?>
    <?php if($value["idcampo"]==21){?>
	<tr><td><?php echo $value["comentario"];?></td><td><input type="Checkbox" name="chkCompuesto" id="chkCompuesto" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==22){?>
	<tr><td width="120"><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" style="text-transform:uppercase" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==23){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php /*if($value["idcampo"]==24){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }*/?>    
<?php }?>
	<tr>
	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
	</tr>
</table>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listProducto','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoProducto->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoProducto->descripcionmant; ?></td>
</tr></table>
</div>
</body>
</HTML>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           