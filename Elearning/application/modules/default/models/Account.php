<?php
class Default_Model_Account extends Zend_Db_Table_Abstract {
	protected $_name = "user";
	protected $_primary = "id";
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
			echo "Exits";
			return true;
		}
		else {
			echo "Not Exits";
			return false;
		}
	}

	public function isValid($username, $password, $role){
		$query = $this->select()
		->from($this->_name, array('username','password','role'))
		->where('username=?',
				$username)
				->where('password=?',
						$password)
						->where('role=?',$role);
		$result = $this->getAdapter()->fetchAll($query);
		if($result){
			return true;
		}
		else{
			return false;
		}
	}

	public function checkIpValid($username, $curr_ip){
		$query = $this->select()
		->from($this->_name, array('username','last_login_ip'))
		->where('username=?',
				$username)
				->where("last_login_ip=$curr_ip OR last_login_ip IS NULL");
		$result = $this->getAdapter()->fetchAll($query);
		if($result)
			return true;
		else return false;
	}

	public function incLoginFailure($username){
		$data = array(
				'fail_login_count'      => "'fail_login_count'+1"
		);
		$where = "username='$username'";
		$this->update($data,$where);
	}
}