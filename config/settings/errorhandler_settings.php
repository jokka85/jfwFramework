<?php
/**
 * errorhandler_settings.php
 *
 * core/ErrorHandler.php will seek this information when handling errors within the application. By default the
 * errors will be recorded under logs/errors.log. This can be changed to either a different file or to the
 * database settings below.
 *
 * The database settings require a table name and a primary key.
 *
 * If using the database settings, there is a default setup that can be used.
 *
 * @see config/error_handler.sql
 *
 * @global array $GLOBALS['settings'] - Holds a global array of the settings for the given application
 * @name $settings
 * @author Joshua Weeks
 * @version 0.1
 * @since 0.1
 */

$GLOBALS['settings']['ErrorHandler'] = [

    /**
     * The options that can be used here are Default, Database, or Both
     *
     *      Default: writes errors to logs/errors.log (by default)
     *
     *      Database: inserts entries into the database per the settings below. Requires the table to be created
     *                  using the config/error_handler.sql file.
     *
     *      Both: will write to the error file and to the database
     */
    'use' => 'Default',

    /**
     * File location where errors are to be written
     */

    'error_file' => dirname(__FILE__, 3) . DIRECTORY_SEPARATOR .
        'logs' . DIRECTORY_SEPARATOR . "errors.log",
    /**
     * Database table and primary key for the errors to be processed with.
     */

    'Database' => [

        'table' => 'error_handler',

        'primary' => 'error_id'
    ]

];