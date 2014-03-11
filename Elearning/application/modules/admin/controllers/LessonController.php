<?php
require_once 'IController.php';
class Admin_LessonController extends IController {
    
    
    /**
     * 授業リスト画面
     */
    public function indexAction() {
        
        $baseurl = $this->_request->getbaseurl();
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style.css");
        $this->view->headLink()->appendStylesheet($baseurl . "/public/css/style_2.css");
        $this->view->headScript()->appendFile($baseurl . "/public/js/jquery.min.js");
        
        $lessons = new Default_Model_Lesson();
        $get_type = $this->_request->getParam('type');
        $tagId = $this->_request->getParam('tagId');
        $teacherId = $this->_request->getParam('teacherId');
        $this->view->tagId = $tagId;
        $this->view->teacherId = $teacherId;
        if ($get_type == null || $get_type == 1) {
            $tags = new Default_Model_Tag();
            $this->view->tags = $tags->listAll();
            $this->view->type = 1;
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
            $paginator = Zend_Paginator::factory($lessons->listWithTeacher($teacherId));
            $paginator->setItemCountPerPage(6);
            $paginator->setPageRange(3);
            $this->view->numpage = $paginator->count();
            $currentPage = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage);
            $this->view->data = $paginator;
        }

        if ($this->_request->isPost()) {
            $keyword = $this->_request->getParam('keyword');
            $paginator = Zend_Paginator::factory($lessons->findByKeyword($keyword));
            $paginator->setItemCountPerPage(6);
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
        
    }
    
    /**
     * ファイルを見る画面
     */
    public function fileAction() {
        
    }
    
    /**
     * レポートを見る画面
     */
    public function reportAction() {
        
    }
    
}