<?php
//Nombre y Codigo de la Clase a Ejecutar
$clase = $_GET["clase"];
$id_clase = $_GET["id_clase"];
$nombre = $_GET["nombre"];
//Uso la variable nombre para el ordenar y paginar
//Requiere para Ejecutar Clase
eval("require(\"../modelo/cls".$clase.".php\");");

//Nro de Hoja a mostrar en la Grilla
$nro_hoja = $_GET["nro_hoja"];
if(!$nro_hoja){//Si no se envia muestra Hoja Nro 1
	$nro_hoja = 1;
}
//Nro de Registros a mostrar en la Grilla
$nro_reg = $_GET["nro_reg"];
if(!$nro_reg){//Si no se envia muestra segun session
	$nro_reg = $_SESSION["R_NroFilaMostrar"];
}

//Para el Filtro
$filtro_str = $_GET["filtro"];
$filtro = str_replace("\'", "'", $filtro_str);
if(!$filtro){//Si esta vacio cierra busqueda
	$filtro = ");";
}else{//Agrega filtro y cierra busqueda
	$filtro = ", ".$filtro.");";
}
?>
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
</head>
<body>
<?php
//Instancia la Clase
eval("\$objGrilla = new cls".$clase."(".$id_clase.", ".$_SESSION['R_IdSucursal'].",\"".$_SESSION['R_NombreUsuario']."\",\"".$_SESSION['R_Clave']."\");");
//Para ver que es lo que consulta
//echo "\$rst = \$objGrilla->consultar".$clase."Interna(".$nro_reg.",".$nro_hoja.$filtro;
?>
    <input name="nro_hoj" type="hidden" id="nro_hoj" value="<?php echo $nro_hoja;?>"/>
    <input type="hidden" id="txtMesa" value="<?=$_GET["txtMesa"]?>" />
    <input type="hidden" id="categoria" value="<?=$_GET["categoria"]?>" />
    <!--<h3 align="center">PLATOS - <?=$_GET["categoria"]?></h3>-->
    <table align="center">
        <tr>
            <td><input type="button" value="1" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="2" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="3" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="4" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="5" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="6" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="7" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="8" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
            <td><input type="button" value="9" class="zoom2" onclick="cargarcantidad(this.value)" /></td>
        </tr>
    </table>
    <table align="center">
        <tr>
            <td>
                <table id="tabla<?php echo $nombre?>" class="tablaint">
                    <tr>
                        <th class="zoom" colspan="4"><?=$_GET["categoria"]?></th>
                    </tr>
                    <?php
                    //>>Inicio Obtiene Operaciones
                    $rstOperaciones = $objGrilla->obtenerOperaciones();
                    if(is_string($rstOperaciones)){
                    	echo "<td colspan=100>Error al obtener Operaciones</td></tr><tr><td colspan=100>".$rstCampos."</td>";
                    	echo "</tr></table>";
                    	exit();
                    }
                    $datoOperaciones = $rstOperaciones->fetchAll();
                    //<<Fin
                    
                    //>>Inicio Obtiene Campos a mostrar
                    $rstCampos = $objGrilla->obtenerCamposMostrar("G");
                    if(is_string($rstCampos)){
                    	echo "<td colspan=100>Error al obtener campos a mostrar</td></tr><tr><td colspan=100>".$rstCampos."</td>";
                    	echo "</tr></table>";
                    	exit();
                    }
                    $dataCampos = $rstCampos->fetchAll();
                    //>>Inicio Ejecutando la consulta
                    //echo "\$rst = \$objGrilla->consultar".$clase."Interna(".$nro_reg.",".$nro_hoja.$filtro;
                    eval("\$rst = \$objGrilla->consultar".$clase."Interna(".$nro_reg.",".$nro_hoja.$filtro);
                    if(is_string($rst)){
                    	echo "<td colspan=100>Error al ejecutar consulta</td></tr><tr><td colspan=100>".$rst."</td>";
                    	echo "</tr></table>";
                    	exit();
                    }
                    $nro_registros_total=0;
                    $c=0;
                    //PRINT_R($rst);
                    while($dato = $rst->fetch()){
                    	$nro_registros_total = $dato["nrototal"];
                    	reset($dataCampos);
                    	$c+=1;
                        if(($c%2-1)==0){
                    ?>
                    <tr id='<?php echo $dato[1];?>-<?php echo $dato[2];?>' class="<?php echo 'impar';?>">
                    <?php
                    }
                    	//foreach($dataCampos as $value){--->dato[33]indica la abreviatura, el 4 indica la descripcion
                    ?>
                    <td width="50%"><input class="" style="font-size: 20px;font-weight: bold;width: auto;" type="button" style="text-transform:uppercase;" value="<?php echo substr(umill($dato[4]),0,26)?>" onclick="seleccionar(<?php echo $dato[1];?>,<?php echo $dato[2];?>);"/></td>
                    <?php
                    if($c%2==0){
                        echo "</tr>";
                    }
                    ?>
                    <?php
                    }
                    if($nro_registros_total==0){
                    	echo "<tr><td class='zoom21' colspan=100>Sin Informaci&oacute;n</td></tr>";
                    }
                    ?>
                    </table>
                    <?php
                    if($nro_reg==0){
                    $nro_reg = $nro_registros_total;
                    }
                    if($nro_registros_total % $nro_reg == 0){
                    	$nro_hojas = (int)($nro_registros_total/$nro_reg);
                    }else{
                    	$nro_hojas = (int)($nro_registros_total/$nro_reg) + 1;
                    }
                    if($nro_hojas<$nro_hoja){
                    	$nro_hoja=1;
                    }
                    if($nro_hoja==$nro_hojas){
                    	$mostrar = $nro_registros_total % $nro_reg;
                    }else{
                    	$mostrar  = $nro_reg;
                    }
                    $inicio=($nro_hoja - 1)*$nro_reg + 1;
                    $fin=($nro_hoja - 1)*$nro_reg + $mostrar;
                    ?>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td>
                        <table class="tablaPaginacion">
                            <tr>
                                <?php
                                $ini = "<td class='zoom21'><a style='font-size: 23px;' href=\"#\" onClick=\"buscarGrillaInterna(";
                                $medio=")\">";
                                if($nro_hojas>11){
                                	for($i=1;$i<=3;$i++){
                                		if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                	}
                                	if($nro_hojas % 2 == 0){
                                		$mitad = (int)($nro_hojas/2);
                                	}else{
                                		$mitad = (int)($nro_hojas/2) + 1;
                                	}
                                	if($nro_hoja>3 && $nro_hoja <= $nro_hojas-3){
                                		if($nro_hoja > 6 && $nro_hoja < $nro_hojas - 5){
                                			if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
                                			for($i=$nro_hoja-2;$i<$nro_hoja;$i++){
                                				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                			}	
                                			for($i=$nro_hoja;$i<=$nro_hoja+2;$i++){
                                				if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                			}	
                                			if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
                                		}else{
                                			if($nro_hoja>=4 && $nro_hoja<=6){
                                				for($i=4;$i<=8;$i++){
                                					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                				}
                                				if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
                                			}else{
                                				if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
                                				for($i=$nro_hojas-7;$i<=$nro_hojas-3;$i++){
                                					if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                				}
                                			}
                                		}
                                	}else{
                                		if($nro_hoja!=4){echo $ini.'4'.$medio."-></a></td>";}else{ echo "<td>-></td>";}
                                		for($i=(int)$mitad-2;$i<=(int)$mitad+2;$i++){
                                			if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                		}
                                		if($nro_hoja!=($nro_hojas-3)){echo $ini.($nro_hojas-3).$medio."<-</a></td>";}else{ echo "<td><-</td>";}
                                	}
                                	for($i=(int)$nro_hojas-2;$i<=(int)$nro_hojas;$i++){
                                		if($nro_hoja!=$i){echo $ini.$i.$medio.$i."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                	}
                                }else{
                                	for($i=1;$i<=$nro_hojas;$i++){
                                		if($nro_hoja!=$i){echo $ini.$i.$medio.$i.""."</a></td>";}else{ echo "<td class='zoom21'>".$i."</td>";}
                                	}
                                }
                                ?>
                                    <td>
                                        <div id="cargando"></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="right">
                            <table><tr><td class="zoom21" width="100%" align="right"><?php if($nro_registros_total==0){echo "No hay registros";}else{echo "Registros del $inicio al $fin (".$mostrar.") de ".$nro_registros_total;}?></td><td></td></tr></table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="tablaint" width="100%">
                    <tr>
                        <th class="zoom" colspan="4">PROPIEDADES</th>
                    </tr>
                    <?php
                        $cont=1;
                        $rst1=$objGrilla->obtenerDataSQL("select * from detallecategoria where idcategoria=".$_GET["idcategoria"]." and estado='N' and idsucursal=".$_SESSION["R_IdSucursal"]);
                        if($rst1->rowCount()>0){
                            while($dato1=$rst1->fetchObject()){
                                if(($cont-1)%3==0){
                                   $tabla.="<tr class=''>";
                                }
                                $tabla.='<td align="left" style="font-size: 20px;font-weight: bold;width: auto;"><input  type="checkbox" onclick="detalleCategoria(this.checked,'.$dato1->iddetallecategoria.')" />'.$dato1->abreviatura.'</td>';
                                if($cont%3==0){
                                    $tabla.="</tr>";
                                }
                                $cont++;
                            } 
                            echo $tabla;
                        }
                    ?>
                </table>
            </td>
        </tr>
    </table>
</body>
</HTML>
<script>
var cantidad=1;
function inicio(){
    document.getElementById("url").value="vista/frmComanda";
    document.getElementById("par").value="&mesa=<?=$_GET["mesa"]?>";
    document.getElementById("div").value="frame";
    document.getElementById("msj").value="frame";
    document.getElementById("img").value="imgloading";
}

function buscarGrillaInterna(nro_hoja){
	if(document.getElementById("nro_hoj")){
		document.getElementById("nro_hoj").value = nro_hoja;
	}
	setRun('vista/listPlatosPredeterminado','&mesa=<?=$_GET["mesa"]?>&categoria=<?=$_GET["categoria"]?>&idcategoria=<?=$_GET["idcategoria"]?>&nro_reg=<?php echo $nro_reg;?>&nro_hoja='+document.getElementById("nro_hoj").value+'&clase=Producto&nombre=Producto&id_clase=45&filtro=<?=addslashes($_GET["filtro"])?>', 'cargagrilla', 'cargagrilla', 'img03');
}

function seleccionar(idproducto,idsucursalproducto){
    //document.getElementById("cargagrilla").innerHTML="";
    //setRun('vista/frmProductoSeleccionado','&mesa=<?=$_GET["mesa"]?>&categoria=<?=$_GET["categoria"]?>&idcategoria=<?=$_GET["idcategoria"]?>&idproducto='+idproducto+'&idsucursalproducto='+idsucursalproducto+'&cantidad='+document.getElementById('txtCantidad').value+'&clase=Producto&nombre=Producto&id_clase=45&filtro=<?=$_GET["filtro"]?>', 'cargagrilla', 'cargagrilla', 'img03');
    var recipiente = document.getElementById('divDetallePedido');
	g_ajaxPagina = new AW.HTTP.Request;
	g_ajaxPagina.setURL("vista/ajaxPedido.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("accion", "agregarProductoMozo");
	g_ajaxPagina.setParameter("IdProducto", idproducto);
	g_ajaxPagina.setParameter("IdSucursalProducto", idsucursalproducto);
	g_ajaxPagina.setParameter("Cantidad", cantidad);
    g_ajaxPagina.setParameter("class", "zoom12");
    g_ajaxPagina.setParameter("modo", "PlatosPredeterminado");
    var list="";
    for(i=0;i<lista.length;i++){
        list+=lista[i]+"-";
    }
    if(lista.length>0){
        g_ajaxPagina.setParameter("listaDetalle", list.substr(0,list.length-1));
    }else{
        g_ajaxPagina.setParameter("listaDetalle", list);
    }
    g_ajaxPagina.setParameter("comanda",document.getElementById("txtNumeroComanda").value);
	g_ajaxPagina.response = function(text){
		recipiente.innerHTML = text;
        //agregarplatos();
	};
	g_ajaxPagina.request();
    //document.getElementById("cargagrilla").innerHTML="";
    //document.getElementById("cargagrilla").style.display="none";
    document.getElementById("frame").style.display='';
    
    document.getElementById("url").value="vista/frmMozo";
    document.getElementById("par").value="&id_clase=0";
    document.getElementById("div").value="frame";
    document.getElementById("msj").value="frame";
    document.getElementById("img").value="imgloading";
}

function cargarcantidad(vcantidad){
    this.cantidad=vcantidad;
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
    //alert(lista);
}

inicio();
</script>