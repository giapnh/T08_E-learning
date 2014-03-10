<?php

class Default_Model_TeacherLesson extends Zend_Db_Table_Abstract {
    
    protected $_name = "lesson";
    protected $_primary = "id";
    protected $db;
    
    public static $TEACHER_ID = "teacher_id";
    public static $TITLE = "title";
    public static $DESCRIPTION = "description";
    public static $STATUS = "status";
    public static $LESSON_STATUS_AVAILABLE = 1;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }
    
    
    /**
     * 授業作成
     * 
     * @param int $teacherId
     * @param string $title
     * @param string $description
     * @return int
     */
    public function createLesson($teacherId, $title, $description, $tags) {
        $insertData = array(
                self::$TEACHER_ID => $teacherId,
                self::$TITLE => $title,
                self::$DESCRIPTION => $description,
                self::$STATUS => self::$LESSON_STATUS_AVAILABLE
            );
        $lessonId = $this->insert($insertData);
        $tagModel = new Default_Model_Tag();
        $lessonTagModel = new Default_Model_LessonTag();
        
        foreach ($tags as $tag) {
            $tagId = $tagModel->isExistTag($tag);
            if (!$tagId) {
                $tagId = $tagModel->insert(array("tag_name"=>$tag));
            }
            $lessonTagModel->insert(array("lesson_id"=>$lessonId, "tag_id"=>$tagId));
        }
        
        return $lessonId;
    }
    
}