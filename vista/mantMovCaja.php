<?php
require("../modelo/clsMovCaja.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsMovCaja($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>-GASTO");
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
			setRun('vista/listMovCaja','&id_clase=<?php echo $_GET['id_clase'];?>','frame','carga','imgloading');	
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
			if($_GET['accion']=='NUEVO-CAJERO'){?>
			genera_cboConceptoPago(idtipodocumento);
			<?php }?>
		};
		g_ajaxPagina.request();
}
<?php
if($_GET['accion']=='CIERRE-CAJERO'){?>
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
<?php if($_GET['accion']=="NUEVO-CAJERO"){?>
    CargarCabeceraRuta([["Nuevo","vista/mantMovCaja","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }else if($_GET['accion']=="APERTURA"){?>
    CargarCabeceraRuta([["Aperturar Caja","vista/mantMovCaja","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }else if($_GET['accion']=="CIERRE-CAJERO"){?>
    CargarCabeceraRuta([["Cerrar Caja","vista/mantMovCaja","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
<?php /*?>    
<!--AUTOCOMPLETAR: LOS ESTILOS SIGUIENTES SON PARA CAMBIAR EL EFECTO AL MOMENTO DE NAVEGAR POR LA LISTA DEL AUTOCOMPLETAR-->
<style type="text/css">    
		.autocompletar tr:hover, .autocompletar .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
		.autocompletar tr span {text-decoration:none; color:#99CCFF; font-weight:bold; }
		.autocompletar {border:1px solid rgb(0, 0, 0); background-color:rgb(255, 255, 255); position:absolute; overflow:hidden; }
    </style>  
<!--AUTOCOMPLETAR--> 
<?php */?>
<?php //require("tablaheader.php");?>
<?php /*?>
    <div class="container Mesas">
        <form id="frmCaja" action="" method="POST">
        <div class="row">
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <select>
                      <option value="1">INGRESO</option>
                      <option value="1">EGRESO</option>
                    </select>
                    <label>Tipo de Documento</label>
                </div>
            </div>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input id="numero" type="text">
                    <label for="numero">Numero</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input type="date" id="fecha" value="2016-11-30" readonly="true">
                    <label for="fecha" class="active">Fecha</label>
                </div>
            </div>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <select>
                      <option value="1">PAGO DE CLIENTE</option>
                      <option value="1">POR ANULACION DE COMPRA</option>
                      <option value="1">POR COMPRA DE DOLARES</option>
                      <option value="1">AJUSTE TIPO CAMBIO</option>
                      <option value="1">PRESTAMO</option>
                      <option value="1">DEVOLUCION DE PRESTAMO</option>
                      <option value="1">OTROS INGRESOS</option>
                    </select>
                    <label>Concepto Pago</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m6 l6 valign-wrapper">
                <div class="input-field inline col s10 m10 l11">
                    <input type="text" id="persona" class="autocomplete">
                    <label for="persona">Persona</label>
                </div>
                <div class="col s2 m2 l1 center">
                    <button onclick="modalNuevoPersona()" class="btn-floating light-green accent-1"><i class="material-icons black-text">add</i></button>
                </div>
            </div>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input class="validate" id="monto" type="number" min="0" step="0.01" value="0">
                    <label for="monto">Monto (S/.)</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="input-field inline">
                    <textarea id="comentario" class="materialize-textarea"></textarea>
                    <label for="comentario">Comentario</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12" style="padding-bottom: 10px;">
                <button class="btn right amber darken-4">GUARDAR<i class="material-icons right">save</i></button>
            </div>
        </div>
        </form>
    </div>
<?php */?>
    <div class="container">
        <form id="frmCaja" action="" method="POST">
        <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
        <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
        <div class="row Mesas">
            <div class="col s12 m12 l10 offset-l1">
                <table>
<?php
require("fun.php");
reset($dataCaja);
foreach($dataCaja as $value){
    $value = $value;
?>
	<?php if($value["idcampo"]==3){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><div id="divcboConceptoPago">
                <input type="hidden" id="cboConceptoPago" name="cboConceptoPago" value="<?php if($_GET["accion"]=="APERTURA") echo '1'; elseif($_GET["accion"]=="CIERRE-CAJERO") echo '2';?>"><?php if($_GET["accion"]=="APERTURA") echo 'APERTURA DE CAJA'; elseif($_GET["accion"]=="CIERRE-CAJERO") echo 'CIERRE DE CAJA';?>
            </div>
        </td></tr>
	<?php }?>
    <?php if($value["idcampo"]==1){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "" size="6" maxlength="6" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) return false;"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<tr><td class="alignright">Tipo Documento</td>
    	<td><?php if($_GET["accion"]=="APERTURA") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=9",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); elseif($_GET["accion"]=="CIERRE-CAJERO") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4 AND idtipodocumento=10",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4",$value["descripcion"],0,'',$objMantenimiento,"generaNumero(this.value)");?></td>
	<?php }?>
    <?php if($value["idcampo"]==2){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><input name = "txt<?php echo $value["descripcion"];?>" type="Text" disabled id="txt<?php echo $value["descripcion"];?>" value = "<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==9 and $_GET["accion"]=="NUEVO-CAJERO") {?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optS" value="S" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}}else{ echo "checked=checked";}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    S/.</label>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optD" value="D" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="D"){ echo "checked=checked";}}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    $</label></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==4){?>
	<tr><td class="alignright">Monto</td>
    	<td><?php if($_GET["accion"]=="NUEVO-CAJERO") {?><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>><?php }else{?><tr><td>
        Soles : </td><td><input name="txtMontoSoles" type="text" id="txtMontoSoles" 
				value="<?php
				if($_GET['accion']=='APERTURA'){
				$rst = $objMantenimiento->montodeaperturasoles();
				echo $rst;	
				}elseif($_GET['accion']=='CIERRE-CAJERO'){
				$rst = $objMantenimiento->montodecierresoles($_SESSION['R_FechaProceso'],$_SESSION['R_IdCaja'],$_SESSION['R_IdUsuario']);
				echo $rst;	
				}			
				?>" size="15" maxlength="11"<?php 
				$num = $objMantenimiento->existenciamov();
				if($_GET['accion']=='APERTURA' && $num==0){
				}else{
				echo "readonly=''";
				}
				?>></td></tr><!--<tr><td>
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
				?><?php */?>></td></tr>-->
          <?php }?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
	<tr><td class="alignright">Persona</td>
    	<td><input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE-CAJERO") echo $_SESSION['R_IdSucursal'];?>" title="Debe indicar un cliente"><input type="hidden" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE-CAJERO") echo $_SESSION['R_IdSucursal'];?>" title="Debe indicar un persona">
        <?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE-CAJERO") {
			echo $_SESSION["R_NombreSucursal"];
		}else{
		?>
        <input name="txtPersona" id="txtPersona" onBlur="autocompletar_blur('divregistrosPersona')" onKeyUp="buscarPersona(event,'divregistrosPersona')" style="width:230px" value=""><button type="button" class="boton" onClick="window.open('main2.php?vista=listPersona&idtablavista=23','_blank','width=580,height=480');">...</button><br>
<div id="divregistrosPersona" class="autocompletar" style="display:none"></div></td>
		<?php }?>
</td></tr>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><textarea id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>"></textarea></td></tr>
	<?php }?>
<?php }?>
                        </table>
                        <?php include ('./footerMantenimiento.php');?>
                    </div>
            </div>
        </form>
    </div>
</body>
</html>