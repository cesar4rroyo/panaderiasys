<?php
include_once __DIR__ . "/../cado/AccesoPostgres.php";

class mdlSerie  extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function listarSeries() {
        $sql = "SELECT * FROM serie mr WHERE mr.estado_serie = 'N' ORDER BY mr.nombre_serie";
        $result = AccesoPostgres::obtener($sql,array());
        $series = array();
        while ($fila = pg_fetch_object($result)) {
            $series[] = array(
                "id_serie"=>$fila->id_serie,
                "nombre_serie"=>$fila->nombre_serie
            );
        }
        return $series;
    }
    
    public function insertarSerie($nombre,$correlativo,$id_empresa,$tipo_doc) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO serie(numero_serie,correlativo_serie,id_empresa,tipo_documento) VALUES ($1,$2,$3,$4)";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$correlativo,$id_empresa,$tipo_doc));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarSerie($id_serie,$nombre) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE serie SET nombre_serie = $1 WHERE id_serie = $2";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$id_serie));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarSerie2($id_serie) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE serie SET correlativo_serie = correlativo_serie + 1 WHERE id_serie = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_serie));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarSerie($id_serie) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE serie SET estado_serie = 'A' WHERE id_serie = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_serie));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verSerie($id_serie) {
        $sql = "SELECT * FROM serie mr WHERE id_serie = $1";
        $result = AccesoPostgres::obtener($sql,array($id_serie));
        $serie = array();
        while ($fila = pg_fetch_object($result)) {
            $serie = array(
                "id_serie"=>$fila->id_serie,
                "numero_serie"=>$fila->numero_serie,
                "correlativo_serie"=>$fila->correlativo_serie,
                "id_empresa"=>$fila->id_empresa,
                "tipo_documento"=>$fila->tipo_documento,
                "estado_serie"=>$fila->estado_serie
            );
        }
        return $serie;
    }
    
    public function verSerie2($direccion) {
        $sql = "SELECT * FROM serie mr WHERE direccion_serie = $1";
        $result = AccesoPostgres::obtener($sql,array($direccion));
        $serie = array();
        while ($fila = pg_fetch_object($result)) {
            $serie = array(
                "id_serie"=>$fila->id_serie,
                "direccion_serie"=>$fila->direccion_serie,
                "id_empresa"=>$fila->id_empresa,
            );
        }
        return $serie;
    }
    
    public function listarSeries2($nombre,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM serie mr";
        $sql_count = "SELECT count(DISTINCT mr.id_serie) as numero_filas FROM serie mr";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mr.nombre_serie";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_serie = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND mr.nombre_serie ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $series = array();
        while ($fila = pg_fetch_object($result)) {
            $series[] = array(
                "id_serie"=>$fila->id_serie,
                "nombre_serie"=>$fila->nombre_serie
            );
        }
        return array($series,$numero_filas);
    }
    
    public function listarSeries3($id_empresa,$tipo_doc) {
        $sql = "SELECT * FROM serie mr WHERE mr.estado_serie = 'N' AND mr.id_empresa = $1 AND mr.tipo_documento = $2 ORDER BY mr.numero_serie";
        $result = AccesoPostgres::obtener($sql,array($id_empresa,$tipo_doc));
        $series = array();
        while ($fila = pg_fetch_object($result)) {
            $series[] = array(
                "id_serie"=>$fila->id_serie,
                "numero_serie"=>$fila->numero_serie,
                "correlativo_serie"=>$fila->correlativo_serie,
                "id_empresa"=>$fila->id_empresa,
                "tipo_documento"=>$fila->tipo_documento,
                "estado_serie"=>$fila->estado_serie
            );
        }
        return $series;
    }
    
    public function verSeries3($id_empresa,$tipo_doc,$serihoy) {
        $sql = "SELECT * FROM serie mr WHERE mr.estado_serie = 'N' AND mr.id_empresa = $1 AND mr.tipo_documento = $2 AND mr.numero_serie = $3 ORDER BY mr.numero_serie";
        $result = AccesoPostgres::obtener($sql,array($id_empresa,$tipo_doc,$serihoy));
        $series = array();
        while ($fila = pg_fetch_object($result)) {
            $series = array(
                "id_serie"=>$fila->id_serie,
                "numero_serie"=>$fila->numero_serie,
                "correlativo_serie"=>$fila->correlativo_serie,
                "id_empresa"=>$fila->id_empresa,
                "tipo_documento"=>$fila->tipo_documento,
                "estado_serie"=>$fila->estado_serie
            );
        }
        return $series;
    }
    
}
