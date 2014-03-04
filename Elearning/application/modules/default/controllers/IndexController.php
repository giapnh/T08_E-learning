<?php

require_once 'IController.php';

//require 'RegisterController.php';

class IndexController extends IController {

    public function indexAction() {
        $this->_redirect('user/login');
    }

    /**
     * Hàm này gọi trước khi gọi action
     */
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_request->getActionName() != 'login') {
                $this->_redirect('user/login');
            }
        }
    }

}
