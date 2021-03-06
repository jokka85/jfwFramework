<?php
/**
 * core/Router.php
 * 
 * The Router namespace will route the information. This is dependent on the 
 * requested URI. It associates a name with a controller/method and includes 
 * the information accordingly.
 * 
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */

namespace Router{
    
    use Controller\getController;
    
    /**
     * class Router
     * 
     * Class used to provide the Routing methods
     */
    class Router {
        
        /**
         * @var array
         * Routes, as provided by the config/router.php file 
         */
        private $routes;
        
        /**
         * @var array
         * Directories, as provided by config/required.php file 
         */
        private $dirs;
        
        /**
         * constructor that sets up directories, routes, and then engages the 
         * primary method in order to begin the routing process.
         */
        function __construct($routes){
            
            $this->cleanDirs();
            
            $this->routes = $routes;
            
            $this->route();
            
        }
        
        /**
         * When initiated the route method will attempt to locate the 
         * appropriate controller and/or method.
         */
        public function route(){
            
            /**
             * @var String
             * Page Name
             */
            $page = '';
			
			
            // IS THE LAST OCCURENCE SPECIFIC GET REQUESTS??
            $count = count($this->dirs);
            if(strpos($this->dirs[$count - 1], '?') !== FALSE){
                
                $var = explode("&", substr($this->dirs[$count - 1], 1));
                
                foreach($var as $v){
                    list($key,$value) = explode("=", $v);
                    $_GET[(strpos($key, "amp;") !== FALSE) ? str_replace("amp;", "", $key) : $key] = $value;
                }
                
                unset($this->dirs[$count - 1]);
                
                $this->dirs[0] = (($count - 1 ) == 0) ? "" : $this->dirs[0];
            }
            
            // NAME OF THE REQUESTED CONTROLLER
            // $name will be used to attempt to locate title within $this->routes
            $name = ($this->dirs[0] == '' || $this->dirs[0] == null) ? "/" : $this->dirs[0];
                             
            // if the name exists within the routes, send to appropriate location
            if(array_key_exists($name, $this->routes)){
                                
                $page = $this->routes[$name];
                
            } else {
                
                // Doesn't show a route but may exist within the Controller folder
                if($this->checkController($this->dirs[0])){
                                        
                    $page = $this->dirs[0];
                    
                } else {
                
                    // send to error page
                    $page = $this->routes['Error'];
                    
                }
                
            }
            
            // remove the controller name from the list of directory structure
            unset($this->dirs[0]);
            $this->dirs = array_merge($this->dirs);
            
            // instantiate the getController() class
            $controller = new getController();
            $controller->controller($page, (count($this->dirs) >= 1) ? $this->dirs : null);
            
            // verify vars
            if($controller->checkVars()){
                // send to error page
                $controller->controller($this->routes['Error']);
            }
                        
        }
        
        /**
         * cleanDirs() - Simply "cleans" the directory structure by removing the 
         * base directory and then creating an array of directories
         */
        private function cleanDirs(){
            
            // GET THE REQUESTED URI
            $requestURI = str_replace("/" . str_replace(array("\\","/"), "/", BASE_DIR), "", 
                              htmlspecialchars($_SERVER['REQUEST_URI']));
			
            $DIR_LIST = explode("/", $requestURI);
            
            if((is_null($DIR_LIST[0]) || empty($DIR_LIST[0])) && count($DIR_LIST) > 1){
                unset($DIR_LIST[0]);
		$DIR_LIST = array_values($DIR_LIST);
            }
            
            // reset keys and set to variable     
            $this->dirs = array_merge($DIR_LIST);
		
        }
        
        /**
         * checkController($controller)
         * 
         * Will check the Controller folder for a file that matches the name 
         * given in the request_uri.
         * 
         * @param String $controller - Name of controller to locate
         * @return Boolean - If controller exists then this will return TRUE, 
         * otherwise it will return FALSE
         */
        private function checkController($controller){
            return file_exists(CONTROLLER_DIRECTORY . $controller . "Controller.php");
        }
        
    }
    
}