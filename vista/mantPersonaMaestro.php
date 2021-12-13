<?php
require("../modelo/clsPersonaMaestro.php");
$id_clase = $_GET["id_clase"];
//echo $id_clase;
try{
$objMantenimiento = new clsPersonaMaestro($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarPersonaMaestro(1,1,'1',1,$_GET["Id"]);
	$dato = $rst->fetch();
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
	g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtApellidos", document.getElementById("txtApellidos").value);
	g_ajaxGrabar.setParameter("txtNombres", document.getElementById("txtNombres").value);
	g_ajaxGrabar.setParameter("cboTipoPersona", document.getElementById("cboTipoPersona").value);
	g_ajaxGrabar.setParameter("txtNroDoc", document.getElementById("txtNroDoc").value);
	if(document.getElementById("optM").checked){
		g_ajaxGrabar.setParameter("optSexo", "M");
	}
	if(document.getElementById("optF").checked){
		g_ajaxGrabar.setParameter("optSexo", "F");
	}
	g_ajaxGrabar.setParameter("txtFechaNac", document.getElementById("txtFechaNac").value);
}
function aceptar(){
	if(setValidar("frmMantPersonaMaestro")){
		g_ajaxGrabar.setURL("controlador/contPersonaMaestro.php?ajax=true");
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
function verificaNroDoc(nro,tipo)
{
		var g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "verificaNroDoc");
		g_ajaxPagina.setParameter("nrodoc", nro);
		g_ajaxPagina.setParameter("tipo", tipo);
		g_ajaxPagina.response = function(text){
			eval(text);
			if(vCant>0){
				LabelVerificaNroDoc.innerHTML="El N&uacute;mero de Documento ya existe";
			}else{
				LabelVerificaNroDoc.innerHTML="";
			}
		};
		g_ajaxPagina.request();
}
<?php if($_GET['accion']=='ACTUALIZAR'){?>
cambiaNroDoc('<?php echo $dato['tipopersona']?>');
<?php }?>
<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantPersonaMaestro','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantPersonaMaestro','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
<?php
require ('fun.php');
?>
    <div class="container">
        <form id="frmMantPersonaMaestro" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<div class="row Mesas">
                <div class="col s12 m12 l10 offset-l1">
                    <table>
<?php
reset($dataCampos);
foreach($dataCampos as $value){
?>
	<?php if($value["idcampo"]==2){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td>
    <select id="cbo<?php echo $value["descripcion"];?>" name="cbo<?php echo $value["descripcion"];?>" onChange="cambiaNroDoc(this.value)">
    <option value="JURIDICA" <?php if($_GET["accion"]=="ACTUALIZAR") {if($dato[strtolower($value["descripcion"])]=='JURIDICA') echo 'selected';}?>>Jur&iacute;dica</option>
    <option value="NATURAL" <?php if($_GET["accion"]=="ACTUALIZAR") {if($dato[strtolower($value["descripcion"])]=='NATURAL') echo 'selected';}?>>Natural</option>
    <option value="VARIOS" <?php if($_GET["accion"]=="ACTUALIZAR") {if($dato[strtolower($value["descripcion"])]=='VARIOS') echo 'selected';}?>>Varios</option>
    </select>
    </td></tr>
	<?php }?>
    <?php if($value["idcampo"]==3){?>
	<tr><td><div id="divNombres">Razon Social</div></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" style="text-transform:uppercase" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
	<tr><td><div id="divNroDoc">RUC</div></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
        ?>" size="11" maxlength="11" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) return false;" onBlur="verificaNroDoc(this.value,cboTipoPersona.value);" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?>> <label hidden="" id="LabelVerificaNroDoc" style="color: #003399"></label></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==4){?>
    <tr id="trApellido" style="display:none"><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" style="text-transform:uppercase" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
   	<?php if($value["idcampo"]==6){?>
	<tr id="trSexo" style="display:none"><td><?php echo $value["comentario"];?></td>
    	<td>
            <p>
                <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optM" value="M" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="M"){ echo "checked=checked";}}else{ echo "checked=checked";}?>>
                <label for="optM">M</label>
            </p>
            <p>
                <input type="radio" name="opt<?php echo $value["descripcion"];?>" value="F" id="optF" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="F"){ echo "checked=checked";}}?>>
                <label for="optF">F</label>
            </p>
        </td></tr>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
    <tr id="trFechaNac" style="display:none">
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="date" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");?>" size="10" maxlength="10"><!--button id="btnCalendar" type="button" class="boton"><img src="img/date.png" width="16" height="16"> </button--></td></tr>
	<?php }?>
<?php }?>
                        </table>
                        <?php include ('./footerMantenimiento.php');?>
                    </div>
            </div>
        </form>
    </div>
</body>
</HTML>