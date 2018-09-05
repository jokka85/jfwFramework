<?php

use Database\Table\Table;

class DefaultTable extends Table {
    
    /**
     * The WHERE section of the SQL statement
     * @var Array | null 
     */
    private $where = null;
    
    /**
     * The ORDER BY section of the SQL statement
     * @var String
     */
    private $orderBy = null;
    
    /**
     * Will be placed in the WHERE section when searching between two dates.
     * @var String
     */
    private $dates = null;

    /**
     * AdminSettings Object
     * @var \AdminSettings\AdminSettings
     */
    private $_adm;

    /**
     * Array of masks set within the AdminSettings
     * @var array
     */
    private $_masks;

    function __construct(){
        parent::__construct();
        $this->setTable('dummy_table');
        
        // GET WHERE, ORDERBY, AND/OR DATES
        $this->where = (isset($_GET['filter'])) ? 
                $this->whereFromGet("filter") : null;
        
        $this->orderBy = (isset($_GET['orderBy'])) ? 
                $this->orderFromGet() : null;

        $this->_adm = new \AdminSettings\AdminSettings();
        $this->_masks = $this->_adm->getMaskSettings();
    }

    /**
     * Displays the values of the WHERE section of Query built
     */
    public function printVars(){
        print_r($this->where);
        echo $this->orderBy;
    }


    public function getInvoiceFromID($id){
        return $this->get(null, ["employee_id" => ["=" => $id]]);
    }
    
    /**
     * Creates variables for the WHERE section of the SQL statement.
     * @param String $name
     * @return Array
     */
    private function whereFromGet($name){
        $array = [];
        // SPLIT EACH FILTER
        $arr = explode("*", filter_input(INPUT_GET, $name));
                
        foreach($arr as $value){
            $where = explode(":", $value);
            
            if(count($where) == 3){
                $array[$where[0]] = [(($this->_comparison($where[1])) ? $where[1] : "=") => $where[2]];
            } else {
                $array[$where[0]] = ["=" => $where[1]];
            } 
            
        }
        return $array;
    }

    /**
     * Gets the ORDER from the $_GET 
     * @return String
     */
    private function orderFromGet(){
        return filter_input(INPUT_GET, "orderBy");
    }
    
}