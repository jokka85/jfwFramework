<?php

/**
 * 
 * config/router.php
 * 
 * Routes as needed to various locations within the MVC
 * 
 */

$routes = [
    
    // INDEX / HOME
    "/" => 'Default',
    "home" => 'Default',
    
    // ERROR PAGE
    "Error" => 'Error',

    /**
     * ADMIN URL TO BE USED TO ACCESS THE PAGE
     *
     * It is best to make this unique so only yourself and those users that need access can locate it.
     *
     */
    "2..340cbbdkfwqp43l4jf.ffkklwdlfja.s.dfjeo" => "jFWAdmin",

    // WE NOW HAVE TO BLOCK DIRECT ACCESS TO THE PAGE
    "jFWAdmin" => "Error"
    
];

