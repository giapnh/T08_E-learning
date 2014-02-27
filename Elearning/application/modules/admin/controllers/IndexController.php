<?php
class Admin_IndexController extends Zend_Controller_Action {
    
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
     * 課金情報画面
     * 
     */
    public function paymentInfoAction() {
        
    }
    
    /**
     * 月の課金情報
     * @param int $year 年
     * @param int @month 月
     */
    public function monthPaymentInfoAction() {
        
    }
    
    /**
     * 保守
     */
    public function maintainAction() {
        
    }
}