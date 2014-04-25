<?php

class Default_Model_Tag extends Zend_Db_Table_Abstract {

    protected $_name = "tag";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function isExistTag($tag_name) {
        $select = $this->db->select()
                ->from("tag")
                ->where("tag_name = ?", $tag_name);
        $result = $this->db->fetchRow($select);
        Zend_Debug::dump($result);
        if ($result)
            return $result["id"];
        else
            return false;
    }

    public function listAll() {
        $result = $this->fetchAll();
        if ($result) {
            return $result->toArray();
        } else {
            return array();
        }
    }

    public function listAllOfTeacher($teacherId) {
        $select = $this->getAdapter()->select();
        $select->from(array('t' => 'tag'))
                ->joinInner(array('lt' => 'lesson_tag'), 't.id=lt.tag_id', NULL)
                ->joinInner(array('l' => 'lesson'), 'l.lesson_id = lt.lesson_id')
                ->joinInner(array('u' => 'user'), 'l.teacher_id = u.id')
                ->where('u.id=?', $teacherId)
                ->group('t.id');
        return $this->fetchAll()->toArray();
    }

    public function getAllTagOfLesson($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from(array('t' => 'tag'))
                ->joinInner(array('lt' => 'lesson_tag'), 't.id=lt.tag_id', NULL)
                ->where("lt.lesson_id=$lessonId")
                ->group('t.id');
        return $this->getAdapter()->fetchAll($select);
    }

    public function listAllTagByStudent($studentId) {
        $select = $this->getAdapter()->select();
        $select->from(array('t' => 'tag'), "*")
                ->joinInner('lesson_tag', 'lesson_tag.tag_id = t.id', NULL)
                ->joinInner('lesson', 'lesson.id = lesson_tag.lesson_id', NULL)
                ->joinInner('learn', 'learn.lesson_id = lesson.id', NULL)
                ->joinInner('user', 'user.id = learn.student_id', NULL)
                ->where('user.id = ?', $studentId)
                ->group('t.id');
        return $this->getAdapter()->fetchAll($select);
    }

    public function listAllLessonWithTagByStudent($tag, $studentId) {
        $select = $this->getAdapter()->select();
        $select->from(array('t' => 'tag'))
                ->where("t.id=$tag")
                ->joinInner(array('lt' => 'lesson_tag'), 't.id = lt.tag_id', NULL)
                ->joinInner('lesson', 'lesson.id=learn.lesson_id')
                ->joinInner('learn', 'lesson.lesson_id=learn.lesson_id', NULL)
                ->where('learn.student_id=?', $studentId);
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * 授業がないタグを削除する
     */
    public function cleanUnuseTags() {
        $lessonModel = new Default_Model_Lesson();
        
        $tags = $this->listAll();
        foreach ($tags as $tag) {
            $lessons = $lessonModel->listWithTag($tag['id']);
            if (!$lessons) {
                $this->delete("id=".$tag['id']);
            }
        }
    }
}
