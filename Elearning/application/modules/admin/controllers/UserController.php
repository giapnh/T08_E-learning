<?php
class Admin_UserController extends Zend_Controller_Action {
    /**
     * ユーザリスト画面
     */
    public function indexAction() {
//            $muser=new Default_Model_Account;
//            $data=$muser->listAll();
//            print_r($data);
        echo 'Account Controller, index action';
    }
    
    /**
     * ユーザ情報画面
     */
    public function userInfoAction() {
        
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