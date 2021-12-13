<?php
require('../modelo/clsCategoria.php');
$id_clase = $_GET["id_clase"];
$nro_reg = 0;
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
	$nro_hoja = 1;
}
$order = $_GET["order"];
if(!$order){
	$order="1";
}
$by = $_GET["by"];
if(!$by){
	$by="1";
}
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = $_SESSION['R_IdEmpresa'];
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;

function actualizar(id, idsucursal){
	setRun('vista/mantDetalleCategoria','&accion=ACTUALIZAR&clase=Categoria&id_clase=<?php echo $id_clase;?>&Id=' + id +'&id_cliente='+ idsucursal +'&idsucursal=<?php echo $_GET['IdSucursal'];?>&idcategoria=<?=$_GET["idcategoria"]?>','cargamant', 'cargamant', 'imgloading03');
}

function eliminar(id, idsucursal){
	if(!confirm('Est� seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contCategoria.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINARDETALLE");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("txtIdSucursal", idsucursal);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			setRun('vista/listDetalleCategoria','&clase=Categoria&id_clase=<?php echo $id_clase;?>&idcategoria=' + <?=$_GET["idcategoria"]?> + '&IdSucursal=' + <?=$_GET["IdSucursal"]?> + '&id_cliente='+<?php echo $id_cliente;?>,'frame','carga','imgloading');
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}
</script>
</head>
<body>
<?php
$objFiltro = new clsCategoria($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['NombreUsuario'],$_SESSION['Clave']);
?>
<!--BOTONERA INICIO-->
    <div class="Botones" id="opciones">
        <div class="row">
            <div class="col s12 m12 l12 center">
                <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                        type="button" data-position="bottom" data-delay="50" 
                        data-tooltip="Nuevo" 
                        onClick="javascript:setRun('vista/mantDetalleCategoria', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>&id_cliente=<?php echo $_GET["id_cliente"];?>&idsucursal=<?=$_GET["IdSucursal"]?>&idcategoria=<?php echo $_GET['idcategoria'];?>', 'cargamant','cargamant', 'img04');"><i class="material-icons right">note_add</i>Nuevo</button>
            </div>
        </div>
    </div>
    <!--BOTONERA FIN-->

<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row">
        <div id="divdiagramaMesa" class="col s12">
            <div class="row">
                <div class="col s12">
                    <div id="cargagrilla"></div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div id="grilla">
                        <div class="row">
                            <table class="striped bordered highlight">
                                <thead>
                                    <tr>
                                        <th class="center">Descripcion</th>
                                        <th class="center">Abreviatura</th>
                                        <th class="center">Operaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rs=$objFiltro->obtenerDataSQL("select * from detallecategoria where estado='N' and idsucursal=".$_GET["IdSucursal"]." and idcategoria=".$_GET["idcategoria"]);
                                    while($dat=$rs->fetchObject()){?>
                                    <tr class="hoverable">
                                        <td class="center"><?php echo $dat->descripcion;?></td>
                                        <td class="center"><?php echo $dat->abreviatura;?></td>
                                        <td class="center">
                                            <div class="col s12">
                                                <button type="button" style="margin-left: 10px;" 
                                                        class="btn-floating btn-large tooltipped light-green accent-1" 
                                                        data-position="bottom" data-delay="50" data-tooltip="Permite modificar registro" 
                                                        onclick="javascript:actualizar(<?php echo $dat->iddetallecategoria;?>,<?php echo $dat->idsucursal;?>);">
                                                    <i class="material-icons light-green-text text-darken-4">edit</i>
                                                </button>
                                                <button type="button" style="margin-left: 10px;" 
                                                        class="btn-floating btn-large tooltipped red accent-1" 
                                                        data-position="bottom" data-delay="50" data-tooltip="Permite eliminar registro" 
                                                        onclick='javascript:eliminar(<?php echo $dat->iddetallecategoria;?>,<?php echo $dat->idsucursal;?>);'>
                                                    <i class="material-icons red-text text-darken-4">clear</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /*
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
    <td><a href="#" onClick="javascript:setRun('vista/listCategoria','&id_clase=11','frame', 'frame', 'img05')">Categoria</a></td><td>></td>
    <?php
	$rstProducto = $objFiltro->obtenerDataSQL("select Descripcion from categoria where idcategoria = ".$_GET["idcategoria"]. " and idsucursal=".$_GET["IdSucursal"]);
	if(is_string($rstProducto)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstProducto."</td>";
	}else{
		$datoProducto = $rstProducto->fetchObject();
	}
	?>
	<td><?php echo $datoProducto->descripcion; ?></td>
    <td>></td>
	<td><?php echo $datoListaUnidad ->descripcion; ?></td>
</tr></table>
</div>
<?php
//echo "Fin de archivo".date("d-m-Y H:i:s:u")."<br>";*/
?>
</body>
</html>