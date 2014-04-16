<?php

//require 'RegisterController.php';
class ApiController extends Zend_Controller_Action {
    
    private $userInfo;
    protected $ERROR_AUTHENTICAL = 1;
    protected $ERROR_COMMENT_EMPTY = 2;

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
        if ($errorCode == $this->ERROR_AUTHENTICAL) {
            $errorMsg = "Authentical error";
        } else if ($errorCode == $this->ERROR_AUTHENTICAL) {
            $errorMsg = "Comment empty";
        }
        echo json_encode(array(
            "error" => $errorMsg
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
}