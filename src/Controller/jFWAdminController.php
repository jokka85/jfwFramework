<?php

/**
 * src/ontroller/jFWAdminController.php
 *
 * The admin page controller.
 */

require 'jFWController.php';

use AdminSettings\AdminSettings;
use Form\FormCreator;

class jFWAdminController extends jFWController implements ControllerInterface {

    /**
     * @var array
     * Topics that are saved in the default settings file and databsae.
     */
    protected $_topics;

    function __construct(){
        parent::__construct();
        $this->setHeader('jFWAdminHeader');
        $this->setFooter('jFWAdminFooter');
    }

    public function index()
    {

        $args = $this->create_navigation();

        $type = null;

        if(isset($_GET['type'])){
            $type = filter_input(INPUT_GET, 'type');
        }

        $args['ADMIN_BODY'] = (!is_null($type) && in_array($type, $this->_topics)) ?
            $this->create_form($type) :
            "We're in..";

        $this->finalize($args);
    }

    private function create_navigation(){
        $adm = new AdminSettings();
        $this->_topics = $adm->getAdminTopics();

        $args = [];

        foreach($this->_topics as $topic){
            $name = null;
            if(strpos($topic, "_") !== false) {
                $words = explode("_", $topic);
                for($i = 0; $i < count($words); $i++){
                    $name .= ucfirst($words[$i]) . (($i == (count($words) - 1)) ? "" : " ");
                }
            } else {
                $name = ucfirst($topic);
            }

            $args["SETTING_LOOP"][] = ["SETTING_TOPIC" => $name];
        }

        return $args;
    }

    private function create_form($type){
        $form = new FormCreator($type);
        $html = $form->start_form();
        $html .= $form->set_form();
        return $html . $form->end_form();
    }

}