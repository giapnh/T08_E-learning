<?php

require_once 'IController.php';

//require 'RegisterController.php';
class TeacherController extends IController {

    protected $user;
    protected $currentTeacherId;

    public function preDispatch() {
        $auth = Zend_Auth::getInstance();

        if (!$_SESSION) {
            session_start();
        }
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } else if (time() - $_SESSION['CREATED'] > Code::$SESSION_TIME) {
            // １時間後自動にログアウトしています。
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
            $auth->clearIdentity();
            $this->_redirect('user/login');
            return;
        }
        if (!$auth->hasIdentity()) {
            if ($this->_request->getActionName() != 'login') {
                $this->_redirect('user/login');
            }
        } else {
            //学生チェックする
            $auth = Zend_Auth::getInstance();
            $infoUser = $auth->getStorage()->read();
            if ($infoUser['role'] != 2) {
                $auth->clearIdentity();
                $this->_redirect('user/login');
            }
        }
    }

    /**
     * If loged in, get user information
     */
    public function initial() {
        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        $this->view->user_info = $infoUser;
        $this->currentTeacherId = $infoUser['id'];
        $baseurl = $this->_request->getbaseurl();
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/admin-common-style.css");
    }

    /**
     * 先生の授業リスト処理
     *
     */
    public function indexAction() {
        $this->initial();
        $lessons = new Default_Model_Lesson();
        $tagId = $this->_request->getParam('tag_id');
        $tags = new Default_Model_Tag();

        $this->view->tagId = $tagId;
        $this->view->tags = $tags->listAll();
        $this->view->type = 1;

        $paginator = Zend_Paginator::factory($lessons->listTeacherLessonsByTag($this->currentTeacherId, $tagId));
        $paginator->setItemCountPerPage(6);
        $paginator->setPageRange(3);
        $this->view->numpage = $paginator->count();
        $currentPage = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage);
        $this->view->data = $paginator;
        //        foreach($this->view->data as $item) {
        //            var_dump($item);
        //        }
        //        die();
        if ($this->_request->isPost()) {
            //            $keyword = $this->_request->getParam('keyword');
            //            $paginator = Zend_Paginator::factory($lessons->findByKeyword($keyword));
            //            $paginator->setItemCountPerPage(6);
            //            $paginator->setPageRange(3);
            //            $this->view->numpage = $paginator->count();
            //            $currentPage = $this->_request->getParam('page', 1);
            //            $paginator->setCurrentPageNumber($currentPage);
            //            $this->view->data = $paginator;
        }
    }

    public function profileAction() {
        $this->initial();
    }

    /**
     * プロファイが変更機能アクション
     * @return type
     */
    public function profilechangeinfoAction() {
        $this->initial();
        $user = new Default_Model_Account();
        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();
            //Check empty
            if (trim($data['username']) == '') {
                return;
            }

            if (trim($data['fullname']) == '') {
                $this->view->errorMessage = Message::$M009;
                return;
            }

            if (trim($data['address']) == '') {
                $this->view->errorMessage = Message::$M011;
                return;
            }

            if (trim($data['phone']) == '') {
                $this->view->errorMessage = Message::$M012;
                return;
            }

            if (trim($data['bank_acc']) == '') {
                $this->view->errorMessage = Message::$M013;
                return;
            }

            $user->updateNewInfo($data);
            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($user->getUserInfo($data['username']));
            $this->view->user_info = $auth->getStorage()->read();
            $this->_redirect('student/profile');
        }
    }

    /**
     * パスワードが変更機能アクション
     */
    public function changepasswordAction() {
        $this->initial();
        $user = new Default_Model_Account();
        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();

            if (trim($data['password']) == '') {
                $this->view->errorMessage = Message::$M007;
                return;
            }

            if (trim($data['new_password'] == '')) {
                $this->view->errorMessage = Message::$M007;
                return;
            }
            if (!$user->isValid($data['username'], $data['password'], '1')) {
                $this->view->errorMessage = Message::$M0031;
                return;
            }

            if (trim($data['new_password']) != trim($data['new_password_confirm'])) {
                $this->view->errorMessage = Message::$M008;
                return;
            }

            if (!preg_match(Code::$REGEX_PASSWORD, $data['password'])) {
                $this->view->errorMessage = Message::$M007;
                return;
            }

            $user->updatePassword($data);
            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($user->getUserInfo($data['username']));
            $this->view->user_info = $auth->getStorage()->read();
            $this->_redirect('student/profile');
        }
    }

    /**
     * 秘密質問が変更機能アクション
     */
    public function changesecretqaAction() {
        $this->initial();
        $user = new Default_Model_Account();
        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();
            if (trim($data['secret_question'] == '')) {
                $this->view->errorMessage = Message::$M014;
                return;
            }
            if (trim($data['secret_answer'] == '')) {
                $this->view->errorMessage = Message::$M015;
                return;
            }
            $user->updateSecretQA($data);
            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($user->getUserInfo($data['username']));
            $this->view->user_info = $auth->getStorage()->read();
            $this->_redirect('student/profile');
        }
    }

    /**
     * 授業作成画面
     * @param type $name Description
     * @return type
     */
    public function createLessonAction() {
        $this->initial();
        //        return;
        //        $form = new Default_Form_CreateLesson();
        //        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $param = $this->_getAllParams();
            //var_dump($param);
            // Check title
            if ((!isset($param['title'])) || $param['title'] == '') {
                $this->view->errorMessage = Message::$M2064;
                return;
            }

            // Check description
            if ((!isset($param['description'])) || $param['description'] == '') {
                $this->view->errorMessage = Message::$M2065;
                return;
            }

            // Check copyright
            if (!isset($param['copyright_check'])) {
                $this->view->errorMessage = Message::$M2061;
                return;
            }
            
            // Save file
            $fileModel = new Default_Model_File();
            if (!$fileModel->exercuteFiles($param["file_dec"])) {
                $this->view->errorMessage = Message::$M2062;
                return;
            }

            // Check tag
            if (!isset($param['tags'])) {
                $this->view->errorMessage = Message::$M2066;
                return;
            }

            // Save lesson
            $lessonModel = new Default_Model_TeacherLesson();
            $title = $param['title'];
            $description = $param['description'];
            $lessonId = $lessonModel->createLesson($this->currentTeacherId, $title, $description, $param['tags']);

            // Save files
            $fileModel->createFilesData($lessonId);

            // Redirect
            $this->redirect("teacher/lesson?lesson_id=" . $lessonId);
        }
    }

    /**
    * 授業を見る画面
    * @param type $name Description
    */
   public function lessonAction() {
           $this->initial();
           $lessonId = $this->_request->getParam('lesson_id');
           $lessonModel = new Default_Model_Lesson();
           $fileModel = new Default_Model_File();
           $lessonTagModel = new Default_Model_LessonTag();
           $commentModel = new Default_Model_Comment();
           $learnModel = new Default_Model_Learn();

           $comment = $this->getParam('comment');
           if (isset($comment) && $comment != '') {
                   $commentModel->addComment($lessonId, $this->currentTeacherId, $comment);
           }

           $lesson = $lessonModel->findLessonById($lessonId);
           $files = $fileModel->getFileByLesson($lessonId);
           $tags = $lessonTagModel->getTagsByLesson($lessonId);
           $comments = $commentModel->getAllCommentOfLesson($lessonId);
           $studentsNum = $learnModel->countStudenJoinLesson($lessonId);
           $lesson['students_num'] = $studentsNum;
           $this->view->lessonId = $lessonId;
           $this->view->lessonInfo = $lesson;
           $this->view->files = $files;
           $this->view->tags = $tags;
           $this->view->comments = $comments;
           $this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('addFileFailed');
           $this->view->messages = $this->_helper->FlashMessenger->getMessages('addFileSuccess');
   }

   /**
    * ファイル追加処理
    * 
    */
   public function addFileAction() {
           $params = $this->getAllParams();
           $lessonId = $params['lesson_id'];
           $descriptions = $params['description'];
           $copyright_check = isset($params['copyright_check']);

           $fileModel = new Default_Model_File();

           if (!$copyright_check) {
               $this->_helper->FlashMessenger->addMessage(Message::$M2061, 'addFileFailed');
               $this->redirect('teacher/lesson?lesson_id=' . $lessonId);
               return;
           }

           if (!$fileModel->exercuteFiles($descriptions)) {
               // Send flash error message
               $this->_helper->FlashMessenger->addMessage(Message::$M2062, 'addFileFailed');
               $this->redirect('teacher/lesson?lesson_id=' . $lessonId);
               return;
           }

           // Save files
           $fileModel->createFilesData($lessonId);
           $this->_helper->FlashMessenger->addMessage(Message::$M2063, 'addFileSuccess');
           $this->redirect('teacher/lesson?lesson_id=' . $lessonId);
   }

    /**
     * 授業を削除処理
     *
     */
    public function deleteLessonAction() {
        $this->initial();
        $lessonId = $this->_request->getParam('lesson_id');
        $lessonModel = new Default_Model_Lesson();
        $teacherId = $this->currentTeacherId;

        if ($lessonModel->isLessonOwner($teacherId, $lessonId)) {
            //　削除する
            $lessonModel->delete("id=" . $lessonId);
        }

        $this->redirect("teacher");
    }

    /**
     * ファイルを削除処理
     */
    public function deleteFileAction() {
        $this->initial();
        $fileId = $this->_request->getParam('file_id');
        $lessonModel = new Default_Model_Lesson();
        $fileModel = new Default_Model_File();
        $teacherId = $this->currentTeacherId;

        $file = $fileModel->findFileById($fileId);
        $lessonId = $file['lesson_id'];

        if ($lessonModel->isLessonOwner($teacherId, $lessonId)) {
            //　削除する
            $fileModel->delete("id=" . $fileId);
        }

        $this->redirect("teacher/lesson?lesson_id=" . $lessonId);
    }

    /**
     * ファイルを見る画面
     * @param type $name Description
     */
    public function fileAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $lessonFileModel = new Default_Model_LessonFile();
        $filecommentModel = new Default_Model_FileComment();
        $reportModel = new Default_Model_CopyrightReport();

        if ($this->_request->isGet()) {
            $lessonId = $this->_request->getParam('lesson_id');
            $currentFileId = $this->_request->getParam('file_id');
        }
        if ($this->_request->isPost()) {
            $u = Zend_Auth::getInstance()->getStorage()->read();
            $lessonId = $this->_request->getParam('lesson_id');
            $currentFileId = $this->_request->getParam('file_id');
// 			$report = $this->_request->getParam('report_content');
// 			if ($report != NULL) {
// 				$repordModel->addReport($this->currentTeacherId, $currentFileId, $report);
// 				$this->view->reportNotify = Message::$M047;
// 			}
            $comment = $this->_request->getParam('comment');
            if ($comment != NULL) {

                $filecommentModel->addComment($currentFileId, $u['id'], $comment);
            }
        }
        $this->view->reports = $reportModel->getReport($currentFileId);
        $this->view->comments = $filecommentModel->getAllCommentOfFile($currentFileId);
        $currentFile = $lessonFileModel->findFileById($currentFileId);
        $lessonInfo = $lessonModel->findLessonById($lessonId);
        $this->view->lessonInfo = $lessonInfo;
        $files = $lessonFileModel->listFileOfLesson($lessonId);
        $this->view->files = $files;
        if ($currentFile == NULL) {
            $this->view->currentFile = $files[0];
        } else {
            $this->view->currentFile = $currentFile;
        }

        $this->view->fileModel = new Default_Model_File();
        $this->view->controller = $this;
        $this->view->lessonId = $lessonId;
    }

    public function getTestHtml($testId) {
        $fileModel = new Default_Model_File();
        return $fileModel->getTestHtml($testId);
    }

    /**
     * 授業を更新する画面
     * @param type $name Description
     */
    public function updateLessonAction() {
        
    }

    /**
     * 授業の学生を見る画面
     * @param type $name Description
     */
    public function studentsAction() {
        $this->initial();
        $lessonId = $this->_request->getParam('lesson_id');
        $modelLearn = new Default_Model_Learn();
        $students = $modelLearn->getStudentsByLessonId($lessonId);
        //Zend_Debug::dump($students);
        $this->view->students = $students;
        $this->view->lessonId = $lessonId;
    }

    /**
     * 授業の学生をロック
     * @param type $name Description
     */
    public function lockStudentAction() {
        $this->initial();
        $modelLearn = new Default_Model_Learn();
        $lessonId = $this->_request->getParam('lesson_id');
        $id = $this->_request->getParam("id");
        $modelLearn->lockStudent($id);
        $this->_redirect("teacher/students?lesson_id=$lessonId");
    }

	/**
	 * 授業の学生を見る画面
	 * @param type $name Description
	 */
	public function studentsAction() {
		$this->initial();
		$lessonId = $this->_request->getParam('lesson_id');
		$modelLearn = new Default_Model_Learn();
		$students = $modelLearn->getStudentsByLessonId($lessonId);
		//Zend_Debug::dump($students);
		$this->view->students =$students;
		$this->view->lessonId = $lessonId;
	}
	/**
	 * 授業の学生をロック
	 * @param type $name Description
	 */
	public function lockStudentAction(){
		$this->initial();
		$modelLearn = new Default_Model_Learn();
		$lessonId = $this->_request->getParam('lesson_id');
		$id = $this->_request->getParam("id");
		$modelLearn->lockStudent($id);
		$this->_redirect("teacher/students?lesson_id=$lessonId");
	}
	/**
	 * 授業の学生をロック
	 * @param type $name Description
	 */
	public function unlockStudentAction(){
		$this->initial();
		$modelLearn = new Default_Model_Learn();
		$lessonId = $this->_request->getParam('lesson_id');
		$id = $this->_request->getParam("id");
		$modelLearn->unlockStudent($id);
		$this->_redirect("teacher/students?lesson_id=$lessonId");
	}
	/**
	 * 学生のテスト結果を見る画面
	 * @param type $name Description
	 */
	public function studentResultAction() {
		$this->initial();
		$modelLearn = new Default_Model_Learn();
		//$lessonId = $this->_request->getParam('lesson_id');
		$learn_id = $this->_request->getParam("learn_id");
		$modelTestResult = new Default_Model_Result();
		$result = $modelTestResult->getTestResultByLearn($learn_id);
		//::dump($result);
		$this->view->results = $result;
	}

    /**
     * 課金情報処理
     *
     *
     */
    public function paymentAction() {
        $this->initial();
        $param = $this->getAllParams();
        if (isset($param["month"]) && isset($param["year"])) {
            $month = $param["month"];
            $year = $param["year"];
        } else {
            $month = date("m", time());
            $year = date("Y", time());
        }
        if ((int) $month < 10)
            $month = "0" . (int) $month;
        if ((int) $month == 12) {
            $nextMonth = "01";
            $nextYear = (int) $year + 1;
            $preMonth = "11";
            $preYear = (int) $year;
        } else if ((int) $month == 1) {
            $nextMonth = "02";
            $nextYear = (int) $year;
            $preMonth = "12";
            $preYear = (int) $year - 1;
        } else {
            $nextMonth = (int) $month + 1;
            $nextYear = (int) $year;
            $preMonth = (int) $month - 1;
            $preYear = (int) $year;
        }
        $this->view->nextMonth = $nextMonth;
        $this->view->nextYear = $nextYear;
        $this->view->preMonth = $preMonth;
        $this->view->preYear = $preYear;
        $this->view->month = $month;
        $this->view->year = $year;
        $learnModel = new Default_Model_Learn();
        $paymentInfos = $learnModel->getTeacherTotalPaymentInfo($this->currentTeacherId, $year, $month);
        //Zend_Debug::dump($paymentInfos);
        $this->view->paymentInfos = $paymentInfos;
        $modelMaster = new Admin_Model_Master();
        $master = $modelMaster->getMasterData();
        $this->view->price = $master[Admin_Model_Master::$KEY_COMA_PRICE];
        $this->view->rate = $master[Admin_Model_Master::$KEY_TEACHER_FEE_RATE];
        $totalPay = 0;
        foreach ($paymentInfos as $i) {
            $totalPay += $i["total"] * $this->view->price * $this->view->rate / 100;
        }
        $this->view->totalPayment = $totalPay . "(VND)";
    }
    
}
