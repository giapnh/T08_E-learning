<?php

require_once 'IController.php';

class Admin_IndexController extends IController {
    
    public function indexAction() {
//            $muser=new Default_Model_Account;
//            $data=$muser->listAll();
//            print_r($data);
        echo 'Account Controller, index action';
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