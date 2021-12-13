<?php
require("../modelo/clsMovimiento.php");
require("../modelo/clsSalon.php");
$id_clase = $_GET["id_clase"];
$id_tabla = $_GET["id_tabla"];
//echo $id_clase;
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

if(isset($_SESSION['R_carroVenta']))
$_SESSION['R_carroVenta']="";

try{
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
}catch(PDOException $e) {
    echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
	exit();
}

$rstMovimiento = $objMantenimiento->obtenerTabla();
if(is_string($rstMovimiento)){
	echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rstMovimiento."</td>";
}else{
	$datoMovimiento = $rstMovimiento->fetchObject();
}

$rst = $objMantenimiento->obtenercamposMostrar("F");
$dataMovimientos = $rst->fetchAll();

if($_GET["accion"]=="ACTUALIZAR"){
	$rst = $objMantenimiento->consultarMovimiento(1,1,'2',1,$_GET["Id"],"");
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
	getFormData("frmMantVenta");
	
}
function aceptar(){
	if(setValidar("frmMantVenta")){
		if(document.getElementById('divDetalleVenta').innerHTML!='' && document.getElementById('divDetalleVenta').innerHTML!='Debe agregar un pedido!!!'){
			if(parseFloat(document.getElementById('txtTotal').value)>0){
				g_ajaxGrabar.setURL("controlador/contVenta.php?ajax=true");
				g_ajaxGrabar.setRequestMethod("POST");
				setParametros();
					
				g_ajaxGrabar.response = function(text){
					loading(false, "loading");
					//alert(text);
					eval(text);
					buscar();
					//document.getElementById('cargamant').innerHTML="";
				};
				g_ajaxGrabar.request();
				loading(true, "loading", "frame", "line.gif",true);
			}else{
				alert("Debe indicar los productos");
			}	
		}else{
			alert("Debe indicar los productos");
		}
	}
}
function ordenarPedido(id){
	document.getElementById("order").value = id;
	if(document.getElementById("by").value=="1"){
		document.getElementById("by").value = "0";	
	}else{
		document.getElementById("by").value = "1";
	}
	buscarPedido();
}
function ocultarResultadoListGrillaInterna(){
	document.getElementById('divBusquedaPedido').style.display='none';
}
function buscarPedido(e){
	if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
	//alert(keyc);
	//teclas izquierda, derescha, shift, control
	if(keyc == 37 || keyc == 39 || keyc == 16 || keyc == 17) { return false;}
	if(keyc == 38 || keyc == 40 || keyc == 13) {
		div="divBusquedaPedido";
		if(document.getElementById(div).innerHTML!=""){
        autocompletarPedido_teclado2(div, 'tablaPedido', keyc);
		}
    }else{
		
		vOrder = document.getElementById("order").value;
		vBy = document.getElementById("by").value;
		vCliente = encodeURI(document.getElementById("txtClienteBuscar").value.replace('\'',''));
		
		vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','A','"+vCliente+"'";
		setRun('vista/listGrillaInternaTeclado','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&nombre=Pedido&id_clase=47&filtro=' + vValor, 'divBusquedaPedido', 'divBusquedaPedido', 'img03');
		document.getElementById('divBusquedaPedido').style.display='';
	}
}
//buscarPedido();
		vOrder = document.getElementById("order").value;
		vBy = document.getElementById("by").value;
		vCliente = encodeURI(document.getElementById("txtClienteBuscar").value.replace('\'',''));
		
		vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','A'";
		setRun('vista/listGrillaInternaTeclado','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&nombre=Pedido&id_clase=47&filtro=' + vValor, 'divBusquedaPedido', 'divBusquedaPedido', 'img03');
		document.getElementById('divBusquedaPedido').style.display='';
		
function seleccionarpedido(idpedido){
		seleccionarPedidoReserva(idpedido)
		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "listaDetallePedido");
		g_ajaxPagina.setParameter("IdPedido", idpedido);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			document.getElementById('agregar').focus();
		};
		g_ajaxPagina.request();
}
function seleccionarPedidoReserva(idpedido){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "obtenerDatosReserva");
		g_ajaxPagina.setParameter("IdPedido", idpedido);
		g_ajaxPagina.response = function(text){
			eval(text);
			if(vidpersona!="" && vidpersona!="0"){
				if(confirm('Esté pedido tiene datos de reserva. \nDesea asignar estos datos (Cliente) al documento?')){
					mostrarPersona(vidsucursalpersona,vidpersona,'divregistrosPersona');
				}
			}
		};
		g_ajaxPagina.request();
}
function agregar(iddetalle){
		var recipiente = document.getElementById('divDetalleVenta');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarDetalleVenta");
		g_ajaxPagina.setParameter("IdDetalle", iddetalle);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		if(document.getElementById('chkIgv').checked) g_ajaxPagina.setParameter("IncluyeIgv", 'S');
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
		};
		g_ajaxPagina.request();
}
function agregartodo(idpedido){
		var recipiente = document.getElementById('divDetalleVenta');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarTodoDetalleVenta");
		g_ajaxPagina.setParameter("IdPedido", idpedido);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		if(document.getElementById('chkIgv').checked) g_ajaxPagina.setParameter("IncluyeIgv", 'S'); else g_ajaxPagina.setParameter("IncluyeIgv", 'N');
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
		};
		g_ajaxPagina.request();
}
function quitar(iddetalle){
		var recipiente = document.getElementById('divDetalleVenta');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "quitarDetalleVenta");
		g_ajaxPagina.setParameter("IdDetalle", iddetalle);
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		if(document.getElementById('chkIgv').checked) g_ajaxPagina.setParameter("IncluyeIgv", 'S'); else g_ajaxPagina.setParameter("IncluyeIgv", 'N');
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);

		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
		};
		g_ajaxPagina.request();
}
function actualizarDetalleVenta(){
		var recipiente = document.getElementById('divDetalleVenta');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "actualizarDetalleVenta");
		if(document.getElementById("optS").checked){
			g_ajaxPagina.setParameter("Moneda", "S");
		}
		if(document.getElementById("optD").checked){
			g_ajaxPagina.setParameter("Moneda", "D");
		}
		if(document.getElementById('chkIgv').checked) {
			g_ajaxPagina.setParameter("IncluyeIgv", 'S');
		}else{
			g_ajaxPagina.setParameter("IncluyeIgv", 'N');
			}
		g_ajaxPagina.setParameter("IdTipoDocumento", document.getElementById('cboIdTipoDocumento').value);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
		};
		g_ajaxPagina.request();
}
//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
cal.manageFields("btnCalendar", "txtFecha", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>
function generaNumero(idtipodocumento){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "generaNumero");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.response = function(text){
			eval(text);
			document.getElementById('txtNumero').value=vnumero;
			asignar();
		};
		g_ajaxPagina.request();
}
function asignar(){
	if(document.getElementById('cboIdTipoDocumento').value=="5"){
	//    document.getElementById('divTipoPago').style.display="";
		document.getElementById('divImpuesto').style.display="";
		document.getElementById('txtIdSucursalPersona').value="";
		document.getElementById('txtIdPersona').value="";
		document.getElementById('txtPersona').value="";
	}else{
		document.getElementById('divImpuesto').style.display="none";
     //   document.getElementById('divTipoPago').style.display="none";
      //  document.getElementById('divBanco').style.display="none";
		document.getElementById('chkIgv').checked = true;
		document.getElementById('txtIdSucursalPersona').value=<?php echo $_SESSION['R_IdSucursal']?>;
		document.getElementById('txtIdPersona').value=2;
		document.getElementById('txtPersona').value="VARIOS";
	}
	if(document.getElementById('divDetalleVenta').innerHTML!='' && document.getElementById('divDetalleVenta').innerHTML!='Debe agregar un pedido!!!'){
		actualizarDetalleVenta();
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
			listadoPersona(div,3,document.getElementById('txtPersona').value,document.getElementById('cboIdTipoDocumento').value);
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
genera_cboCaja(<?php echo $dato['idsalon'];?>,<?php echo $dato['idcaja'];?>,'disabled');
<?php }else{
if(isset($_SESSION['R_IdSalon'])) $idsalon=$_SESSION['R_IdSalon']; else $idsalon=0;
if(isset($_SESSION['R_IdCaja'])) $idcaja=$_SESSION['R_IdCaja']; else $idcaja=0;
?>
genera_cboCaja(<?php echo $idsalon;?>,<?php echo $idcaja;?>,'');
<?php }?>
document.getElementById('txtBuscar').focus();
</script>
</head>
<body>
<!--AUTOCOMPLETAR: LOS ESTILOS SIGUIENTES SON PARA CAMBIAR EL EFECTO AL MOMENTO DE NAVEGAR POR LA LISTA DEL AUTOCOMPLETAR-->
<style type="text/css">    
		.autocompletar tr:hover, .autocompletar .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
		.autocompletar2 .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
		.autocompletar tr span {text-decoration:none; color:#99CCFF; font-weight:bold; }
		.autocompletar {border:1px solid rgb(0, 0, 0); background-color:rgb(255, 255, 255); position:absolute; overflow:hidden; }
    </style>  
<!--AUTOCOMPLETAR-->  
<?php require("tablaheader.php");?>
<form id="frmMantVenta" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $_GET['Id'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<fieldset><legend><strong>DATOS DEL DOCUMENTO:</strong></legend> 
<table>
<?php
require("fun.php");
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
	<?php if($value["idcampo"]==5){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $objMantenimiento->generaNumero(2,6,substr($_SESSION["R_FechaProceso"],6,4));
	?>" size="15" maxlength="15" title="Debe indicar el número" onBlur="if(!validarnumeroconserie(this.value)){alert('El Numero debe tener el siguiente formato 000-000000-0000');this.focus();}" onKeyUp="mascara(this,'-',new Array(3,6,4),true)"></td>
	<?php }?>
    <?php if($value["idcampo"]==6){?>
	<tr><td>Tipo Documento</td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2",$value["descripcion"],$dato[strtolower($value["descripcion"])],'',$objMantenimiento,"generaNumero(this.value)"); else echo genera_cboGeneralSQL("select * from tipodocumento where idtipomovimiento=2",$value["descripcion"],6,'',$objMantenimiento,"generaNumero(this.value)");?></td>
	<?php }?>
    <?php if($value["idcampo"]==8){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $_SESSION["R_FechaProceso"];?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar" type="button" class="boton" <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>><img src="img/date.png" width="16" height="16"> </button></td>
	<?php }?>
    <?php if($value["idcampo"]==13){?>
	<td><?php echo $value["comentario"];?></td>
    	<td>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optS" value="S" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="S"){ echo "checked=checked";}}else{ echo "checked=checked";}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    S/.</label>
    	  <label>
    	    <input name="opt<?php echo $value["descripcion"];?>" type="radio" id="optD" value="D" <?php if($_GET["accion"]=="ACTUALIZAR"){if($dato[strtolower($value["descripcion"])]=="D"){ echo "checked=checked";}}?> <?php if($_GET["accion"]=="ACTUALIZAR") echo 'disabled';?>>
    	    $</label></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==20){?>
	<tr><td>Cliente</td>
    	<td><input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato['idsucursalpersona']), ENT_QUOTES, "UTF-8"); else echo $_SESSION['R_IdSucursal'];
	?>" title="Debe indicar un cliente">
        <input type="hidden" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo "2";
	?>" title="Debe indicar un cliente"><input name="txtPersona" id="txtPersona" onBlur="autocompletar_blur('divregistrosPersona')" onKeyUp="buscarPersona(event,'divregistrosPersona')" style="width:230px" value="<?php if($_GET["accion"]=="ACTUALIZAR") echo $dato["cliente"]; else echo 'VARIOS';?>"><button type="button" class="boton" onClick="window.open('main2.php?vista=listPersona&idtablavista=23','_blank','resizable=yes,scrollbars=yes,width=1000,height=520');">...</button><br>
<div id="divregistrosPersona" class="autocompletar" style="display:none"></div>
</td>
	<?php }?>
    <?php if($value["idcampo"]==29){?>
	<td>Sal&oacute;n</td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$dato['idsalon'],'disabled',$objSalon,'genera_cboCaja(this.value,0,"")'); else echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$idsalon,'',$objSalon,'genera_cboCaja(this.value,0,"")');?></td>
	<td><?php echo $value["comentario"];?></td>
    	<td><div id="divcboCaja"></div></td>
	<?php }?>
<?php }?>
<td><div id="divImpuesto" style="display:none"><label><input name="chkIgv" type="checkbox" onClick="asignar()" value="S" checked id="chkIgv">Incluido IGV</label></div></td></tr>
<tr id="divTipoPago" style=""><td><label><input name="optTipoPago" type="radio" id="optContado" value="Efectivo" checked="checked" onclick="javascript:document.getElementById('divBanco').style.display='none';" />Efectivo</label></td><td><label><input type="radio" name="optTipoPago" id="optCredito" value="Tarjeta" onclick="javascript:document.getElementById('divBanco').style.display=''; "/>Tarjeta</label></td></tr>
<tr id="divBanco" style="display: none;"><td><label>Banco :</label></td><td><?php echo genera_cboGeneralSQL("select * from banco where idsucursal='".$_SESSION['R_IdSucursal']."' order by descripcion",'Banco','','',$objSalon); ?></td>
<td><label>Tipo Tarjeta :</label></td><td><?php echo genera_cboGeneralSQL("select * from tipotarjeta order by descripcion",'TipoTarjeta','','',$objSalon); ?></td>
<td><label>N&#176; Tarjeta :</label></td><td><input type="text" value="" id="txtNumeroTarjeta" name="txtNumeroTarjeta" size="16" maxlength="16" /></td>
</tr>
<tr>
<td><label>Pago Efectivo : </label></td><td><input type="text" size="6" id="txtPagoEfectivo" name="txtPagoEfectivo" /></td>
</tr>
</table>
</fieldset>
<fieldset>
<legend><strong>BUSQUEDA DE PEDIDOS DE COMENSALES:</strong></legend> 
<div id="busquedaPedido">
<table><tr><td>Buscar por n&uacute;mero:</td><td><input type="text" id="txtBuscar" name="txtBuscar" value="" size="6" maxlength="6" onKeyPress="return validarsolonumeros(event)" onKeyUp="javascript: buscarPedido(event);"></td><td>Cliente:</td><td><input type="text" id="txtClienteBuscar" name="txtClienteBuscar" value="" size="20" maxlength="50" style="text-transform:uppercase" onKeyUp="javascript: if(this.value!=''){buscarPedido(event);}"></td><td style="display:none"><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:buscarPedido(event)">
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="divBusquedaPedido" class="autocompletar2">
</div>
</fieldset>
<fieldset>
<legend><strong>DETALLE DEL PEDIDO:</strong></legend> 
<div id="divDetallePedido">
Debe seleccionar un pedido!!!
</div>
</fieldset>
<fieldset><legend><strong>DETALLE DEL DOCUMENTO:</strong></legend> 
<div id="divDetalleVenta">Debe agregar un pedido!!!</div>
</fieldset>
<fieldset>
<?php
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
    <?php if($value["idcampo"]==24){?>
	<table><tr><td><?php echo $value["comentario"];?></td>
    	<td><textarea name="txt<?php echo $value["descripcion"];?>" id="txt<?php echo $value["descripcion"];?>" cols="30" rows="3"><?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?></textarea></td>
	<?php }?>
<?php }?>

	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('cargamant').innerHTML='';buscar();"></td>
	</tr>
</table>
</fieldset>
</form>
<?php require("tablafooter.php");?>
<div id="enlaces">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><a href="#" onClick="javascript:setRun('vista/listVenta','&id_clase=<?php echo $_GET['id_clase'];?>&id_tabla=<?php echo $_GET['id_tabla'];?>','frame', 'frame', 'img02')"><?php echo $datoMovimiento->descripcion; ?></a></td><td>></td>
	<td><?php echo $datoMovimiento->descripcionmant; ?></td>
</tr></table>
</div>
<hr>
</body>
</HTML>