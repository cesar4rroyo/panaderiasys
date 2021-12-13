<?php
require("../modelo/clsOpcionMenu.php");
$id_clase = $_GET["id_clase"];
//echo $id_clase;
try{
$objMantenimiento = new clsOpcionMenu($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstTabla = $objMantenimiento->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}

$rst = $objMantenimiento->obtenerCamposMostrar("F");
$dataCampos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	if(is_string($rst)){
		echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
	}else{
		$rst = $objMantenimiento->consultarOpcionMenu(1,1,'1',1,$_GET["Id"],"%%");
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
/*	g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("cboIdModulo", document.getElementById("cboIdModulo").value);
	g_ajaxGrabar.setParameter("cboIdMenuPrincipal", document.getElementById("cboIdMenuPrincipal").value);
	g_ajaxGrabar.setParameter("cboIdTabla", document.getElementById("cboIdTabla").value);
	g_ajaxGrabar.setParameter("txtAccion", document.getElementById("txtAccion").value);
	g_ajaxGrabar.setParameter("txtDiccionario", document.getElementById("txtDiccionario").value);*/
	getFormData("frmMantOpcionMenu");
}
function aceptar(){
	if(setValidar("frmMantOpcionMenu")){
		g_ajaxGrabar.setURL("controlador/contOpcionMenu.php?ajax=true");
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
CargarCabeceraRuta([["ACTUALIZAR",'vista/mantOpcionMenu','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantOpcionMenu','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
    <?php require("fun.php"); ?>
    <div class="container">
        <form id="frmMantOpcionMenu" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<div class="row Mesas">
                <div class="col s12 m12 l10 offset-l1">
                    <table>
<?php
reset($dataCampos);
foreach($dataCampos as $value){
?>  
<?php if($value["idcampo"]==2){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * From Modulo Where Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("Select * From Modulo Where Estado='N'",$value["descripcion"],0,'',$objMantenimiento);?></td></tr>
	<?php }?>
    
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtDescripcion" name = "txtDescripcion" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
    
    <?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("Select * From MenuPrincipal Where Estado='N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("Select * From MenuPrincipal Where Estado='N'",$value["descripcion"],0,'',$objMantenimiento);?></td></tr>
	<?php }?>
	
	<?php if($value["idcampo"]==7){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("SELECT IdTabla, Descripcion FROM Tabla WHERE Estado = 'N'",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento); else echo genera_cboGeneralSQL("SELECT idtabla, descripcion FROM Tabla WHERE Estado = 'N'",$value["descripcion"],0,'',$objMantenimiento);?></td></tr>
	<?php }?>
	
	<?php if($value["idcampo"]==8){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtAccion" name = "txtAccion" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==9){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txtDiccionario" name = "txtDiccionario" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==12){?>
	<tr><td><?php echo $value["comentario"];?></td>
            <td>
                <p>
                    <input type="Checkbox" name="chkWAP" id="chkWAP" value="S" <?php if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}?>>
                    <label for="chkWAP"><?php echo $value["comentario"];?></label>
                </p>
            </td>
    	</tr>
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