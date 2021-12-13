<?php
require("../modelo/clsGastos.php");
require("../modelo/clsSalon.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
try{
$objMantenimiento = new clsGastos($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
function listadoPersona2(){
    $.ajax({
        url: "vista/ajaxPersonaMaestro.php",
        type: 'POST',
        data: "accion=BuscaPersonaJSON&idrol=1,3,4,5&nombres=&tipopersona=DNI",
        success: function(a) {
            a = JSON.parse(a);
            var datos = a.datos;
            //console.log($(".autocomplete-content"));
            $(".autocomplete-content").remove();
            $("#persona").autocomplete({
                data: datos
            },selecctionarPersona,"");
        }
    });
}
function selecctionarPersona(dato){
    var ids = dato.split("|");
    $('#txtIdSucursalPersona').val(ids[0]);
    $('#txtIdPersona').val(ids[1]);
    $('#persona').attr("readonly",true);
}
function limpiarCamposPersona(){
    $('#txtIdSucursalPersona').val("");
    $('#txtIdPersona').val("");
    $('#persona').attr("readonly",false);
    $('#txtPersona').val("");
    $('#persona').val("");
    $('#persona').focus();
}
listadoPersona2();
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "<?php echo $_GET['accion'];?>");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	getFormData("frmGastos");
}
function aceptar(){
	if(setValidar("frmGastos")){
		g_ajaxGrabar.setURL("controlador/contGastos.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			//buscar();
			alert(text);
			//cargamant.innerHTML="";	
			setRun('vista/listGastos','&id_clase=<?php echo $_GET['id_clase'];?>','frame','carga','imgloading');	
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
  		eval('document.getElementById("'+div+'")'+'.style.display="";');
		window.setTimeout('document.getElementById("'+div+'")'+'.style.display="";', 300);
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
				document.getElementById('divregistrosPersona').style.display="none";
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
	g_ajaxPagina.setURL("vista/ajaxGastos.php");
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
generaNumero(14);
<?php }else{?>
generaNumero(13);
<?php }?>
function genera_cboConceptoPago(idtipodocumento){
	var recipiente = document.getElementById('divcboConceptoPago');
	g_ajaxPagina = new AW.HTTP.Request;
	g_ajaxPagina.setURL("vista/ajaxGastos.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "genera_cboConceptoPago");
	g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
	g_ajaxPagina.response = function(text){
		recipiente.innerHTML = text;			
		$('select').material_select();
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
function verificaNroDoc(nro,tipo){
	var g_ajaxPagina = new AW.HTTP.Request;
	g_ajaxPagina.setURL("vista/ajaxPersonaMaestro.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "verificaNroDoc");
	g_ajaxPagina.setParameter("nrodoc", nro);
	g_ajaxPagina.setParameter("tipo", tipo);
	g_ajaxPagina.response = function(text){
		eval(text);
		if(vCant>0){
			$("#LabelVerificaNroDoc").show();
            $("#btnAceptarModalPersona").attr("disabled");
		}else{
			$("#LabelVerificaNroDoc").hide();
            $("#btnAceptarModalPersona").removeAttr("disabled");
		}
        console.log(text);
	};
	g_ajaxPagina.request();
}
function setParametrosModalPersona(){
	g_ajaxGrabar.setParameter("accion", "NUEVO");
	g_ajaxGrabar.setParameter("clase", "23");
	//g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
        g_ajaxGrabar.setParameter("txtIdPersona", "");
        g_ajaxGrabar.setParameter("txtIdSucursal", "1");
        g_ajaxGrabar.setParameter("txtIdPersonaMaestro", "");
        g_ajaxGrabar.setParameter("txtDireccion", $("#txtDireccion").val());
        g_ajaxGrabar.setParameter("txtEmail", "");
        g_ajaxGrabar.setParameter("txtTelefonoFijo", "");
        g_ajaxGrabar.setParameter("txtTelefonoMovil", "");
        g_ajaxGrabar.setParameter("cboDpto", "1347");
        g_ajaxGrabar.setParameter("cboProv", "1348");
        g_ajaxGrabar.setParameter("cboDist", "1349");
        g_ajaxGrabar.setParameter("txtImagen", "");
        g_ajaxGrabar.setParameter("chkCompartido", "N");
        g_ajaxGrabar.setParameter("cboIdRol", "5");
	g_ajaxGrabar.setParameter("txtApellidos", $("#txtApellidos").val());
	g_ajaxGrabar.setParameter("txtNombres", $("#txtNombres").val());
	g_ajaxGrabar.setParameter("cboTipoPersona", $("#cboTipoPersona").val());
	g_ajaxGrabar.setParameter("txtNroDoc", $("#txtNroDoc").val());
	if($("#optM").length>1){
            if(document.getElementById("optM").checked){
                g_ajaxGrabar.setParameter("optSexo", "M");
            }
            if(document.getElementById("optF").checked){
		g_ajaxGrabar.setParameter("optSexo", "F");
            }
        }else{
            g_ajaxGrabar.setParameter("optSexo", "");
        }
	g_ajaxGrabar.setParameter("txtFechaNac", "");
}
function aceptarModalPersona(){
    g_ajaxGrabar.setURL("controlador/contPersona.php?ajax=true");
    g_ajaxGrabar.setRequestMethod("POST");
    setParametrosModalPersona();
    g_ajaxGrabar.response = function(text){
            loading(false, "loading");
            alert(text);
            listadoPersona2();
            $('#modalNuevoPersona').closeModal();
            $("#persona").val();
            $("#persona").removeAttr("readonly");
            $("#persona").focus();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "contenido", "line.gif",true);
}
<?php if($_GET['accion']=="NUEVO"){?>
    CargarCabeceraRuta([["Nuevo","vista/mantGastos","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }else if($_GET['accion']=="APERTURA"){?>
    CargarCabeceraRuta([["Aperturar Caja","vista/mantGastos","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }else if($_GET['accion']=="CIERRE"){?>
    CargarCabeceraRuta([["Cerrar Caja","vista/mantGastos","<?php echo $_SERVER['QUERY_STRING'];?>"]],false);
<?php }?>
$("#tablaActual").hide();
$("#opciones").hide();
</script>
</head>
<body>
<div class="container">
    <form id="frmGastos" action="" method="POST">
    <input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
    <input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
    <div class="row Mesas">
        <div class="col s12 m12 l10 offset-l1">
            <table>
<?php
require("fun.php");
reset($dataCaja);
foreach($dataCaja as $value){
?>
	<?php if($value["idcampo"]==3){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><div id="divcboConceptoPago"><input type="hidden" id="cboConceptoPago" name="cboConceptoPago" value="<?php if($_GET["accion"]=="APERTURA") echo '1'; elseif($_GET["accion"]=="CIERRE") echo '2'; elseif($_GET["accion"]=="ASIGNAR") echo '18';?>"><?php if($_GET["accion"]=="APERTURA") echo 'APERTURA DE CAJA'; elseif($_GET["accion"]=="CIERRE") echo 'CIERRE DE CAJA'; elseif($_GET["accion"]=="ASIGNAR") echo 'ASIGNAR MONTO CAJA';?></div></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==1){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "" size="6" maxlength="6" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) return false;"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<tr><td class="alignright">Tipo Documento</td>
    	<td><?php if($_GET["accion"]=="APERTURA") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=7 AND idtipodocumento=13",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); elseif($_GET["accion"]=="CIERRE" OR $_GET["accion"]=="ASIGNAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=7 AND idtipodocumento=14",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,""); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=7",$value["descripcion"],0,'',$objMantenimiento,"generaNumero(this.value)");?></td>
	<?php }?>
    <?php if($value["idcampo"]==2){?>
	<tr><td class="alignright"><?php echo $value["comentario"];?></td>
    	<td><input name = "txt<?php echo $value["descripcion"];?>" type="Text" disabled id="txt<?php echo $value["descripcion"];?>" value = "<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10"></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==9 and $_GET["accion"]=="NUEVO") {?>
	<tr style="display: none;"><td class="alignright"><?php echo $value["comentario"];?></td>
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
				?>" size="10" maxlength="11" <?php 
				$num = $objMantenimiento->existenciamov();
				if(($_GET['accion']=='APERTURA' && $num==0) or $_GET['accion']=='ASIGNAR'){
				}else{
				//echo "readonly=''";
				}
				?> onKeyPress='return validarsolonumerosdecimales(event,this.value);' /></td></tr></table>
          <?php }?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==5){?>
	<tr><td class="alignright">Persona</td>
    	<td><input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE" or $_GET["accion"]=="ASIGNAR") echo $_SESSION['R_IdSucursal'];?>" title="Debe indicar un cliente"><input type="hidden" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE" or $_GET["accion"]=="ASIGNAR") echo $_SESSION['R_IdSucursal'];?>" title="Debe indicar una pesona">
        <?php if($_GET["accion"]=="APERTURA" or $_GET["accion"]=="CIERRE" or $_GET["accion"]=="ASIGNAR") {
			echo $_SESSION["R_NombreSucursal"];
		}else{
		?>
			<div class="input-field inline col s10 m10 l11">
	            <input type="text" id="persona" class="autocomplete" ondblclick="limpiarCamposPersona()" autocomplete="off">
           	</div>
            <div class="col s2 m2 l1 center">
                <button type="button" onclick="modalNuevoPersona()" class="btn-floating light-green accent-1"><i class="material-icons black-text">add</i></button>
            </div>
          
        </td>
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
    <div class="modalNuevoPersona">
        <div id="modalNuevoPersona" class="modal modal-fixed-footer orange lighten-3">
            <div class="modal-content">
              <div class="white" style="padding: 10px;border-radius: 10px;">
                  <form id="frmMantPersonaMaestro" method="POST" action="">
                <div class="row">
                  <div class="col s12">
                      <div class="input-field inline">
                        <select id="cboTipoPersona" name="cboTipoPersona" onchange="cambiarTipoPersona('contenido',$(this).val());">
                            <option value="NATURAL">Natural</option>
                            <option value="VARIOS">Varios</option>
                        </select>
                        <label for="monto">Tipo Persona</label>
                      </div>
                  </div>
                  <div class="col s12" id="contenido"></div>
                </div>
                  </form>
              </div>
            </div>
            <div class="modal-footer amber lighten-3">
                <button id="btnAceptarModalPersona" disabled="" type="button" onclick="aceptarModalPersona()" class="waves-effect waves-green btn light-green accent-1 black-text">Agregar<i class="material-icons right">add</i></button>
            </div>
        </div>
    </div>
</body>
</html>