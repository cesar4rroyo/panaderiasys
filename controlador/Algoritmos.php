<?php
include __DIR__ . "/../vendor/autoload.php";
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Anouar\Fpdf\Fpdf;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Algoritmos {
    
    public static function ValidarUsuario($usuario,$tipo_usuario=null){
        if(empty($usuario)){
            return false;;
        }
        if(strlen(trim($usuario["user_usuario"]))==0 && strlen(trim($usuario["pass_usuario"]))==0){
            return false;
        }
        if(empty($usuario["persona"])){
            return false;
        }
        if(!empty($tipo_usuario)){
            if($usuario["tipo_usuario"]!=$tipo_usuario){
                return false;
            }
        }
        return true;
    }
    
    public static function FirmaDigital($filename_xml,$uriSignature,$nombre_certificado,$llave_certificado) {
        $domDocument = new DOMDocument();
        $domDocument->load($filename_xml);
        $ReferenceNodeName = 'ExtensionContent';
        $nombre_certificado = __DIR__ ."/../certificados/".$nombre_certificado;
        $info_cert = array();
        if (!$almacen_cert = file_get_contents($nombre_certificado)) {
            throw new Exception("NO SE ENCUENTRA EL FICHERO DEL CERTIFICADO CON EL NOMBRE: ".$nombre_certificado);
        }
        if (!openssl_pkcs12_read($almacen_cert, $info_cert, $llave_certificado)) {
            throw new Exception("NO SE PUEDE LEER EL CERTIFICADO CON LA CONTRASEÑA INDICADA");
        }
        $certificate = $info_cert["cert"];
        $privateKey = $info_cert["pkey"];
        
        $objSign = new RobRichards\XMLSecLibs\XMLSecurityDSig($uriSignature);
        $objSign->setCanonicalMethod(RobRichards\XMLSecLibs\XMLSecurityDSig::C14N);
        $objSign->addReference(
            $domDocument, 
            RobRichards\XMLSecLibs\XMLSecurityDSig::SHA1, 
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),
            $options = array('force_uri' => true)
        );
        
        $objKey = new RobRichards\XMLSecLibs\XMLSecurityKey(RobRichards\XMLSecLibs\XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
        $objKey->loadKey($privateKey);
        $elementsByTagName = $domDocument->getElementsByTagName($ReferenceNodeName);
        $item = $elementsByTagName->item(1);
        if(empty($item)){
            throw new Exception("OBJETO NULO");
        }
        $objSign->sign($objKey, $item);
        $objSign->add509Cert($certificate);
        $elementsByTagName = $domDocument->getElementsByTagName("Signature");
        $item = $elementsByTagName->item(0);
        return $domDocument;
    }
    
    public static function ComprimirFichero($nombre_documento,$filename_zip){
        $zip = new ZipArchive();
        if($zip->open($filename_zip,ZIPARCHIVE::CREATE)===true) {
            $zip->addFile(__DIR__ ."/../ficheros/".$nombre_documento."xml",$nombre_documento."xml");
            $zip->close();
        }else {
            throw new Exception('Error creando '.$filename_zip);
        }
    }

    public static function DescomprimirFichero($nombre_documento,$filename_zip){
        $zip = new ZipArchive();
        if($zip->open($filename_zip)===true) {
            $zip->extractTo(__DIR__ ."/../ficheros/",array($nombre_documento."xml"));
            $zip->close();
            return __DIR__ ."/../ficheros/".$nombre_documento."xml";
        }else {
            throw new Exception('Error creando '.$filename_zip);
        }
    }

    public static function ObtenerTOKENAutorizacion($user_wsdl,$pass_wsdl){
        $cliente = new nusoap_client($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
        //throw new Exception($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
        $error = $cliente->getError();
        if ($error) {
            throw new Exception($error);
        }
        $result = $cliente->call("getAutorizacion", array("ruc"=>$user_wsdl,"password" => $pass_wsdl));
        if ($cliente->fault) {
            throw new Exception($result);
        } else {
            $error = $cliente->getError();
            if ($error) {
                throw new Exception($error);
            } else {
                $result = json_decode($result);
                if($result->code=="0"){
                    $token = $result->mensaje;
                    return $token;
                }else{
                    throw new Exception($result->mensaje);
                }
            }
        }
    }
    
    public static function ComprobarTOKENAutorizacion($token){
        try{
            $cliente = new nusoap_client($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
            //throw new Exception($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
            $error = $cliente->getError();
            if ($error) {
                throw new Exception($error);
                return false;
            }
            $result = $cliente->call("comprobarTOKEN", array("token"=>$token));
            if ($cliente->fault) {
                throw new Exception($result);
                return false;
            } else {
                $error = $cliente->getError();
                if ($error) {
                    throw new Exception($error);
                    return false;
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        } catch (Exception $e){
            return false;
        }
    }
    
    public static function GenerarPDF($nombre_documento,$filename_zip,$empresa,$tipo_documento){
        //$filenameR_zip = __DIR__ ."/../ficheros/S".$id_solicitud."/R-".$nombre_documento."zip";
        /*$pdf = new Anouar\Fpdf\Fpdf();
        $pdf->AddPage('L','A4');
        $pdf->Cell(69,5,utf8_decode("MUNICIPALIDAD PROVINCIAL DE SATIPO"),1,0,'C');
        $pdf->Output('masiva.pdf','D');
        */
        // Variables correspondientes a la factura.
        $RUC = "";       // RUC DE ADQUIRIENTE
        $NombreCliente = "";
        $CodigoDoc = ""; // CODIGO DE ADQUIRIENTE
        $Serie = "";     // SERIE DEL DOCUMENTO
        $Numeracion = "";// NUMERACION DEL DOC
        $NomRazSoc = ""; // Nombre o Razón social.
        $FecEmi = "";    // Fecha de emisión.
        $Moneda = "";
        $Domicilio = ""; // Domicilio.
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
            throw new Exception("ERROR EN LA DESCOMPRESION DEL ARCHIVO ".$filename_zip);
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


        // Crear el gráfico con el código de barras. ===================================
        $textoCodBar = $empresa["ruc_empresa"]."|".$TipoDoc."|".$Serie."|".$Numeracion."|".$TotIGV
                ."|".$TotMonto."|".$FecEmi."|".$CodigoDoc."|".$RUC."|".$CodHash;
        $qrCode = new QrCode();
        $qrCode
            ->setText($textoCodBar)
            ->setSize(75)
            ->setPadding(10)
            ->setErrorCorrection('QUARTILE')
            //->setErrorCorrection('HIGH')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel($Serie."-".$Numeracion)
            ->setLabelFontSize(10)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        $qrCode->save(__DIR__ ."/../ficheros/".$nombre_documento."png");
        
        $pdf = new Fpdf();
        $pdf->AddPage('P','A4');
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','B',16);
        $borde = 0;
        $pdf->SetXY(135, 15);
        $alto = 6;
        if($tipo_documento=="F"){
            $pdf->MultiCell(60,$alto,utf8_decode("FACTURA ELECTRÓNICA"),$borde,'C');
        }elseif($tipo_documento=="B"){
            $pdf->MultiCell(60,$alto,utf8_decode("BOLETA ELECTRÓNICA"),$borde,'C');
        }else{
            throw new Exception("INDEFINIDO DOCUMENTO");
        }
        $pdf->SetXY(135, $pdf->GetY());
        $pdf->SetFont('Arial','',12);
        $alto = 5;
        $pdf->Cell(60, $alto, utf8_decode("RUC: ".$empresa["ruc_empresa"]), $borde, 0, "C");
        $pdf->SetXY(135, $pdf->GetY() + $alto);
        $pdf->Cell(60, $alto, utf8_decode($Serie."-".$Numeracion), $borde, 0, "C");
        $pdf->Rect(135, 12, 60, 25);
        
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',14);
        $alto = 7;
        $pdf->Cell(180, $alto, utf8_decode($empresa["nombre_empresa"]), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','',11);
        //$pdf->Image(__DIR__ ."/../assets/img/logo2.png",30,10,70,25);
        $alto = 6;
        $pdf->MultiCell(180, $alto, utf8_decode($empresa["domiciliofiscal_empresa"]), $borde, "L");
        
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $alto = 5;
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Fecha Emision:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(140, $alto, utf8_decode(date_format(date_create($FecEmi), "d/m/Y")), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Señor(es):"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(140, $alto, utf8_decode($NombreCliente), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("RUC/DNI:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(140, $alto, utf8_decode($RUC), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Moneda:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(40, $alto, utf8_decode($Moneda), $borde, 0, "L");
        if($tipo_documento=="F"){
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(40, $alto, utf8_decode("Forma de Pago:"), $borde, 0, "L");
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(40, $alto, utf8_decode("CONTADO"), $borde, 0, "L");
        }
        $pdf->Rect(12, $pdf->GetY()-4*$alto+3, 186, 5*$alto - 1);
        
        $pdf->SetXY(15, $pdf->GetY()+2*$alto);
        $alto = 5;
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(10, $alto, utf8_decode("Item"), 1, 0, "C");
        $pdf->Cell(75, $alto, utf8_decode("Descripción"), 1, 0, "C");
        $pdf->Cell(10, $alto, utf8_decode("UM"), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("Cantidad"), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("V.U."), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("P.U."), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("Dscto."), 1, 0, "C");
        $pdf->Cell(25, $alto, utf8_decode("Valor Venta"), 1, 0, "C");
        $pdf->SetFont('Arial','',9);
        $total = 0;
        foreach ($ProdServ as $key => $value) {
            $pdf->SetXY(15, $pdf->GetY()+$alto);
            $pdf->Cell(10, $alto, utf8_decode($key+1), 1, 0, "C");
            $pdf->Cell(75, $alto, utf8_decode($ProdServ[$key]), 1, 0, "L");
            $pdf->Cell(10, $alto, utf8_decode($UniMed[$key]), 1, 0, "C");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Cant[$key],2)), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Precio[$key]/1.18,2,'.','')), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Precio[$key],2,'.','')), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode("0.00"), 1, 0, "R");
            $pdf->Cell(25, $alto, utf8_decode(number_format($Precio[$key]*$Cant[$key]/1.18,2)), 1, 0, "R");
            $total = $total + $Precio[$key]*$Cant[$key]/1.18;
        }
        $y = $pdf->GetY();
        $pdf->SetXY(130, $pdf->GetY()+$alto);
        $alto = 5;
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Op. Gravada:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total,2)), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("I.G.V.:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*0.18,2)), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Op. Inafecta:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Op. Exonerada:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Importe Total"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*1.18,2)), $borde, 0, "R");
        $pdf->Line(130, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln();
        include_once '../modelo/NumeroTexto.php';
        $importe_total_venta = number_format($total*1.18,2,'.','');
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
        
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(100, $alto, $son, $borde, 0, "L");
        $pdf->Ln();
        
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(185, $alto, utf8_decode("Representación impresa de la Factura Electrónica, consulte en https://facturae-garzasoft.com"), $borde, 0, "L");
        $pdf->Ln();
        $pdf->Image(__DIR__ ."/../ficheros/".$nombre_documento."png");

        $pdf->Output(__DIR__ ."/../ficheros/".$nombre_documento."pdf", 'F'); // Se graba el documento .PDF en el disco duro o unidad de estado sólido.
        //chmod($NomArchPDF,0777);  // Se dan permisos de lectura y escritura.

        $pdf->Output($nombre_documento."pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
        $pdf->Output();
        exit;
    }

    public static function GenerarPLESunat($fecini,$fecfin,$nombre){
        include_once '../modelo/mdlSolicitud.php';
        $mdlSolicitud = new mdlSolicitud();
        $comprobantes = $mdlSolicitud->listarSolicitudes4($fecini,$fecfin,$id_empresa);
        
        $helper = new Sample();
        
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue("A1", "Vou.Origen");
        $sheet->setCellValue("B1", "Vou.Numero");
        $sheet->setCellValue("C1", "Vou.Fecha");
        $sheet->setCellValue("D1", "Doc");
        $sheet->setCellValue("E1", "Numero");
        $sheet->setCellValue("F1", "Fec.Doc.");
        $sheet->setCellValue("G1", "Fec.Venc.");
        $sheet->setCellValue("H1", "Codigo");
        $sheet->setCellValue("I1", "Valor Exp.");
        $sheet->setCellValue("J1", "B.Imponible");
        $sheet->setCellValue("K1", "Inafecto");
        $sheet->setCellValue("L1", "Exonerado");
        $sheet->setCellValue("M1", "I.S.C.");
        $sheet->setCellValue("N1", "IGV");
        $sheet->setCellValue("O1", "Otros Trib.");
        $sheet->setCellValue("P1", "Moneda");
        $sheet->setCellValue("Q1", "TC");
        $sheet->setCellValue("R1", "Glosa");
        $sheet->setCellValue("S1", "Cta Ingreso");
        $sheet->setCellValue("T1", "Cta IGV");
        $sheet->setCellValue("U1", "Cta O. Trib.");
        $sheet->setCellValue("V1", "Cta x Cobrar");
        $sheet->setCellValue("W1", "C.Costo");
        $sheet->setCellValue("X1", "Presupuesto");
        $sheet->setCellValue("Y1", "R.Doc");
        $sheet->setCellValue("Z1", "R.Numero");
        $sheet->setCellValue("AA1", "R.Fecha");
        $sheet->setCellValue("AB1", "RUC");
        $sheet->setCellValue("AC1", "R. Social");
        //$sheet->setCellValue("S1", "Tipo");
        //$sheet->setCellValue("T1", "Tip.Doc.Iden.");
        
        $i = 2;$x=1;
        foreach ($comprobantes as $comprobante) {
            $data = json_decode($comprobante["data_solicitud"],true);
            $detalles = $data["detalles"];
            $tipoDoc = "";
            $docId = "";
            $diasPago = "0";
            if($comprobante["tipo_documento"]=="F"){
                $tipoDoc = "001";
                $docId = $data["ruc"];
            }elseif($comprobante["tipo_documento"]=="B"){
                $tipoDoc = "003";
                $docId = $data["dni"];
            }elseif($comprobante["tipo_documento"]=="C"){
                $tipoDoc = "007";
                $docId = $data["doc"];
            }elseif($comprobante["tipo_documento"]=="D"){
                $tipoDoc = "008";
                $docId = $data["doc"];
            }
            $tasaIGV = $comprobante["IGV"];
            if(empty($tasaIGV) || strlen(trim($tasaIGV))){
                $tasaIGV = 0.18;
            }
            /*$importe = 0;
            foreach ($detalles as $detalle) {
                $importe = $importe + ($detalle["cantidad"] * $detalle["precioventaunitarioxitem"]);
            }*/
            $sheet->setCellValue("A".$i, "02");
            $sheet->setCellValue("B".$i, str_pad($x,5,"0",STR_PAD_LEFT));
            $sheet->setCellValue("C".$i, date("d/m/Y",strtotime($data["fechaemision"])));
            $sheet->setCellValue("D".$i, $tipoDoc);
            $sheet->setCellValue("E".$i, $comprobante["tipo_documento"].$comprobante["serie"].'-'.str_pad($comprobante["correlativo"],8,"0",STR_PAD_LEFT));
            $sheet->setCellValue("F".$i, date("d/m/Y",strtotime($data["fechaemision"])));
            $sheet->setCellValue("G".$i, date("d/m/Y",strtotime($data["fechaemision"])));
            $sheet->setCellValue("H".$i, "00000000001");
            $sheet->setCellValue("I".$i, "");
            $sheet->setCellValue("J".$i, "");
            $sheet->setCellValue("K".$i, "");
            if($comprobante["estado_solicitud"]=="M" || $comprobante["estado_solicitud"]=="R" || $comprobante["estado_solicitud"]=="T")
                $sheet->setCellValue("L".$i, number_format($comprobante["total_doc"],2,'.',''));
            else
                $sheet->setCellValue("L".$i, number_format(0,2,'.',''));
            $sheet->setCellValue("M".$i, "");
            $sheet->setCellValue("N".$i, "");
            $sheet->setCellValue("O".$i, "");
            $sheet->setCellValue("P".$i, "S");
            $sheet->setCellValue("Q".$i, 1.000);
            $sheet->setCellValue("R".$i, "Ventas");
            $sheet->setCellValue("S".$i, "70211");
            $sheet->setCellValue("T".$i, "40111");
            $sheet->setCellValue("U".$i, "");
            $sheet->setCellValue("V".$i, "12121");
            $sheet->setCellValue("X".$i, "");
            $sheet->setCellValue("Y".$i, "");
            $sheet->setCellValue("Z".$i, "");
            $sheet->setCellValue("AA".$i, "");
            if($data["usuario"]=="VARIOS"){
                $sheet->setCellValue("AB".$i, "00000000001");
            }else{
                $sheet->setCellValue("AB".$i, $comprobante["doc_cliente"]);
            }
            $sheet->setCellValue("AC".$i, $data["usuario"]);
            $i++;$x++;
            //}
        }
    
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('COMPROBANTES');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="COMPROBANTES.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    }
    
    public static function GenerarResumenComprobantes($fecini,$fecfin,$id_empresa){
        include_once '../modelo/mdlSolicitud.php';
        $mdlSolicitud = new mdlSolicitud();
        $comprobantes = $mdlSolicitud->listarSolicitudes4($fecini,$fecfin,$id_empresa);
        
        $helper = new Sample();
        
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue("A1", "N°");
        $sheet->setCellValue("B1", "Tipo Comprobante");
        $sheet->setCellValue("C1", "Numeracion");
        $sheet->setCellValue("D1", "DNI/RUC");
        $sheet->setCellValue("E1", "Nombre/Razon Social");
        $sheet->setCellValue("F1", "Total");
        $sheet->setCellValue("G1", "Fecha de Emision");
        $sheet->setCellValue("H1", "Fecha de Declaracion");
        $sheet->setCellValue("I1", "Estado");
        
        $i = 2;
        $w = 1;
        foreach ($comprobantes as $comprobante) {
            $data = json_decode($comprobante["data_solicitud"],true);
            $detalles = $data["detalles"];
            $tipoDoc = "";
            $docId = "";
            $diasPago = "0";
            if($comprobante["tipo_documento"]=="F"){
                $tipoDoc = "001";
                $docId = $data["ruc"];
                $tipoDocumento = "FACTURA";
                $numeracion = $data["numerofactura"];
            }elseif($comprobante["tipo_documento"]=="B"){
                $tipoDoc = "003";
                $docId = $data["dni"];
                $tipoDocumento = "BOLETA";
                $numeracion = $data["numeroboleta"];
            }elseif($comprobante["tipo_documento"]=="C"){
                $tipoDoc = "007";
                $docId = $data["doc"];
                $tipoDocumento = "NOTA DE CREDITO";
                $numeracion = $data["numeronotacredito"];
            }elseif($comprobante["tipo_documento"]=="D"){
                $tipoDoc = "008";
                $docId = $data["doc"];
                $tipoDocumento = "NOTA DE DEBITO";
                $numeracion = $data["numeronotadebito"];
            }
            if($comprobante["estado_solicitud"]=="T" || $comprobante["estado_solicitud"]=="M"){
                $estadoComprobante = "DECLARADO Y ACTIVO";
            }elseif($comprobante["estado_solicitud"]=="B"){
                $estadoComprobante = "DECLARADO Y ANULADO";
            }elseif($comprobante["estado_solicitud"]=="I"){
                $estadoComprobante = "DECLARADO E INCORRECTO";
            }elseif($comprobante["estado_solicitud"]=="S"){
                $estadoComprobante = "DECLARADO Y SIN COMPROBAR";
            }elseif($comprobante["estado_solicitud"]=="R"){
                $estadoComprobante = "AUN NO DECLARADO";
            }elseif($comprobante["estado_solicitud"]=="E"){
                $estadoComprobante = "NO REGISTRADO";
            }
            $sheet->setCellValue("A".$i, $w);$w++;
            $sheet->setCellValue("B".$i, $tipoDocumento);
            $sheet->setCellValue("C".$i, $comprobante["tipo_documento"].$numeracion);
            $sheet->setCellValue("D".$i, $docId);
            $sheet->setCellValue("E".$i, $data["usuario"]);
            $sheet->setCellValue("F".$i, $comprobante["total_doc"]);
            $sheet->setCellValue("G".$i, date_format(date_create($data["fechaemision"]), "d/m/Y"));
            $sheet->setCellValue("H".$i, date_format(date_create($comprobante["fechahora_envio"]), "d/m/Y"));
            $sheet->setCellValue("I".$i, $estadoComprobante);
            $i++;
        }
        
        $spreadsheet->getActiveSheet()->setTitle('Comprobantes');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ResumenComprobantes.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    }

    public static function ComprobarRespuestaXML($filenameR_xml){
        if(!file_exists($filenameR_xml)){
            throw new Exception("NO SE ENCUENTRA EL FICHERO EN LA RUTA: ".$filenameR_xml);
        }
        $xml = file_get_contents($filenameR_xml);
        $DOM = new DOMDocument('1.0', 'ISO-8859-1');
        $DOM->preserveWhiteSpace = FALSE;
        $DOM->loadXML($xml);
        $DocumentResponse = $DOM->getElementsByTagName('DocumentResponse')->item(0);
        $ResponseCode = $DocumentResponse->getElementsByTagName('ResponseCode');
        foreach($ResponseCode as $Nodo){
            $code = $Nodo->nodeValue;
            break;
        }
        $Description = $DocumentResponse->getElementsByTagName('Description');
        foreach($Description as $Nodo){
            $descripcion = $Nodo->nodeValue;
            break;
        }
        return array(
            "codigo"=>$code,
            "detalle"=>$descripcion
        );
    }

    public static function CrearQR($nombre_documento,$ruc_empresa){print_r($nombre_documento);
        if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
            Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
        }
        // Variables correspondientes a la factura.
        $RUC = "";       // RUC DE ADQUIRIENTE
        $NombreCliente = "";
        $CodigoDoc = ""; // CODIGO DE ADQUIRIENTE
        $Serie = "";     // SERIE DEL DOCUMENTO
        $Numeracion = "";// NUMERACION DEL DOC
        $NomRazSoc = ""; // Nombre o Razón social.
        $FecEmi = "";    // Fecha de emisión.
        $Moneda = "";
        $Domicilio = ""; // Domicilio.
        $CodHash = "";   // Código Hash.
        $TipoDoc = "";   // Tipo de documento.
        $TotGrav = 0;    // Total gravado.
        $TotIGV = 0;     // Total IGV.
        $TotMonto = 0;   // Total importe. 
        // Obteniendo datos del archivo .XML (factura electrónica)======================
        $xml = file_get_contents(__DIR__ ."/../ficheros/".$nombre_documento."xml");
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


        // Crear el gráfico con el código de barras. ===================================
        $textoCodBar = $ruc_empresa."|".$TipoDoc."|".$Serie."|".$Numeracion."|".$TotIGV
                ."|".$TotMonto."|".$FecEmi."|".$CodigoDoc."|".$RUC."|".$CodHash;
        $qrCode = new QrCode();
        $qrCode
            ->setText($textoCodBar)
            ->setSize(75)
            ->setPadding(10)
            ->setErrorCorrection('QUARTILE')
            //->setErrorCorrection('HIGH')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel($Serie."-".$Numeracion)
            ->setLabelFontSize(10)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        $qrCode->save(__DIR__ ."/../ficheros/".$nombre_documento."png");

    }

    
    public static function EnviarCorreoNuevaPoliza($usuario,$poliza) {
        if (!empty($usuario["correos"]) && is_array($usuario["correos"]) && count($usuario["correos"])>0) {
            $para = implode(", ", $usuario["correos"]);
            $título = 'NUEVA POLIZA';
            $mensaje = '
                <html>
                    <head>
                      <title>NUEVA POLIZA</title>
                    </head>
                    <body>
                        <p>¡SE HA REGISTRADO UNA NUEVA POLIZA EN LA PLATAFORMA GCCORREDORES!</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>NUMERO</th>
                                    <th>FECHA INICIO</th>
                                    <th>FECHA FINAL</th>
                                    <th>NOMBRE DE POLIZA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>' . $poliza["numero_poliza"] . '</td>
                                    <td>' . $poliza["fecha_inicio_poliza"] . '</td>
                                    <td>' . $poliza["fecha_fin_poliza"] . '</td>
                                    <td>' . $poliza["nombre_compania_tipopoliza"] . '</td>
                                </tr>
                            </tbody>
                        </table>
                        <p>INGRESA A <a href="http://gccorredores.com/seguros">AQUI</a></p>
                    </body>
                </html>
                ';
            $cabeceras = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $cabeceras .= 'From: GCCorredores <sistema@gccorredores.com>' . "\r\n";
            mail($para, $título, $mensaje, $cabeceras);
        }
    }
    
    public static function EnviarCorreoNuevoUsuario($usuario) {
        if (!empty($usuario["correos"]) && is_array($usuario["correos"]) && count($usuario["correos"])>0) {
            $para = implode(", ", $usuario["correos"]);
            $título = 'USUARIO REGISTRADO';
            $mensaje = '
                <html>
                    <head>
                      <title>USUARIO REGISTRADO</title>
                    </head>
                    <body>
                        <p>¡SE HA REGISTRADO EN LA PLATAFORMA GCCORREDORES!</p>
                        <p><b>USUARIO: </b>' . $usuario["user_usuario"] . '</p>
                        <p><b>CONTRASEÑA: </b>' . $usuario["pass_usuario"] . '</p>
                        <p>INGRESA A <a href="http://gccorredores.com/seguros">AQUI</a></p>
                    </body>
                </html>
                ';
            $cabeceras = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $cabeceras .= 'From: GCCorredores <sistema@gccorredores.com>' . "\r\n";
            mail($para, $título, $mensaje, $cabeceras);
        }
    }

    public static function GenerarPDF5($nombre_documento,$filename_zip,$empresa,$tipo_documento){
        //$filenameR_zip = __DIR__ ."/../ficheros/S".$id_solicitud."/R-".$nombre_documento."zip";
        /*$pdf = new Anouar\Fpdf\Fpdf();
        $pdf->AddPage('L','A4');
        $pdf->Cell(69,5,utf8_decode("MUNICIPALIDAD PROVINCIAL DE SATIPO"),1,0,'C');
        $pdf->Output('masiva.pdf','D');
        */
        // Variables correspondientes a la factura.
        $RUC = "";       // RUC DE ADQUIRIENTE
        $NombreCliente = "";
        $CodigoDoc = ""; // CODIGO DE ADQUIRIENTE
        $Serie = "";     // SERIE DEL DOCUMENTO
        $Numeracion = "";// NUMERACION DEL DOC
        $NomRazSoc = ""; // Nombre o Razón social.
        $FecEmi = "";    // Fecha de emisión.
        $Moneda = "";
        $Domicilio = ""; // Domicilio.
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


        // Crear el gráfico con el código de barras. ===================================
        $textoCodBar = $empresa["ruc_empresa"]."|".$TipoDoc."|".$Serie."|".$Numeracion."|".$TotIGV
                ."|".$TotMonto."|".$FecEmi."|".$CodigoDoc."|".$RUC."|".$CodHash;
        $qrCode = new QrCode();
        $qrCode
            ->setText($textoCodBar)
            ->setSize(75)
            ->setPadding(10)
            ->setErrorCorrection('QUARTILE')
            //->setErrorCorrection('HIGH')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel($Serie."-".$Numeracion)
            ->setLabelFontSize(10)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        $qrCode->save(__DIR__ ."/../ficheros/".$nombre_documento."png");
        
        $pdf = new Fpdf();
        $pdf->AddPage('P','A4');
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','B',16);
        $borde = 0;
        $pdf->SetXY(135, 15);
        $alto = 6;
        if($tipo_documento=="F"){
            $pdf->MultiCell(60,$alto,utf8_decode("FACTURA ELECTRÓNICA"),$borde,'C');
        }elseif($tipo_documento=="B"){
            $pdf->MultiCell(60,$alto,utf8_decode("BOLETA ELECTRÓNICA"),$borde,'C');
        }else{
            throw new Exception("INDEFINIDO DOCUMENTO");
        }
        $pdf->SetXY(135, $pdf->GetY());
        $pdf->SetFont('Arial','',12);
        $alto = 5;
        $pdf->Cell(60, $alto, utf8_decode("RUC: ".$empresa["ruc_empresa"]), $borde, 0, "C");
        $pdf->SetXY(135, $pdf->GetY() + $alto);
        $pdf->Cell(60, $alto, utf8_decode($Serie."-".$Numeracion), $borde, 0, "C");
        $pdf->Rect(135, 12, 60, 25);
        
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',14);
        $alto = 7;
        $pdf->Cell(180, $alto, utf8_decode($empresa["nombre_empresa"]), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','',11);
        //$pdf->Image(__DIR__ ."/../assets/img/logo2.png",30,10,70,25);
        $alto = 6;
        $pdf->MultiCell(180, $alto, utf8_decode($empresa["domiciliofiscal_empresa"]), $borde, "L");
        
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $alto = 5;
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Fecha Emision:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(140, $alto, utf8_decode(date_format(date_create($FecEmi), "d/m/Y")), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Señor(es):"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(140, $alto, utf8_decode($NombreCliente), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("RUC/DNI:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(140, $alto, utf8_decode($RUC), $borde, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Moneda:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(40, $alto, utf8_decode($Moneda), $borde, 0, "L");
        if($tipo_documento=="F"){
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(40, $alto, utf8_decode("Forma de Pago:"), $borde, 0, "L");
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(40, $alto, utf8_decode("CONTADO"), $borde, 0, "L");
        }
        $pdf->Rect(12, $pdf->GetY()-4*$alto+3, 186, 5*$alto - 1);
        
        $pdf->SetXY(15, $pdf->GetY()+2*$alto);
        $alto = 5;
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(10, $alto, utf8_decode("Item"), 1, 0, "C");
        $pdf->Cell(75, $alto, utf8_decode("Descripción"), 1, 0, "C");
        $pdf->Cell(10, $alto, utf8_decode("UM"), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("Cantidad"), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("V.U."), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("P.U."), 1, 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("Dscto."), 1, 0, "C");
        $pdf->Cell(25, $alto, utf8_decode("Valor Venta"), 1, 0, "C");
        $pdf->SetFont('Arial','',9);
        $total = 0;
        foreach ($ProdServ as $key => $value) {
            $pdf->SetXY(15, $pdf->GetY()+$alto);
            $pdf->Cell(10, $alto, utf8_decode($key+1), 1, 0, "C");
            $pdf->Cell(75, $alto, utf8_decode($ProdServ[$key]), 1, 0, "L");
            $pdf->Cell(10, $alto, utf8_decode($UniMed[$key]), 1, 0, "C");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Cant[$key],2)), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Precio[$key]/1.18,2,'.','')), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Precio[$key],2,'.','')), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode("0.00"), 1, 0, "R");
            $pdf->Cell(25, $alto, utf8_decode(number_format($Precio[$key]*$Cant[$key]/1.18,2)), 1, 0, "R");
            $total = $total + $Precio[$key]*$Cant[$key]/1.18;
        }
        $y = $pdf->GetY();
        $pdf->SetXY(130, $pdf->GetY()+$alto);
        $alto = 5;
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Op. Gravada:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total,2)), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("I.G.V.:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*0.18,2)), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Op. Inafecta:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Op. Exonerada:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(130);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $alto, utf8_decode("Importe Total"), $borde, 0, "L");
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*1.18,2)), $borde, 0, "R");
        $pdf->Line(130, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln();
        include_once '../modelo/NumeroTexto.php';
        $importe_total_venta = number_format($total*1.18,2,'.','');
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
        
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(100, $alto, $son, $borde, 0, "L");
        $pdf->Ln();
        
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(185, $alto, utf8_decode("Representación impresa de la Factura Electrónica, consulte en https://facturae-garzasoft.com"), $borde, 0, "L");
        $pdf->Ln();
        $pdf->Image(__DIR__ ."/../ficheros/".$nombre_documento."png");

        $pdf->Output(__DIR__ ."/../ficheros/".$nombre_documento."pdf", 'F'); // Se graba el documento .PDF en el disco duro o unidad de estado sólido.
        //chmod($NomArchPDF,0777);  // Se dan permisos de lectura y escritura.

        //$pdf->Output($nombre_documento."pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
        $pdf->close();
        return true;
    }
    
}
//$nombre_documento = "10723124871-01-F001-4355";
//Algoritmos::FirmaDigital(new DOMDocument(),"pruebaId", "CERTIFICADO.cer", "at25jk3o2016");
//$xmlFile = Algoritmos::FirmaDigital(new DOMDocument(),"signature10723124871", "dlVyY0hRRklGa1RxRGFsSQ==.p12", "vcNTfp5gEdP8VH4z");
//$xmlFile->save(__DIR__ ."/../ficheros/$nombre_documento.xml");
/*$domDocument = new DOMDocument();
        $domDocument->load(__DIR__ ."/../ficheros/10723124871-01-F001-4355.xml");

        $zip = new ZipArchive();
        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
        if($zip->open($filename_zip,ZIPARCHIVE::CREATE)===true) {
            //$zip->addEmptyDir("dummy");
            $zip->addFile(__DIR__ ."/../ficheros/".$nombre_documento."xml",$nombre_documento."xml");
            //$zip->addFile($nombre_documento."xml","dummy/".$nombre_documento."xml");
            $zip->close();
        }else {
            throw new Exception('Error creando '.$filename_zip);
        }
        
        $file_ZIP = file_get_contents($filename_zip);
        $file_ZIP_BASE64 = base64_encode($file_ZIP);
        
        $cliente_SUNAT = new nusoap_client(WBSV_ENV_PRO);
        $error = $cliente_SUNAT->getError();
        if ($error) {
            throw new Exception("ERROR: ".$error);
        }
        $cliente_SUNAT->setCredentials($usuario_sunat, $password_sunat);
        
        $mdlSolicitud->actualizarSolicitud($id_solicitud, $nombre_documento, date("Y-m-d\TH:i:s"), "", "E", 
                array(
                    array($nombre_documento,$filename_xml,"xml"),
                    array($nombre_documento,$filename_zip,"zip"),
                ));
        
        $result = $cliente_SUNAT->call("sendBill", array(
            "fileName"=>$nombre_documento."zip",
            "contentFile"=>$file_ZIP_BASE64
        ));
        
        throw new Exception("ERROR 0: ". json_encode($result));
        if ($cliente_SUNAT->fault) {
            throw new Exception("ERROR 1: ". json_encode($result));
        } else {
            $error = $cliente->getError();
            if ($error) {
                throw new Exception("ERROR 2: ". json_encode($error));
            } else {
                throw new Exception("NO HAY ERROR");
            }
        }
        */