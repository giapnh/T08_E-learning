<?php
require 'IController.php';
class RegisterController extends IController {
	public function indexAction() {
		$form = new Default_Form_Register();
		$users = new Default_Model_Account;
		$this->view->form = $form;
		$mask = APPLICATION_PATH."/../public/images/captcha/*.png";
		array_map("unlink",glob($mask));

		if ($this->_request->isPost ()) {
			//$name = $this->_request->getPost ( 'email' );
			if($form->isValid($_POST)){
				$data = $form->getValues();
				if($data['password'] != $data['repass']){
					$this->view->errorMessage = "Password and Confirm Password don't match!";
					return;
				}
				if($users->isExits($data['username'])){
					$this->view->errorMessage = "Username already existed. Please choose another one.";
					return;
				}

				unset($data['repass']);
				unset($data['captcha']);
				$data['password'] = md5($data['password']);
				$users->insert($data);
				$this->_redirect('index/login');
			}
		}
	}
}