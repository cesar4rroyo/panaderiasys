<?php
//error_reporting(0);
session_start();
$_SESSION['R_ini_ses']="SI";
$_SESSION['R_origen_ses']="I";
$_SESSION['R_IdSucursal']=1;
$_SESSION['R_versesadm']=1;
require_once "nusoap/lib/nusoap.php";
require_once "modelo/clsMovimiento.php";
function getCaja($idapertura,$idcierre,$idsucursal){
    $objMantenimiento = new clsMovimiento(1,1,1,1);
    $sucursal = $objMantenimiento->obtenerDataSQL("select * from sucursal where idsucursal=".$idsucursal)->fetchObject()->razonsocial;
    $efectivo = $objMantenimiento
                    ->obtenerDataSQL("SELECT CASE WHEN sum(totalpagado) IS NULL THEN 0 ELSE sum(totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago = 3 AND estado='N' AND (modopago='E' OR modopago='A') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
                    ->fetchObject()->sum;
    $tarjetas_visa_modoT = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total-totalpagado) IS NULL THEN 0 ELSE sum(total-totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (3) AND estado='N' AND (modopago='T') AND idtipotarjeta = 1 and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_visa_modoA = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (3) AND estado='N' AND (modopago='A') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_visa = $tarjetas_visa_modoT + $tarjetas_visa_modoA;
    $tarjetas_visa2_modoT = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total-totalpagado) IS NULL THEN 0 ELSE sum(total-totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (27) AND estado='N' AND (modopago='T') AND idtipotarjeta = 1 and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_visa2_modoA = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(montotarjeta,position('1@' in montotarjeta)+2,position('|' in montotarjeta)-2-position('1@' in montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (27) AND estado='N' AND (modopago='A') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_visa2 = $tarjetas_visa2_modoT + $tarjetas_visa2_modoA;
    $tarjetas_mastercard_modoT = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago = 3 AND estado='N' AND (modopago='T') AND idtipotarjeta = 2 and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_mastercard_modoA = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum((substr(montotarjeta,position('2@' in montotarjeta)+2,length(montotarjeta)-2-position('1@' in montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(montotarjeta,position('2@' in montotarjeta)+2,length(montotarjeta)-2-position('1@' in montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (3) AND estado='N' AND (modopago='A') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_mastercard = $tarjetas_mastercard_modoT + $tarjetas_mastercard_modoA;
    $tarjetas_mastercard2_modoT = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago = 27 AND estado='N' AND (modopago='T') AND idtipotarjeta = 2 and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_mastercard2_modoA = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum((substr(montotarjeta,position('2@' in montotarjeta)+2,length(montotarjeta)-2-position('1@' in montotarjeta)))::numeric) IS NULL THEN 0 ELSE sum((substr(montotarjeta,position('2@' in montotarjeta)+2,length(montotarjeta)-2-position('1@' in montotarjeta)))::numeric) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (27) AND estado='N' AND (modopago='A') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_mastercard2 = $tarjetas_mastercard2_modoT + $tarjetas_mastercard2_modoA;
    $tarjetas_modoT = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (3,27) AND estado='N' AND (modopago='T') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas_modoA = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total-totalpagado) IS NULL THEN 0 ELSE sum(total-totalpagado) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago in (3,27) AND estado='N' AND (modopago='A') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $tarjetas = $tarjetas_visa + $tarjetas_mastercard;
    $cheques = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago = 3 AND estado='N' AND (modopago='C') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $depositos = $objMantenimiento
            ->obtenerDataSQL("SELECT CASE WHEN sum(total) IS NULL THEN 0 ELSE sum(total) END FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago = 3 AND estado='N' AND (modopago='D') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $saldoinicial = $objMantenimiento
            ->obtenerDataSQL("SELECT sum(total) FROM (select * from movimientohoy union all select * from movimiento) as T WHERE idconceptopago = 1 AND estado='N' and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
    $ingresos = $objMantenimiento
            ->obtenerDataSQL("SELECT sum(totalpagado) as total FROM (select * from movimientohoy union all select * from movimiento) as mh WHERE idtipodocumento = 9 AND idconceptopago NOT IN (1,3,27) AND estado='N' and modopago not in ('C','D') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->total;
    $anticipado = $objMantenimiento
                    ->obtenerDataSQL("SELECT sum(totalpagado) as total FROM (select * from movimientohoy union all select * from movimiento) as mh WHERE idtipodocumento = 9 AND idconceptopago IN (27) AND estado='N' and modopago not in ('C','D') and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
                    ->fetchObject()->total;
    $ingresostotalpagado = $objMantenimiento
            ->obtenerDataSQL("SELECT sum(totalpagado) as totalpagado FROM (select * from movimientohoy union all select * from movimiento) as mh WHERE idtipodocumento = 9 AND idconceptopago NOT IN (1,3) AND estado='N' and modopago not in ('C','D') and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->totalpagado;        
    $egresos = $objMantenimiento
            ->obtenerDataSQL("SELECT sum(total) FROM (select * from movimientohoy union all select * from movimiento) as mh WHERE idtipodocumento = 10 AND mh.estado = 'N' and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)
            ->fetchObject()->sum;
            
    $apertura = $objMantenimiento->obtenerDataSQL("select *,(select nombreusuario from usuario where idusuario=mh.idusuario and idsucursal=mh.idsucursalusuario) as usuario from (select * from movimientohoy union all select * from movimiento) as mh where idmovimiento=".$idapertura." and idsucursal=".$idsucursal." and idmovimiento=".$idapertura)->fetchObject();
    
    $gasto = $objMantenimiento->obtenerDataSQL("select (select descripcion from conceptopago where idconceptopago=T.idconceptopago) as conceptopago,* from (select * from movimientohoy union all select * from movimiento) as T where idsucursal=$idsucursal and idtipodocumento=10 and idconceptopago<>2 and idmovimiento>".$idapertura." and idmovimiento<".$idcierre);
    $detalleGasto = "";
    if($gasto->rowCount()>0){
        while($dat=$gasto->fetchObject()){
            $detalleGasto.=$dat->comentario."@".$dat->total."|";
        }
    }
    $detalleGasto = substr($detalleGasto,0,strlen($detalleGasto)-1);
    
    $credito = $objMantenimiento->obtenerDataSQL("select sum(total) from (select * from movimientohoy union all select * from movimiento) as T where comentario like '%VENTA AL CREDITO%' and idtipomovimiento=2 AND estado='N' and idsucursal=".$idsucursal." and idmovimiento>".$idapertura." and idmovimiento<".$idcierre)->fetchObject()->sum;
    //$credito = $credito - $glovocredito;
    
    $boveda=0;
    $cierre = $objMantenimiento->obtenerDataSQL("select T.totalpagado from (select idmovimiento,idsucursal,totalpagado,idconceptopago from movimiento union all select idmovimiento,idsucursal,totalpagado,idconceptopago from movimientohoy) as T where T.idconceptopago=2 and T.idsucursal=$idsucursal and T.idmovimiento=".$idcierre);
    if($cierre->rowCount()>0){
        $boveda = $cierre->fetchObject()->totalpagado;
    }
    
    $array[]=array("fecha"=>($apertura->fecha),"efectivo"=>$efectivo,"apertura"=>$apertura->total,"ingresos"=>($ingresos+0),
                 "visa"=>$tarjetas_visa,"master"=>$tarjetas_mastercard,"inicial"=>$saldoinicial,"gastos"=>$egresos,"anticipado"=>$anticipado,
                 "detalleGasto"=>$detalleGasto,"comentario"=>$apertura->comentario,"responsable"=>$apertura->usuario,"credito"=>($credito+0),"boveda"=>$boveda,"depositos"=>$depositos,
                 "sucursal"=>$sucursal);
    return $array;
}

function getStock($idsucursal){
    $objMantenimiento = new clsMovimiento(1,1,1,1);
    $stock = $objMantenimiento->obtenerDataSQL("select p.descripcion as producto, case when p.idproducto in (69,98,104,101) then round(sp.stockbase*lu.precioventa,2) else sp.stockbase end as stock,c.descripcion as categoria
        from producto as p 
        left join categoria as c on c.idcategoria=p.idcategoria and c.idsucursal=p.idsucursal
        inner join stockproducto sp on sp.idproducto=p.idproducto and p.idsucursal=sp.idsucursal and p.idunidadbase=sp.idunidad and sp.idsucursalproducto=p.idsucursal
        inner join LISTAUNIDAD LU on LU.idproducto= P.Idproducto  and P.idsucursal=LU.idsucursal and LU.idunidad=P.idunidadbase and P.idsucursal=LU.idsucursalproducto 
        where p.estado='N' and p.idsucursal=".$idsucursal." and p.kardex='S' and p.idcategoria in (20,10,11,22,12,19,13)
        order by c.descripcion asc,p.descripcion asc");
    while($v=$stock->fetchObject()){
        //if($v->stock>=0){
            $array[]=array("producto"=>$v->producto,"stock"=>$v->stock,"categoria"=>$v->categoria);
        //}
    }
    return $array;
}

// Deshabilitar cachedie()
ini_set("soap.wsdl_cache_enabled", "0");
  
//generar instancia de soap_server  
$server = new soap_server();
$server->configureWSDL("caja", "urn:caja");
  
//configurar la estructura de los datos, 
//este arreglo es de tipo asociativo y contiene el nombre y tipo de dato.
$server->wsdl->addComplexType(
        'Estructura',
        'complexType',
        'struct',
        'all',
        '',
          array(
            'fecha' => array('name' => 'fecha', 'type' => 'xsd:string'),
            'efectivo'=>array('name' => 'efectivo', 'type' => 'xsd:string'),
            'ingresos'=>array('name' => 'ingresos', 'type' => 'xsd:string'),
            'apertura'=>array('name' => 'apertura', 'type' => 'xsd:string'),
            'visa'=>array('name' => 'visa', 'type' => 'xsd:string'),
            'master'=>array('name' => 'master', 'type' => 'xsd:string'),
            'inicial'=>array('name' => 'inicial', 'type' => 'xsd:string'),
            'gastos'=>array('name' => 'gastos', 'type' => 'xsd:string'),
            'anticipado'=>array('name' => 'anticipado', 'type' => 'xsd:string'),
            'detalleGasto'=>array('name' => 'detalleGasto', 'type' => 'xsd:string'),
            'comentario'=>array('name' => 'comentario', 'type' => 'xsd:string'),
            'responsable'=>array('name' => 'responsable', 'type' => 'xsd:string'),
            'credito'=>array('name' => 'credito', 'type' => 'xsd:string'),
            'boveda'=>array('name' => 'boveda', 'type' => 'xsd:string'),
            'depositos'=>array('name' => 'depositos', 'type' => 'xsd:string'),
            'sucursal'=>array('name' => 'sucursal', 'type' => 'xsd:string'),
            //'formapago'=>array('name' => 'direccion', 'type' => 'xsd:string')
            )
      );

$server->wsdl->addComplexType(
        'Estructura2',
        'complexType',
        'struct',
        'all',
        '',
          array(
            'producto' => array('name' => 'producto', 'type' => 'xsd:string'),
            'categoria'=>array('name' => 'categoria', 'type' => 'xsd:string'),
            'stock'=>array('name' => 'stock', 'type' => 'xsd:string'),
            )
      );
//configurar arreglo de la estructura
$server->wsdl->addComplexType(
      'ArregloDeEstructuras',
      'complexType',
      'array',
      'sequence',
      'http://schemas.xmlsoap.org/soap/encoding/:Array',
      array(),
      array(
        array('ref' => 'http://schemas.xmlsoap.org/soap/encoding/:arrayType',
          'wsdl:arrayType' => 'tns:Estructura[]'
        )
      ),'tns:Estructura');

$server->wsdl->addComplexType(
      'ArregloDeEstructuras2',
      'complexType',
      'array',
      'sequence',
      'http://schemas.xmlsoap.org/soap/encoding/:Array',
      array(),
      array(
        array('ref' => 'http://schemas.xmlsoap.org/soap/encoding/:arrayType',
          'wsdl:arrayType' => 'tns:Estructura2[]'
        )
      ),'tns:Estructura2');

$server->register("getCaja",
    array("idapertura" => "xsd:integer","idcierre" => "xsd:integer","idsucursal" => "xsd:integer"),
    array("return" => "tns:ArregloDeEstructuras"),
    "urn:venta",
    "urn:venta#getCaja",
    "rpc",
    "literal",
    "Nos da una lista del cierre de caja");

$server->register("getStock",
    array("idsucursal" => "xsd:integer"),
    array("return" => "tns:ArregloDeEstructuras2"),
    "urn:venta",
    "urn:venta#getStock",
    "rpc",
    "literal",
    "Nos da una lista del stock actual");

//Establecer servicio        
if (isset($HTTP_RAW_POST_DATA)) { 
    $input = $HTTP_RAW_POST_DATA; 
}else{ 
    $input = implode("\r\n", file('php://input')); 
}
   
$server->service($input);
?>