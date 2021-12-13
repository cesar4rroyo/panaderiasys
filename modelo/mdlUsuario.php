<?php
include_once '../cado/AccesoPostgres.php';

class mdlUsuario extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function actualizarTelefonosCorreos($id_persona,$telefonos,$correos) {
        $sql = "DELETE FROM telefono WHERE id_persona = $1";
        $result = AccesoPostgres::obtener($sql,array($id_persona));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }
        $sql = "DELETE FROM correo WHERE id_persona = $1";
        $result = AccesoPostgres::obtener($sql,array($id_persona));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }
        if(!empty($telefonos) || count($telefonos)>0){
            foreach ($telefonos as $telefono) {
                if(strlen(trim($telefono))>0){
                    $sql = "INSERT INTO telefono(numero_telefono,id_persona) VALUES ($1,$2)";
                    $result = AccesoPostgres::obtener($sql,array($telefono,$id_persona));
                    if(!$result){
                        AccesoPostgres::abortarTransaccion();
                        throw new Exception("ERROR EN LA CONSULTA");
                    }
                }
            }
        }
        if(!empty($correos) || count($correos)>0){
            foreach ($correos as $correo) {
                if(strlen(trim($correo))>0){
                    $sql = "INSERT INTO correo(direccion_correo,id_persona) VALUES ($1,$2)";
                    $result = AccesoPostgres::obtener($sql,array($correo,$id_persona));
                    if(!$result){
                        AccesoPostgres::abortarTransaccion();
                        throw new Exception("ERROR EN LA CONSULTA");
                    }
                }
            }
        }
    }
    
    public function insertarPersonaUsuario($nombres,$apellidos,$dni,$fecnac,$direccion,$departamento,$provincia,$distrito,$telefonos,$correos,$user, $pass, $id_perfil){
        AccesoPostgres::iniciarTransaccion();
        $parametros = array("N",$nombres,$apellidos,$dni);
        $n = 4;
        $sql = "INSERT INTO persona(tipo_persona,nombres_persona,apellidos_persona,dni_persona";
        if(!empty($fecnac) && strlen($fecnac)>0){ $sql.= ",fecha_nacimiento_persona";$parametros[]=$fecnac;$n++;}
        if(!empty($direccion) && strlen($direccion)>0){ $sql.= ",direccion_persona";$parametros[]=$direccion;$n++;}
        if(!empty($departamento) && strlen($departamento)>0 && $departamento>0){ $sql.= ",id_departamento";$parametros[]=$departamento;$n++;}
        if(!empty($provincia) && strlen($provincia)>0 && $provincia>0){ $sql.= ",id_provincia";$parametros[]=$provincia;$n++;}
        if(!empty($distrito) && strlen($distrito)>0 && $distrito>0){ $sql.= ",id_distrito";$parametros[]=$distrito;$n++;}
        $sql.= ") VALUES (";
        for ($i = 1; $i <= $n; $i++) {
            if($i==$n){
                $sql.= "$".$i;
            }else{
                $sql.= "$".$i.",";
            }
        }
        $sql.= ") RETURNING id_persona";
        $result = AccesoPostgres::obtener($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            $id_persona = 0;
            while ($fila = pg_fetch_object($result)) {
                $id_persona = $fila->id_persona;
            }
            if ($id_persona>0) {
                $this->actualizarTelefonosCorreos($id_persona, $telefonos, $correos);
                $this->insertarUsuario($user, $pass, $id_perfil, $id_persona);
                AccesoPostgres::finalizarTransaccion();
            }else{
                AccesoPostgres::abortarTransaccion();
                throw new Exception("ERROR EN LA CONSULTA");
            }
        }
    }
    
    public function insertarUsuarioPersona($id_persona,$nombres,$apellidos,$dni,$fecnac,$direccion,$departamento,$provincia,$distrito,$telefonos,$correos,$user, $pass, $id_perfil){
        AccesoPostgres::iniciarTransaccion();
        $parametros = array($nombres,$apellidos,$dni);
        $n = 4;
        $sql = "UPDATE persona SET nombres_persona = $1,apellidos_persona = $2,dni_persona = $3";
        if(!empty($fecnac) && strlen($fecnac)>0){ $sql.= ",fecha_nacimiento_persona = $$n";$parametros[]=$fecnac;$n++;}else{ $sql.= ",fecha_nacimiento_persona = NULL";}
        if(!empty($direccion) && strlen($direccion)>0){ $sql.= ",direccion_persona = $$n";$parametros[]=$direccion;$n++;}else{ $sql.= ",direccion_persona = NULL";}
        if(!empty($departamento) && strlen($departamento)>0 && $departamento>0){ $sql.= ",id_departamento = $$n";$parametros[]=$departamento;$n++;}else{ $sql.= ",id_departamento = NULL";}
        if(!empty($provincia) && strlen($provincia)>0 && $provincia>0){ $sql.= ",id_provincia = $$n";$parametros[]=$provincia;$n++;}else{ $sql.= ",id_provincia = NULL";}
        if(!empty($distrito) && strlen($distrito)>0 && $distrito>0){ $sql.= ",id_distrito = $$n";$parametros[]=$distrito;$n++;}else{ $sql.= ",id_distrito = NULL";}
        $sql.= " WHERE id_persona = $$n";
        $parametros[] = $id_persona;
        $result = AccesoPostgres::ejecutar($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            $this->actualizarTelefonosCorreos($id_persona, $telefonos, $correos);
            $this->insertarUsuario($user, $pass, $id_perfil, $id_persona);
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function insertarUsuario($user,$pass,$id_empresa) {
        $parametros = array($user,$pass);
        $n = count($parametros);
        $sql = "INSERT INTO usuario(user_usuario,pass_usuario";
        if(!empty($id_empresa) && strlen($id_empresa)>0 && $id_empresa>0){$sql .= ",id_empresa";$parametros[]=$id_empresa;$n++;}
        $sql.= ") VALUES (";
        for ($i = 1; $i <= $n; $i++) {
            if($i==$n){
                $sql.= "$".$i;
            }else{
                $sql.= "$".$i.",";
            }
        }
        $sql.= ") RETURNING id_usuario";
        $result = AccesoPostgres::obtener($sql, $parametros);
        if (!$result) {
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            $id_usuario = 0;
            while ($fila = pg_fetch_object($result)) {
                $id_usuario = $fila->id_usuario;
            }
            if ($id_usuario>0) {
                AccesoPostgres::finalizarTransaccion();
            }else{
                AccesoPostgres::abortarTransaccion();
                throw new Exception("ERROR EN LA CONSULTA");
            }
        }
    }
    
    public function actualizarUsuario($id_usuario,$user, $pass, $id_empresa){
        AccesoPostgres::iniciarTransaccion();
        $parametros = array($user, $pass);
        $n = count($parametros) + 1;
        $sql = "UPDATE usuario SET user_usuario = $1, pass_usuario = $2";
        if(!empty($id_empresa) && strlen($id_empresa)>0){ $sql.= ",id_empresa = $$n";$parametros[]=$id_empresa;$n++;}else{ $sql.= ",id_empresa = NULL";}
        $sql.= " WHERE id_usuario = $$n";
        $parametros[] = $id_usuario;
        $result = AccesoPostgres::ejecutar($sql,$parametros);
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarUsuario($id_usuario){
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE usuario SET estado_usuario = 'A' WHERE id_usuario = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_usuario));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function cambiarEstadoUsuario($id_usuario,$estado){
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE usuario SET estado_usuario = $2 WHERE id_usuario = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_usuario,$estado));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verUsuario($id_usuario) {
        $sql = "SELECT us.* FROM usuario us";
        $sql_where = " WHERE us.id_usuario = $1";
        $sql = $sql . $sql_where;
        $result = AccesoPostgres::obtener($sql,array($id_usuario));
        $index = -1;
        $ultimo_id = 0;
        while ($fila = pg_fetch_object($result)) {
            if($ultimo_id != $fila->id_usuario){
                $index ++;
                $ultimo_id = $fila->id_usuario;
                $usuarios[$index] = array(
                    "id_usuario"=>$fila->id_usuario,
                    "user_usuario"=>$fila->user_usuario,
                    "pass_usuario"=>$fila->pass_usuario,
                    "estado_usuario"=>$fila->estado_usuario,
                    "id_empresa"=>$fila->id_empresa,
                );
            }
        }
        return $usuarios[0];
    }
    
    public function listarUsuarios($user,$nombre,$nrodoc,$tipo,$estado,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT us.*,emp.*,pf.nombre_perfil,pf.id_perfil"
                . " FROM usuario us"
                . " LEFT JOIN empresa emp ON us.id_empresa = emp.id_empresa"
                . " LEFT JOIN perfil_usuario pfus ON pfus.id_usuario = us.id_usuario"
                . " LEFT JOIN perfil pf ON pf.id_perfil = pfus.id_perfil";
        $sql_count = "SELECT count(DISTINCT us.id_usuario) as numero_filas"
                . " FROM usuario us"
                . " LEFT JOIN empresa emp ON us.id_empresa = emp.id_empresa"
                . " LEFT JOIN perfil_usuario pfus ON pfus.id_usuario = us.id_usuario"
                . " LEFT JOIN perfil pf ON pf.id_perfil = pfus.id_perfil";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY emp.nombre_empresa";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE us.estado_usuario <> 'A'";
        if(!empty($user) && strlen($user)>0){ $sql_where.= " AND us.user_usuario ILIKE $$n";$parametros[] = "%$user%";$n++;}
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND (ps.nombres_persona ILIKE $$n OR ps.apellidos_persona ILIKE $$n)";$parametros[] = "%$nombre%";$n++;}
        if(!empty($nrodoc) && strlen($nrodoc)>0){ $sql_where.= " AND ps.dni_persona ILIKE $$n";$parametros[] = "%$nrodoc%";$n++;}
        if($tipo!=""){ $sql_where.= " AND pf.tipo_usuario = $$n";$parametros[] = $tipo;$n++;}
        if($estado!=""){ $sql_where.= " AND us.estado_usuario = $$n";$parametros[] = $estado;$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $usuarios = array();
        $index = -1;
        $ultimo_id = 0;
        while ($fila = pg_fetch_object($result)) {
            if($ultimo_id != $fila->id_usuario){
                $index ++;
                $ultimo_id = $fila->id_usuario;
                $usuarios[$index] = array(
                    "id_usuario"=>$fila->id_usuario,
                    "user_usuario"=>$fila->user_usuario,
                    "nombre_perfil"=>"",
                    "nombre_perfiles"=>array(),
                    "id_perfil"=>array(),
                    "estado_usuario"=>$fila->estado_usuario,
                    "id_empresa"=>$fila->id_empresa,
                    "nombre_empresa"=>$fila->nombre_empresa,
                    "razon_empresa"=>$fila->razon_empresa,
                    "ruc_empresa"=>$fila->ruc_empresa,
                    "password_empresa"=>$fila->password_empresa,
                    "username_sunat"=>$fila->username_sunat,
                    "password_sunat"=>$fila->password_sunat,
                    "nombrecomercial_empresa"=>$fila->nombrecomercial_empresa,
                    "domiciliofiscal_empresa"=>$fila->domiciliofiscal_empresa,
                );
            }
            if(strlen($fila->id_perfil)>0){
                if(!in_array($fila->id_perfil, $usuarios[$index]["id_perfil"])){
                    $usuarios[$index]["nombre_perfil"] = $usuarios[$index]["nombre_perfil"].$fila->nombre_perfil.", ";
                    $usuarios[$index]["id_perfil"][] = $fila->id_perfil;
                    $usuarios[$index]["nombre_perfiles"][] = $fila->nombre_perfil;
                }
            }
        }
        return array($usuarios,$numero_filas);
    }
    
    public function login($user,$pass) {
        $sql = "SELECT /*us.*,ps.*,pf.*,tf.numero_telefono,co.direccion_correo,cl.id_cliente*/ * FROM usuario us"
                . " LEFT JOIN empresa em ON em.id_empresa = us.id_empresa"
                . " INNER JOIN perfil_usuario pfus ON pfus.id_usuario = us.id_usuario"
                . " INNER JOIN perfil pf ON pf.id_perfil = pfus.id_perfil"
                . " WHERE us.estado_usuario = 'N' AND us.pass_usuario = $2 AND ("
                . " (us.user_usuario = $1) OR"
                . " (em.ruc_empresa = $1) )";
        $result = AccesoPostgres::obtener($sql,array($user,$pass));
        $usuario = null;
        while ($fila = pg_fetch_object($result)) {
            if(empty($usuario)){
                $usuario = array(
                    "id_usuario"=>$fila->id_usuario,
                    "user_usuario"=>$fila->user_usuario,
                    "pass_usuario"=>$fila->pass_usuario,
                    "nombre_perfil"=>"",
                    "ids_perfiles"=>array(),
                    "nombre_perfiles"=>array(),
                    "tipos_usuario"=>array(),
                    "id_empresa"=>$fila->id_empresa,
                    "id_persona"=>$fila->id_persona,
                    "direccion_persona"=>$fila->direccion_persona,
                    "tipo_persona"=>$fila->tipo_persona,
                    "nombres_persona"=>$fila->nombres_persona,
                    "apellidos_persona"=>$fila->apellidos_persona,
                    "dni_persona"=>$fila->dni_persona,
                    "fecha_nacimiento_persona"=>$fila->fecha_nacimiento_persona,
                    "id_departamento"=>$fila->id_departamento,
                    "id_provincia"=>$fila->id_provincia,
                    "id_distrito"=>$fila->id_distrito,
                    "telefono"=>"",
                    "telefonos"=>array(),
                    "correo"=>"",
                    "correos"=>array(),
                    "id_cliente"=>$fila->id_cliente,
                );
            }
            if(strlen($fila->nombre_perfil)>0){
                if(!in_array($fila->tipo_usuario, $usuario["tipos_usuario"])){
                    $usuario["nombre_perfil"] = $usuario["nombre_perfil"].$fila->nombre_perfil.", ";
                    $usuario["ids_perfiles"][] = $fila->id_perfil;
                    $usuario["nombre_perfiles"][] = $fila->nombre_perfil;
                    $usuario["tipos_usuario"][] = $fila->tipo_usuario;
                }
            }
        }
        return $usuario;
    }
}
