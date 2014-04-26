<?php

require_once 'IController.php';

//require 'RegisterController.php';
class StudentController extends IController {

    protected $currentUser;
    
    protected $ITEMS_PER_PAGE = 6;

    /**
     * Check login or not yet
     */
    public function preDispatch() {
        $master = new Default_Model_Master();
        $auth = Zend_Auth::getInstance();
        if (!$_SESSION) {
            session_start();
        }
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } else if (time() - $_SESSION['CREATED'] > $master->getMasterValue(Default_Model_Master::$KEY_SESSION_TIME)) {
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
            $infoUser = $auth->getStorage()->read();
            if ($infoUser['role'] != 1) {
                //学生チェックする
                //$auth->clearIdentity();
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
        $this->user = $infoUser;
        $this->currentUser = $infoUser;
    }

    /**
     * 授業リスト処理
     */
    public function indexAction() {
        // Check login
        $this->initial();
        $lessons = new Default_Model_Lesson();
        $this->view->params = $this->_request->getParams();
        $get_type = $this->_request->getParam('type');
        $tagId = $this->_request->getParam('tagId');
        $teacherId = $this->_request->getParam('teacherId');
        $keyword = $this->_request->getParam('keyword');
        $sa = $this->_request->getParam('sa');
        $type = $this->_request->getParam('sort_type');
        $asc = $this->_request->getParam('sort_asc');
        if (!isset($type)) {
            $type = 0;
        }
        if (!isset($asc)) {
            $asc = 0;
        }
        
        $this->view->tagId = $tagId;
        $this->view->teacherId = $teacherId;
        if ($get_type == null || $get_type == 1) {
            $tags = new Default_Model_Tag();
            $this->view->tags = $tags->listAll();
            $this->view->type = 1;
            $paginator = Zend_Paginator::factory($lessons->listWithTag($tagId, $type, $asc));
        } else {
            $users = new Default_Model_Account();
            $this->view->teachers = $users->listTeacher();
            $this->view->type = 2;
            $paginator = Zend_Paginator::factory($lessons->listWithTeacher($teacherId, $type, $asc));
        }

        if (isset($sa)) {
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

    public function deleteAccountAction() {
        $this->initial();
        if ($this->user) {
            $modelUser = new Default_Model_Account();
            $modelUser->deleteStudent($this->user["id"]);
            $auth = Zend_Auth::getInstance();
            $auth->clearIdentity();
        }
        $this->_redirect("user/login");
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

    public function lessondetailAction() {
        $this->initial();
        $lesson_id = $this->_request->getParam('lessonId');
        $userId = $this->currentUser["id"];
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        $commentModel = new Default_Model_Comment();
        $lfileModel = new Default_Model_LessonFile();
        $master = new Default_Model_Master();
        $lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
        if (!$learnModel->isStudentLearn($userId, $lesson_id, $lessonDeadline))
            $this->_redirect("student/myLessonDetail?lessonId=" . $lesson_id);
        if ($this->_request->isGet()) {
        	$info = $lessonModel->findLessonById($lesson_id);
        	if(!$info)
        		$this->_redirect("student/index/");
            $lessonModel->incrementView($lesson_id);
            $this->view->numComment = $commentModel->countCommentOnLesson($lesson_id);
            $this->view->comments = $commentModel->getAllCommentOfLesson($lesson_id);
            // Number of student join to this course
            $tagInfo = $tagModel->getAllTagOfLesson($lesson_id);
            $this->view->lessonInfo = $info;
            $this->view->tagsInfo = $tagInfo;
            $num = $learnModel->countStudenJoinLesson($lesson_id);
            $this->view->numStudent = $num[0];
            $this->view->filesInfo = $lfileModel->listFileOfLesson($lesson_id);
        }
    }

    public function registerlessonAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        $lfileModel = new Default_Model_LessonFile();
        $master = new Default_Model_Master();
        $lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
        $this->view->lessonCost = $master->getMasterValue(Default_Model_Master::$KEY_COMA_PRICE);
        if ($this->_request->isGet()) {
            $do = $this->_request->getParam('do');
            $this->view->do = $do;
            $lesson_id = $this->_request->getParam('lessonId');
            if ($do == 'view') {
                $info = $lessonModel->findLessonById($lesson_id);
                // Number of student join to this course
                $tagInfo = $tagModel->getAllTagOfLesson($lesson_id);
                $this->view->lessonInfo = $info;
                $this->view->tagsInfo = $tagInfo;
                $num = $learnModel->countStudenJoinLesson($lesson_id);
                $this->view->numStudent = $num[0];
                $this->view->filesInfo = $lfileModel->listFileOfLesson($lesson_id);
            } else if ($do == 'submit') {
                $u = Zend_Auth::getInstance()->getStorage()->read();
                $isLearn = $learnModel->isStudentLearn($u['id'], $lesson_id, $lessonDeadline);
                $this->view->isLearn = $isLearn;
                if ($isLearn == 0) {
                    $this->view->notify = "前に、あなたはこの授業に登録した！";
                    $this->view->lessonId = $lesson_id;
                } else {
                    //Add to db
                    $learnModel->doRegisterLesson($u['id'], $lesson_id);
                    $this->view->notify = "おめでとう！授業登録が成功した！";
                    $this->view->lessonId = $lesson_id;
                }
            }
        }
    }

    public function mylessondetailAction() {
        $this->initial();
        $u = Zend_Auth::getInstance()->getStorage()->read();
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        $commentModel = new Default_Model_Comment();
        $lfileModel = new Default_Model_LessonFile();

//        $auth = Zend_Auth::getInstance();
//        $infoUser = $auth->getStorage()->read();
//        $studentId = $infoUser['id'];
//        $lessonId = $this->_request->getParam('lessonId');
//        if ($lessonId == null) {
//            $this->redirect('student/index');
//            return;
//        }
//        if ($learnModel->isStudentLearn($studentId, $lessonId) == 0) {
//            $this->redirect('student/index');
//            return;
//        }
        if ($this->_request->isGet()) {
            $lesson_id = $this->_request->getParam('lessonId');
        }
        if ($this->_request->isPost()) {
            $lesson_id = $this->_request->getParam('lessonId');
            $comment = $this->_request->getParam('comment');
            $commentModel->addComment($lesson_id, $u['id'], $comment);
        }

        //$lessonModel->incrementView($lesson_id);
        $info = $lessonModel->findLessonById($lesson_id);
        $this->view->numComment = $commentModel->countCommentOnLesson($lesson_id);
        $this->view->comments = $commentModel->getAllCommentOfLesson($lesson_id);
        // Number of student join to this course
        $tagInfo = $tagModel->getAllTagOfLesson($lesson_id);
        $this->view->lessonId = $lesson_id;
        $this->view->lessonInfo = $info;
        $this->view->tagsInfo = $tagInfo;
        $num = $learnModel->countStudenJoinLesson($lesson_id);
        $this->view->numStudent = $num[0];
        $this->view->filesInfo = $lfileModel->listFileOfLesson($lesson_id);
        $likeModel = new Default_Model_Like();
        $this->view->liked = $likeModel->liked($u["id"], $lesson_id);
    }

    public function likeAction() {
        $this->initial();
        $u = Zend_Auth::getInstance()->getStorage()->read();
        if ($this->_request->isPost()) {
            $lesson_id = $this->_request->getParam('lesson_id');
            $lessonModel = new Default_Model_Lesson();
            $lessonModel->getAdapter()->query("UPDATE  `elearning`.`lesson` SET  `num_like` =  `num_like` +1  WHERE  `lesson`.`id` = " . $lesson_id);
            $likeModel = new Default_Model_Like();
            $likeModel->insert(array("user_id" => $u["id"], "lesson_id" => $lesson_id));
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
        exit();
    }

    public function dislikeAction() {
        $this->initial();
        $u = Zend_Auth::getInstance()->getStorage()->read();
        if ($this->_request->isPost()) {
            $lesson_id = $this->_request->getParam('lesson_id');
            $lessonModel = new Default_Model_Lesson();
            $lessonModel->getAdapter()->query("UPDATE  `elearning`.`lesson` SET  `num_like` =  `num_like` -1  WHERE  `lesson`.`id` = " . $lesson_id);
            $likeModel = new Default_Model_Like();
            $likeModel->delete(array("user_id" => $u["id"], "lesson_id" => $lesson_id));
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
        exit();
    }

    public function mylessonAction() {
        $this->initial();
        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        $lessons = new Default_Model_Lesson();
        $get_type = $this->_request->getParam('type');
        $tagId = $this->_request->getParam('tagId');
        $teacherId = $this->_request->getParam('teacherId');
        $sa = $this->getParam('sa');
        $type = $this->_request->getParam('sort_type');
        $asc = $this->_request->getParam('sort_asc');
        
        if ($get_type == null || $get_type == 1) {
            $tags = new Default_Model_Tag();
            $this->view->tags = $tags->listAllTagByStudent($infoUser['id']);
            $this->view->type = 1;
            if ($tagId == 0) {
                $paginator = Zend_Paginator::factory($lessons->listAllByStudent($infoUser['id'], $type, $asc));
            } else {
                $paginator = Zend_Paginator::factory($lessons->findLessonWithTagByStudent($tagId, $infoUser['id'], $type, $asc));
            }
        } else {
            $account = new Default_Model_Account();
            $this->view->teachers = $account->listTeacherByStudent($infoUser['id']);
            $this->view->type = 2;
            $lessons = new Default_Model_Lesson();
            if ($teacherId == 0) {
                $paginator = Zend_Paginator::factory($lessons->listAllByStudent($infoUser['id'], $type, $asc));
            } else {
                $paginator = Zend_Paginator::factory($lessons->findLessonWithTeacherByStudent($teacherId, $infoUser['id'], $type, $asc));
            }
        }

        if (isset($sa)) {
            $keyword = $this->_request->getParam('keyword');
            $paginator = Zend_Paginator::factory($lessons->findByKeyword($keyword, $type, $asc, null, $infoUser['id']));
        }
        
        $paginator->setItemCountPerPage($this->ITEMS_PER_PAGE);
        $paginator->setPageRange(3);
        $this->view->numpage = $paginator->count();
        $currentPage = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage);
        $this->view->data = $paginator;
            
        $this->view->tagId = $tagId;
        $this->view->teacherId = $teacherId;
        $this->view->params = $this->_request->getParams();
    }

    /**
     * ファイル見る処理
     */
    public function fileAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $lessonFileModel = new Default_Model_File();
        $filecommentModel = new Default_Model_FileComment();
        $repordModel = new Default_Model_CopyrightReport();
        $learnModel = new Default_Model_Learn();
        $masterModel = new Default_Model_Master();

        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        $studentId = $infoUser['id'];
        $lessonId = $this->_request->getParam('lessonId');
        $currentFileId = $this->_request->getParam('fileId');
        if ($lessonId == null) {
            $this->redirect('student/index');
            return;
        }
        
        if ($learnModel->isStudentBeLocked($studentId, $lessonId) == 0) {
            $this->redirect('student/index');
            return;
        }
        
        //ロックチェックする
        // ファイル情報を取る
        $files = $lessonFileModel->getFileByLesson($lessonId);
        if (!$currentFileId) {
            if (count($files) > 0) {
                $currentFile = $files[0];
                $currentFileId = $currentFile["id"];
            } else {
                $currentFileId = -1; // なし
            }
        }
        $currentFile = $lessonFileModel->findFileById($currentFileId);

        if ($currentFile != null) {
            // ファイルがこの授業のかをチェック
            if ($currentFile['lesson_id'] != $lessonId) {
                $currentFile = null;
                $this->view->errorMsg = "ファイルが無効";
            }

            // ファイルが削除されたかをチェック
            if ($currentFile['status'] == 3) {
                $this->view->errorMsg = "ファイルが無効";
            }
        } else {
            $this->view->errorMsg = "ファイルがない";
        }

        // 授業情報を取る
        $lessonInfo = $lessonModel->findLessonById($lessonId);
        $studentsNum = $learnModel->countStudenJoinLesson($lessonId);
        $lessonInfo['students_num'] = $studentsNum;

        // 授業が見えるかをチェック
        $lessonDeadline = $masterModel->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
        $isLearn = $learnModel->isStudentLearn($this->currentUser['id'], $lessonId, $lessonDeadline);
        if ($isLearn == 1) {
            $this->redirect('student/lessonDetail?lessonId=' . $lessonId);
        }

        //
        $this->view->lessonInfo = $lessonInfo;
        $this->view->currentFile = $currentFile;
        $this->view->files = $files;
        $this->view->controller = $this;

        // Report
        if ($this->_request->isPost()) {
            $u = Zend_Auth::getInstance()->getStorage()->read();
            $report = $this->_request->getParam('report_content');
            if ($report != NULL) {
                $repordModel->addReport($u['id'], $currentFileId, $report);
                $this->view->reportNotify = Message::$M047;
            }
        }
        $this->view->comments = $filecommentModel->getAllCommentOfFile($currentFileId);
    }

    public function testResultAction() {
        $this->initial();

        $lessonId = $this->_request->getParam('lessonId');
        $fileId = $this->_request->getParam('file_id');
        $studentId = $this->currentUser['id'];

        $lessonModel = new Default_Model_Lesson();
        $lessonFileModel = new Default_Model_LessonFile();
        $questionModel = new Default_Model_Question();
        $resultModel = new Default_Model_Result();
        $learnModel = new Default_Model_Learn();

        $lessonInfo = $lessonModel->findLessonById($lessonId);

        $learn = $learnModel->findByLessonAndStudent($lessonId, $studentId);

        $this->view->lessonInfo = $lessonInfo;
        $files = $lessonFileModel->listFileOfLesson($lessonId);
        $this->view->files = $files;

        $questions = $questionModel->findQuestionByFile($fileId);
        $score = 0;
        $total = 0;
        foreach ($questions as $i => $question) {
            $questions[$i]['result'] = $resultModel->findResultByQuestionAndLearn($learn['id'], $question['id']);
            if ($questions[$i]['result']) {
                if ($questions[$i]['result']['selected'] == $questions[$i]['answer']) {
                    $score += $questions[$i]['point'];
                    $questions[$i]['is_true'] = true;
                } else {
                    $questions[$i]['is_true'] = false;
                }
            }
            $total += $questions[$i]['point'];
        }
        $this->view->score = $score;
        $this->view->total = $total;
        $this->view->questions = $questions;
//        var_dump($questions);
//        var_dump($score);
//        var_dump($total);
//        die();
    }

    public function updateResultAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $fileModel = new Default_Model_File();
        $learnModel = new Default_Model_Learn();
        $questionModel = new Default_Model_Question();
        $resultModel = new Default_Model_Result();

        $fileId = $this->_request->getParam('file_id');
        $studentId = $this->currentUser['id'];
        $file = $fileModel->findFileById($fileId);
        $lesson = $lessonModel->findLessonById($file['lesson_id']);
        $learn = $learnModel->findByLessonAndStudent($lesson['id'], $studentId);

        // Update result
        $questions = $questionModel->findQuestionByFile($fileId);
        $answers = $this->_request->getParam('Q');
        foreach ($questions as $i => $q) {
        	$index = (int)substr($q["title"], 1);
            $question = $q["title"];
            $selected = isset($answers[$index]) ? 'S' . $answers[$index] : "X" ;
            $question = $questionModel->findQuestionByTitleAndFile($question, $fileId);
            $resultModel->updateResult($learn['id'], $question['id'], $selected);
        }

        $this->redirect('student/test-result?file_id=' . $fileId . "&lessonId=" . $lesson['id']);
    }

    public function getTestHtml($testId) {
        $fileModel = new Default_Model_File();
        return $fileModel->getTestHtml($testId);
    }

    public function paymentAction() {
        $this->initial();
        //TODO
        $learnModel = new Default_Model_Learn();
        $paymentInfos = $learnModel->getStudentTotalPaymentInfo($this->user["id"]);
        $this->view->paymentInfos = $paymentInfos;
        $modelMaster = new Admin_Model_Master();
        $master = $modelMaster->getMasterData();
        $this->view->price = $master[Admin_Model_Master::$KEY_COMA_PRICE];
    }

    public function streamAction() {
        set_time_limit(0);
        $this->initial();
        $fileId = $this->_request->getParam("id");
        $lessonFileModel = new Default_Model_File();
        if ($lessonFileModel->checkUserCanSeeFile($this->currentUser["id"], $fileId)) {
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
            //echo $path;
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
        }
        exit();
    }

}
