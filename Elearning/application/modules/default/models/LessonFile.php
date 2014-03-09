<?php

class Default_Model_LessonFile extends Zend_Db_Table_Abstract {

    protected $_name = "lesson_file";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function findFileById($fileId) {
        return $this->fetchRow("id='$fileId'");
    }

    public function listFileOfLesson($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from(array('lf' => 'lesson_file'), "*")
                ->where("lesson_id=$lessonId");
        return $this->getAdapter()->fetchAll($select);
    }

}
