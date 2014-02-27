<?php
class Admin_AccountController extends Zend_Controller_Action {
    
    /**
     * 管理者の個人情報画面
     */
    public function indexAction() {
//            $muser=new Default_Model_Account;
//            $data=$muser->listAll();
//            print_r($data);
        echo 'Account Controller, index action';
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
}