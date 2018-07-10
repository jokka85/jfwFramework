<?php

/**
 * Controller/DefaultController.php
 * 
 * This will stand in as the default (homepage). Here we can place a stand 
 * alone file that will be displayed on initial load.
 */

use Controller\Controller;

class DefaultController extends Controller implements ControllerInterface {
    
    function __construct(){
        parent::__construct();
    }

    /**
     * index()
     * 
     * Displays the initial HomePage
     */
    public function index() {
        
        $this->Default->setTable('invoice_category');
                
        $where = [
            'invoice_cat_id' =>
            [
                '>=' => 1
            ]
        ];
        
        $stmt = $this->Default->get(
                ['invoice_cat_title'], 
                $where);
        
        $rows = $this->Default->getDB()->fetchAll($stmt);
        
        $args = [
            'title' => 'Test Title',
            'body' => '<h1>IT WORKED!</h1>'
        ];
        
        foreach($rows as $value){
            foreach($value as $key => $v){
                $args['LOOP'][] = ['KEY' => $key, 'VALUE' => $v];
            }
            
        }
                
        // FOR TEST PURPOSES
        // SET TO JSON
        // $this->setType('json');
        
        // change header?
        // $this->setHeader('testHeader');
        
        $this->finalize($args);
    }

}