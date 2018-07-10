<?php

/**
 *  * Required.php
 * 
 * The object of this file is to initiate all files that are required throughout the program. Come here to 
 * add any files necessary.
 * 
 * This file also holds folder names for ease
 */

// DETERMINE BASE WITH THE CONCEPT THAT WE WILL ALWAYS LOAD FROM THE 'ROOT' FOLDER
$base = explode(DIRECTORY_SEPARATOR, dirname(getcwd()));

// BASE DIRECTORY
DEFINE('BASE_DIR', $base[count($base) - 1]);

// PARENT DIRECTORY
DEFINE ('PARENT_DIRECTORY', ".." . DIRECTORY_SEPARATOR);

// SOURCE DIRECTORY
DEFINE ('SRC', 'src' . DIRECTORY_SEPARATOR);

// CONTROLLER NAME
DEFINE ('CONTROLLER', 'Controller');

// TEMPLATE NAME
DEFINE ('TEMPLATE', 'Template');

// DATABASE NAME
DEFINE ('DATABASE', 'Database');

// MODEL NAME
DEFINE ('MODEL' , 'Model' . DIRECTORY_SEPARATOR);

// TABLE NAME
DEFINE ('TABLE' , 'Table' . DIRECTORY_SEPARATOR);

// CONTROLLER DIRECTORY
DEFINE ('CONTROLLER_DIRECTORY', dirname(getcwd()) . DIRECTORY_SEPARATOR . SRC . CONTROLLER  . DIRECTORY_SEPARATOR);

// TEMPLATE DIRECTORY
DEFINE ('TEMPLATE_DIRECTORY', PARENT_DIRECTORY . SRC . TEMPLATE . DIRECTORY_SEPARATOR);

// CSS DIRECTORY
DEFINE ('CSS_DIR', "/" . BASE_DIR . "/root/css/");

// IMAGE DIRECTORY
DEFINE ('IMG_DIR', "/" . BASE_DIR . "/root/img/");

// JAVASCRIPT DIRECTORY
DEFINE ('JS_DIR', "/" . BASE_DIR . "/root/js/");

/**
 * @param Array $folder - Holds information regarding folder locations
 */
$folder = [
    
    // CONFIGURATION FOLDER
    'config' => PARENT_DIRECTORY .'config' . DIRECTORY_SEPARATOR,
    
    // CORE FILES NEEDED FOR PROGRAM
    'core' => PARENT_DIRECTORY . 'core' . DIRECTORY_SEPARATOR,
    
    // DATABASE FOLDER
    'database' => PARENT_DIRECTORY . 'core' . DIRECTORY_SEPARATOR . DATABASE . DIRECTORY_SEPARATOR
    
];

/**
 * @param Array $file_list - List of files associated with application that 
 * will need to be loaded
 */
$file_list = [
    
    // SETTINGS FILE
    'settings' => $folder['config'] . 'settings.php',
    
    // DATABASE NAMESPACE FILE
    'database_ns' => $folder['database'] . 'Database.php',
    
    // ROUTER NAMESPACE FILE
    'router_ns' => $folder['core'] . 'Router.php',
    
    // ROUTING LIST
    'route_list' => $folder['config'] . 'router.php',
    
    // CONTROLER NAMESPACE FILE
    'controller' => $folder['core'] . 'Controller.php',
    
    // CONTROLLER INTERFACE
    'controller_interface' => $folder['core'] . 'ControllerInterface.php',
    
    // TABLE NAMESPACE
    'table' => $folder['database'] . 'Table.php'
];

