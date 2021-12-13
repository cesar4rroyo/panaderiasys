<?php
session_start();
require("../modelo/clsDetalleAlmacen.php");
$accion = $_POST["accion"];
switch($accion){
case "generaNumero" :
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$numero = $ObjDetalleAlmacen->generaNumeroSinSerie(4,$_POST["IdTipoDocumento"],substr($_SESSION["R_FechaProceso"],3,2));

	echo "vnumero='".$numero."';";
	break;
case "genera_cboConceptoPago" :
	if($_POST["IdTipoDocumento"]==9) $tipo='I';
	if($_POST["IdTipoDocumento"]==10) $tipo='E';
	require("../modelo/clsConceptoPago.php");
	$ObjConceptoPago = new clsconceptoPago(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjConceptoPago->consultarConceptoPagoxTipoDocumento($tipo);

	$cadena="<select name='cboConceptoPago' id='cboConceptoPago'>";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		if($registro->idconceptopago!=1 and $registro->idconceptopago!=2 and $registro->idconceptopago!=3 and $registro->idconceptopago!=17 and $registro->idconceptopago!=18){
		$cadena=$cadena."<option value='".$registro->idconceptopago."' ".$seleccionar.">".$registro->descripcion."</option>";
		}
	}
	}else{
		$cadena=$cadena."<option value='0'>No hay Conceptos de Pago</option>";
	}
	$cadena=$cadena."</select>";
	$cadena=utf8_encode($cadena);
	echo $cadena;
	break;
case "genera_cboConceptoPago2" :
	if($_POST["IdTipoDocumento"]==9) $tipo='I';
	if($_POST["IdTipoDocumento"]==10) $tipo='E';
	require("../modelo/clsConceptoPago.php");
	$ObjConceptoPago = new clsconceptoPago(5,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$consulta = $ObjConceptoPago->consultarConceptoPagoxTipoDocumento($tipo);

	$cadena="<select name='cboConceptoPago' id='cboConceptoPago'>";
	if(isset($_POST["todos"])) $cadena.="<option value='0'>".$_POST["todos"]."</option>";
	if($consulta->rowCount()>0){
	while($registro=$consulta->fetchObject()){
		//if($registro->idconceptopago!=1 and $registro->idconceptopago!=2 and $registro->idconceptopago!=17 and $registro->idconceptopago!=18){
		$cadena=$cadena."<option value='".$registro->idconceptopago."' ".$seleccionar.">".$registro->descripcion."</option>";
		//}
	}
	}else{
		$cadena=$cadena."<option value='0'>No hay Conceptos de Pago</option>";
	}
	$cadena=$cadena."</select>";
	$cadena=utf8_encode($cadena);
	echo $cadena;
	break;

case "pedidospendientes" :
	$ObjDetalleAlmacen = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$rst = $ObjDetalleAlmacen->consultarPedidosPendientes();
	if($rst->rowCount()>0){
		$cadena = "Pedidos Pendientes";
	}else{	
		$cadena = "ok";
	}
	echo $cadena;
	break;
case "detalleCierreCaja";
	$obj = new clsDetalleAlmacen(3,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
	$registro="<div class='row'>
    			<div class='col s12'>
	      			<ul class='tabs blue' style='overflow-x: hidden;'>
	      				<li class='tab col s6 m3 l3 Tab-activo' id='egresost'><a href='#egresos' onclick=\"tabResumen('egresost')\">DETALLE DE GASTOS</a></li>
	      				<li class='tab col s6 m3 l3 Tab-inactivo' id='ventas1t'><a href='#ventas1' onclick=\"tabResumen('ventas1t')\">VENTAS-COCINA</a></li>
	      				<li class='tab col s6 m3 l3 Tab-inactivo' id='ventas2t'><a href='#ventas2' onclick=\"tabResumen('ventas2t')\" style='display:none;'>VENTAS-BARRA</a></li>
	      			</ul>
	      		</div>
	      		<div id='egresos' class='col s12'>
	      			<table class='striped bordered highlight'>
						<thead>
							<tr>
								<th>#</th>
								<th class='center'>DESCRIPCION</th>
								<th class='center'>MONTO</th>
							</tr>
						</thead>
						<tbody>";
	$rs = $obj->obtenerDataSQL("select T.* from (select * from movimiento union all select * from movimientohoy) T where T.idtipodocumento=10 and T.estado='N' and T.idconceptopago<>2 and T.idmovimiento>".$_POST["idapertura"]." and T.idmovimiento<".$_POST["idcierre"]);
	$total = 0;
    while($dat=$rs->fetchObject()){$c=$c+1;
    	$registro.="<tr>";
    	$registro.="<td>$c</td>";
    	$registro.="<td>$dat->comentario</td>";
    	$registro.="<td class='center'>".number_format($dat->total,2,'.','')."</td>";
    	$registro.="</tr>";
    	$total = $total + $dat->total;
    }
    $registro.="<tfoot>
   					<tr>
   						<th colspan='2' class='center'>TOTAL</th>
   						<th class='center'>".number_format($total,2,'.','')."</th>
   					</tr>
   				</tfoot>
   			</table>
   		</div>";
   	$sql = "select p.descripcion as productos,sum(dma.cantidad) as cantidad,dma.precioventa as preciounitario,round(sum(dma.precioventa*dma.cantidad),2) as preciototal,p.kardex,p.idproducto,c.descripcion as categoria,c.orden,p.idimpresora
    from (select * from movimientohoy union select * from movimiento) as T
    inner join (select * from detallemovimientohoy union select * from detallemovimiento) as D on D.idmovimiento=T.idmovimiento and D.idsucursal=T.idsucursal
    inner join detallemovalmacen dma on dma.idmovimiento=T.idmovimiento and D.iddetallemovalmacen=dma.iddetallemovalmacen and dma.idsucursal=T.idsucursal
    inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
    left join categoria as c on c.idcategoria=p.idcategoria and p.idsucursal=c.idsucursal
    where T.estado='N' and T.idsucursal=".$_SESSION["R_IdSucursal"]." and T.idmovimiento>=".$_POST["idapertura"]." and T.idmovimiento<".$_POST["idcierre"];
    $sql .= " group by p.idimpresora,c.orden,c.descripcion,p.descripcion,p.idcategoria ,dma.precioventa,p.kardex,p.idproducto,c.descripcion order by p.idimpresora,c.orden,p.descripcion,p.kardex asc";
    $rst=$obj->obtenerDataSQL($sql);
    $cocina = 0; $bar = 0;
    while($data=$rst->fetchObject()){
        if(isset($platos[$data->idproducto.'-'.$data->preciounitario])){
            $platos[$data->idproducto.'-'.$data->preciounitario]["cantidad"]=$platos[$data->idproducto.'-'.$data->preciounitario]["cantidad"] + $data->cantidad;
            $platos[$data->idproducto.'-'.$data->preciounitario]["preciototal"] = $platos[$data->idproducto.'-'.$data->preciounitario]["preciototal"] + $data->preciototal;
            $resumen["productos"]="TOTAL";
            $resumen["cantidad"]="";
            $resumen["preciounitario"]="";
            $resumen["preciototal"]=number_format($data->preciototal+$resumen["preciototal"],2,'.','');
            if($data->idimpresora==1){//COCINA
                $cocina = $cocina + $data->preciototal;
            }else{
                $bar = $bar + $data->preciototal;
            }
        }else{
            $platos[$data->idproducto.'-'.$data->preciounitario]=array("productos"=>utf8_decode($data->productos),"cantidad"=>$data->cantidad,"preciounitario"=>$data->preciounitario,"preciototal"=>$data->preciototal,"columna"=>$data->categoria,"idimpresora"=>$data->idimpresora);
            //$resumen["productos"]="TOTAL BARRA + COCINA";
            $resumen["productos"]="TOTAL";
            $resumen["cantidad"]="";
            $resumen["preciounitario"]="";
            $resumen["preciototal"]=number_format($data->preciototal+$resumen["preciototal"],2,'.','');           
            if($data->idimpresora==1){//COCINA
                $cocina = $cocina + $data->preciototal;
            }else{
                $bar = $bar + $data->preciototal;
            }
        }
    }
    $columna="";$idimpresora="";$totalg=0;
   	$registro.="<div id='ventas1' class='col s12'>";
   	foreach($platos as $k=>$v){
   		if($v["idimpresora"]==1){
   			if($categoria!=$v["columna"]){
   				if($categoria!=""){
	   				$registro.="<tfoot>
	   								<tr>
	   									<th>Total</th>
	   									<th class='center'>$total</th>
	   									<th class='center'>".number_format($total2*100/$cocina,2,'.','')."%</th>
	   									<th class='center'>".number_format($total2,2,'.','')."</th>
	   								</tr>
	   							</tfoot>
	   						</table>
	   					<br>";
	   			}
   				$registro.="<table>
	   							<thead>
	   								<tr>
	   									<th>".$v["columna"]."</th>
	   								</tr>
	   								<tr>
	   									<th>PRODUCTO</th>
	   									<th class='center'>CANTIDAD</th>
	   									<th class='center'>P. VENTA</th>
	   									<th class='center'>TOTAL</th>
	   								</tr>
	   							</thead>
	   							<tbody>";
	   			$categoria=$v["columna"];
	   			$total=0;$total2=0;
	   		}
	   		$registro.="<tr>";
	   		$registro.="<td>".$v["productos"]."</td>";
	   		$registro.="<td class='center'>".$v["cantidad"]."</td>";
	   		$registro.="<td class='center'>".$v["preciounitario"]."</td>";
	   		$registro.="<td class='center'>".$v["preciototal"]."</td>";
	   		$registro.="</tr>";
	   		$total=$total+$v["cantidad"];
        	$total2=$total2+$v["preciototal"];
        	$totalg=$totalg+$v["preciototal"];
   		}
   	}
   	$registro.="<tfoot>
					<tr>
						<th>Total</th>
						<th class='center'>$total</th>
						<th class='center'>".number_format($total2*100/$cocina,2,'.','')."%</th>
						<th class='center'>".number_format($total2,2,'.','')."</th>
					</tr>
				</tfoot>
			</table>
		<br>
		<h3>TOTAL : ".number_format($cocina,2,'.','')."</h3>";
   	$registro.="</div>";

    $columna="";$idimpresora="";$totalg=0;$categoria="";$total2=0;
   	$registro.="<div id='ventas2' class='col s12'>";
   	foreach($platos as $k=>$v){
   		if($v["idimpresora"]!=1){
   			if($categoria!=$v["columna"]){
   				if($categoria!=""){
	   				$registro.="<tfoot>
	   								<tr>
	   									<th>Total</th>
	   									<th class='center'>$total</th>
	   									<th class='center'>".number_format($total2*100/($bar>0?$bar:1),2,'.','')."%</th>
	   									<th class='center'>".number_format($total2,2,'.','')."</th>
	   								</tr>
	   							</tfoot>
	   						</table>
	   					<br>";
	   			}
   				$registro.="<table>
	   							<thead>
	   								<tr>
	   									<th>".$v["columna"]."</th>
	   								</tr>
	   								<tr>
	   									<th>PRODUCTO</th>
	   									<th class='center'>CANTIDAD</th>
	   									<th class='center'>P. VENTA</th>
	   									<th class='center'>TOTAL</th>
	   								</tr>
	   							</thead>
	   							<tbody>";
	   			$categoria=$v["columna"];
	   			$total=0;$total2=0;
	   		}
	   		$registro.="<tr>";
	   		$registro.="<td>".$v["productos"]."</td>";
	   		$registro.="<td class='center'>".$v["cantidad"]."</td>";
	   		$registro.="<td class='center'>".$v["preciounitario"]."</td>";
	   		$registro.="<td class='center'>".$v["preciototal"]."</td>";
	   		$registro.="</tr>";
	   		$total=$total+$v["cantidad"];
        	$total2=$total2+$v["preciototal"];
        	$totalg=$totalg+$v["preciototal"];
   		}
   	}
   	$registro.="<tfoot>
					<tr>
						<th>Total</th>
						<th class='center'>$total</th>
						<th class='center'>".number_format($total2*100/($bar>0?$bar:1),2,'.','')."%</th>
						<th class='center'>".number_format($total2,2,'.','')."</th>
					</tr>
				</tfoot>
			</table>
		<br>
		<h3>TOTAL : ".number_format($bar,2,'.','')."</h3>";
   	$registro.="</div>";
    echo $registro."</div>";
	break;
default:
	echo "Error en el Servidor: Operacion no Implementada.";
	exit();
}
?>