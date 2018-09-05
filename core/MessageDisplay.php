<?php
/**
 * core\MessageDisplay.php
 *
 * MessageDisplay handles the messages that the user will see based upon the type of message and how it is to be
 * displayed.
 *
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace MessageDisplay {

    /**
     * List of constants used to determine message type.
     */
    DEFINE ('ERROR_MSG', 0);
    DEFINE ('WARNING_MSG', 1);
    DEFINE ('SUCCESS_MSG', 2);

    /**
     * List of constants to determine how the message will be formatted.
     */
    DEFINE ('PLAIN_MSG', 0);
    DEFINE ('HTML_MSG', 1);

    /**
     * Class MessageHandler
     * @package MessageDisplay
     *
     * Create or delete messages to be displayed to a user. This will be messages such as Errors, Warnings, or Success.
     *
     * Each type will hold its own CSS information within the root/css/main.css file. This can be edited as well.
     *
     * Example:
     * <pre>

        $msgHandler = new \MessageDisplay\MessageHandler();
        $msgHandler->add_message("Success TEST: <b>#</b>" . rand(1,300), SUCCESS_MSG, PLAIN_MSG);
        $msgHandler->add_message("Error TEST: <b>#</b>" . rand(1,300), ERROR_MSG, PLAIN_MSG);
        $msgHandler->add_message("Warning TEST: <b>#</b>" . rand(1,300), WARNING_MSG, HTML_MSG);
        $msgHandler->build_messages();
     *  </pre>
     *
     */
    class MessageHandler {

        /**
         * Type of message to be displayed
         * @var array
         */
        private $_msgType = [
            'error',
            'warning',
            'success'
        ];

        /**
         * The format at which to display the message
         * @var array
         */
        private $_msgFormat = [
            'plain',
            'html'
        ];

        /**
         * Message to be placed into the session.
         * @var Array
         */
        protected $_msgs = [];

        /**
         * Add a message to the session. The method asks for three variables to work with.
         *
         * $_msg = Message to be sent to the session
         * $type = Type of message (ERROR_MSG = 0 (Default), WARNING_MSG = 1, SUCCESS_MSG = 2)
         * $format = The format for the message to be displayed in (PLAIN_MSG = 0 (default), HTML_MSG = 1)
         *
         * @param String $_msg
         * @param int $type
         * @param int $format
         * @return bool
         * Returns TRUE if message isn't null, FALSE if message is null.
         */
        public function add_message($_msg, $type = ERROR_MSG, $format = PLAIN_MSG){

            $msgType   = ($this->checkType($type)) ? $this->_msgType[$type] : $this->_msgType[0];
            $msgFormat = ($this->checkFormat($format)) ? $this->_msgFormat[$format] : $this->_msgFormat[0];

            if(!is_null($_msg)){

                $this->_msgs[$msgType][] = [
                    'format' => $msgFormat,
                    'message' => htmlentities($_msg)
                ];

                return true;

            } else {
                return false;
            }
        }

        /**
         * Build the message(s) into the session array
         *
         * @return void
         */
        public function build_messages(){
            foreach($this->_msgs as $type => $arr){
                $_SESSION[$type] = $arr;
            }
        }

        /**
         * Clears the messages from the array / session.
         *
         * @param bool $session
         * <pre>
         *      Defaults to <u><b>TRUE</b></u> to remove messages form the SESSION varaible
         *      but can be set to <u><b>FALSE</b></u> to ignore SESSIONS.
         * </pre>
         */
        public function clear_messages($session = true){
            $this->_msgs = [];

            if($session){
                foreach($this->_msgType as $type){
                    unset($_SESSION[$type]);
                }
            }

        }

        public function getMsgsArray(){
            return $this->_msgs;
        }

        public function getSessionMsgs(){
            $arr = [];
            foreach($this->_msgType as $type){
                if(isset($_SESSION[$type])){
                    $arr[$type][] = $_SESSION[$type];
                }
            }
            return $arr;
        }

        public function checkType($type){
            return (isset($this->_msgType)) ? true : false;
        }

        public function checkFormat($format){
            return (isset($this->_msgFormat[$format])) ? true : false;
        }

        public function isHtml($format){
            return ($this->_msgFormat[HTML_MSG] == $format) ?  true : false;
        }

    }

    /**
     * Class MessageDisplay
     * @package MessageDisplay
     *
     * Any message given will be displayed using this class.
     */
    class MessageDisplay {

        /**
         * Holds that Object for the MessageHandler.
         * @var MessageHandler
         */
        protected $_messageHandler;

        /**
         * MessageDisplay constructor.
         *
         * Calls the message handler in order to fetch the messages stored within the SESSIONS.
         */
        function __construct(){
            $this->_messageHandler = new MessageHandler();
        }

        /**
         * Use this to display the messages that have been stored within the session.
         *
         * @return null|string
         * <pre>
         *      Returns <b><u>NULL</u></b> if there are no messages. Otherwise it will return the
         *      full body of all messages.
         * </pre>
         */
        public function SHOW_MESSAGES(){
            $messages = $this->_messageHandler->getSessionMsgs();
            if(empty($messages)){
                return null;
            }

            $all_msgs = '';

            foreach($messages as $type => $arr){
                // $type = error, warning, or success
                // $arr = [0][1][2][3]
                foreach($arr as $msg){
                    $all_msgs .= $this->message_body($type, $msg[0]['message'], $msg[0]['format']);
                }
            }

            // clear messages from session
            $this->_messageHandler->clear_messages();

            return $all_msgs;
        }

        /**
         * Creates the DIV to store the message in based on the type by pulling the class from the
         * root/css/main.css file.
         *
         * @param string $class
         * Class to be called from the main.css file (error, warning, success)
         *
         * @param string $message
         * Message to be displayed within the DIV
         *
         * @param int $format
         * The format at which to show the message. If set to <b><u>PLAIN_MSG</u></b> (0) then the message remains
         * formatted with <i>htmlspecialchars</i>. If set to <b><u>HTML_MSG</u></b> (1) then initiate <i>htmlspecialchars_decode</i>.
         *
         * @return string
         * Returns the fully built DIV with message, class, and proper handling of message.
         */
        private function message_body($class, $message, $format = PLAIN_MSG){

            $msg = ($this->_messageHandler->isHtml($format)) ? html_entity_decode($message) : $message;

            return "<div class='msg_box ".$class."'>\r\n" .
                "<div class='msg_head'> " . strtoupper($class) . "</div>\r\n" .
                "<div class='msg_body'>" .
                $msg .
                    "\r\n</div>\r\n</div>\r\n";
        }

    }

}