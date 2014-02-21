<?php
require 'IController.php';
//require 'RegisterController.php';
class UserController extends IController {
	public function indexAction(){
		$this->redirect('user/login');
	}

	public function loginAction() {
		$form = new Default_Form_Login ();
		$this->view->form = $form;
		if ($this->_request->isPost ()) {
			// 1.Goi ket noi voi Zend Db
			$db = Zend_Registry::get("connectDB");
			//$db = Zend_Db::factory($dbOption['adapter'],$dbOption['params']);
			// 2. Khoi tao Zend Autho
			$auth = Zend_Auth::getInstance ();
			// 3. Khai bao bang va 2 cot se su dung so sanh trong qua trinh
			// login
			$authAdapter = new Zend_Auth_Adapter_DbTable ( $db );
			$authAdapter->setTableName ( 'user' )->setIdentityColumn ( 'username' )->setCredentialColumn ( 'password' );

			// 4. Lay gia tri duoc gui qua tu FORM
			$uname = $this->_request->getParam ( 'username' );
			$paswd = $this->_request->getParam ( 'password' );
			$role = $this->_request->getParam('role');
			// If filed is null
			if(trim($uname) == ''){
				$this->view->errorMessage = "ユーザー名は空にすることはできません";
				return;
			}else if(trim($paswd) == ''){
				$this->view->errorMessage = "パスワードは空にすることはできません";
				return;
			}
			// 5. Dua vao so sanh voi du lieu khai bao o muc 3
			$authAdapter->setIdentity ( $uname );
			$authAdapter->setCredential ( $paswd );
			//          $authAdapter->setCredential ( md5 ( $paswd ) );

			// 6. Kiem tra trang thai cua user neu status = 1 moi duoc login
			$select = $authAdapter->getDbSelect ();
			$select->where ( 'role = '.$role );

			// 7. Lay ket qua truy van
			$result = $auth->authenticate ( $authAdapter );
			$flag = false;

			if ($result->isValid ()) {
				$data = $authAdapter->getResultRowObject ( null, array (
						'password'
				) );
				$auth->getStorage ()->write ( $data );
				$flag = true;
			}else{
				$this->view->errorMessage = "ログイン情報は正しくない！";
			}
			if ($flag == true) {
				if($role == 1){
					$this->_redirect ( '/student/index' );
				}else{
					$this->_redirect ( '/teacher/index' );
				}
			}
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