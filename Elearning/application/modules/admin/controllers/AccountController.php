<?php

require_once 'IController.php';
require_once '/../../default/controllers/Message.php';
require_once '/../../default/controllers/Code.php';

class Admin_AccountController extends IController {


	public static $ADMIN_ROLE = 3;

	/**
	 *
	 * @var array 現在のログインしているユーザ情報
	 */
	private $currentUser;

	public function init(){
		parent::init();
	}

	/**
	 * ログイン情報をチェック
	 *
	 */
	public function preDispatch() {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$data = $auth->getIdentity();
			if ($data['role'] != Admin_AccountController::$ADMIN_ROLE) {
				$this->_redirect('user/login');
			} else {
				$this->currentUser = $data;
                                $this->view->currentUser = $data;
			}
		} elseif ($this->_request->getActionName() != 'login') {
			$this->_redirect('admin/account/login');
		}
	}

	/**
	 * 管理者の個人情報画面
	 */
	public function indexAction() {
		$adminModel = new Admin_Model_Admin();

		$create_admin = $adminModel->getAdminById($this->currentUser['create_admin']);
		$create_admin = $create_admin['username'];
		$this->currentUser['created'] = $create_admin;

		$this->view->adminInfo = $this->currentUser;

		$this->view->allowedIp = $adminModel->getAllowedIp($this->currentUser['id']);

		$messages = $this->_helper->FlashMessenger->getMessages('updateInfoSuccess');
		$this->view->messages = $messages;
		$errorMessages = $this->_helper->FlashMessenger->getMessages('updateInfoError');
		$this->view->errorMessages = $errorMessages;
	}

	/**
	 * ログイン
	 * @param String username ユーザ名
	 * @param String password パースワード
	 * @param Boolean remember 存在するか
	 */
	public function loginAction() {
		if($this->currentUser){
			$this->_redirect ("admin/user");
		}

		$form = new Admin_Form_Login ();
		$this->view->form = $form;

		if ($this->_request->isPost()) {
			// 「Post」パラメータを取る
			$username = $this->_request->getParam('username');
			$password = $this->_request->getParam('password');
			$role = $this->_request->getParam('role');

			// インプットがなしチェック
			if (trim($username) == '') {
				$this->view->errorMessage = Message::$M4011;
				return;
			} else if (trim($password) == '') {
				$this->view->errorMessage = Message::$M4012;
				return;
			}

			// ユーザ名とパースワードがデータベースにあっているかをチェック
			$accountModel = new Admin_Model_Account();
			$auth = Zend_Auth::getInstance();
			if ($accountModel->isValid($username, $password)) {

				// IPチェック
				$ipStr = $this->_request->getServer('REMOTE_ADDR');
				if ($ipStr === "::1") {
					$ipStr = "127.0.0.1";
				}

				// ユーザが存在しているかをチェック
				if (!$accountModel->isExits($username)) {
					$this->view->errorMessage = Message::$M4013;
				}

				if ($accountModel->isAllowedIp($username, $ipStr)) {
					// ログインセッションを保存
					$userInfo = $accountModel->getAdminInfo($username);
					$userInfo['role'] = Admin_AccountController::$ADMIN_ROLE;
					$auth->getStorage()->write($userInfo);

					// ホームページに移転する
					$this->redirect("/admin/user");
				} else {
					// IPがログインできない
					$this->view->errorMessage = Message::$M4015;
				}

			} else {
				// パースワードが正しくない
				$this->view->errorMessage = Message::$M4014;
				return;
			}
		}
	}

	/**
	 * IP追加画面
	 */
	public function addIpAction() {
		//        $userId = $this->_request->getParam('user_id');
		$userId = $this->currentUser['id'];

		if ($this->_request->isPost()) {
			$ip = $this->_request->getParam('ip');

			if ($ip == "") {
				$this->view->errorMessage = Message::$M049;
				return;
			}
			if (!preg_match(Code::$REGEX_IP, $ip)) {
				$this->view->errorMessage = Message::$M027;
				return;
			}
			$adminModel = new Admin_Model_Admin();
			if ($adminModel->isAllowedIp($userId, $ip)) {
				$this->view->errorMessage = Message::$M049;
				return;
			}
			if (!$adminModel->getAdminById($userId)) {
				$this->view->errorMessage = Message::$M048;
				return;
			}
			$adminModel->addIp($userId, $ip);
			$this->_helper->FlashMessenger->addMessage(Message::$M051, 'updateInfoSuccess');
			$this->redirect("admin/account");
		}
	}

	/**
	 * IPを削除処理
	 *
	 */
	public function deleteIpAction() {
		$ipId = $this->getParam('id');
		$ipModel = new Admin_Model_AdminIp();

		$ipModel->deleteIp($ipId);
		$this->_helper->FlashMessenger->addMessage(Message::$M4021, 'updateInfoSuccess');

		$this->redirect($_SERVER['HTTP_REFERER']);
	}
	public function changeIpAction() {
		if($this->_request->isPost()){
			$ipId = $this->getParam('id');
			$ip = $this->getParam('ip');
			$ipModel = new Admin_Model_AdminIp();

			$ipModel->update(array("ip" => $ip), "id = ".$ipId );
			 
		}
		exit();
	}
	/**
	 * パースワード更新画面
	 */
	public function changePasswordAction() {
		$submit = $this->getParam('submit');
		if (isset($submit)) {
			$current_password = $this->getParam('current-password');
			$new_password = $this->getParam('new-password');
			$new_password_confirm = $this->getParam('new-password-confirm');

			// インプットチェック
			if ($current_password == '') {
				$this->view->errorMessage = Message::$M4054;
				return;
			}
			if (!preg_match(Code::$REGEX_PASSWORD, $new_password)) {
				$this->view->errorMessage = Message::$M4051;
				return;
			}
			if ($new_password != $new_password_confirm) {
				$this->view->errorMessage = Message::$M4052;
				return;
			}

			$accountModel = new Admin_Model_Account();
			if ($accountModel->isValid($this->currentUser['username'], $current_password)) {
				// 新しいパースワードに更新する
				$accountModel->changePassword($this->currentUser['username'], $new_password);
				$this->_helper->FlashMessenger->addMessage(Message::$M4055, 'updateInfoSuccess');
				$this->redirect("admin/account");
			} else {
				// 現在のパースワードが正しくない
				$this->view->errorMessage = Message::$M4053;
			}

		}
	}

	/**
	 * ログアウト処理
	 */
	public function logoutAction() {
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$this->_redirect('admin/account/login');
	}
}
