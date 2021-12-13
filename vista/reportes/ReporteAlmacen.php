<?php
session_start();
require_once('clsReporteDinamico.php');
require_once('../../modelo/clsMovCaja.php');

$obj = new clsMovcaja(1,$_SESSION['R_IdSucursal'],$_SESSION['R_NombreUsuario'],$_SESSION['R_Clave']);
//$rst = $obj->consultarMovimiento(1,1,'2',1,$_GET["idmovimiento"],3);
$rst=$obj->buscarMovimiento($_GET['idmovimiento'], 3, '');
$dato = $rst->fetchObject();
$pdf=new PDF_Dinamico('P','mm','A4');
$pdf->Open();
$pdf->AddPage();
$pdf->SetXY(10,10);
$pdf->SetFont("Arial",'B',14);
$pdf->Cell(0,5,utf8_decode($_SESSION["R_NombreSucursal"])." - DOC. ALMACEN NRO ".$dato->numero,0,0,"C");
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont("Arial",'B',12);
$pdf->Cell(15,5,"Fecha:",0,0,"L");
$pdf->SetFont("Arial",'',12);
$pdf->Cell(50,5,substr($dato->fecha,0,30),0,0,"L");

$pdf->SetFont("Arial",'B',12);
$pdf->Cell(15,5,"Pers.:",0,0,"L");
$pdf->SetFont("Arial",'',12);
$pdf->Cell(50,5,$dato->persona,0,0,"L");

$pdf->Ln();

$pdf->SetFont("Arial",'B',12);
$pdf->Cell(15,5,"Tipo:",0,0,"L");
$pdf->SetFont("Arial",'',12);
$pdf->Cell(50,5,($dato->idtipodocumento==7?"Ingreso":"Salida"),0,0,"L");

$pdf->SetFont("Arial",'B',12);
$pdf->Cell(15,5,"Resp.:",0,0,"L");
$pdf->SetFont("Arial",'',12);
$pdf->Cell(50,5,$dato->responsable,0,0,"L");
/*$pdf->SetFont("Arial",'B',12);
$pdf->Cell(15,5,"Motivo:",0,0,"L");
$pdf->SetFont("Arial",'',12);
$pdf->Cell(50,5,$dato->motivo,0,0,"L");

$pdf->SetFont("Arial",'B',12);
$pdf->Cell(20,5,"De/Para:",0,0,"L");
$pdf->SetFont("Arial",'',12);
$pdf->Cell(30,5,$dato->envio,0,0,"L");
$pdf->Ln();*/
$pdf->Ln();

$pdf->SetFont("Arial",'B',12);
$pdf->Cell(10,5,"#",1,0,"C");
$pdf->Cell(15,5,"Codigo",1,0,"C");
$pdf->Cell(80,5,"Producto",1,0,"C");
$pdf->Cell(25,5,"Unidad",1,0,"C");
$pdf->Cell(20,5,"Cant.",1,0,"C");
$pdf->Cell(20,5,"P. Venta",1,0,"C");
$pdf->Cell(20,5,"Subtotal",1,0,"C");
$pdf->Ln();

$rst2=$obj->buscarDetalleProducto($_GET['idmovimiento'],"h");
$c=0;$total=0;
while($data=$rst2->fetchObject()){$c=$c+1;
    $pdf->SetFont("Arial",'',12);
    $pdf->Cell(10,5,$c,1,0,"L");
    $pdf->Cell(15,5,$data->codigo,1,0,"L");
    $pdf->Cell(80,5,utf8_decode($data->producto),1,0,"L");
    $pdf->Cell(25,5,$data->unidad,1,0,"C");
    $pdf->Cell(20,5,number_format($data->cantidad,2,'.',''),1,0,"C");
    $pdf->Cell(20,5,number_format($data->precioventa,2,'.',''),1,0,"C");
    $pdf->Cell(20,5,number_format($data->cantidad*$data->precioventa,2,'.',''),1,0,"C");
    $pdf->Ln();
    $total = $total + number_format($data->cantidad*$data->precioventa,2,'.','');
}
$pdf->SetFont("Arial",'B',12);
$pdf->Cell(10,5,"",0,0,"C");
$pdf->Cell(15,5,"",0,0,"C");
$pdf->Cell(80,5,"",0,0,"C");
$pdf->Cell(25,5,"",0,0,"C");
$pdf->Cell(20,5,"",0,0,"C");
$pdf->Cell(20,5,"Total",1,0,"C");
$pdf->Cell(20,5,number_format($total,2,'.',''),1,0,"C");
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont("Arial",'B',12);
$pdf->Cell(20,5,"Coment.:",0,0,"L");
$pdf->SetFont("Arial",'',12);
$pdf->Cell(0,5,utf8_decode($dato->comentario),0,0,"L");
$pdf->Ln();
$pdf->Ln();

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(20,5,"",0,0,"L");
$pdf->Cell(70,5,"VB DESPACHADO",'T',0,"C");
$pdf->Cell(20,5,"",0,0,"L");
$pdf->Cell(70,5,"VB TRANSPORTE",'T',0,"C");
$pdf->Ln();
$pdf->Ln();
/*$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(20,5,"",0,0,"L");
$pdf->Cell(70,5,"VB TRANSPORTE",'T',0,"C");
$pdf->Cell(20,5,"",0,0,"L");
$pdf->Cell(70,5,"VB RECEPCION",'T',0,"C");
$pdf->Ln();*/

$pdf->Output($clase.'.pdf','I');
?>