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
        echo $select;
        if ($result)
            return $result["id"];
        else
            return false;
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    public function getAllTagOfLesson($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from(array('t' => 'tag'))
                ->join(array('lt' => 'lesson_tag'), 't.id=lt.tag_id')
                ->where("lt.lesson_id=$lessonId")
                ->group('t.id');
        return $this->getAdapter()->fetchAll($select);
    }

    public function listAllTagByStudent($studentId) {
        $select = $this->getAdapter()->select();
        $select->from('tag')
                ->join('lesson_tag', 'lesson_tag.tag_id = tag.id')
                ->join('lesson', 'lesson.id = lesson_tag.lesson_id')
                ->join('learn', 'learn.lesson_id = lesson.id')
                ->join('user', 'user.id = learn.student_id')
                ->where('user.id = ?', $studentId);
        return $this->getAdapter()->fetchAll($select);
    }

}
