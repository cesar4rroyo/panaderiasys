<?php
require("../modelo/clsUnidad.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsUnidad($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstUnidad = $objMantenimiento->obtenerTabla();
if(is_string($rstUnidad)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstUnidad."</td>";
}else{
	$datoUnidad = $rstUnidad->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataUnidads = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarUnidad(1,1,'2',1,$_GET["Id"],"");
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
	g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura",document.getElementById("txtAbreviatura").value);
	if(document.getElementById("optTipoM").checked){
		g_ajaxGrabar.setParameter("optTipo", "M");
	}
	if(document.getElementById("optTipoL").checked){
		g_ajaxGrabar.setParameter("optTipo", "L");
	}
	if(document.getElementById("optTipoA").checked){
		g_ajaxGrabar.setParameter("optTipo", "A");
	}
	if(document.getElementById("optTipoO").checked){
		g_ajaxGrabar.setParameter("optTipo", "O");
	}
}
function aceptar(){
	if(setValidar("frmMantUnidad")){
		g_ajaxGrabar.setURL("controlador/contUnidad.php?ajax=true");
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
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantUnidad','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantUnidad','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container">
        <form id="frmMantUnidad" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<div class="row Mesas">
                <div class="col s12 m12 l10 offset-l1">
                    <table>
<?php
reset($dataUnidads);
foreach($dataUnidads as $value){
?>
	<?php if($value["idcampo"]==2){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	
    <?php if($value["idcampo"]==4){?>
	<tr><td>
	<?php echo $value["comentario"];?></td>
    	<td>
            <p>
                <input type="radio" id="optTipoM" name = "optTipo" value = "M" <?php if($dato[strtolower($value["descripcion"])]=="M" || empty($dato[strtolower($value["descripcion"])])){ echo "checked=checked";}?>>
                <label for="optTipoM">Masa</label>
            </p>
            <p>
                <input type="radio" id="optTipoL" name = "optTipo" value = "L" <?php if($dato[strtolower($value["descripcion"])]=="L"){ echo "checked=checked";}?>>
                <label for="optTipoL">Longitud</label>
            </p>
            <p>
                <input type="radio" id="optTipoA" name = "optTipo" value = "A" <?php if($dato[strtolower($value["descripcion"])]=="A"){ echo "checked=checked";}?>>
                <label for="optTipoA">Área</label>
            </p>
            <p>
                <input type="radio" id="optTipoO" name = "optTipo" value = "O" <?php if($dato[strtolower($value["descripcion"])]=="O"){ echo "checked=checked";}?>>
                <label for="optTipoO">Otro</label>
            </p>
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