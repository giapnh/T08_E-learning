<?php
class Default_Model_LessonFile extends Zend_Db_Table_Abstract {
	protected $_name = "lesson_file";
	protected $_primary = "id";
	protected $db;

	public function __construct(){
		parent::__construct();
		$this->db = Zend_Registry::get('connectDB');
	}
}