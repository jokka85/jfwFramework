<?php
/**
 * core\Database\Database\Paginator.php
 * 
 * The Paginator namespace creates a query with proper pagination
 * 
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace Database\Paginator {
    
    use Database\Database;
    
    class Paginator {
        
        /**
         * @var String 
         * The query we will use
         */
        private $_query;
        
        /**
         * @var Integer 
         * The limit given from the dropdown menu
         */
        private $_limit;
        
        /**
         * @var Integer 
         * The page number we are on.
         *      PAGE 1 = (0,25) , PAGE 2 = (25, 50) etc
         */
        private $_page;
        
        /**
         * @var Integer
         * The amount of rows returned from the database 
         */
        private $_rows;
        
        /**
         * @var Array
         * A limit must match one of the limits within the array or it will not 
         * be accepted. You can add/remove your own limits as you wish. 
         */
        private $_allowed_limits = [
            10, 25, 50, 75, 100
        ];
        
        public function set($config, $query){
            $db = new Database($config);            
            $this->_query = $query;
            $con = $db->prepAndBind($query, null);
            $this->_rows = $con->rowCount();
            $con->closeCursor();
        }
        
        /**
         * get() Creates a string with the limits set.
         * 
         * @return String 
         * The string contains a new Query with the limits set appropriately
         */
        public function get(){
            
            // FIRST WE GET THE LIMIT AND PAGE FROM THE REQUEST
            $this->_limit = (isset($_GET['limit'])) ? 
                    
                    // WE HAVE A GET ATTRIBUTE, SEND TO BE SURE IT MATCHES AN 
                    // ITEM IN THE ARRAY
                    $this->getLimits($_GET['limit']) : 
                
                25;
            
            $total = ceil($this->_rows / $this->_limit);
            
            $this->_page = (isset($_GET['page'])) ? 
                    (filter_input(INPUT_GET, "page") > $total) ?
                        // go to last row
                        $total : filter_input(INPUT_GET, "page")
                    : 
                1;
            
            $this->_query .= " LIMIT " . 
                    ( ( $this->_page - 1 ) * $this->_limit ) . ", " . $this->_limit;
            
            return $this->_query;
            
        }
        
        
        public function linker(){
            
            $boxes = 7;
            
            $last       = ceil( $this->_rows / $this->_limit );
 
            $start      = ( ( $this->_page - $boxes ) > 0 ) ? $this->_page - $boxes : 1;
            $end        = ( ( $this->_page + $boxes ) < $last ) ? $this->_page + $boxes : $last;

            $html       = '<ul class="<{__CSS_DIR__}>paginator.css">';

            $class      = ( $this->_page == 1 ) ? "disabled" : "";
            $html       .= '<li class="<{__CSS_DIR__}>paginator.css"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';

            if ( $start > 1 ) {
                $html   .= '<li><a href="?limit=' . $this->_limit . '&page=1">1</a></li>';
                $html   .= '<li class="disabled"><span>...</span></li>';
            }

            for ( $i = $start ; $i <= $end; $i++ ) {
                $class  = ( $this->_page == $i ) ? "active" : "";
                $html   .= '<li class="<{__CSS_DIR__}>paginator.css"><a href="?limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
            }

            if ( $end < $last ) {
                $html   .= '<li class="disabled"><span>...</span></li>';
                $html   .= '<li><a href="?limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>';
            }

            $class      = ( $this->_page == $last ) ? "disabled" : "";
            $html       .= '<li class="<{__CSS_DIR__}>paginator.css"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';

            $html       .= '</ul>';

            return $html;
        }
        
        private function getLimits($limit = 25){
            // SET LIMIT BOUNDARIES
            if(in_array($limit, $this->_allowed_limits)){
                return $limit;
            } else {
                return 25;
            }
        }
        
    }
    
}