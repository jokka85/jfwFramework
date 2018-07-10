<?php

/**
 * settings.php
 * 
 * Settings throughout the application in order to hold information important to 
 * processing information.
 *
 * @global Array $GLOBALS['settings'] - Holds a global array of the settings for the given application
 * @name $settings 
 * @author Joshua Weeks
 * @version 0.1
 * @since 0.1
 */

$GLOBALS['settings'] = [
    
    /**
     * Name of the application being used. This will be, by default, placed in 
     * the Header file. The variable can be accessed by the GLOBALS operator:
     *      $GLOBALS['settings']['APP_NAME'];
     */
    'APP_NAME' => 'jFW Framework',
    
    
    'DEFAULT_TITLE' => '',
    
    /**
     * The database settings can be maintained here. Add more database connections 
     * as needed or just use the initial Default connection.
     * 
     * You will need to provide the following:
     *  user = DATABASE_USERNAME
     *  pass = DATABASE_PASSWORD
     *  server = DATABASE_SERVER
     *  db = DATABASE_NAME
     * 
     */
    'Database' => [
        
        /**
         * The default database will be called each time unless told to use a 
         * different connection upon constructing the core/Database/Database class
         */
        'Default' => [
            'user' => 'root',
            'pass' => '',
            'server' => 'localhost',
            'db' => ''
        ]
    ]
];