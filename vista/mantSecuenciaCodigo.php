<?php
require("../modelo/clsSecuenciaCodigo.php");
$id_clase = $_GET["id_clase"];
$id_cliente = $_GET["id_cliente"];
$id_empresa = $_GET["id_empresa"];
//echo $id_clase;
try{
$objMantenimiento = new clsSecuenciaCodigo($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstSecuenciaCodigo = $objMantenimiento->obtenerTabla();
if(is_string($rstSecuenciaCodigo)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstSecuenciaCodigo."</td>";
}else{
	$datoSecuenciaCodigo = $rstSecuenciaCodigo->fetchObject();
}

$rst = $objMantenimiento->obtenerCamposMostrar("F");
$dataCampos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarSecuenciaCodigo(1,1,'1',1,$_GET["Id"],$id_cliente,"%%");
	echo '$rst = $objMantenimiento->consultarSecuenciaCodigo(1,1,'.$_GET["Id"].','.$id_cliente.',"%%");';
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
	alert("desp");
	g_ajaxGrabar.setParameter("cboIdTabla", document.getElementById("cboIdTabla").value);
	
	g_ajaxGrabar.setParameter("txtIdSucursal", document.getElementById("txtIdSucursal").value);
	
	g_ajaxGrabar.setParameter("txtCodigo", document.getElementById("txtCodigo").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtDescripcionMant", document.getElementById("txtDescripcionMant").value);	
}
function aceptar(){
	//if(setValidar()){
		g_ajaxGrabar.setURL("controlador/contSecuenciaCodigo.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		
		setParametros();
		
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	//}
}
</script>
</head>
<body>
<form id="frmMantSecuenciaCodigo" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdSucursal" name = "txtIdSucursal" value = "<?php echo $id_cliente;?>">
<table width="200" border="1">
<?php
reset($dataCampos);
foreach($dataCampos as $value){
?>
	<?php if($value["idcampo"]==1){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><select id="cbo<?php echo $value["descripcion"];?>" name="cbo<?php echo $value["descripcion"];?>" <?php if($_GET["accion"]=="ACTUALIZAR"){echo "disabled";}?>>
		<?php
		$fil="";
		if($_GET["accion"]=="ACTUALIZAR"){
			$fil=" (NOT IdTabla in (SELECT IdTabla FROM SecuenciaCodigo WHERE Estado = 'N' AND IdSucursal = ".$id_cliente.") OR IdTabla = ".$dato[strtolower($value["descripcion"])]." ) AND ";
		}else{
			$fil=" NOT IdTabla in (SELECT IdTabla FROM SecuenciaCodigo WHERE Estado = 'N' AND IdSucursal = ".$id_cliente.") AND ";
		}
		$rstCombo = $objMantenimiento->obtenerDataSQL("SELECT IdTabla, Descripcion FROM Tabla WHERE ".$fil." Estado='N' ORDER BY Descripcion");
		if(is_string($rstCombo)){
			echo "<option value=0>Sin Informacion".$rstCombo."</option>";
		}else{
			echo "<option value=0>Seleccione Tabla</option>";
			$datoCombo = $rstCombo->fetchAll();
			foreach($datoCombo as $combo){
				$chk="";
				if($_GET["accion"]=="ACTUALIZAR"){
					if($dato[strtolower($value["descripcion"])]==$combo[idtabla]){
						$chk="selected";
					}
				}
				echo "<option value = \"".$combo[idtabla]."\" ".$chk.">".$combo[descripcion]."</option>";
			}
		}
		
		?>
  	  </select></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower(strtolower($value["descripcion"]))]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==4){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
	<?php if($value["idcampo"]==6){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>"></td></tr>
	<?php }?>
	
<?php }?>
	<tr>
	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
	</tr>
</table>
</form>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listSecuenciaCodigo','&id_clase=<?php echo $_GET['id_clase'];?>','frame', 'frame', 'img02')"><?php echo $datoSecuenciaCodigo->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoSecuenciaCodigo->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>