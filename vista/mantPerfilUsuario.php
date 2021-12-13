<?php
require("../modelo/clsUsuario.php");
require("../modelo/clsPersona.php");
$id_clase = $_GET["id_clase"];

$id_cliente = $_SESSION['R_IdSucursal'];

$id_persona = $_SESSION['R_IdPersonaMaestro'];

$id_personal = $_SESSION['R_IdPersona'];

$id_usuario = $_SESSION['R_IdUsuario'];

$_GET['accion'] = "CAMBIARDATOSPERFIL";
//echo $id_clase;
try{
$objMantenimiento = new clsUsuario($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objPersonal = new clsPersona(23,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objPersonaMaestro = new clsPersonaMaestro(22,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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

$rstPersonal = $objPersonal->obtenerTabla();
if(is_string($rstPersonal)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstPersonal."</td>";
}else{
	$datoPersonal = $rstPersonal->fetchObject();
}

$rstPersonaMaestro = $objPersonaMaestro->obtenerTabla();
if(is_string($rstPersonaMaestro)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstPersonaMaestro."</td>";
}else{
	$datoPersonaMaestro = $rstPersonaMaestro->fetchObject();
}

$rst = $objMantenimiento->obtenerCamposMostrar("F");
$dataCampos = $rst->fetchAll();

$rstPersonal = $objPersonal->obtenerCamposMostrar("F");
$dataCamposPersonal = $rstPersonal->fetchAll();

$rstPersonaMaestro = $objPersonaMaestro->obtenerCamposMostrar("F");
$dataCamposPersonaMaestro = $rstPersonaMaestro->fetchAll();

if($_GET["accion"]=="CAMBIARDATOSPERFIL"){
//echo "consultarUsuario(1,1,1,1,$id_cliente,$id_personal,'', $id_usuario);";
	$rst = $objMantenimiento->consultarUsuario(1,1,'1',1,$id_cliente,$id_personal,'', $id_usuario);
	$dato = $rst->fetch();

//		$rstPersonal = $objPersonal->consultarPersona(1,1,'1',1,$id_persona,$id_cliente,'',$id_personal);
	$rstPersonal = $objPersonal->consultarPersona(1,1,'1',1,$id_cliente,$id_personal,$id_persona);
	$datoPersonal = $rstPersonal->fetch();
	
	$rstrstPersonaMaestro= $objPersonaMaestro->consultarPersonaMaestro(1,1,'1',1,$id_persona);
	$datoPersonaMaestro = $rstrstPersonaMaestro->fetch();
}

?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
function aceptar(){
	if(setValidar("frmMantUsuario")){
	document.getElementById("frmMantUsuario").submit();
	}
}
CargarCabeceraRuta([["MI PERFIL",'vista/mantPerfilUsuario','<?php echo $_SERVER["QUERY_STRING"];?>']],true);
</script>
</head>
<body>
    <div class="container">
        <form action="controlador/contUsuario.php" method="POST" enctype="multipart/form-data" id="frmMantUsuario" name="frmMantUsuario">
<input type="hidden" id="txtId" name = "txtId" value = "<?php echo $id_usuario;?>">
<input type="hidden" id="accion" name = "accion" value = "<?php echo $_GET['accion'];?>">
<input type="hidden" id="clase" name = "clase" value = "<?php echo $_GET['id_clase'];?>">
<input type="hidden" id="txtIdCliente" name = "txtIdCliente" value = "<?php echo $id_cliente;?>">
<input type="hidden" id="txtIdPersona" name = "txtIdPersona" value = "<?php echo $id_personal;?>">
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
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?></td></tr>
	<?php }?>
<?php }?>
<?php
reset($dataCamposPersonaMaestro);
foreach($dataCamposPersonaMaestro as $value){
?>
	<?php if($value["idcampo"]==2){?>
	<tr style="display:none"><td><?php echo $value["comentario"];?></td>
		<td><?php echo $datoPersonaMaestro[strtolower($value["descripcion"])];?></td></tr></tr>
	<?php }?>

	<?php if($value["idcampo"]==5){?>
	<tr><td>DNI</td>
		<td><?php echo $datoPersonaMaestro[strtolower($value["descripcion"])];?></td></tr>
	<?php }?>
<?php }?>	
<?php
reset($dataCamposPersonal);
foreach($dataCamposPersonal as $value){
?>
<?php if($value["idcampo"]==7){?>
	<tr><td><?php echo $value["comentario"];?></td>
		<td><input type="text" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php echo htmlentities(umill($datoPersonal[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="text" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php echo htmlentities(umill($datoPersonal[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==10){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="text" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php echo htmlentities(umill($datoPersonal[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==8){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="text" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php echo htmlentities(umill($datoPersonal[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
<?php }?>
<?php
reset($dataCampos);
foreach($dataCampos as $value){
?>
	<?php if($value["idcampo"]==8){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="text" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
<?php }?>
<?php
reset($dataCamposPersonal);
foreach($dataCamposPersonal as $value){
?>
	<?php if($value["idcampo"]==12){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><div style="position:absolute; top:100px; right:50px;">
        <!--img src='<?php if($datoPersonal[strtolower($value["descripcion"])]!=""){ $aleatorio = rand (1,1000000); echo 'img/empresas/'.htmlentities(umill($datoPersonal[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8")."?random=".$aleatorio; }else{ echo "img/foto.png";}?>' alt="Foto" width="120" height="172" alt="Foto" title="Foto"-->
        </div><input type="file" id="txtImagen" name="txtImagen"><br>
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