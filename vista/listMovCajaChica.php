<?php
require('../modelo/clsMovCaja.php');
$id_clase = $_GET["id_clase"];
$nro_reg = 0;
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){
	$nro_hoja = 1;
}
$order = $_GET["order"];
if(!$order){
	$order="1";
}
$by = $_GET["by"];
if(!$by){
	$by="1";
}
if(isset($_GET["idcaja"]) && $_GET["idcaja"]!=""){
	$_SESSION["R_IdCaja"]=$_GET["idcaja"];
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<?php
$objFiltro = new clsMovCaja($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>

g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	*/
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, 4, '" + document.getElementById("txtBuscar").value + "','<?php echo $_SESSION['R_FechaProceso'];?>','CC'," + document.getElementById("cboCaja").value + ",0," + document.getElementById("cboIdTipoDocumento").value + "," + document.getElementById("cboConceptoPago").value + ",'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtComentario").value + "','" + document.getElementById("txtCajero").value + "'";
	<?php
	$cierre = $objFiltro->consultarcierre($_SESSION['R_FechaProceso']);
	if($cierre==0){?>
		setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=MovCaja&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=Caja Chica&origen=CajaChica', 'grilla', 'grilla', 'img03');
                //CargarCabeceraRuta([["Movimiento","vista/listMovCajaChica","<?php echo $_SERVER['QUERY_STRING'];?>"],["Caja Chica","vista/listMovCajaChica","<?php echo $_SERVER['QUERY_STRING'];?>"]],true);
	<?php }else{?>
		setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=MovCaja&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=Caja Chica&origen=CajaChica', 'grilla', 'grilla', 'img03');
                //CargarCabeceraRuta([["Movimiento","vista/listMovCajaChica","<?php echo $_SERVER['QUERY_STRING'];?>"],["Caja Chica","vista/listMovCajaChica","<?php echo $_SERVER['QUERY_STRING'];?>"]],true);
	<?php
	}
	?>

	if($("#cboCaja").val()!="<?=$_SESSION['R_IdCaja']?>"){
		preRun('vista/listmovcajachica','&id_clase=53&idcaja='+$('#cboCaja').val(),'frame','frame','imgloading',3);
	}
}

function ordenar(id){
	document.getElementById("order").value = id;
	if(document.getElementById("by").value=="1"){
		document.getElementById("by").value = "0";	
	}else{
		document.getElementById("by").value = "1";
	}
	buscar();
}

function actualizar(id){
	setRun('vista/mantMovCajaChica','&accion=ACTUALIZAR&clase=Movimiento&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}
function eliminar(id){
	if(!confirm('Est� seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contMovCaja.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "ELIMINAR");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}
function atender(id){
	//if(setValidar()){
		g_ajaxGrabar.setURL("controlador/contMovCaja.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "CABIASITUACION");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
}
buscar();
$("#listaCategorias").remove();
$("#cargagrilla").remove();
//$("#div").val("frameTotal");
function genera_cboConceptoPago(idtipodocumento){
	if(idtipodocumento==0){
		$("#divcboConceptoPago").html('<select id="cboConceptoPago" name="cboConceptoPago"><option value="0">TODOS</option></select><label class="black-text">Concepto Pago</label>');
		$('select').material_select();
		//document.getElementById('divcboConceptoPago').innerHTML='<select id="cboConceptoPago" name="cboConceptoPago"><option value="0">TODOS</option></select><label class="black-text">Concepto Pago</label>';
	}else{
		//var recipiente = document.getElementById('cboConceptoPago');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxMovCaja.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboConceptoPago2");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.setParameter("todos", "TODOS");
		g_ajaxPagina.response = function(text){
			//recipiente.innerHTML = text;
			$("#divcboConceptoPago").html(text+'<label class="black-text">Concepto Pago</label>');
			$('select').material_select();
		};
		g_ajaxPagina.request();
	}
}
function generaBackup(){
    if(confirm('Desea generar el backup del sistema?')){
        g_ajaxGrabar.setURL("controlador/contMovCaja.php?ajax=true");
    	g_ajaxGrabar.setRequestMethod("POST");
    	g_ajaxGrabar.setParameter("accion", "BACKUP");
    	g_ajaxGrabar.response = function(text){
    		loading(false, "loading");		
    		alert(text);			
    	};
    	g_ajaxGrabar.request();
    }
}
function verComprobante(id, idtipodocumento){//alert(idtipodocumento);
	if(idtipodocumento==4){//boleta
		setRun('vista/frmComprobanteB','&idventa=' + id,'frame','carga','imgloading');
	}else{
		if(idtipodocumento==5){//factura
			setRun('vista/frmComprobanteF','&idventa=' + id,'frame','carga','imgloading');
		}else{//ticket
			//alert("Upps. estamos trabajando.");
			setRun('vista/frmComprobanteT','&idventa=' + id,'frame','carga','imgloading');
		}
	}
}
function editarModoPago(idmovimiento){
    setRun('vista/frmEditarModoPago','ajax=true&accion=ACTUALIZAR&clase=Movimiento&id_clase=44&Id=' + idmovimiento,'frame','carga','imgloading');
}
function imprimirEgreso(id){
    g_ajaxGrabar.setURL("http://localhost/lasmusas4874578/vista/ajaxPedido.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "imprimir_egreso");
	g_ajaxGrabar.setParameter("id", id);
	g_ajaxGrabar.response = function(text){
        eval(text);
		loading(false, "loading");
		buscar();
		alert(vmsg);			
	};
	g_ajaxGrabar.request();
	
	loading(true, "loading", "grilla", "linea.gif",true);	
}
</script>
</head>
<body>
    <!--BOTONERA INICIO-->
    <div class="Botones" id="opciones">
        <div class="row">
<?php
require("fun.php");
$rstMovimiento = $objFiltro->obtenerTabla();
if(is_string($rstMovimiento)){
	echo "<td colspan=100>Error al Obtener datos de Perfil</td></tr><tr><td colspan=100>".$rstMovimiento."</td>";
}else{
	$datoMovimiento = $rstMovimiento->fetchObject();
}
$rstOperaciones = $objFiltro->obtenerOperaciones();
if(is_string($rstOperaciones)){
	echo "<td colspan=100>Error al obener Operaciones sobre Perfil</td></tr><tr><td colspan=100>".$rstOperaciones."</td>";
}else{
	$datoOperaciones = $rstOperaciones->fetchAll();
        $opciones = array();
        $ultimoConcepto = $objFiltro->consultarultimoconcepto($_SESSION["R_IdCaja"]);
	foreach($datoOperaciones as $operacion){

	  //echo $ultimoConcepto;
	  
	  if($ultimoConcepto==0)
	  {
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"-->
		<div class="col s6 m6 l3 center">
                    <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();"><i class="material-icons right">open_in_new</i><?php echo umill($operacion['descripcion']);?></button>
                  </div>
		<?php */
		}
		if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
		<div class="col s6 m6 l3 center">
              <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();" disabled="disabled"><i class="material-icons right">open_in_new</i><?php echo umill($operacion['descripcion']);?></button>
            </div> 
		<?php */
		}
		if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled">
		<div class="col s6 m6 l3 center"-->
          <button class="btn-large orange accent-1 truncate orange-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();"><i class="material-icons right">move_to_inbox</i><?php echo umill($operacion['descripcion']);?></button>
        </div> 
		<?php */
		}
		if($operacion["idoperacion"] == 6 && $operacion["tipo"] == "T"){ $opciones[6]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAsignaciones" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=ASIGNAR&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"--> 	  
		<?php */
		}
	  }elseif($ultimoConcepto==1)
	  {
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]=""; /*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
		<div class="col s6 m6 l3 center">
                    <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();"><i class="material-icons right">open_in_new</i><?php echo umill($operacion['descripcion']);?></button>
                  </div>
		<?php */
		}
		if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
		<div class="col s6 m6 l3 center">
              <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();" disabled="disabled"><i class="material-icons right">open_in_new</i><?php echo umill($operacion['descripcion']);?></button>
            </div>
		<?php */
		}
		if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
		<div class="col s6 m6 l3 center">
          <button class="btn-large orange accent-1 truncate orange-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();" disabled="disabled"><i class="material-icons right">move_to_inbox</i><?php echo umill($operacion['descripcion']);?></button>
        </div>	  
		<?php */
		}
		if($operacion["idoperacion"] == 6 && $operacion["tipo"] == "T"){ $opciones[6]=""; /*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAsignaciones" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=ASIGNAR&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
		<!--div class="col s6 m6 l3 center">
          <button class="btn-large red accent-1 truncate red-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=ASIGNAR&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','Nuevo']);" disabled="disabled"><i class="material-icons right">assignment</i><?php echo umill($operacion['descripcion']);?></button>
        </div-->  
		<?php */
		}
	  }elseif($ultimoConcepto==2){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 
		<div class="col s6 m6 l3 center"-->
                <div class="col s6 m6 l3 center">
                    <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();" disabled="disabled"><i class="material-icons right">open_in_new</i><?php echo umill($operacion['descripcion']);?></button>
                  </div>
		<?php */
		}
		if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
			<div class="col s6 m6 l3 center">
              <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();"><i class="material-icons right">open_in_new</i><?php echo umill($operacion['descripcion']);?></button>
            </div>
		<?php */
		}
		if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"-->
		<div class="col s6 m6 l3 center">
          <button class="btn-large orange accent-1 truncate orange-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();" disabled="disabled"><i class="material-icons right">move_to_inbox</i><?php echo umill($operacion['descripcion']);?></button>
        </div>	  
		<?php */
		}
		if($operacion["idoperacion"] == 6 && $operacion["tipo"] == "T"){ $opciones[6]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAsignaciones" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=ASIGNAR&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"-->
		<!--div class="col s6 m6 l3 center">
          <button class="btn-large red accent-1 truncate red-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=ASIGNAR&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','Nuevo']);" disabled="disabled"><i class="material-icons right">assignment</i><?php echo umill($operacion['descripcion']);?></button>
        </div-->  
		<?php */
		}
              
	  }else{
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]="";/*
		?>
                <!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"--> 
                <div class="col s6 m6 l3 center">
                    <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onclick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
                </div>
		<?php */
		}
		if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"--> 
                <div class="col s6 m6 l3 center">
                  <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();" disabled="disabled"><i class="material-icons right">open_in_new</i><?php echo umill($operacion['descripcion']);?></button>
                </div>
		<?php */
		}
		if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
                <div class="col s6 m6 l3 center">
                  <button class="btn-large orange accent-1 truncate orange-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','<?php echo umill($operacion['descripcion']);?>']);$('#tablaActual').hide();"><i class="material-icons right">move_to_inbox</i><?php echo umill($operacion['descripcion']);?></button>
                </div>
	  	<?php */
		}
		if($operacion["idoperacion"] == 6 && $operacion["tipo"] == "T"){ $opciones[6]="disabled='disabled'";/*
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAsignaciones" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=ASIGNAR&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"--> 	  
                <!--div class="col s6 m6 l3 center">
                  <button class="btn-large red accent-1 truncate red-text text-darken-4 tooltipped" type="button" data-position="bottom" data-delay="50" data-tooltip="<?php echo umill($operacion['comentario']);?>" onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=ASIGNAR&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();cargarCabeceraRuta(['Inicio','Movimiento','Caja Chica','Nuevo']);" disabled="disabled"><i class="material-icons right">assignment</i><?php echo umill($operacion['descripcion']);?></button>
                </div-->
		<?php*/
		}
            }
	}?>
                <div class="col s6 m6 l4 center">
                    <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                            type="button" data-position="bottom" data-delay="50" 
                            data-tooltip="NUEVO" 
                            onclick="javascript:setRun('vista/mantMovCajaChica', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();$('#tablaActual').hide();" <?php echo $opciones[1];?>><i class="material-icons right">note_add</i>NUEVO</button>
                </div>
                <div class="col s6 m6 l4 center">
                  <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" 
                          data-delay="50" data-tooltip="APERTURAR CAJA" 
                          onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();$('#tablaActual').hide();" <?php echo $opciones[4];?>><i class="material-icons right">open_in_new</i>APERTURAR CAJA</button>
                </div>
                <div class="col s6 m6 l4 center">
                  <button class="btn-large orange accent-1 truncate orange-text text-darken-4 tooltipped" type="button" data-position="bottom" 
                          data-delay="50" data-tooltip="CERRAR CAJA" 
                          onClick="javascript:setRun('vista/mantMovCajaChica', 'accion=CIERRE&id_clase=<?php echo $id_clase;?>&idcaja='+$('#cboCaja').val(), 'cargamant','cargamant', 'img04');$('#'+$(this).attr('data-tooltip-id')).remove();$('#tablaActual').hide();" <?php echo $opciones[5];?>><i class="material-icons right">move_to_inbox</i>CERRAR CAJA</button>
                </div>
<?php        
}
?>
<?php
if($_SESSION["R_IdPerfil"]=="1" || $_SESSION["R_IdPerfil"]=="2"){
?>
            <div class="col s6 m6 l4 center">
                <p style="margin-top: 25px;">Tipo Cambio: <?php echo number_format($_SESSION['R_TipoCambio'],2);?></p>
            </div>
            <div class="col s6 m6 l4 center">
                <button class="btn-large lime accent-1 truncate lime-text text-darken-4 tooltipped" type="button" data-position="bottom" 
                          data-delay="50" data-tooltip="Liquidacion Diaria" 
                          onClick="javascript:window.open('vista/reportes/ReporteLiquidacionDiaria.php?fecha=<?php echo $_SESSION["R_FechaProceso"]?>','_blank')"><i class="material-icons right">gavel</i>Liquidacion</button>
            </div>
            <div class="col s6 m6 l4 center">
                <button class="btn-large light-blue accent-1 truncate light-blue-text text-darken-4 tooltipped" type="button" data-position="bottom" 
                          data-delay="50" data-tooltip="Generar Backup" 
                          onclick="generaBackup();"><i class="material-icons right">backup</i>BACKUP</button>
            </div>
<?php 
}?>
        </div>
    </div>
    <!--BOTONERA FIN-->


<div id="cargamant"></div>

<!--FILTROS INICIO-->
<div class="container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar">
                    <label for="txtBuscar">Numero</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <?php echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=4",'IdTipoDocumento',0,'',$objFiltro,"genera_cboConceptoPago(this.value)","TODOS");?>
                    <label class="black-text">Tipo documento</label>
                </div>
            </div>
            <div class="col s12 m6 l2" style="">
                <div class="input-field inline" id="divcboConceptoPago">
                    <select id="cboConceptoPago" name="cboConceptoPago"><option value="0">TODOS</option></select>
                    <label class="black-text">Concepto Pago</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input id="txtPersona" name="txtPersona" type="text">
                    <label for="txtPersona">Persona</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input id="txtComentario" name="txtComentario" type="text">
                    <label for="txtComentario">Comentario</label>
                </div>
            </div>
            <div class="col s12 m6 l2" hidden="">
                <div class="input-field inline">
                    <input id="txtCajero" name="txtCajero" type="text">
                    <label for="txtCajero">Cajero</label>
                </div>
            </div>
            <div class="col s12 m6 l2" <?php if($_SESSION["R_IdPerfil"]==4){echo "hidden=''";}?>>
                <div class="input-field inline">
                    <select id="cboCaja" name="cboCaja">
                    	<option value="1" <?php if($_SESSION["R_IdCaja"]==1) echo "selected"; ?>>Caja 1</option>
                    	<option value="2" <?php if($_SESSION["R_IdCaja"]==2) echo "selected"; ?>>Caja 2</option>
                    	<option value="3" <?php if($_SESSION["R_IdCaja"]==3) echo "selected"; ?>>Caja 3</option>
                    	<option value="4" <?php if($_SESSION["R_IdCaja"]==4) echo "selected"; ?>>Caja 4</option>
                    	<option value="5" <?php if($_SESSION["R_IdCaja"]==5) echo "selected"; ?>>Caja 5</option>
                    	<option value="6" <?php if($_SESSION["R_IdCaja"]==6) echo "selected"; ?>>Caja 6</option>
                    	<option value="7" <?php if($_SESSION["R_IdCaja"]==7) echo "selected"; ?>>Boleteria</option>
                    </select>
                    <label for="cboCaja">Caja</label>
                </div>
            </div>
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
            <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2"  onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
                </div>
            </div>
        </div>
    </div>
    <!--FILTROS FINAL-->
    <div class="row">
        <div class="col s12">
            <div id="cargagrilla"></div>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <div id="grilla"></div>
        </div>
    </div>
    <?php
    if($_SESSION["R_IdPerfil"]=="1" || $_SESSION["R_IdPerfil"]=="2"){
        $saldos = $objFiltro->montodecierre($_SESSION['R_FechaProceso']);
        $datosaldo=$saldos->fetchObject();
        $ingreso=number_format($datosaldo->ingreso,2,'.','');
        $egreso=number_format($datosaldo->egreso,2,'.','');
        $saldosoles=number_format($datosaldo->ingreso-$datosaldo->egreso,2,'.','');
        $master=number_format($datosaldo->montomaster,2,'.','');
        $visa=number_format($datosaldo->montovisa,2,'.','');
        $totalcredito=number_format($datosaldo->montomaster + $datosaldo->montovisa,2,'.','');
    ?>
    <div class="row" style="padding-bottom: 20px;">
        <div class="col s12 m4 l4">
            <h5 class="blue lighten-4 blue-text text-darken-4">RESUMEN CAJA</h5>
            <table class="bordered striped highlight">
                <tbody>
                    <tr>
                        <td class="center">Ingresos Total</td>
                        <td class="center"><?php echo $ingreso;?></td>
                    </tr>
                    <tr>
                        <td class="center">Egresos Total</td>
                        <td class="center"><?php echo $egreso;?></td>
                    </tr>
                    <tr>
                        <td class="center">Saldo</td>
                        <td class="center"><?php echo $saldosoles;?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col s12 m4 l4">
            <h5 class="blue lighten-4 blue-text text-darken-4">RESUMEN CREDITO</h5>
            <table class="bordered striped highlight">
                <tbody>
                    <tr>
                        <td class="center">Tarjeta Master Card</td>
                        <td class="center"><?php echo $master;?></td>
                    </tr>
                    <tr>
                        <td class="center">Tarjeta Visa</td>
                        <td class="center"><?php echo $visa;?></td>
                    </tr>
                    <tr>
                        <td class="center">Total</td>
                        <td class="center"><?php echo $totalcredito;?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col s12 m4 l4">
            <h5 class="blue lighten-4 blue-text text-darken-4">RESUMEN EFECTIVO</h5>
            <table class="bordered striped highlight">
                <tbody>
                    <tr>
                        <td class="center">Monto Efectivo</td>
                        <td class="center"><?php echo $saldosoles;?></td>
                    </tr>
                    <tr>
                        <td class="center">Monto Credito</td>
                        <td class="center"><?php echo $totalcredito;?></td>
                    </tr>
                    <tr>
                        <td class="center">Saldo</td>
                        <td class="center"><?php echo number_format($saldosoles+$totalcredito,2,'.','');?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
</div>
</body>
</html>