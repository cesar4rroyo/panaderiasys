<?php
require("../modelo/clsEmpresa.php");
$id_clase = $_GET["id_clase"];
//echo $id_clase;
try{
$objMantenimiento = new clsEmpresa($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
	$rst = $objMantenimiento->consultarEmpresa(1,1,'1',1,$_GET["Id"],"%%");
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
	/*g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtNombreEmpresa", document.getElementById("txtNombreEmpresa").value);
	g_ajaxGrabar.setParameter("txtDireccion", document.getElementById("txtDireccion").value);
	g_ajaxGrabar.setParameter("txtRuc", document.getElementById("txtRuc").value);
	g_ajaxGrabar.setParameter("txtEmail", document.getElementById("txtEmail").value);
	g_ajaxGrabar.setParameter("txtTelefonoFijo", document.getElementById("txtTelefonoFijo").value);
	g_ajaxGrabar.setParameter("txtTelefonoMovil", document.getElementById("txtTelefonoMovil").value);
	g_ajaxGrabar.setParameter("txtFax", document.getElementById("txtFax").value);
	g_ajaxGrabar.setParameter("txtLogo", document.getElementById("txtLogo").value);*/
	getFormData("frmMantEmpresa");
}
function aceptar(){
	if(!valEmail(document.getElementById('txtEmail').value)){alert('La dirección de correo no es correcta');document.getElementById('txtEmail').focus();return false;}
	if(setValidar("frmMantEmpresa")){
		g_ajaxGrabar.setURL("controlador/contEmpresa.php?ajax=true");
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
<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantEmpresa','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantEmpresa','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container">
        <form id="frmMantEmpresa" action="" method="POST">
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
                    <td><input type="Text" id="txtNombreEmpresa" name = "txtNombreEmpresa" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==3){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txtDireccion" name = "txtDireccion" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==4){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txtRuc" name = "txtRuc" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==5){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txtEmail" name = "txtEmail" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==6){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txtTelefonoFijo" name = "txtTelefonoFijo" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==7){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txtTelefonoMovil" name = "txtTelefonoMovil" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==8){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txtFax" name = "txtFax" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==9){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txtLogo" name = "txtLogo" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
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