<?php
class Default_Model_Account extends Zend_Db_Table_Abstract {
	protected $_name = "user";
	protected $_primary = "u_id";
	protected $db;

	public function __construct(){
		parent::__construct();
		$this->db = Zend_Registry::get('connectDB');
	}

	public function isExits($username) {
		$query = $this->select()
							->from($this->_name, array('username'))
							->where('username=?', $username);
        $result = $this->getAdapter()->fetchAll($query);
        if($result){
            return true;
        }
        else return false;
	}
    
	/*
    public function listall2(){
        $sql=$this->db->query("select * from users where status='0' order by id DESC");
        return $sql->fetchAll();
    }
    public function listall(){
        $query=$this->select();
        $query->where('status =?','0');
        return $this->fetchAll($query)->toArray();
    }
    */
}