<?php

require_once '/../controllers/Code.php';

class Default_Model_Result extends Zend_Db_Table_Abstract {
    protected $_name = "result";
    protected $_primary = "id";
    protected $db;
    
    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }
    
    public function findResultByQuestionAndLearn($learnId, $questionId) {
        $query = $this->select()
                ->where("learn_id='".$learnId."' and question_id='".$questionId."'");
        $result = $this->getAdapter()->fetchAll($query);
        if (isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }
    
    public function updateResult($learnId, $questionId, $selected) {
        $currentResult = $this->findResultByQuestionAndLearn($learnId, $questionId);
        if ($currentResult) {
            $this->update(array(
                "selected"=>$selected
            ), "id=".$currentResult['id']);
        } else {
            $this->insert(array(
                "learn_id"=>$learnId,
                "question_id" => $questionId,
                "selected"=>$selected
            ));
        }
    }
    
}