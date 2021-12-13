<?php
//error_reporting(0);
session_start();
$_SESSION['R_ini_ses']="SI";
$_SESSION['R_origen_ses']="I";
$_SESSION['R_IdSucursal']=1;
$_SESSION['R_versesadm']=1;
require_once "nusoap/lib/nusoap.php";
require_once "modelo/clsMovimiento.php";
function getVenta($id,$idsucursal){
    $objMovimiento = new clsMovimiento(1,1,1,1);
    $dat=$objMovimiento->obtenerDataSQL("select T.*,(case when T.idconceptopago>0 then (select descripcion from conceptopago where idconceptopago=T.idconceptopago) else '-' end) as conceptopago,
    (case when T.idtipomovimiento=2 then (select case when X.tipoventa='A' then 'Gerencia' when X.tipoventa='T' then 'Cortesia' when X.tipoventa='C' then 'Credito' when X.tipoventa='V' then 'Vale' else 'Comun' end  from (select tipoventa,idmovimientoref,idsucursalref from movimiento union all select tipoventa,idmovimientoref,idsucursalref from movimientohoy) X where X.idmovimientoref=T.idmovimiento and X.idsucursalref=T.idsucursal) else '-' end) as tipoventa,
    pm.apellidos||' '||pm.nombres as cliente,pm.nrodoc,p.direccion
    from (select * from movimiento union all select * from movimientohoy) as T 
    left join persona p on p.idpersona=T.idpersona and p.idsucursal=T.idsucursalpersona
    left join personamaestro pm on pm.idpersonamaestro=p.idpersonamaestro
    where T.idmovimiento=".$id." and T.idsucursal=".$idsucursal)->fetchObject();

    if($dat->idconceptopago==27){
      $pagoanticipado = $objMovimiento->obtenerDataSQL("select * from pagoanticipado where idpagoanticipado=".$dat->idmovimientoref)->fetchObject();
      $saldo = $pagoanticipado->total - $pagoanticipado->valor;
    }else{
      $saldo=0;
    }
    $array[]=array("fecha"=>($dat->fecha),"numero"=>$dat->numero,"tipoventa"=>$dat->tipoventa,"idtipodocumento"=>$dat->idtipodocumento,
                 "cliente"=>$dat->cliente,"subtotal"=>$dat->subtotal,"igv"=>$dat->igv,"total"=>$dat->total,
                 "comentario"=>$dat->comentario,"vendedor"=>$dat->idresponsable,"conceptopago"=>$dat->conceptopago,"glosa"=>$dat->glosa,"nrodoc"=>$dat->nrodoc,"direccion"=>$dat->direccion,"saldo"=>$saldo);
    return $array;
}

function getVentaDetalle($id,$idsucursal){
    $objMovimiento = new clsMovimiento(1,1,1,1);
    $rs=$objMovimiento->obtenerDataSQL("select (case when dma.idproducto>0 then p.descripcion else dma.comentario end) as producto,p.abreviatura,dma.precioventa,dma.cantidad,p.idunidadbase as idunidad,p.idproducto,dma.idsucursal,dma.comentario from detallemovalmacen dma left join producto p on p.idproducto=dma.idproducto and dma.idsucursalproducto=p.idsucursal
    where dma.idsucursal=".$idsucursal." and dma.idmovimiento=".$id);
    while($dat=$rs->fetchObject()){
        $descuento=0;
        $precioventa = $dat->precioventa;
        $comentario="";
        $array[]=array("cantidad"=>($dat->cantidad),"unidad"=>$dat->idunidad,"producto"=>trim(utf8_decode($dat->producto)),"precioventa"=>$precioventa,"abreviatura"=>$dat->abreviatura,"idproducto"=>$dat->idproducto,"idsucursal"=>$dat->idsucursal,"comentario"=>$dat->comentario);
    }
    return $array;
}

// Deshabilitar cachedie()
ini_set("soap.wsdl_cache_enabled", "0");
  
//generar instancia de soap_server  
$server = new soap_server();
$server->configureWSDL("venta", "urn:venta");
  
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
            'numero'=>array('name' => 'numero', 'type' => 'xsd:string'),
            'cliente'=>array('name' => 'cliente', 'type' => 'xsd:string'),
            'subtotal'=>array('name' => 'subtotal', 'type' => 'xsd:string'),
            'igv'=>array('name' => 'igv', 'type' => 'xsd:string'),
            'total'=>array('name' => 'total', 'type' => 'xsd:string'),
            'idtipodocumento'=>array('name' => 'idtipodocumento', 'type' => 'xsd:string'),
            //'dni'=>array('name' => 'dni', 'type' => 'xsd:string'),
            'comentario'=>array('name' => 'comentario', 'type' => 'xsd:string'),
            //'direccion'=>array('name' => 'direccion', 'type' => 'xsd:string'),
            'vendedor'=>array('name' => 'direccion', 'type' => 'xsd:string'),
            'tipoventa'=>array('name' => 'tipoventa', 'type' => 'xsd:string'),
            'conceptopago'=>array('name' => 'conceptopago', 'type' => 'xsd:string'),
            'glosa'=>array('name' => 'glosa', 'type' => 'xsd:string'),
            'nrodoc'=>array('name' => 'nrodoc', 'type' => 'xsd:string'),
            'direccion'=>array('name' => 'direccion', 'type' => 'xsd:string'),
            'saldo'=>array('name' => 'saldo', 'type' => 'xsd:string'),
            )
      );

$server->wsdl->addComplexType(
        'EstructuraDetalle',
        'complexType',
        'struct',
        'all',
        '',
          array(
            'cantidad' => array('name' => 'cantidad', 'type' => 'xsd:string'),
            'unidad' => array('name' => 'unidad', 'type' => 'xsd:string'),
            'producto'=>array('name' => 'producto', 'type' => 'xsd:string'),
            'precioventa'=>array('name' => 'precioventa', 'type' => 'xsd:string'),
            'abreviatura'=>array('name' => 'abreviatura', 'type' => 'xsd:string'),
            'idproducto'=>array('name' => 'idproducto', 'type' => 'xsd:string'),
            'idsucursal'=>array('name' => 'idsucursal', 'type' => 'xsd:string'),
            'comentario'=>array('name' => 'comentario', 'type' => 'xsd:string'),
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
      'ArregloDeEstructurasDetalle',
      'complexType',
      'array',
      'sequence',
      'http://schemas.xmlsoap.org/soap/encoding/:Array',
      array(),
      array(
        array('ref' => 'http://schemas.xmlsoap.org/soap/encoding/:arrayType',
          'wsdl:arrayType' => 'tns:EstructuraDetalle[]'
        )
      ),'tns:EstructuraDetalle');
      
$server->register("getVenta",
    array("id" => "xsd:integer","idsucursal" => "xsd:integer"),
    array("return" => "tns:ArregloDeEstructuras"),
    "urn:venta",
    "urn:venta#getVenta",
    "rpc",
    "literal",
    "Nos da una lista de venta por id");

$server->register("getVentaDetalle",
    array("id" => "xsd:integer","idsucursal" => "xsd:integer"),
    array("return" => "tns:ArregloDeEstructurasDetalle"),
    "urn:venta",
    "urn:venta#getVentaDetalle",
    "rpc",
    "literal",
    "Nos da una lista del detalle de la venta por id");

//Establecer servicio        
if (isset($HTTP_RAW_POST_DATA)) { 
    $input = $HTTP_RAW_POST_DATA; 
}else{ 
    $input = implode("\r\n", file('php://input')); 
}
   
$server->service($input);
?>