<?php


class AccesoPostgres {
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $pass;
    private $conexion;
    
    function __construct() {
        $this->host = "localhost";
        $this->port = "5432";
        $this->dbname = "clifacturacion";
        $this->user = "postgres";
        $this->pass = "Garzasoft-djlksjldksjlksdjlk";
        $conn_string = "host=$this->host port=$this->port dbname=$this->dbname user=$this->user password=$this->pass";
        try{
            //$this->conexion = pg_pconnect($conn_string);
            $this->conexion = pg_connect($conn_string);
        }catch (PDOException $e) {
            echo "Error:\n" . $e->getMessage();
        }
    }
    
    function __destruct(){
        try{
            //pg_close($this->gCnx);
            unset($this->gCnx);
        } catch (PDOException $e) {
            return "Error:\n" . $e->getMessage();
        }
    }
    
    function setConexion($conexion) {
        $this->conexion = $conexion;
    }

    function getConexion() {
        return $this->conexion;
    }

    function ejecutar($sql,$array){
        try{
            $my_query = rand(1, 100000000000);
            pg_prepare($this->conexion, $my_query, $sql);
            $result = pg_execute($this->conexion, $my_query, $array);
            return $result;
        }  catch (PDOException $e){
            return $e->getMessage();
        }
    }
    
    function obtener($sql,$array){
        try{
            $result = pg_query_params($this->conexion, $sql, $array);
            return $result;
        }  catch (PDOException $e){
            return $e->getMessage();
        }
    }
    
    function iniciarTransaccion(){
        try{
            $result = pg_query($this->conexion, "BEGIN");
            return $result;
        }  catch (PDOException $e){
            return $e->getMessage();
        }
    }

    function abortarTransaccion(){
        try{
            $result = pg_query($this->conexion, "ROLLBACK");
            return $result;
        }  catch (PDOException $e){
            return $e->getMessage();
        }
    }

    function finalizarTransaccion(){
        try{
            $result = pg_query($this->conexion, "COMMIT");
            return $result;
        }  catch (PDOException $e){
            return $e->getMessage();
        }
    }

}
