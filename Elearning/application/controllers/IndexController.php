<?php
require 'IController.php';
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
				$this->_redirect ( '/index/login' );
			}
		}
	}
	public function loginAction() {
		$form = new Form_Login ();
		if ($this->_request->isPost ()) {
			
			// 1.Goi ket noi voi Zend Db
			$db = Zend_Registry::get ( 'connectDB' );
			// $db = Zend_Db::factory($dbOption['adapter'],$dbOption['params']);
			// 2. Khoi tao Zend Autho
			$auth = Zend_Auth::getInstance ();
			
			// 3. Khai bao bang va 2 cot se su dung so sanh trong qua tronh
			// login
			$authAdapter = new Zend_Auth_Adapter_DbTable ( $db );
			$authAdapter->setTableName ( 'account' )->setIdentityColumn ( 'ac_username' )->setCredentialColumn ( 'ac_password' );
			
			// 4. Lay gia tri duoc gui qua tu FORM
			$uname = $this->_request->getParam ( 'username' );
			$paswd = $this->_request->getParam ( 'password' );
			
			// 5. Dua vao so sanh voi du lieu khai bao o muc 3
			$authAdapter->setIdentity ( $uname );
			$authAdapter->setCredential ( $paswd );
			// $authAdapter->setCredential ( md5 ( $paswd ) );
			
			// 6. Kiem tra trang thai cua user neu status = 1 moi duoc login
			$select = $authAdapter->getDbSelect ();
			// $select->where ( 'status = 1' );
			
			// 7. Lay ket qua truy van
			$result = $auth->authenticate ( $authAdapter );
			$flag = false;
			if ($result->isValid ()) {
				// 8. Lay nhung du lieu can thiet trong bang users neu login
				// thanh cong
				$data = $authAdapter->getResultRowObject ( null, array (
						'ac_password' 
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
}