<?php

require_once 'IController.php';
require_once 'AccountController.php';
require_once '/../../default/controllers/Message.php';

class Admin_UserController extends IController {
    
    public static $PAGE_LIMIT = 10;

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
        $usersPager = $userModel->getUsers($page, $limit, $orderBy, $order);
        $this->view->users = $usersPager['users'];
        $this->view->orderBy = $orderBy;
        $this->view->order = $order;
        $this->view->pager = array(
            'page' => $usersPager['page'],
            'totalPages' => $usersPager['totalPages'],
            'limit' => $usersPager['limit'],
            'next' => $usersPager['next'],
            'pre' => $usersPager['pre']
        );
        
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
        
    }
    
    /**
     * 管理者を追加処理
     */
    public function addAdminAction() {
        
    }
    
}