<?php
/**
 * admin_settings.php
 *
 * The admin_settings portion of this is used to gather administrative
 * information. You can manually type in any information you need or you can
 * use a database to gather the information. However, all of the information
 * that is marked within the "Default" array should be placed into the table
 * as well.
 *
 * In order to access the ADMIN page, you will need to go to config/router.php and set the
 * information as you desire for the url to access the page.
 *
 * @see admin_settings.sql For the default admin_settings table Query
 *
 * @global array $GLOBALS['settings'] - Holds a global array of the settings for the given application
 * @name $settings
 * @author Joshua Weeks
 * @version 0.1
 * @since 0.1
 */

$GLOBALS['settings']['admin_settings'] = [
    /**
     * Which admin_settings would you like to use?
     *
     * If you use the Default settings then be sure that the settings provided
     * are accurate. You can add to and change the settings as needed.
     *
     * If you are using the Database table then you must provide the name
     * of the table you are using. Default provided in the admin_settings.sql
     * file is `admin_settings`
     *
     * Options:
     *      Database - Uses the database table provided in the array
     *
     *      Default - Pulls the information direction from this array
     */
    'use' => 'Database',

    /**
     * Database admin_settings
     */
    'Database' => [

        // Name of table where admin_settings are stored
        'table' => 'admin_settings',

        // Name of primary key
        'primary' => 'setting_name',

        // Name of the value key
        'value' => 'setting_value'
    ],

    /**
     * User information settings. Max/min length, alphanumeric required, etc.
     *
     * All settings declared here will be compared against the databse when
     * pulling `user_information` and use='Databsae'
     *
     */
    'user_information' =>

        [

            // Minimum password length
            'min_password' => 8,

            // Maximum password length
            'max_password' => 25,

            // Does password have to be alphanumeric (TRUE = Y , FALSE = N)
            'alphaNum_password' => true,

            // Minimum username length
            'min_username' => 8,

            // Maximum username length
            'max_username' => 12,

            // Does username need to be alpha numeric? (TRUE = Y , FALSE = N)
            'alphaNum_username' => true

        ],

    /**
     * Masks to be used within forms to hide the names of the fields. These are set as the defaults.
     */
    "masks" => [

        "mask_username" => "username",

        "mask_password" => "password",

        "mask_firstname" => "first_name",

        "mask_middle" => "middle_name",

        "mask_lastname" => "last_name",

        "mask_birthdate" => "birth_date"

    ],

    /**
     * Site wide settings
     */
    'site_settings' => [

        // LOGO TO BE DISPLAYED ON THE SITE
        'logo' => "logo.jpg",

        // SLOGAN TO BE DISPLAYED ON THE SITE
        'slogan' => 'This is an application.'
    ],

    /**
     * Location to redirect to on logout.
     */
    'logout_redirect' => '/' . BASE_DIR . '/login',

    /**
     * Redirect location if the user is logged in
     */
    'logged_in_redirect' => '/' . BASE_DIR . '/view/',

    /*
     * Contact Emails
     */
    'ADMIN_EMAIL' => 'admin@localhost.com'
];