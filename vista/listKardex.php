<?php
require('../modelo/clsKardex.php');
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
	$order="idkardex";
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
	var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0,<?php echo $id_cliente;?>, '" + document.getElementById("txtBuscar_Descripcion").value + "','"+ document.getElementById("cbocategoria").value + "','" + document.getElementById("cbomarca").value + "','" + document.getElementById("txtFechaInicio").value + "','" + document.getElementById("txtFechaFin").value + "','" + document.getElementById("cbocompartido").value + "','" + document.getElementById("cbotipo").value + "','','"+$("#cboTipo").val()+"','"+$("#cboTipoDocumento").val()+"'";

	setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Kardex&id_clase=<?php echo $id_clase;?>&imprimir=SI&tiporeporte=KardexAgrupado&filtro=' + vValor, 'grilla', 'grilla', 'img03');
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
$objFiltro = new clsKardex($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<?php
$rstKardex = $objFiltro->obtenerTabla();
if(is_string($rstKardex)){
	echo "<td colspan=100>Error al Obtener datos de Perfil</td></tr><tr><td colspan=100>".$rstKardex."</td>";
}else{
	$datoKardex = $rstKardex->fetchObject();
}
?>
<div id="cargamant"></div>


<div class="col s12 container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;margin-bottom: 0px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l3">
                <div class="input-field inline">
                    <input id="txtBuscar_Descripcion" type="text" name="txtBuscar_Apellido_Nombre">
                    <label for="txtBuscar_Descripcion">Descripci&oacute;n/Producto</label>
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
                    echo genera_cboGeneralSQL("Select vIdCategoria, vDescripcion as Descripcion from up_buscarcategoriaproductoarbol(".$_SESSION['R_IdSucursal'].") order by vDescripcion","categoria",0,'',$objMantenimiento,'', 'Todos');


                    //genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'",$value["descripcion"],0,'',$objMantenimiento, '', 'Ninguna');
                    ?>
                    <label>Categoria</label>
                </div>
            </div>
            <div class="col s12 m6 l3" hidden="">
                <div class="input-field inline">
                    <?php
                    echo genera_cboGeneralSQL("Select * from Marca Where idsucursal=".$_SESSION['R_IdSucursal']." and Estado='N'","marca",0,'',$objMantenimiento, '', 'Todos');
                    ?>
                    <label>Marca</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <select id="cboTipo" name="cboTipo">
                        <option value="">Todos</option>
                        <option value="S">Ingreso</option>
                        <option value="R">Salida</option>
                    </select>
                    <label>Tipo</label>
                </div>
            </div>
            <div class="col s12 m6 l2">
                <div class="input-field inline">
                    <select id="cboTipoDocumento" name="cboTipoDocumento">
                        <option value="0">Todos</option>
                        <?php 
                        $rst = $objMantenimiento->obtenerDataSQL("select * from tipodocumento where idtipomovimiento in (1,2,3) order by idtipomovimiento desc");
                        while($dat=$rst->fetchObject()){
                            echo "<option value='$dat->idtipodocumento'>$dat->descripcion</option>";
                        }
                        ?>
                    </select>
                    <label>Tipo Doc.</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaInicio" name="txtFechaInicio" value="<?php 
                    $fecha = explode('/',$_SESSION['R_FechaProceso']);
	                echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaInicio" class="active">Fecha Mov. Inicial</label>
                </div>
            </div>
            <div class="col s12 m3 l2">
                <div class="input-field inline">
                    <input type="date" id="txtFechaFin" name="txtFechaFin" value="<?php 
                    	$fecha = explode('/',$_SESSION['R_FechaProceso']);
	                    echo $fecha[2]."-".$fecha[1]."-".$fecha[0];?>">
                    <label for="txtFechaFin" class="active">Fecha Mov. Final</label>
                </div>
            </div>
            <div class="col s12 m6 l11" hidden="">
                <div class="input-field inline">
                    <select name="cbocompartido" id="cbocompartido">
                        <option value="">Todos</option>
                        <option value="S">Si</option>
                        <option value="N">No</option>
                    </select>
                    <label>Compartido</label>
                </div>
            </div>
            <div class="col s12 m6 l11" hidden="">
                <div class="input-field inline">
                    <select name="cbotipo" id="cbotipo">
                        <option value="">Todos</option>
                        <option value="P">P: Producto Final</option>
                        <option value="I">I: Ingrediente</option>
                    </select>
                    <label>Tipo</label>
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