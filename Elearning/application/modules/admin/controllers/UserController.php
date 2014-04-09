<?php

require_once 'IController.php';
require_once 'AccountController.php';
require_once '/../../default/controllers/Message.php';
require_once '/../../default/controllers/Code.php';

class Admin_UserController extends IController {
    
    public static $PAGE_LIMIT = 10;
    private $currentUser;

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
            } else {
                $this->currentUser = $data;
            }
        } elseif ($this->_request->getActionName() != 'login') {
            $this->_redirect('admin/account/login');
        }
    }
    
    /**
     * ユーザリスト画面
     * 
     * @param int $page ページ
     * @param string $order_by
     * @param string $order
     */
    public function indexAction() {
        
        $page = $this->_request->getParam('page');
        $orderBy = $this->_request->getParam('order_by');
        $order = $this->_request->getParam('order');
        $status = $this->getParam('status');
        
        if (!isset($page)) {
            $page = 1;
        }
        if (!isset($orderBy)) {
            $orderBy = Admin_Model_User::$USERNAME;
        }
        if (!isset($order)) {
            $order = Admin_Model_User::$DESC;
        }
        
        $limit = Admin_UserController::$PAGE_LIMIT;
        
        // ユーザリストと取る
        $userModel = new Admin_Model_User();
        if (isset($status) && $status != 0) {
            $usersPager = $userModel->getUsersByStatus($page, $limit, $orderBy, $order, $status);
            $this->view->users = $usersPager['users'];
            $this->view->orderBy = $orderBy;
            $this->view->order = $order;
            $this->view->status = $status;
            $this->view->pager = array(
                'page' => $usersPager['page'],
                'totalPages' => $usersPager['totalPages'],
                'limit' => $usersPager['limit'],
                'next' => $usersPager['next'],
                'pre' => $usersPager['pre']
            );
        } else {
            $usersPager = $userModel->getUsers($page, $limit, $orderBy, $order);
            $this->view->users = $usersPager['users'];
            $this->view->orderBy = $orderBy;
            $this->view->order = $order;
            $this->view->status = 0;
            $this->view->pager = array(
                'page' => $usersPager['page'],
                'totalPages' => $usersPager['totalPages'],
                'limit' => $usersPager['limit'],
                'next' => $usersPager['next'],
                'pre' => $usersPager['pre']
            );
        }
        
        // 管理者リストを取る
        $adminModel = new Admin_Model_Account();
        $auth = Zend_Auth::getInstance();
        $data = $auth->getIdentity();
        $currentUsername = $data['username'];
        $admins = $adminModel->getAllAdmin($currentUsername);
        $this->view->admins = $admins;
        
        $messages = $this->_helper->FlashMessenger->getMessages('updateInfoSuccess');
        $this->view->messages = $messages;
        $errorMessages = $this->_helper->FlashMessenger->getMessages('updateInfoError');
        $this->view->errorMessages = $errorMessages;
    }
    
    /**
     * ユーザ情報画面
     * 
     * @param string $user_id ユーザID
     */
    public function infoAction() {
        $userId = $this->_request->getParam('user_id');
        
        $userModel = new Admin_Model_User();
        $this->view->user = $userModel->getUser($userId);
        $messages = $this->_helper->FlashMessenger->getMessages('updateInfoSuccess');
        $this->view->messages = $messages;
        $errorMessages = $this->_helper->FlashMessenger->getMessages('updateInfoError');
        $this->view->errorMessages = $errorMessages;
    }
    
    /**
     * 許可する処理
     * 
     * @param string user_id ユーザ名
     */
    public function acceptAction() {
        $userId = $this->_request->getParam('user_id');
        $userModel = new Admin_Model_User();
        if ($userModel->accept($userId) == true) {
            $this->_helper->FlashMessenger->addMessage(Message::$M036, 'updateInfoSuccess');
        } else {
            $this->_helper->FlashMessenger->addMessage(Message::$M040, 'updateInfoError');
        }
        $this->redirect("/admin/user/info?user_id=".$userId);
    }
    
    /**
     * 初期のパースワードにリセットする処理
     * 
     * @param string user_id ユーザ名
     */
    public function resetPasswordAction() {
        $userId = $this->_request->getParam('user_id');
        $userModel = new Admin_Model_User();
        if ($userModel->resetPassword($userId) == true) {
            $this->_helper->FlashMessenger->addMessage(Message::$M037, 'updateInfoSuccess');
        } else {
            $this->_helper->FlashMessenger->addMessage(Message::$M041, 'updateInfoError');
        }
        $this->redirect("/admin/user/info?user_id=".$userId);
    }
    
    /**
     * 初期のVerifyコードにリセットする処理
     * 
     * @param string user_id ユーザ名
     */
    public function resetVerifycodeAction() {
        $userId = $this->_request->getParam('user_id');
        $userModel = new Admin_Model_User();
        if ($userModel->resetPassword($userId) == true) {
            $this->_helper->FlashMessenger->addMessage(Message::$M038, 'updateInfoSuccess');
        } else {
            $this->_helper->FlashMessenger->addMessage(Message::$M042, 'updateInfoError');
        }
        $this->redirect("/admin/user/info?user_id=".$userId);
    }
    
    /**
     * ユーザを削除する処理
     * 
     * @param string user_id Description
     */
    public function deleteAction() {
        $userId = $this->_request->getParam('user_id');
        $userModel = new Admin_Model_User();
        if ($userModel->deleteUser($userId) == true) {
            $this->_helper->FlashMessenger->addMessage(Message::$M039, 'updateInfoSuccess');
        } else {
            $this->_helper->FlashMessenger->addMessage(Message::$M043, 'updateInfoError');
        }
        $this->redirect("/admin/user");
    }
    
    /**
     * 管理者の情報処理
     * 
     * 
     */
    public function adminInfoAction() {
        $userId = $this->_request->getParam('user_id');
        $adminModel = new Admin_Model_Admin();
        
        $adminInfo = $adminModel->getAdminById($userId);
        if ($adminInfo == null) {
            $this->view->errorMessage = Message::$M048;
        } else {
            $this->view->allowedIp = $adminModel->getAllowedIp($adminInfo['id']);
        }
        $this->view->adminInfo = $adminInfo;
        
        $messages = $this->_helper->FlashMessenger->getMessages('updateInfoSuccess');
        $this->view->messages = $messages;
        $errorMessages = $this->_helper->FlashMessenger->getMessages('updateInfoError');
        $this->view->errorMessages = $errorMessages;
    }
    
    /**
     * 管理者を削除処理
     * 
     * 
     * 
     */
    public function deleteAdminAction() {
        $userId = $this->_request->getParam('user_id');
        $adminModel = new Admin_Model_Admin();
        
        if ($adminModel->deleteUser($userId)) {
            $this->_helper->FlashMessenger->addMessage(Message::$M039, 'updateInfoSuccess');
        } else {
            $this->_helper->FlashMessenger->addMessage(Message::$M043, 'updateInfoError');
        }
        $this->redirect("admin/user/admin-info?user_id=".$userId);
    }


    /**
     * 管理者を追加処理
     */
    public function addAdminAction() {
        $params = $this->getAllParams();
        if ($this->_request->isPost()) {
            $username = $params["username"];
            $password = $params["password"];
            $passwordConfirm = $params["password_confirm"];
            
            if (!(isset($username)) || $username == "") {
                $this->view->errorMessage = Message::$M001;
                return;
            }
            if (!(isset($password)) || $password == "") {
                $this->view->errorMessage = Message::$M002;
                return;
            }
            if (!preg_match(Code::$REGEX_USERNAME, $username)) {
                $this->view->errorMessage = Message::$M006;
                return;
            }
            if (!preg_match(Code::$REGEX_PASSWORD, $password)) {
                $this->view->errorMessage = Message::$M007;
                return;
            }
            if ($passwordConfirm != $password) {
                $this->view->errorMessage = Message::$M008;
                return;
            }
            
            $adminModel = new Admin_Model_Admin();
            if ($adminModel->getAdminByUsername($username) != null) {
                $this->view->errorMessage = Message::$M034;
                return;
            }
            
            $adminModel->createAdmin($this->currentUser['id'], $username, md5($username . '+' . $password . '+' . Code::$PASSWORD_CONST));
            $this->redirect("admin/user");
        }
    }
    
    /**
     * 管理者のログインできるIPを追加処理
     */
    public function addIpAction() {
        $userId = $this->_request->getParam('user_id');
        
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
            $this->redirect("admin/user/admin-info?user_id=".$userId);
        }
    }
    
}