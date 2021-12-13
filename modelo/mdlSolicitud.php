<?php
include_once __DIR__ . "/../cado/AccesoPostgres.php";

class mdlSolicitud  extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function listarSolicitudes() {
        $sql = "SELECT * FROM solicitud mr WHERE mr.estado_solicitud = 'N' ORDER BY mr.nombre_solicitud";
        $result = AccesoPostgres::obtener($sql,array());
        $solicitudes = array();
        while ($fila = pg_fetch_object($result)) {
            $solicitudes[] = array(
                "id_solicitud"=>$fila->id_solicitud,
                "nombre_solicitud"=>$fila->nombre_solicitud
            );
        }
        return $solicitudes;
    }
    
    public function insertarSolicitud($tipo,$data,$user,$id_sesion,$tipodoc,$serie,$correlativo,$docCliente='',$nombreCliente='',$totalDoc=0,$direccion='') {
        AccesoPostgres::iniciarTransaccion();
        $parametros = array($tipo,$data,$user,$tipodoc,$serie,$correlativo);
        $n = count($parametros);
        $sql = "INSERT INTO solicitud(tipo_solicitud,data_solicitud,username_solicitud,tipo_documento,serie,correlativo";
        if(!empty($id_sesion) && strlen($id_sesion)>0 && $id_sesion>0){ $sql.= ",id_sesion";$parametros[]=$id_sesion;$n++;}
        if(!empty($docCliente) && strlen($docCliente)>0){ $sql.= ",doc_cliente";$parametros[]=$docCliente;$n++;}
        if(!empty($nombreCliente) && strlen($nombreCliente)>0){ $sql.= ",nombre_cliente";$parametros[]=$nombreCliente;$n++;}
        if(!empty($totalDoc) && strlen($totalDoc)>0){ $sql.= ",total_doc";$parametros[]=$totalDoc;$n++;}
        if(!empty($direccion) && strlen($direccion)>0){ $sql.= ",direccion_cliente";$parametros[]=$direccion;$n++;}
        
        $sql.= ") VALUES (";
        for ($i = 1; $i <= $n; $i++) {
            if($i==$n){
                $sql.= "$".$i;
            }else{
                $sql.= "$".$i.",";
            }
        }
        $sql.= ") RETURNING id_solicitud";
        //throw new Exception(json_encode(array($sql,$parametros)));
        $result = AccesoPostgres::ejecutar($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            $id_solicitud = 0;
            while ($fila = pg_fetch_object($result)) {
                $id_solicitud = $fila->id_solicitud;
            }
            if ($id_solicitud>0) {
                AccesoPostgres::finalizarTransaccion();
                return $id_solicitud;
            }else{
                AccesoPostgres::abortarTransaccion();
                throw new Exception("ERROR EN LA CONSULTA");
            }
        }
    }
    
    public function actualizarSolicitud2($id_solicitud,$id_sesion="",$estado="",$ticket="",$id_solicitudservidor="",$errorcode="",$id_solicitud_servidor='N') {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE solicitud SET id_solicitud = id_solicitud";
        $parametros = array();
        $n = 1;
        if(!empty($id_sesion) && strlen($id_sesion)>0 && $id_sesion>0){ $sql .= ",id_sesion = $$n";$n++;$parametros[]=$id_sesion;}
        if(!empty($estado) && strlen(trim($estado))>0){ $sql .= ",estado_solicitud = $$n";$n++;$parametros[]=$estado;}
        if(!empty($ticket) && strlen(trim($ticket))>0){ $sql .= ",ticket_solicitud = $$n";$n++;$parametros[]=$ticket;}
        if(!empty($id_solicitudservidor) && strlen($id_solicitudservidor)>0 && $id_solicitudservidor>0){ $sql .= ",id_solicitud_servidor = $$n";$n++;$parametros[]=$id_solicitudservidor;}
        if(!empty($errorcode) && strlen($errorcode)>0 && $errorcode>0){ $sql .= ",error_code = $$n";$n++;$parametros[]=$errorcode;}
        if($id_solicitud_servidor=="N"){
            $sql .= " WHERE id_solicitud = $$n";
        }else{
            $sql .= " WHERE id_solicitud_servidor = $$n";
        }
        $parametros[] = $id_solicitud;
        $result = AccesoPostgres::ejecutar($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            foreach ($ficheros as $fichero) {
                $result = AccesoPostgres::ejecutar("INSERT INTO fichero (nombre_fichero,direccion_fichero,extension_fichero,id_solicitud) VALUES ($1,$2,$3,$4)", 
                        array($fichero[0],$fichero[1],$fichero[2],$id_solicitud));
                if(!$result){
                    AccesoPostgres::abortarTransaccion();
                    throw new Exception("ERROR EN LA CONSULTA");
                }
            }
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    
    public function actualizarSolicitud($id_solicitud,$nombre="",$fechaenvio="",$fecharespuesta="",$estado="",$ficheros=array(),$id_solicitud_servidor='N') {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE solicitud SET id_solicitud = id_solicitud";
        $parametros = array();
        $n = 1;
        if(!empty($nombre) && strlen(trim($nombre))>0){ $sql .= ",nombre_solicitud = $$n";$n++;$parametros[]=$nombre;}
        if(!empty($fechaenvio) && strlen(trim($fechaenvio))>0){ $sql .= ",fechahora_envio = $$n";$n++;$parametros[]=$fechaenvio;}
        if(!empty($fecharespuesta) && strlen(trim($fecharespuesta))>0){ $sql .= ",fechahora_respuesta = $$n";$n++;$parametros[]=$fecharespuesta;}
        if(!empty($estado) && strlen(trim($estado))>0){ $sql .= ",estado_solicitud = $$n";$n++;$parametros[]=$estado;}
        if($id_solicitud_servidor=="N"){
            $sql .= " WHERE id_solicitud = $$n";
        }else{
            $sql .= " WHERE id_solicitud_servidor = $$n";
        }
        $parametros[] = $id_solicitud;
        $result = AccesoPostgres::ejecutar($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            foreach ($ficheros as $fichero) {
                $result = AccesoPostgres::ejecutar("INSERT INTO fichero (nombre_fichero,direccion_fichero,extension_fichero,id_solicitud) VALUES ($1,$2,$3,$4)", 
                        array($fichero[0],$fichero[1],$fichero[2],$id_solicitud));
                if(!$result){
                    AccesoPostgres::abortarTransaccion();
                    throw new Exception("ERROR EN LA CONSULTA");
                }
            }
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarSolicitud($id_solicitud) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE solicitud SET estado_solicitud = 'A' WHERE id_solicitud = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_solicitud));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verSolicitud($id_solicitud) {
        $sql = "SELECT * FROM solicitud mr WHERE id_solicitud = $1";
        $result = AccesoPostgres::obtener($sql,array($id_solicitud));
        $solicitud = array();
        while ($fila = pg_fetch_object($result)) {
            $solicitud = array(
                "id_solicitud"=>$fila->id_solicitud,
                "tipo_solicitud"=>$fila->tipo_solicitud,
                "data_solicitud"=>$fila->data_solicitud,
                "fechahora_solicitud"=>$fila->fechahora_solicitud,
                "fechahora_envio"=>$fila->fechahora_envio,
                "fechahora_respuesta"=>$fila->fechahora_respuesta,
                "estado_solicitud"=>$fila->estado_solicitud,
                "username_solicitud"=>$fila->username_solicitud,
                "nombre_solicitud"=>$fila->nombre_solicitud,
                "nombre_cliente"=>$fila->nombre_cliente,
                "direccion_cliente"=>$fila->direccion_cliente,
                "serie"=>$fila->serie,
                "correlativo"=>$fila->correlativo,
                "tipo_documento"=>$fila->tipo_documento,
                "ticket_solicitud"=>$fila->ticket_solicitud,
                "id_solicitud_servidor"=>$fila->id_solicitud_servidor,
                "id_sesion"=>$fila->id_sesion,
                "fechahora_sesion"=>$fila->fechahora_sesion,
                "token_sesion"=>$fila->token_sesion,
                "direccion_terminal"=>$fila->direccion_terminal,
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa,
                "total_doc"=>$fila->total_doc,
                "doc_cliente"=>$fila->doc_cliente,
            );
        }
        return $solicitud;
    }
    
    public function verSolicitud2($id_solicitud,$password) {
        $sql = "SELECT * FROM solicitud mr WHERE id_solicitud = $1 AND password_solicitud = $2";
        $result = AccesoPostgres::obtener($sql,array($id_solicitud,$password));
        $solicitud = array();
        while ($fila = pg_fetch_object($result)) {
            $solicitud = array(
                "id_solicitud"=>$fila->id_solicitud,
                "nombre_solicitud"=>$fila->nombre_solicitud,
                "razon_solicitud"=>$fila->razon_solicitud,
                "ruc_solicitud"=>$fila->ruc_solicitud,
                //"password_solicitud"=>$fila->password_solicitud,
                "modo_autenticacion"=>$fila->modo_autenticacion,
                "username_sunat"=>$fila->username_sunat,
                "password_sunat"=>$fila->password_sunat
            );
        }
        return $solicitud;
    }
    
    public function listarSolicitudes2($fecini,$fecfin,$id_empresa,$nombre,$estado,$tipodoc,$OFFSET,$LIMIT,$fecComp ="") {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT mr.*,ss.*,em.* FROM solicitud mr"
                . " LEFT JOIN sesion ss ON ss.id_sesion = mr.id_sesion"
                . " LEFT JOIN empresa em ON em.id_empresa = ss.id_empresa";
        $sql_count = "SELECT count(DISTINCT mr.id_solicitud) as numero_filas FROM solicitud mr"
                . " LEFT JOIN sesion ss ON ss.id_sesion = mr.id_sesion"
                . " LEFT JOIN empresa em ON em.id_empresa = ss.id_empresa";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mr.fechahora_solicitud DESC";
        if($LIMIT>0){ $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;}else{ $sql_limit = "";}
        $sql_where = " WHERE TRUE";
        if(!empty($fecini) && strlen($fecini)>0){ $sql_where.= " AND mr.fechahora_solicitud::date >= $$n";$parametros[] = $fecini;$n++;}
        if(!empty($fecfin) && strlen($fecfin)>0){ $sql_where.= " AND mr.fechahora_solicitud::date <= $$n";$parametros[] = $fecfin;$n++;}
        //if(!empty($id_empresa) && strlen($id_empresa)>0 && $id_empresa>0){ $sql_where.= " AND ss.id_empresa = $$n";$parametros[] = $id_empresa;$n++;}
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND mr.nombre_solicitud ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        if(!empty($fecComp) && strlen($fecComp)>0){ $sql_where.= " AND mr.data_solicitud ILIKE $$n";$parametros[] = '%"fechareferencia":"'.$fecComp.'"%';$n++;}
        if(!empty($estado) && strlen($estado)>0){ 
            $estadoArr = explode(",", $estado);
            $estado = array();
            foreach ($estadoArr as $key => $value) {
                $estado[] = "'".$value."'";
            }
            $estado = implode(",", $estado);
            $sql_where.= " AND mr.estado_solicitud IN ($estado)";
        }
        if(!empty($tipodoc) && strlen($tipodoc)>0){ $sql_where.= " AND mr.tipo_documento = $$n";$parametros[] = $tipodoc;$n++;}else{ $sql_where.= " AND mr.tipo_documento IN ('B','C','D','F')";}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        //echo $sql;exit();
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $solicitudes = array();
        while ($fila = pg_fetch_object($result)) {
            $solicitudes[] = array(
                "id_solicitud"=>$fila->id_solicitud,
                "tipo_solicitud"=>$fila->tipo_solicitud,
                "data_solicitud"=>$fila->data_solicitud,
                "fechahora_solicitud"=>$fila->fechahora_solicitud,
                "fechahora_envio"=>$fila->fechahora_envio,
                "fechahora_respuesta"=>$fila->fechahora_respuesta,
                "estado_solicitud"=>$fila->estado_solicitud,
                "username_solicitud"=>$fila->username_solicitud,
                "nombre_solicitud"=>$fila->nombre_solicitud,
                "serie"=>$fila->serie,
                "correlativo"=>$fila->correlativo,
                "tipo_documento"=>$fila->tipo_documento,
                "ticket_solicitud"=>$fila->ticket_solicitud,
                "doc_cliente"=>$fila->doc_cliente,
                "nombre_cliente"=>$fila->nombre_cliente,
                "total_doc"=>$fila->total_doc,
                "direccion_cliente"=>$fila->direccion_cliente,
                "id_solicitud_servidor"=>$fila->id_solicitud_servidor,
                "error_code"=>$fila->error_code,
                "id_sesion"=>$fila->id_sesion,
                "fechahora_sesion"=>$fila->fechahora_sesion,
                "token_sesion"=>$fila->token_sesion,
                "direccion_terminal"=>$fila->direccion_terminal,
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa,
            );
        }
        return array($solicitudes,$numero_filas);
    }
    
    
    public function listarSolicitudes3($fecini,$id_empresa,$estado,$tipodoc,$LIMIT='') {
        $sql = "SELECT mr.*,ss.*,em.* FROM solicitud mr"
                . " LEFT JOIN sesion ss ON ss.id_sesion = mr.id_sesion"
                . " LEFT JOIN empresa em ON em.id_empresa = ss.id_empresa";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mr.fechahora_solicitud DESC";
        $sql_where = " WHERE TRUE";
        //if(!empty($fecini) && strlen($fecini)>0){ $sql_where.= " AND mr.fechahora_solicitud::date = $$n";$parametros[] = $fecini;$n++;}
        if(!empty($fecini) && strlen($fecini)>0){ $sql_where.= " AND mr.data_solicitud LIKE '%\"fechaemision\":\"$fecini\"%'";}
        //if(!empty($id_empresa) && strlen($id_empresa)>0 && $id_empresa>0){ $sql_where.= " AND ss.id_empresa = $$n";$parametros[] = $id_empresa;$n++;}
        if(!empty($estado) && strlen($estado)>0){ $sql_where.= " AND mr.estado_solicitud IN ($estado)";}
        if(!empty($tipodoc) && strlen($tipodoc)>0){ $sql_where.= " AND mr.tipo_documento = ($tipodoc)";}
        if($LIMIT>0){ $sql_limit = " OFFSET 0 LIMIT ".$LIMIT;}else{ $sql_limit = "";}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $solicitudes = array();
        while ($fila = pg_fetch_object($result)) {
            $solicitudes[] = array(
                "id_solicitud"=>$fila->id_solicitud,
                "tipo_solicitud"=>$fila->tipo_solicitud,
                "data_solicitud"=>$fila->data_solicitud,
                "fechahora_solicitud"=>$fila->fechahora_solicitud,
                "fechahora_envio"=>$fila->fechahora_envio,
                "fechahora_respuesta"=>$fila->fechahora_respuesta,
                "estado_solicitud"=>$fila->estado_solicitud,
                "username_solicitud"=>$fila->username_solicitud,
                "nombre_solicitud"=>$fila->nombre_solicitud,
                "serie"=>$fila->serie,
                "correlativo"=>$fila->correlativo,
                "numeracion"=>$fila->serie."-". str_pad($fila->correlativo, 8, "0", STR_PAD_LEFT),
                "tipo_documento"=>$fila->tipo_documento,
                "ticket_solicitud"=>$fila->ticket_solicitud,
                "doc_cliente"=>$fila->doc_cliente,
                "nombre_cliente"=>$fila->nombre_cliente,
                "total_doc"=>$fila->total_doc,
                "id_sesion"=>$fila->id_sesion,
                "fechahora_sesion"=>$fila->fechahora_sesion,
                "token_sesion"=>$fila->token_sesion,
                "direccion_terminal"=>$fila->direccion_terminal,
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa,
                "id_solicitud_servidor"=>$fila->id_solicitud_servidor,
            );
        }
        return $solicitudes;
    }

    public function listarSolicitudes4($fecini,$fecfin,$id_empresa,$tipodoc) {
        $sql = "SELECT mr.*,ss.*,em.* FROM solicitud mr"
                . " LEFT JOIN sesion ss ON ss.id_sesion = mr.id_sesion"
                . " LEFT JOIN empresa em ON em.id_empresa = ss.id_empresa";
        $sql_count = "SELECT count(DISTINCT mr.id_solicitud) as numero_filas FROM solicitud mr"
                . " LEFT JOIN sesion ss ON ss.id_sesion = mr.id_sesion"
                . " LEFT JOIN empresa em ON em.id_empresa = ss.id_empresa";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mr.fechahora_solicitud asc";
        $sql_where = " WHERE mr.estado_solicitud IN ('T','B','M','X','R','S','E','P','I') AND mr.tipo_documento IN ('F','B','C','D')";
        if(!empty($fecini) && strlen($fecini)>0){ $sql_where.= " AND mr.fechahora_solicitud::date >= $$n";$parametros[] = $fecini;$n++;}
        if(!empty($fecfin) && strlen($fecfin)>0){ $sql_where.= " AND mr.fechahora_solicitud::date <= $$n";$parametros[] = $fecfin;$n++;}
        //if(!empty($id_empresa) && strlen($id_empresa)>0 && $id_empresa>0){ $sql_where.= " AND ss.id_empresa = $$n";$parametros[] = $id_empresa;$n++;}
        //if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND mr.nombre_solicitud ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        //if(!empty($estado) && strlen($estado)>0){ $sql_where.= " AND mr.estado_solicitud = $$n";$parametros[] = $estado;$n++;}
        if(!empty($tipodoc) && strlen($tipodoc)>0){ $sql_where.= " AND mr.tipo_documento = $$n";$parametros[] = $tipodoc;$n++;}else{ $sql_where.= " AND mr.tipo_documento IN ('B','C','D','F')";}
        $sql = $sql . $sql_where . $sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $solicitudes = array();
        while ($fila = pg_fetch_object($result)) {
            $solicitudes[] = array(
                "id_solicitud"=>$fila->id_solicitud,
                "tipo_solicitud"=>$fila->tipo_solicitud,
                "data_solicitud"=>$fila->data_solicitud,
                "fechahora_solicitud"=>$fila->fechahora_solicitud,
                "fechahora_envio"=>$fila->fechahora_envio,
                "fechahora_respuesta"=>$fila->fechahora_respuesta,
                "estado_solicitud"=>$fila->estado_solicitud,
                "username_solicitud"=>$fila->username_solicitud,
                "nombre_solicitud"=>$fila->nombre_solicitud,
                "serie"=>$fila->serie,
                "correlativo"=>$fila->correlativo,
                "tipo_documento"=>$fila->tipo_documento,
                "ticket_solicitud"=>$fila->ticket_solicitud,
                "doc_cliente"=>$fila->doc_cliente,
                "nombre_cliente"=>$fila->nombre_cliente,
                "total_doc"=>$fila->total_doc,
                "id_sesion"=>$fila->id_sesion,
                "fechahora_sesion"=>$fila->fechahora_sesion,
                "token_sesion"=>$fila->token_sesion,
                "direccion_terminal"=>$fila->direccion_terminal,
                "id_empresa"=>$fila->id_empresa,
                "nombre_empresa"=>$fila->nombre_empresa,
            );
        }
        return $solicitudes;
    }
    
}
