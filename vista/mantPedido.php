<?php
require("../modelo/clsMovimiento.php");
require("../modelo/clsPersona.php");
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

if(isset($_SESSION['R_carroPedido']))
$_SESSION['R_carroPedido']="";

try{
    $objMantenimiento = new clsMovimiento($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
    $objPersona = new clsPersona($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
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

if($_GET["situacionmesa"]=='O'){
$_GET["accion"]="ACTUALIZAR";
}
if($_GET["accion"]=="ACTUALIZAR"){
	if($_GET["situacionmesa"]=='O'){	
	   $rst = $objMantenimiento->consultarMovimientoxMesa($_GET["idmesa"],5);
	}else{
	   $rst = $objMantenimiento->consultarMovimiento(1,1,'2',1,$_GET["Id"],5);
	}
	if(is_string($rst)){
		echo "<td colspan=100>Sin Informacion</td></tr><tr><td colspan=100>".$rst."</td>";
	}else{
		$dato = $rst->fetch();
        $_GET["idmesa"]=$dato['idmesa'];
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
	getFormData("frmMantPedido");
	
}
function aceptar(){
	if(setValidar("frmMantPedido")){
		if(document.getElementById('divDetallePedido').innerHTML!='' && document.getElementById('divDetallePedido').innerHTML!='Debe Agregar Platos y/o Productos!!!'){
			if(parseFloat(document.getElementById('txtTotal').value)>0){
			<?php if($_GET["idmesa"]=="111" || $_GET["idmesa"]=="112" || $_GET["idmesa"]=="113" || $_GET["idmesa"]=="114"){  
			         echo "if(parseFloat(document.getElementById('txtDinero').value)>0){";
                 }     
            ?>
				g_ajaxGrabar.setURL("controlador/contPedido.php?ajax=true");
				g_ajaxGrabar.setRequestMethod("POST");
				setParametros();
					
				g_ajaxGrabar.response = function(text){
					loading(false, "loading");
					//alert(text);
					if(text!='La mesa está ocupada'){
						document.getElementById("cargamant").innerHTML="";
						buscar();
					}
				};
				g_ajaxGrabar.request();
				loading(true, "loading", "frame", "line.gif",true);
 			<?php if($_GET["idmesa"]=="111" || $_GET["idmesa"]=="112" || $_GET["idmesa"]=="113" || $_GET["idmesa"]=="114"){
       			      echo "}else{ alert('Debe ingresar dinero');}";      
                }
            ?>
			}else{
				alert("Debe indicar los productos");
			}			
		}else{
			alert("Debe indicar los productos");
		}
	}
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
	document.getElementById('divBusquedaProducto').style.display='none';
}
function buscarProducto(e){
	if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
	//alert(keyc);
	//teclas izquierda, derescha, shift, control
	if(keyc == 37 || keyc == 39 || keyc == 16 || keyc == 17) { return false;}
	if(keyc == 38 || keyc == 40 || keyc == 13) {
		div="divBusquedaProducto";
		if(document.getElementById(div).innerHTML!=""){
        autocompletarProducto_teclado2(div, 'tablaProducto', keyc);
		}
    }else{

		vOrder = document.getElementById("order").value;
		vBy = document.getElementById("by").value;
		
		vDescripcion = encodeURI(document.getElementById("txtBuscar").value.replace('\'',''));

		vValor = "'"+vOrder + "'," + vBy + ", 0, '" + vDescripcion + "',"+ document.getElementById("cboCategoria").value + "," + document.getElementById("cboMarca").value + ", '" + document.getElementById("txtCodigoBuscar").value + "','P'";
		setRun('vista/listGrilla2InternaTeclado','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=45&filtro=' + vValor, 'divBusquedaProducto', 'divBusquedaProducto', 'img03');
		document.getElementById('divBusquedaProducto').style.display='';
	}
}
//buscarProducto();
function seleccionarProducto(idproducto,idsucursalproducto){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "seleccionarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			eval(text);
			centraDivAutorizar()
			//document.getElementById("divDatosProductoSeleccionado").style.display="";
			document.getElementById("divDatosProductoSeleccionado").className="muestra";
			document.getElementById("txtPrecioVenta").value=vprecioventa;
			document.getElementById("lblProducto").innerHTML=vproducto;
			document.getElementById("txtIdProductoSeleccionado").value=idproducto;
			document.getElementById("txtStockActual").value=vstockactual;
			document.getElementById("txtPrecioCompra").value=vpreciocompra;
			document.getElementById("txtIdSucursalProductoSeleccionado").value=idsucursalproducto;
			document.getElementById("txtCantidad").select();
		};
		g_ajaxPagina.request();
}
function seleccionar(idproducto,idsucursalproducto){
		var recipiente = document.getElementById('DivUnidad');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboUnidad");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.setParameter("Moneda", "S");
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
			seleccionarProducto(idproducto,idsucursalproducto);
            propiedades(idproducto,idsucursalproducto);
		};
		g_ajaxPagina.request();
}
function propiedades(idproducto,idsucursalproducto){
		var recipiente = document.getElementById('divPropiedad');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_propiedad");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
		g_ajaxPagina.response = function(text){
            lista = new Array();
			recipiente.innerHTML = text;			
		};
		g_ajaxPagina.request();
}
var lista = new Array();

function detalleCategoria(checked,iddetallecategoria){
    if(checked){
        lista.push(iddetallecategoria);
    }else{
        for(i=0;i<lista.length;i++){
            if(lista[i]==iddetallecategoria){
                lista.splice(i,1);
            }
        }
    }
}
function agregar(){
		var vprecioventa=parseFloat(document.getElementById('txtPrecioVenta').value);
		var vcantidad=document.getElementById('txtCantidad').value;
		var vpreciocompra=document.getElementById('txtPrecioCompra').value;
		
		if(vprecioventa>=0 && vprecioventa!='' && vcantidad>=0 && vcantidad!=''){
    		var recipiente = document.getElementById('divDetallePedido');
    		g_ajaxPagina = new AW.HTTP.Request;
    		g_ajaxPagina.setURL("vista/ajaxPedido.php");
    		g_ajaxPagina.setRequestMethod("POST");
    		g_ajaxPagina.setParameter("accion", "agregarProducto");
    		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
    		g_ajaxPagina.setParameter("IdSucursalProducto", document.getElementById('txtIdSucursalProductoSeleccionado').value);
    		g_ajaxPagina.setParameter("IdUnidad", document.getElementById('cboUnidad').value);
    		g_ajaxPagina.setParameter("Cantidad", vcantidad);
    		g_ajaxPagina.setParameter("PrecioVenta", vprecioventa);
    		g_ajaxPagina.setParameter("PrecioCompra", vpreciocompra);
    		g_ajaxPagina.setParameter("StockActual", document.getElementById('txtStockActual').value);
    		g_ajaxPagina.setParameter("IdMesa", document.getElementById('cboMesa').value);
		    var list="";
            for(i=0;i<lista.length;i++){
                list+=lista[i]+"-";
            }
            if(lista.length>0){
                g_ajaxPagina.setParameter("listaDetalle", list.substr(0,list.length-1));
            }else{
                g_ajaxPagina.setParameter("listaDetalle", list);
            }
            g_ajaxPagina.response = function(text){
    			recipiente.innerHTML = text;
    			document.getElementById("txtPrecioVenta").value="";
    			document.getElementById("lblProducto").innerHTML="";
    			document.getElementById("txtIdProductoSeleccionado").value="";
    			document.getElementById("txtIdSucursalProductoSeleccionado").value="";
    			document.getElementById("txtStockActual").value="";
    			document.getElementById("txtPrecioCompra").value="";
                document.getElementById("divBusquedaProducto").style.display="none";
    			document.getElementById("txtBuscar").select();
    		};
    		g_ajaxPagina.request();
		}else{
			alert("Los precios y cantidad deben ser números positivos!!!");
		}
}
function quitar(idproducto,idsucursalproducto){
		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "quitarProducto");
		g_ajaxPagina.setParameter("IdProducto", idproducto);
		g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
        g_ajaxPagina.setParameter("IdMesa", <?php echo $_GET['idmesa'];?>);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
			recipiente.focus();
		};
		g_ajaxPagina.request();
}
function agregarDetalleProducto(idmovimiento,idmesa,dinero){
		var recipiente = document.getElementById('divDetallePedido');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "agregarDetallesProducto");
		g_ajaxPagina.setParameter("idmovimiento", idmovimiento);
        g_ajaxPagina.setParameter("IdMesa", idmesa);
		g_ajaxPagina.setParameter("Dinero", dinero);
        g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;
            calcularVuelto();
		};
		g_ajaxPagina.request();
}
function genera_cboMesas(idsalon,situacion,seleccionado,disabled){
		var recipiente = document.getElementById('divcboMesa');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboMesa");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.setParameter("situacion", situacion);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("disabled", disabled);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text;			
		};
		g_ajaxPagina.request();
}
function generaNumero(idmesero){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "generaNumero");
		g_ajaxPagina.setParameter("IdMesero", idmesero);
		g_ajaxPagina.response = function(text){
			eval(text);
			document.getElementById('txtNumero').value=vnumero;
			asignar();
		};
		g_ajaxPagina.request();
}
function cambiaPrecioUnidad(idunidad){
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxPedido.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "cambiaPrecioUnidad");
		g_ajaxPagina.setParameter("IdUnidad", idunidad);
		if(document.getElementById('chkLlevar').checked){
			g_ajaxPagina.setParameter("NroPrecio", 2);		
		}else{
			g_ajaxPagina.setParameter("NroPrecio", 1);		
		}
		g_ajaxPagina.setParameter("IdProducto", document.getElementById('txtIdProductoSeleccionado').value);
		g_ajaxPagina.setParameter("IdSucursalProducto", document.getElementById('txtIdSucursalProductoSeleccionado').value);
		g_ajaxPagina.response = function(text){
			eval(text);
			document.getElementById('txtPrecioCompra').value=vpreciocompra;
			document.getElementById('txtPrecioVenta').value=vprecioventa;
		};
		g_ajaxPagina.request();
}

function calcularVuelto(){
    if(document.getElementById("txtDinero").value!=""){
        var vuelto = parseFloat(document.getElementById("txtDinero").value) - parseFloat(document.getElementById("txtTotal").value);
        vuelto=Math.round(vuelto*100)/100;
        document.getElementById("txtVuelto").value=vuelto;
    }
}

<?php if($_GET['accion']=='ACTUALIZAR'){?>
agregarDetalleProducto(<?php echo $dato['idmovimiento'];?>,<?php echo $dato['idmesa'];?>,'<?php echo $dato['dinero'];?>');
genera_cboMesas(<?php echo $dato['idsalon'];?>,'%',<?php echo $dato['idmesa'];?>,'');
<?php }else{
	if($_GET["situacionmesa"]=='R'){?>
		genera_cboMesas(<?php echo $_GET['idsalon'];?>,'%',<?php echo $_GET['idmesa'];?>,'');
<?php }else{?>
		genera_cboMesas(<?php echo $_GET['idsalon'];?>,'N',<?php echo $_GET['idmesa'];?>,'');
<?php }
}?>
function centraDivAutorizar(){ 
    var top=(document.body.clientHeight/4)+"px"; 
	var left1=(document.body.clientWidth/2);
    var left=(left1-parseInt(document.getElementById("divDatosProductoSeleccionado").style.width)/2)+"px"; 
    document.getElementById("divDatosProductoSeleccionado").style.top=top; 
    document.getElementById("divDatosProductoSeleccionado").style.left=left; 
} 
document.getElementById('txtNroPersonas').select();


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
			listadoPersona(div,3,document.getElementById('txtPersona').value,6);
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
<form id="frmMantPedido" name="frmMantPedido" action="" method="POST">
<input type="hidden" id="txtId" name = "txtId" value = "<?php if($_GET['accion']=='ACTUALIZAR')echo $dato['idmovimiento'];?>">
<input type="hidden" id="txtIdTabla" name = "txtIdTabla" value = "<?php echo $id_tabla;?>">
<table width="100%" border="0"><tr><td>
<fieldset><legend><strong>DATOS DEL DOCUMENTO:</strong></legend> 
<table border="0">
<?php
require("fun.php");
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
	<?php if($value["idcampo"]==5){?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo $objMantenimiento->generaNumeroSinSerie(5,11,substr($_SESSION["R_FechaProceso"],3,2));
	?>" size="6" maxlength="6" title="Debe indicar el n&uacute;mero de pedido" <?php if($_GET["accion"]=="ACTUALIZAR")echo 'disabled';?> onKeyPress="return validarsolonumeros(event);"></td>
	<?php }?>
    <?php if($value["idcampo"]==12){?>
	<tr><td>Sal&oacute;n</td>
    	<td><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$dato['idsalon'],'',$objSalon,'genera_cboMesas(this.value,"%",'.$dato['idmesa'].',"")'); else echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$_GET['idsalon'],'',$objSalon,'genera_cboMesas(this.value,"N",0,"")');?></td>
	<td><?php echo $value["comentario"];?></td>
    	<td><div id="divcboMesa"></div></td>
	<?php }?>
    <?php if($value["idcampo"]==11){?>
	<td><?php echo $value["comentario"];?></td>
    	<td><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8"); else echo 1;
	?>" <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?> title='Debe indicar Nro de Personas'  onKeyPress="return validarsolonumeros(event);"></td>
    <?php if($_GET["situacionmesa"]=='R'){?>
    <td>Reserva</td><td><input type="checkbox" id="chkReserva" name="chkReserva" checked value="S" title="Incluir Datos de Reserva"><input type="hidden" id="txtIdReserva" name="txtIdReserva" value="<?php echo $_GET["idmovimientoreserva"];?>"><input type="text" id="txtNroReserva" name="txtNroReserva" disabled size="6" value="<?php echo $_GET["nroreserva"];?>"></td>
    <?php }?>
    <?php if($_GET["accion"]=="ACTUALIZAR"){
		//Verifico si teine Reserva
		if(isset($dato['idmovimientoref'])){
		?>
        <td>Reserva</td><td><input type="checkbox" id="chkReserva" name="chkReserva" checked value="S" title="Incluir Datos de Reserva"><input type="hidden" id="txtIdReserva" name="txtIdReserva" value="<?php echo $dato['idmovimientoref'];?>"><input type="text" id="txtNroReserva" name="txtNroReserva" disabled size="6" value="<?php echo $dato['numeroref'];?>"></td>
        <?php 
		}
	}?>
    </tr>
	<?php }?>
    <?php if($value["idcampo"]==21){?>
	<td><?php echo $value["comentario"];?></td><td colspan="3"><?php if($_GET["accion"]=="ACTUALIZAR") echo genera_cboGeneralFun2("consultarPersonaxRol(1)",$value["descripcion"],$dato['idsucursalresponsable'].'-'.$dato[strtolower($value["descripcion"])],'',$objPersona,'generaNumero(this.value)'); else echo genera_cboGeneralFun2("consultarPersonaxRol(1)",$value["descripcion"],$_SESSION['R_IdSucursalUsuario'].'-'.$_SESSION['R_IdPersona'],'',$objPersona,'generaNumero(this.value)');?></td></tr>
	<?php }?>
    <?php if($value["idcampo"]==29){
        if($_GET["idmesa"]=="111" || $_GET["idmesa"]=="112" || $_GET["idmesa"]=="113" || $_GET["idmesa"]=="114"){    
    ?>
    <tr><td>Cliente</td>
    	<td colspan="3"><input type="hidden" id="txtIdSucursalPersona" name = "txtIdSucursalPersona" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato['idsucursalpersona']), ENT_QUOTES, "UTF-8"); else echo $_SESSION['R_IdSucursal'];
	?>" title="Debe indicar un cliente">
        <input type="hidden" id="txtIdPersona" name = "txtIdPersona" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato["idpersona"]), ENT_QUOTES, "UTF-8"); else echo "2";
	?>" title="Debe indicar un cliente"><input name="txtPersona" id="txtPersona" onBlur="autocompletar_blur('divregistrosPersona')" onKeyUp="buscarPersona(event,'divregistrosPersona')" style="width:280px" value="<?php if($_GET["accion"]=="ACTUALIZAR") echo $dato["cliente"]; else echo 'VARIOS';?>" autocomplete="off"><button type="button" class="boton" onClick="window.open('main2.php?vista=listPersona&idtablavista=23','_blank','resizable=yes,scrollbars=yes,width=1000,height=520');">...</button><br>
        <div id="divregistrosPersona" class="autocompletar" style="display:none"></div>
    </td>
    <?php
        }else{
    ?>
	<tr><td><?php echo $value["comentario"];?></td>
    	<td colspan="3"><input type="Text" id="txt<?php echo $value["descripcion"];?>" name = "txt<?php echo $value["descripcion"];?>" value = "<?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");?>" style="text-transform:uppercase"  <?php if($value["validar"]=='S' and !($value["msgvalidar"]=='' or empty($value["msgvalidar"]))){echo "title='".$value["msgvalidar"]."'";}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){echo "maxlength=".$value["longitud"];}?> <?php if(!($value["longitud"]==0 or empty($value["longitud"]))){if($value["longitud"]<=100){echo "size=".$value["longitud"];}else{echo "size=100";}}?>></td>
        <?PHP 
        }
        ?>
    <tr>
	<?php }?>
<?php }?>
</table>
</fieldset>
</td></tr><tr><td>
<fieldset>
<legend><strong>BUSQUEDA DE PLATOS Y PRODUCTOS:</strong></legend> 
<div id="busquedaProducto">
    <table>
        <tr>
            <td>Por Descripci&oacute;n :</td>
            <td><input type="text" id="txtBuscar" name="txtBuscar" value="" onKeyUp="javascript: if(this.value!=''){buscarProducto(event);}" autocomplete="off" /></td>
            <td>C&oacute;digo :</td>
            <td><input type="text" id="txtCodigoBuscar" name="txtCodigoBuscar" autocomplete="off" value=""  size="6" maxlength="6" onKeyPress="return validarsolonumeros(event)" onKeyUp="javascript: if(this.value!=''){buscarProducto(event);}"></td><td>Categor&iacute;a :</td><td><?php echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")","Categoria",0,'',$objMantenimiento,'buscarProducto(event)', 'Todos');?></td>
            <td style="display: none;">Marca :</td>
            <td style="display: none;"><?php
    echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'","Marca",0,'',$objMantenimiento, 'buscarProducto(event)', 'Todos');
    ?></td>
            <td><input id="cmdBuscar" type="button" value="Buscar" onClick="javascript:buscarProducto()" style="display:none">
                <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
                <input name="by" type="hidden" id="by" value="<?php echo $by;?>">
                <input name="order" type="hidden" id="order" value="<?php echo $order;?>">
            </td>
        </tr>
    </table>
</div>
<div id="divBusquedaProducto" class="autocompletar2">
</div>
</fieldset>
</td></tr><tr><td>
<div id="divDatosProductoSeleccionado" class="oculta" style="width:600px;position:absolute;">
<?php require("tablaheader.php");?>
<fieldset><legend><strong>DATOS DEL PRODUCTO SELECCIONADO:</strong></legend> 
    <div id="divProductoSeleccionado">
        <table>
            <tr>
                <td>Producto :</td>
                <td><input name="txtIdProductoSeleccionado" type="hidden" id="txtIdProductoSeleccionado" value="0">
                    <input name="txtIdSucursalProductoSeleccionado" type="hidden" id="txtIdSucursalProductoSeleccionado" value="0">
                    <label id="lblProducto" name="lblProducto">...</label>
                </td>
                <td>Unidad :</td>
                <td><div id="DivUnidad"></div><!--Aca se genera el combo unidades y el link para ver las unidades(ponerle imagen: Archivo: xajax_prueba2.php funcion:genera_cboUnidad())--></td>
                <td align="center"><label><input type="checkbox" id="chkLlevar" name="chkLlevar" onchange='cambiaPrecioUnidad(cboUnidad.value)'>Para&nbsp;llevar</label></td>
                <td style="display:none">Stock Actual :</td>
                <td style="display:none">
                    <input name="txtStockActual" type="text" id="txtStockActual" value="0" size="10" maxlength="10" disabled />
                </td>
            </tr>
            <tr>
                <td>Precio Ofertado:</td>
                <td><input type="hidden" name="txtPrecioCompra" id="txtPrecioCompra" value="" maxlength="10"  onKeyPress="return validarsolonumerosdecimales(event,this.value);"><input type="text" name="txtPrecioVenta" id="txtPrecioVenta" value="" maxlength="10" onKeyPress="return validarsolonumerosdecimales(event,this.value);" <?php if($_SESSION["R_IdPerfil"]!="1" && $_SESSION["R_IdPerfil"]!="2"){?>readonly=""<?php }?> size="5"></td>
                <td>Cantidad:</td>
                <td><input type="text" name="txtCantidad" id="txtCantidad" value="1" maxlength="10" size="5" on onKeyPress="if (event.keyCode==13){agregar();document.getElementById('divDatosProductoSeleccionado').className='oculta';}else{return validarsolonumerosdecimales(event,this.value);}">
                </td>
                <td valign="middle" align="center"><!--<a href="#" onClick="agregar()">Agregar</a>-->
                    <button type="button" onClick="javascript: agregar();document.getElementById('divDatosProductoSeleccionado').className='oculta';"><img src="img/cart_add.png" align="absbottom" />&nbsp;Agregar</button>
                    <button type="button" onClick="javascript: document.getElementById('divDatosProductoSeleccionado').className='oculta';"><img src="img/s_cancel.png" align="absbottom" />&nbsp;Cerrar</button>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    <div id="divPropiedad"></div>
                </td>
            </tr>
        </table>
    </div>
</fieldset>
<?php require("tablafooter.php");?>
</div>
</td></tr><tr><td>
<fieldset><legend><strong>DETALLE DEL DOCUMENTO:</strong></legend> 
<div id="divDetallePedido">Debe Agregar Platos y/o Productos!!!</div>
</fieldset>
</td></tr><tr><td>
<fieldset>
<?php
reset($dataMovimientos);
foreach($dataMovimientos as $value){
?>
    <?php if($value["idcampo"]==24){?>
	<table ><tr><td><?php echo $value["comentario"];?></td>
    	<td><textarea name="txt<?php echo $value["descripcion"];?>" id="txt<?php echo $value["descripcion"];?>" cols="30" rows="3"><?php if($_GET["accion"]=="ACTUALIZAR")
echo htmlentities(umill($dato[strtolower($value["descripcion"])]), ENT_QUOTES, "UTF-8");
	?></textarea></td>
	<?php }?>
<?php }?>

	<td><input id="cmdGrabar" type="button" value="GRABAR" onClick="javascript:aceptar();"></td>
    	<td><input id="cmdCancelar" type="button" value="CANCELAR" onClick="javascript:document.getElementById('cargamant').innerHTML='';buscar();"></td>
	</tr>
</table>
</fieldset>
</td></tr></table>
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