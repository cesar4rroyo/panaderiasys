<?php
require("../modelo/clsBanco.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['id_cliente'];
//echo $id_clase;
try{
$objMantenimiento = new clsBanco($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstBanco = $objMantenimiento->obtenerTabla();
if(is_string($rstBanco)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstBanco."</td>";
}else{
	$datoBanco = $rstBanco->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataPerfils = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarBanco(1,1,'2',1,$_GET["Id"],$id_cliente,"");
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetch();
	}
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmMantBanco");	
}
function aceptar(){
	if(setValidar("frmMantBanco")){
		g_ajaxGrabar.setURL("controlador/contBanco.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			if(text==1){
                            alert('La descripci�n del banco no esta disponible, intente con otra descripci�n.');
                            
			}else{
			buscar();
			alert(text);
			}
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}
<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR - <?php echo umill($dato["descripcion"]);?>",'vista/mantBanco','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantBanco','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container Mesas">
        <form id="frmMantBanco" action="" method="POST">
        <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
        <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
        <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
        <div class="row">
        <?php
        reset($dataPerfils);
        foreach($dataPerfils as $value){
        ?>
                <?php if($value["idcampo"]==2){?>
                <div class="input-field col s12">
                    <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" 
                               value = "<?php if($_GET["accion"]=="ACTUALIZAR") echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");?>" 
                                   <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> style="text-transform:uppercase">
                    <label for="txt<?php echo $value["descripcion"];?>" class="<?php if($_GET["accion"]=="ACTUALIZAR"){ echo "active";}?>"><?php echo $value["comentario"];?></label>
                </div>
                <?php }?>
            <?php /*if($value["idcampo"]==3){?>
                <tr><td><?php echo $value["comentario"];?></td>
                <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> style="text-transform:uppercase"></td></tr>
                <?php }*/?>
        <?php }?>
        </div>
        <?php include ('./footerMantenimiento.php');?>
        </form>
    </div>
</body>
</html>