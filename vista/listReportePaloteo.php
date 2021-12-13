<?php
require('../modelo/clsProducto.php');
require("../modelo/clsSalon.php");
require ('fun.php');
$id_clase = $_GET["id_clase"];
$nro_reg = $_SESSION["R_NroFilaMostrar"];
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
$id_empresa = $_GET["id_empresa"];
if(!$id_empresa){
	$id_empresa = $_SESSION['R_IdEmpresa'];
}
$id_cliente = $_GET["id_cliente"];
if(!$id_cliente){
	$id_cliente = $_SESSION["R_IdSucursal"];
}
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
vcomida = false;
function buscar(){
    var formData = $("#frmBusqueda").serialize();
    setRun('vista/listGrillaReportePaloteo',formData+"&idcategoria="+$("#cboCategoria").val(), 'grilla', 'grilla', 'img03');
}
function consultaControlador(id, accion, mensaje){
	if(!confirm(mensaje)) return false;
		g_ajaxGrabar.setURL("controlador/contValeConsumo.php?ajax=true");
		g_ajaxGrabar.setRequestMethod("POST");
		g_ajaxGrabar.setParameter("accion", accion);
		g_ajaxGrabar.setParameter("txtId", id);
		g_ajaxGrabar.setParameter("txtIdSucursal", <?php echo $_SESSION["R_IdSucursalUsuario"]?>);
		g_ajaxGrabar.setParameter("clase", 52);
		g_ajaxGrabar.response = function(text){
			loading(false, "loading");
			buscar();
			alert(text);			
		};
		g_ajaxGrabar.request();
		
		loading(true, "loading", "grilla", "linea.gif",true);
	//}
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
//buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsProducto($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$objSalon = new clsSalon($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<?php

$rstTabla = $objFiltro->obtenerTabla();
if(is_string($rstTabla)){
	echo "<td colspan=100>Error al Obtener datos de Tabla</td></tr><tr><td colspan=100>".$rstTabla."</td>";
}else{
	$datoTabla = $rstTabla->fetchObject();
}
?>
<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <form id="frmBusqueda">
            <div class="col s12 FiltrosCajero" id="busqueda">
            
            <div class="col s12 m6 l2">
                <div class="input-field inline" id="inptFechaInicio">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    echo date("Y-m-d");?>">
                    <label for="txtFechaInicio" class="active">Fecha</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <?php
                    try{
                    $objMantenimiento = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
                    }catch(PDOException $e) {
                        echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
                    }
                    $rs = $objMantenimiento->obtenerDataSQL("Select vIdCategoria as idcategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")");
                    echo "<select id='cboCategoria' name='cboCategoria' multiple=''>";
                    while($dat=$rs->fetchObject()){
                        echo "<option value='$dat->idcategoria' selected=''>$dat->descripcion</option>";
                    }
                    echo "</select>";
                    //genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');
                    ?>
                    <label class="black-text">Categoria</label>
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
        </form>
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
<div id="modalDetalle" class="modal modal-fixed-footer">
    <div class="modal-content orange lighten-3">
      <div class="white" style="border-radius: 10px;">
        <div class="row">
            <div class="col s12 center"><h4 style="background-color: transparent;">DETALLE DEL MOVIMIENTO</h4></div>
        </div>
          <div class="row tabla" style="padding: 0px 15px 10px 15px" id="tblDetalle">  
        </div>
      </div>
    </div>
    <div class="modal-footer amber lighten-3">
      <button id="" class="modal-action modal-close btn light-green accent-1 black-text" type="button">CERRAR</button>
    </div>
</div>
</body>
</HTML>
