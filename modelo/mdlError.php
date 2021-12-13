<?php

include_once __DIR__ . "/../cado/AccesoPostgres.php";

class mdlError  extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function listarErrores($id_solicitud) {
        $sql = "SELECT * FROM error er WHERE er.id_solicitud = '$id_solicitud' ORDER BY er.fecha_error";
        $result = AccesoPostgres::obtener($sql,array());
        $solicitudes = array();
        while ($fila = pg_fetch_object($result)) {
            $solicitudes[] = array(
                "id_solicitud"=>$fila->id_solicitud,
                "codigo_error"=>$fila->codigo_error,
                "fecha_error"=>$fila->fecha_error,
                "descripcion_error"=>$fila->descripcion_error,
                "tipo_error"=>$fila->tipo_error,
                "id_error"=>$fila->id_error
            );
        }
        return $solicitudes;
    }
    
    public function insertarError($id_solicitud,$fecha_error,$codigo_error,$descripcion_error,$tipo_error) {
        AccesoPostgres::iniciarTransaccion();
        $n = 0;
        $sql = "INSERT INTO error(";
        if(!empty($id_solicitud) && strlen($id_solicitud)>0 && $id_solicitud>0){ $sql.= "id_solicitud";$parametros[]=$id_solicitud;$n++;}
        if(!empty($fecha_error) && strlen($fecha_error)>0){ $sql.= ",fecha_error";$parametros[]=$fecha_error;$n++;}
        if(!empty($codigo_error) && strlen($codigo_error)>0){ $sql.= ",codigo_error";$parametros[]=$codigo_error;$n++;}
        if(!empty($descripcion_error) && strlen($descripcion_error)>0){ $sql.= ",descripcion_error";$parametros[]=$descripcion_error;$n++;}
        if(!empty($tipo_error) && strlen($tipo_error)>0){ $sql.= ",tipo_error";$parametros[]=$tipo_error;$n++;}
        
        $sql.= ") VALUES (";
        for ($i = 1; $i <= $n; $i++) {
            if($i==$n){
                $sql.= "$".$i;
            }else{
                $sql.= "$".$i.",";
            }
        }
        $sql.= ")";
        //throw new Exception(json_encode(array($sql,$parametros)));
        $result = AccesoPostgres::ejecutar($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    
}

