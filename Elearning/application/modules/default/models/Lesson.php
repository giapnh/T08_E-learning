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
                ->joinInner(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id')
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where("lt.tag_id=$tag");
        return $this->getAdapter()->fetchAll($select);
    }
    
    public function listTeacherLessonsByTag($teacher, $tag) {
        if ($tag == 0) {
            return $this->listWithTeacher($teacher);
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->joinInner(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id')
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where("lt.tag_id=$tag and l.teacher_id=$teacher");
        return $this->getAdapter()->fetchAll($select);
    }

    public function listWithTeacher($teacher) {
        if ($teacher == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->joinInner(array('u' => 'user'), 'l.teacher_id = u.id')
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where("l.teacher_id=$teacher");
        return $this->getAdapter()->fetchAll($select);
    }

    public function listAllByStudent($student) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                ->joinInner('learn', 'learn.lesson_id=lesson.id', array('status'))
                ->where("learn.student_id=$student");
        return $this->getAdapter()->fetchAll($select);
    }

    public function findLessonWithTagByStudent($tagId, $studentId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id', NULL)
                ->where('lesson_tag.tag_id=?', $tagId)
                ->joinInner('learn', 'learn.lesson_id=lesson_tag.lesson_id', array('status'))
                ->joinInner('user', 'user.id=lesson.teacher_id', array('name'))
                ->where('learn.student_id=?', $studentId);
        return $this->getAdapter()->fetchAll($select);
    }

    public function findLessonWithTeacherByStudent($teacher, $studentId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('learn', 'lesson.id=learn.lesson_id', array('status'))
                ->where('lesson.teacher_id=?', $teacher)
                ->where('learn.student_id=?', $studentId)
                ->joinInner('user', 'user.id=lesson.teacher_id', array(name));
        echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * 
     * @param type $id
     */
    public function findLessonById($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->where('lesson.id = ?', $lessonId)
                ->joinInner('user', 'lesson.teacher_id = user.id', array('name'));
        return $this->getAdapter()->fetchRow($select);
    }

    public function incrementView($lessonId) {
        $view = $this->fetchRow("id='$lessonId'")['view'];
        $update = array(
            'view' => $view + 1
        );
        $where = "id='$lessonId'";
        $this->update($update, $where);
    }

    /**
     * 検索機能：先生名、タイトル、。。。 
     * @param type $keyword
     * @return type
     */
    public function findByKeyword($keyword) {
        $select = $this->getAdapter()->select();
//        $keyword = utf8_encode($keyword);
        $select->from('lesson')
                ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id')
                ->joinInner('tag', 'lesson_tag.tag_id=tag.id', array('tag_name'))
                ->where("name LIKE '%$keyword%'")
                ->orWhere("tag_name  LIKE '%$keyword%'")
                ->orWhere("title LIKE '%$keyword%'")
                ->orWhere("description LIKE '%$keyword%'")
                ->group('lesson.id');
        return $this->getAdapter()->fetchAll($select);
    }

}
