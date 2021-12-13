<?php
//require_once('fpdf\fpdf.php');
class ticket{
    function imprimir($mesa){
        
        $handle = fopen("TPVM", "w"); // note 1
        /*$mesa= $_GET["mesa"];
        fwrite($handle,chr(27). chr(64));  //->Reinicializa la impresion, esto hay que hacerlo siempre al inicio.
        
        fwrite($handle, chr(27). chr(97). chr(0)); //->Izquierda
        fwrite($handle, chr(27). chr(97). chr(1)); //->Centro
        fwrite($handle, chr(27). chr(97). chr(2)); //->Derecha
        fwrite($handle, chr(27). chr(100). chr(N)); //-> Limpia el buffer, y salta N lineas, poner numero de saltos en la ‘N’, admite un 0.
        fclose($handle); // cierra el fichero PRN
        $salida = shell_exec("lpr PRN"); //lpr->puerto impresora, imprimir archivo PRN
        */
        if(($handle = @ fopen("TPVM","w")) === FALSE){
        die('No se puedo Imprimir, Verifique su conexion con el Terminal');}
        fwrite($handle,chr(27).chr(64));//reinicio //
        fwrite($handle, chr(27). chr(112). chr(48));//ABRIR EL CAJON 
        fwrite($handle,chr(27).chr(100).chr(0));//salto de linea VACIO 
        fwrite($handle,chr(27).chr(33).chr(8));//negrita 
        fwrite($handle,chr(27).chr(97).chr(1));//centrado
        fwrite($handle,"=================================");
        fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
        fwrite($handle,chr(27).chr(32).chr(3));//ESTACIO ENTRE LETRAS
        fwrite($handle,$_SESSION['R_NombreEmpresa']);
        fwrite($handle,chr(27).chr(32).chr(0));//ESTACIO ENTRE LETRAS
        fwrite($handle,chr(27).chr(100).chr(0));//salto de linea VACIO
        fwrite($handle,chr(27).chr(33).chr(8));//negrita
        fwrite($handle, chr(27). chr(97). chr(0)); //->Izquierda
        fwrite($handle,"Descripcion");
        fwrite($handle, chr(27). chr(97). chr(2)); //->Derecha
        fwrite($handle,"Cantidad");
        fwrite($handle,chr(27).chr(100).chr(0));//salto de linea VACIO
        fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
        $cont=1;
        foreach($_SESSION['R_carroPedidoMozo'] as $v){ 
            fwrite($handle, chr(27). chr(97). chr(0)); //->Izquierda
            fwrite($handle,$cont." ".substr($v["producto"],0,10));
            fwrite($handle, chr(27). chr(97). chr(2)); //->Derecha
            fwrite($handle,$v["cantidad"]);
            fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
            $cont++;
        }
        fwrite($handle,"=================================");
        fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
        fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
        fwrite($handle,chr(27).chr(97).chr(1));//centrado
        fwrite($handle,"MESA : ".$mesa);
        fclose($handle);
        echo shell_exec('lpr -S 192.168.1.184 -P Prueba  "TPVM"');        
    }
    function imprimir_printer($data,$mesa){
        $handle = printer_open("Prueba");
        printer_set_option($handle, PRINTER_MODE, "RAW");
        printer_write($handle, $_SESSION['PrintBuffer']);
        printer_write($handle, "HOLAAAAA");
        //print $_SESSION['PrintBuffer'];         //for testing
        printer_close($handle);
    }
    function imprimir_printer2(){
        $printer = "Prueba";
        // $printer = "\\\\PC\\EPSON CX3200";
        if($ph = printer_open($printer))
        {
           echo "Printing...";
           // Get file contents
           $fh = fopen("test.php", "rb");
           $content = fread($fh, filesize("test.php"));
           fclose($fh);
               
           // Set print mode to RAW and send PDF to printer
            printer_set_option($ph, PRINTER_MODE, "RAW");
           
            printer_start_doc($ph, "My Document");
            printer_start_page($ph);
        
            printer_write($ph, $content);
        
            printer_end_page($ph);
            printer_end_doc($ph);
            printer_close($ph);
        }
        else {
            "Couldn't connect...";
        }  
    }
    function imprimirticket($mesa){
         $handle = fopen("TICKET", "w");
         fwrite($handle,chr(27).chr(64));//reinicio //
         fwrite($handle, chr(27). chr(112). chr(48));//ABRIR EL CAJON 
         fwrite($handle,chr(27).chr(100).chr(0));//salto de linea VACIO 
         fwrite($handle,chr(27).chr(33).chr(8));//negrita 
         fwrite($handle,chr(27).chr(97).chr(1));//centrado
         fwrite($handle,"=================================");
         fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
         fwrite($handle,chr(27).chr(32).chr(3));//ESTACIO ENTRE LETRAS
         fwrite($handle,$_SESSION['R_NombreEmpresa']);
         fwrite($handle,chr(27).chr(32).chr(0));//ESTACIO ENTRE LETRAS
         fwrite($handle,chr(27).chr(100).chr(0));//salto de linea VACIO
         fwrite($handle,chr(27).chr(33).chr(8));//negrita
         fwrite($handle, chr(27). chr(97). chr(0)); //->Izquierda
         fwrite($handle,"Descripcion");
         fwrite($handle, chr(27). chr(97). chr(2)); //->Derecha
         fwrite($handle,"Cantidad");
         fwrite($handle,chr(27).chr(100).chr(0));//salto de linea VACIO
         fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
         $cont=1;
         foreach($_SESSION['R_carroPedidoMozo'] as $v){
              fwrite($handle, chr(27). chr(97). chr(0)); //->Izquierda
              fwrite($handle,$cont." ".substr($v["producto"],0,10));
              fwrite($handle, chr(27). chr(97). chr(2)); //->Derecha
              fwrite($handle,$v["cantidad"]);
              fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
              $cont++;
         }
         fwrite($handle,"=================================");
         fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
         fwrite($handle,chr(27).chr(100).chr(1));//salto de linea
         fwrite($handle,chr(27).chr(97).chr(1));//centrado
         fwrite($handle,"MESA : ".$mesa);
         
         //$printer = "EPSON L200 Series (Copiar 1)";
         $printer = "Prueba";
        //$printer = "\\\\ASISTENTE\\EPSON L200 Series (Copiar 1)";
        // $printer = "\\\\PC\\EPSON CX3200";
         if($ph = printer_open($printer)){
           echo "Printing...";
           // Get file contents
           $content = fread($handle, filesize("TICKET"));
           fclose($handle);
               
           // Set print mode to RAW and send PDF to printer
            printer_set_option($ph, PRINTER_MODE, "RAW");
           
            printer_start_doc($ph);
            printer_start_page($ph);
        
            printer_write($ph, $content);
        
            printer_end_page($ph);
            printer_end_doc($ph);
            printer_close($ph);
        }
        else {
            "Couldn't connect...";
        }
    } 
    
}
//$obj = new ticket;
//$obj->imprimir("hola","01");
//$obj->imprimir_printer("hola","01");
//$obj->imprimir_printer2();
//$obj->imprimirticket("01");
 /*
$handle = fopen("PRN", "w");
fwrite($handle,chr(27). chr(64));fwrite($handle, chr(27). chr(97). chr(1));//centrado
fwrite($handle,"Pedido de la Mesa ".$mesa);
fwrite($handle, chr(27). chr(100). chr(1));//salto de linea
fwrite($handle, chr(27). chr(97). chr(0)); //izquierda
fwrite($handle, "texto");
fclose($handle); // cierra el fichero PRN
$salida = shell_exec("lpr PRN"); //lpr->puerto impresora, imprimir archivo PRN

/*
$dimensiones = array(7.5,0);
$pdf=new FPDF("P","cm",$dimensiones);
$pdf->AddPage('L');
$pdf->SetFont('Arial','B',16);
$pdf->Cell(290,8,'LISTA DE PRODUCTOS',0,1,'C');
$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,8,"CODIGO",1,0,'C');
$pdf->Cell(65,8,"DESCRIPCION",1,0,'C');
$pdf->Cell(25,8,"CATEGORIA",1,0,'C');
$pdf->Cell(20,8,"MARCA",1,0,'C');
$pdf->Cell(27,8,"UNIDAD BASE",1,0,'C');
//$pdf->Cell(15,8,"PESO",1,0,'C');
//$pdf->Cell(25,8,"MEDIDA",1,0,'C');
$pdf->Cell(35,8,"STOCK SEGURIDAD",1,0,'C');
$pdf->Cell(25,8,"ARMARIO",1,0,'C');
$pdf->Cell(19,8,"COLUMNA",1,0,'C');
$pdf->Cell(10,8,"FILA",1,0,'C');
$pdf->Cell(15,8,"KARDEX",1,0,'C');
$pdf->SetFont('Arial','',10);
$pdf->Ln();
$rst = $objProducto->buscar(NULL,$desc,$categoria,$marca);
while($dato=$rst->fetchObject()){

  $pdf->Cell(30,8,$dato->codigo,1,0,'C');
  $pdf->Cell(65,8,substr($dato->descripcion,0,32),1,0,'C');
  $pdf->Cell(25,8,substr($dato->categoria,0,10),1,0,'C');
  $pdf->Cell(20,8,substr($dato->marca,0,8),1,0,'C');
  $pdf->Cell(27,8,substr($dato->unidadbase,0,11),1,0,'C');
  //$pdf->Cell(15,8,$dato->peso,1,0,'C');
  //$pdf->Cell(25,8,substr($dato->medidapeso,0,10),1,0,'C');
  $pdf->Cell(35,8,$dato->stockseguridad,1,0,'C');
  $pdf->Cell(25,8,$dato->armario,1,0,'C');
  $pdf->Cell(19,8,$dato->columna,1,0,'C');
  $pdf->Cell(10,8,$dato->fila,1,0,'C');
  $pdf->Cell(15,8,$dato->kardex,1,0,'C');
  $pdf->Ln();
}

$pdf->SetAutoPageBreak(auto,2); 
$pdf->Output();
*/
?>