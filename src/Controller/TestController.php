<?php

/**
 * Controller/TestController.php
 * 
 * This will stand in as the default (homepage). Here we can place a stand 
 * alone file that will be displayed on initial load.
 */

use Controller\Controller;

class TestController extends Controller implements ControllerInterface {
    
    function __construct(){
        parent::__construct();
    }
    
    public function index() {
        
        $rows = $this->Test->getAll();
        
        $args = [];
        
        foreach($rows as $value){
            foreach($value as $key => $v){
                $args['LOOP'][] = ['KEY' => $key, 'VALUE' => $v];
            }
            
        }
        
        $this->finalize($args);
    }
    
    public function test($arg){

        if(strlen(implode("", $arg)) > 0){ var_dump($arg); }
        
        $this->finalize();
    }

}