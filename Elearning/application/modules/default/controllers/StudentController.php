<?php

require_once 'IController.php';

//require 'RegisterController.php';
class StudentController extends IController {

    /**
     * Check login or not yet
     */
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_request->getActionName() != 'login') {
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
    }

    public function indexAction() {
        // Check login
        $this->initial();
        if ($this->_request->isGet()) {
            $get_type = $this->_request->getParam('type');
            $tagId = $this->_request->getParam('tagId');
            $teacherId = $this->_request->getParam('teacherId');
            $this->view->tagId = $tagId;
            $this->view->teacherId = $teacherId;
            if ($get_type == null || $get_type == 1) {
                $tags = new Default_Model_Tag();
                $this->view->tags = $tags->listAll();
                $this->view->type = 1;
                $lessons = new Default_Model_Lesson();
                $paginator = Zend_Paginator::factory($lessons->listWithTag($tagId));
                $paginator->setItemCountPerPage(6);
                $paginator->setPageRange(3);
                $this->view->numpage = $paginator->count();
                $currentPage = $this->_request->getParam('page', 1);
                $paginator->setCurrentPageNumber($currentPage);
                $this->view->data = $paginator;
            } else {
                $users = new Default_Model_Account();
                $this->view->teachers = $users->listTeacher();
                $this->view->type = 2;
                $lessons = new Default_Model_Lesson();
                $paginator = Zend_Paginator::factory($lessons->listWithTeacher($teacherId));
                $paginator->setItemCountPerPage(6);
                $paginator->setPageRange(3);
                $this->view->numpage = $paginator->count();
                $currentPage = $this->_request->getParam('page', 1);
                $paginator->setCurrentPageNumber($currentPage);
                $this->view->data = $paginator;
            }
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

    public function lessondetailAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        $commentModel = new Default_Model_Comment();
        $lfileModel = new Default_Model_LessonFile();
        if ($this->_request->isGet()) {
            $lesson_id = $this->_request->getParam('lessonId');
            $lessonModel->incrementView($lesson_id);
            $info = $lessonModel->findLessonById($lesson_id);
            $this->view->numComment = $commentModel->countCommentOnLesson($lesson_id);
            $this->view->comments = $commentModel->getAllCommentOfLesson($lesson_id);
            // Number of student join to this course
            $tagInfo = $tagModel->getAllTagOfLesson($lesson_id);
            $this->view->lessonInfo = $info;
            $this->view->tagsInfo = $tagInfo;
            $this->view->numStudent = $learnModel->countStudenJoinLesson($lesson_id)[0];
            $this->view->filesInfo = $lfileModel->listFileOfLesson($lesson_id);
        }
    }

    public function registerlessonAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        $lfileModel = new Default_Model_LessonFile();
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
                $this->view->numStudent = $learnModel->countStudenJoinLesson($lesson_id)[0];
                $this->view->filesInfo = $lfileModel->listFileOfLesson($lesson_id);
            } else if ($do == 'submit') {
                $isLearn = $learnModel->isStudentLearn(Zend_Auth::getInstance()->getStorage()->read()['id'], $lesson_id);
                $this->view->isLearn = $isLearn;
                if ($isLearn == 0) {
                    $this->view->notify = "前に、あなたはこの授業に登録した！";
                    $this->view->lessonId = $lesson_id;
                } else {
                    //Add to db
                    $learnModel->doRegisterLesson(Zend_Auth::getInstance()->getStorage()->read()['id'], $lesson_id);
                    $this->view->notify = "おめでとう！授業登録が成功した！";
                    $this->view->lessonId = $lesson_id;
                }
            }
        }
    }

    public function mylessondetailAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        $commentModel = new Default_Model_Comment();
        $lfileModel = new Default_Model_LessonFile();
        if ($this->_request->isGet()) {
            $lesson_id = $this->_request->getParam('lessonId');
        }
        if ($this->_request->isPost()) {
            $lesson_id = $this->_request->getParam('lessonId');
            $comment = $this->_request->getParam('comment');
            $commentModel->addComment($lesson_id, Zend_Auth::getInstance()->getStorage()->read()['id'], $comment);
        }
        $lessonModel->incrementView($lesson_id);
        $info = $lessonModel->findLessonById($lesson_id);
        $this->view->numComment = $commentModel->countCommentOnLesson($lesson_id);
        $this->view->comments = $commentModel->getAllCommentOfLesson($lesson_id);
        // Number of student join to this course
        $tagInfo = $tagModel->getAllTagOfLesson($lesson_id);
        $this->view->lessonInfo = $info;
        $this->view->tagsInfo = $tagInfo;
        $this->view->numStudent = $learnModel->countStudenJoinLesson($lesson_id)[0];
        $this->view->filesInfo = $lfileModel->listFileOfLesson($lesson_id);
    }

    public function mylessonAction() {
        $this->initial();
        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        if ($this->_request->isGet()) {
            $get_type = $this->_request->getParam('type');
            $tagId = $this->_request->getParam('tagId');
            $teacherId = $this->_request->getParam('teacherId');
            $this->view->tagId = $tagId;
            $this->view->teacherId = $teacherId;
            if ($get_type == null || $get_type == 1) {
                $tags = new Default_Model_Tag();
                $this->view->tags = $tags->listAllTagByStudent($infoUser['id']);
                $this->view->type = 1;
                $lessons = new Default_Model_Lesson();
                if ($tagId == 0) {
                    $paginator = Zend_Paginator::factory($lessons->listAllByStudent($infoUser['id']));
                } else {
                    $paginator = Zend_Paginator::factory($lessons->findLessonWithTagByStudent($tagId, $infoUser['id']));
                }
                $paginator->setItemCountPerPage(6);
                $paginator->setPageRange(3);
                $this->view->numpage = $paginator->count();
                $currentPage = $this->_request->getParam('page', 1);
                $paginator->setCurrentPageNumber($currentPage);
                $this->view->data = $paginator;
            } else {
                $account = new Default_Model_Account();
                $this->view->teachers = $account->listTeacherByStudent($infoUser['id']);
                $this->view->type = 2;
                $lessons = new Default_Model_Lesson();
                if ($teacherId == 0) {
                    $paginator = Zend_Paginator::factory($lessons->listAllByStudent($infoUser['id']));
                } else {
                    $paginator = Zend_Paginator::factory($lessons->findLessonWithTeacherByStudent($teacherId, $infoUser['id']));
                }
                $paginator->setItemCountPerPage(6);
                $paginator->setPageRange(3);
                $this->view->numpage = $paginator->count();
                $currentPage = $this->_request->getParam('page', 1);
                $paginator->setCurrentPageNumber($currentPage);
                $this->view->data = $paginator;
            }
        }
    }

    public function fileAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $lessonFileModel = new Default_Model_LessonFile();
        $filecommentModel = new Default_Model_FileComment();
        $repordModel = new Default_Model_CopyrightReport();
        if ($this->_request->isGet()) {
            $lessonId = $this->_request->getParam('lessonId');
            $currentFileId = $this->_request->getParam('fileId');
        }
        if ($this->_request->isPost()) {
            $lessonId = $this->_request->getParam('lessonId');
            $currentFileId = $this->_request->getParam('fileId');
            $report = $this->_request->getParam('report_content');
            if ($report != NULL) {
                $repordModel->addReport(Zend_Auth::getInstance()->getStorage()->read()['id'], $fileId, $report);
            }
        }

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
    }

}
