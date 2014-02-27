<?php

require 'IController.php';

//require 'RegisterController.php';
class StudentController extends IController {

    /**
     * Check login or not yet
     */
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_request->getActionName() != 'login') {
                $this->_redirect('user/login');
            }
        }
    }

    /**
     * If loged in, get user information
     */
    public function initial() {
        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        $this->view->user_info = $infoUser;
    }

    public function indexAction() {
        $this->initial();
    }

    public function profileAction() {
        $this->initial();
    }

}
