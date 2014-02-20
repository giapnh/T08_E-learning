<?php
require 'IController.php';
//require 'RegisterController.php';

class IndexController extends IController {
	public function indexAction() {
		$auth = Zend_Auth::getInstance ();
		$infoUser = $auth->getIdentity ();
		$this->view->fullName = $infoUser->full_name;
	}
	/**
	 * Hàm này gọi trước khi gọi action
	 */
	public function preDispatch() {
		$auth = Zend_Auth::getInstance ();
		if (! $auth->hasIdentity ()) {
			if ($this->_request->getActionName () != 'login') {
				$this->_redirect ( 'user/login' );
			}
		}
	}
	
}