<?php
require("../modelo/clsMarca.php");
require("fun.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['id_cliente'];
//echo $id_clase;
try{
$objMantenimiento = new clsMarca($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstMarca = $objMantenimiento->obtenerTabla();
if(is_string($rstMarca)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstMarca."</td>";
}else{
	$datoMarca = $rstMarca->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataPerfils = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarMarca(1,1,'2',1,$_GET["Id"],$id_cliente,"");
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
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
	getFormData("frmMantMarca");	
}
function aceptar(){
	if(setValidar("frmMantMarca")){
		g_ajaxGrabar.setURL("controlador/contMarca.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			if(text==1){
				alert('La descripción de la marca no esta disponible, intente con otra descripción.');						
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
CargarCabeceraRuta([["ACTUALIZAR - <?php echo umill($dato["descripcion"]);?>",'vista/mantMarca','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantMarca','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container Mesas">
        <form id="frmMantMarca" action="" method="POST">
            <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
            <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
            <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
            <table width="200" border="0">
            <?php
            reset($dataPerfils);
            foreach($dataPerfils as $value){
            ?>
            <?php if($value["idcampo"]==2){?>
                <div class="row">
                    <div class="col s12 m6 l6">
                        <div class="input-field inline">
                            <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
                                echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                                ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> style="text-transform:uppercase">
                            <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                        </div>
                    </div>
            <?php }?>
        <?php if($value["idcampo"]==3){?>
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
                            echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
                            ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> style="text-transform:uppercase">
                        <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                    </div>
                </div>
            </div>
            <?php }?>
    <?php }?>
    <?php include ('./footerMantenimiento.php');?>
        </form>
    </div>
</body>
</HTML>