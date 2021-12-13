<?php
require("../modelo/clsAsignacionTurno.php");
require("../modelo/clsSalon.php");
require("../modelo/clsTurno.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsAsignacionTurno($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objTurno = new clsTurno($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstAsignacionTurno = $objMantenimiento->obtenerTabla();
if(is_string($rstAsignacionTurno)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstAsignacionTurno."</td>";
}else{
	$datoAsignacionTurno = $rstAsignacionTurno->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataAsignacionTurnos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarAsignacionTurno(1,1,'2',1,$_GET["Id"],"");
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
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
	getFormData("frmMantAsignacionTurno");
	
}
function aceptar(){
	if(setValidar("frmMantAsignacionTurno")){
		g_ajaxGrabar.setURL("controlador/contAsignacionTurno.php?ajax=true");
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
<!--LAS SIGUIENTES FUNCIONES LAS USO PARA LLAMAR AL XAJAX Y A LAS FUNCIONES DEL AUTOCOMPLETAR-->
function listadoPersona(div,idrol,nombres,idtipodocumento){
	var recipiente = document.getElementById(div);
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "BuscaPersona");
	g_ajaxPagina.setParameter("idrol", idrol);
	g_ajaxPagina.setParameter("nombres", nombres);
	g_ajaxPagina.setParameter("div", div);
	if(idtipodocumento==5){
		g_ajaxPagina.setParameter("tipopersona", "RUC");
	}else{
		g_ajaxPagina.setParameter("tipopersona", "DNI");
	}
	g_ajaxPagina.response = function(xform){
		recipiente.innerHTML = xform
	};
	g_ajaxPagina.request();
}

function buscarPersona(e,div){
  if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
    
    if(keyc == 38 || keyc == 40 || keyc == 13) {
		if(document.getElementById(div).innerHTML!=""){
        autocompletar_teclado2(div, 'tablaPersona', keyc);
		}
    }else{
		if(div=='divregistrosPersona'){
			//si presiona retroceso o suprimir
			if(keyc == 8 || keyc == 46) {
				document.getElementById('txtIdPersona').value="";
			}
			listadoPersona(div,1,document.getElementById('txtPersona').value);
		}else{
			//si presiona retroceso o suprimir
			/*if(keyc == 8 || keyc == 46) {
				document.getElementById('txtIdMadre').value="";
			}
			listadoPersona(div,1,document.getElementById('txtMadre').value);*/
		}
  		eval(div+'.style.display="";');
		window.setTimeout(div+'.style.display="";', 300);
  }
}
function mostrarPersona(idsucursal,id,div){
		var g_ajaxPagina = new AW.HTTP.Request;  
		g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "mostrarPersona");
		g_ajaxPagina.setParameter("idsucursal", idsucursal);
		g_ajaxPagina.setParameter("id", id);
		g_ajaxPagina.response = function(xform){
			eval(xform);
			if(div=='divregistrosPersona'){
				document.getElementById('txtIdSucursalPersona').value = idsucursal;
				document.getElementById('txtIdPersona').value = id;
				document.getElementById('txtPersona').value = vNombres;
				divregistrosPersona.style.display="none";
			}else{
				/*document.getElementById('txtIdMadre').value = id;
				document.getElementById('txtMadre').value = vNombres;
				divregistrosMadre.style.display="none";*/
			}
		};
		g_ajaxPagina.request();
}
function genera_cboCaja(idsalon,seleccionado,disabled){
		var recipiente = document.getElementById('divcboCaja');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboCaja");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("disabled", disabled);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
		};
		g_ajaxPagina.request();
}
<?php if($_GET['accion']=='ACTUALIZAR'){?>
genera_cboCaja(<?php echo $dato['idsalon'];?>,<?php echo $dato['idcaja'];?>,'');
<?php }else{?>
genera_cboCaja(0,0,'');
<?php }?>
</script>
</head>
<body>
<!--AUTOCOMPLETAR: LOS ESTILOS SIGUIENTES SON PARA CAMBIAR EL EFECTO AL MOMENTO DE NAVEGAR POR LA LISTA DEL AUTOCOMPLETAR-->
<style type="text/css">    
		.autocompletar tr:hover, .autocompletar .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
		.autocompletar tr span {text-decoration:none; color:#99CCFF; font-weight:bold; }
		.autocompletar {border:1px solid rgb(0, 0, 0); background-color:rgb(255, 255, 255); position:absolute; overflow:hidden; }
    </style>  
<!--AUTOCOMPLETAR-->  
<?php require("tablaheader.php");?>
<form id="frmMantAsignacionTurno" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="200" border="0">
<?php
require("fun.php");
reset($dataAsignacionTurnos);
foreach($dataAsignacionTurnos as $value){
?>
	<?php if($value["idcampo"]==2){?>
	<tr><td>Personal</td>
    	<td><input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato['idsucursalpersona']), ENT_QUOTES, "UTF-8");
	?>" title="Debe indicar un cliente"><input type="hidden" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?>" title="Debe indicar un cliente"><input name="txtPersona" id="txtPersona" onBlur="autocompletar_blur('divregistrosPersona')" onKeyUp="buscarPersona(event,'divregistrosPersona')" style="width:230px" value="<?php if($_GET["accion"]=="ACTUALIZAR") echo $dato["persona"]?>"><br>
<div id="divregistrosPersona" class="autocompletar" style="display:none"></div>
</td>
	<?php }?>
    <?php if($value["idcampo"]==3){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarTurno(0)",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objTurno,'','','seleccione un turno'); else echo genera_cboGeneralFun("buscarTurno(0)",$value["descripcion"],0,'',$objTurno,'','','seleccione un turno');?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==4){?>
	<td>Sal&oacute;n</td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$dato['idsalon'],'',$objSalon,'genera_cboCaja(this.value,0,"")'); else echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',0,'',$objSalon,'genera_cboCaja(this.value,0,"")');?></td></tr>
    <tr><td><?php echo $value["comentario"];?></td>
    	<td><div id="divcboCaja"></div></td>
	<?php }?>

<?php }?>
	<tr>
	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
	</tr>
</table>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listAsignacionTurno','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoAsignacionTurno->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoAsignacionTurno->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>