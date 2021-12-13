<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsMovCaja.php');

$obj = new clsMovcaja(1,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
$pdf=new PDF_Dinamico('P','mm','A4');
$pdf->Open();
$rw = $obj->obtenerDataSQL("select T.* from (select * from movimiento union all select * from movimientohoy) T where T.idconceptopago=1 and T.fecha>='".$_GET["fechainicio"]."' and T.fecha<='".$_GET["fechafin"]."' order by T.idmovimiento desc");
while($apertura=$rw->fetchObject()){
    $pdf->AddPage();
    $pdf->SetXY(10,10);
    $pdf->Cell(0,4,"CUADRE DE CAJA",0,0,"C");
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(18,4,"TURNO:",0,0,"L");
    $cierre = $obj->obtenerDataSQL("select T.* from (select * from movimiento union all select * from movimientohoy) T where T.idconceptopago=2 and T.idmovimiento>".$apertura->idmovimiento." order by T.idmovimiento asc limit 1")->fetchObject();

    $apert = $obj->obtenerDataSQL("SELECT sum(T.total) FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 1 AND T.estado='N' and T.idmovimiento=".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
                 ->fetchObject()->sum;
    $efectivo = $obj->obtenerDataSQL("SELECT CASE WHEN sum(T.totalpagado) IS NULL THEN 0 ELSE sum(T.totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='E' OR T.modopago='A') and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
                 ->fetchObject()->sum;
    $visa_modoT = $obj->obtenerDataSQL("SELECT CASE WHEN sum(T.total-T.totalpagado) IS NULL THEN 0 ELSE sum(T.total-T.totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='T') AND T.idtipotarjeta = 1 and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
                      ->fetchObject()->sum;
    $visa_modoA = $obj->obtenerDataSQL("SELECT CASE WHEN sum((substr(T.montotarjeta,position('1@' in T.montotarjeta)+2,position('|' in T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(T.montotarjeta,position('1@' in T.montotarjeta)+2,position('|' in T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='A') and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
            		  ->fetchObject()->sum;
    $visa = $visa_modoT + $visa_modoA;
    $mastercard_modoT = $obj->obtenerDataSQL("SELECT CASE WHEN sum(T.total) IS NULL THEN 0 ELSE sum(T.total) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='T') AND T.idtipotarjeta = 2 and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
            				->fetchObject()->sum;
    $mastercard_modoA = $obj->obtenerDataSQL("SELECT CASE WHEN sum((substr(T.montotarjeta,position('2@' in T.montotarjeta)+2,length(T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(T.montotarjeta,position('2@' in T.montotarjeta)+2,length(T.montotarjeta)-2-position('1@' in T.montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idconceptopago = 3 AND T.estado='N' AND (T.modopago='A') and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
            				->fetchObject()->sum;
    $mastercard = $mastercard_modoT + $mastercard_modoA;
    $ingresos = $obj->obtenerDataSQL("SELECT sum(T.total) FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idtipodocumento = 9 AND T.idconceptopago NOT IN (1,3) AND T.estado='N' and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
                    ->fetchObject()->sum;
    $egresos = $obj->obtenerDataSQL("SELECT sum(T.total) FROM (select * from movimientohoy union all select * from movimiento) T WHERE T.idtipodocumento = 10 and T.idconceptopago<>2 AND T.estado = 'N' and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento)
                   ->fetchObject()->sum;
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(15,4,date("d/m/Y H:i:s",strtotime($apertura->fecha))." - ".date("d/m/Y H:i:s",strtotime($cierre->fecha)),0,0,"L");
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(70,5,"VENTAS",1,0,"C");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->Cell(70,5,"CAJA",1,0,"C");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Efectivo",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($efectivo,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Apertura(+)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($apert,2,'.',''),1,0,"R");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Visa",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($visa,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Ventas(+)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($efectivo+$visa+$mastercard,2,'.',''),1,0,"R");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Master",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($mastercard,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Ingresos(+)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($ingresos,2,'.',''),1,0,"R");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"TOTAL",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($master+$visa+$efectivo,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Egresos(-)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($egresos,2,'.',''),1,0,"R");
    $pdf->Ln();
    $pdf->Cell(70,5,"",0,0,"L");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"TOTAL",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($apert+$efectivo+$visa+$mastercard+$ingresos-$egresos,2,'.',''),1,0,"R");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(70,5,"ARQUEO",1,0,"C");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Apertura(+)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($apert,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(70,5,"EFECTIVO",1,0,"C");
    $pdf->SetFont("Arial",'',10);
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Ventas Efect.(+)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($efectivo,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"DOLARES U$       x S/",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Otros Ing.(+)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($ingresos,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"SOLES - BILLETES      S/ 200",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"Egresos(-)",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($egresos,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 100",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(35,5,"TOTAL",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(35,5,number_format($apert+$efectivo+$ingresos-$egresos,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 50",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $pdf->Cell(105,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 20",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(70,5,"FACTURAS (A)",1,0,"C");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 10",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $ifactura = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=5 and idmovimiento>".$apertura->idmovimiento." and estado='N' and situacion='N' order by idmovimiento asc")->fetchObject()->numero;
    $ffactura = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=5 and idmovimiento<".$cierre->idmovimiento." and estado='N' and situacion='N' order by idmovimiento desc")->fetchObject()->numero;
    $facturas = (substr($ifactura,5,8)+0) . ' - ' . (substr($ffactura,5,8)+0);
    $tfacturas = $obj->obtenerDataSQL("select sum(total) as total from movimiento where idmovimiento>".$apertura->idmovimiento." and idmovimiento<".$cierre->idmovimiento." and idtipodocumento=5 and estado='N' and situacion='N'")->fetchObject()->total;

    $pdf->SetFont("Arial",'',9.5);
    $pdf->Cell(55,5,$facturas,1,0,"L");
    $pdf->Cell(15,5,$tfacturas,1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"SOLES - MONEDAS         S/ 5",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(70,5,"BOLETAS (B)",1,0,"C");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 2",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $iboleta = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=4 and idmovimiento>".$apertura->idmovimiento." and estado='N' and situacion='N' order by idmovimiento asc")->fetchObject()->numero;
    $fboleta = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=4 and idmovimiento<".$cierre->idmovimiento." and estado='N' and situacion='N' order by idmovimiento desc")->fetchObject()->numero;
    $boletas = (substr($iboleta,5,8)+0) . ' - ' . (substr($fboleta,5,8)+0);
    $tboletas = $obj->obtenerDataSQL("select sum(total) as total from movimiento where idmovimiento>".$apertura->idmovimiento." and idmovimiento<".$cierre->idmovimiento." and idtipodocumento=4 and estado='N' and situacion='N'")->fetchObject()->total;

    $pdf->SetFont("Arial",'',9.5);
    $pdf->Cell(55,5,$boletas,1,0,"L");
    $pdf->Cell(15,5,$tboletas,1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 1",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $pdf->SetFont("Arial",'B',9.5);
    $pdf->Cell(55,5,"TOTAL (A+B)",1,0,"L");
    $pdf->Cell(15,5,number_format($tfacturas + $tboletas,2,'.',''),1,0,"R");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 0.50",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(70,5,"COMPROBANTES NULOS",1,0,"C");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 0.20",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $rs = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=5 and idmovimiento>".$apertura->idmovimiento." and idmovimiento<".$cierre->idmovimiento." and estado='I'");
    $afactura = "";
    if($rs->rowCount()>0){
        while($value = $rs->fetchObject()) {
            $afactura = $afactura.(substr($value->numero,5,8)+0) . ' - ';
        }
        $afactura=substr($afactura, 0, strlen($afactura)-2);
    }

    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(25,5,"FACTURAS:",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(45,5,$afactura,1,0,"L");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"S/ 0.10",1,0,"R");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $rs = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=4 and idmovimiento>".$apertura->idmovimiento." and idmovimiento<".$cierre->idmovimiento." and estado='I'");
    $aboleta = "";
    if($rs->rowCount()>0){
        while($value = $rs->fetchObject()) {
            $aboleta = $aboleta.(substr($value->numero,5,8)+0) . ' - ';
        }
        $aboleta=substr($aboleta, 0, strlen($aboleta)-2);
    }

    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(25,5,"BOLETAS:",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(45,5,$aboleta,1,0,"L");
    $pdf->Cell(35,5,"",0,0,"L");
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(50,5,"TOTAL EFECTIVO",1,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(20,5,"",1,0,"L");
    $pdf->Ln();

    $pdf->Ln();

    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(70,5,"COMANDAS",1,0,"C");
    $pdf->Ln();

    $icomanda = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=11 and idmovimiento>".$apertura->idmovimiento." /*and estado='N' and situacion='P'*/ order by idmovimiento asc")->fetchObject()->numero;
    $fcomanda = $obj->obtenerDataSQL("select * from movimiento where idtipodocumento=11 and idmovimiento<".$cierre->idmovimiento." /*and estado='N' and situacion='P'*/ order by idmovimiento desc")->fetchObject()->numero;
    $tcomanda = $obj->obtenerDataSQL("select sum(total) as total from movimiento where idmovimiento>".$apertura->idmovimiento." and idmovimiento<".$cierre->idmovimiento." and idtipodocumento=11 and estado='N' and situacion='P'")->fetchObject()->total;

    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(23,5,"INICIO",1,0,"C");
    $pdf->Cell(23,5,"FIN",1,0,"C");
    $pdf->Cell(24,5,"VALOR",1,0,"C");
    $pdf->Ln();



    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(23,5,$icomanda,1,0,"L");
    $pdf->Cell(23,5,$fcomanda,1,0,"L");
    $pdf->Cell(24,5,$tcomanda,1,0,"R");
    $pdf->Ln();

    $pdf->AddPage();
    $pdf->SetXY(10,10);
    $pdf->SetFont("courier",'BU',22);
    $pdf->Cell(0,4,"DETALLE DE GASTOS",0,0,"C");
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(18,4,"TURNO:",0,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(15,4,date("d/m/Y H:i:s",strtotime($apertura->fecha))." - ".date("d/m/Y H:i:s",strtotime($cierre->fecha)),0,0,"L");
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(8,5,"#",1,0,"C");
    $pdf->Cell(150,5,utf8_decode("Descripción"),1,0,"C");
    $pdf->Cell(20,5,"Monto",1,0,"C");
    $pdf->Ln();$total=0;
    $rs = $obj->obtenerDataSQL("select T.* from (select * from movimiento union all select * from movimientohoy) T where T.idtipodocumento=10 and T.estado='N' and T.idconceptopago<>2 and T.idmovimiento>".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento);
    while($dat=$rs->fetchObject()){$c=$c+1;
    	$pdf->SetFont("Arial",'',10);
    	$pdf->Cell(8,5,$c,1,0,"C");
    	$pdf->Cell(150,5,utf8_decode($dat->comentario),1,0,"L");
    	$pdf->Cell(20,5,number_format($dat->total,2,'.',''),1,0,"R");
    	$pdf->Ln();
    	$total=$total + $dat->total;
    }
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(158,5,utf8_decode("Total"),1,0,"C");
    $pdf->Cell(20,5,number_format($total,2,'.',''),1,0,"R");
    $pdf->Ln();
    //$pdf->Output('CuadreCaja.pdf','I');


    //-----------------------------------------
    require_once('../../modelo/clsProducto.php');
    //VALORES DE LA CLASE, VALIDACION SI AY ALGUN ERROR O ESTA VACIA LA CONSULTA A REPORTAR
    //Nombre y Codigo de la Clase a Ejecutar
    $clase = "MovCaja";
    $id_clase = "53";
    $ocultarcampos='';
    $objGrilla = new clsProducto($id_clase,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);

    //Inicio Obtiene Campos a mostrar
    //CREAMOS LA CABECERA A MOTRAR EN LA TABLA
    $CABECERA = array();
    $CABECERA3 = array();
    $CAMPOS = array();
    $CAMPOS3 = array();
    $dataCampos = array("Productos"=>"Productos","Cantidad"=>"Cantidad","preciounitario"=>"preciounitario","preciototal"=>"preciototal");
    $w = array("0"=>"110","1"=>"20","2"=>"25","3"=>"25");
    $a = array("0"=>"L","1"=>"C","2"=>"C","3"=>"C");
    $CAMPOS[] = array("descripcion"=>"Productos");
    $CABECERA[]= "Productos";
    $CAMPOS[] = array("descripcion"=>"Cant.");
    $CABECERA[]= "Cantidad";
    $CAMPOS[] = array("descripcion"=>"preciounitario");
    $CABECERA[]= "P. Venta";
    $CAMPOS[] = array("descripcion"=>"preciototal");
    $CABECERA[]= "Subtotal";

    //Inicio Ejecutando la consulta
    $sql = "select p.descripcion as productos,sum(dma.cantidad) as cantidad,dma.precioventa as preciounitario,round(sum(dma.precioventa*dma.cantidad),2) as preciototal,p.kardex,p.idproducto,c.descripcion as categoria,c.orden,1 as idimpresora
    from (select * from movimientohoy union select * from movimiento) as T
    inner join (select * from detallemovimientohoy union select * from detallemovimiento) as D on D.idmovimiento=T.idmovimiento and D.idsucursal=T.idsucursal
    inner join detallemovalmacen dma on dma.idmovimiento=T.idmovimiento and D.iddetallemovalmacen=dma.iddetallemovalmacen and dma.idsucursal=T.idsucursal
    inner join producto as p on p.idproducto=dma.idproducto and p.idsucursal=dma.idsucursal
    left join categoria as c on c.idcategoria=p.idcategoria and p.idsucursal=c.idsucursal
    where T.estado='N' and T.idsucursal=".$_SESSION["R_IdSucursal"]." and T.idmovimiento>=".$apertura->idmovimiento." and T.idmovimiento<".$cierre->idmovimiento;
    $sql .= " group by p.idimpresora,c.orden,c.descripcion,p.descripcion,p.idcategoria ,dma.precioventa,p.kardex,p.idproducto,c.descripcion order by c.orden,p.descripcion,p.kardex asc";
    $rst=$objGrilla->obtenerDataSQL($sql);
    if(is_string($rst)){
    	echo "Error al ejecutar consulta";
    	exit();
    }

    // UNA VES VALIDADO LOS DATOS--> CREAMOS EL PDF  DE LA CLASE clsReporteDinamico
    /*$pdf=new PDF_Dinamico('P','mm','A4');
    $pdf->Open();*/
    $title='PRODUCTOS VENDIDOS - COCINA';
    //Primera página
    $pdf->AddPage();
    //Cantidad de Paginas Existentes LO LLAMA EN EL FOOTER ---> {nb}
    $pdf->AliasNbPages();
    //LLENAMOS LAS VARIABLES
    $pdf->SetTamLetraGrilla(9);
    $pdf->SetTamLetraCabecera(10);
    //LLenar Anchos de Columna
    $pdf->SetWidths($w,'P');
    //LLenar Alineacion de Columna
    $pdf->SetAligns($a);
    $pdf->SetFont("Arial",'B',10);
    $pdf->Cell(18,4,"TURNO:",0,0,"L");
    $pdf->SetFont("Arial",'',10);
    $pdf->Cell(0,4,date("d/m/Y H:i:s",strtotime($apertura->fecha))." - ".date("d/m/Y H:i:s",strtotime($cierre->fecha)),0,1,'L',false);
    $pdf->Ln();
    //LLenar Cabecera
    //$pdf->SetCabecera_tabla($CABECERA);
    //CREAMOS LA TABLA
    $pdf->LlenarTabla_Cabecera();
    //$pdf->LlenarTabla_Datos($rst2,$CAMPOS);
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
    $fill=true;$columna="";$idimpresora="";$totalg=0;
    foreach($platos as $k=>$v){
        if($columna!=$v["columna"]){
            if($columna!=""){
                $pdf->SetFont("courier",'B',10);
                $resumen1=array();
                $resumen1["productos"]="Total";
                $resumen1["cantidad"]=number_format($total,0,'.','');
                if($v["idimpresora"]=="1"){
                    $resumen1["preciounitario"]=number_format($total2*100/($cocina==0?1:$cocina),2,'.','')."%";
                }else{
                    $resumen1["preciounitario"]=number_format($total2*100/($bar==0?1:$bar),2,'.','')."%";
                }
                $resumen1["preciototal"]=number_format($total2,2,'.','');
                $resumenx = array_values($resumen1);
                $pdf->Row($resumenx,$fill,10,true,true,true,'BTR');
                $pdf->Ln();
                $fill=!$fill;//para manejo de colores
            }
            if($idimpresora!=$v["idimpresora"]){
    			$pdf->SetFont("courier",'B',10);
    	        $resumen1=array();
    	        if($v["idimpresora"]=="1"){
    	        	
    	        }else{
    	        	$pdf->SetFont("courier",'B',10);
    				$resumen1=array();
    	            $resumen1["productos"]="TOTAL COCINA";
    	            $resumen1["cantidad"]="";
    	            $resumen1["preciounitario"]="";
    	            $resumen1["preciototal"]="S/" .number_format($totalg,2,'.','');
    	            $resumenx = array_values($resumen1);
    	            $pdf->Row($resumenx,$fill,10,true,false);
    	            $pdf->Ln();
    	            $totalg=0;
                    $title='PRODUCTOS VENDIDOS - BAR';
                    //$pdf->SetCabecera_tabla($CABECERA);
    	        	$pdf->AddPage();
    	        }
    	        $idimpresora=$v["idimpresora"];
    		}
            $pdf->SetFont("courier",'B',10);
            $resumen1=array();
            $resumen1["productos"]=$v["columna"];
            $resumenx = array_values($resumen1);
            $pdf->Row($resumenx,$fill,10,true,true);
            $fill=!$fill;//para manejo de colores
            $pdf->SetFont("courier",'B',10);
            $resumen1=array();
            $resumen1["productos"]="Producto";
            $resumen1["cantidad"]="Cantidad";
            $resumen1["preciounitario"]="P. Venta";
            $resumen1["preciototal"]="Total";
            $resumenx = array_values($resumen1);
            $pdf->Row($resumenx,$fill,10,true,true,true,1);
            $fill=!$fill;//para manejo de colores
            $columna=$v["columna"];
            $total=0;$total2=0;
        }
        $total=$total+$v["cantidad"];
        $total2=$total2+$v["preciototal"];
        $totalg=$totalg+$v["preciototal"];
        $v1["productos"]=$v["productos"];
        $v1["cantidad"]=round($v["cantidad"],0);
        $v1["preciounitario"]=$v["preciounitario"];
        $v1["preciototal"]=$v["preciototal"];
        $venta = array_values($v1);
        $pdf->Row($venta,$fill,10,true,false,true,1);
        $fill=!$fill;//para manejo de colores    
    }
    $pdf->SetFont("courier",'B',10);
    $resumen1=array();
    $resumen1["productos"]="Total";
    $resumen1["cantidad"]=number_format($total,0,'.','');
    $resumen1["preciounitario"]=number_format($total2*100/($bar==0?1:$bar),2,'.','')."%";
    $resumen1["preciototal"]=number_format($total2,2,'.','');
    $resumenx = array_values($resumen1);
    $pdf->Row($resumenx,$fill,10,true,true,true,'BTR');
    $pdf->Ln();
    $fill=!$fill;//para manejo de colores


    $resumen1=array();
    $resumen1["productos"]="TOTAL";
    $resumen1["cantidad"]="";
    $resumen1["preciounitario"]="";
    $resumen1["preciototal"]="S/ ".number_format($totalg,2,'.','');
    $resumenx = array_values($resumen1);
    $pdf->Row($resumenx,$fill,10,false,false);
    $pdf->Ln();

    /*$resumen["preciototal"] = "S/ ".$resumen["preciototal"];
    $resumen = array_values($resumen);
    $pdf->Row($resumen,$fill,10,false,false);*/

    /*$pdf->Ln();
    $pdf->SetFont("courier",'B',9);
    $pdf->Cell(0,10,'PRODUCTOS ELIMINADOS',0,0,'L',0);
    $pdf->Ln();
    $pdf->Cell(12,6,'CANT.',1,0,'C',0);
    $pdf->Cell(55,6,'PRODUCTO',1,0,'C',0);
    $pdf->Cell(18,6,'P. VENT.',1,0,'C',0);
    $pdf->Cell(18,6,'MESA',1,0,'C',0);
    $pdf->Cell(15,6,'MESERO',1,0,'C',0);
    $pdf->Cell(16,6,'HORA',1,0,'C',0);
    $pdf->Cell(60,6,'COMENTARIO',1,0,'C',0);
    $pdf->Ln();
    $sql = "SELECT pd.descripcion as producto,ct.descripcion as categoria, dme.cantidad, dme.precioventa,"
            . " dme.comentario, ms.numero as mesa, us.nombreusuario, dme.fecha, mv.numero as comanda, mv.idmovimiento"
            . " FROM detallemovalmacen_eliminado dme LEFT JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) mv ON mv.idmovimiento=dme.idmovimiento"
            . " LEFT JOIN producto pd ON dme.idproducto=pd.idproducto LEFT JOIN categoria ct ON pd.idcategoria = ct.idcategoria"
            . " LEFT JOIN mesa ms ON ms.idmesa = mv.idmesa LEFT JOIN usuario us ON us.idusuario = mv.idusuario WHERE ms.idsucursal = ".$_SESSION['R_IdSucursal'];
    $fechaInicio = $apertura->fecha;
    $fechaFin = $cierre->fecha;
    if(!empty($fechaInicio)){
        $sql.=" AND cast(dme.fecha as date) >= '$fechaInicio'";
    }
    if(!empty($fechaFin)){
        $sql.=" AND cast(dme.fecha as date) <= '$fechaFin'";
    }
    $sql.=" ORDER BY dme.fecha ASC";//print_r($sql);
    $rs = $obj->obtenerDataSQL($sql);
    while($dat=$rs->fetchObject()){
        $pdf->SetFont("courier",'',8);
        $pdf->Cell(12,6,$dat->cantidad,1,0,'C',0);
        $pdf->Cell(55,6,$dat->producto,1,0,'L',0);
        $pdf->Cell(18,6,$dat->precioventa,1,0,'C',0);
        $pdf->Cell(18,6,$dat->mesa,1,0,'C',0);
        $pdf->Cell(15,6,$dat->nombreusuario,1,0,'C',0);
        $pdf->Cell(16,6,substr($dat->fecha,10,9),1,0,'C',0);
        $x=$pdf->GetX();
        $y=$pdf->GetY();
        $pdf->Multicell(60,6,utf8_decode($dat->comentario),0,'L',0);
        $pdf->SetXY($x,$y);
        $pdf->Cell(60,6,'',1,0,'L',0);
        $pdf->Ln();
    }*/
    /*$sql = "SELECT mv.*, ms.numero as mesa, us.nombreusuario  FROM (SELECT * FROM movimiento 
    UNION SELECT * FROM movimientohoy) mv 
    LEFT JOIN mesa ms ON ms.idmesa = mv.idmesa LEFT JOIN usuario us ON us.idusuario = mv.idusuario 
    WHERE TRUE AND mv.idtipomovimiento = 5 AND mv.estado = 'A' AND mv.comentario not like '%Pedido por division de cuenta con referencia a la mesa%' AND ms.idsucursal = ".$_SESSION['R_IdSucursal'];
    if(!empty($fechaInicio)){
        $sql.=" AND mv.fecha >= '$fechaInicio'";
    }
    if(!empty($fechaFin)){
        $sql.=" AND mv.fecha <= '$fechaFin'";
    }
    $sql.=" ORDER BY mv.fecha ASC";
    //echo($sql);
    $rs = $obj->obtenerDataSQL($sql);
    while($dat=$rs->fetchObject()){
        $pdf->SetFont("courier",'',8);
        $detalle=$obj->buscarDetalleProducto2($dat->idmovimiento);
        while($dat1=$detalle->fetchObject()){
            $pdf->Cell(12,6,$dat1->cantidad,1,0,'C',0);
            $pdf->Cell(55,6,$dat1->producto,1,0,'L',0);
            $pdf->Cell(18,6,$dat1->precioventa,1,0,'C',0);
            $pdf->Cell(18,6,$dat->mesa,1,0,'C',0);
            $pdf->Cell(15,6,$dat->nombreusuario,1,0,'C',0);
            $pdf->Cell(16,6,substr($dat->fecha,10,9),1,0,'C',0);
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->Multicell(60,6,utf8_decode($dat->comentario),0,'L',0);
            $pdf->SetXY($x,$y);
            $pdf->Cell(60,6,'',1,0,'L',0);
            $pdf->Ln();
        }
    }*/

    $pdf->Ln();
    $pdf->SetFont("courier",'B',9);
    $pdf->Cell(0,10,'PRODUCTOS CORTESIA',0,0,'L',0);
    $pdf->Ln();
    $pdf->Cell(12,6,'CANT.',1,0,'C',0);
    $pdf->Cell(55,6,'PRODUCTO',1,0,'C',0);
    $pdf->Cell(18,6,'P. VENT.',1,0,'C',0);
    $pdf->Cell(18,6,'MESA',1,0,'C',0);
    $pdf->Cell(15,6,'MESERO',1,0,'C',0);
    $pdf->Cell(16,6,'HORA',1,0,'C',0);
    //$pdf->Cell(60,6,'COMENTARIO',1,0,'C',0);
    $pdf->Ln();
    $sql = "SELECT pd.descripcion as producto,ct.descripcion as categoria, dma.cantidad, dma.precioventa,"
        . " mv.comentario, ms.numero as mesa, us.nombreusuario, mv.fecha, mv.numero as comanda, mv.idmovimiento"
        . " FROM movimientoproductocortesia dme LEFT JOIN (SELECT * FROM movimiento UNION SELECT * FROM movimientohoy) mv ON mv.idmovimiento=dme.idmovimiento"
        . " LEFT JOIN detallemovalmacen dma ON dme.idproducto=dma.idproducto and mv.idsucursal=dma.idsucursal and dma.idmovimiento = dme.idmovimiento"
        . " LEFT JOIN producto pd ON dme.idproducto=pd.idproducto LEFT JOIN categoria ct ON pd.idcategoria = ct.idcategoria"
        . " LEFT JOIN mesa ms ON ms.idmesa = mv.idmesa LEFT JOIN usuario us ON us.idusuario = mv.idusuario WHERE ms.idsucursal = ".$_SESSION['R_IdSucursal'];
    if(!empty($fechaInicio)){
        $sql.=" AND cast(mv.fecha as date) >= '$fechaInicio'";
    }
    if(!empty($fechaFin)){
        $sql.=" AND cast(mv.fecha as date) <= '$fechaFin'";
    }
    $sql.=" ORDER BY mv.fecha ASC";
    $rs = $obj->obtenerDataSQL($sql);
    while($dat=$rs->fetchObject()){
        $pdf->SetFont("courier",'',8);
        $pdf->Cell(12,6,$dat->cantidad,1,0,'C',0);
        $pdf->Cell(55,6,$dat->producto,1,0,'L',0);
        $pdf->Cell(18,6,$dat->precioventa,1,0,'C',0);
        $pdf->Cell(18,6,$dat->mesa,1,0,'C',0);
        $pdf->Cell(15,6,$dat->nombreusuario,1,0,'C',0);
        $pdf->Cell(16,6,substr($dat->fecha,10,9),1,0,'C',0);
        /*$x=$pdf->GetX();
        $y=$pdf->GetY();
        $pdf->Multicell(60,6,utf8_decode($dat->comentario),0,'L',0);
        $pdf->SetXY($x,$y);
        $pdf->Cell(60,6,'',1,0,'L',0);*/
        $pdf->Ln();
    }
}
$pdf->Output($clase.'.pdf','I');
?>