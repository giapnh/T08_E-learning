<?php

require_once 'IController.php';

//require 'RegisterController.php';
class TeacherController extends IController {

    protected $user;
    protected $currentTeacherId;

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
        $this->currentTeacherId = $infoUser['id'];
    }

    public function indexAction() {
        $this->initial();
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

            // Check title
            if ((!isset($param['title'])) || $param['title'] == '') {
                $this->view->errorMessage = Message::$M020;
                return;
            }

            // Check description
            if ((!isset($param['description'])) || $param['description'] == '') {
                $this->view->errorMessage = Message::$M046;
                return;
            }

            // Save file
            $fileModel = new Default_Model_File();
            if (!$fileModel->exercuteFiles($param["file_dec"])) {
                $this->view->errorMessage = Message::$M023;
                return;
            }

            // Check tag
            if (!isset($param['tags'])) {
                $this->view->errorMessage = Message::$M021;
                return;
            }

            // Save lesson
            $lessonModel = new Default_Model_TeacherLesson();
            $title = $param['title'];
            $description = $param['description'];
            $lessonId = $lessonModel->createLesson($this->currentTeacherId, $title, $description);

            // Save files
            $fileModel->createFilesData($lessonId);

            // Redirect
            $this->redirect("teacher/lesson");
        }
    }

    /**
     * 授業を見る画面
     * @param type $name Description
     */
    public function lessonAction() {
        $this->initial();
    }

    /**
     * ファイルを見る画面
     * @param type $name Description
     */
    public function fileAction() {
        $this->initial();

        $fileLocation = "13942932700.html";
        $file = file(APPLICATION_PATH . Default_Model_File::$UPLOAD_DIR . $fileLocation);
        $testHtml = implode("", $file);
        $this->view->testHtml = $testHtml;
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
        
    }

    /**
     * 学生のテスト結果を見る画面
     * @param type $name Description
     */
    public function studentResultAction() {
        
    }

    /**
     * 課金情報処理
     * 
     * 
     */
    public function paymentAction() {
        $this->initial();
    }

}
