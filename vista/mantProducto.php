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
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
</head>
<body>
    <div class="container">
        <form id="frmMantProducto" action="" method="POST">
            <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['IdProducto'];?>">
            <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
            <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
            <div class="row Mesas">
                <div class="col s12 m6 l6">
                    <table>
            <?php
            reset($dataProductos);
            $estructura = array();
            foreach($dataProductos as $value){
            ?>
                    <?php if($value["idcampo"]==4){ 
                        $input = array();
                        $input["comentario"]=$value["comentario"];
                        ?>
                    <tr><td width="160"><?php echo $value["comentario"];?></td>
                    <td width="328"><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $objMantenimiento->generaCodigo();
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==5){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" style="text-transform:uppercase" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php //if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==6){?>
                    <tr id="trCategoria"><td><?php echo $value["comentario"];?></td>
                    <td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].") order by vDescripcion asc", $value["descripcion"], $dato[strtolower($value["descripcion"])],'',$objMantenimiento,'', 'Ninguna'); else echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].") order by vDescripcion asc",$value["descripcion"],0,'',$objMantenimiento,'', 'Ninguna');?></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==7){?>
                    <tr id="trMarca"><td><?php echo $value["comentario"];?></td>
                    <td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, '', 'Ninguna'); else echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');?></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==8){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <?php 
                            if($_GET["accion"]=="ACTUALIZAR"){
                            $rstListaunidad1 = $objMantenimiento->buscarxidproductoyidunidad($_GET["IdProducto"],$dato['idsucursal'],NULL);
                            $CantListaUnidad=$rstListaunidad1->rowCount();
                            $rstListaunidad = $objMantenimiento->buscarxidproductoyidunidad($_GET["IdProducto"],$dato['idsucursal'],$dato[strtolower($value["descripcion"])]);
                            $datosListaUnidad=$rstListaunidad->fetchObject();
                            }
                            ?>
                    <td><?php if($_GET["accion"]=="ACTUALIZAR") { if($CantListaUnidad==1){ echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, '');}else{echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'disabled',$objMantenimiento, '');} }else{ echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '');}?></td></tr>
                    <tr id="trPrecioCompra"><td>Precio Compra</td><td><input type="hidden" id="txtIdListaUnidad" name = "txtIdListaUnidad" value = "<?php if($_GET["accion"]=="ACTUALIZAR") echo $datosListaUnidad->idlistaunidad;
                    ?>"><input type="Text" id="txtPrecioCompra" name = "txtPrecioCompra" value = "<?php if($_GET["accion"]=="ACTUALIZAR") echo $datosListaUnidad->preciocompra;
                    ?>" size="10" maxlength="10" title="Debe indicar un precio de compra" onKeyPress="return validarsolonumerosdecimales3(event,this.value);"></td></tr>
                <tr id="trPrecioManoObra" style="display:none"><td>Precio Mano Obra</td><td><input type="Text" id="txtPrecioManoObra" name = "txtPrecioManoObra" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->preciomanoobra;
                    ?>" size="10" maxlength="10" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
                <tr id="trPrecioVenta"><td>Precio Venta</td><td><input type="Text" id="txtPrecioVenta" name = "txtPrecioVenta" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->precioventa;
                    ?>" size="10" maxlength="10" title="Debe indicar un precio de venta" onKeyPress="return validarsolonumerosdecimales3(event,this.value);"></td></tr>
                <tr id="trPrecioVentaLlevar"><td>Precio Venta Especial</td><td><input type="Text" id="txtPrecioVenta2" name = "txtPrecioVenta2" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->precioventa2;
                    ?>" size="10" maxlength="10" title="Debe indicar un precio de venta para llevar" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
                <tr id="trPrecioVentaLlevar" style="display:none;"><td>Precio Venta Sabado</td><td><input type="Text" id="txtPrecioVenta3" name = "txtPrecioVenta3" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->precioventa3;
                    ?>" size="10" maxlength="10" title="Debe indicar un precio de venta para llevar" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
                <tr id="trPrecioVentaLlevar" style="display:none;"><td>Precio Venta Domingo</td><td><input type="Text" id="txtPrecioVenta4" name = "txtPrecioVenta4" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo $datosListaUnidad->precioventa4;
                    ?>" size="10" maxlength="10" title="Debe indicar un precio de venta para llevar" onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
                    </table>
                </div>
                <div class="col s12 m6 l6">
                    <table>
                    <?php }?>
                <?php if($value["idcampo"]==9){?>
                    <tr id="trPeso"><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> onKeyPress="return validarsolonumerosdecimales(event,this.value);"></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==10){?>
                    <tr id="trMedidaPeso"><td><?php echo $value["comentario"];?></td>
                    <td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N' AND tipo='M'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, ''); else echo genera_cboGeneralSQL("Select * from Unidad Where Estado='N' AND tipo='M'",$value["descripcion"],0,'',$objMantenimiento, '');?></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==11){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $_SESSION["FechaProceso"];?>" size="10" maxlength="10" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?>><button id="btnCalendar" type="button" class="boton" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>><img src="img/date.png" width="16" height="16"> </button></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==12){?>
                    <tr><td width="160"><?php echo $value["comentario"];?></td>
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
                    <tr id="trMinimoVender"><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==16){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr></table></div>
                        <table  width="500">
                    <?php }?>
                <?php if($value["idcampo"]==17){?>
                    <tr><td width="160"><?php echo $value["comentario"];?></td>
                    <td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * from Ubicacion Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento, "asignaUbi(this.value)", 'Ninguna'); else echo genera_cboGeneralSQL("Select * from Ubicacion Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, "asignaUbi(this.value)", 'Ninguna');?></td></tr></table>
                    <div id="divUbi" style="display:none"></div>
                        <table  width="500">
                       <?php }?>

                    <?php if($value["idcampo"]==20){?>
                <tr id="trKardex"><td width="160"><?php echo $value["comentario"];?></td>
                    <td width="328">
                        <div class="col s12">
                            <input type="Checkbox" name="chkKardex" id="chkKardex" value="S" <?php if($_GET["accion"]=="ACTUALIZAR") {if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}}?> onClick="javascript:  cambiaSegunKardex(this.checked); if(document.getElementById('divKardex').style.display=='') {document.getElementById('divKardex').style.display='none'; }else{document.getElementById('divKardex').style.display='';}"> 
                            <label for="chkKardex">El producto controla Stock</label>
                        </div>
                    </td></tr></table>
                    <div id="divKardex" <?php if($dato[strtolower($value["descripcion"])]!="S"){ echo 'style="display:none"';}?>><table  width="500">
                    <?php }?>
                <?php if($value["idcampo"]==21){?>
                    <tr id="trCompuesto"><td><?php echo $value["comentario"];?></td>
                        <td width="328">
                            <div class="col s12">
                                <input type="Checkbox" name="chkCompuesto" id="chkCompuesto" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?> onClick="javascript: cambiaSegunCompuesto(this.checked);">
                                <label for="chkCompuesto">El Producto est&aacute; compuesto por ingredientes</label>
                            </div>
                        </td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==22){?>
                    <tr><td width="160"><?php echo $value["comentario"];?></td>
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
                <?php if($value["idcampo"]==29){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                        <td>
                            <div class="col s12">
                                <input type="Checkbox" name="chkCompartido" id="chkCompartido" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>>
                                <label for="chkCompartido">El Producto se compartir&aacute; con las dem&aacute;s sucursales</label>
                            </div>
                        </td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==30){?>
                    <tr><td><?php echo $value["comentario"];?></td><td><select name="cboTipo" id="cboTipo" onChange="javascript: cambiaSegunTipo(this.value);">
                <option value="P" <?php if($dato[strtolower($value["descripcion"])]=="P"){ echo "selected";}?>>Producto Final</option>
                <option value="I" <?php if($dato[strtolower($value["descripcion"])]=="I"){ echo "selected";}?>>Ingrediente</option></select></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==31){?>
                    <tr><td><?php echo $value["comentario"];?></td>
                    <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                    ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
                    <?php }?>
                <?php if($value["idcampo"]==32){?>
                    <tr ><td><?php echo $value["comentario"];?></td>
                    <td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select idimpresora, nombre as Impresora from impresora where idsucursal=".$_SESSION["R_IdSucursal"]." and estado='N'", $value["descripcion"], $dato["idimpresora"],'',$objMantenimiento,'', 'Ninguna'); else echo genera_cboGeneralSQL("Select idimpresora, nombre as Impresora from impresora where idsucursal=".$_SESSION["R_IdSucursal"]." and estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');?></td></tr>
                    <tr>
                        <td>Cortesia:</td>
                        <td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select idproducto, descripcion as Cortesia from producto where idsucursal=".$_SESSION["R_IdSucursal"]." and estado='N' and idcategoria=6 order by descripcion asc","Cortesia", $dato["idproductoref"],'',$objMantenimiento,'', 'Ninguna'); else echo genera_cboGeneralSQL("Select idproducto, descripcion as Cortesia from producto where idsucursal=".$_SESSION["R_IdSucursal"]." and estado='N' and idcategoria=6 order by descripcion asc","Cortesia",0,'',$objMantenimiento, '', 'Ninguno');?>
                        </td>
                    </tr>
                    <?php }?>
                <?php if($value["idcampo"]==14){?>
                    <tr><td><?php echo $value["comentario"];?></td><td><label><input type="Checkbox" name="chkComida" id="chkComida" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>> La categor&iacute;a se considera comida</label></td></tr>
                    <?php }?>
            <?php }?>
                        </table>
                        <?php include ('./footerMantenimiento.php');?>
                    </div>
            </div>
        </form>
    </div>
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
<?php if($value["idcampo"]==11){?>
cal.manageFields("btnCalendar", "txtFechaVencimiento", "%d/%m/%Y");
<?php }?>
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
function cambiaSegunTipo(tipo){
	if(tipo=='I'){
		document.getElementById('chkCompuesto').checked=false;
		document.getElementById('trCompuesto').style.display='none';
		//document.getElementById('trCategoria').style.display='none';
		document.getElementById('trPrecioVenta').style.display='none';
		document.getElementById('trPrecioVentaLlevar').style.display='none';
		document.getElementById('trMinimoVender').style.display='none';
		//document.getElementById('cboIdCategoria').value=1;//INGREDIENTE
		document.getElementById('txtPrecioVenta').title='';
		document.getElementById('txtPrecioVenta2').title='';
	}else{
		//document.getElementById('trCategoria').style.display='';
		document.getElementById('trPrecioVenta').style.display='';
		document.getElementById('trPrecioVentaLlevar').style.display='';
		document.getElementById('trMinimoVender').style.display='';
		document.getElementById('txtPrecioVenta').title='Debe indicar un precio de venta';
		document.getElementById('txtPrecioVenta2').title='Debe indicar un precio de venta para llevar';
		if(document.getElementById('chkKardex').checked==true){
			document.getElementById('chkCompuesto').checked=false;
			document.getElementById('trCompuesto').style.display='none';
		}else{
			document.getElementById('trCompuesto').style.display='';
		}
	}
	cambiaSegunCompuesto(document.getElementById('chkCompuesto').checked);
}
function cambiaSegunKardex(checked){
	if(checked==true){
		document.getElementById('chkCompuesto').checked=false;
		document.getElementById('trCompuesto').style.display='none';
	}else{
		if(document.getElementById('cboTipo').value=='I'){
			document.getElementById('chkCompuesto').checked=false;
			document.getElementById('trCompuesto').style.display='none';
		}else{
			document.getElementById('trCategoria').style.display='';
			document.getElementById('trCompuesto').style.display='';
			document.getElementById('trPrecioVenta').style.display='';
			document.getElementById('trPrecioVentaLlevar').style.display='';
			document.getElementById('trMinimoVender').style.display='';
			document.getElementById('txtPrecioVenta').title='Debe indicar un precio de venta';
			document.getElementById('txtPrecioVenta2').title='Debe indicar un precio de venta para llevar';
		}
	}
	cambiaSegunCompuesto(document.getElementById('chkCompuesto').checked);
}
function cambiaSegunCompuesto(checked){
	if(checked==true){
		document.getElementById('cboIdMarca').value=0;
		document.getElementById('txtPrecioCompra').value='';	
		document.getElementById('txtPrecioCompra').title='';
		document.getElementById('txtPrecioManoObra').title='Debe indicar un precio de mano de obra';
		document.getElementById('trMarca').style.display='none';
		document.getElementById('trPrecioCompra').style.display='none';
		document.getElementById('trPrecioManoObra').style.display='';
		document.getElementById('trKardex').style.display='none';
		document.getElementById('chkKardex').checked=false;
			if(document.getElementById('cboTipo').value=='I'){
				document.getElementById('chkCompuesto').checked=false;
				document.getElementById('trCompuesto').style.display='none';
			}else{
				document.getElementById('trCompuesto').style.display='';
			}
		document.getElementById('trPeso').style.display='none';
		document.getElementById('trMedidaPeso').style.display='none';
	}else{
		document.getElementById('txtPrecioManoObra').value='';
		document.getElementById('txtPrecioCompra').title='Debe indicar un precio de compra';
		document.getElementById('txtPrecioManoObra').title='';
		document.getElementById('trMarca').style.display='';
		document.getElementById('trPrecioCompra').style.display='';
		document.getElementById('trPrecioManoObra').style.display='none';
		document.getElementById('trKardex').style.display='';
		document.getElementById('trPeso').style.display='';
		document.getElementById('trMedidaPeso').style.display='';		
	}
}
<?php if($_GET['accion']=='ACTUALIZAR') {
	if($dato['idubicacion']!=0) {?>
	asignaUbi(<?php echo $dato['idubicacion'];?>);
	<?php }?>
	cambiaSegunTipo('<?php echo $dato['tipo'];?>');
	cambiaSegunKardex(<?php echo $dato['kardex']=='S'?true:false;?>);
	cambiaSegunCompuesto(<?php echo $dato['compuesto']=='S'?true:false;?>);
<?php
}?>
document.getElementById('txtDescripcion').focus();

<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR - <?php echo umill($dato["descripcion"]);?>",'vista/mantProducto','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantProducto','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</body>
</html>