<?php
class Admin_Model_Learn extends Zend_Db_Table_Abstract {
	protected $_name = "learn";
	protected $_primary = "id";
	protected $db;
	
	public function getTotalPaymentInfo(){
		$query = $this->select()
			->from($this->_name, "COUNT(id) as total, DATE_FORMAT(`register_time`, '%Y') as year, DATE_FORMAT(`register_time`, '%m') as month, DATE_FORMAT(`register_time`, '%Y年%m月') as time")
			->group("time")
			->order("time");
		return $this->fetchAll($query)->toArray();
	}
	public function getPaymentInfoByMonth($month, $year){
		$query = $this->getAdapter()->select()
		->from($this->_name, "COUNT(learn.id) as total")
		->join("user", "learn.student_id = user.id", array("username", "name", "address", "role", "bank_account","phone"))
		->where("DATE_FORMAT(`register_time`, '%Y') = ?", $year)
		->where("DATE_FORMAT(`register_time`, '%m') = ?", $month)
		->group("student_id");
		return $this->getAdapter()->fetchAll($query);
		
	}
	public function getTeacherPaymentInfoByMonth($month, $year){
		$query = $this->getAdapter()->select()
		->from($this->_name, "COUNT(learn.id) as total")
		->join("lesson", "learn.lesson_id = lesson.id", array("teacher_id"))
		->join("user", "lesson.teacher_id = user.id", array("username", "name", "address", "role", "bank_account","phone"))
		->where("DATE_FORMAT(`register_time`, '%Y') = ?", $year)
		->where("DATE_FORMAT(`register_time`, '%m') = ?", $month)
		->group("teacher_id");
		return $this->getAdapter()->fetchAll($query);
	
	}
}
?>