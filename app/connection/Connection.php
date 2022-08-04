<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

class Connection {

    private $connection;

    function __construct() {
        $this->connect();
    }

    /**
     * Conexión
     * 
     * @return Object Objeto conexion
     */
    public function connect() {

        $config = parse_ini_file(__DIR__ . '../../config/config.ini');

     try {
            $dsn = "mysql:host={$config['server']};dbname={$config['dbname']}";
            $this->connection = new PDO(
                $dsn, 
                $config['username'], 
                $config['password']/*, 
                array(
                    PDO::ATTR_TIMEOUT => 10, // in seconds
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                )*/
            );            
        } catch (PDOException $e) {
            throw new Exception('No fue posible conectarse con la base de datos.'.$e->getMessage()); 
        }
        //$this->connection->query("SET wait_timeout=9000;");
        return $this->connection;
    }

    /**
     * Obtener o reconectar conexion a BD
     *      
     * @return Object Objeto conexion
     */
    public function getConnection() {
        if ($this->connection == null) {
            $this->connect();
        }        
        return $this->connection;
    }

    /**
     * Obtener todos los registros de una consulta con o sin condiciones
     * 
     * @param String SQL(SELECT)
     * @return Array respuesta de ejecución en formato array
     */
     public function getAll($query) {
        $this->getConnection()->exec("set time_zone = '-06:00';");   
        $stmt = $this->getConnection()->prepare($query);        
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

  

    /**
     * Obtener todos los registros de una consulta con o sin condiciones
     * 
     * @param String SQL(SELECT)
     * @return Array respuesta de ejecución en formato array
     */
    public function getHTMLTable($query) {   
        $this->getConnection()->exec("set time_zone = '-06:00';");   
        $stmt = $this->getConnection()->prepare($query);        
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $table = "<table id='reportDataTableX' class='reportDataTable table table-hover table-striped table-bordered'>";
        foreach ($result as $key => $value) {
            if($key == 0){                
                $headerKeys = array_keys($value);
                $table .= "<thead>";
                $table .= "<tr>";
                foreach ($headerKeys as $itemHeader){
                    $table .= "<th>{$itemHeader}</th>";
                }         
                $table .= "</tr>";
                $table .= "</thead><tbody>";      
            }
            $table .= "<tr>";
            foreach ($value as $itemBody){
                $table .= "<td>{$itemBody}</td>";
            }         
            $table .= "</tr>";             
        }
        $table .= "</tbody></table>";
                
        return $table;
    
    }

    /**
     * Obtener ultomo ID insertado     
     * @return Integer ultimo id insertado
     */
    public function getLastInserId() {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Ejecuta sentancia SQL
     * 
     * @param String SQL(INSERT, UPDATE, DELETE)
     * @return Boolean
     */
    public function executeQuery($query) {
        $this->getConnection()->exec("set time_zone = '-06:00';");
        /*$result =  $this->getConnection()->exec($query);*/        
        $stmt = $this->getConnection()->prepare($query);        
        $stmt->execute();
        if($stmt->rowCount() > 0) {                
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Ejecuta sentancia SQL
     * 
     * @param String SQL(DELETE)
     * @return Boolean
     */
    public function executeDelete($query) {
        $this->getConnection()->exec("set time_zone = '-06:00';");       
        $result =  $this->getConnection()->exec($query);              
        if($result) {                
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Inicializar una transaccion SQL     
     */
    public function beginTransaction() {
        $this->getConnection()->beginTransaction();
    }

    /**
     *genera un rollback una transaccion SQL     
     */
    public function rollBackTransaction() {
        $this->getConnection()->rollback();
    }

    /**
     * Finaliza una transaccion SQL     
     */
    public function commitTransaction() {
    
        $this->getConnection()->commit();
    }

    /**
     * cierra conexion
     */
    public function closeConnection() {    
        $this->connection = null;
    }

}
