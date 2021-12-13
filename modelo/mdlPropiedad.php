<?php
include_once __DIR__ . "/../cado/AccesoPostgres.php";

class mdlPropiedad extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function actualizarPropiedad($id_propiedad,$nombre,$valor){
        AccesoPostgres::iniciarTransaccion();
        $parametros = array($nombre,$valor);
        $n = 3;
        $sql = "UPDATE propiedad SET nombre_propiedad = $1,valor_propiedad = $2";
        $sql.= " WHERE id_propiedad = $$n";
        $parametros[] = $id_propiedad;
        $result = AccesoPostgres::ejecutar($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verPropiedad($idpropiedad) {
        $sql = "SELECT pp.* FROM propiedad pp";
        $sql_where = " WHERE pp.id_propiedad = $1";
        $sql = $sql . $sql_where;
        $result = AccesoPostgres::obtener($sql,array($idpropiedad));
        $index = -1;
        $ultimo_id = 0;
        while ($fila = pg_fetch_object($result)) {
            if($ultimo_id != $fila->id_propiedad){
                $index ++;
                $ultimo_id = $fila->id_propiedad;
                $propiedades[$index] = array(
                    "id_propiedad"=>$fila->id_propiedad,
                    "codigo_propiedad"=>$fila->codigo_propiedad,
                    "nombre_propiedad"=>$fila->nombre_propiedad,
                    "valor_propiedad"=>$fila->valor_propiedad,
                    "estado_propiedad"=>$fila->estado_propiedad
                );
            }
        }
        return $propiedades[0];
    }
    
    public function verPropiedad2($codigo) {
        $sql = "SELECT pp.* FROM propiedad pp";
        $sql_where = " WHERE pp.codigo_propiedad = $1";
        $sql = $sql . $sql_where;
        $result = AccesoPostgres::obtener($sql,array($codigo));
        $index = -1;
        $ultimo_id = 0;
        while ($fila = pg_fetch_object($result)) {
            if($ultimo_id != $fila->id_propiedad){
                $index ++;
                $ultimo_id = $fila->id_propiedad;
                $propiedades[$index] = array(
                    "id_propiedad"=>$fila->id_propiedad,
                    "codigo_propiedad"=>$fila->codigo_propiedad,
                    "nombre_propiedad"=>$fila->nombre_propiedad,
                    "valor_propiedad"=>$fila->valor_propiedad
                );
            }
        }
        return $propiedades[0];
    }
    
    public function listarTiposReglas() {
        $sql = "SELECT * FROM propiedad pp";
        $sql_where = " WHERE pp.codigo_propiedad LIKE 'REGTAS-%'";
        $sql_order = " ORDER BY pp.id_propiedad";
        $n=1;
        $parametros = array();
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $propiedades = array();
        while ($fila = pg_fetch_object($result)) {
            $propiedades[] = array(
                "id_propiedad"=>$fila->id_propiedad,
                "codigo_propiedad"=>$fila->codigo_propiedad,
                "nombre_propiedad"=>$fila->nombre_propiedad,
                "valor_propiedad"=>$fila->valor_propiedad
            );
        }
        return $propiedades;
    }
    
    public function listarPropiedad2($nombre,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM propiedad pp ";
        $sql_count = "SELECT count(DISTINCT pp.id_propiedad) as numero_filas FROM propiedad pp";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY pp.nombre_propiedad";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE TRUE";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND pp.nombre_propiedad ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $propiedades = array();
        while ($fila = pg_fetch_object($result)) {
            $propiedades[] = array(
                "id_propiedad"=>$fila->id_propiedad,
                "codigo_propiedad"=>$fila->codigo_propiedad,
                "nombre_propiedad"=>$fila->nombre_propiedad,
                "valor_propiedad"=>$fila->valor_propiedad,
                "estado_propiedad"=>$fila->estado_propiedad
            );
        }
        return array($propiedades,$numero_filas);
    }
    
}
