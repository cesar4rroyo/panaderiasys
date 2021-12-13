<?php
require("../modelo/clsCategoria.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['id_cliente'];
//echo $id_clase;
try{
$objMantenimiento = new clsCategoria($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstCategoria = $objMantenimiento->obtenerTabla();
if(is_string($rstCategoria)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstCategoria."</td>";
}else{
	$datoCategoria = $rstCategoria->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataCategorias = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarCategoria(1,1,'2',1,$_GET["Id"],$id_cliente,"");
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
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura",document.getElementById("txtAbreviatura").value);
	g_ajaxGrabar.setParameter("cboIdCategoriaRef",document.getElementById("cboIdCategoriaRef").value);
	g_ajaxGrabar.setParameter("txtImagen",document.getElementById("txtImagen").value);*/
	getFormData("frmMantCategoria");
}
function aceptar(){
	if(setValidar("frmMantCategoria")){
		g_ajaxGrabar.setURL("controlador/contCategoria.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			if(text==1){
				alert('La descripci�n de la categor�a no esta disponible, intente con otra descripci�n.');						
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
CargarCabeceraRuta([["ACTUALIZAR - <?php echo umill($dato["descripcion"]);?>",'vista/mantCategoria','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantCategoria','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container Mesas">
        <form id="frmMantCategoria" action="" method="POST">
        <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
        <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
        <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
        <?php
        reset($dataCategorias);
        foreach($dataCategorias as $value){
        ?>
	<?php if($value["idcampo"]==2){?>
        <div class="row">
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
                            echo htmlentities(umill(trim($dato[strtolower($value["descripcion"])])), ENT_QUOTES, "UTF-8");
                            ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> style="text-transform:uppercase">
                        <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                    </div>
                </div>
	<?php }?>
        <?php if($value["idcampo"]==3){?>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
                        echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                        ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?> style="text-transform:uppercase">
                    <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                </div>
            </div>
        </div>
	<?php }?>
	<?php if($value["idcampo"]==4){?>
            <div class="row">
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$id_cliente.")", $value["descripcion"], $dato[strtolower($value["descripcion"])],'',$objMantenimiento,'', 'Ninguna'); else echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$id_cliente.")",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');?>
                        <label><?php echo $value["comentario"];?></label>
                    </div>
                </div>
	<?php }?>
        <?php if($value["idcampo"]==7){?>
	<tr><td><?php echo $value["idcampo"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>
	<?php }?>
<!--Alex-->
        <?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["idcampo"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>></td></tr>
	<?php }?>
        <?php if($value["idcampo"]==10){?>
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
                            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                            ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=50){echo "size=".$value["longitud"]*1;}else{echo "size=50";}}?>>
                        <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                    </div>
                </div>
            </div>
	<?php }?>
    <?php if($value["idcampo"]==11){?>
            <div class="row" hidden="">
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select idimpresora, nombre as Impresora from impresora where idsucursal=".$id_cliente." and estado='N'", $value["descripcion"], $dato[strtolower($value["descripcion"])],'',$objMantenimiento,'', 'Ninguna'); else echo genera_cboGeneralSQL("Select idimpresora, nombre as Impresora from impresora where idsucursal=".$id_cliente." and estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');?>
                        <label><?php echo $value["comentario"];?></label>
                    </div>
                </div>
            </div>
	<?php }?>
    <?php if($value["idcampo"]==14){?>
	<tr><td><?php echo $value["idcampo"];?></td><td><label><input type="Checkbox" name="chkComida" id="chkComida" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>> La categor&iacute;a se considera comida</label></td></tr>
	<?php }?>
<?php }?>
    <?php include ('./footerMantenimiento.php');?>
        </form>
    </div>
</body>
</HTML>