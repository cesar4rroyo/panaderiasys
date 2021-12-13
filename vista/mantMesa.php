<?php
require("../modelo/clsMesa.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['id_cliente'];
//echo $id_clase;
try{
$objMantenimiento = new clsMesa($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstMesa = $objMantenimiento->obtenerTabla();
if(is_string($rstMesa)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstMesa."</td>";
}else{
	$datoMesa = $rstMesa->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataMesas = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarMesa(1,1,'2',1,$_GET["Id"],$id_cliente,"");
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
	/*g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtIdSucursal", document.getElementById("txtIdSucursal").value);
	g_ajaxGrabar.setParameter("txtNumero", document.getElementById("txtNumero").value);
	g_ajaxGrabar.setParameter("cboIdSalon",document.getElementById("cboIdSalon").value);
	g_ajaxGrabar.setParameter("txtNroPersonas",document.getElementById("txtNroPersonas").value);
	g_ajaxGrabar.setParameter("txtComentario",document.getElementById("txtComentario").value);
	g_ajaxGrabar.setParameter("txtImagen",document.getElementById("txtImagen").value);*/
	getFormData("frmMantMesa");
}
function aceptar(){
	if(setValidar("frmMantMesa")){
		g_ajaxGrabar.setURL("controlador/contMesa.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			if(text==1){
				alert('El número de mesa no esta disponible, intente con otro número.');						
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
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantMesa','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantMesa','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container">
        <form id="frmMantMesa" action="" method="POST">
        <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
        <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
        <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
        <div class="row Mesas">
                <div class="col s12 m12 l10 offset-l1">
                    <table>
        <?php
        reset($dataMesas);
        foreach($dataMesas as $value){
        ?>
                <?php if($value["idcampo"]==2){?>
                <tr><td><?php echo $value["comentario"];?></td>
                <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> style="text-transform:uppercase"></td></tr>
                <?php }?>

            <?php if($value["idcampo"]==3){?>
                <tr><td><?php echo $value["comentario"];?></td>
                <td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Salon Where Estado='N' and idsucursal=".$id_cliente,$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("Select * from Salon Where Estado='N' and idsucursal=".$id_cliente,$value["descripcion"],0,'',$objMantenimiento);?></td></tr>
                <?php }?>

            <?php if($value["idcampo"]==4){?>
                <tr><td><?php echo $value["comentario"];?></td>
                <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>  maxlength="2" onKeyPress="return validarsolonumeros(event)"></td></tr>
                <?php }?>

                <?php if($value["idcampo"]==5){?>
                <tr><td><?php echo $value["comentario"];?></td>
                <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                <?php }?>

                <?php if($value["idcampo"]==6){?>
                <tr><td><?php echo $value["comentario"];?></td>
                <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
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