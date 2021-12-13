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

if($_GET["accion"]=="ACTUALIZAR"){
    $rst = $objMantenimiento->obtenerDataSQL("SELECT * FROM vale WHERE idvale = ".$_GET["Id"]);
	//$rst = $objMantenimiento->consultarMarca(1,1,'2',1,$_GET["Id"],$id_cliente,"");
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
    if($("#txtIdPersona").val()>0 && 
            !isNaN($("#txtValor").val()) && $("#txtValor").val().trim().length>0 && Number($("#txtValor").val())>0 &&
            !isNaN($("#txtPlazo").val()) && $("#txtPlazo").val().trim().length>0 && Number($("#txtPlazo").val())>0 &&
            $("#txtFecha").val().length>0){
        if(setValidar("frmMantMarca")){
		g_ajaxGrabar.setURL("controlador/contValeConsumo.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		setParametros();
        	
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			if(text==1){
				alert('La descripción de la marca no esta disponible, intente con otra descripción.');						
			}else{
			//buscar();
			alert(text);
			}
		};
		g_ajaxGrabar.request();
		loading(true, "cargamant", "frame", "line.gif",true);
	}
    }else{
        alert("CAMPOS LLENADOS INCORRECTAMENTE");
        $(this).stopImmediatePropagation();
    }
}
function verificaNroDoc(nro,tipo)
{
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
        $("#txtPersona").val("");
        $("#txtPersona").removeAttr("readonly");
        $("#txtPersona").focus();
    };
    g_ajaxGrabar.request();
    loading(true, "loading", "contenido", "line.gif",true);
}
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
    $('#persona').prop("readonly",true);
}
function limpiarCamposPersona(){
    $('#txtIdSucursalPersona').val("");
    $('#txtIdPersona').val("");
    $('#persona').prop("readonly",false);
    $('#txtPersona').val("");
    $('#persona').val("");
    $('#persona').focus();
}
listadoPersona2();
<?php if($_GET["accion"]=="ACTUALIZAR"){?>
CargarCabeceraRuta([["ACTUALIZAR - <?php echo umill(str_pad($dato["correlativo"],6,"0",STR_PAD_LEFT));?>",'vista/mantValeConsumo','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
<?php }else{?>
CargarCabeceraRuta([["NUEVO",'vista/mantValeConsumo','<?php echo $_SERVER["QUERY_STRING"];?>']],false);
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
                <div class="row">
                    <div class="col s12 m6 l6 valign-wrapper">
                        <div class="input-field inline col s10 m10 l10">
                            <input type="hidden" id="txtIdPersona" name="txtIdPersona" value="<?php if($_GET["accion"]=="ACTUALIZAR"){ echo $dato["idcliente"];}?>">
                            <input type="hidden" id="txtIdSucursalPersona" name="txtIdSucursalPersona" value="<?php if($_GET["accion"]=="ACTUALIZAR"){ echo $_SESSION["R_IdSucursalUsuario"];}?>">
                            <input type="text" name="persona" id="persona" class="autocomplete" ondblclick="limpiarCamposPersona()" autocomplete="off" <?php if($_GET["accion"]=="ACTUALIZAR"){ echo 'readonly="" value="'.$dato["propietario"].'"';}?>>
                            <label for="persona" <?php if($_GET['accion']=='ACTUALIZAR'){ echo 'class="active"';}?>>Propietario</label>
                        </div>
                        <div class="col s2 m2 l2 center">
                            <button type="button" onclick="limpiarCamposPersona()" class="btn-floating red accent-1"><i class="material-icons black-text">clear</i></button>
                            <button type="button" onclick="modalNuevoPersona()" class="btn-floating light-green accent-1"><i class="material-icons black-text">add</i></button>
                        </div>
                    </div>
                    <div class="col s12 m6 l6">
                        <div class="input-field inline">
                            <input type="text" id="txtValor" name = "txtValor" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo htmlentities(umill($dato["valor"]), ENT_QUOTES, "UTF-8");?>">
                            <label for="txtValor" <?php if($_GET['accion']=='ACTUALIZAR'){ echo 'class="active"';}?>>Valor del Vale</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6 l6">
                        <div class="input-field inline" id="inptFechaInicio">
                            <input type="date" id="txtFecha" name="txtFecha" value="<?php if($_GET["accion"]=="ACTUALIZAR"){ echo $dato["fecha_emision"];}elseif($_GET["accion"]=="NUEVO"){$fecha = explode('/',$_SESSION['R_FechaProceso']); echo $fecha[2]."-".$fecha[1]."-".$fecha[0];}?>">
                            <label for="txtFecha" class="active">Fecha de Emision</label>
                        </div>
                    </div>
                    <div class="col s12 m6 l6">
                        <div class="input-field inline">
                            <input type="text" id="txtPlazo" name = "txtPlazo" value = "<?php if($_GET["accion"]=="ACTUALIZAR")echo htmlentities(umill($dato["plazo"]), ENT_QUOTES, "UTF-8");?>">
                            <label for="txtPlazo" <?php if($_GET['accion']=='ACTUALIZAR'){ echo 'class="active"';}?>>Plazo del Vale</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                </div>
            </div>
    <?php include ('./footerMantenimiento.php');?>
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
</HTML>