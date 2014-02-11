<?php
class Model_Account extends Zend_Db_Table_Abstract {
	protected $_name = "account";
	protected $_primary = "ac_id";
	/**
	 * check username is exited
	 *
	 * @param unknown_type $username        	
	 * @return boolean
	 */
	public function isExits($username) {
		$query = $this->select ();
		$query->from ( 'account', array (
				'ac_username' 
		) );
		$query->where ( 'ac_username = ?', $username );
		$data = $this->fetchAll ( $query )->count ();
		if ($data > 0) {
			return true;
		} else
			return false;
	}
}