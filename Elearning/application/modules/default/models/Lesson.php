<?php

class Default_Model_Lesson extends Zend_Db_Table_Abstract {

    protected $_name = "lesson";
    protected $_primary = "id";
    protected $db;
    protected $lessonDeadline;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
        $master = new Default_Model_Master();
        $this->_lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
    }

    public function listAll() {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->join('user', 'lesson.teacher_id=user.id', array('name'));
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        return $result;
    }

    public function listWithTag($tag) {
        if ($tag == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->joinInner(array('lt' => 'lesson_tag'), 'l.id = lt.lesson_id', array('lesson_tag.id' => 'tag_id'))
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where("lt.tag_id=$tag");
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        return $result;
    }

    public function listTeacherLessonsByTag($teacher, $tag) {
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

    public function listWithTeacher($teacher) {
        if ($teacher == 0) {
            return $this->listAll();
        }
        $select = $this->getAdapter()->select();
        $select->from(array('l' => 'lesson'))
                ->joinInner('user', 'l.teacher_id=user.id', array('name'))
                ->where("l.teacher_id=$teacher");
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $lesson) {
            $result[$index]['is_reported'] = $this->isReported($lesson);
        }
        return $result;
    }

    public function listAllByStudent($student) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                ->joinInner('learn', 'learn.lesson_id=lesson.id', array('status'))
                ->where("learn.student_id=$student")
                ->where("learn.register_time + INTERVAL " . $this->_lessonDeadline . " DAY >= NOW()");
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * 「Copyright」レポートがある違犯の授業を取る
     * 
     * @return array $result
     */
    public function listCopyrightFalse() {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->join('user', 'lesson.teacher_id=user.id', array('name'));
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

    public function findLessonWithTagByStudent($tagId, $studentId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id', NULL)
                ->where('lesson_tag.tag_id=?', $tagId)
                ->joinInner('learn', 'learn.lesson_id=lesson_tag.lesson_id', array('status'))
                ->joinInner('user', 'user.id=lesson.teacher_id', array('name'))
                ->where('learn.student_id=?', $studentId)
                ->where("learn.register_time + INTERVAL " . $this->_lessonDeadline . " DAY >= NOW()");
        return $this->getAdapter()->fetchAll($select);
    }

    public function findLessonWithTeacherByStudent($teacher, $studentId) {
        $select = $this->getAdapter()->select();
        $select->from('lesson')
                ->joinInner('learn', 'lesson.id=learn.lesson_id', array('status'))
                ->where('lesson.teacher_id=?', $teacher)
                ->where('learn.student_id=?', $studentId)
                ->where("learn.register_time + INTERVAL " . $this->_lessonDeadline . " DAY >= NOW()")
                ->joinInner('user', 'user.id=lesson.teacher_id', array("name"));
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
        $result['is_reported'] = $this->isReported($result);
        return $result;
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
    public function findByKeyword($keyword, $type, $asc) {
        // Check if have "+" or "-" charecter
        if (strpos($keyword, '+')) {
            $subKey = explode('+', $keyword);
            $select = $this->getAdapter()->select();
            //$keyword = utf8_encode($keyword);
            $select->from('lesson')
                    ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                    ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id')
                    ->joinInner('tag', 'lesson_tag.tag_id=tag.id', array('tag_name'));
            $nameWhere = "";
            for ($i = 0; $i < count($subKey); $i++) {
                $nameWhere = $nameWhere . "name LIKE '%$subKey[$i]%'";
                if ($i != count($subKey) - 1) {
                    $nameWhere = $nameWhere . " AND ";
                }
            }
            var_dump($nameWhere);
            die;
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
                $select->order('lesson.like' . ' ' . $asc_str);
            }
            return $this->getAdapter()->fetchAll($select);
        } else if (strpos($keyword, "-")) {
            $select = $this->getAdapter()->select();
            //$keyword = utf8_encode($keyword);
            $select->from('lesson')
                    ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                    ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id')
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
                $select->order('lesson.like' . ' ' . $asc_str);
            }
            return $this->getAdapter()->fetchAll($select);
        } else {
            $select = $this->getAdapter()->select();
            //$keyword = utf8_encode($keyword);
            $select->from('lesson')
                    ->joinInner('user', 'lesson.teacher_id=user.id', array('name'))
                    ->joinInner('lesson_tag', 'lesson.id=lesson_tag.lesson_id')
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
                $select->order('lesson.like' . ' ' . $asc_str);
            }
            return $this->getAdapter()->fetchAll($select);
        }
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
        $select = $this->getAdapter()->select();
        $select->from(array('f' => 'lesson_file'))
                ->joinInner(array('c' => 'copyright_report'), 'f.id = c.file_id')
                ->where("f.lesson_id=" . $lesson['id'] . " AND c.status=1");
        $result = $this->getAdapter()->fetchAll($select);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 授業をロックする
     * 
     * @param int $lessonId
     */
    public function lockLesson($lessonId) {
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
