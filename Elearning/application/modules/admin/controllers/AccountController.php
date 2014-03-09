<?php

require_once 'IController.php';
require '/../../default/controllers/Message.php';

class Admin_AccountController extends IController {
    
    public static $ADMIN_ROLE = 3;

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
                if ($this->_request->getActionName() != 'login') {
                    $this->_redirect('admin/account/login');
                }
            }
        } elseif ($this->_request->getActionName() != 'login') {
            $this->_redirect('admin/account/login');
        }
    }
    
    /**
     * 管理者の個人情報画面
     */
    public function indexAction() {
        
    }

    /**
     * ログイン
     * @param String username ユーザ名
     * @param String password パースワード
     * @param Boolean remember 存在するか
     */
    public function loginAction() {
        $form = new Admin_Form_Login ();
        $this->view->form = $form;
        
        if ($this->_request->isPost()) {
            // 「Post」パラメータを取る
            $username = $this->_request->getParam('username');
            $password = $this->_request->getParam('password');
            $role = $this->_request->getParam('role');
            
            // インプットがなしチェック
            if (trim($username) == '') {
                $this->view->errorMessage = Message::$M001;
                return;
            } else if (trim($password) == '') {
                $this->view->errorMessage = Message::$M002;
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
                
                if ($accountModel->isAllowedIp($username, $ipStr)) {
                    // ログインセッションを保存
                    $userInfo = $accountModel->getAdminInfo($username);
                    $userInfo['role'] = Admin_AccountController::$ADMIN_ROLE;
                    $auth->getStorage()->write($userInfo);
                    
                    // ホームページに移転する
                    $this->redirect("/admin/user");
                } else {
                    $this->view->errorMessage = Message::$M0031;
                }
                
            } else {
                $this->view->errorMessage = Message::$M0031;
                return;
            }
        }
    }

    /**
     * IP追加画面
     */
    public function addIpAction() {
        
    }

    /**
     * 「Verify」コード更新画面
     */
    public function changeVerifyCodeAction() {
        
    }

    /**
     * パースワード更新画面
     */
    public function changePasswordAction() {
        
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
