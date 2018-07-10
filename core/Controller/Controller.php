<?php
/**
 * core/Controller.php
 * 
 * The Controller namespace handles all of the controller actions. Within the 
 * namespace we will locate the controller, include it, get the contents of the 
 * template folder for processing, and make changes as necessary.
 * 
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace Controller {
    
    use Database\Table\Table;
    
    /**
     * getController
     * 
     * Gets the controller from the Controller folder as needed by the application
     * 
     */
    class getController {
        
        /**
         * @var String $name 
         * Name of the controller
         */
        private $name;
        
        /**
         * @var String $method 
         * The method to be called for the controller
         */
        private $method;
        
        /**
         * @var Array $vars 
         * Any variables, including methods from the 
         * controller class, that may be needed.
         */
        private $vars;
        
        /**
         * @var Class $_controller 
         * The actual controller class called from the src/Controller Folder.
         */
        private $_controller;
        
        /**
         * controller($name, $vars = null)
         * 
         * The controller will open up the file from the Controller folder.
         * 
         * @param String $name - The name of the controller to be located
         * @param Array/Null $vars - Any variables, including methods from the 
         * controller class, that may be needed.
         */
        public function controller($name, $vars = null){
            
            // INCLUDE THE CONTROLLER FILE
            $path = CONTROLLER_DIRECTORY . $name . "Controller.php";
            if(!file_exists($path)){
                $path = CONTROLLER_DIRECTORY . "ErrorController.php";
                $name = "Error";
            }
            
            include $path;
            
            $this->name = $name;
            $this->vars = $vars;
            
            // SET AS GLOBALS FOR OTHER CLASSES
            $GLOBALS['name'] = $name;
            $GLOBALS['vars'] = $vars;
            
            // CREATE CONTROLLER VAR
            $class = $this->name . "Controller";
            $this->_controller = new $class();
            
            // SET METHOD IF AVAILABLE
            $this->method = ($this->vars[0] != null && $this->vars[0] != '' 
                    && !empty($this->vars[0])) ? $this->vars[0] : 'index';
            
            // VERIFY THAT THE VARS ARE ACCURATE
            if($this->checkVars()){
            
                unset($this->vars[0]);

                if(!empty($this->vars)) { array_merge($this->vars); }

                $this->_controller->{$this->method}($this->vars);
            } else {
                $this->_controller->index($this->vars);
            }
        }
        
        /**
         * controllerMethod($method)
         * 
         * Fetches the method requested from the controller called
         * 
         * @param String $method - Method to be called from controller.
         * 
         * @example /Error/404 - Controller:Error | Method: 404
         */
        public function controllerMethod($method){
            $this->_controller->$method;
        }
        
        /**
         * checkVars()
         * 
         * Checks to see if the method is within the controller
         * 
         * @return Boolean - Returns TRUE if the method is found and FALSE if it 
         * is not.
         */
        public function checkVars(){
            
            $methods = get_class_methods($this->_controller);
            
            if(isset($this->vars[0]) && in_array($this->vars[0], $methods)){
                return true;
            } else {
                return false;
            }
            
        }
        
        public function get_controller(){
            return $this->_controller;
        }
        
    }
    
    /**
     * Controller
     * 
     * The class used by each Controller class within the Controller folder.
     * 
     * When creating a new Controller for the MVC, you will implement in the 
     * simplest way. The following is short example:
     * 
     *      use Controller\Controller;
     *
     *      // Extend Controller and implement ControllerInterface
     *      class TestController extends Controller implements ControllerInterface {
     * 
     *          function __construct(){
     *              parent::__construct();
     *          }
     * 
     *          public function index(){
     *          
     *              $this->finalize();
     * 
     *          }
     *      }
     */
    class Controller {
        
        /**
         * @var String 
         * Name of the controller 
         */
        private $name;
        
        /**
         * @var Array 
         * Variables in the controller 
         */
        private $vars;

        /**
         * @var String 
         * Html contained within the src/Template/__NAME__/$method.php file 
         */
        private $html = '';
        
        /**
         * @var String 
         * The type of document to display. Html or JSON 
         */
        private $type = 'html';
        
        /**
         * @var String 
         * The name of the file to use as a "header" file. These files are 
         * stored within src/Template/jFW_Headers/{$header}.php 
         */
        private $header = 'index';
        
        /**
         * @var String 
         * $the name of the ifle to use as the "footer" file. These files are 
         * stored within src/Template/jFW_Footers/{$footer}.php 
         */
        private $footer = 'index';
        
        /**
         * @var Array 
         * Settings contained within the GLOBALS variable 
         */
        private $settings;
        
        /**
         * __construct()
         * 
         * Sets up the variables for the controller name, vars, and HTML
         */
        function __construct(){
            $this->settings = $GLOBALS['settings'];
            $this->name = $GLOBALS['name'];
            $this->vars = $GLOBALS['vars'];
            
            // TRY TO LOCATE TABLE FILE FIRST
            // IF NONE EXISTS, USE DEFAULT TABLE CLASS
            
            $className = str_replace("Controller", "", get_called_class());
            
            $tableName = $className . "Table";
            
            $filePath = PARENT_DIRECTORY . SRC . MODEL . TABLE . $tableName . ".php";
            
            if(file_exists($filePath)){
                include_once($filePath);
                $this->$className = new $tableName();
            } else {
                $this->$className = new Table();
            }
        }
        
        /**
         * setHtml()
         * 
         * Fetches the HTML from the Template/{Controller Name}/{METHOD} folder
         */
        public function setHtml(){
            
            $html_header = $this->fetchHeader();
            $html_footer = $this->fetchFooter();
            
            $this->html = $html_header;
            
            // IF VARIABLE IS EMPTY, FETCH INDEX OF CONTROLLER
            $controller_page = ($this->vars[0] != null && $this->vars[0] != '' 
                    && !empty($this->vars[0])) ? $this->vars[0] : 'index';
            
            // STRING OF FILE PATH
            $file = TEMPLATE_DIRECTORY . $this->name 
                    . DIRECTORY_SEPARATOR . $controller_page . '.php';
            
            // CHECK IF FILE EXISTS.
            // if so, load the file, if not then load error page.
            if(file_exists($file)){
            
                $this->html .= file_get_contents($file);
                
            } else {
                
                $this->html .= file_get_contents(TEMPLATE_DIRECTORY . 'Error' . 
                        DIRECTORY_SEPARATOR . 'index.php');
            }
            
            $this->html .= $html_footer;
        }
        
        /**
         * setType($type)
         * 
         * Set the type of page to be displayed
         * EX: Html, Json, etc..
         * 
         * @param String $type - Type of page to display (HTML / JSON / ETC)
         */
        public function setType($type){
            $this->type = $type;
        }
        
        /**
         * finalize($args = null)
         * 
         * Within the Controller Class in the Controller folder you set the 
         * arguments to change information within the template file. This is 
         * where that takes place and then the information will be displayed.
         * 
         * @param type $args
         */
        public function finalize($args = null){
            
            if($this->type != 'json'){
                            
                // PROCESS CHANGES FROM FILE
                $this->setHtml();
                
                // BE SURE TO SORT STRINGS BEFORE ARRAYS
                if($args != null){
                    asort($args);
                    $this->html = $this->set($args, $this->html);
                }
                
                // SET APP NAME
                $this->html = $this->set(['APP_NAME' => $this->settings['APP_NAME']], $this->html);
                
                // CHECK IF A TITLE HAS BEEN SET
                if(strpos($this->html, '<{title}>')){
                    $this->html = $this->set(['title' => $this->settings['DEFAULT_TITLE']], $this->html);
                }
                
                // create runtime counter
                $_SESSION['stop'] = microtime(true);
                $runtime = $_SESSION['stop'] - $_SESSION['start'];

                $this->html = $this->set(['runtime' => $runtime], $this->html);
            }
            
            $this->display($args);
            
        }

        /**
         * set($arg, $html)
         * 
         * Set values to keys that are placed within the files of the Template folder.
         * EX: ['PAGE_TITLE' => 'This Title']
         * 
         * For LOOPS you can add them into the array in this fashion:
         *      for($i = 1; $i <= 5; $i++){
         *          $array['LOOP'][] = ['TEST_NUMBER' => $i;
         *      }
         * 
         * HTML displays on page like so:
         *      <{PAGE_TITLE}>
         * 
         *      <{LOOP}>
         *          <{TEST_NUMBER}>
         *      <{\LOOP}>
         * 
         * @param Array $arg - Array of keys and values to be swapped out
         * @param String $html - HTML provided by controller
         * @return String   - Returns changed HTML
         */
        public function set($arg, $html){
            
            $search = '';
            $replace = '';    
            $new_html = null;

            foreach($arg as $key => $value){

                if(is_array($value) && is_string($key)){

                    preg_match("/<{" . $key . "}>(.*?)<{\/" . $key ."}>/s", ($new_html != null) ? $new_html : $html, $matches);

                    $search = $matches[0];

                    $replace = $this->set($value, $matches[1]);

                    if($new_html != null){
                        $new_html = str_replace($search, $replace, $new_html);

                    } else {

                        $new_html .= str_replace($search, $replace, $html);
                    }

                } else if (is_array($value) && is_int($key)){

                    $search = $html;

                    $replace = $this->set($value, $html);

                    $new_html .= str_replace($search, $replace, $html);
                    
                } else {

                    $search = "<{".$key."}>";

                    $replace = $value;

                    $new_html = str_replace($search, $replace, ($new_html != null) ? $new_html : $html);
                }

            }
            
            return $new_html;
        }
        
        /**
         * display()
         * 
         * Echo the information
         */
        public function display($args = null){
            
            $html = '';
            
            switch ($this->type) {
                    
                case 'json':
                    header('Content-Type: application/json; charset=utf-8');
                    $html = json_encode($args);
                    break;
                    
                default;
                    $html = $this->html;
                
            }
            
            echo $html;
        }
        
        
        /**
         * fetchHeader()
         * 
         * Fetches the header file associated with controller, as needed.
         * Defaults to: jFW_Headers/index.php
         * 
         * @return String   - returns contents of the header file
         */
        private function fetchHeader(){
            $html_header = '';
            
            $header_index = TEMPLATE_DIRECTORY . "jFW_Headers" 
                    . DIRECTORY_SEPARATOR . "index.php";
            
            if($this->header == null){
                $html_header = file_get_contents($header_index);
            } else {
                $header_file = TEMPLATE_DIRECTORY . "jFW_Headers" . DIRECTORY_SEPARATOR . $this->header . ".php";
                
                if(file_exists($header_file)){
                    $html_header = file_get_contents($header_file);
                } else {
                    $html_header = file_get_contents($header_index);
                }
            }
            
            return $html_header;
        }
        
        /**
         * fetchFooter()
         * 
         * Fetches the footer file associated with controller, as needed.
         * Defaults to: jFW_Footers/index.php
         * 
         * @return String   - returns contents of the footer file
         */
        private function fetchFooter(){
            $html_footer = '';
            $footer_index = TEMPLATE_DIRECTORY . "jFW_Footers" 
                    . DIRECTORY_SEPARATOR . "index.php";
            if($this->footer == null){
                $html_footer= file_get_contents($footer_index);
            } else {
                $footer_file = TEMPLATE_DIRECTORY . "jFW_Footers" . DIRECTORY_SEPARATOR . $this->footer . ".php";
                
                if(file_exists($footer_file)){
                    $html_footer = file_get_contents($footer_file);
                } else {
                    $html_footer = file_get_contents($footer_index);
                }
            }
            return $html_footer;
        }
        
        /**
         * setHeader($header)
         * 
         * Sets the name of the header file to look for
         * 
         * @param String $header
         */
        public function setHeader($header){
            $this->header = $header;
        }
        
        /**
         * setFooter($footer)
         * 
         * Sets the name of the footer file to look for
         * 
         * @param String $footer
         */
        public function setFooter($footer){
            $this->footer = $footer;
        }

    }
    
}