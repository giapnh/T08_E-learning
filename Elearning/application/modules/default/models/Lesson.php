<?php

class Default_Model_Lesson extends Zend_Db_Table_Abstract {

    protected $_name = "lesson";
    protected $_primary = "id";
    protected $db;
    protected $_lessonDeadline;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
        $master = new Default_Model_Master();
        $this->_lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
    }
	public function deleteLessonById($lessonId){
		$sql = "DELETE lesson, lesson_file, lesson_tag, comment, file_comment, lesson_like, lesson_report, copyright_report
				FROM lesson
				LEFT JOIN lesson_file ON lesson.id = lesson_file.lesson_id
				LEFT JOIN lesson_tag ON lesson.id = lesson_tag.lesson_id
				LEFT JOIN comment ON lesson.id = comment.lesson_id
				LEFT JOIN file_comment ON lesson_file.id = file_comment.file_id
				LEFT JOIN lesson_like ON lesson.id = lesson_like.lesson_id
				LEFT JOIN lesson_report ON lesson.id = lesson_report.lesson_id
				LEFT JOIN copyright_report ON lesson_file.id = copyright_report.file_id 
				WHERE lesson.id = ". $lessonId;
		$this->getAdapter()->query($sql);
		$tagModel = new Default_Model_Tag();
		$tagModel->cleanUnuseTags();
		//delete file on disk
		$path = APPLICATION_PATH. "\..\\files\\".$lessonId;
		if(is_dir($path)){
			$this->__removeDir($path);
		}
	}
	protected function __removeDir($path) {

    // Normalise $path.
    $path = rtrim($path, '/') . '/';

    // Remove all child files and directories.
    $items = glob($path . '*');

    foreach($items as $item) {
        is_dir($item) ? removeDir($item) : unlink($item);
    }

    // Remove directory.
    rmdir($path);
}
	
    public function listAll($type = 0, $asc = 0) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->join('user', 'lesson.teacher_id=user.id', array('name'));
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('lesson.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('lesson.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('lesson.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('lesson.num_like' . ' ' . $asc_str);
        }
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        return $result;
    }

    public function listWithTag($tag, $type = 0, $asc = 0) {
        if ($tag == 0) {
            return $this->listAll();
        }
        
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->joinInner(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id', array('lesson_tag.id' => 'tag_id'))
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where("lt.tag_id=$tag");
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('l.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('l.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('l.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('l.num_like' . ' ' . $asc_str);
        }
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        return $result;
    }

    public function listWithTagByTeacher($tag, $teacher_id, $type = 0, $asc = 0) {
        if ($tag == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->joinInner(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id', array('lesson_tag.id' => 'tag_id'))
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where('user.id=?', $teacher_id)
                ->where("lt.tag_id=$tag");
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('l.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('l.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('l.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('l.num_like' . ' ' . $asc_str);
        }
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        return $result;
    }

    public function listTeacherLessonsByTag($teacher, $tag, $type = 0, $asc = 0) {
        $select = $this->getAdapter()->select();
        if ($tag == 0) {
            $select->from($this->_name)
                    ->where("teacher_id=$teacher");
        } else {
            $select->from(array('a' => 'lesson'))
                    ->join(array('b' => 'lesson_tag'), 'a.id = b.lesson_id')
                    ->where("b.id=$tag and a.teacher_id=$teacher");
        }

        return $this->getAdapter()->fetchAll($select);
    }

    public function listWithTeacher($teacher, $type = 0, $asc = 0) {
        if ($teacher == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where("l.teacher_id=$teacher");
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('l.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('l.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('l.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('l.num_like' . ' ' . $asc_str);
        }
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        return $result;
    }

    public function listAllByStudent($student,  $type = 0, $asc = 0) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                ->joinInner('learn', 'learn.lesson_id=lesson.id', array('status'))
                ->where("learn.student_id=$student")
                ->where("learn.register_time + INTERVAL " . $this->_lessonDeadline . " DAY >= NOW()");
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('l.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('l.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('l.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('l.num_like' . ' ' . $asc_str);
        }
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * 「Copyright」レポートがある違犯の授業を取る
     * 
     * @return array $result
     */
    public function listCopyrightFalse($type = 0, $asc = 0) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->join('user', 'lesson.teacher_id=user.id', array('name'));
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('lesson.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('lesson.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('lesson.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('lesson.num_like' . ' ' . $asc_str);
        }
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            if ($this->isReported($lesson)) {
                $result[$index]['is_reported'] = true;
            } else {
                unset($result[$index]);
            }
        }
        return $result;
    }

    public function findLessonWithTagByStudent($tagId, $studentId,  $type = 0, $asc = 0) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id', NULL)
                ->where('lesson_tag.tag_id=?', $tagId)
                ->joinInner('learn', 'learn.lesson_id=lesson_tag.lesson_id', array('status'))
                ->joinInner('user', 'user.id=lesson.teacher_id', array('name'))
                ->where('learn.student_id=?', $studentId)
                ->where("learn.register_time + INTERVAL " . $this->_lessonDeadline . " DAY >= NOW()");
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('l.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('l.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('l.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('l.num_like' . ' ' . $asc_str);
        }
        return $this->getAdapter()->fetchAll($select);
    }

    public function findLessonWithTeacherByStudent($teacher, $studentId,  $type = 0, $asc = 0) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('learn', 'lesson.id=learn.lesson_id', array('status'))
                ->where('lesson.teacher_id=?', $teacher)
                ->where('learn.student_id=?', $studentId)
                ->where("learn.register_time + INTERVAL " . $this->_lessonDeadline . " DAY >= NOW()")
                ->joinInner('user', 'user.id=lesson.teacher_id', array("name"));
        $asc_str = "";
        if ($asc == 0) {
            $asc_str = $asc_str . "ASC";
        } else if ($asc == 1) {
            $asc_str = $asc_str . "DESC";
        }
        if ($type == 0) {//Title
            $select->order('l.title' . ' ' . $asc_str);
        } else if ($type == 1) {// time
            $select->order('l.create_time' . ' ' . $asc_str);
        } else if ($type == 2) {
            $select->order('l.view' . ' ' . $asc_str);
        } else if ($type == 3) {
            $select->order('l.num_like' . ' ' . $asc_str);
        }
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * 授業をIDで取る
     * 
     * @param type $id
     */
    public function findLessonById($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->where('lesson.id = ?', $lessonId)
                ->joinInner('user', 'lesson.teacher_id = user.id', array('name'));
        $result = $this->getAdapter()->fetchRow($select);
        if ($result) {
            $result['is_reported'] = $this->isReported($result);
            return $result;
        } else {
            return null;
        }
    }

    public function incrementView($lessonId) {
        $view = $this->fetchRow("id='$lessonId'");
        $view = $view['view'];
        $update = array(
            'view' => $view + 1
        );
        $where = "id='$lessonId'";
        $this->update($update, $where);
    }

    /**
     * 検索機能：先生名、タイトル、。。。 
     * @param type $keyword
     * @return type
     */
    public function findByKeyword($keyword, $type, $asc, $userId = null,$studentId = null) {
        // Check if have "+" or "-" charecter
        if (strpos($keyword, '+')) {
            $subKey = explode('+', $keyword);
            $select = $this->getAdapter()->select();
            $select->from('lesson')
                    ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                    ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id', array())
                    ->joinInner('tag', 'lesson_tag.tag_id=tag.id', array('tag_name'));
            $nameWhere = "";
            for ($i = 0; $i < count($subKey); $i++) {
                $nameWhere = $nameWhere . "name LIKE '%$subKey[$i]%'";
                if ($i != count($subKey) - 1) {
                    $nameWhere = $nameWhere . " AND ";
                }
            }
            $select->orWhere($nameWhere);
            $select->orWhere("tag_name  LIKE '%" . trim($keyword) . "%'")
                    ->orWhere("title LIKE '%" . trim($keyword) . "%'")
                    ->orWhere("description LIKE '%" . trim($keyword) . "%'")
                    ->group('lesson.id');
            $asc_str = "";
            if ($asc == 0) {
                $asc_str = $asc_str . "ASC";
            } else if ($asc == 1) {
                $asc_str = $asc_str . "DESC";
            }
            if ($type == 0) {//Title
                $select->order('lesson.title' . ' ' . $asc_str);
            } else if ($type == 1) {// time
                $select->order('lesson.create_time' . ' ' . $asc_str);
            } else if ($type == 2) {
                $select->order('lesson.view' . ' ' . $asc_str);
            } else if ($type == 3) {
                $select->order('lesson.num_like' . ' ' . $asc_str);
            }
        } else if (strpos($keyword, "-")) {
            $subKey = explode("-", $keyword);
            $select = $this->getAdapter()->select();
            $select->from('lesson')
                    ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                    ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id', array())
                    ->joinInner('tag', 'lesson_tag.tag_id=tag.id', array('tag_name'));
            for ($i = 0; $i < count($subKey); $i++) {
                $select->orWhere("name LIKE '%" . trim($subKey[$i]) . "%'");
            }
            $select->orWhere("tag_name  LIKE '%$keyword%'")
                    ->orWhere("title LIKE '%$keyword%'")
                    ->orWhere("description LIKE '%$keyword%'")
                    ->group('lesson.id');
            $asc_str = "";
            if ($asc == 0) {
                $asc_str = $asc_str . "ASC";
            } else if ($asc == 1) {
                $asc_str = $asc_str . "DESC";
            }
            if ($type == 0) {//Title
                $select->order('lesson.title' . ' ' . $asc_str);
            } else if ($type == 1) {// time
                $select->order('lesson.create_time' . ' ' . $asc_str);
            } else if ($type == 2) {
                $select->order('lesson.view' . ' ' . $asc_str);
            } else if ($type == 3) {
                $select->order('lesson.num_like' . ' ' . $asc_str);
            }
        } else {
            $select = $this->getAdapter()->select();
            //$keyword = utf8_encode($keyword);
            $select->from('lesson')
                    ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                    ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id', array())
                    ->joinInner('tag', 'lesson_tag.tag_id=tag.id', array('tag_name'))
                    ->where("name LIKE '%$keyword%'")
                    ->orWhere("tag_name  LIKE '%$keyword%'")
                    ->orWhere("title LIKE '%$keyword%'")
                    ->orWhere("description LIKE '%$keyword%'")
                    ->group('lesson.id');
            $asc_str = "";
            if ($asc == 0) {
                $asc_str = $asc_str . "ASC";
            } else if ($asc == 1) {
                $asc_str = $asc_str . "DESC";
            }
            if ($type == 0) {//Title
                $select->order('lesson.title' . ' ' . $asc_str);
            } else if ($type == 1) {// time
                $select->order('lesson.create_time' . ' ' . $asc_str);
            } else if ($type == 2) {
                $select->order('lesson.view' . ' ' . $asc_str);
            } else if ($type == 3) {
                $select->order('lesson.num_like' . ' ' . $asc_str);
            }
            
        }
        if($userId)
        	$select->having("lesson.teacher_id = $userId");
        
        $result =  $this->getAdapter()->fetchAll($select);  
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        if($studentId){
        	$modelLearn = new Default_Model_Learn();
        	foreach ($result as $k => $r){
        		if( $modelLearn->isStudentLearn($studentId, $r["id"]))
        			unset($result[$k]);
        	}
        }
       	return $result;
    }

    /**
     * この授業は先生の授業かをチェック
     * 
     * @param int $teacherId
     * @param int $lessonId
     * @return boolean
     */
    public function isLessonOwner($teacherId, $lessonId) {
        $lesson = $this->findLessonById($lessonId);
        return ($teacherId == $lesson['teacher_id']);
    }

    /**
     * この授業のファイルのなかに、「Copyright」レポートがあるかどうかをチェック
     * 
     * @param array $lesson
     * @return boolean
     */
    public function isReported($lesson) {
        $lessonReportModel = new Default_Model_LessonReport();
        if (!$lesson) {
            return false;
        }
        $select = $this->getAdapter()->select();
        $select->from(array('f' => 'lesson_file'))
                ->joinInner(array('c' => 'copyright_report'), 'f.id = c.file_id')
                ->where("f.lesson_id=" . $lesson['id'] . " AND c.status=1");
        $result = $this->getAdapter()->fetchAll($select);
        if ($result) {
            return true;
        }
        if ($lessonReportModel->isReported($lesson['id'])) {
            return TRUE;
        }
        return false;
    }

    /**
     * 授業をロックする
     * 
     * @param int $lessonId
     */
    public function lockLesson($lessonId) {
        $userModel = new Default_Model_Account();
        $this->update(array("status" => 0), "id=" . $lessonId);
    }

    /**
     * 授業をアンロックする
     * 
     * @param int $lessonId
     */
    public function unlockLesson($lessonId) {
        $this->update(array("status" => 1), "id=" . $lessonId);
    }

}
