<?php
require("../modelo/clsMovCaja.php");
require("../modelo/clsSalon.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsMovCaja($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstCaja = $objMantenimiento->obtenerTabla();
if(is_string($rstCaja)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstCaja."</td>";
}else{
	$datoCaja = $rstCaja->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataCaja = $rst->fetchAll();
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
	getFormData("frmCaja");
}
function aceptar(){
	if(setValidar("frmCaja")){
		g_ajaxGrabar.setURL("controlador/contMovCaja.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			//buscar();
			alert(text);
			//cargamant.innerHTML="";	
			setRun('vista/listMovCajaChica','&id_clase=<?php echo $_GET['id_clase'];?>','frame','carga','imgloading');	
		};
		g_ajaxGrabar.request();
		loading(true, "loading", "frame", "line.gif",true);
	}
}
<!--LAS SIGUIENTES FUNCIONES LAS USO PARA LLAMAR AL XAJAX Y A LAS FUNCIONES DEL AUTOCOMPLETAR-->
function listadoPersona(div,idrol,nombres){
	var recipiente = document.getElementById(div);
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "BuscaPersona");
	g_ajaxPagina.setParameter("idrol", idrol);
	g_ajaxPagina.setParameter("nombres", nombres);
	g_ajaxPagina.setParameter("div", div);
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
			listadoPersona(div,'1,3,4',document.getElementById('txtPersona').value);
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
function generaNumero(idtipodocumento){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxMovCaja.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "generaNumero");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.response = function(text){
			eval(text);
			document.getElementById('txtNumero').value=vnumero;
			<?php
			if($_GET['accion']=='NUEVO'){?>
			genera_cboConceptoPago(idtipodocumento);
			<?php }?>
		};
		g_ajaxPagina.request();
}
<?php
if($_GET['accion']=='CIERRE' or $_GET['accion']=='ASIGNAR'){?>
generaNumero(10);
<?php }else{?>
generaNumero(9);
<?php }?>
function genera_cboConceptoPago(idtipodocumento){
		var recipiente = document.getElementById('divcboConceptoPago');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxMovCaja.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboConceptoPago");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
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
<?php if($_GET["accion"]=="ASIGNAR") {?>
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
<form id="frmCaja" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="200">
<?php
require("fun.php");
reset($dataCaja);
foreach($dataCaja as $value){
?>
	<?php if($value["idcampo"]==2){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><div id="divcboConceptoPago"><input type="hidden" id="cboConceptoPago" name="cboConceptoPago" value="<?php if($_GET["accion"]=="APERTURA") echo '1'; elseif($_GET["accion"]=="CIERRE") echo '2'; elseif($_GET["accion"]=="ASIGNAR") echo '18';?>"><?php if($_GET["accion"]=="APERTURA") echo 'APERTURA DE CAJA'; elseif($_GET["accion"]=="CIERRE") echo 'CIERRE DE CAJA'; elseif($_GET["accion"]=="ASIGNAR") echo 'ASIGNAR MONTO CAJA';?></div></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "" size="6" maxlength="6" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) return false;"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<tr><td class="alignright">Tipo Documento</td>
    	<td><?php if($_GET["accion"]=="APERTURA") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=9",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); elseif($_GET["accion"]=="CIERRE" OR $_GET["accion"]=="ASIGNAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=10",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4",$value["descripcion"],0,'',$objMantenimiento,"generaNumero(this.value)");?></td>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><input name = "txt<?php echo $value["descripcion"];?>" type="Text" disabled id="txt<?php echo $value["descripcion"];?>" value = "<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==13 and $_GET["accion"]=="NUEVO") {?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optS" value="S" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}}else{ echo "checked=checked";}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    S/.</label>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optD" value="D" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="D"){ echo "checked=checked";}}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    $</label></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==17){?>
	<tr><td class="alignright">Monto</td>
    	<td><?php if($_GET["accion"]=="NUEVO") {?><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "" maxlength="11" size="15" onKeyPress="return validarsolonumerosdecimales(event,this.value);" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>><?php }else{?><table><tr><td>
        Soles : </td><td><input name="txtMontoSoles" type="text" id="txtMontoSoles" 
				value="<?php
				if($_GET['accion']=='APERTURA'){
				$rst = $objMantenimiento->montodeaperturasoles();
				echo $rst;	
				}elseif($_GET['accion']=='CIERRE'){
				$rst = $objMantenimiento->montodecierresoles($_SESSION['R_FechaProceso']);
				echo $rst;	
				}			
				?>" size="15" maxlength="11" <?php 
				$num = $objMantenimiento->existenciamov();
				if(($_GET['accion']=='APERTURA' && $num==0) or $_GET['accion']=='ASIGNAR'){
				}else{
				//echo "readonly=''";
				}
				?> onKeyPress='return validarsolonumerosdecimales(event,this.value);'></td></tr><!--<tr><td>
         Dolares : </td><td><input name="txtMontoDolares" type="text" id="txtMontoDolares" value="<?php /*?><?php 
				if($_GET['accion']=='APERTURA'){
				$rst = $objMantenimiento->montodeaperturadolares();
				echo $rst;
				}elseif($_GET['accion']=='CIERRE'){
				$rst = $objMantenimiento->montodecierredolares($_SESSION['R_FechaProceso']);
				echo $rst;	
				}
				?><?php */?>" size="15" maxlength="11"<?php /*?><?php 
				$num = $objMantenimiento->existenciamov();
				if($_GET['accion']=='APERTURA' && $num==0){
				}else{
				echo "readonly=''";
				}
				?><?php */?>></td></tr>--></table>
          <?php }?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==20){?>
	<tr><td class="alignright">Persona</td>
    	<td><input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE" or $_GET["accion"]=="ASIGNAR") echo $_SESSION['R_IdSucursal'];?>" title="Debe indicar un cliente"><input type="hidden" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE" or $_GET["accion"]=="ASIGNAR") echo $_SESSION['R_IdSucursal'];?>" title="Debe indicar una pesona">
        <?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE" or $_GET["accion"]=="ASIGNAR") {
			echo $_SESSION["R_NombreSucursal"];
		}else{
		?>
        <input name="txtPersona" id="txtPersona" onBlur="autocompletar_blur('divregistrosPersona')" onKeyUp="buscarPersona(event,'divregistrosPersona')" style="width:230px" value=""><button type="button" class="boton" onClick="window.open('main2.php?vista=listPersona&idtablavista=23','_blank','width=580,height=480');">...</button><br>
<div id="divregistrosPersona" class="autocompletar" style="display:none"></div></td>
		<?php }?>
</td></tr>
	<?php }?>
    <?php if($value["idcampo"]==24){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><textarea id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>"></textarea></td></tr>
	<?php }?>
    <?php if($_GET["accion"]=="ASIGNAR") {
    if($value["idcampo"]==35){?>
	<tr><td class="alignright">Sal&oacute;n</td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$dato['idsalon'],'disabled',$objSalon,'genera_cboCaja(this.value,0,"")'); else echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',0,'',$objSalon,'genera_cboCaja(this.value,0,"")');?></td></tr>
    <tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><div id="divcboCaja"></div></td>
	<?php }}?>

<?php }?>
	<tr>
	<td colspan="2" align="center"><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"> <input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('cargamant').innerHTML='';buscar()"></td>
	</tr>
</table>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listMovCaja','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoCaja->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoCaja->descripcionmant; ?></td>
</tr></table>
</div>

</body>
</HTML>