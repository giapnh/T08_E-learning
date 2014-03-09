<?php

require_once 'Code.php';
require_once 'Message.php';

class IController extends Zend_Controller_Action {

    public function init() {
        $baseurl = $this->_request->getbaseurl();
        $this->view->doctype();
        $this->view->headtitle("E-learning");
        $this->view->headMeta()->appendName("keyword", "E-learning...");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style.css");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style_2.css");
        $this->view->headScript()->appendFile($baseurl . "/public/js/jquery.min.js");
    }

}
