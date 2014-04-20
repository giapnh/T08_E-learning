<?php

//require 'RegisterController.php';
class ApiController extends Zend_Controller_Action {
    
    private $userInfo;
    protected $ERROR_AUTHENTICAL = 1;
    protected $ERROR_COMMENT_EMPTY = 2;
    protected $ERROR_ACTION_FAILED = 3;

    /**
     * 初期処理
     */
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        
        $this->getHelper('Layout')
         ->disableLayout();

        $this->getHelper('ViewRenderer')
             ->setNoRender();

        $this->getResponse()
             ->setHeader('Content-Type', 'application/json');

        // UTF-8
        $this->getResponse()
             ->setHeader('Content-Type', 'application/json; charset=UTF-8');
        
        if ($this->getParam('action') != 'error') {
            // ログインチェック
            if (!$auth->hasIdentity()) {
                $this->redirect('api/error?code='.$this->ERROR_AUTHENTICAL);
            } else {
                $auth = Zend_Auth::getInstance();
                $this->userInfo = $auth->getStorage()->read();
            }
        }
    }
    
    /**
     * エラー処理
     */
    public function errorAction() {
        $errorCode = $this->getParam('code');
//        if ($errorCode == $this->ERROR_AUTHENTICAL) {
//            $errorMsg = "Authentical error";
//        } else if ($errorCode == $this->ERROR_AUTHENTICAL) {
//            $errorMsg = "Comment empty";
//        }
        echo json_encode(array(
            "error" => $errorCode
        ));
    }
    
    /**
     * コメント処理
     */
    public function testAction() {
        
        $comment = $this->getParam('comment');
        
        echo json_encode(array(
            "user" => $this->userInfo['username'],
            "comment" => $comment
        ));
    }
    
    /**
     * 授業コメント処理
     */
    public function lessonCommentAction() {
        $comment = $this->getParam('comment');
        $lessonId = $this->getParam('lesson_id');
        
        $commentModel = new Default_Model_Comment();
        
        if (isset($comment) && $comment != '') {
            $commentInfo = $commentModel->addComment($lessonId, $this->userInfo['id'], $comment);
            echo json_encode($commentInfo);
        } else {
            $this->redirect('api/error?code='.$this->ERROR_COMMENT_EMPTY);
        }
    }
    
    /**
     * ファイルコメント処理
     */
    public function fileCommentAction() {
        $comment = $this->getParam('comment');
        $fileId = $this->getParam('file_id');
        
        $commentModel = new Default_Model_FileComment();
        
        if (isset($comment) && $comment != '') {
            $commentInfo = $commentModel->addComment($fileId, $this->userInfo['id'], $comment);
            echo json_encode($commentInfo);
        } else {
            $this->redirect('api/error?code='.$this->ERROR_COMMENT_EMPTY);
        }
    }
    
    /**
     * ファイルのレポートを削除処理
     */
    public function deleteReportAction() {
        $reportId = $this->getParam('report-id');
        
        $copyrightModel = new Default_Model_CopyrightReport();
        $copyrightModel->deleteReport($reportId);
        echo json_encode("Success");
    }
    
    /**
     * ファイルをロックする処理
     */
    public function lockFileAction() {
        $fileId = $this->getParam('file_id');
        if ($this->userInfo['role'] !=3 ) {
            $this->redirect('api/error?code='.$this->$ERROR_AUTHENTICAL);
            return;
        }
        $fileModel = new Default_Model_File();
        if ($fileModel->lockFile($fileId)) {
            echo json_encode('success');
        } else {
            $this->redirect('api/error?code='.$this->ERROR_ACTION_FAILED);
        }
    }
    
    /**
     * ファイルをアンロックする処理
     */
    public function unlockFileAction() {
        $fileId = $this->getParam('file_id');
        if ($this->userInfo['role'] !=3 ) {
            $this->redirect('api/error?code='.$this->$ERROR_AUTHENTICAL);
            return;
        }
        $fileModel = new Default_Model_File();
        if ($fileModel->unlockFile($fileId)) {
            echo json_encode('success');
        } else {
            $this->redirect('api/error?code='.$this->ERROR_ACTION_FAILED);
        }
    }
    
    /**
     * ファイルを削除する処理
     */
    public function deleteFileAction() {
        $fileId = $this->getParam('file_id');
        if ($this->userInfo['role'] !=3 ) {
            $this->redirect('api/error?code='.$this->$ERROR_AUTHENTICAL);
            return;
        }
        $fileModel = new Default_Model_File();
        if ($fileModel->deleteFile($fileId)) {
            echo json_encode('success');
        } else {
            $this->redirect('api/error?code='.$this->ERROR_ACTION_FAILED);
        }
    }
}