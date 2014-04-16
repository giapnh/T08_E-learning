<?php

class Default_Model_FileComment extends Zend_Db_Table_Abstract {

    protected $_name = "file_comment";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    public function getAllCommentOfFile($fileId) {
        $select = $this->getAdapter()->select();
        $select->from(array('fcmt' => 'file_comment'))
                ->joinInner('user', 'fcmt.user_id=user.id', array('name', 'role'))
                ->where("fcmt.file_id='$fileId'");
        return $this->getAdapter()->fetchAll($select);
    }

    public function addComment($fileId, $studentId, $comment) {
        $data = array(
            'user_id' => $studentId,
            'file_id' => $fileId,
            'comment' => $comment
        );
        $id = $this->insert($data);
        return $this->getById($id);
    }

    protected function getById($id) {
        $select = $this->getAdapter()->select();
        $select->from(array('cmt' => $this->_name))
                ->joinInner('user', 'cmt.user_id=user.id', array('name', 'role'))
                ->where("cmt.id='$id'");
        $result = $this->getAdapter()->fetchAll($select);
        return $result[0];
    }
}
