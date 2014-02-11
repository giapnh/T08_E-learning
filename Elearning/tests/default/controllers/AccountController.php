<?php
class AccountController extends Zend_Controller_Action {
	public function indexAction() {
		$mAccount = new Model_Account ();
		$this->view->data = $mAccount->listall ();
	}
}