<?php
require('../modelo/clsMovimiento.php');
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
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
CargarCabeceraRuta([["Movimiento","vista/listPedido","<?php echo $_SERVER['QUERY_STRING'];?>"],["Pedidos","vista/listPedido","<?php echo $_SERVER['QUERY_STRING'];?>"]],true);
function buscar(){
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	*/
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, 5, '" + document.getElementById("txtBuscar").value + "','"+document.getElementById("txtSituacion").value+"','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "',0,0,0,'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtResponsable").value + "','" + document.getElementById("txtComentario").value + "','" + document.getElementById("txtMesa").value + "'";
	if(document.getElementById("txtSituacion").value=='P'){
		setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=de Pedidos Consumidos&origen=Pedido&fechainicio=' + document.getElementById("txtFechaInicio").value + '&fechafin=' + document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
		//CargarCabeceraRuta([["Movimiento","vista/listPedido","<?php echo $_SERVER['QUERY_STRING'];?>"],["Caja Chica","vista/listPedido","<?php echo $_SERVER['QUERY_STRING'];?>"]],true);
	}else{
		if(document.getElementById("txtSituacion").value=='O'){
			setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=de Pedidos Ordenados&origen=Pedido&fechainicio=' + document.getElementById("txtFechaInicio").value + '&fechafin=' + document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
		}else{
			setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Movimiento&id_clase=<?php echo $id_clase;?>&ocultaope=4&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=de Pedidos Atendidos&origen=Pedido&fechainicio=' + document.getElementById("txtFechaInicio").value + '&fechafin=' + document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
		}
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

function actualizar(idmesa, mesa){
	setRun('vista/frmComanda','&accion=ACTUALIZAR&salon=PR&idmesa='+idmesa+'&mesa='+mesa,'frame', 'frame', 'imgloading');
}
function eliminar2(id){
    document.getElementById("DivAutorizar").style.display='';
    document.getElementById("blokeador").style.display='';
    document.getElementById("txtIdPedidoEliminar").value=id;
}
function validarUsuario(){
	g_ajaxGrabar.setURL("controlador/contPedido.php?ajax=true");
	g_ajaxGrabar.setRequestMethod("POST");
	g_ajaxGrabar.setParameter("accion", "VALIDAR");
	g_ajaxGrabar.setParameter("password", document.getElementById("txtPassword").value);
	g_ajaxGrabar.response = function(text){
		eval(text);
        if(vmsg=="S"){
            eliminar2(document.getElementById("txtIdPedidoEliminar").value);            
        }else{
            alert("Password incorrecto");
        }
	};
	g_ajaxGrabar.request();
	
	loading(true, "loading", "grilla", "linea.gif",true);    
}
function eliminar(id){
	if(!confirm('Está seguro que desea eliminar el registro?')) return false;
		g_ajaxGrabar.setURL("controlador/contPedido.php?ajax=true");
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
	if(confirm("Desea atender el pedido???...sino presione cancelar")){
		g_ajaxGrabar.setURL("controlador/contPedido.php?ajax=true");
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
	}
}
buscar();

/*function generarComprobante(id){

	setRun('vista/mantVentaRapida','&accion=NUEVO&clase=Movimiento&id_clase=44&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}*/

function cuentaDelivery(id){
		g_ajaxGrabar.setURL("vista/ajaxPedido.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "imprimir_cuenta_delivery");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert("Imprimiendo");			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
}

function imprimirCocina(id){
		g_ajaxGrabar.setURL("vista/ajaxPedido.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", "imprimir_ticket2");
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("clase", <?php echo $id_clase;?>);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert("Imprimiendo");			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
}

//INICIO CODIGO TABS
function tab(pestana)
	{
		var values = ["document.getElementById('txtSituacion').value='O';buscar('O');", "document.getElementById('txtSituacion').value='A';buscar('A');", "document.getElementById('txtSituacion').value='P';buscar('P');"];
		/*pst 	= document.getElementById(pestana);
		//pnl 	= document.getElementById(panel);
		psts	= document.getElementById('tabs').getElementsByTagName('li');
		//pnls	= document.getElementById('paneles').getElementsByTagName('div');
		
		// eliminamos las clases de las pestañas
		for(i=0; i< psts.length; i++)
		{
			psts[i].className = '';
		}
		
		// Añadimos la clase "actual" a la pestaña activa
		pst.className = 'actual';
		
		var value = values[index];
		eval(value);*/

		var tabs = $(".tab");
		var pos = -1;
	    for(var i=0;i<tabs.length;i++){
	        var tab = tabs[i];
	        $(tab).removeClass("Tab-activo");
	        $(tab).removeClass("Tab-inactivo");
	        $(tab).addClass("Tab-inactivo");
	        if($(tab).attr("id")==pestana){
	        	pos = i;
	        }
	    }
	    $("#"+pestana).removeClass("Tab-inactivo");
	    $("#"+pestana).addClass("Tab-activo");

		var value = values[pos];
		eval(value);
	}
//FIN CODIGO TABS

function centraDivSucursal(){ 
        var top=(document.body.clientHeight/4)+"px"; 
        var left1=(document.body.clientWidth/2);
        var left=(left1-parseInt(document.getElementById("DivAutorizar").style.width)/2)+"px"; 
        document.getElementById("DivAutorizar").style.top=top; 
        document.getElementById("DivAutorizar").style.left=left; 
} 

function cerrar(){
    document.getElementById("DivAutorizar").style.display='none';
    document.getElementById("DivAutorizar").style.height=document.body.clientHeight+'px';
    document.getElementById("DivAutorizar").style.width=document.body.clientWidth+'px';
}

//centraDivSucursal();

</script>
</head>
<body>
<div id="blokeador"></div>
<!--BOTONERA INICIO-->
    <div class="Botones" id="opciones">
        <div class="row">
<?php
$objFiltro = new clsMovimiento($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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
	foreach($datoOperaciones as $operacion){
		if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){
		?>
		<!--input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/frmMesas', 'accion=NUEVO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"-->
		<div class="col s12 m12 l12 center">
            <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                    type="button" data-position="bottom" data-delay="50" 
                    data-tooltip="<?php echo umill($operacion['comentario']);?>" 
                    onClick="javascript:setRun('vista/frmCajero', '&id_clase=<?php echo $id_clase;?>', 'frame','frame', 'imgloading');"><i class="material-icons right">note_add</i><?php echo umill($operacion['descripcion']);?></button>
        </div>
		<?php
		}
	}
}
?>
		</div>
    </div>
    <!--BOTONERA FIN-->

<div id="cargamant"></div>

<!--FILTROS INICIO-->
<div class="row" style="padding: 10px;">
	<div class="col s12">
      	<ul class="tabs blue" style="overflow-x: hidden;">
      		<li class="tab col s6 m4 l4 Tab-activo" id="tabPedido" onclick="tab('tabPedido');"><a href="#">Pedido</a></li>
      		<li class="tab col s6 m4 l4 Tab-inactivo" id="tabAtendido" onclick="tab('tabAtendido');"><a href="#">Atendido</a></li>
      		<li class="tab col s6 m4 l4 Tab-inactivo" id="tabConsumido" onclick="tab('tabConsumido');"><a href="#">Consumido</a></li>
      	</ul>
    </div>
    <input name="txtSituacion" type="hidden" id="txtSituacion" value="O">
	<div class="col s12 container Mesas" id="tablaActual">
	    <div class="row" style="padding: 10px;margin-bottom: 0px;">
	        <div class="col s12 FiltrosCajero" id="busqueda">
	            <div class="col s12 m6 l1">
	                <div class="input-field inline">
	                    <input id="txtBuscar" type="text" name="txtBuscar">
	                    <label for="txtBuscar">Numero</label>
	                </div>
	            </div>
	            <div class="col s12 m6 l1">
	                <div class="input-field inline">
	                    <input id="txtMesa" type="text" name="txtMesa">
	                    <label for="txtMesa">Mesa</label>
	                </div>
	            </div>
	            <div class="col s12 m6 l1">
	                <div class="input-field inline">
	                    <input id="txtResponsable" type="text" name="txtResponsable">
	                    <label for="txtResponsable">Mesero</label>
	                </div>
	            </div>
	            <div class="col s12 m3 l2">
	                <div class="input-field inline">
	                    <input id="txtPersona" name="txtPersona" type="text">
	                    <label for="txtPersona">Cliente</label>
	                </div>
	            </div>
	            <div class="col s12 m3 l2">
	                <div class="input-field inline">
	                    <input id="txtComentario" name="txtComentario" type="text">
	                    <label for="txtComentario">Comentario</label>
	                </div>
	            </div>
	            <div class="col s12 m3 l2">
	                <div class="input-field inline">
	                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
	                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
	                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
	                    <label for="txtFechaInicio" class="active">Fecha Inicio</label>
	                </div>
	            </div>
	            <div class="col s12 m3 l2">
	                <div class="input-field inline">
	                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
	                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
	                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
	                    <label for="txtFechaFin" class="active">Fecha Fin</label>
	                </div>
	            </div>
	            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
			  	<input name="by" type="hidden" id="by" value="<?php echo $by;?>">
			  	<input name="order" type="hidden" id="order" value="<?php echo $order;?>">
	            <div class="col s12 m6 l1 center">
	                <div class="input-field inline">
	                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
	                </div>
	            </div>
	        </div>
	    </div>
	    <!--FILTROS FINAL-->
	    <div class="row">
		    <!--div class="col s12">
		      	<ul class="tabs blue" style="overflow-x: hidden;">
		      		<li class="tab col s6 m4 l4 Tab-activo" id="tabPedido" onclick="tab('tabPedido');"><a href="#">Pedido</a></li>
		      		<li class="tab col s6 m4 l4 Tab-inactivo" id="tabAtendido" onclick="tab('tabAtendido');"><a href="#">Atendido</a></li>
		      		<li class="tab col s6 m4 l4 Tab-inactivo" id="tabConsumido" onclick="tab('tabConsumido');"><a href="#">Consumido</a></li>
		      	</ul>
		    </div>
		    <input name="txtSituacion" type="hidden" id="txtSituacion" value="O"-->
		    <div id="divdiagramaMesa" class="col s12">
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
		    </div>
		</div>
	</div>
</div>

<?php /*?>
<div id="busqueda">
<table>
<tr><td>&nbsp;</td><td>N&uacute;mero</td><td>Mesa</td><td>Mesero</td><td>Cliente</td><td>Comentario</td><td>Fecha Inicio</td><td>Fecha Fin</td></tr>
<tr><td>Buscar por:</td><td>
<input type="text" id="txtBuscar" name="txtBuscar" value="" size="10"></td><td>
<input type="text" id="txtMesa" name="txtMesa" value="" size="4"></td><td>
<input type="text" id="txtResponsable" name="txtResponsable" value="" size="10"></td><td>
<input type="text" id="txtPersona" name="txtPersona" value="" size="10"></td><td>
<input type="text" id="txtComentario" name="txtComentario" value="" size="10"></td><td>
<input type="text" id="txtFechaInicio" name="txtFechaInicio" value="<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar" type="button" class="boton"><img src="img/date.png" width="16" height="16"> </button></td><td>
<input type="text" id="txtFechaFin" name="txtFechaFin" value="<?php echo $_SESSION['R_FechaProceso'];?>" size="10" maxlength="10" title="Debe indicar la fecha"><button id="btnCalendar2" type="button" class="boton"><img src="img/date.png" width="16" height="16"> </button></td><td>
<input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"></td>
  <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
  <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
  <input name="order" type="hidden" id="order" value="<?php echo $order;?>"></td></tr></table>
</div>
<div id="cargagrilla"></div>
<div id="situaciontabs">
  <input name="txtSituacion" type="hidden" id="txtSituacion" value="O">
</div>
<div id="panel">
<ul id="tabs">
    <li id="tab_01" class="actual"><a href="#" onClick="tab('tab_01',0);">Pedido</a></li>
    <li id="tab_02"><a href="#" onClick="tab('tab_02',1);">Atendido</a></li>
    <li id="tab_03"><a href="#" onClick="tab('tab_03',2);">Consumido</a></li>
</ul>
<div id="paneles">
<div id="grilla" style="border: 0px solid #aaa; padding: 0px"></div>
</div>
</div>
<br><br>
<div id="enlaces" style="position:relative">
<table><tr>
	<td><a href="main.php">Inicio</a></td><td>></td>
	<td><?php echo $datoMovimiento->descripcion; ?></td>
</tr></table>
</div>
    <div id="DivAutorizar" style="width:300px;position:absolute;display:none;">
    <?php require("../vista/tablaheader.php");?>
    <form id="form1" name="form1" method="post" action="">
    <br>
    <input type="hidden" id="txtIdPedidoEliminar" name="txtIdPedidoEliminar" value="0" />
    <table>
        <tr>
            <td>Password :</td>
            <td><input type="password" id="txtPassword" name="txtPassword" /></td>
        </tr>
        <tr><td colspan="2" align="center"><input type="button" onclick="validarUsuario();" value="VALIDAR" />
            <input type="button" onclick="cerrar()" value="CERRAR" />
            </td>
        </tr>        
    </table>
    </form>
    <?php require("../vista/tablafooter.php");?>
    </div>
<?php */?>
</body>
</html>