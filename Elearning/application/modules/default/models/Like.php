<?php

require_once '/../controllers/Code.php';

class Default_Model_Like extends Zend_Db_Table_Abstract {

    protected $_name = "like";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }
	public function liked($u, $lesson){
		$q = $this->select()
			->from($this->_name)
			->where("user_id = ?", $u )
			->where("lesson_id = ?" , $lesson);
		return $this->getAdapter()->fetchAll($q);
	}
  
}
