<?php
/**
 * core\ErrorHandler.php
 *
 * The ErrorHandler will take the error information and place the error into a file and/or the database
 *
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace ErrorHandler {

    use Database\Table\Table;

    /**
     * Class ErrorHandler
     * @package ErrorHandler
     *
     * Handles the errors. How it is handled depends on the setup within the config/settings.php file.
     *
     */
    class ErrorHandler extends \Exception {

        /**
         * ErrorHandler constructor.
         * @param null|String $_msg
         * Added message to place within the Error. This can be used to be more specific to what the issue is that has
         * been encountered.
         */
        function __construct($_msg = null, $_frontEndMsg = null){
            $this->message .= (is_null($_msg)) ? "" : "Error: " . $_msg;
        }

        /**
         * Base method that will decide where the error information is to be written. It will write in either the
         * logs/error.logs file, the database from the configuration in config/settings.php, or both.
         *
         * If there is a problem sending information to the database, the method will automatically  send the
         * error the the logs/error.logs file in order to continue to keep track of errors.
         *
         * @param bool $_toFile
         * If set to TRUE then the database settings are bypassed. If set to FALSE then it proceeds to whatever the
         * default setting is from the config/settings.php file.
         */
        public function write_to_errors($_toFile = false){

            // add to error handler in database
            $eh_settings = $GLOBALS['settings']['ErrorHandler'];
            $db_down = false;

            if($eh_settings['use'] == ('Database' || 'Both') && $_toFile == false) {
                if(!$this->write_to_database()){
                    // write to file as backup
                    $this->write_to_file();
                    $db_down = true;
                }
            }

            if($eh_settings['use'] == ('Default' || 'Both') && $db_down == false){
                $this->write_to_file();
            }
        }

        /**
         * Writes information to database if the settings call for it. The default databse settings for the error_handler
         * can be found in config/settings.php under 'ErrorHandler'.
         *
         * @return bool
         * Returns true if \PDO statement is returned and false if null
         */
        private function write_to_database(){
            $db = $GLOBALS['settings']['ErrorHandler']['Database'];
            $table = new Table();

            if(!$table->setTable($db['table'])){
                return false;
            }

            $table->setPrimary($db['primary']);

            $val = [
                'error_code' => $this->getCode(),
                'error_file' => $this->getFile(),
                'error_line' => $this->getLine(),
                'error_message' => $this->getMessage(),
                'error_trace' => $this->getTraceAsString()
            ];

            $stmt = $table->set($val);

            return ($stmt != null) ? true : false;
        }

        /**
         * Writes the error to the logs/errors.log file. Feel free to change up the look if you would like. This is
         * simply a default setup provided to you.
         *
         * @return bool
         * If there is a problem appending to the logs/errors.log file then this will return FALSE, otherwise returns TRUE
         */
        private function write_to_file(){
            $eh = $GLOBALS['settings']['ErrorHandler'];

            $text = "DATE: " . date("Y-m-d H:i:s") . " - Error Code: " . $this->getCode() . " in file " . $this->getFile()
                    . " Line # " . $this->getLine() . PHP_EOL . $this->getMessage() . PHP_EOL
                    . "Trace: " . PHP_EOL . $this->getTraceAsString() . PHP_EOL .
                "----------------------------------------------------------------------------------" . PHP_EOL;

            if(!file_put_contents($eh['error_file'], $text, FILE_APPEND | LOCK_EX)){
                return false;
            } else {
                return true;
            }
        }

    }

}