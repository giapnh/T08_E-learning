<?php

require_once 'IController.php';

//require 'RegisterController.php';
class TeacherController extends IController {

    protected $user;
    protected $currentTeacherId;

    protected $ITEMS_PER_PAGE = 6;
    
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        $master = new Default_Model_Master();

        if (!$_SESSION) {
            session_start();
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
                //$auth->clearIdentity();
                $this->_redirect('user/login');
            }else{
            	if (!isset($_SESSION['CREATED'])) {
            		$_SESSION['CREATED'] = time();
            	} else if (time() - $_SESSION['CREATED'] > $master->getMasterValue(Default_Model_Master::$KEY_SESSION_TIME)) {
            		// １時間後自動にログアウトしています。
            		session_regenerate_id(true);
            		//thiennx : cho nay Giap lam kieu gi day ko ro, phai unset session di chu
            		//$_SESSION['CREATED'] = time();
            		unset($_SESSION["CREATED"]);
            		$auth->clearIdentity();
            		$this->_redirect('user/login');
            		return;
            	}
            	$this->view->user_info = $infoUser;
            }
        }
    }

    /**
     * If loged in, get user information
     */
    public function initial() {
        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        $this->user = $infoUser;
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
        $get_type = $this->_request->getParam('type');
        $tagId = $this->_request->getParam('tagId');
        $teacherId = $this->_request->getParam('teacherId');
        $type = $this->_request->getParam('sort_type');
        $asc = $this->_request->getParam('sort_asc');
        $sa = $this->_request->getParam('sa');
        $filterAsc = $this->_request->getParam('filter_asc');
        
        if (!isset($type)) {
            $type = 0;
        }
        if (!isset($asc)) {
            $asc = 0;
        }
        if (!isset($filterAsc)) {
            $filterAsc = 0;
        }
        
        if ($get_type == null || $get_type == 1|| $tagId) {
            $tags = new Default_Model_Tag();
            $this->view->tags = $tags->listAll($filterAsc);
            $this->view->type = 1;
            $paginator = Zend_Paginator::factory($lessons->listWithTag($tagId, $type, $asc));
        } else {
            $users = new Default_Model_Account();
            $this->view->teachers = $users->listTeacher($filterAsc);
            $this->view->type = 2;
            $paginator = Zend_Paginator::factory($lessons->listWithTeacher($teacherId, $type, $asc));
        }
        if (isset($sa)) {
            $keyword = $this->_request->getParam('keyword');
            $paginator = Zend_Paginator::factory($lessons->findByKeyword($keyword, $type, $asc));
        }
        
        $paginator->setItemCountPerPage($this->ITEMS_PER_PAGE);
        $paginator->setPageRange(3);
        $this->view->numpage = $paginator->count();
        $currentPage = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage);
        $this->view->data = $paginator;
        $this->view->sortType = $type;
        $this->view->asc = $asc;
        $this->view->tagId = $tagId;
        $this->view->teacherId = $teacherId;
        $this->view->params = $this->_request->getParams();
    }

    public function mylessonAction() {
        $this->initial();
        $auth = Zend_Auth::getInstance();
        $uInfo = $auth->getStorage()->read();
        $lessons = new Default_Model_Lesson();
        $tags = new Default_Model_Tag();
        
        $get_type = $this->_request->getParam('type');
        $tagId = $this->_request->getParam('tag_id');
        $sa = $this->_request->getParam('sa');
        $type = $this->_request->getParam('sort_type');
        $asc = $this->_request->getParam('sort_asc');
        if (!isset($type)) {
            $type = 0;
        }
        if (!isset($asc)) {
            $asc = 0;
        }
        $filterAsc = $this->_request->getParam('filter_asc');
        if (!isset($filterAsc)) {
            $filterAsc = 0;
        }
        $this->view->tags = $tags->listAllOfTeacher($uInfo['id'], $filterAsc);
        
        if ($tagId) {
            $paginator = Zend_Paginator::factory($lessons->listWithTagByTeacher($tagId, $uInfo['id'], $type, $asc));
        } else {
            $paginator = Zend_Paginator::factory($lessons->listWithTeacher($uInfo['id'], $type, $asc));
        }

        if (isset($sa)) {
            $keyword = $this->_request->getParam('keyword');
            $paginator = Zend_Paginator::factory($lessons->findByKeyword($keyword, $type, $asc,$uInfo["id"]));
        }
        
        $paginator->setItemCountPerPage($this->ITEMS_PER_PAGE);
        $paginator->setPageRange(3);
        $this->view->numpage = $paginator->count();
        $currentPage = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage);
        $this->view->tagId = $tagId;
        $this->view->data = $paginator;
        $this->view->sortType = $type;
        $this->view->asc = $asc;
        $this->view->params = $this->_request->getParams();
    }

    public function profileAction() {
        $this->initial();
    }

    //thiennx delete acount
    public function deleteAcountAction() {
        $this->initial();
        if ($this->currentTeacherId) {
            $modelUser = new Default_Model_Account();
            $modelUser->deleteTeacher($this->currentTeacherId);
            $auth = Zend_Auth::getInstance();
            $auth->clearIdentity();
        }
        $this->_redirect("user/login");
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
            $this->_redirect('teacher/profile');
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
            } else {
                if (!preg_match(Code::$REGEX_PASSWORD, $data['password'])) {
                    $this->view->errorMessage = Message::$M007;
                    return;
                }
            }

            if (trim($data['new_password'] == '')) {
                $this->view->errorMessage = Message::$M007;
                return;
            } else {
                if (!preg_match(Code::$REGEX_PASSWORD, $data['new_password'])) {
                    $this->view->errorMessage = Message::$M007;
                    return;
                }
            }

            if (trim($data['new_password_confirm']) == '') {
                $this->view->errorMessage = Message::$M007;
                return;
            }

            if (!$user->isValid($data['username'], $data['password'], '2')) {
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
            $this->_redirect('teacher/profile');
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
            $this->_redirect('teacher/profile');
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
                $this->view->errorMessage = Message::$M2062."<br>".$fileModel->getLastError();
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
            $this->_redirect("teacher/lesson?lesson_id=" . $lessonId);
        }
    }

    /**
     * 授業を見る画面
     * @param type $name Description
     */
    public function lessonAction() {
        $this->initial();
        
        //
        $lessonId = $this->_request->getParam('lesson_id');
        
        //
        $lessonModel = new Default_Model_Lesson();
        $fileModel = new Default_Model_File();
        $lessonTagModel = new Default_Model_LessonTag();
        $commentModel = new Default_Model_Comment();
        $learnModel = new Default_Model_Learn();
        $reportModel = new Default_Model_CopyrightReport();
        $lessonReportModel = new Default_Model_LessonReport();

        //
        $lesson = $lessonModel->findLessonById($lessonId);
        $files = $fileModel->getFileByLesson($lessonId);
        $tags = $lessonTagModel->getTagsByLesson($lessonId);
        $comments = $commentModel->getAllCommentOfLesson($lessonId);
        $studentsNum = $learnModel->countStudenJoinLesson($lessonId);
        $adminReports = $lessonReportModel->getAdminReports($lessonId);
        if ($lesson) {
            $lesson['students_num'] = $studentsNum;
            $lesson['reports_num'] = count($lessonReportModel->getReports($lesson['id']));
            if ($lesson['teacher_id'] != $this->user['id']) {
                $lesson['is_mine'] = false;
            } else {
                $lesson['is_mine'] = true;
            }
        }
        
        $this->view->lessonId = $lessonId;
        $this->view->lessonInfo = $lesson;
        $this->view->files = $files;
        $this->view->tags = $tags;
        $this->view->comments = $comments;
        $this->view->adminReports = $adminReports;
        $this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('addFileFailed');
        $this->view->messages = $this->_helper->FlashMessenger->getMessages('addFileSuccess');
//        $this->view->reports = $reportModel->getReportLesson($lessonId);
//        $this->view->reports = null;
    }

    public function lessondetailAction() {
        $this->initial();
        $lesson_id = $this->_request->getParam('lesson_id');
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        $commentModel = new Default_Model_Comment();
        $lfileModel = new Default_Model_LessonFile();
        $master = new Default_Model_Master();
        $lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
            $lessonModel->incrementView($lesson_id);
            $info = $lessonModel->findLessonById($lesson_id);
            $this->view->numComment = $commentModel->countCommentOnLesson($lesson_id);
            $this->view->comments = $commentModel->getAllCommentOfLesson($lesson_id);
            // Number of student join to this course
            $tagInfo = $tagModel->getAllTagOfLesson($lesson_id);
            $this->view->lessonInfo = $info;
            $this->view->tagsInfo = $tagInfo;
            $num = $learnModel->countStudenJoinLesson($lesson_id);
            $this->view->numStudent = $num[0];
            $this->view->filesInfo = $lfileModel->listFileOfLesson($lesson_id);
        if ($this->_request->isPost()) {
        	$u = Zend_Auth::getInstance()->getStorage()->read();
        	$report = $this->_request->getParam('report_content');
        	$repordModel = new Default_Model_CopyrightReport();
        	if ($report != NULL) {
        		$repordModel->insert(array(
        				"user_id" => $u['id'], 
        				"lesson_id" => $lesson_id, 
        				"reason" => $report,
        				"status" => 1
        				
        		));
        		$this->view->reportNotify = Message::$M047;
        	}
        }
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
            $tsvFileError = $fileModel->getLastError();
            $this->_helper->FlashMessenger->addMessage(Message::$M2062."<br>".$tsvFileError, 'addFileFailed');
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
            $lessonModel->deleteLessonById($lessonId);
        }

        $this->_redirect("teacher");
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
            $fileModel->deleteFileById($fileId);
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
        $lessonFileModel = new Default_Model_File();
        $filecommentModel = new Default_Model_FileComment();
        $reportModel = new Default_Model_CopyrightReport();

        $lessonId = $this->_request->getParam('lesson_id');
        $currentFileId = $this->_request->getParam('file_id');

        if ($this->_request->isPost()) {
            $u = Zend_Auth::getInstance()->getStorage()->read();
            $lessonId = $this->_request->getParam('lesson_id');
            $currentFileId = $this->_request->getParam('file_id');
            $comment = $this->_request->getParam('comment');
            if ($comment != NULL) {
                $filecommentModel->addComment($currentFileId, $u['id'], $comment);
            }
        }

        $currentFile = $lessonFileModel->findFileById($currentFileId);
        $lessonInfo = $lessonModel->findLessonById($lessonId);

        $files = $lessonFileModel->getFileByLesson($lessonId);
        $this->view->files = $files;
        if ($currentFile == NULL) {
            if (count($files) > 0) {
                $currentFile = $files[0];
            } else {
                $this->view->fileError = "ファイルがない";
            }
        }

        // 授業はこのユーザの授業かをチェック
        if ($lessonInfo['teacher_id'] != $this->user['id']) {
            $this->_redirect('teacher/index');
        }

        // ファイルは授業のファイルかをチェック
        if ($currentFile != NULL) {
            if ($currentFile['lesson_id'] != $lessonInfo['id']) {
                $currentFile = NULL;
                $this->view->fileError = "ファイルが無効";
            }
        }

        $this->view->currentFile = $currentFile;
        $this->view->lessonInfo = $lessonInfo;
        $this->view->fileModel = new Default_Model_File();
        $this->view->controller = $this;
        $this->view->lessonId = $lessonId;
        $this->view->reports = $reportModel->getReport($currentFileId);
        $this->view->comments = $filecommentModel->getAllCommentOfFile($currentFileId);

        $uploadErrors = $this->_helper->FlashMessenger->getMessages('uploadError');
        if (count($uploadErrors) >= 1) {
            $this->view->fileUploadError = $uploadErrors[0];
        }
    }
    public function editFileAction() {
        $this->getHelper('ViewRenderer')
                ->setNoRender();

        $fileId = $this->_request->getParam('file_id');
        $fileDescription = $this->_request->getParam('description');
        $lessonId = $this->_request->getParam('lesson_id');
        $copyrightCheck = $this->_request->getParam('copyright_check');

        $fileModel = new Default_Model_File();

        /*
         *  チェック
         */
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $newFile = $adapter->getFileInfo();
        // ファイルがあるかどうか
        if ($newFile['file']['type'] == null) {
            $msg = Message::$M2084;
            $this->_helper->FlashMessenger->addMessage($msg, 'uploadError');
            $this->_redirect('teacher/file?lesson_id=' . $lessonId . "&file_id=" . $fileId);
        }
        // ファイルタイプチェック
        $currentFile = $fileModel->findFileById($fileId);
        $fileExt = $fileModel->getFileExt($currentFile['filename']);
        $newFileExt = $fileModel->getFileExt($newFile['file']['name']);
        if ($fileExt != $newFileExt) {
            $msg = str_replace("<filetype>", $fileExt, Message::$M2081);
            $this->_helper->FlashMessenger->addMessage($msg, 'uploadError');
            $this->_redirect('teacher/file?lesson_id=' . $lessonId . "&file_id=" . $fileId);
        }
        // ファイルサイズ
        if ($newFile['file']['size'] > Default_Model_File::$FILE_MAX_SIZE) {
            $msg = Message::$M2082;
            $this->_helper->FlashMessenger->addMessage($msg, 'uploadError');
            $this->_redirect('teacher/file?lesson_id=' . $lessonId . "&file_id=" . $fileId);
        }
        // Copyrightチェック
        if (!isset($copyrightCheck)) {
            $msg = Message::$M2083;
            $this->_helper->FlashMessenger->addMessage($msg, 'uploadError');
            $this->_redirect('teacher/file?lesson_id=' . $lessonId . "&file_id=" . $fileId);
        }

        // ファイル更新
        if (!$fileModel->editFile($fileId, $fileDescription)) {
            $msg = Message::$M2085."<br>".$fileModel->getLastError();
            $this->_helper->FlashMessenger->addMessage($msg, 'uploadError');
        }
        $this->_redirect('teacher/file?lesson_id=' . $lessonId . "&file_id=" . $fileId);
    }

    public function getTestHtml($testId) {
        $fileModel = new Default_Model_File();
        return $fileModel->getTestHtml($testId);
    }
    public function testResultAction(){
    	$this->initial();
    	$lessonId = $this->_request->getParam('lessonId');
    	$lessonModel = new Default_Model_Lesson();
    	$fileModel = new Default_Model_File();
    	$questionModel = new Default_Model_Question();
    	$resultModel = new Default_Model_Result();
    	
    	$fileId = $this->_request->getParam('file_id');
    	$file = $fileModel->findFileById($fileId);
    	
    	// Update result
    	$answers = $this->_request->getParam('Q');
    	$lessonFileModel = new Default_Model_LessonFile();
    	$questionModel = new Default_Model_Question();
    	
    	$lessonInfo = $lessonModel->findLessonById($lessonId);
    	
    	
    	$this->view->lessonInfo = $lessonInfo;
    	$files = $lessonFileModel->listFileOfLesson($lessonId);
    	$this->view->files = $files;
    	
    	$questions = $questionModel->findQuestionByFile($fileId);
    	$score = 0;
    	$total = 0;
    	foreach ($questions as $i => $question) {
    		$index = (int)substr($question["title"],1);
    		if (isset($answers[$index])) {
    			$questions[$i]["result"]["selected"] = "S".$answers[$index];
    			if ("S".$answers[$index] == $questions[$i]['answer']) {
    				$score += $questions[$i]['point'];
    				$questions[$i]['is_true'] = true;
    				
    			} else {
    				$questions[$i]['is_true'] = false;
    			}
    		}else{
    			$questions[$i]['is_true'] = false;
    			$questions[$i]["result"]["selected"] = "X";
    		}
    		$total += $questions[$i]['point'];
    	}
    	$this->view->score = $score;
    	$this->view->total = $total;
    	$this->view->questions = $questions;
    	 
    	 
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
//        Zend_Debug::dump($students);die();
        $this->view->students = $students;
        $this->view->lessonId = $lessonId;
    }

    /**
     * 授業の学生をロック
     * @param type $name Description
     */
    public function lockStudentLearnAction(){
    	$this->initial();
    	$modelUser = new Default_Model_Account();
    	$lessonId = $this->_request->getParam("lesson_id");
    	$modelLesson = new Default_Model_Lesson();
    	if(!$lessonId || ! $modelLesson->findLessonById($lessonId))
    		$this->redirect("teacher/myLesson");
    	$search = $this->_request->getParam("keyword");
    	if(!$modelLesson->isLessonOwner($this->currentTeacherId, $lessonId))
    		$this->redirect("teacher/myLesson");
    	$students = $modelUser->listStudentForLock($lessonId, $search);
    	$this->view->students = $students;
    		
    	$this->view->lessonId = $lessonId;
    }
    public function lockStudentLearnLessonAction() {
    	$this->initial();
    	$modelLock = new Default_Model_Lock();
    	$lessonId = $this->_request->getParam('lesson_id');
    	$studentId= $this->_request->getParam("id");
    	$lock = $modelLock->getByStudentAndLesson($studentId, $lessonId);
    	if($lock){
    		$modelLock->update(array("status" => "1"), "id=". $lock["id"]);
    	}
    	else{
    		$modelLock->insert(array("student_id" => $studentId, "lesson_id" => $lessonId,"status" => 1));
    	}
    	$this->_redirect("teacher/lock-student-learn?lesson_id=$lessonId");
    }
    
    /**
     * 授業の学生をロック
     * @param type $name Description
     */
    public function unlockStudentLearnLessonAction() {
    	$this->initial();
    	$modelLock = new Default_Model_Lock();
    	$lessonId = $this->_request->getParam('lesson_id');
    	$studentId= $this->_request->getParam("id");
    	$lock = $modelLock->getByStudentAndLesson($studentId, $lessonId);
    	if($lock){
    		$modelLock->update(array("status" => "0"), "id=". $lock["id"]);
    	}
    	$this->_redirect("teacher/lock-student-learn?lesson_id=$lessonId");
    	    }
    
    public function lockStudentAction() {
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
    public function unlockStudentAction() {
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

    public function streamAction() {
        set_time_limit(0);
        $fileId = $this->_request->getParam("id");
        $lessonFileModel = new Default_Model_File();
        $file = $lessonFileModel->findFileById($fileId);
        $path = $lessonFileModel->getFileFolder() . $file["location"];
        $currentFileExt = explode(".", $file['filename']);
        $currentFileExt = $currentFileExt[count($currentFileExt) - 1];
        $arrayType = array(
            "pdf" => "application/pdf",
            "mp3" => "audio/mpeg",
            "mp4" => "video/mp4",
            "MP4" => "video/mp4",
            "jpg" => "image/jpeg",
            "png" => "image/jpeg",
            "gif" => "image/jpeg",
            "wav" => "audio/mpeg"
        );
        if (is_readable($path)) {
            if ($currentFileExt == "pdf") {
                echo $link = $this->view->serverUrl() . $this->view->baseUrl() . "/public/viewpdf/web/viewer.html?file="
                . $this->view->serverUrl() . $this->view->baseUrl() . "/" . $lessonFileModel->getFileFolderName() . $file["location"];
                header("Location: " . $link);
            } else {
                header('Content-type: ' . $arrayType[$currentFileExt]);
                header("Content-Length: " . filesize($path));
                //readfile($path);
                $handle = fopen($path, "rb");
                echo stream_get_contents($handle);
                fclose($handle);
            }
        }
        exit();
    }

}
