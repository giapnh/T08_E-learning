<?php

class Default_Model_Comment extends Zend_Db_Table_Abstract {

    protected $_name = "comment";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    public function countCommentOnLesson($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from(array('cm' => 'comment'), array('num_comment' => 'COUNT(user_id)'))
                ->where("lesson_id='$lessonId'")
                ->group('cm.user_id');
        return $this->getAdapter()->fetchRow($select)['num_comment'];
    }

    public function getAllCommentOfLesson($lesson_id) {
        $select = $this->getAdapter()->select();
        $select->from(array('cmt' => 'comment'))
                ->joinInner('user', 'cmt.user_id=user.id', array('name', 'role'))
                ->where("cmt.lesson_id='$lesson_id'");
        return $this->getAdapter()->fetchAll($select);
    }

    public function addComment($lessonId, $studentId, $comment) {
        $data = array(
            'user_id' => $studentId,
            'lesson_id' => $lessonId,
            'comment' => $comment
        );
        $this->insert($data);
    }

}
