<?php

require_once 'IController.php';
require_once 'AccountController.php';

class Admin_UserController extends IController {
    
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
     */
    public function indexAction() {
        
    }
    
    /**
     * ユーザ情報画面
     */
    public function infoAction() {
        
    }
    
    /**
     * 管理者の情報画面
     */
    public function adminInfoAction() {
        
    }
    
    /**
     * 管理者を追加画面
     */
    public function addAdminAction() {
        
    }
    
}