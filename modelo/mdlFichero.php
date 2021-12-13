<?php
include_once '../cado/AccesoPostgres.php';

class mdlFichero  extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function listarFicheros($id_solicitud) {
        $sql = "SELECT * FROM fichero mn WHERE mn.id_solicitud = $1 ORDER BY mn.fechahora_fichero";
        $result = AccesoPostgres::obtener($sql,array($id_solicitud));
        $ficheros = array();
        while ($fila = pg_fetch_object($result)) {
            $ficheros[] = array(
                "id_fichero"=>$fila->id_fichero,
                "nombre_fichero"=>$fila->nombre_fichero,
                "fechahora_fichero"=>$fila->fechahora_fichero,
                "direccion_fichero"=>$fila->direccion_fichero,
                "extension_fichero"=>$fila->extension_fichero,
                "id_solicitud"=>$fila->id_solicitud
            );
        }
        return $ficheros;
    }
    
    public function insertarFichero($nombre,$orden) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO fichero(nombre_fichero,orden_fichero) VALUES ($1,$2)";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$orden));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarFichero($id_fichero,$nombre,$orden) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE fichero SET nombre_fichero = $1, orden_fichero = $2 WHERE id_fichero = $3";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$orden,$id_fichero));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarFichero($id_fichero) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE fichero SET estado_fichero = 'A' WHERE id_fichero = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_fichero));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verFichero($id_fichero) {
        $sql = "SELECT * FROM fichero mn WHERE id_fichero = $1 ORDER BY mn.nombre_fichero";
        $result = AccesoPostgres::obtener($sql,array($id_fichero));
        $fichero = array();
        while ($fila = pg_fetch_object($result)) {
            $fichero = array(
                "id_fichero"=>$fila->id_fichero,
                "nombre_fichero"=>$fila->nombre_fichero,
                "orden_fichero"=>$fila->orden_fichero
            );
        }
        return $fichero;
    }
    
    public function listarFicheros2($nombre,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM fichero mn";
        $sql_count = "SELECT count(DISTINCT mn.id_fichero) as numero_filas FROM fichero mn";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mn.nombre_fichero";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_fichero = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND mn.nombre_fichero ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $ficheros = array();
        while ($fila = pg_fetch_object($result)) {
            $ficheros[] = array(
                "id_fichero"=>$fila->id_fichero,
                "nombre_fichero"=>$fila->nombre_fichero,
                "orden_fichero"=>$fila->orden_fichero
            );
        }
        return array($ficheros,$numero_filas);
    }
    
}
