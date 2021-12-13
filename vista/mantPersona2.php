<?php
require("../modelo/clsPersona.php");
require_once('../modelo/clsPersonaMaestro.php');
$id_clase = $_GET["id_clase"];
//echo $id_clase;
try{
$objMantenimiento = new clsPersona($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objPersonaMaestro = new clsPersonaMaestro(22,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstTabla = $objMantenimiento->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}

$rst = $objMantenimiento->obtenerCamposMostrar("F");
$dataCampos = $rst->fetchAll();

$rstTabla2 = $objPersonaMaestro->obtenerTabla();
if(is_string($rstTabla2)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstTabla2."</td>";
}else{
	$datoTablaPersonaMaestro = $rstTabla2->fetchObject();
}

$rst2 = $objPersonaMaestro->obtenerCamposMostrar("F");
$dataCamposPersonaMaestro = $rst2->fetchAll();


if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarPersona(1,1,'1',1,$_GET["IdSucursal"],$_GET["IdPersona"],$_GET['IdPersonaMaestro']);
	$dato = $rst->fetch();
}

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarPersonaMaestro(1,1,'1',1,$_GET["IdPersonaMaestro"]);
	$datoPersonaMaestro = $rst->fetch();
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
	getFormData("frmMantPersona");
	/*g_ajaxGrabar.setParameter("txtIdPersona", document.getElementById("txtIdPersona").value);
	g_ajaxGrabar.setParameter("txtIdSucursal", document.getElementById("txtIdSucursal").value);
	g_ajaxGrabar.setParameter("txtApellidos", document.getElementById("txtApellidos").value);
	g_ajaxGrabar.setParameter("txtNombres", document.getElementById("txtNombres").value);
	g_ajaxGrabar.setParameter("cboTipoPersona", document.getElementById("cboTipoPersona").value);
	g_ajaxGrabar.setParameter("txtNroDoc", document.getElementById("txtNroDoc").value);*/
	if(document.getElementById("optM").checked){
		g_ajaxGrabar.setParameter("optSexo", "M");
	}
	if(document.getElementById("optF").checked){
		g_ajaxGrabar.setParameter("optSexo", "F");
	}
	/*
	g_ajaxGrabar.setParameter("txtIdPersonaMaestro", document.getElementById("txtIdPersonaMaestro").value);
	g_ajaxGrabar.setParameter("txtDireccion", document.getElementById("txtDireccion").value);
	g_ajaxGrabar.setParameter("txtEmail", document.getElementById("txtEmail").value);
	g_ajaxGrabar.setParameter("txtTelefonoFijo", document.getElementById("txtTelefonoFijo").value);
	g_ajaxGrabar.setParameter("txtTelefonoMovil", document.getElementById("txtTelefonoMovil").value);
	g_ajaxGrabar.setParameter("cboDist", document.getElementById("cboDist").value);
	g_ajaxGrabar.setParameter("txtImagen", document.getElementById("txtImagen").value);	
	g_ajaxGrabar.setParameter("cboIdRol", document.getElementById("cboIdRol").value);*/
}
function aceptar(){
	if(!valEmail(document.getElementById('txtEmail').value) && document.getElementById('txtEmail').value!=''){alert('La dirección de correo no es correcta');document.getElementById('txtEmail').focus();return false;}
	if(setValidar("frmMantPersona")){
		g_ajaxGrabar.setURL("controlador/contPersona.php?ajax=true");
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
function buscaPersonaMaestro(nrodoc,tipo,mensaje){
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "BuscaxNroDoc");
	g_ajaxPagina.setParameter("nrodoc", nrodoc);
	g_ajaxPagina.setParameter("tipo", tipo);
	g_ajaxPagina.response = function(xform){
		eval(xform);
		if(vError==0){
        document.getElementById('txtIdPersonaMaestro').value = vIdPersonaMaestro;
		document.getElementById('txtApellidos').value = vApellidos;
		document.getElementById('txtNombres').value = vNombres;
		if(vSexo=='M'){
			document.getElementById('optM').checked = "checked";
		}else{
			document.getElementById('optF').checked = "checked";
		}
		
		document.getElementById("txtApellidos").disabled = "disabled";
		document.getElementById('txtNombres').disabled = "disabled";
		document.getElementById('optM').disabled = "disabled";
		document.getElementById('optF').disabled = "disabled";
		}else{
			document.getElementById("txtApellidos").value = "";
			document.getElementById('txtNombres').value = "";
			document.getElementById('optM').checked = "checked";
			document.getElementById('optF').checked = "";
		
			document.getElementById("txtApellidos").disabled = "";
			document.getElementById('txtNombres').disabled = "";
			document.getElementById('optM').disabled = "";
			document.getElementById('optF').disabled = "";
			if(mensaje!='NO'){
			alert("No se encontro a la persona con el Numero de Documento: "+nrodoc);
			}
		}
	};
	g_ajaxPagina.request();
}
function borraDatosEncontrados(e)
{
	if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;   
	if(keyc == 8 || keyc == 46) {
		if(document.getElementById('txtNombres').disabled!=""){
			document.getElementById('txtIdPersonaMaestro').value = "";
			document.getElementById('txtApellidos').value = "";
			document.getElementById('txtNombres').value = "";
			document.getElementById('optM').checked = "checked";
			document.getElementById('optF').checked = "";
		
			document.getElementById("txtApellidos").disabled = "";
			document.getElementById('txtNombres').disabled = "";
			document.getElementById('optM').disabled = "";
			document.getElementById('optF').disabled = "";
		}
	}
}
var cont=0;
function verDpto(id,Disabled){
	var recipiente = document.getElementById('DivDpto');
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxUbigeo.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("action", "verDpto");
	g_ajaxPagina.setParameter("seleccionado", id);
	g_ajaxPagina.setParameter("Disabled", Disabled);
	g_ajaxPagina.response = function(xform){
		recipiente.innerHTML = xform;
		if(cont==0){
			
			<?php if($_GET['accion']=='ACTUALIZAR') {?>
			VerProv2(id,<?php echo $dato['idprovincia']?>,'');
			<?php }else{?>
			VerProv();
			<?php } ?>
		}else{
			if(cont==1){
				VerProv();
			}
		}
	};
	g_ajaxPagina.request();
}
function VerProv(){
	vvalor=document.getElementById('cboDpto').value;
	VerProv2(vvalor, 0);
}
function VerDist(){
	vvalor=document.getElementById('cboProv').value;
	VerDist2(vvalor, 0);
}
function VerProv2(iddpto,id,Disabled){
	var recipiente = document.getElementById('DivProv');
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxUbigeo.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("action", "verProv");
	g_ajaxPagina.setParameter("iddpto", iddpto);
	g_ajaxPagina.setParameter("seleccionado", id);
	g_ajaxPagina.setParameter("Disabled", Disabled);
	g_ajaxPagina.response = function(xform){
		recipiente.innerHTML = xform;	
		if(cont==0){
			<?php if($_GET['accion']=='ACTUALIZAR') {?>
			VerDist2(id,<?php echo $dato['iddistrito']?>,'');
			<?php }else{?>
			VerDist();
			<?php } ?>
		}else{
			if(cont==1){
				VerDist();
			}
		}
	};
	g_ajaxPagina.request();
}
function VerDist2(idprov,id,Disabled){
	var recipiente = document.getElementById('DivDist');
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxUbigeo.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("action", "verDist");
	g_ajaxPagina.setParameter("idprov", idprov);
	g_ajaxPagina.setParameter("seleccionado", id);
	g_ajaxPagina.setParameter("Disabled", Disabled);
	g_ajaxPagina.response = function(xform){
		recipiente.innerHTML = xform;	
		cont=1;
	};
	g_ajaxPagina.request();
}
<?php if($_GET['accion']=='ACTUALIZAR') {?>
verDpto(<?php echo $dato['iddepartamento']?>,'');
<?php }else{?>
verDpto(1347);
<?php } ?>
function cambiaNroDoc(tipopersona){
	if(tipopersona=='VARIOS'){
		divNroDoc.innerHTML='DNI';
		document.getElementById("txtNroDoc").size=8;
		document.getElementById("txtNroDoc").maxLength=8;
		document.getElementById("txtNroDoc").value=document.getElementById("txtNroDoc").value.substr(0,8);
		divNombres.innerHTML='Nombres';
		trApellido.style.display="";
		trSexo.style.display="";
		trFechaNac.style.display="";
	}else{
		divNroDoc.innerHTML='RUC';
		document.getElementById("txtNroDoc").size=11;
		document.getElementById("txtNroDoc").maxLength=11;
		if(tipopersona=='JURIDICA'){
			divNombres.innerHTML='Razon Social';
			trApellido.style.display="none";
			trSexo.style.display="none";
			trFechaNac.style.display="none";
			document.getElementById("txtApellidos").value="";
			document.getElementById("optM").checked="checked";
		}else{
			divNombres.innerHTML='Nombres';
			trApellido.style.display="";
			trSexo.style.display="";
			trFechaNac.style.display="";
		}
	}
}
<?php if($_GET['accion']=='ACTUALIZAR'){?>
cambiaNroDoc('<?php echo $datoPersonaMaestro['tipopersona']?>');
<?php }?>

//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFechaNac", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>
</script>
</head>
<body>
<?php
//echo $_GET["IdSucursal"].'-'.$_GET["IdPersona"].'-'.$_GET['IdPersonaMaestro'];
require ('fun.php');
?>
<?php require("tablaheader.php");?>
<form id="frmMantPersona" action="" method="POST">
<input type="hidden" id="txtIdPersona" name = "txtIdPersona" value = "<?php if($_GET['accion']=='ACTUALIZAR') echo $_GET['IdPersona']; ?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php if($_GET['accion']=='ACTUALIZAR') echo $_GET['IdSucursal']; else echo $_SESSION['R_IdSucursal'];?>">
<input type="hidden" id="txtIdPersonaMaestro" name = "txtIdPersonaMaestro" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato["idpersonamaestro"]), ENT_QUOTES, "UTF-8");
	?>">
<table width="200" border="0">
<tr><td>Tipo Persona</td>
<td>
<select id="cboTipoPersona" name="cboTipoPersona" onChange="cambiaNroDoc(this.value)" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
<option value="JURIDICA" <?php if($_GET["accion"]=="ACTUALIZAR") {if($datoPersonaMaestro["tipopersona"]=='JURIDICA') echo 'selected';}?>>Jur&iacute;dica</option>
<option value="NATURAL" <?php if($_GET["accion"]=="ACTUALIZAR") {if($datoPersonaMaestro["tipopersona"]=='NATURAL') echo 'selected';}?>>Natural</option>
<option value="VARIOS" <?php if($_GET["accion"]=="ACTUALIZAR") {if($datoPersonaMaestro["tipopersona"]=='VARIOS') echo 'selected';}?>>Varios</option>
</select>
</td></tr>
<tr><td><div id="divNroDoc">RUC</div></td>
    	<td><input type="Text" id="txtNroDoc" name = "txtNroDoc" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($datoPersonaMaestro["nrodoc"]), ENT_QUOTES, "UTF-8");
	?>" size="11" maxlength="11"  <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?> <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> /><button type="button" onClick="buscaPersonaMaestro(txtNroDoc.value,cboTipoPersona.value,'SI')" title="Busca Persona Maestro" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>><img src="img/b_search.png" align="absbottom"></button><sup title="Obligatorio">(&lowast;)</sup> <label id="LabelVerificaNroDoc" style="color: #003399"></label></td></tr>
<?php
//CAMPOS DE LA ALUMNO MAESTRO
reset($dataCamposPersonaMaestro);
foreach($dataCamposPersonaMaestro as $value){
?>
	<?php if($value["idcampo"]==3){?>
	<tr><td><div id="divNombres">Razon Social</div></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($datoPersonaMaestro[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" style="text-transform:uppercase" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==4){?>
	<tr id="trApellido" style="display:none"><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($datoPersonaMaestro[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" style="text-transform:uppercase" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
   	<?php if($value["idcampo"]==6){?>
	<tr id="trSexo" style="display:none"><td><?php echo $value["comentario"];?></td>
    	<td>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optM" value="M" <?php if($_GET["accion"]=="ACTUALIZAR"){if($datoPersonaMaestro[strtolower($value["descripcion"])]=="M"){ echo "checked=checked";}}else{ echo "checked=checked";}?>>
    	    M</label>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optF" value="F" <?php if($_GET["accion"]=="ACTUALIZAR"){if($datoPersonaMaestro[strtolower($value["descripcion"])]=="F"){ echo "checked=checked";}}?>>
    	    F</label></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
    <tr id="trFechaNac" style="display:none">
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($datoPersonaMaestro[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");?>" size="10" maxlength="10"><button id="btnCalendar" type="button" class="boton"><img src="img/date.png" width="16" height="16"> </button></td></tr>
	<?php }?>
<?php }
//CAMPOS DEL ALUMNO
reset($dataCampos);
foreach($dataCampos as $value){
?>
	<?php if($value["idcampo"]==7){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td colspan="2"><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==8){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td colspan="2"><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td colspan="2"><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> onKeyPress="return validarsolonumeros(event)"></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==10){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td colspan="2"><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> onKeyPress="return validarsolonumeros(event)"></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==11){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td colspan="2"><div id="DivUbigeo">
<table>
<tr>
<td>Departamento :</td>
<td><div id="DivDpto"></div></td>
</tr>
<tr>
<td>Provincia :</td>
<td><div id="DivProv"></div></td>
</tr>
<tr>
<td>Distrito :</td>
<td><div id="DivDist"></div></td>
</tr>
</table>
</div></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==12){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td colspan="2"><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==17){?>
	<tr><td><?php echo $value["comentario"];?></td><td><input type="Checkbox" name="chkCompartido" id="chkCompartido" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>>
	</td>
    	</tr>
	<?php }?>
<?php }?>
<?php if($_GET["accion"]<>"ACTUALIZAR"){?>
	<tr><td>Rol</td>
    	<td><?php echo genera_cboGeneralSQL("select * from rol where estado='N'","IdRol",0,'',$objMantenimiento);?></td></tr>
<?php }?>
	<tr>
	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td colspan="2"><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
	</tr>
</table>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listPersona','&id_clase=<?php echo $_GET['id_clase'];?>','frame', 'frame', 'img02')"><?php echo $datoTabla->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoTabla->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               