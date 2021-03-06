<?php
require_once 'IController.php';
require_once '/../../default/controllers/Message.php';
require_once 'AccountController.php';

class Admin_IndexController extends IController {
    
    public static $BACKUP_FILE_PER_PAGE = 5;
    
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data = $auth->getIdentity();
            if ($data['role'] != Admin_AccountController::$ADMIN_ROLE) {
                    $this->_redirect('user/login');
            } else {
                $this->view->currentUser = $data;
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
//        echo 'Account Controller, index action';
        $this->redirect('admin/user');
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
        $modelMaster = new Default_Model_Master();
        $master = $modelMaster->getMasterData();
        $this->view->price = $master[Default_Model_Master::$KEY_COMA_PRICE];
        $this->view->rate = $master[Default_Model_Master::$KEY_TEACHER_FEE_RATE];
        
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
    	$modelMaster = new Default_Model_Master();
    	$master = $modelMaster->getMasterData();
    	$this->view->price = $master[Admin_Model_Master::$KEY_COMA_PRICE];
    	$this->view->rate = $master[Default_Model_Master::$KEY_TEACHER_FEE_RATE];
    	if(isset($param["download"])){
    		// create TSV file
    	$filename = "/public/TSV/ELS-UBT-".date("Y-m-d-H-i-s", time()).".tsv";
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
   			$write=fwrite($file,$info["username"]."\t".$info["name"]."\t".($info["total"] * $this->view->price *$this->view->rate )."\t".$info["address"]."\t".$info["phone"]."\t54\t".$info["bank_account"]. "\r\n");
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
        $masterModel = new Default_Model_Master();
        $dbModel = new Admin_Model_DB();
        $fileModel = new Default_Model_File();
        
        $masterData = $masterModel->getMasterData();
        $this->view->masterData = $masterData;
        
        // TODO: get autobackup status
        $this->view->autoBackup = true;
        
        $page = $this->getParam('page');
        if (!isset($page)) {
            $page = 1;
        }
        
        // バックアップしたファイルリストを取る
        $this->view->backupList = $dbModel->getBackupFilesPager($page, self::$BACKUP_FILE_PER_PAGE);
        
        if ($this->_request->isPost()) {
            $masterData = array();
            
            $comaPrice = $this->_request->getParam('coma_price');
            $teacherFeeRate = $this->_request->getParam('teacher_fee_rate');
//            $fileLocation = $this->_request->getParam('file_location');
            $lessonDeadline = $this->_request->getParam('lesson_deadline');
            $lockCount = $this->_request->getParam('lock_count');
            $loginFailLockTime = $this->_request->getParam('login_fail_lock_time');
            $sessonTime = $this->_request->getParam('session_time');
            $violationTime = $this->_request->getParam('violation_time');
            $backupTimeHour = $this->_request->getParam('backup_time_hour');
            $backupTimeMinute = $this->_request->getParam('backup_time_minute');
            
            $masterData[Default_Model_Master::$KEY_COMA_PRICE] = $comaPrice;
            $masterData[Default_Model_Master::$KEY_TEACHER_FEE_RATE] = $teacherFeeRate;
//            $masterData[Default_Model_Master::$KEY_FILE_LOCATION] = $fileLocation;
            $masterData[Default_Model_Master::$KEY_LESSON_DEADLINE] = $lessonDeadline;
            $masterData[Default_Model_Master::$KEY_LOCK_COUNT] = $lockCount;
            $masterData[Default_Model_Master::$KEY_LOGIN_FAIL_LOCK_TIME] = $loginFailLockTime;
            $masterData[Default_Model_Master::$KEY_SESSION_TIME] = $sessonTime;
            $masterData[Default_Model_Master::$KEY_VIOLATION_TIME] = $violationTime;
            $masterData[Default_Model_Master::$KEY_BACKUP_TIME] = $backupTimeHour*3600 + $backupTimeMinute*60;
            
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
            if (!$this->isNumber($lockCount) || $lockCount > 1000000000) {
                $this->view->errorMessage = Message::$M41212;
                return;
            }
            if (!$this->isNumber($loginFailLockTime) || $loginFailLockTime > 1000000000) {
                $this->view->errorMessage = Message::$M4125;
                return;
            }
            if (!$this->isNumber($sessonTime) || $sessonTime > 1000000000) {
                $this->view->errorMessage = Message::$M41213;
                return;
            }
            if (!$this->isNumber($violationTime) || $violationTime > 1000000000) {
                $this->view->errorMessage = Message::$M4126;
                return;
            }
            if (!$this->isNumber($backupTimeHour) || $backupTimeHour > 1000000000) {
                $this->view->errorMessage = Message::$M4127;
                return;
            }
            if (!$this->isNumber($backupTimeMinute) || $backupTimeMinute > 59) {
                $this->view->errorMessage = Message::$M4127;
                return;
            }
            
//             file location チェック
//            if ($fileLocation != $masterModel->getMasterValue([Admin_Model_Master::$KEY_FILE_LOCATION])) {
//                if (!$fileModel->setFileLocation($fileLocation)) {
//                    $this->view->errorMessage = Message::$M4123;
//                }
//            }
            $minutes = $backupTimeHour* 60 + $backupTimeMinute;
            if($minutes<=0){
            	$this->view->errorMessage = "バックアップ時間が１分以上";
            	return;
            }
            // データ更新
            if ($masterModel->setMasterData($masterData)) {
            	//create schedule
            	$path = realpath(APPLICATION_PATH . '/../')."\backup.bat";
            	echo "<div style='display: none;'>";
            	$command="schtasks /delete /TN AutoBackupDatabase /F";
            	exec($command);
            	$command="schtasks /create /SC MINUTE /MO $minutes /TN AutoBackupDatabase /TR $path";
//            	echo $command;
//                echo "<div style='display: none;'>";
                exec($command);
               echo "</div>";
                $this->view->masterData = $masterData;
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
    
    /**
     * 自動バックアップ処理
     */
    public function autoBackupAction() {
        $status = $this->getParam('turn');
        
        if ($status == 'on') {
            // TODO: turn autobackup on
            $this->_helper->FlashMessenger->addMessage("自動バックアップをオンにしている", 'backupSuccess');
        } else if ($status == 'off') {
            // TODO: turn autobackup off
            $this->_helper->FlashMessenger->addMessage("自動バックアップをオフにした", 'backupSuccess');
        }
        
        $this->redirect('admin/index/maintain');
    }

    /**
     * データベースを回復処理
     */
    public function restoreAction() {
        $file = $this->getParam('file');
        
        $dbModel = new Admin_Model_DB();
        $result = $dbModel->restore($file);
        
        if ($result) {
            $successMessage = str_replace("<filename>", $file, Message::$M41210);
            $this->_helper->FlashMessenger->addMessage($successMessage, 'backupSuccess');
        } else {
            $successMessage = str_replace("<filename>", $file, Message::$M41211);
            $this->_helper->FlashMessenger->addMessage($successMessage, 'backupSuccess');
        }
        
        $this->redirect('admin/index/maintain');
    }
    
    /**
     * 文字列が数字かどうかをチェック
     * 
     * @param string $string
     * @return boolean
     */
    protected function isNumber($string) {
        return ctype_digit($string);
    }
}