<?php
class Default_Model_LessonTag extends Zend_Db_Table_Abstract {
	protected $_name = "lesson_tag";
	protected $_primary = "id";
	protected $db;

	public function __construct(){
		parent::__construct();
		$this->db = Zend_Registry::get('connectDB');
	}
        
        public function getTagsByLesson($lessonId) {
            $select = $this->getAdapter()->select();
            $select->from(array('a'=>'lesson_tag'))
                ->join(array('b'=>'tag'), 'a.tag_id = b.id')
                ->where("lesson_id=$lessonId");
            return $this->getAdapter()->fetchAll($select);
        }
        
}