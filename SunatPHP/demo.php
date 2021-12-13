<?php
    require ("curl.php");
    require ("sunat.php");
    $cliente = new Sunat();
    $ruc = $_GET["ruc"];
    //$ruc="20441053658";
    header('Content-Type: application/json');
    //$empresa = $cliente->BuscaDatosSunat($ruc);
    echo json_encode( $cliente->BuscaDatosSunat($ruc), JSON_PRETTY_PRINT );
?>
