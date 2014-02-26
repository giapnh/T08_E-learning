<?php

require 'Message.php';

class IController extends Zend_Controller_Action {

    public function init() {
        $baseurl = $this->_request->getbaseurl();
        $this->view->doctype();
        $this->view->headtitle("E-learning");
        $this->view->headMeta()->appendName("keyword", "E-learning...");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style.css");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style_2.css");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/mystyle.css");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/group.css");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/profile.css");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/ta123_styles.css");
    }

}
