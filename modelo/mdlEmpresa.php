<?php
include_once __DIR__ . "/../cado/AccesoPostgres.php";

class mdlEmpresa  extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function listarEmpresas() {
        $sql = "SELECT * FROM empresa mr WHERE mr.estado_empresa <> 'A' ORDER BY mr.nombre_empresa";
        $result = AccesoPostgres::obtener($sql,array());
        $empresas = array();
        while ($fila = pg_fetch_object($result)) {
            $empresas[] = array(
                    "id_empresa"=>$fila->id_empresa,
                    "nombre_empresa"=>$fila->nombre_empresa,
                    "razon_empresa"=>$fila->razon_empresa,
                    "ruc_empresa"=>$fila->ruc_empresa,
                    "password_empresa"=>$fila->password_empresa,
                    "modo_autenticacion"=>$fila->modo_autenticacion,
                    "username_sunat"=>$fila->username_sunat,
                    "password_sunat"=>$fila->password_sunat,
                    "nombrecomercial_empresa"=>$fila->nombrecomercial_empresa,
                    "domiciliofiscal_empresa"=>$fila->domiciliofiscal_empresa,
                    "estado_empresa"=>$fila->estado_empresa,
            );
        }
        return $empresas;
    }
    
    public function insertarEmpresa($nombre) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO empresa(nombre_empresa) VALUES ($1)";
        $result = AccesoPostgres::ejecutar($sql,array($nombre));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarEmpresa($id_empresa,$nombre) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE empresa SET nombre_empresa = $1 WHERE id_empresa = $2";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$id_empresa));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarEmpresa($id_empresa) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE empresa SET estado_empresa = 'A' WHERE id_empresa = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_empresa));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verEmpresa($id_empresa) {
        $sql = "SELECT * FROM empresa mr WHERE id_empresa = $1";
        $result = AccesoPostgres::obtener($sql,array($id_empresa));
        $empresa = array();
        while ($fila = pg_fetch_object($result)) {
            $empresa = array(
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa,
                "razon_empresa"=>$fila->razon_empresa,
                "ruc_empresa"=>$fila->ruc_empresa,
                //"password_empresa"=>$fila->password_empresa,
                "modo_autenticacion"=>$fila->modo_autenticacion,
                "username_sunat"=>$fila->username_sunat,
                "password_sunat"=>$fila->password_sunat,
                "nombrecomercial_empresa"=>$fila->nombrecomercial_empresa,
                "domiciliofiscal_empresa"=>$fila->domiciliofiscal_empresa,
                "ubigeo_empresa"=>$fila->ubigeo_empresa,
                "urbanizacion_direccion"=>$fila->urbanizacion_direccion,
                "departamento_direccion"=>$fila->departamento_direccion,
                "provincia_direccion"=>$fila->provincia_direccion,
                "distrito_direccion"=>$fila->distrito_direccion
            );
        }
        return $empresa;
    }
    
    public function verEmpresa2($id_empresa,$password) {
        $sql = "SELECT * FROM empresa mr WHERE id_empresa = $1 AND password_empresa = $2";
        $result = AccesoPostgres::obtener($sql,array($id_empresa,$password));
        $empresa = array();
        while ($fila = pg_fetch_object($result)) {
            $empresa = array(
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa,
                "razon_empresa"=>$fila->razon_empresa,
                "ruc_empresa"=>$fila->ruc_empresa,
                //"password_empresa"=>$fila->password_empresa,
                "modo_autenticacion"=>$fila->modo_autenticacion,
                "username_sunat"=>$fila->username_sunat,
                "password_sunat"=>$fila->password_sunat
            );
        }
        return $empresa;
    }
    
    public function verEmpresa3($ruc_empresa,$password) {
        $sql = "SELECT * FROM empresa mr WHERE ruc_empresa = $1 AND password_empresa = $2";
        $result = AccesoPostgres::obtener($sql,array($ruc_empresa,$password));
        $empresa = array();
        while ($fila = pg_fetch_object($result)) {
            $empresa = array(
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa,
                "razon_empresa"=>$fila->razon_empresa,
                "ruc_empresa"=>$fila->ruc_empresa,
                //"password_empresa"=>$fila->password_empresa,
                "modo_autenticacion"=>$fila->modo_autenticacion,
                "username_sunat"=>$fila->username_sunat,
                "password_sunat"=>$fila->password_sunat,
                "nombrecomercial_empresa"=>$fila->nombrecomercial_empresa,
                "domiciliofiscal_empresa"=>$fila->domiciliofiscal_empresa,
                "ubigeo_empresa"=>$fila->ubigeo_empresa,
                "urbanizacion_direccion"=>$fila->urbanizacion_direccion,
                "departamento_direccion"=>$fila->departamento_direccion,
                "provincia_direccion"=>$fila->provincia_direccion,
                "distrito_direccion"=>$fila->distrito_direccion
            );
        }
        return $empresa;
    }
    
    public function listarEmpresas2($nombre,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM empresa mr";
        $sql_count = "SELECT count(DISTINCT mr.id_empresa) as numero_filas FROM empresa mr";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mr.nombre_empresa";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_empresa = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND mr.nombre_empresa ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $empresas = array();
        while ($fila = pg_fetch_object($result)) {
            $empresas[] = array(
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa
            );
        }
        return array($empresas,$numero_filas);
    }
    
    public function cambiarEstadoEmpresa($id_empresa,$estado){
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE empresa SET estado_empresa = $2 WHERE id_empresa = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_empresa,$estado));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
}
