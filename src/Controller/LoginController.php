<?php

/**
 * Controller/LoginController.php
 *
 * Our login / logout page to be used for users
 */

use Database\Users\Users;

require 'jFWController.php';

class LoginController extends jFWController implements ControllerInterface {
    function __construct(){
        parent::__construct();
        
        // Is the Users component loaded?
        if(!isset($this->_user)){
            // redirect away from login
            header("Location: /" . BASE_DIR . "/");
        }

    }
    /**
     * Default page loaded to log the user in.
     */
    public function index()
    {
        $login = (isset($_GET['log_in'])) ? filter_input(INPUT_GET, "log_in") : false;

        if($login != false) {
            $this->log_in();
        }

        if($this->_user->id() != false){
            header("Location: " . $GLOBALS['settings']['admin_settings']['logged_in_redirect']);
            return false;
        }

        $settings = $GLOBALS['settings']['admin_settings']['user_information'];
        // SET MIN / MAX VALUES
        $args = [
            "USER_MIN" => $settings['min_username'],
            "USER_MAX" => $settings['max_username'],
            "PASS_MIN" => $settings['min_password'],
            "PASS_MAX" => $settings['max_password'],
            "__BASE_DIR__" => BASE_DIR
        ];

        $this->finalize($args);
    }

    /**
     * Logs the user out
     */
    public function logout(){
        $this->_user->logout();
    }

    private function log_in(){

        $username = (isset($_POST['username'])) ? filter_input(INPUT_POST, "username") : null;
        $password = (isset($_POST['password'])) ? filter_input(INPUT_POST, "password") : null;

        $this->_user->login($username, $password);

    }
}