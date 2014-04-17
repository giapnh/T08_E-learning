<?php

class Default_Model_LessonFile extends Zend_Db_Table_Abstract {

    protected $_name = "lesson_file";
    protected $_primary = "id";
    protected $db;
	protected $lessonDeadline;
    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
        $master = new Default_Model_Master();
        $this->_lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
    }

    public function findFileById($fileId) {
        return $this->fetchRow("id = ". $fileId);
    }

    public function listFileOfLesson($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson_file', "*")
                ->where("lesson_id= ?", $lessonId);
        return $this->getAdapter()->fetchAll($select);
    }
    //thiennx check user can see the file
    public function checkUserCanSeeFile($userId, $fileId){
    	$select = $this->getAdapter()->select()
    			->from($this->_name)
    			->join("learn", "learn.lesson_id = lesson_file.lesson_id")
    			->where("lesson_file.id = ?", $fileId)
    			->where("student_id = ?", $userId)
    			->where("learn.register_time + INTERVAL ".$this->_lessonDeadline." DAY >= NOW() ");
    	return $this->getAdapter()->fetchAll($select);
    }

}
