<?php
session_start();
date_default_timezone_set("America/Lima");
/* Change to the correct path if you copy this example! */
include_once '../controlador/Algoritmos.php';
include_once '../modelo/mdlSolicitud.php';
require __DIR__ . '/../autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;

$accion = $_GET["accion"];

if($accion=="ImprimirVenta"){
    $mdlSolicitud = new mdlSolicitud();
    $id_solicitud = $_GET["id_solicitud"];
    $objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud);

    //throw new Exception(json_encode($id_solicitud));

    $tipo_documento = $objSolicitud["tipo_documento"];
    $nombre_documento = $objSolicitud["nombre_solicitud"];
    if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
        Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
    }
    $filename_zip=__DIR__ ."/../ficheros/".$nombre_documento."xml";
    $empresa=$_SESSION["empresa"];
    // Variables correspondientes a la factura.
    $RUC = "";       // RUC DE ADQUIRIENTE
    $NombreCliente = "";
    $CodigoDoc = ""; // CODIGO DE ADQUIRIENTE
    $Serie = "";     // SERIE DEL DOCUMENTO
    $Numeracion = "";// NUMERACION DEL DOC
    $NomRazSoc = ""; // Nombre o Razón social.
    $FecEmi = "";    // Fecha de emisión.
    $Moneda = "";
    $Domicilio = $objSolicitud["direccion_cliente"]; // Domicilio.
    $CodHash = "";   // Código Hash.
    $TipoDoc = "";   // Tipo de documento.
    $TotGrav = 0;    // Total gravado.
    $TotIGV = 0;     // Total IGV.
    $TotMonto = 0;   // Total importe. 
    // Variables correspondientes a los productos o servicios de la factura.
    $CodProdServ = array(); // Código.
    $ProdServ = array(); // Descripción.
    $Cant = array(); // Cantidad. 
    $UniMed = array(); // Unidad de medida.
    $Precio = array(); // Precio unitario.
    $Importe = array();  // Importe.
    
    
    // Obteniendo datos del archivo .XML (factura electrónica)======================
    if(!file_exists($filename_zip)){
        throw new Exception("ERROR EN LA DESCOMPRESION DEL ARCHIVO");
    }
    $xml = file_get_contents($filename_zip);
    #== Obteniendo datos del archivo .XML 
        $DOM = new DOMDocument('1.0', 'ISO-8859-1');
        $DOM->preserveWhiteSpace = FALSE;
        $DOM->loadXML($xml);
        ### DATOS DE LA FACTURA ####################################################

        //Obteniendo el Numero del documento
        $DocXML = $DOM->getElementsByTagName('ID');
        foreach($DocXML as $Nodo){
            $Serie = $Nodo->nodeValue;
            break;
        }
        $Serie = explode("-", $Serie);
        $Numeracion = $Serie[1];
        $Serie = $Serie[0];
        
        // Obteniendo Fecha de emisión.
        $DocXML = $DOM->getElementsByTagName('IssueDate');
        foreach($DocXML as $Nodo){
            $FecEmi = $Nodo->nodeValue;
            break;
        }
        
        // Obteniend
        $DocXML = $DOM->getElementsByTagName('DocumentCurrencyCode');
        foreach($DocXML as $Nodo){
            $Moneda = $Nodo->nodeValue;
            break;
        }
        
        // Obteniendo Codigo Hash.
        $DocXML = $DOM->getElementsByTagName('DigestValue');
        foreach($DocXML as $Nodo){
            $CodHash = $Nodo->nodeValue;
            break;
        }    

        // Clave del tipo de documento.
        $DocXML = $DOM->getElementsByTagName('InvoiceTypeCode');
        $i=0;
        foreach($DocXML as $Nodo){
            $TipoDoc = $Nodo->nodeValue; 
            break;
        }    

        // Obteniendo datos ADQUIRIENTE.
        $DocXML = $DOM->getElementsByTagName('AccountingCustomerParty')[0]->getElementsByTagName('ID');
        $i=0;
        foreach($DocXML as $Nodo){
            $RUC = $Nodo->nodeValue; 
            $CodigoDoc = $Nodo->getAttribute("schemeID");
            break;
        }
        
        $DocXML = $DOM->getElementsByTagName('AccountingCustomerParty')[0]->getElementsByTagName('RegistrationName');
        $i=0;
        foreach($DocXML as $Nodo){
            $NombreCliente = $Nodo->nodeValue;
            break;
        }

        ### DATOS DEL PRODUCTO O SERVICIO. #########################################
        $InvoiceLineXML = $DOM->getElementsByTagName('InvoiceLine');
        
        foreach($InvoiceLineXML as $Nodo1){
            
            // Código del producto o servicio.
            $DocXML = $Nodo1->getElementsByTagName('PriceTypeCode');
            $i=0;
            foreach($DocXML as $Nodo){
                $CodProdServ[] = $Nodo->nodeValue;
            }    

            // Descripción del producto o servicio.
            $DocXML = $Nodo1->getElementsByTagName('Description');
            $i=0;
            foreach($DocXML as $Nodo){
                $ProdServ[] = $Nodo->nodeValue;
            }    

            // Cantidad de producto o servicio.
            $DocXML = $Nodo1->getElementsByTagName('InvoicedQuantity');
            $i=0;
            foreach($DocXML as $Nodo){
                $Cant[] = $Nodo->nodeValue;
                $UniMed[] = $Nodo->getAttribute('unitCode');
            }    

            // Precio unitario. 
            $DocXML = $Nodo1->getElementsByTagName('PricingReference');
            $i=0;
            foreach($DocXML as $Nodo){
                $Doc2 = $Nodo->getElementsByTagName('PriceAmount');
                foreach($Doc2 as $Nodo2){
                    $Precio[] = $Nodo2->nodeValue;
                } 
            }           
            // Importe.
            $DocXML = $Nodo1->getElementsByTagName('LineExtensionAmount');
            $i=0;
            foreach($DocXML as $Nodo){
                $Importe[] = $Nodo->nodeValue;
            }    

        }
        
        ### TOTALES DE LA FACTURA ##################################################

        // Total gravado.
        $DocXML = $DOM->getElementsByTagName('PayableAmount');
        $i=0;
        foreach($DocXML as $Nodo){
            if ($i==1){
                $TotGrav = $Nodo->nodeValue;
            }
            $i++;
        }    

        // Total IGV.
        $DocXML = $DOM->getElementsByTagName('TaxAmount');
        $i=0;
        foreach($DocXML as $Nodo){
            $TotIGV = $Nodo->nodeValue; 
        }    

        // Monto total.
        $DocXML = $DOM->getElementsByTagName('PayableAmount');
        $i=0;
        foreach($DocXML as $Nodo){
            $TotMonto = $Nodo->nodeValue; 
        }    
    //FINALIZA DATOS
        
    
    

   // $connector = new WindowsPrintConnector("smb://DESKTOP1VC83HD/GenericTextOnly");
    $connector = new WindowsPrintConnector("CAJA");
    $printer = new Printer($connector);
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    //$printer -> bitImage($tux,Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
    $printer -> text($empresa["nombre_empresa"]);
    $printer -> feed();
    $printer -> text("DE: LAZO VARELA CARMEN TERESA");
    $printer -> feed();
    $printer -> text("AV. MIGUEL GRAU 900 INT. 101");
    $printer -> feed();
    $printer -> text("URB. VILLA EL SALVADO");
    $printer -> feed();
    $printer -> text("LAMBAYEQUE-CHICLAYO-CHICLAYO");
    $printer -> feed();
    $printer -> text("RUC:".$empresa["ruc_empresa"]);
    $printer -> feed();
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    if($tipo_documento=="F"){
        $printer -> text("Factura Electronica: ".$Serie."-".$Numeracion);
        $printer -> feed();
    }else{
        $printer -> text("Boleta Electronica: ".$Serie."-".$Numeracion);
        $printer -> feed();
    }
    $printer -> text("Fecha: ".date_format(date_create($FecEmi), "d/m/Y"));
    $printer -> feed();
    $printer -> text("RUC/DNI: ".$RUC);
    $printer -> feed();
    $printer -> text("Cliente: ".utf8_decode($NombreCliente));
    $printer -> feed();
    $printer -> text("Dir.: ".$Domicilio);
    $printer -> feed();
	$printer -> text("---------------------------------------------"."\n");
    $printer -> text("Cant.  Producto                    Importe");
    $printer -> feed();
    $printer -> text("---------------------------------------------"."\n");
    foreach ($ProdServ as $key => $value) {
        $printer -> text(number_format($Cant[$key],0,'.','')."  ".str_pad($ProdServ[$key],33," ")." ".number_format($Cant[$key]*$Precio[$key],2,'.',' ')."\n");
        $total = $total + $Precio[$key]*$Cant[$key]/1.18;
	}
    $printer -> text("---------------------------------------------"."\n");
    //$printer -> setTextSize(2 , 1,7);
    $printer -> text(str_pad("Op. Gravada:",37," "));
    $printer -> text(number_format($total,2,'.',' ')."\n");
    $printer -> text(str_pad("I.G.V. (18%):",37," "));
    $printer -> text(number_format($total*0.18,2,'.',' ')."\n");
    $printer -> text(str_pad("Op. Inafecta:",37," "));
    $printer -> text(number_format(0,2,'.',' ')."\n");
    $printer -> text(str_pad("Op. Exonerada:",37," "));
    $printer -> text(number_format(0,2,'.',' ')."\n");
    $printer -> text(str_pad("TOTAL S/ ",37," "));
    $printer -> text(number_format($total*1.18,2,'.',' ')."\n");
    include_once '../modelo/NumeroTexto.php';
    $importe_total_venta = number_format($total*1.18,2,'.',' ');
    $numeroTexto = new NumeroTexto($importe_total_venta);
    $decimales = intval(round($importe_total_venta,2)*100);
    $decimales = $decimales - intval(round($importe_total_venta,2))*100;
    $decimales = intval($decimales);
    if($decimales==0){
        $decimales = '00';
    }else{
        if($decimales<10){
            $decimales = '0'.strval($decimales);
        }else{
            $decimales = strval($decimales);
        }
    }
    $son = strtoupper("SON: ".$numeroTexto->convertirLetras($importe_total_venta)).' CON '.$decimales.'/100 SOLES';
    $printer -> text("\n");
    $printer -> text($son."\n");
    $printer -> text("---------------------------------------------"."\n");
    $printer -> text("Hora: ".date("H:i:s")."\n");
    $printer -> text("\n");
    //$printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text(utf8_encode("Representación impresa del Comprobante Electrónico, consulte en https://facturae-garzasoft.com"));
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("        GRACIAS POR SU PREFERENCIA"."\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> feed();
    $printer -> feed();
    $printer -> cut();
    
    /* Close printer */
    $printer -> close();   
    
    echo "Ok";    
}


?>