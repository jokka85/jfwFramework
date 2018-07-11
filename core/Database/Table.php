<?php
/**
 * core\Database\Database\Table.php
 * 
 * The Table namespace is designed to handle the basic database table functions
 * 
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace Database\Table {
    
    // USES DATABASE CONNECTION
    use Database\Database;
    
    /**
     * The Table class is designed as a default tool used to fetch information 
     * from a table in a database. Everything that is needed is provided here 
     * initially. Any additional functionalities should be added to a custom 
     * Table Model /src/Model/Table/{Name}Table.php and extend this class.
     * 
     * The tables stored will follow something similiar to the following layout: 
     *      use Database\Table\Table;
     * 
     *      class TestTable extends Table {
     *          
     *          function __construct(){
     *              parent::__construct();
     * 
     *              // If table name is different than file name
     *              $this->setTable('table_name');
     *          }
     *
     *          public function getAll(){
     *              $stmt = $this->get();
     *              return $this->fetchAll($stmt);
     *          }
     *      }
     */
    class Table {
        
        /**
         * @var Object 
         * Holds the Database object 
         */
        public $db;
        
        /**
         * @var Array
         * Holds the configuration that was called
         */
        private $config;
        
        /**
         * @var String 
         * Table Name 
         */
        private $table;
        
        /**
         * @var Object | Null
         * If paginator object isn't set, then it remains null.
         */
        private $_paginator = null;
        
        /**
         * __construct($config = null)
         * 
         * During creation of the table object, you can send a configuration 
         * title as well to use a separate database configuration.
         * 
         * @param String $config - Name of database configuration to use
         */
        function __construct($config = null){
            $this->config = $config;
            $this->db = new Database($config);
        }
        
        /**
         * Tell the Table if you need the results Paginated or not
         * @param Boolean $paginator
         */
        public function setPaginator($paginator = null){
            $this->_paginator = (!is_null($paginator)) ? $paginator : null;
        }
        
        /**
         * setConnection($config)
         * 
         * If a different connection is needed then you set it here.
         * 
         * @param String $config - Name of database configuration to use
         */
        public function setConnection($config){
            
            // CLOSE THE INITIAL DATABASE
            $this->db->close();
            
            // CREATE NEW CONNECTION
            $this->db = new Database($config);
            
            // SET TABLE FOR INITIAL VALUE
            $this->table = get_class();
        }
        
        /**
         * setTable($table)
         * 
         * Name of the table within the database if different from Model name
         * 
         * @param String $table
         */
        public function setTable($table){
            $this->table = $table;
        }
        
        /**
         * getDB()
         * 
         * Return the database object
         * 
         * @return Database Object
         */
        public function getDB(){
            return $this->db;
        }
        
        /**
         * get($select = null, $where = null, $options = null)
         * 
         * When fetching, retrieving, or "getting" information; this is the 
         * method that will be used. It will return the PDO::STMT
         * 
         * @param Array/null $select - 
         * Array of the items to be selected or default to all (*) ['id']
         * 
         * @param Array/null $where - 
         * Array of the items within the where clause. Needs to be entered in 
         * a multidimensional array in a specific order.
         * <pre>
         *      [ FIELD_NAME =>
         *          [
         *              COMPARISON_OPERATOR => VALUE
         *          ]
         *      ]
         * 
         * EX: 
         *      [ 'id' =>
         *          [
         *              '>' => 1
         *          ]
         *      ]
         * </pre>
         * 
         * @param Array/null $options -
         * Array of any additional options ['LIMIT' => 1]
         * 
         * @return PDO::STMT
         */
        public function get($select = null, $where = null, $options = null){
            
            // CREATE THE SELECT QUERY
            $q = "SELECT ";
            
            for($i = 0; $i < count($select); $i++){
                $q .= $this->table . "." . $select[$i];
                    
                if($i == (count($select) - 1)){                    
                   $q .= " ";                    
                } else {                    
                   $q .= ", ";                    
                }
            }
            
            if($select == null){
                $q .= "* ";
            }
            
            $q .= "FROM " . $this->table . " ";      
            
            $q .= ($where == null)? "" : "WHERE ";
            
            $count = 0;
            
            $args = [];
                
            if($where != null){
                foreach($where as $key => $arr){
                    $q .= " " . $key . " ";

                    foreach($arr as $comp_op => $value){
                        $q .= $comp_op . " :" . $key . " ";
                        $args[$key] = $value;
                    }

                    $count++;

                    if($count < count($where)){
                        $q .= "AND ";
                    }
                }
            }
            
            if($options != null){
                // ADD ANY ADDITIONAL OPTIONS
                foreach($options as $key => $value){
                    switch ($key){
                        
                        case 'LIMIT':
                            $q .= "LIMIT " . $value;
                            break;

                        default:
                            if(is_int($key)){
                                $q .= $value . " ";
                            } else {
                                $q .= $key . " " . $value;
                            }
                            break;

                    }
                }
            }
            
            if(!is_null($this->_paginator)){
                $this->_paginator->set($this->config, $q);
                $q = $this->_paginator->get();
            }
                        
            // WE NOW HAVE A FULL QUERY
            return $this->db->prepAndBind($q, $args);
            
        }
        
        /**
         * fetchAll($stmt, $type = null)
         * 
         * Fetch all is a pointer to the parent file core/Database/Database.php 
         * to get the information from its method.
         * 
         * @param PDO::STMT $stmt - 
         * STMT to use
         * 
         * @param String $type - 
         * The type of fetch Ex: array, obj, serialize, both
         * 
         * @return Mixed - 
         * Dependent of the $type but defaults to PDO::FETCH_ASSOC
         */
        public function fetchAll($stmt, $type = null){
            return $this->db->fetchAll($stmt, $type);
        }
        
    }    
}