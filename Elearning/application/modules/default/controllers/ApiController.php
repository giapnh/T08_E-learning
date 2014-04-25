<?php

//require 'RegisterController.php';
class ApiController extends Zend_Controller_Action {
    
    private $userInfo;
    protected $ERROR_AUTHENTICAL = 1;
    protected $ERROR_COMMENT_EMPTY = 2;
    protected $ERROR_ACTION_FAILED = 3;

    // モデル
    private $lessonCommentModel;
    private $fileCommentModel;
    private $copyrightModel;
    private $fileModel;
    private $lessonReportModel;
    private $lessonModel;
    private $userModel;

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
        
        $this->initModels();
    }
    
    private function initModels() {
        $this->lessonCommentModel = new Default_Model_Comment();
        $this->fileCommentModel = new Default_Model_FileComment();
        $this->copyrightModel = new Default_Model_CopyrightReport();
        $this->fileModel = new Default_Model_File();
        $this->lessonReportModel = new Default_Model_LessonReport();
        $this->lessonModel = new Default_Model_Lesson();
        $this->userModel = new Default_Model_Account();
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
        
        if (isset($comment) && $comment != '') {
            $commentInfo = $this->lessonCommentModel->addComment($lessonId, $this->userInfo['id'], $comment);
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
            $commentInfo = $this->fileCommentModel->addComment($fileId, $this->userInfo['id'], $comment);
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
        
        $this->copyrightModel->deleteReport($reportId);
        echo json_encode("Success");
    }
    
    /**
     * ファイルをロックする処理
     */
    public function lockFileAction() {
//        $userModel = new Default_Model_Account();
//        $lessonModel = new Default_Model_Lesson();
//        $fileModel = new Default_Model_File();
        
        $fileId = $this->getParam('file_id');
        if ($this->userInfo['role'] !=3 ) {
            $this->redirect('api/error?code='.$this->$ERROR_AUTHENTICAL);
            return;
        }
        if ($this->fileModel->lockFile($fileId)) {
            $file = $this->fileModel->findFileById($fileId);
            $lesson = $this->lessonModel->findLessonById($file['lesson_id']);
            $this->userModel->upViolationLock($lesson['teacher_id']);
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
        if ($this->fileModel->unlockFile($fileId)) {
            $file = $this->fileModel->findFileById($fileId);
            $lesson = $this->lessonModel->findLessonById($file['lesson_id']);
            $this->userModel->downViolationLock($lesson['teacher_id']);
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
        if ($this->userInfo['role'] != 3 ) {
            $this->redirect('api/error?code='.$this->$ERROR_AUTHENTICAL);
            return;
        }
        
        if ($this->fileModel->deleteFile($fileId)) {
            echo json_encode('success');
        } else {
            $this->redirect('api/error?code='.$this->ERROR_ACTION_FAILED);
        }
    }
    
    /**
     * 授業をレポート処理
     */
    public function reportLessonAction() {
        $lessonId = $this->getParam('lesson_id');
        $reason = $this->getParam('reason');
        
        $this->lessonReportModel->addReport($this->userInfo['id'], $lessonId, $reason);
        $reports = $this->lessonReportModel->getReports($lessonId);
        echo json_encode(count($reports));
    }
    
    /**
     * 授業をレポート処理
     */
    public function adminReportLessonAction() {
        $lessonId = $this->getParam('lesson_id');
        $reason = $this->getParam('reason');
        
        $reportId = $this->lessonReportModel->addAdminReport($this->userInfo['id'], $lessonId, $reason);
        $reports = $this->lessonReportModel->getReportsFull($lessonId);
        $report = $this->lessonReportModel->getReportById($reportId);
        $report['reports_num'] = count($reports);
        echo json_encode($report);
    }
}