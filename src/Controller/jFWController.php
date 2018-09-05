<?php
/**
 * Controller/jFWController.php
 *
 * jFWController is used as a parent to load any components that child controllers may want to use.
 *
 */

use Controller\Controller;
use Database\Users\Users;

class jFWController extends Controller  {

    // User created data.
    protected $_user;

    function __construct(){
        parent::__construct();

        /**
         * How to load new components in as needed
         */
        $this->_components = [
            'Crumbs',
            //'Users',
            //'AdminSettings'
        ];

        // or alternatively
        // $this->_components['Users'] = 'users_table';
        // Adding the class as the key and the arguments for the constructor as the value

        $this->loadComponents();

        /*
        // NEED TO BE LOGGED IN
        // LOGIN USERS
        $this->_user = new Users();

        if(!$this->_user->id() && $this->getControllerName() != "login"){
            header("Location: " . $GLOBALS['settings']['admin_settings']['logout_redirect']);
        }

        // CHECK TIMEOUT
        if($this->getControllerName() != "login"){ $this->_user->checkTimeout(); }
         * 
         */
    }

}