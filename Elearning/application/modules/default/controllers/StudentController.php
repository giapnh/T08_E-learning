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

    public function registerlessonAction() {
        $this->initial();
        $lessonModel = new Default_Model_Lesson();
        $tagModel = new Default_Model_Tag();
        $learnModel = new Default_Model_Learn();
        if ($this->_request->isGet()) {
            $lesson_id = $this->_request->getParam('id');
            $info = $lessonModel->findLessonById($lesson_id);
            // Number of student join to this course
            $tagInfo = $tagModel->getAllTagOfLesson($lesson_id);
            $this->view->lessonInfo = $info;
            $this->view->tagsInfo = $tagInfo;
            $this->view->numStudent = $learnModel->countStudenJoinLesson($lesson_id)[0];
        }
    }

}
