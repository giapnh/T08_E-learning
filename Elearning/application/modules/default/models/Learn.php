<?php

class Default_Model_Learn extends Zend_Db_Table_Abstract {

    protected $_name = "learn";
    protected $_primary = "id";
    protected $db;
    protected $_lessonDeadline;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
        $master = new Default_Model_Master();
        $this->_lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
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
    public function isStudentLearn($studentId, $lesson, $time = null) {
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'learn'), "*")
                ->where("student_id=?", $studentId)
                ->where("lesson_id=?", $lesson)
                ->where('NOW() - INTERVAL ' . $this->_lessonDeadline . ' DAY < register_time');
        if ($this->getAdapter()->fetchRow($select) != NULL) {
            return 0; //Unsuccesful
        } else {
            return 1; //Succesful
        }
    }

    /**
     * 授業に学生がロックさせたかどうかチェックする機能
     * @param type $studentId
     * @param type $lessonId
     * @return int
     */
    public function isStudentBeLocked($studentId, $lessonId) {
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'learn'), "*")
                ->where("student_id=?", $studentId)
                ->where("lesson_id=?", $lessonId);
        $res = $this->getAdapter()->fetchRow($select);
        if ($res == NULL) {
            return 0; //Unsuccesful
        } else {
            return $res['status']; //Succesful
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

    //thiennx get payment info student
    public function getStudentTotalPaymentInfo($studen_id) {
        $query = $this->select()
                ->from($this->_name, "COUNT(id) as total, DATE_FORMAT(`register_time`, '%Y') as year, DATE_FORMAT(`register_time`, '%m') as month, DATE_FORMAT(`register_time`, '%Y年%m月') as time")
                ->where("student_id = ?", $studen_id)
                ->group("time")
                ->order("time");
        return $this->fetchAll($query)->toArray();
    }

    // get payment info teacher
    public function getTeacherTotalPaymentInfo($teacher_id, $year, $month) {
        $query = $this->getAdapter()->select()
                ->from($this->_name, "COUNT(learn.id) as total, DATE_FORMAT(`register_time`, '%Y') as year, DATE_FORMAT(`register_time`, '%m') as month, DATE_FORMAT(`register_time`, '%Y年%m月') as time")
                ->join("lesson", "lesson.id = learn.lesson_id")
                ->where("teacher_id = ?", $teacher_id)
                ->where("DATE_FORMAT(`register_time`, '%Y') = ?", $year)
                ->where("DATE_FORMAT(`register_time`, '%m') = ?", $month)
                ->group("lesson_id");
        return $this->getAdapter()->fetchAll($query);
    }

    public function findByLessonAndStudent($lessonId, $studentId) {
        $query = $this->select()
                ->where("lesson_id='" . $lessonId . "' and student_id='" . $studentId . "'");
        $result = $this->getAdapter()->fetchAll($query);
        if (count($result) != 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    //get students who learn the lesson
    public function getStudentsByLessonId($lessonId) {
        $query = $this->getAdapter()->select()
                ->from($this->_name)
                ->join("lesson", "lesson.id = learn.lesson_id", array("title"))
                ->join("user", "learn.student_id = user.id", array("username", "name", "email", "phone"))
                ->joinLeft("lesson_like", "lesson_like.user_id = learn.student_id AND lesson_like.lesson_id = " . $lessonId, array("liked" => "id"))
                ->joinLeft("comment", "comment.user_id = learn.student_id AND comment.lesson_id=" . $lessonId, array("comment" => "COUNT(comment.id)"))
                ->group("learn.id")
                ->where("learn.lesson_id = $lessonId")
                ->order("register_time DESC");
        return $this->getAdapter()->fetchAll($query);
    }
    
    public function getStudentByLearnId($learnId) {
        $userModel = new Default_Model_Account();
        $learn = $this->find($learnId);
        if ($learn && count($learn)>0) {
            $learn = $learn[0];
            $student = $userModel->getUserById($learn['student_id']);
            if ($student) {
                return $student;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    //lock students
    public function lockStudent($ids) {
        $q = $this->db->quoteInto("update learn set status = 0 where id =?", $ids);
        $this->db->query($q);
    }

    //unlock students
    public function unlockStudent($ids) {
        $q = $this->db->quoteInto("update learn set status = 1 where id =?", $ids);
        $this->db->query($q);
    }

}
