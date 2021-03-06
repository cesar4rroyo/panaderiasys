<?php
require('clsPdf_ticket.php');

class PDF_AutoPrint extends pdfticket{
    function AutoPrint($dialog=false)
    {
    	//Open the print dialog or start printing immediately on the standard printer
    	$param=($dialog ? 'true' : 'false');
    	$script="print($param);";
    	$this->IncludeJS($script);
    }
    
    function AutoPrintToPrinter($server, $printer, $dialog=false)
    {
    	//Print on a shared printer (requires at least Acrobat 6)
    	$script = "var pp = getPrintParams();";
    	if($dialog)
    		$script .= "pp.interactive = pp.constants.interactionLevel.full;";
    	else
    		$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
    	$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
    	$script .= "print(pp);";
    	$this->IncludeJS($script);
    }
}

$pdf=new PDF_AutoPrint();
$pdf->AddPage();
$pdf->SetFont('Arial','',20);
$pdf->Text(90, 50, 'Prueba!!!');
//Open the print dialog
$pdf->AutoPrintToPrinter("ASISTENTE","EPSON L200 Series (Copiar 1)");
$pdf->Output();
//$pdf->Close();
?>
