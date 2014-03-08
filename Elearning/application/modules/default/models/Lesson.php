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
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->join('user', 'lesson.teacher_id=user.id', array('name'));
        return $this->getAdapter()->fetchAll($select);
    }

    public function listWithTag($tag) {
        if ($tag == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->join(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id')
                ->join('user', 'l.teacher_id=user.id', array('name'))
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
                ->join('user', 'l.teacher_id=user.id', array('name'))
                ->where("l.teacher_id=$teacher");
        return $this->getAdapter()->fetchAll($select);
    }

    public function listAllByStudent($student) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->join('user', 'lesson.teacher_id=user.id', array('name'))
                ->join('learn', 'learn.lesson_id=lesson.id')
                ->where("learn.student_id=$student");
        return $this->getAdapter()->fetchAll($select);
    }

    public function listWithTagByStudent($tag, $studentId) {
        if ($tag == 0) {
            return $this->listAllByStudent($studentId);
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->join(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id')
                ->join('user', 'l.teacher_id=user.id', array('name'))
                ->where("lt.tag_id=$tag")
                ->where('l.student_id=?', $studentId);
        return $this->getAdapter()->fetchAll($select);
    }

    public function listWithTeacherByStudent($teacher, $studentId) {
        if ($teacher == 0) {
            return $this->listAllByStudent($studentId);
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->join(array('u' => 'user'), 'l.teacher_id = u.id')
                ->join('user', 'l.teacher_id=user.id', array('name'))
                ->where("l.teacher_id=$teacher")
                ->where('l.student_id=?', $studentId);
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * 
     * @param type $id
     */
    public function findLessonById($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->where('lesson.id=?', $lessonId)
                ->join('user', 'lesson.teacher_id=user.id');
        return $this->getAdapter()->fetchRow($select);
    }

    /**
     * 
     * @param type $lessonId
     */
    public function getNumStudentJoinToCourseById($lessonId) {
        
    }

}
