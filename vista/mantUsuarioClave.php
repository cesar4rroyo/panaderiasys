<?php
require("../modelo/clsUsuario.php");
require("../modelo/clsPersona.php");
$id_clase = $_GET["id_clase"];

$id_cliente = $_SESSION['R_IdSucursal'];

$id_persona = $_SESSION['R_IdPersonaMaestro'];

$id_usuario = $_SESSION['R_IdUsuario'];

$id_personal = $_SESSION['R_IdPersona'];


$_GET['accion'] = "CAMBIARCLAVE";
//echo $id_clase;
try{
$objMantenimiento = new clsUsuario($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objPersonal = new clsPersona(23,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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

$rstPersonal = $objPersonal->obtenerCamposMostrar("F");
$dataCamposPersonal = $rstPersonal->fetchAll();

/*$rstPersonaMaestro = $objPersonaMaestro->obtenerCamposMostrar("F");
$dataCamposPersonaMaestro = $rstPersonaMaestro->fetchAll();*/

if($_GET["accion"]=="CAMBIARCLAVE"){
//echo "$rst = $objMantenimiento->consultarUsuario(1,1,1,1,1,0,0, $id_usuario);";
	$rst = $objMantenimiento->consultarUsuario(1,1,'1',1,$id_cliente,0,'', $id_usuario);
	$dato = $rst->fetch();
	
	$rstPersonal = $objPersonal->consultarPersona(1,1,'1',1,$id_cliente,$id_personal,$id_persona);
	$datoPersonal = $rstPersonal->fetch();
	
}

?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function aceptar(){
	if(document.getElementById("txtClave").value!=''){
		if(document.getElementById("txtNuevaClave").value!='' && document.getElementById("txtConfirmaClave").value!=''){
				if(document.getElementById("txtNuevaClave").value==document.getElementById("txtConfirmaClave").value){
					//document.getElementById("frmMantUsuario").submit();
					g_ajaxGrabar.setURL("controlador/contUsuario.php?ajax=true");
					g_ajaxGrabar.setRequestMethod("POST");
					g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
					g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
					g_ajaxGrabar.setParameter("txtId", "<?php echo $id_usuario;?>");
					g_ajaxGrabar.setParameter("txtIdCliente", "<?php echo $id_cliente;?>");
					g_ajaxGrabar.setParameter("txtIdPersonaMaestro", "<?php echo $id_persona;?>");
					g_ajaxGrabar.setParameter("txtIdUsuario", "<?php echo $id_usuario;?>");
					g_ajaxGrabar.setParameter("txtClave", document.getElementById("txtClave").value);
					g_ajaxGrabar.setParameter("txtNuevaClave", document.getElementById("txtNuevaClave").value);
					g_ajaxGrabar.response = function(text){
						loading(false, "loading");
						//alert(text);
						eval(text);
					};
					g_ajaxGrabar.request();
					loading(true, "loading", "frame", "line.gif",true);
				}else{
					alert('No coinciden las password');
				}
		}else{
			alert('Debe ingresar y confirmar el password nuevo');
		}
	}else{
		alert('Debe ingresar el password actual');
	}
}
CargarCabeceraRuta([["CAMBIO DE CLAVE",'vista/mantUsuarioClave','<?php echo $_SERVER["QUERY_STRING"];?>']],true);
</script>
</head>
<body>
    <div class="container">
        <form action="controlador/contUsuario.php" method="POST"id="frmMantUsuario" name="frmMantUsuario">
        <input type="hidden" id="txtId" name = "txtId" value = "<?php echo $id_usuario;?>">
        <input type="hidden" id="accion" name = "accion" value = "<?php echo $_GET['accion'];?>">
        <input type="hidden" id="clase" name = "clase" value = "<?php echo $_GET['id_clase'];?>">
        <input type="hidden" id="txtIdCliente" name = "txtIdCliente" value = "<?php echo $id_cliente;?>">
        <input type="hidden" id="txtIdPersonaMaestro" name = "txtIdPersonaMaestro" value = "<?php echo $id_persona;?>">
        <input type="hidden" id="txtIdUsuario" name = "txtIdUsuario" value = "<?php echo $id_usuario;?>">
        <div class="row Mesas">
                <div class="col s12 m12 l10 offset-l1">
                    <table>
        <?php
        reset($dataCampos);
        foreach($dataCampos as $value){
        ?>
                <?php if($value["idcampo"]==3){?>
                <tr><td>Apellidos y Nombres</td>
                <td><?php echo htmlentities(umill($datoPersonal['personamaestro']), ENT_QUOTES, "UTF-8");
                ?></td></tr>
                <tr><td><?php //echo $value["comentario"];?></td>
                <td><?php //echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?></td></tr>
                <?php }?>
            <?php if($value["idcampo"]==4){?>
                <tr><td><?php echo $value["comentario"];?> Actual</td>
                <td><input type="password" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?>"></td></tr>
                        <tr><td>Nueva <?php echo $value["comentario"];?></td>
                <td><input type="password" id="txtNueva<?php echo $value["descripcion"];?>" name = "txtNueva<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?>"></td></tr>
                        <tr><td>Confirma <?php echo $value["comentario"];?></td>
                <td><input type="password" id="txtConfirma<?php echo $value["descripcion"];?>" name = "txtConfirma<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?>"></td></tr>
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