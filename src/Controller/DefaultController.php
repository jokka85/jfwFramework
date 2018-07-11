<?php

/**
 * Controller/DefaultController.php
 * 
 * This will stand in as the default (homepage). Here we can place a stand 
 * alone file that will be displayed on initial load.
 */

use Controller\Controller;
use Database\Paginator\Paginator;

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
        
        // CREATE PAGINATOR
        $paginator = new Paginator();
        
        //$this->Default->setTable('invoice_category');
        $this->Default->setTable('dummy_table');
        $this->Default->setPaginator($paginator);
            
        /*
        $where = [
            'invoice_cat_id' =>
            [
                '>=' => 1
            ]
        ];
        
        $stmt = $this->Default->get(
                ['invoice_cat_title'], 
                $where);
         * 
         */
        
        $stmt = $this->Default->get();
        
        $rows = $this->Default->getDB()->fetchAll($stmt);
        
        $stmt->closeCursor();
        
        $args = [
            'title' => 'Test Title',
            'body' => '<h1>IT WORKED!</h1>',
            '__PAGINATOR__' => $paginator->linker()
        ];
        
        
        foreach($rows as $value){
            $args['LOOP'][] = ['ID' => $value['dummy_id'], 'VALUE' => $value['dummy_vars']];            
        }
                
        // FOR TEST PURPOSES
        // SET TO JSON
        // $this->setType('json');
        
        // change header?
        // $this->setHeader('testHeader');
        
        $this->finalize($args);
    }

}