<?php

/**
 * Controller/DefaultController.php
 * 
 * This will stand in as the default (homepage). Here we can place a stand 
 * alone file that will be displayed on initial load.
 */

use Database\Paginator\Paginator;
use ErrorHandler\ErrorHandler;
use Database\Table\QueryBuilder\QueryBuilder;

require 'jFWController.php';

class DefaultController extends jFWController implements ControllerInterface {

    function __construct(){
        parent::__construct();
    }

    /**
     * index()
     * 
     * Displays the initial HomePage
     */
    public function index()
    {
        $this->setHeader('default');
        $this->finalize();
    }

}