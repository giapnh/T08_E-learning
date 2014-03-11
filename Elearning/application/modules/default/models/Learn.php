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
                ->where("student_id=?", $studentId)
                ->where("lesson_id=?", $lesson)
                ->where('NOW() - INTERVAL 7 DAY < register_time');
        if ($this->getAdapter()->fetchRow($select) != NULL) {
            return 0; //Unsuccesful
        } else {
            return 1; //Succesful
        }
    }

    public function countStudenJoinLesson($lessonId) {
        return $this->getAdapter()->fetchOne("SELECT COUNT(*) AS count FROM learn WHERE lesson_id=$lessonId");
    }

    public function doRegisterLesson($studentId, $lessonId) {
        $ins_data = array(
            'student_id' => $studentId,
            'lesson_id' => $lessonId,
            'status' => '1'
        );
        $this->insert($ins_data);
    }

    public function findByLessonAndStudent($lessonId, $studentId) {
        $query = $this->select()
                ->where("lesson_id='".$lessonId."' and student_id='".$studentId."'");
        $result = $this->getAdapter()->fetchAll($query);
        if (count($result) != 0) {
            return $result[0];
        } else {
            return null;
        }
    }
}
