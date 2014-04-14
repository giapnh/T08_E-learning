<?php

class Default_Model_CopyrightReport extends Zend_Db_Table_Abstract {

    protected $_name = "copyright_report";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    public function addReport($userId, $fileId, $report) {
        $ins = array(
            'user_id' => $userId,
            'file_id' => $fileId,
            'reason' => $report,
        	'status' => 1
        );
        $this->insert($ins);
    }
    
    public function getReport($fileId){
            $select = $this->getAdapter()->select()
                    ->from($this->_name)
                    ->join("user", "copyright_report.user_id = user.id", array("username"))
                    ->where("copyright_report.status = 1");
            return $this->getAdapter()->fetchAll($select);
    }
    
    /**
     * 「Copyright」レポートを数える
     *  
     * @return int
     */
    public function countAllReport() {
        $select = $this->getAdapter()->select()
                    ->from($this->_name)
                    ->where("copyright_report.status = 1");
        return count($this->getAdapter()->fetchAll($select));
    }
}
