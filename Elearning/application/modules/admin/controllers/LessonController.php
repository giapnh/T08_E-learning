<?php
require_once 'IController.php';
class Admin_LessonController extends IController {
    
    
    /**
     * 授業リスト画面
     */
    public function indexAction() {
        
        $baseurl = $this->_request->getbaseurl();
//        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style.css");
//        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style_2.css");
//        $this->view->headScript()->appendFile($baseurl . "/public/js/jquery.min.js");
        
        $lessonModel = new Default_Model_Lesson();
        $copyrightModel = new Default_Model_CopyrightReport();
        
        $get_type = $this->_request->getParam('type');
        $tagId = $this->_request->getParam('tagId');
        $teacherId = $this->_request->getParam('teacherId');
        $this->view->tagId = $tagId;
        $this->view->teacherId = $teacherId;
        $tagModel = new Default_Model_Tag();
        $userModel = new Default_Model_Account();
        $this->view->tags = $tagModel->listAll();
        $this->view->teachers = $userModel->listTeacher();
        $this->view->reportsNum = $copyrightModel->countAllReport();
        
        if ($get_type == null || $get_type == 1) {
            $this->view->type = 1;
            $paginator = Zend_Paginator::factory($lessonModel->listWithTag($tagId));
            $paginator->setItemCountPerPage(3);
            $paginator->setPageRange(3);
            $this->view->numpage = $paginator->count();
            $currentPage = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage);
            $this->view->data = $paginator;
        } else {
            $this->view->type = 2;
            $paginator = Zend_Paginator::factory($lessonModel->listWithTeacher($teacherId));
            $paginator->setItemCountPerPage(3);
            $paginator->setPageRange(3);
            $this->view->numpage = $paginator->count();
            $currentPage = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage);
            $this->view->data = $paginator;
        }

        if ($this->_request->isPost()) {
            $keyword = $this->_request->getParam('keyword');
            $paginator = Zend_Paginator::factory($lessonModel->findByKeyword($keyword));
            $paginator->setItemCountPerPage(3);
            $paginator->setPageRange(3);
            $this->view->numpage = $paginator->count();
            $currentPage = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage);
            $this->view->data = $paginator;
        }
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
        
        $lessonModel = new Default_Model_Lesson();
        $lessonFileModel = new Default_Model_LessonFile();
        $filecommentModel = new Default_Model_FileComment();
        $reportModel = new Default_Model_CopyrightReport();

        if ($this->_request->isGet()) {
            $lessonId = $this->_request->getParam('lesson_id');
            $currentFileId = $this->_request->getParam('file_id');
        }
        if ($this->_request->isPost()) {
            $u = Zend_Auth::getInstance()->getStorage()->read();
            $lessonId = $this->_request->getParam('lesson_id');
            $currentFileId = $this->_request->getParam('file_id');
            $comment = $this->_request->getParam('comment');
            if ($comment != NULL) {

                $filecommentModel->addComment($currentFileId, $u['id'], $comment);
            }
        }
        $this->view->reports = $reportModel->getReport($currentFileId);
        $this->view->comments = $filecommentModel->getAllCommentOfFile($currentFileId);
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

        $this->view->fileModel = new Default_Model_File();
        $this->view->controller = $this;
        $this->view->lessonId = $lessonId;
    }
    
    public function getTestHtml($testId) {
        $fileModel = new Default_Model_File();
        return $fileModel->getTestHtml($testId);
    }
    
    /**
     * レポートを見る画面
     */
    public function reportAction() {
        
    }
    
}