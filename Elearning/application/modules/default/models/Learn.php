<?php

class Default_Model_Learn extends Zend_Db_Table_Abstract {

    protected $_name = "learn";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    public function countStudenJoinLesson($lessonId) {
        return $this->getAdapter()->fetchOne("SELECT COUNT(*) AS count FROM learn WHERE lesson_id=$lessonId");
    }

}
