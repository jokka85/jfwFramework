<?php
/**
 * core/Database.php
 * 
 * The Database namespace is designed to handle all Database connectivity functions.
 * 
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace Database {
    
    // We use the PDO funtionality to make our connections and queries
    use \PDO;
    
    /**
     * class Database - Connects us to our database and gives proper functions.
     * 
     * @param PDO con - Con is designed to hold the PDO connection information
     * @param Array args - Binding variables to be used within prepare/bind statements
     */
    class Database{
        
        /**
         * @var PDO CONNECTION 
         * Holds the database connection
         */
        public $con;
        
        /**
         * @var Array 
         * Binding variables to be used within prepare/bind statements.
         */
        public $args;
             
        /**
         * __construct()
         * 
         * Creates the Database connection
         */
        function __construct($config = null){
            
            if($config == null){
                $config = 'Default';
            }
            
            $databases = $GLOBALS['settings']['Database'][$config];
            
            // create PDO info
            $dsn = "mysql:host=" .$databases['server'] . ";dbname=" .
                    $databases['db'];
            
            $this->con = new PDO(
                    $dsn, 
                    $databases['user'], 
                    $databases['pass'],
                    array(PDO::ATTR_PERSISTENT => true));
                                    
        }
        
        /**
         * Closes the database connection
         */
        public function close(){
            $this->con = null;
        }
        
        /**
         * prepAndBind($query,$args)
         * 
         * Designed to give a user easier movement for preparing and binding information 
         * within the database.
         * 
         * @param String $query 
         * SQL statement to be used in prepare process
         * 
         * @param Array $args 
         * Array that holds the type and values
         * 
         * @return PDO::stmt | null 
         * Returns the STMT after the process is finished or null if it fails
         */
        public function prepAndBind($query, $args){
            
            try{
                $db = $this->con->prepare($query);

                foreach($args as $key => $value){
                    $db->bindValue(":".$key, $value);
                }

                if($db->execute($args)){                    
                    return $db;
                } else {
                    return null;
                }
                
            } catch (PDOException $e){
                trigger_error("Bad SQL: " . $query . " ERROR: " . $e->getMessage() . E_USER_ERROR);
            }
            
        }
        
        /**
         * 
         * This function is a stand in to do the fetchAll functionality without 
         * depending on the user to do all work themselves within the program.
         * 
         * Types:
         * 
         *      array
         *          PDO::FETCH_COLUMN
         * 
         *      both
         *          PDO::FETCH_BOTH
         * 
         *      serialize
         *          PDO::FETCH_SERIALIZE
         * 
         *      obj
         *          PDO::FETCH_OBJ
         * 
         *      default/assoc
         *          PDO::FETCH_ASSOC
         * 
         * @param PDO::stmt $stmt 
         * Information is fetched from the PDO::stmt object
         * 
         * @param String $type 
         * How to return the results of the PDO::stmt->fetchAll() function
         * 
         * @return Array/Object
         */
        public function fetchAll($stmt, $type = null){
            
            switch(strtolower($type)){
                case "array":
                    return $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                case "both":
                    return $stmt->fetchAll(PDO::FETCH_BOTH);
                    
                case "serialize":
                    return $stmt->fetchAll(PDO::FETCH_SERIALIZE);
                    
                case "obj":
                    return $stmt->fetchAll(PDO::FETCH_OBJ);
                    
                default:
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
        }
        
    }
    
}
