<?php
/**
 * core/Database/Users.php
 * 
 * The Users namespace will handle user database functionality.
 * 
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace Database\Users {

    use Database\Table;
    use AdminSettings\AdminSettings;
    use ErrorHandler\ErrorHandler;
    use MessageDisplay\MessageHandler;
    
    /**
     *  The USERS class is used as a base point for get/set user information
     */
    class Users {
        
        /**
         * Table name for the administrative settings
         * @var String 
         */
        protected $settings_table = "admin_settings";
        
        /**
         * You can change this variable if the user table is different
         * @var String
         */
        protected $default_table = "employees";
        
        /**
         * The name of the PASSWORD field in the users table
         * @var String
         */
        protected $_pass_field = "employee_pass";
        
        /**
         * The name of the ID field in the user table
         * @var String
         */
        protected $_id = "employee_id";
        
        /**
         * Table Object for users
         * @var Table\Table;
         */
        protected $table;

        /**
         * Timeout for sessions.
         * @var int
         */
        private $_timeout = 20; // 20 Minutes
        
        /**
         * Create table object on constructor. Also need to set the table to the 
         * user table in the database.
         * 
         * @param String $table
         * Is the table name if not the default set in $default_table of the 
         * Database/Database/Users.php file
         */
        function __construct($table = null){
            // CREATES DATABASE
            $this->default_table = ($table != null) ? $table : $this->default_table;
            $this->table = new Table\Table();
            $this->table->setTable($this->default_table);
            $this->table->setPrimary("employee_id");
        }
        
        /**
         * Returns the Table object
         * @return Object
         */
        public function getTable(){
            return $this->table;
        }
        
        /**
         * Set the name of the password field we will be looking for.
         * @param String $name
         */
        public function setPasswordField($name){
            $this->_pass_field = $name;
        }
        
        /**
         * Set the name of the ID field programmatically
         * @param String $name
         */
        public function setIdField($name){
            $this->_id = $name;
        }

        /**
         * Login function for users. A hash will be verified against when
         * attempting to login
         *
         * @param String $username
         * @param String $password
         *
         * @return bool
         */
        public function login($username, $password){
            
            // we need a function for the passwords
            $where = ['employee_uname' => [
                '=' => $username
            ]];

            $message_handler = new MessageHandler();

            // run through validator
            $userCreate = new CreateUser();
            $validators = $userCreate->getValidators();

            if (($key = array_search('has_existing', $validators['username'])) !== false) {
                unset($validators['username'][$key]);
            }

            $userCreate->setValidators("username", $validators['username']);

            if(!$userCreate->validate("username",$username) || !$userCreate->validate("password", $password)){
                $message_handler->add_message($userCreate->_warnings, WARNING_MSG, HTML_MSG);
                $message_handler->build_messages();
                return false;
            }
            
            $stmt = $this->table->get(null, $where);
            $rows = $this->table->getDB()->fetchAll($stmt);        
            $stmt->closeCursor();

            if(count($rows) == 0){
                $message_handler->add_message("Login information not found. Please try again.");
                $message_handler->build_messages();
                return false;
            }

            $pass = $rows[0][$this->_pass_field];
            
            if($this->verifyPass($password, $pass)){
                unset($rows[0][$this->_pass_field]);
                
                // set session info
                $this->setSession($rows[0]);

                // Success
                $message_handler->add_message("You are now logged in.", SUCCESS_MSG);
                $message_handler->build_messages();

                return true;
            } else {
                $message_handler->add_message("Incorrect Password");
                $message_handler->build_messages();
                return false;
            }
        }
        
        /**
         * If the user is logged in then the stored ID of the user will be 
         * returned. Otherwise the function will return false.
         *
         * @return Integer/Boolean
         */
        public function id(){
            return (isset($_SESSION['user'][$this->_id])) ? $_SESSION['user'][$this->_id] : false;
        }
        
        /**
         * Fetches the name of the group the user is in. 
         * 
         * @return String/Boolean
         * Returns the name of the group if found, "No Group", if none are 
         * associated with the user, or FALSE if no users are logged in.
         */
        public function groupName(){
            if(isset($_SESSION['user']['group_id'])){
                $groupTable = new Table\Table();
                $groupTable->setTable("employee_group");
                $groupTable->setPrimary("group_id");
                
                $where = ["group_id" =>
                    [
                        "=" => $_SESSION['user']['group_id']
                    ]];
                    
                 $stmt = $groupTable->get(['group_name'], $where);
                 $rows = $this->table->getDB()->fetchAll($stmt);
                 $stmt->closeCursor();
                 
                 if(isset($rows[0]["group_name"])){
                     $groupName = $rows[0]["group_name"];
                 } else {
                     $groupName = "No Group";
                 }
                 
            } else {
                $groupName = false;
            }
            
            return $groupName;
        }

        /**
         * If the user is part of the Owner group then will return TRUE otherwise FALSE
         * @return bool
         */
        public function is_owner(){
            return ($this->groupName() == "Owner") ? true : false;
        }

        /**
         * Get the employees name and return it.
         *
         * @return null|string
         */
        public function getName(){
            return (isset($_SESSION['user'])) ? $_SESSION['user']['first_name'] . " " . $_SESSION['user']['last_name'] : null;
        }
        
        /**
         * Returns the users information in an array
         * 
         * @return array
         */
        public function userArray(){
            return (isset($_SESSION['user'])) ? $_SESSION['user'] : [];
        }
        
        /**
         * Logs the user out by unsetting their information from the $_SESSION
         */
        public function logout(){
            unset($_SESSION['user']);

            // Successful logout
            $message_handler = new MessageHandler();
            $message_handler->add_message("You are now logged out.", SUCCESS_MSG);
            $message_handler->build_messages();

            header("Location: " . $GLOBALS['settings']['admin_settings']['logout_redirect']);
        }
        
        /**
         * Hashes a password for user security
         * 
         * @param String $password 
         * Basic password to be converted to hash
         * @return String 
         * Hashed password is returned from password_hash as a string or False 
         * if unable to hash the password.
         */
        public function hasher($password){
            return password_hash($password, PASSWORD_BCRYPT);
        }
        
        /**
         * Verifies that the password does match the hash
         * 
         * @param String $password
         * Basic text password to compare against
         * 
         * @param String $hash
         * Hashed password to compare against
         * 
         * @return Boolean
         */
        public function verifyPass($password, $hash){
            return password_verify($password, $hash);
        }
        
        /**
         * Sets the users information into the $_SESSION for use throughout the program.
         * @param array $info
         */
        private function setSession($info){
            // GET ALL ROWS AND SET INTO THE SESSION
            foreach($info as $key => $value){
                $_SESSION['user'][$key] = $value;
            }
            $this->setTimeout();
        }

        private function setTimeout(){
            $_SESSION['user']['timeout'] = time();
        }

        public function checkTimeout(){
            $timeout = (isset($_SESSION['user'])) ? $_SESSION['user']['timeout'] : 0;
            if($timeout + $this->_timeout * 60 < time()){
                $this->logout();
            } else {
                $this->setTimeout();
            }
        }
        
    }
    
    class CreateUser extends Users {
        
        /**
         * Array of settings collected from the admin table
         * @var array
         */
        protected $_settings;

        /**
         * @var int
         * A default minimum amount. This is typically used for names.
         */
        protected $_min = 2;

        /**
         * @var int
         * A default minimum aount. This is typically used for names.
         */
        protected $_max = 25;
        
        /**
         * If you would like for the field to be hidden when submitting then 
         * you would enter the "mask" here.
         * 
         * EX:
         *      <form>
         *          <input type="text" name="id" id="id"/>
         *      </form>
         * 
         *      But the ID field is not actually ID in database.
         *      $_field_mask = [
         *              'id' => 'user_id'
         *          ];
         * 
         *      [ MASK , ACTUAL ]
         * @var array
         */
        private $_field_mask = [];

        /**
         * Messages to be displayed after submission. This will be the error message, the warning message, and the
         * success message (respectively).
         *
         * @var array
         */
        protected $_msg_display = [

            "error" => "There was an error submitting the information. Please go back and try again or notify the administrator.",

            "warning" => "The following items need to be corrected:",

            "success" => "Your information was submitted successfully."
        ];

        /**
         * Holds object for MessageHandler
         * @var MessageHandler
         */
        protected $_message_handler;

        private $_comparison_list = [
            'password' => [
                'has_min', 'has_max', 'has_punctuation', 'has_upper', 'has_lower', 'has_num'
            ],
            'username' => [
                'has_existing', 'has_min', 'has_max', 'has_lower', 'has_num'
            ],
            'firstname' => [
                'has_min', 'has_max'
            ],
            'middle' => [
                'has_min', 'has_max'
            ],
            'lastname' => [
                'has_min', 'has_max'
            ],
            'birthdate' => [
                'over_18'
            ],
            'startdate' => [
                'before_today'
            ]
        ];

        /**
         * @var null|string
         * Holds information regarding warnings.
         */
        public $_warnings = null;

        /**
         * CreateUser constructor.
         *
         * Fetch the administrator settings for users and masks.
         *
         * @param null|String $table
         * The table is the name of the Users table.
         */
        function __construct($table = null){
            parent::__construct($table);
            $admSettings = new AdminSettings();
            $this->_settings = $admSettings->getUserSettings();
            $this->_field_mask = $admSettings->getMaskSettings();

            $this->_message_handler = new MessageHandler();
        }
        
        /**
         * This is the user information that has been submitted for creation
         * @param array $user
         */
        public function create($user){
            
            // WE HAVE TO DO CHECKS AS WE GO
            // ALL OF THE FIELDS REQUIRE VALIDATION
            $valid = true;
            $info = [];
            
            foreach($user as $key => $value){
                
                $key = (isset($this->_field_mask[$key])) ? $this->_field_mask[$key] : $key;

                $valid = (!$this->validate($key, $value)) ? false : true;
                
                if($key == $this->_pass_field){
                    $value = $this->hasher($value);
                }

                $info[$key] =  $value;
            }

            // CREATE ARGUMENTS TO SUBMIT
            if($valid) {

                $process = $this->table->set($info);

                if (is_null($process)) {
                    // SET ERROR MESSAGE
                    $this->_message_handler->add_message($this->_msg_display['error']);
                } else {
                    $this->_message_handler->add_message($this->_msg_display['success'], SUCCESS_MSG);
                }
            } else if($this->_warnings != null){
                $this->_message_handler->add_message($this->_warnings, WARNING_MSG, HTML_MSG);
            }

            $this->_message_handler->build_messages();
        }
        
        /**
         * Create an array to set the masks to hide the names of table fields. This will overwrite any masks set
         * within the database or in the config/settings.php file.
         * 
         *  EX:
         *      <form>
         *          <input type="text" name="id" id="id"/>
         *      </form>
         * 
         *      But the ID field is not actually ID in database.
         *      $_field_mask = [
         *              'id' => 'user_id'
         *          ];
         * 
         *      [ MASK , ACTUAL ]
         * 
         * @param array $vars
         */
        public function setMasks($vars){
            if(is_array($vars)){
                foreach($vars as $key => $value){
                    $this->_field_mask[$key] = $value;
                }
            }
        }
        
        /**
         * Runs password validation on a string
         *
         * @param string $var
         * The string to be tested
         *
         * @param string $value
         * Value to compare against.
         *
         * @return bool
         * <pre>
         *      Returns <b>TRUE</b> if all validation passes.
         *      Returns <b>FALSE</b> if there are validation warnings.
         * </pre>
         */
        public function validate($var, $value){

            // IS THERE A VALIDATOR METHOD ?
            if(isset($this->_comparison_list[$var])) {
                foreach($this->_comparison_list[$var] as $test) {
                    if (is_callable(array($this, $test), false)) {
                        // create arguments
                        $args = [];
                        $args['var'] = $var;
                        $args['min'] = (isset($this->_settings['min_' . $var])) ? $this->_settings['min_' . $var] : $this->_min;
                        $args['max'] = (isset($this->_settings['max_' . $var])) ? $this->_settings['max_' . $var] : $this->_max;
                        $args['value'] = $value;
                        $this->$test($args);
                    }
                }
            }

            if($this->_warnings != null){
                return false;
            }
            return true;
        }

        /**
         * Set the validators for the variables to be test. By default the variables are 'password', 'username',
         * 'firstname', 'middle', 'lastname', and 'birthdate'.
         *
         * In order to ADD TO the validators you would simply use something such as:
         *  CreateUser::setValidators('username', ['has_num']);
         *
         * If you wish to append to the list of validators then simply add TRUE as a third variable.
         *  CreateUser::setValidators('username', ['has_min', 'has_max'], true);
         *
         * @param string $type
         * The name of the variable to either overwrite or append.
         *
         * @param array $options
         * Options that you will overwrite or add to existing validators.
         *
         * @param bool $append
         * Defaults to FALSE as it is assumed that you wish to overwrite. If you want to append then use TRUE.
         */
        public function setValidators($type, $options = [], $append = false){
            if(isset($this->_comparison_list[$type])){
                // If append is true then append, don't overwrite
                if($append){
                    $this->_comparison_list[$type] = array_merge($this->_comparison_list[$type], $options);
                } else {
                    $this->_comparison_list[$type] = $options;
                }
            }
        }

        /**
         * Fetches the comparison list full of validator information.
         *
         * @return array
         */
        public function getValidators(){
            return $this->_comparison_list;
        }

        /*
         * The following functions are for validating purposes of all fields.
         */

        private function has_existing($args){
            $where = [
                $this->_field_mask['mask_' . $args['var']] => [
                    '=' => $args['value']
                ]
            ];

            $q = $this->table->get(null, $where);
            $count = $q->rowCount();
            $q->closeCursor();

            if($count == 0){
                return true;
            } else {
                $this->_warnings .= ucfirst($args['var']) . " already exists. Please try a different username.<br/>\r\n";
                return false;
            }
        }

        /**
         * Determines if the string reaches the minimum length.
         *
         * @param array $args
         * Arguments made to the function. Would contain $args['value'] for the variable and $args['min'] for integer.
         *
         * @return bool
         * <pre>
         *      Returns <b>TRUE</b> if the strings length is at the minimum requirement or greater.
         *      <b>FALSE</b> if it does not meet the minimum string length.
         * </pre>
         */
        private function has_min($args){
            if(strlen($args['value']) >= $args['min']){
                return true;
            } else {
                $this->_warnings .= ucfirst($args['var']) . " must be at least " . $args['min'] . " characters long.<br/>\r\n";
                return false;
            }
        }

        /**
         * Determines if the string reaches the maximum length.
         *
         * @param array $args
         * Arguments made to the function. Would contain $args['value'] for the variable and $args['max'] for integer.
         *
         * @return bool
         * <pre>
         *      Returns <b>TRUE</b> if the strings length is at the maximum requirement or less.
         *      <b>FALSE</b> if it does not meet the maximum string length.
         * </pre>
         */
        private function has_max($args){
            if(strlen($args['value']) <=  $args['max']) {
                return true;
            } else {
                $this->_warnings .= ucfirst($args['var']) . " must be equal to or less than " . $args['max'] . " characters long.<br/>\r\n";
                return false;
            }
        }

        /**
         * Determines if the string contains any punctuation (or non alpha numeric entities).
         *
         * @param array $args
         * Will contain string in $args['value']
         *
         * @return bool
         * <pre>
         *      Returns <b>TRUE</b> if punctuation is found.
         *      Returns <b>FALSE</b> if punctuation not found.
         * </pre>
         */
        private function has_punctuation($args){
            if(preg_match("/[\W]+/", $args['value'])){
                return true;
            } else {
                $this->_warnings .= ucfirst($args['var']) . " must contain at least one punctuation (EX: . ? ! $ % )<br/>\r\n";
                return false;
            }
        }

        /**
         * Determines if string contains at least 1 uppercase letter.
         *
         * @param array $args
         * Will contain string in $args['value']
         *
         * @return bool
         * <pre>
         *      Returns <b>TRUE</b> if uppercase letter is found.
         *      Returns <b>FALSE</b> if uppercase letter is not found.
         * </pre>
         */
        private function has_upper($args){
            if(preg_match("/[A-Z]/", $args['value'])){
                return true;
            } else {
                $this->_warnings .= ucfirst($args['var']) . " must contain at least one uppercase letter.<br/>\r\n";
                return false;
            }
        }

        /**
         * Determines if string contains at least 1 lowercase letter.
         *
         * @param array $args
         * Will contain string in $args['value']
         *
         * @return bool
         * <pre>
         *      Returns <b>TRUE</b> if lowercase letter is found.
         *      Returns <b>FALSE</b> if lowercase letter is not found.
         * </pre>
         */
        private function has_lower($args){
            if(preg_match("/[a-z]/", $args['value'])){
                return true;
            } else {
                $this->_warnings .= ucfirst($args['var']) . " must contain at least one lowercase letter.<br/>\r\n";
                return false;
            }
        }

        /**
         * Determines if the string contains any integers.
         *
         * @param array $args
         * Will contain string in $args['value']
         *
         * @return bool
         * <pre>
         *      Returns <b>TRUE</b> if integer is found.
         *      Returns <b>FALSE</b> if integer is not found.
         * </pre>
         */
        private function has_num($args){
            if(preg_match("/[0-9]/", $args['value'])){
                return true;
            } else {
                $this->_warnings .= ucfirst($args['var']) . " must contain at least one number.<br/>\r\n";
                return false;
            }
        }

    }
    
}