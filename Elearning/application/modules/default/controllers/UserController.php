<?php
require 'IController.php';
class UserController extends IController {
	public function indexAction(){
		$this->redirect('user/login');
	}

	public function loginAction() {
		// 		//Ip cache
		// 		$frontendOptions = array(
		// 				'lifetime' => Code::$LOGIN_FAIL_LOCK_TIME, // cache lifetime of 2 hours
		// 				'automatic_serialization' => true
		// 		);

		// 		$backendOptions = array(
		// 				'cache_dir' => 'C:/Users/Public' // Directory where to put the cache files
		// 		);

		// 		$cache = Zend_Cache::factory('Core',
		// 				'File',
		// 				$frontendOptions,
		// 				$backendOptions);
		// 		// see if a cache already exists:
		// 		if($result = $cache->load('ip_locking')) {
			
		// 			// 			$cache->save($result, 'myresult');
		// 		} else {
		// 			// cache hit! shout so that we know
		// 			echo "This one is from cache!\n\n";
		// 		}
		$form = new Default_Form_Login ();
		$this->view->form = $form;
		if ($this->_request->isPost ()) {
			$authAdapter = new Default_Model_Account();
			$auth = Zend_Auth::getInstance();
			$uname = $this->_request->getParam ( 'username' );
			$paswd = $this->_request->getParam ( 'password' );
			$role = $this->_request->getParam('role');
			// If filed is null
			if(trim($uname) == ''){
				$this->view->errorMessage = Message::$M001;
				return;
			}else if(trim($paswd) == ''){
				$this->view->errorMessage = Message::$M002;
				return;
			}
			$flag = false;

			if ($authAdapter->isValid($uname,$paswd,$role)) {
				$curr_ip =  $_SERVER['REMOTE_ADDR'];
				if($curr_ip == "::1"){
					$curr_ip = "127.0.0.1";
				}
				if($authAdapter->checkIpValid($uname, $curr_ip)){
					// 					$data = $authAdapter->getResultRowObject ( null, array (
					// 							'password'
					// 					) );
					// 					$auth->getStorage ()->write ( $data );
					$flag = true;
				}else{
					echo "Invalid ip address";

					$flag = false;
				}
			}else{
				$authAdapter->incLoginFailure($uname);
				$this->view->errorMessage = Message::$M003;
			}
			// 			if ($flag == true) {
			// 				if($role == 1){
			// 					$this->_redirect ( '/student/index' );
			// 				}else{
			// 					$this->_redirect ( '/teacher/index' );
			// 				}
			// 			}
		}
	}

	public function registerAction(){
		$account = new Default_Model_Account();
		$form = new Default_Form_Register ();
		$this->view->form = $form;
		if ($this->_request->isPost ()) {
			$data = $this->_request->getParams();
			if($data['password'] != $data['repassword']){
				$this->view->errorMessage = "Password and confirm password don't match.";
				$this->redirect('user/register');
				return;
			}
			if($account->isExits($data['username'])){
				$this->view->errorMessage = "Name already taken. Please choose      another one.";
				return;
			}
			unset($data['repassword']);
			$users->insert($data);
			$this->_redirect('auth/login');
		}
			
	}

	public function logoutAction(){
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
	}
}