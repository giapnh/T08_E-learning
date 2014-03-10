<?php
require_once 'IController.php';
require_once '/../../default/controllers/Message.php';
require_once 'AccountController.php';

class Admin_IndexController extends IController {
    
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
     * 保守処理
     */
    public function maintainAction() {
        $masterModel = new Admin_Model_Master();
        
        if ($this->_request->isPost()) {
            $masterData = array();
            $masterData[Admin_Model_Master::$KEY_COMA_PRICE] = $this->_request->getParam('coma_price');
            $masterData[Admin_Model_Master::$KEY_TEACHER_FEE_RATE] = $this->_request->getParam('teacher_fee_rate');
            $masterData[Admin_Model_Master::$KEY_FILE_LOCATION] = $this->_request->getParam('file_location');
            $masterData[Admin_Model_Master::$KEY_LESSON_DEADLINE] = $this->_request->getParam('lesson_deadline');
            $masterData[Admin_Model_Master::$KEY_LOGIN_FAIL_LOCK_TIME] = $this->_request->getParam('login_fail_lock_time');
            $masterData[Admin_Model_Master::$KEY_VIOLATION_TIME] = $this->_request->getParam('violation_time');
            $masterData[Admin_Model_Master::$KEY_BACKUP_TIME] = $this->_request->getParam('backup_time');
        
            if ($masterModel->setMasterData($masterData)) {
                $this->view->message = Message::$M044;
            } else {
                $this->view->errorMessage = Message::$M045;
            }
        }
        
        $masterData = $masterModel->getMasterData();
        $this->view->masterData = $masterData;
    }
}