<?php
require("../modelo/clsUsuario.php");
require("fun.php");
$id_clase = $_GET["id_clase"];

$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
$id_persona = $_GET["id_persona"];
if(!$id_persona){
	$id_persona = 0;
}
$id_usuario = $_GET["id_usuario"];
if(!$id_usuario){
	$id_usuario = 0;
}
//echo $id_clase;
try{
$objMantenimiento = new clsUsuario($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstUsuario = $objMantenimiento->obtenerTabla();
if(is_string($rstUsuario)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstUsuario."</td>";
}else{
	$datoUsuario = $rstUsuario->fetchObject();
}

$rst = $objMantenimiento->obtenerCamposMostrar("F");
$dataCampos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarUsuario(1,1,'1',1,$id_cliente,0,'', $id_usuario);
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
	g_ajaxGrabar.setParameter("txtIdSucursal", <?php echo $id_cliente;?>);
	g_ajaxGrabar.setParameter("txtNombreUsuario", document.getElementById("txtNombreUsuario").value);
	g_ajaxGrabar.setParameter("txtPassword", document.getElementById("txtPassword").value);	
	g_ajaxGrabar.setParameter("txtIdPersona", <?php echo $id_persona;?>);	
	g_ajaxGrabar.setParameter("txtIdUsuario", <?php echo $id_usuario;?>);	
	g_ajaxGrabar.setParameter("cboIdPerfil", document.getElementById("cboIdPerfil").value);	
	g_ajaxGrabar.setParameter("txtNroFilaMostrar", document.getElementById("txtNroFilaMostrar").value);	
	if(document.getElementById('chkOpcionMenudefecto').checked){
		g_ajaxGrabar.setParameter("txtOpcionMenuDefecto", document.getElementById("cboOpcionMenu").value);	
	}else{
		g_ajaxGrabar.setParameter("txtOpcionMenuDefecto", 0);	
	}
}
function aceptar(){
	if(setValidar("frmMantUsuario")){
		g_ajaxGrabar.setURL("controlador/contUsuario.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			//alert(text);						
			if(text==1){
				alert('El nombre de usuario no esta disponible, intentelo con otro nombre.');						
			}else{
				buscar();
				document.getElementById('cmdNuevo').style.display='none';
				alert(text);						
			}
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}
				
cont=0;
function genera_cboModulo(idperfil,seleccionado){
		var recipiente = document.getElementById('divcboModulo');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxOpcionMenu.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboModulo");
		g_ajaxPagina.setParameter("idperfil", idperfil);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("change", 'genera_cboMenuPrincipal(cboIdPerfil.value,this.value,0)');
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
                        $("select").material_select();
			if(cont==0){
				<?php if($_GET['accion']=='ACTUALIZAR') {?>
					<?php if($dato['opcionmenudefecto']!=0){?>
					genera_cboMenuPrincipal(<?php echo $dato['idperfil'].",".$dato['idmodulo'].",".$dato['idmenuprincipal'];?>);
					<?php } ?>
				<?php }else{?>
				genera_cboModulo2();
				<?php } ?>
			}else{
				if(cont==1)	genera_cboModulo2();
			}
		};
		g_ajaxPagina.request();
}
function genera_cboModulo2(){
	vValorPerfil=document.getElementById('cboIdPerfil').value;
	vValorModulo=document.getElementById('cboIdModulo').value;
	genera_cboMenuPrincipal(vValorPerfil,vValorModulo,0)
}
function genera_cboMenuPrincipal2(){
	vValorPerfil=document.getElementById('cboIdPerfil').value;
	vValorModulo=document.getElementById('cboIdModulo').value;
	vValorMenuPricipal=document.getElementById('cboIdMenuPrincipal').value;
	genera_cboOpcionMenu(vValorPerfil,vValorModulo,vValorMenuPricipal)
}
function genera_cboMenuPrincipal(idperfil,idmodulo,seleccionado){
		var recipiente = document.getElementById('divcboMenuPrincipal');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxOpcionMenu.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboMenuPrincipal");
		g_ajaxPagina.setParameter("idperfil", idperfil);
		g_ajaxPagina.setParameter("idmodulo", idmodulo);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("change", 'genera_cboOpcionMenu(cboIdPerfil.value,cboIdModulo.value,this.value)');
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
                        $("select").material_select();
			if(cont==0){
				<?php if($_GET['accion']=='ACTUALIZAR'){?>
					<?php if($dato['opcionmenudefecto']!=0){?>
					genera_cboOpcionMenu(<?php echo $dato['idperfil'].",".$dato['idmodulo'].",".$dato['idmenuprincipal'].",".$dato['opcionmenudefecto'];?>);		
					<?php } ?>	
				<?php }else{?>
				genera_cboMenuPrincipal2();
				<?php } ?>
			}else{
				if(cont==1)	genera_cboMenuPrincipal2();
			}

		};
		g_ajaxPagina.request();
}

function genera_cboOpcionMenu(idperfil,idmodulo,idmenuprincipal,seleccionado){
		var recipiente = document.getElementById('divcboOpcionMenu');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxOpcionMenu.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboOpcionMenu");
		g_ajaxPagina.setParameter("idperfil", idperfil);
		g_ajaxPagina.setParameter("idmodulo", idmodulo);
		g_ajaxPagina.setParameter("idmenuprincipal", idmenuprincipal);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
                        $("select").material_select();
			cont=1;		
		};
		g_ajaxPagina.request();
}

function muestraDivOpcionMenudefecto(valor){
	//alert(valor);
	if(valor){
		document.getElementById('DivOpcionMenuDefecto').style.display="";
	}else{
		document.getElementById('DivOpcionMenuDefecto').style.display="none";
	}
}
<?php if($_GET['accion']=='ACTUALIZAR'){?>
	<?php if($dato['opcionmenudefecto']!=0){?>
	genera_cboModulo(<?php echo $dato['idperfil'].",".$dato['idmodulo'];?>);
	muestraDivOpcionMenudefecto(true);
	<?php }?>
<?php }else{?>
	genera_cboModulo(document.getElementById('cboIdPerfil').value,0);
<?php }?>
<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantUsuario','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantUsuario','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container">
        <form id="frmMantUsuario" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['id_usuario'];?>">
<div class="row Mesas">
                <div class="col s12 m12 l10 offset-l1">
                    <table>
<?php
reset($dataCampos);
foreach($dataCampos as $value){
?>
	<?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtNombreUsuario" name = "txtNombreUsuario" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> <?php if($_GET['accion']=='ACTUALIZAR'){ echo "disabled";}?>></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="password" id="txtPassword" name = "txtPassword" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==6){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Perfil Where idsucursal=".$id_cliente." and Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,'genera_cboModulo(this.value,0)'); else echo genera_cboGeneralSQL("Select * from Perfil Where idsucursal=".$id_cliente." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento,'genera_cboModulo(this.value,0)');?></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==8){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> maxlength="3" onKeyPress="return validarsolonumeros(event)"></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==10){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td>
            <p>
                <input name="chkOpcionMenudefecto" id="chkOpcionMenudefecto" type="checkbox" value="S" onClick="muestraDivOpcionMenudefecto(this.checked)" <?php if($_GET["accion"]=="ACTUALIZAR"){ if($dato['opcionmenudefecto']!=0){ echo 'checked';}}?>>
                <label for="chkOpcionMenudefecto"></label>
            </p>
        <div id="DivOpcionMenuDefecto" style="display:none">
        Modulo:<div id="divcboModulo"><select id="cboIdModulo" name="cboIdModulo"><option value="0">Seleccione un Perfil</option></select></div>
        Men&uacute; Principal:<div id="divcboMenuPrincipal"><select id="cboIdMenuPrincipal" name="cboIdMenuPrincipal"><option value="0">Seleccione un Modulo</option></select></div>
        Opci&oacute;n Men&uacute;: <div id="divcboOpcionMenu"><select id="cboOpcionMenu" name="cboOpcionMenu"><option value="0">Seleccione un men&uacute; principal</option></select></div>
        </div>
	</td></tr>
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