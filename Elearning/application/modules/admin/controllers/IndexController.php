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
            } else {
                $this->view->currentUser = $data;
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
        $modelLearn = new Admin_Model_Learn();
        $paymentInfos = $modelLearn->getTotalPaymentInfo();
        //Zend_Debug::dump($paymentInfos);
        $this->view->paymentInfos = $paymentInfos;
        $modelMaster = new Admin_Model_Master();
        $master = $modelMaster->getMasterData();
        $this->view->price = $master[Admin_Model_Master::$KEY_COMA_PRICE];
        
    }
    
    /**
     * 月の課金情報
     * @param int $year 年
     * @param int @month 月
     */
    public function monthPaymentInfoAction() {
    	$param = $this->getAllParams();
    	
    	if(!isset($param["year"]) || !isset($param["month"]))
    		$this->_redirect('admin/index/payment-info');
    	if($param["month"] < 10)
    		$param["month"] = "0".(int)$param["month"];
    	$this->view->param = $param;
    	$modelLearn = new Admin_Model_Learn();
    	$paymentInfos = $modelLearn->getPaymentInfoByMonth($param["month"], $param["year"]);
    	$this->view->paymentInfos = $paymentInfos;
    	$teacherPaymentInfos = $modelLearn->getTeacherPaymentInfoByMonth($param["month"], $param["year"]);
    	//Zend_Debug::dump($teacherPaymentInfos);
    	$this->view->teacherPaymentInfos = $teacherPaymentInfos;
    	$modelMaster = new Admin_Model_Master();
    	$master = $modelMaster->getMasterData();
    	$this->view->price = $master[Admin_Model_Master::$KEY_COMA_PRICE];
    	if(isset($param["download"])){
    		// create TSV file
    	$filename = "/public/TSV/file-".date("Y-m-d-H-i-s", time()).".tsv";
   		$path = APPLICATION_PATH."/..".$filename;
   		$file=fopen($path, "w");
   		$write=fwrite($file,"ELS-UBT-GWK54M78\t");
   		$write=fwrite($file,$param["year"]."\t".$param["month"]."\t");
   		$write=fwrite($file,date("Y\tm\td\tH\ti\ts", time())."\t");
   		$write=fwrite($file,$this->view->currentUser["username"]."\t");
   		$write=fwrite($file,$this->view->currentUser["username"]."\r\n");
   		foreach ($paymentInfos as $info):
   			$write=fwrite($file,$info["username"]."\t".$info["name"]."\t".($info["total"] * $this->view->price)."\t".$info["address"]."\t".$info["phone"]."\t18\t".$info["bank_account"]. "\r\n");
   		endforeach;
   		foreach ($teacherPaymentInfos as $info):
   			$write=fwrite($file,$info["username"]."\t".$info["name"]."\t".($info["total"] * $this->view->price *0.6 )."\t".$info["address"]."\t".$info["phone"]."\t54\t".$info["bank_account"]. "\r\n");
   		endforeach;
   		$write=fwrite($file,"END___END___END\t");
   		$write=fwrite($file,$param["year"]."\t".$param["month"]);
   		fclose($file);
   		$this->_redirect($filename);
    	}
    }
    
    /**
     * 保守処理
     */
    public function maintainAction() {
        $masterModel = new Admin_Model_Master();
        
        $masterData = $masterModel->getMasterData();
        $this->view->masterData = $masterData;
        
        if ($this->_request->isPost()) {
            $masterData = array();
            
            $comaPrice = $this->_request->getParam('coma_price');
            $teacherFeeRate = $this->_request->getParam('teacher_fee_rate');
            $fileLocation = $this->_request->getParam('file_location');
            $lessonDeadline = $this->_request->getParam('lesson_deadline');
            $loginFailLockTime = $this->_request->getParam('login_fail_lock_time');
            $violationTime = $this->_request->getParam('violation_time');
            $backupTime = $this->_request->getParam('backup_time');
            $masterData[Admin_Model_Master::$KEY_COMA_PRICE] = $comaPrice;
            $masterData[Admin_Model_Master::$KEY_TEACHER_FEE_RATE] = $teacherFeeRate;
            $masterData[Admin_Model_Master::$KEY_FILE_LOCATION] = $fileLocation;
            $masterData[Admin_Model_Master::$KEY_LESSON_DEADLINE] = $lessonDeadline;
            $masterData[Admin_Model_Master::$KEY_LOGIN_FAIL_LOCK_TIME] = $loginFailLockTime;
            $masterData[Admin_Model_Master::$KEY_VIOLATION_TIME] = $violationTime;
            $masterData[Admin_Model_Master::$KEY_BACKUP_TIME] = $backupTime;
        
            // インプットチェック
            if (!$this->isNumber($comaPrice) || $comaPrice > 1000000000) {
                $this->view->errorMessage = Message::$M4121;
                return;
            }
            if (!$this->isNumber($teacherFeeRate) || $teacherFeeRate > 100) {
                $this->view->errorMessage = Message::$M4122;
                return;
            }
            if (!$this->isNumber($lessonDeadline) || $lessonDeadline > 1000000000) {
                $this->view->errorMessage = Message::$M4124;
                return;
            }
            if (!$this->isNumber($loginFailLockTime) || $loginFailLockTime > 1000000000) {
                $this->view->errorMessage = Message::$M4125;
                return;
            }
            if (!$this->isNumber($violationTime) || $violationTime > 1000000000) {
                $this->view->errorMessage = Message::$M4126;
                return;
            }
            if (!$this->isNumber($backupTime) || $backupTime > 1000000000) {
                $this->view->errorMessage = Message::$M4127;
                return;
            }
            // file location チェック
            if (!mkdir($fileLocation, 0777, true)) {
                $this->view->errorMessage = Message::$M4123;
                return;
            }
            
            // データ更新
            if ($masterModel->setMasterData($masterData)) {
                $this->view->message = Message::$M4128;
            }
        } else {
            $messages = $this->_helper->FlashMessenger->getMessages('backupSuccess');
            if ($messages) {
                $this->view->message = $messages[0];
            }
        }
    }
    
    /**
     * バックアップ処理
     * 
     */
    public function backupAction() {
        $dbModel = new Admin_Model_DB();
        
        $result = $dbModel->backup_tables('localhost','root','','elearning');
        
        $successMessage = str_replace("<filename>", $result, Message::$M4129);
        $this->_helper->FlashMessenger->addMessage($successMessage, 'backupSuccess');
        
        $this->redirect('admin/index/maintain');
    }
    
    protected function isNumber($string) {
        return ctype_digit($string);
    }
}