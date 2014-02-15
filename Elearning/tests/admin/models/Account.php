<?php
class Admin_Model_Account extends Zend_Db_Table_Abstract {
	protected $_name = "account";
	protected $_primary = "ac_id";
	public function listall() {
		$this->fetchAll ()->toArray ();
	}
}