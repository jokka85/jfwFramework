<?php

use Controller\Controller;

class ErrorController extends Controller implements ControllerInterface {
    
    function __construct(){
        parent::__construct();
    }

    public function index() {
        
        $this->finalize();
    }

}