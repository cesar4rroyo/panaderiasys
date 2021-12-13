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
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
        comida='N';
	vValor = "'"+vOrder + "'," + vBy + ", 0,<?php echo $id_cliente;?>, '" + document.getElementById("cboUsuario").value + "','" + document.getElementById("cbomarca").value + "','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "','" + document.getElementById("cbocompartido").value + "','" + document.getElementById("cbotipo").value + "','',''" + "," + document.getElementById('cboIdSalon').value+",'"+comida+"'";
        var formData = $("#frmBusqueda").serialize();
        setRun('vista/listGrillaPedidosEliminados',formData, 'grilla', 'grilla', 'img03');
        //setRun('vista/listGrillaProductosEliminados','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&id_clase=<?php echo $id_clase;?>&funcion=Reporte&filtro=' + vValor + '&imprimir=SI&tiporeporte=Tops&titulo=Producto&datografico=codigo&fechainicio=' + document.getElementById("txtFechaInicio").value + '&fechafin=' + document.getElementById("txtFechaFin").value, 'grilla', 'grilla', 'img03');
    /*$.ajax({
        type: "POST",
        url: "vista/ajaxPedido.php",        
        data:"accion=detalleProducto"+modo+"&idproducto="+idproducto,
        success: function(a) {
        }
    });*/
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
function cambiaJornada(check){
	/*if(check==true){
		//document.getElementById('lblFecha').innerHTML='Fecha de Jornada';
		//document.getElementById('tdFechaFin').style.display='none';
		document.getElementById('txtFechaInicio').value=document.getElementById('txtFechaFin').value;
		document.getElementById('tdSalon').style.display='';
		document.getElementById('tdCaja').style.display='none';
	}else{
		document.getElementById('lblFecha').innerHTML='Fecha Inicio';
		document.getElementById('tdFechaFin').style.display='';
		document.getElementById('tdSalon').style.display='none';
		document.getElementById('tdCaja').style.display='none';
	}*/
        if(check==true){
            $("#divJornadaSi").show();
	}else{
            $("#divJornadaSi").hide();
	}
}
function genera_cboCaja(idsalon,seleccionado,disabled){
		var recipiente = document.getElementById('divcboCaja');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setAsync(false);
		g_ajaxPagina.setURL("vista/ajaxVenta.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboCaja");
		g_ajaxPagina.setParameter("IdSalon", idsalon);
		g_ajaxPagina.setParameter("seleccionado", seleccionado);
		g_ajaxPagina.setParameter("disabled", disabled);
		g_ajaxPagina.response = function(text){
			recipiente.innerHTML = text+'<label class="black-text">Caja</label>';			
                        $('select').material_select();				
		};
		g_ajaxPagina.request();
}
<?php 
if(isset($_SESSION['R_IdSalon'])) $idsalon=$_SESSION['R_IdSalon']; else $idsalon=0;
if(isset($_SESSION['R_IdCaja'])) $idcaja=$_SESSION['R_IdCaja']; else $idcaja=0;
?>
//genera_cboCaja(<?php echo $idsalon;?>,<?php echo $idcaja;?>,'');
buscar();
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
            <div class="col s12 m6 l3">
                <div class="input-field inline">
                    <?php
                    try{
                    $objMantenimiento = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
                    }catch(PDOException $e) {
                        echo '<script>alert("Error :\n'.$e->getMessage().'");history.go(-1);</script>';
                    }
                    echo genera_cboGeneralSQL(
                            "SELECT idusuario,nombreusuario FROM usuario WHERE estado = 'N' AND idsucursal = ".$_SESSION['R_IdSucursal'],
                            "Usuario",
                            0,
                            '',
                            $objMantenimiento,
                            '',
                            'Todos');
                    //genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');
                    ?>
                    <label class="black-text">Meseros</label>
                </div>
            </div>
            <div class="col s12 m6 l2" hidden="">
                <div class="input-field inline">
                    <?php echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'","marca",0,'',$objMantenimiento, '', 'Todos');?>
                    <label class="black-text">Marca</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline" id="inptFechaInicio">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <input type="time" value="00:00" id="txtHoraInicio" name="txtHoraInicio"/>
                    <label for="txtFechaInicio" class="active">Desde</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <input type="time" value="23:59" id="txtHoraFin" name="txtHoraFin" />
                    <label for="txtFechaFin" class="active">Hasta</label>
                </div>
            </div>
            <div class="col s12 m6 l4" id="divJornadaSi" hidden="">
                <div class="col s12 m12 l12">
                    <div class="input-field inline">
                        <?php echo genera_cboGeneralFun("buscarSalon(0)",'IdSalon',$idsalon,'',$objSalon,'genera_cboCaja(this.value,0,"")');?>
                        <label class="black-text">Sal&oacute;n</label>
                    </div>
                </div>
                <div class="col s12 m12 l4" hidden="">
                    <div class="input-field inline" id="divcboCaja"></div>
                </div>
            </div>
            <div class="col s12 m6 l1" hidden="">
                <div class="input-field inline">
                    <select name="cbosexo" id="cbosexo">
                        <option value="">Todos</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                    <label class="black-text">Sexo</label>
                </div>
            </div>
            <div class="col s12 m6 l1" hidden="">
                <div class="input-field inline">
                    <select name="cbocompartido" id="cbocompartido">
                        <option value="">Todos</option>
                        <option value="S">Si</option>
                        <option value="N">No</option>
                    </select>
                    <label class="black-text">Tipo/Compartido</label>
                </div>
            </div>
            <div class="col s12 m6 l1" hidden="">
                <div class="input-field inline">
                    <select name="cbotipo" id="cbotipo">
                        <option value="">Todos</option>
                        <option value="P">P: Producto Final</option>
                        <option value="I">I: Ingrediente</option>
                    </select>
                    <label class="black-text">Tipo</label>
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
