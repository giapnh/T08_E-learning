<?php
require 'IController.php';
class RegisterController extends IController {
	public function indexAction() {
		$form = new Form_Register ();
		if ($this->_request->isPost ()) {
			$name = $this->_request->getPost ( 'email' );
			
		}
		$this->view->form = $form;
	}
}