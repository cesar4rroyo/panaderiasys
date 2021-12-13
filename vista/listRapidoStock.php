<?php
require("../modelo/clsMovimiento.php");
require("../modelo/clsPersona.php");
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

if(isset($_SESSION['R_carroAlmacen']))
$_SESSION['R_carroAlmacen']="";

try{
$objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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

?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function setParametros(){
	g_ajaxGrabar.setParameter("accion", "STOCKRAPIDO");
	g_ajaxGrabar.setParameter("clase", "<?php echo $_GET['id_clase'];?>");
	/*g_ajaxGrabar.setParameter("txtId", document.getElementById("txtId").value);
	g_ajaxGrabar.setParameter("txtDescripcion", document.getElementById("txtDescripcion").value);
	g_ajaxGrabar.setParameter("txtAbreviatura", document.getElementById("txtAbreviatura").value);*/
	getFormData("frmMantAlmacen");
}


var cuenta=0;
function enviado() {
    if (cuenta == 0){
        cuenta++;
        return true;
    }else{
        alert("A enviado dos veces guardar, espere un momento y presione aceptar");
        return false;
    }
}

function aceptar(){
    if(enviado()){
            vValor = "'descripcion',1, 0, '',0,0, '','','S'";
			g_ajaxGrabar.setURL("controlador/contAlmacen.php?ajax=true");
			g_ajaxGrabar.setRequestMethod("POST");
            g_ajaxGrabar.setParameter("filtro",vValor);
			setParametros();
			g_ajaxGrabar.response = function(text){
				loading(false, "loading");
				alert(text);
				//document.getElementById("cargamant").innerHTML="";
				buscarProducto();
			};
			g_ajaxGrabar.request();
    }
	//loading(true, "loading", "frame", "line.gif",true);
}

function ordenarProducto(id){
	document.getElementById("order").value = id;
	if(document.getElementById("by").value=="1"){
		document.getElementById("by").value = "0";	
	}else{
		document.getElementById("by").value = "1";
	}
	buscarProducto();
}
function ocultarResultadoListGrillaInterna(){
	//document.getElementById('divBusquedaProducto').style.display='none';
}

function buscarProducto(){	   	
		vValor = "'descripcion',1, 0, '',0,0, '','','S'";
		setRun('vista/listGrilla2InternaTeclado','&nro_reg=<?php echo "1000000";?>&nro_hoja=1&clase=Producto&nombre=Producto&id_clase=43&modo=multiple&filtro=' + vValor, 'divBusquedaProducto', 'divBusquedaProducto', 'img03');
		document.getElementById('divBusquedaProducto').style.display='';
	
}
buscarProducto();
function generaNumero(idtipodocumento){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxAlmacen.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "generaNumero");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.response = function(text){
			eval(text);
			document.getElementById('txtNumero').value=vnumero;
//			document.getElementById('txtNumero').value='1';
			//asignar();
		};
		g_ajaxPagina.request();
        if(idtipodocumento==8){
            document.getElementById("trSucursalDestino").style.display='';
        }else{
            document.getElementById("trSucursalDestino").style.display='none';
        }
}

//<![CDATA[
var cal = Calendar.setup({
  onSelect: function(cal) { cal.hide() },
  showTime: false
});
//cal.manageFields("btnCalendar", "txtFecha", "%d/%m/%Y");
//"%Y-%m-%d %H:%M:%S"
//]]>

generaNumero(7);
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
<form id="frmMantAlmacen" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "0">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<input type="hidden" id="txtNumero" name="txtNumero" value="" />
<fieldset><legend><strong>REGISTRO RAPIDO STOCK:</strong></legend>
<table width="100%" border="0"><tr><td> 
</td></tr><tr><td>
<div id="divBusquedaProducto" class="autocompletar2">
</div>
</td></tr>
<tr><td>
<input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar()">
</td></tr></table>
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
<hr />
</body>
</HTML>