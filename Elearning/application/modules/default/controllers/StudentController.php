<?php

require 'IController.php';

//require 'RegisterController.php';
class StudentController extends IController {

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        $this->view->user_info = $infoUser;
    }

    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_request->getActionName() != 'login') {
                $this->_redirect('user/login');
            }
        }
    }

}
