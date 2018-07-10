<?php

/**
 * core/ControllerInterface
 * 
 * The interface that should be implemented by all created Controllers. The 
 * interface requires the index() method as all pages are likely to require an 
 * index.
 */
interface ControllerInterface {

    /**
    * Default page loaded from Controller when Controller called directly.
    */
    public function index();
    
}