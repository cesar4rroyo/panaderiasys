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
//echo "Inicio de archivo".date("d-m-Y H:i:s:u")."<br>";
?>
<?php
$objFiltro = new clsMovCaja($id_clase, $_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
<script>
<?php if(isset($_SESSION['R_IdTurno'])){?>
g_bandera = true;
g_ajaxGrabar = new AW.HTTP.Request;
function buscar(){
	/*var recipiente = document.getElementById("cargamant");
	recipiente.innerHTML = "";	*/
	vOrder = document.getElementById("order").value;
	vBy = document.getElementById("by").value;
	vValor = "'"+vOrder + "'," + vBy + ", 0, 4, '" + document.getElementById("txtBuscar").value + "','<?php echo $_SESSION['R_FechaProceso'];?>','FC',<?php echo $_SESSION['R_IdCaja'];?>,<?php echo $_SESSION['R_IdUsuario'];?>," + document.getElementById("cboIdTipoDocumento").value + "," + document.getElementById("cboConceptoPago").value + ",'" + document.getElementById("txtPersona").value + "','" + document.getElementById("txtComentario").value + "','" /*+ document.getElementById("txtCajero").value*/ + "'";
	<?php
	$cierre = $objFiltro->consultarcierre($_SESSION['R_FechaProceso']);
	if($cierre==0){?>
		setRun('vista/listGrilla','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=MovCaja&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=Flujos de Caja&origen=FlujoCaja', 'grilla', 'grilla', 'img03');
	<?php }else{?>
		setRun('vista/listGrillaSinOperacion','&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=MovCaja&id_clase=<?php echo $id_clase;?>&filtro=' + vValor + '&imprimir=SI&tiporeporte=DinamicoResumen&titulo=Flujos de Caja&origen=FlujoCaja', 'grilla', 'grilla', 'img03');
	<?php
	}
	?>
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
	setRun('vista/mantMovCaja','&accion=ACTUALIZAR&clase=Movimiento&id_clase=<?php echo $id_clase;?>&Id=' + id,'cargamant', 'cargamant', 'imgloading03');
}
function eliminar(id){
	if(!confirm('Est?? seguro que desea eliminar el registro?')) return false;
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
function genera_cboConceptoPago(idtipodocumento){
	if(idtipodocumento==0){
		document.getElementById('divcboConceptoPago').innerHTML='<select id="cboConceptoPago" name="cboConceptoPago"><option value="0">TODOS</option></select>';
	}else{
		var recipiente = document.getElementById('divcboConceptoPago');
		g_ajaxPagina = new AW.HTTP.Request;
		g_ajaxPagina.setURL("vista/ajaxMovCaja.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("accion", "genera_cboConceptoPago2");
		g_ajaxPagina.setParameter("IdTipoDocumento", idtipodocumento);
		g_ajaxPagina.setParameter("todos", "TODOS");
		g_ajaxPagina.response = function(text){
			$("#divcboConceptoPago").html(text+'<label class="black-text">Concepto Pago</label>');
			$('select').material_select();	
		};
		g_ajaxPagina.request();
	}
}
<?php }?>
</script>
</head>
<body>
    <div class="Botones" id="opciones">
        <div class="row">
<?php 
//echo	$_SESSION['R_IdCaja'];
//echo	$_SESSION['R_IdTurno'];
if(isset($_SESSION['R_IdTurno'])){
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
	foreach($datoOperaciones as $operacion){

	  /*$apertura = $objFiltro->consultarapertura();
	  $cierre = $objFiltro->consultarcierre($_SESSION['R_FechaProceso']);*/
	  //echo $apertura;
	  //echo $cierre;
	  $ultimoConceptoChica = $objFiltro->consultarultimoconcepto();
	  $ultimoConcepto = $objFiltro->consultarultimoconcepto($_SESSION['R_IdCaja'],$_SESSION['R_IdUsuario']);
	  //echo $ultimoConcepto;
	  if($ultimoConceptoChica!=2){
		  /*if($ultimoConcepto==0){
		  }else*/
		  if($ultimoConcepto==1)
		  {
			if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]="";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=NUEVO-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"> 
			<?php */
			}
			if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="disabled='disabled'";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 
			<?php */
			}
			if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="disabled='disabled'";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=CIERRE-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 	  
			<?php */
			}
		  }elseif($ultimoConcepto==2){
			if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]="";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=NUEVO-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"> 
			<?php */
			}
			if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"> 
			<?php */
			}
			if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="disabled='disabled'";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=CIERRE-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 	  
			<?php
			*/}
		  }else{
			if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]="";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=NUEVO-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"> 
			<?php */
			}
			if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="disabled='disabled'";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 
			<?php */
			}
			if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=CIERRE-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');"> 	  
		  <?php */}?>
			<?php
		  }
		}else{
			if($operacion["idoperacion"] == 1 && $operacion["tipo"] == "T"){ $opciones[1]="disabled='disabled'";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdNuevo" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=NUEVO-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 
			<?php */
			}
			if($operacion["idoperacion"] == 4 && $operacion["tipo"] == "T"){ $opciones[4]="disabled='disabled'";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdAperturarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 
			<?php */
			}
			if($operacion["idoperacion"] == 5 && $operacion["tipo"] == "T"){ $opciones[5]="disabled='disabled'";/*
			?>
			<input type="button" title="<?php echo umill($operacion['comentario']);?>" name = "cmdCerrarCaja" value = "<?php echo umill($operacion['descripcion']);?>" onClick="javascript:setRun('vista/mantMovCaja', 'accion=CIERRE-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" disabled="disabled"> 	  
			<?php */
			}
		}
	}?>
            <?php if(isset($opciones[1])){?>
                <div class="col s6 m12 l12 center">
                    <button class="tooltipped btn-large light-green accent-1 truncate light-green-text text-darken-4" 
                            type="button" data-position="bottom" data-delay="50" 
                            data-tooltip="NUEVO" 
                            onClick="javascript:setRun('vista/mantMovCaja', 'accion=NUEVO-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" <?php echo $opciones[1];?>><i class="material-icons right">note_add</i>NUEVO</button>
                </div>
            <?php }elseif(isset($opciones[4])){?>
                <div class="col s6 m6 l4 center">
                  <button class="btn-large teal accent-1 truncate teal-text text-darken-4 tooltipped" type="button" data-position="bottom" 
                          data-delay="50" data-tooltip="APERTURAR CAJA" 
                          onClick="javascript:setRun('vista/mantMovCaja', 'accion=APERTURA&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" <?php echo $opciones[4];?>><i class="material-icons right">open_in_new</i>APERTURAR CAJA</button>
                </div>
            <?php }elseif(isset($opciones[5])){?>
                <div class="col s6 m6 l4 center">
                  <button class="btn-large orange accent-1 truncate orange-text text-darken-4 tooltipped" type="button" data-position="bottom" 
                          data-delay="50" data-tooltip="CERRAR CAJA" 
                          onClick="javascript:setRun('vista/mantMovCaja', 'accion=CIERRE-CAJERO&id_clase=<?php echo $id_clase;?>', 'cargamant','cargamant', 'img04');" <?php echo $opciones[5];?>><i class="material-icons right">move_to_inbox</i>CERRAR CAJA</button>
                </div>
            <?php }?>
<?php
}
?>
        </div>
    </div>
    <!--BOTONERA FIN-->

<div id="cargamant"></div>

<!--FILTROS INICIO-->
<div class="container Mesas" id="tablaActual">
    <div class="row" style="padding: 10px;">
        <div class="col s12 FiltrosCajero" id="busqueda">
            <div class="col s12 m6 l2">
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
            <div class="col s12 m6 l3">
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
        $saldos = $objFiltro->montodecierrecaja($_SESSION['R_IdCaja'],$_SESSION['R_FechaProceso'],$_SESSION['R_IdUsuario']);
        $datosaldo=$saldos->fetchObject();
        $ingreso=number_format($datosaldo->ingreso,2);
        $egreso=number_format($datosaldo->egreso,2);
        $saldosoles=number_format($datosaldo->ingreso-$datosaldo->egreso,2);
        $debito=number_format($datosaldo->montodebito,2);
        $credito=number_format($datosaldo->montocredito,2);
        $totalcredito=number_format($datosaldo->montocredito + $datosaldo->montodebito,2);
    ?>
    <div class="row" style="padding-bottom: 20px;">
        <p class="left-align" style="font-size: 1.5rem;margin-bottom: 0px;padding-left: 15px;">Tipo Cambio: <?php echo number_format($_SESSION['R_TipoCambio'],2);?></p>
        <div class="col s12 m4 l4">
            <h5 class="blue lighten-4 blue-text text-darken-4">RESUMEN CAJA</h5>
            <table class="bordered striped highlight">
                <tbody>
                    <tr>
                        <td class="center">Ingresos</td>
                        <td class="center"><?php echo $ingreso;?></td>
                    </tr>
                    <tr>
                        <td class="center">Egresos</td>
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
                        <td class="center">Tarjeta Debito</td>
                        <td class="center"><?php echo $debito;?></td>
                    </tr>
                    <tr>
                        <td class="center">Tarjeta Credito</td>
                        <td class="center"><?php echo $credito;?></td>
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
                        <td class="center"><?php echo number_format($saldosoles+$totalcredito,2);?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}else{?>
<table><tr><td><img src="img/CAUTION.png" width="100" height="100"></td><td>
Ud. no tiene permiso a flujos de caja, las razones puede ser:<Br>
<ul><li>No tiene un turno asignado para est&aacute; hora.</li></ul></td>
</tr></table>
<?php } ?>
</body>
</html>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                