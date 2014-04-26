<?php
class Default_Model_Lock extends Zend_Db_Table_Abstract {

	protected $_name = "lesson_lock";
	protected $_primary = "id";
	protected $db;

	public function __construct() {
		parent::__construct();
		$this->db = Zend_Registry::get('connectDB');
	}
	public function getByStudentAndLesson($studentId,  $lessonId, $con = false){
		$select = $this->getAdapter()->select()
			->from("lesson_lock")
			->where("student_id = ?", $studentId)
			->where("lesson_id = ?", $lessonId);
		if($con)
			$select->where("status = 1");
		return $this->getAdapter()->fetchRow($select);
	}
}