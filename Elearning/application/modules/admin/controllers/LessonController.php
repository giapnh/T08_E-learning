<?php
require_once 'IController.php';
class Admin_LessonController extends IController {
    
    public static $LESSON_PER_PAGE = 3;

    /**
     * 授業リスト画面
     */
    public function indexAction() {
        
        //
        $lessonModel = new Default_Model_Lesson();
        $copyrightModel = new Default_Model_CopyrightReport();
        $tagModel = new Default_Model_Tag();
        $userModel = new Default_Model_Account();
        
        //
        $tagId = $this->_request->getParam('tagId');
        $teacherId = $this->_request->getParam('teacherId');
        $searchText = $this->_request->getParam('text');
        $currentPage = $this->_request->getParam('page', 1);
        $copyright = $this->_request->getParam('copyright');
        
        //
        $this->view->tagId = $tagId;
        $this->view->teacherId = $teacherId;
        $this->view->searchText = $searchText;
        $this->view->copyright = $copyright;
        $this->view->tags = $tagModel->listAll();
        $this->view->teachers = $userModel->listTeacher();
        $this->view->reportsNum = $copyrightModel->countAllReport();
        
        //
        if (isset($tagId)) {
            $paginator = Zend_Paginator::factory($lessonModel->listWithTag($tagId));
        } else if (isset($teacherId)) {
            $paginator = Zend_Paginator::factory($lessonModel->listWithTeacher($teacherId));
        } else if (isset($searchText)) {
            $paginator = Zend_Paginator::factory($lessonModel->findByKeyword($searchText));
        } else if (isset($copyright)) {
            $paginator = Zend_Paginator::factory($lessonModel->listCopyrightFalse());
        } else {
            $paginator = Zend_Paginator::factory($lessonModel->listAll());
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

           $comment = $this->getParam('comment');
           if (isset($comment) && $comment != '') {
                   $commentModel->addComment($lessonId, $this->currentTeacherId, $comment);
           }

           $lesson = $lessonModel->findLessonById($lessonId);
           $files = $fileModel->getFileByLesson($lessonId);
           
           $tags = $lessonTagModel->getTagsByLesson($lessonId);
           $comments = $commentModel->getAllCommentOfLesson($lessonId);
           $studentsNum = $learnModel->countStudenJoinLesson($lessonId);
           $lesson['students_num'] = $studentsNum;
           $this->view->lessonId = $lessonId;
           $this->view->lessonInfo = $lesson;
           $this->view->files = $files;
           $this->view->tags = $tags;
           $this->view->comments = $comments;
           $this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('addFileFailed');
           $this->view->messages = $this->_helper->FlashMessenger->getMessages('addFileSuccess');
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
        
        $lessonModel->lockLesson($lessonId);
        
        $this->redirect('admin/lesson/lesson?lesson_id='.$lessonId);
    }
    
    /**
     * 授業をアンロックする処理
     */
    public function unlockAction() {
        $lessonId = $this->getParam('lesson_id');
        $lessonModel = new Default_Model_Lesson();
        
        $lessonModel->unlockLesson($lessonId);
        
        $this->redirect('admin/lesson/lesson?lesson_id='.$lessonId);
    }
}