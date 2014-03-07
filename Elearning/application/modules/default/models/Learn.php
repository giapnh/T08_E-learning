<?php

class Default_Model_Learn extends Zend_Db_Table_Abstract {

    protected $_name = "learn";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    /**
     * 授業に学生が勉強したかどうかチェックする機能
     * @param type $studentId
     * @param type $lesson
     * @return int
     */
    public function isStudentLearn($studentId, $lesson) {
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'learn'), "*")
                ->where("student_id=$studentId")
                ->where("lesson_id=$lesson");
        if ($this->getAdapter()->fetchRow($select)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function countStudenJoinLesson($lessonId) {
        return $this->getAdapter()->fetchOne("SELECT COUNT(*) AS count FROM learn WHERE lesson_id=$lessonId");
    }

}
