<?php
require('../modelo/clsProducto.php');
require ('fun.php');
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
function buscar(){
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;

	vValor = "'"+vOrder + "'," + vBy + ", 0, '" + document.getElementById("txtBuscar_Descripcion").value + "',"+ document.getElementById("cbocategoria").value + "," + document.getElementById("cbomarca").value + ", '" + document.getElementById("txtCodigoBuscar").value + "','" + document.getElementById("cbotipo").value + "','S',"+document.getElementById('cboBarra').value;

	setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=43&funcion=ReporteStock&imprimir=SI&titulo=Stock Producto&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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

function imprimirStock(){
    var g_ajaxPagina2 = new AW.HTTP.Request;
    g_ajaxPagina2.setURL("vista/ajaxPedido.php");
    g_ajaxPagina2.setRequestMethod("POST");
    g_ajaxPagina2.setParameter("accion", "imprimirStock");
    g_ajaxPagina2.setParameter("idsucursal",document.getElementById('cboBarra').value);
    g_ajaxPagina2.setParameter("barra",$('#cboBarra option:selected').text());
    g_ajaxPagina2.response = function(text){
        console.log(text);
    };
    g_ajaxPagina2.request();
}

buscar();
</script>
</head>
<body>
<?php
$objFiltro = new clsProducto($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<?php

$rstProducto = $objFiltro->obtenerTabla();
if(is_string($rstProducto)){
	echo "<td colspan=100>Error al Obtener datos del Perfil</td></tr><tr><td colspan=100>".$rstProducto."</td>";
}else{
	$datoProducto = $rstProducto->fetchObject();
}
?>
<div id="cargamant"></div>
<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l3">
                <div class="input-field inline">
                    <input id="txtBuscar_Descripcion" type="text" name="txtBuscar_Descripcion">
                    <label for="txtBuscar_Descripcion">Descripci&oacute;n/Producto</label>
                </div>
            </div>
            <div class="col s12 m6 l1">
                <div class="input-field inline">
                    <input id="txtCodigoBuscar" type="text" name="txtCodigoBuscar">
                    <label for="txtCodigoBuscar">C&oacute;digo</label>
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
                    echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].")","categoria",0,'',$objMantenimiento,'', 'Todos');
                    //genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');
                    ?>
                    <label class="black-text">Categoria</label>
                </div>
            </div>
            <div class="col s12 m6 l2" hidden="">
                <div class="input-field inline">
                    <?php echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'","marca",0,'',$objMantenimiento, '', 'Todos');?>
                    <label class="black-text">Marca</label>
                </div>
            </div>
            <div class="col s12 m6 l1" hidden="">
                <div class="input-field inline">
                    <select name="cbocompartido" id="cbocompartido">
                        <option value="">Todos</option>
                        <option value="S">Si</option>
                        <option value="N">No</option>
                    </select>
                    <label class="black-text">Compartido</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <select name="cbotipo" id="cbotipo">
                        <option value="">Todos</option>
                        <option value="P">P: Producto Final</option>
                        <option value="I">I: Ingrediente</option>
                    </select>
                    <label class="black-text">Tipo</label>
                </div>
            </div>
            <div class="col s12 m6 l1" hidden="">
                <div class="input-field inline">
                    <select name="cboBarra" id="cboBarra">
                        <option value="1">PRINCIPAL</option>
                        <option value="14">ALMACEN</option>
                        <option value="-1">TODOS</option>
                    </select>
                    <label class="black-text">Sucursal</label>
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
            <div class="col s12 m6 l1 center">
                <div class="input-field inline">
                    <button id="cmdImprimir" type="button" class="btn blue lighten-2" onClick="javascript:imprimirStock();"><i class="material-icons black-text">print</i></button>
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
