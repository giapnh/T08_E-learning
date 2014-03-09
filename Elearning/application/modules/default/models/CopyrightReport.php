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
            'reason' => $report
        );
        $this->insert($ins);
    }

}
