<?php

require 'IController.php';

class Admin_AccountController extends IController {

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
     * ログイン
     * @param String $username ユーザ名
     * @param String $password パースワード
     * @param Boolean $remember 存在するか
     */
    public function loginAction() {
        
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
