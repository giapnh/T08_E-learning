<?php
require_once 'IController.php';
class Admin_LessonController extends IController {
    /**
     * 授業リスト画面
     */
    public function indexAction() {
//            $muser=new Default_Model_Account;
//            $data=$muser->listAll();
//            print_r($data);
        echo 'Account Controller, index action';
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