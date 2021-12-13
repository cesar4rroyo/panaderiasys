<?php
require("../modelo/clsCaja.php");
//require("fun.php");
require("../modelo/clsSalon.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
$id_cliente=$_GET['id_cliente'];
//echo $id_clase;
try{
$objMantenimiento = new clsCaja($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstCaja = $objMantenimiento->obtenerTabla();
//$funCboSalon = $objSalon->buscarSalon(0);
if(is_string($rstCaja)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstCaja."</td>";
}else{
	$datoCaja = $rstCaja->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataCajas = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarCaja(1,1,'2',1,$_GET["Id"],$id_cliente,"");
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
	g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtIdSucursal", document.getElementById("txtIdSucursal").value);
	/*g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
	getFormData("frmMantCaja");
	
}
function aceptar(){
	if(setValidar("frmMantCaja")){
		g_ajaxGrabar.setURL("controlador/contCaja.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			if(text==1){
				alert('El número de Caja no esta disponible, intente con otro número.');						
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
CargarCabeceraRuta([["ACTUALIZAR - <?php echo umill($dato["numero"]);?>",'vista/mantCaja','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantCaja','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <div class="container Mesas">
        <form id="frmMantCaja" action="" method="POST">
            <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
            <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
            <input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
            <?php
            require("fun.php");
            reset($dataCajas);
            foreach($dataCajas as $value){
            ?>
            <?php if($value["idcampo"]==2){?>
            <div class="row">
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <input type="text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR") echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");?>">
                        <label for="txt<?php echo $value["descripcion"];?>" class="active"><?php echo $value["comentario"];?></label>
                    </div>
                </div>
            <!--tr><td><?php echo $value["comentario"];?></td>
            <td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
    echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
            ?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>  style="text-transform:uppercase"></td></tr-->
            <?php }?>
    <?php  if($value["idcampo"]==3){?>
                <div class="col s12 m6 l6">
                    <div class="input-field inline">
                        <?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objSalon,'','','seleccione un sal&oacute;n'); else echo genera_cboGeneralFun("buscarSalon(0)",$value["descripcion"],0,'',$objSalon,'','','seleccione un sal&oacute;n');?>
                        <label><?php echo $value["comentario"];?></label>
                    </div>
                </div>
            </div>
	<!--tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objSalon,'','','seleccione un sal&oacute;n'); else echo genera_cboGeneralFun("buscarSalon(0)",$value["descripcion"],0,'',$objSalon,'','','seleccione un sal&oacute;n');?></td></tr-->
	<?php }?>
<?php }?>
    <?php include ('./footerMantenimiento.php');?>
        </form>
    </div>
</body>
</html>