<?php
require 'IController.php';
//require 'RegisterController.php';
class UserController extends IController {
	public function indexAction(){
	   $this->redirect('user/login');
	}
	
	public function loginAction() {
		$form = new Default_Form_Login ();
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
			$type = $this->_request->getParam('type');
			// 5. Dua vao so sanh voi du lieu khai bao o muc 3
			$authAdapter->setIdentity ( $uname );
			$authAdapter->setCredential ( $paswd );
			//          $authAdapter->setCredential ( md5 ( $paswd ) );

			// 6. Kiem tra trang thai cua user neu status = 1 moi duoc login
			$select = $authAdapter->getDbSelect ();
			// $select->where ( 'status = 1' );

			// 7. Lay ket qua truy van
			$result = $auth->authenticate ( $authAdapter );
			$flag = false;
			if ($result->isValid ()) {
				echo "login success";
				// 8. Lay nhung du lieu can thiet trong bang users neu login
				// thanh cong
				$data = $authAdapter->getResultRowObject ( null, array (
                        'password'
                        ) );

                        // 9. Luu nhung du lieu cua member vao session
                        $auth->getStorage ()->write ( $data );
                        $flag = true;
			}
			if ($flag == true) {
				$this->_redirect ( '/index/index' );
			}
		}
		$this->view->form = $form;
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