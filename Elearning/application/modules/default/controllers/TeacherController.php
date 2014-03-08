<?php

require_once 'IController.php';

//require 'RegisterController.php';
class TeacherController extends IController {

    protected $user;

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
            $parram = $this->_getAllParams();
            Zend_Debug::dump($this->_getAllParams());
            $adapter = new Zend_File_Transfer_Adapter_Http();
            $uploadDir = APPLICATION_PATH . '\..\upload\\';
            $adapter->setDestination($uploadDir);
            //$upload->setFilters($filters)
            //$upload->receive();
            Zend_Debug::dump($adapter->getFileInfo());
            Zend_Debug::dump($_FILES);
            $i = 1;
            $listFiles = array();
            $k = 0;
            foreach ($adapter->getFileInfo() as $file => $info) {
                if ($adapter->isUploaded($file)) {
                    $ext = $this->_findexts($info['name']);
                    $fileName = 'file' . $i . time() . '.' . $ext;

                    $i++;
                    $target = $uploadDir . $fileName;
                    $adapter->addFilter('Rename', array('target' => $target,
                        'overwrite' => true));
                    echo $file;
                    if (!$adapter->receive($file)) {

                        $this->view->msg = $adapter->getMessages();
                        return;
                    } else {
                        $listFiles[] = array(
                            "filename" => $parram["file_title"][$k] != "" ? $parram["file_title"][$k] : $info['name'],
                            "description" => $parram["file_dec"][$k],
                            "location" => $fileName
                        );
                    }
                }
                $k++;
            }
            if (count($listFiles) >= 0) {
                $lessonModel = new Default_Model_Lesson();
                $les_id = $lessonModel->insert(array(
                    "teacher_id" => $this->user->id,
                    "title" => $parram["title"],
                    "description" => $parram["les_dec"],
                    "status" => 1
                ));
                $tags = explode("、", $parram["tag"]);
                $tagModel = new Default_Model_Tag();
                $leTagModel = new Default_Model_LessonTag();
                $lesFileModel = new Default_Model_LessonFile();
                foreach ($tags as $tag) {
                    if (trim($tag)) {
                        $tag_id = $tagModel->isExistTag($tag);
                        if (!$tag_id) {
                            $tag_id = $tagModel->insert(array("tag_name" => $tag));
                        }
                        $leTagModel->insert(array(
                            "lesson_id" => $les_id,
                            "tag_id" => $tag_id
                        ));
                    }
                }
                foreach ($listFiles as $file) {
                    $lesFileModel->insert(array(
                        "lesson_id" => $les_id,
                        "filename" => $file["filename"],
                        "description" => $file["description"],
                        "location" => $file["location"]
                    ));
                }
            }
        }
    }

    protected function _findexts($filename) {

        $filename = strtolower($filename);

        $exts = explode(".", $filename);


        $n = count($exts) - 1;


        $exts = $exts[$n];


        return $exts;
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
