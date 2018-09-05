<?php
/**
 * core/AdminSettings.php
 * 
 * The AdminSettings namespace will handle all settings set by the owner/administrator. 
 * 
 * The options for the settings can be found in config/settings.php under "admin_settings" 
 * of the array.
 * 
 * If the database does not contain the information for some reason then the 
 * method `sortSettings` will automatically pull from the settings.php file.
 * 
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace AdminSettings {
    
    use Database\Table;
    
    class AdminSettings {
        
        /**
         * Array where the settings will be stored
         * @var array
         */
        private $_settings;
        
        /**
         * Immediately fetch settings on construct
         */
        function __construct(){
            
            $admin_settings = $GLOBALS['settings']['admin_settings'];

            if($admin_settings['use'] == 'Database'){        
                
                $this->_settings = $this->_dbSettings();
                
            } else {
                $this->_settings = $admin_settings;
            }
        }
        
        /**
         * Returns all of the admin_settings. This is likely not necessary in 
         * any case but it is available if needed.
         * 
         * @return Array
         */
        public function getAllSettings(){
            return $this->_settings;
        }
        
        /**
         * Returns only the admin_settings under 'user_information' within the array. 
         * 
         * This information would be useful when creating or editing user information.
         * 
         * @return array
         */
        public function getUserSettings(){
            return $this->_settings['user_information'];
        }

        /**
         * Returns only the admin_settings under 'site_settings' within the array.
         *
         * @return array
         */
        public function getSiteSettings(){
            return $this->_settings['site_settings'];
        }

        /**
         * Returns only the admin_settings under 'masks' within the array.
         *
         * This is used within forms and for validation.
         *
         * @return array
         */
        public function getMaskSettings(){
            return $this->_settings['masks'];
        }

        /**
         * Returns the main "topics" for the administrator. User Information, Site_wide, etc.
         *
         * @return array
         */
        public function getAdminTopics(){
            return array_keys($this->_settings);
        }

        /**
         * Fetches the admin_settings from the Database table and returns the 
         * result in an array.
         * 
         * @return array
         */
        private function _dbSettings(){
            $admin_settings = $GLOBALS['settings']['admin_settings'];
            $db_settings = $admin_settings['Database'];
            $settings = [];

            // GATHER ADMIN SETTINGS
            $admin = new Table\Table();
            $admin->setTable($db_settings['table']);
            $admin->setPrimary($db_settings['primary']);
            try {
                $setStmt = $admin->get();

                if ($setStmt != null) {
                    $settings = $admin->fetchAll($setStmt);
                    $setStmt->closeCursor();
                } else {
                    throw new \Exception();
                }
            } catch (\Exception $e){
                $error = "STMT IS EMPTY::: ERROR CODE: " . $e->getCode() . " | ERROR FILE: " . $e->getFile() .
                    " | ERROR LINE: " . $e->getLine() . " | ERROR MESSAGE: " . $e->getMessage();

                $file = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR .
                    'logs' . DIRECTORY_SEPARATOR . "errors.log";

                error_log($error, 3, $file);

            }

            $temp_settings = [];

            if(empty($settings)) {
                // DATABASE DID NOT GO THROUGH
                // SET SETTINGS TO DEFAULT
                $settings = $admin_settings['user_information'];
            } else {

                // break up settings
                for ($i = 0; $i < count($settings); $i++) {

                    $value = $settings[$i][$db_settings['value']];

                    if (strtolower($value) == 'true') {
                        $value = true;
                    } else if (strtolower($value) == 'false') {
                        $value = false;
                    }

                    $temp_settings[$settings[$i][$db_settings['primary']]] = $value;
                }

            }
            
            return $this->_sortSettings($temp_settings);
        }
        
        /**
         * When creating the array for the admin_settings, there may be several 
         * different areas of interest.
         * 
         * Ex: User information, email information, social media content, front 
         * page, etc.
         * 
         * @param array $settings
         * @return array
         */
        private function _sortSettings($settings){
            // get default array
            $admin_settings = $GLOBALS['settings']['admin_settings'];
            
            // unset some things
            unset($admin_settings['use']);
            unset($admin_settings['Database']);
            
            $temp_settings = [];
            
            foreach($admin_settings as $main_key => $arr){
                // main key such as user_information
                if(is_array($arr)){
                    foreach($arr as $key => $val){
                        $temp_settings[$main_key][$key] = (isset($settings[$key])) ?
                            $settings[$key] :
                            $val;
                    }
                }                
            }
            
            return $temp_settings;
        }
        
    }

}
    