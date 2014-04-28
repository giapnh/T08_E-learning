<?php
require_once 'IController.php';
class Admin_LessonController extends IController {
    
    public static $LESSON_PER_PAGE = 3;
    private $currentUser;

    /**
     * 授業リスト画面
     */
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data = $auth->getIdentity();
            if ($data['role'] != 3) {
               $this->_redirect('user/login');
            } else {
                $this->currentUser = $data;
                $this->view->currentUser = $data;
            }
        } elseif ($this->_request->getActionName() != 'login') {
            $this->_redirect('admin/account/login');
        }
    }
    
    /**
     * 授業リスト処理
     */
    public function indexAction() {
        
        //
        $lessonModel = new Default_Model_Lesson();
        $copyrightModel = new Default_Model_CopyrightReport();
        $tagModel = new Default_Model_Tag();
        $userModel = new Default_Model_Account();
        $lessonReportModel = new Default_Model_LessonReport();
        
        //
        $tagId = $this->_request->getParam('tagId');
        $teacherId = $this->_request->getParam('teacherId');
        $searchText = $this->_request->getParam('searchText');
        $currentPage = $this->_request->getParam('page', 1);
        $copyright = $this->_request->getParam('copyright');
        $sortType = $this->getParam('sort_type');
        $asc = $this->getParam('sort_asc');
        if (!isset($sortType)) {
            $sortType = 0;
        }
        if (!isset($asc)) {
            $asc = 0;
        }
        $fileReportsNum = $copyrightModel->countAllReport();
        $lessonReportsNum = $lessonReportModel->countAllReport();
        
        //
        $this->view->tagId = $tagId;
        $this->view->teacherId = $teacherId;
        $this->view->searchText = $searchText;
        $this->view->copyright = $copyright;
        $this->view->sortType = $sortType;
        $this->view->asc = $asc;
        $this->view->tags = $tagModel->listAll();
        $this->view->teachers = $userModel->listTeacher();
        $this->view->reportsNum = $fileReportsNum + $lessonReportsNum;
        
        //
        if (isset($tagId)) {
            $paginator = Zend_Paginator::factory($lessonModel->listWithTag($tagId,$sortType,$asc));
        } else if (isset($teacherId)) {
            $paginator = Zend_Paginator::factory($lessonModel->listWithTeacher($teacherId,$sortType,$asc));
        } else if (isset($searchText)) {
            $paginator = Zend_Paginator::factory($lessonModel->findByKeyword($searchText, $sortType, $asc));
        } else if (isset($copyright)) {
            $paginator = Zend_Paginator::factory($lessonModel->listCopyrightFalse($sortType,$asc));
        } else {
            $paginator = Zend_Paginator::factory($lessonModel->listAll($sortType,$asc));
        }
        
        //
        $paginator->setItemCountPerPage(self::$LESSON_PER_PAGE);
        $paginator->setPageRange(3);
        $this->view->numpage = $paginator->count();
        $paginator->setCurrentPageNumber($currentPage);
        $this->view->data = $paginator;
    }
    
    /**
     * 授業を見る画面
     */
    public function lessonAction() {
        $lessonId = $this->_request->getParam('lesson_id');
           $lessonModel = new Default_Model_Lesson();
           $fileModel = new Default_Model_File();
           $lessonTagModel = new Default_Model_LessonTag();
           $commentModel = new Default_Model_Comment();
           $learnModel = new Default_Model_Learn();
           $lessonReportModel = new Default_Model_LessonReport();
           
           $comment = $this->getParam('comment');
           if (isset($comment) && $comment != '') {
                $commentModel->addComment($lessonId, $this->currentTeacherId, $comment);
           }

           $lesson = $lessonModel->findLessonById($lessonId);
           $files = $fileModel->getFileByLesson($lessonId);
           
           $tags = $lessonTagModel->getTagsByLesson($lessonId);
           $comments = $commentModel->getAllCommentOfLesson($lessonId);
           $studentsNum = $learnModel->countStudenJoinLesson($lessonId);
           $reports = $lessonReportModel->getReportsFull($lessonId);
           
           $lesson['students_num'] = $studentsNum;
           
           $this->view->lessonId = $lessonId;
           $this->view->lessonInfo = $lesson;
           $this->view->files = $files;
           $this->view->tags = $tags;
           $this->view->comments = $comments;
           $this->view->reports = $reports;
           $this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('addFileFailed');
           $this->view->messages = $this->_helper->FlashMessenger->getMessages('addFileSuccess');
           $this->view->currentUser = $this->currentUser;
//           $this->view->currentUser = $this->
//           $this->view->reports = $reportModel->getReportLesson($lessonId);
//           $this->view->reports = null;
    }
    
    public function deleteReportAction() {
        $this->getHelper('ViewRenderer')
             ->setNoRender();
        
        $lessonReportModel = new Default_Model_LessonReport();
        
        $reportId = $this->getParam('report_id');
        $lessonId = $this->getParam('lesson_id');
        $lessonReportModel->deleteReport($reportId);
        $this->redirect('admin/lesson/lesson?lesson_id='.$lessonId);
    }
    
    /**
     * ファイルを見る画面
     */
    public function fileAction() {
        //
        $lessonModel = new Default_Model_Lesson();
        $lessonFileModel = new Default_Model_File();
        $filecommentModel = new Default_Model_FileComment();
        $reportModel = new Default_Model_CopyrightReport();
        $learnModel = new Default_Model_Learn();

        //
        $lessonId = $this->_request->getParam('lesson_id');
        $currentFileId = $this->_request->getParam('file_id');
        if(!$curreantFileId)
        	$currentFileId = 0;
        //
        $currentFile = $lessonFileModel->findFileById($currentFileId);
        $lessonInfo = $lessonModel->findLessonById($lessonId);
        $studentsNum = $learnModel->countStudenJoinLesson($lessonId);
        $lessonInfo['students_num'] = $studentsNum;
        $files = $lessonFileModel->getFileByLesson($lessonId);
        if ($currentFile == NULL) {
            if (count($files) > 0) {
                $currentFile = $files[0];
            } else {
                $this->view->fileError = "ファイルがない";
                $this->redirect("admin/lesson/lesson?do=view&lesson_id=".$lessonId);
            }
        }
        $reports = $reportModel->getReport($currentFile['id']);
        $comments = $filecommentModel->getAllCommentOfFile($currentFile['id']);
        
        
        
        // ファイルはこの授業のファイルかをチェック
        if ($currentFile) {
            if ($currentFile['lesson_id'] != $lessonInfo['id']) {
                $currentFile = NULL;
                $this->view->fileError = "ファイルが無効";
            }
        }
        
        //
        $this->view->currentFile = $currentFile;
        $this->view->lessonInfo = $lessonInfo;
        $this->view->files = $files;
        $this->view->reports = $reports;
        $this->view->comments = $comments;
        

        $this->view->fileModel = new Default_Model_File();
        $this->view->controller = $this;
        $this->view->lessonId = $lessonId;
    }
    
    public function streamAction() {
        $fileId = $this->_request->getParam("id");
        $lessonFileModel = new Default_Model_File();
        $file = $lessonFileModel->findFileById($fileId);
        $path = $lessonFileModel->getFileFolder() . $file["location"];
        
        $currentFileExt = explode(".", $file['filename']);
        $currentFileExt = $currentFileExt[count($currentFileExt)-1];
        $arrayType = array(
            "pdf" => "application/pdf",
            "mp3" => "audio/mpeg",
            "mp4" => "video/mp4",
            "jpg" => "image/jpeg",
            "png" => "image/jpeg",
            "gif" => "image/jpeg",
            "wav" => "audio/mpeg"
        );
        
        if (is_readable($path)) {
            if ($currentFileExt == "pdf") {
                echo    $link = $this->view->serverUrl() . $this->view->baseUrl() . "/public/viewpdf/web/viewer.html?file=" 
                        . $this->view->serverUrl() . $this->view->baseUrl() . "/" . $lessonFileModel->getFileFolderName() . $file["location"];
                header("Location: " . $link);
            } else {
                header('Content-type: ' . $arrayType[$currentFileExt]);
                header("Content-Length: " . filesize($path));
                readfile($path);
            }
        }
        exit();
    }
    
    /**
     * テストファイルからHtml文字列を取る
     * 
     * @param int $testId
     * @return string Html String
     */
    public function getTestHtml($testId) {
        $fileModel = new Default_Model_File();
        return $fileModel->getTestHtml($testId);
    }
    
    /**
     * 授業をロックする処理
     */
    public function lockAction() {
        $lessonId = $this->getParam('lesson_id');
        $lessonModel = new Default_Model_Lesson();
        $userModel = new Default_Model_Account();
        
        $lessonModel->lockLesson($lessonId);
        $lesson = $lessonModel->findLessonById($lessonId);
        $userModel->upViolationLock($lesson['teacher_id']);
        
        $this->redirect('admin/lesson/lesson?lesson_id='.$lessonId);
    }
    
    /**
     * 授業をアンロックする処理
     */
    public function unlockAction() {
        $lessonId = $this->getParam('lesson_id');
        $lessonModel = new Default_Model_Lesson();
        $userModel = new Default_Model_Account();
        
        $lessonModel->unlockLesson($lessonId);
        $lesson = $lessonModel->findLessonById($lessonId);
        $userModel->downViolationLock($lesson['teacher_id']);
        
        $this->redirect('admin/lesson/lesson?lesson_id='.$lessonId);
    }
}