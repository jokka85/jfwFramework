<?php
/**
 * core/Fpr,.php
 *
 * Create forms based on the particular criteria.
 *
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace Form {

    use AdminSettings\AdminSettings;

    class FormCreator {

        /**
         * @var string
         * Name, id, and action of the form.
         */
        private $_formName = 'site_settings';

        /**
         * @var bool
         * If set to true, then the form will implement AJAX. If not then it will post to the same page.
         */
        private $_ajax = true;

        /**
         * @var null|string
         * Class to use for the form.
         */
        private $_formClass;

        function __construct($formName, $ajax = true, $formClass = null){
            $this->_formClass = (!is_null($formClass)) ? $formClass : $this->_formClass;
            $this->_ajax = $ajax;
            $this->_formName = (!is_null($formName)) ? $formName : $this->_formName;
        }

        /**
         * Being the form you will be using.
         *
         * @return string
         */
        public function start_form(){
            // NEED TO SET A BASE AJAX FILE
            return '<form name="' . $this->_formName . '" id="' . $this->_formName .
                '" action="update(\'' . $this->_formName . '\')">';
        }

        public function set_form($args = null){
            $inner = null;
            if(is_null($args)){
                // fetch args based on form name
                $adm = new AdminSettings();
                $all = $adm->getAllSettings();

                $args = (isset($all[$this->_formName])) ? $all[$this->_formName] : [];
            }

            foreach($args as $inputName => $v){

                $type = gettype($v);

                if($type == "string" && is_numeric($v)) {
                    $type = "integer";
                } else if($type == "string" && is_double($v)) {
                    $type = "double";
                } else if($type == "string" && strtolower($v) === ("true" || "false")) {
                    $type = "boolean";
                } else if($type == "string"){
                    //check for date format first
                    $date = \DateTime::createFromFormat("YYYY-mm-dd", $v);

                    if($date == true){
                        $type = "date";
                    } else if(strlen($v) > 255){
                        $type = "text";
                    } else {

                        $type = (preg_match("/\b(\.jpg|\.JPG|\.png|\.PNG|\.gif|\.GIF)\b/", $v)) ?
                            "file" : "string";

                    }
                 }

                 $inputName = $this->cleanName($inputName);

                $identifier = 'id="'.$inputName.'" name="'.$inputName.'"';

                $inner .= '<div class="form-' . (($type == "boolean") ? 'check row' : 'group') . '">
                            <label for="'.$inputName.'">'.$inputName.'</label>';

                switch($type){

                    // RADIO
                    case "boolean":
                        $inner .= '<div class="col-sm-10 form-check-inline">';
                        $inner .= '<label for="'.$inputName.'_true">True</label>'.
                                '<input class="form-check m-3" type="radio" name="'.$inputName.'" id="'.$inputName.'_true" value="true" ' . (($v == "true") ? "checked" : "") . " /> ";
                        $inner .= '<label for="'.$inputName.'_false">False</label>'.
                                '<input class="form-check m-3" type="radio" name="'.$inputName.'" id="'.$inputName.'_false" value="false" ' . (($v == "false") ? "checked" : "") . " /> ";
                        $inner .= "</div>";
                        break;

                    case "integer":
                        $inner .= '<input class="form-control" type="number" ' . $identifier . ' value="'.$v.'" />';
                        break;

                    case "double":
                        $inner .= "<input class=\"form-control\" type='number' step='0.01' value='".$v."' ".$identifier." placeholder='0.00' />";
                        break;

                    case "text":
                        $inner .= "<textarea class=\"form-control\" " . $identifier . ">" . $v . "</textarea>";
                        break;

                    case "date":
                        // break up date
                        // list($year, $month, $day) = explode("-", $v);
                        $inner .= '<input class="form-control" type="date" ' . $identifier .' value="'.$v.'"/>';
                        break;

                    case "file":
                        $inner .= '<input class="form-control" type="file" ' . $identifier.' accept="image/*" />';
                        $inner .= 'Existing: <img src="' . IMG_DIR . $v . '" alt="Logo" />';
                        break;

                    default:
                        $inner .= '<input class="form-control" type="text" ' . $identifier . ' value="' . $v . '" />';
                        break;
                }

                $inner .= "</div>";
            }

            return $inner;
        }

        /**
         * End the form you are using.
         *
         * @return string
         */
        public function end_form(){
            return '<div class="form-group p-3"> <button type="submit" class="btn btn-primary"> Submit </button></div></form>';
        }

        private function cleanName($original){
            $name = null;
            if(strpos($original, "_") !== false) {
                $words = explode("_", $original);
                for($i = 0; $i < count($words); $i++) {
                    if ($words[$i] != "mask") {
                        $name .= ucfirst($words[$i]) . (($i == (count($words) - 1)) ? "" : " ");
                    }
                }
            } else {
                $name = ucfirst($original);
            }
            return $name;
        }

    }

}