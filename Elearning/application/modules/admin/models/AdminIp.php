<?php

require_once '/../../default/controllers/Code.php';

class Admin_Model_AdminIp extends Zend_Db_Table_Abstract {

    protected $_name = "admin_ip";
    protected $_primary = "id";
    protected $db;
    
    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }
    
    /**
     * IPを削除する処理
     * 
     * @param int $id
     */
    public function deleteIp($id) {
        $this->delete("id=".$id);
    }
    
}