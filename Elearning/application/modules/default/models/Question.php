<?php

require_once '/../controllers/Code.php';

class Default_Model_Question extends Zend_Db_Table_Abstract {
    protected $_name = "question";
    protected $_primary = "id";
    protected $db;
    
    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }
    
    /**
     * 
     * 
     * @param int $fileId ファイルのID
     * @param type $question
     *   'index' => int 1
         'question' => string 'Meeting Question 1 content' (length=26)
         'answers' => 
           array (size=6)
             0 => string 'Answer 1' (length=8)
             1 => string 'Answer 2' (length=8)
             2 => string 'Answer 3' (length=8)
             3 => string 'Answer 4' (length=8)
             4 => string 'Answer 5' (length=8)
             5 => string 'Answer 6' (length=8)
         'trueAnswer' => int 1
         'point' => string '10'
     */
    public function createQuestion($fileId, $question) {
        return $this->insert(array(
            "file_id" => $fileId,
            "title" => "Q".$question['index'],
            "answer" => "S".$question['trueAnswer'],
            "point" => $question['point']
        ));
    }
    
}