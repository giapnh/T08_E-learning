<?php

class Default_Model_Lesson extends Zend_Db_Table_Abstract {

    protected $_name = "lesson";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    public function listWithTag($tag) {
        if ($tag == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->join(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id')
                ->where("lt.tag_id=$tag");
        return $this->getAdapter()->fetchAll($select);
    }

    public function listWithTeacher($teacher) {
        if ($teacher == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->join(array('u' => 'user'), 'l.teacher_id = u.id')
                ->where("l.teacher_id=$teacher");
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * 
     * @param type $id
     */
    public function findLessonById($lessonId) {
        return $this->fetchRow("id='$lessonId'");
    }

    /**
     * 
     * @param type $lessonId
     */
    public function getNumStudentJoinToCourseById($lessonId) {
        
    }

}
