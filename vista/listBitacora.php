<?php
session_start();
if(!isset($_SESSION['R_IdSucursal'])){
	echo "<script>alert('Se cerro la Sesion');redireccionar('Index.php');</script>";
	exit();
}

require('../modelo/clsBitacora.php');
$id_clase = $_GET["id_clase"];
$nro_reg = $_SESSION["NroFilaMostrar"];
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
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ",0, '" + document.getElementById("txtBuscar").value + "', '"+ document.getElementById("cboOpcionBusqueda").value + "',0";
	setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&by='+document.getElementById("by").value+'&clase=Bitacora&id_clase=<?php echo $id_clase;?>&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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



buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsBitacora($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>

<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l5">
                <div class="input-field inline">
                    <select name="cboOpcionBusqueda" id="cboOpcionBusqueda">
                        <option value="empresa.razonsocial">Empresa</option>
                        <option value="sucursal.razonsocial">Sucursal</option>
                        <option value="bitacora.nombreusuario">Usuario</option>
                        <option value="bitacora.fecha">Fecha</option>
                        <option value="bitacora.accion">Accion</option>
                        <option value="bitacora.registro">Detalle</option>
                    </select>
                    <label>Opcion Busqueda</label>
                </div>
            </div>
            <div class="col s12 m6 l6">
                <div class="input-field inline">
                    <input id="txtBuscar" type="text" name="txtBuscar" onKeyUp="if(event.keyCode=='13'){buscar();}">
                    <label for="txtBuscar">Buscar</label>
                </div>
            </div>
            <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>">
            <input name="by" type="hidden" id="by" value="0">
            <input name="order" type="hidden" id="order" value="IdBitacora">
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdBuscar" type="button" class="btn lime lighten-2" onClick="javascript:document.getElementById('nro_hoj').value=1;buscar();"><i class="material-icons black-text">search</i></button>
                </div>
            </div>
        </div>
    </div>
    <!--FILTROS FINAL-->
    <div class="row">
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
</body>
</HTML>