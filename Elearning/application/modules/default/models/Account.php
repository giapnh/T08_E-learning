<?php

require_once '/../controllers/Code.php';

class Default_Model_Account extends Zend_Db_Table_Abstract {

    protected $_name = "user";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    /**
     * ユーザネームが存在かどうチェックする
     * @param type $username
     * @return boolean
     */
    //thiennx : delete user
    public function deleteTeacher($userId) {
    	$query = "UPDATE user
    			LEFT JOIN lesson ON user.id = lesson.teacher_id
    			SET user.status = 5, lesson.status = 5
    			WHERE user.id = " . $userId;
    	$this->db->query($query);
    	
        $query = "DELETE lesson_report, lesson_file, lesson_tag,comment,lesson_like, copyright_report, result, question
    			 FROM user 
    			LEFT JOIN lesson ON user.id = lesson.teacher_id 
    			LEFT JOIN lesson_file ON lesson.id = lesson_file.lesson_id 
    			LEFT JOIN lesson_tag ON lesson.id = lesson_tag.lesson_id
    			LEFT JOIN comment ON lesson.id = comment.lesson_id
    			LEFT JOIN learn ON lesson.id = learn.lesson_id
    			LEFT JOIN lesson_like ON lesson.id = lesson_like.lesson_id
    			LEFT JOIN copyright_report ON lesson_file.id = copyright_report.file_id 
    			LEFT JOIN result ON result.learn_id = learn.id
    			LEFT JOIN question ON question.file_id = lesson_file.id
        		LEFT JOIN lesson_report ON lesson_report.lesson_id = lesson.id
    			WHERE user.id = " . $userId;
        $this->db->query($query);
        
    }

    public function deleteStudent($userId) {
    	$query = "UPDATE user
    			SET status =5
    			WHERE user.id = " . $userId;
    	$this->db->query($query);
        $query = "DELETE comment, lesson_like, copyright_report, file_comment
    			 FROM user
    			LEFT JOIN comment ON user.id = comment.user_id
    			LEFT JOIN lesson_like ON user.id = lesson_like.user_id
    			LEFT JOIN learn ON user.id = learn.student_id
    			LEFT JOIN copyright_report ON user.id = copyright_report.user_id
    			LEFT JOIN file_comment ON user.id = file_comment.user_id
    			WHERE user.id = " . $userId;
        $this->db->query($query);
    }

    public function isExits($username) {
        $query = $this->select()
                ->from($this->_name, array('username'))
                ->where('username=?', $username);
        $result = $this->getAdapter()->fetchAll($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ユーザが正しいかどうかチェッする
     * @param type $username
     * @param type $password
     * @param type $role
     * @return boolean
     */
    public function isValid($username, $password, $role) {
        $master = new Default_Model_Master();
        $query = $this->select()
                ->from($this->_name, array('username', 'password', 'role'))
                ->where('username=?', $username)
                ->where('password=?', sha1(md5($username . '+' . $password . '+' . $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST))))
                ->where('role=?', $role)
                ->where('status=?', 1); //If this account is accepted by admin
        $result = $this->getAdapter()->fetchAll($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Ip アドレスがチェックする
     * @param type $username
     * @param type $curr_ip
     * @return boolean
     */
    public function checkIpValid($username, $curr_ip) {
        $query = $this->select()
                ->from($this->_name, array('username', 'last_login_ip'))
                ->where('username=?', $username)
                ->where("last_login_ip='$curr_ip'");
        $result = $this->getAdapter()->fetchAll($query);
        if ($result)
            return true;
        else
            return false;
    }

    /**
     * ユーザがかくログインが失敗ことと、ログイン失敗数が追加するの機能
     * @param type $username
     */
    public function incLoginFailure($username) {
        $where = "username='$username'";
        $curr_count = $this->getFailCount($username);
        $data = array(
            'fail_login_count' => $curr_count + 1
        );
        $this->update($data, $where);
    }

    public function getFailCount($username) {
        $re = $this->fetchRow("username='{$username}'");
        return $re['fail_login_count'];
    }

    public function resetFailCount($username) {
        $data = array(
            'fail_login_count' => 0
        );
        $where = "username='$username'";
        $this->update($data, $where);
    }

    /**
     * ユーザの情報が取る
     * @param type $username
     * @return null
     */
    public function getUserInfo($username) {
        $query = $this->select()
                ->from($this->_name, "*")
                ->where('username=?', $username)
        		->where("status <> 5");
        $result = $this->getAdapter()->fetchRow($query);
        if ($result) {
            return $result;
        } else {
            return NULL;
        }
    }
    
    /**
     * ユーザの情報が取る
     * 
     * @param int $username
     * @return array
     */
    public function getUserById($userId) {
        $query = $this->select()
                ->from($this->_name, "*")
                ->where('id=?', $userId)
        		->where("status <> 5");
        $result = $this->getAdapter()->fetchRow($query);
        if ($result) {
            return $result;
        } else {
            return NULL;
        }
    }

    public function isValidSecretQA($username, $anwser) {
        $query = $this->select()->
                from($this->_name, "*")
                ->where('username=?', $username)
                ->where('secret_answer=?', sha1(md5($anwser)));
        $result = $this->getAdapter()->fetchRow($query);
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 先生リストが取る
     * @return list 先生リスト
     */
    public function listTeacher($filterAsc = 0) {
        if ($filterAsc == 0) {
            $asc_str = "ASC";
        } else if ($filterAsc == 1) {
            $asc_str = "DESC";
        }
        $query = $this->select()
                ->from($this->_name, "*")
                ->where('status=?', "1")
                ->where('role=?', "2")
                ->order('name' . ' ' . $asc_str);
        return $this->getAdapter()->fetchAll($query);
    }

    /**
     * 学生リストが取る
     * @return list 学生リスト
     */
    public function listStudent() {
        $query = $this->getAdapter()->select()
                ->from($this->_name, "username")
                ->where('role=?', "1")
        		->where("status <> 5");
        return $this->getAdapter()->fetchAll($query);
    }
	public function listStudentForLock($lessonId, $search = null){
		$query = $this->getAdapter()->select()
                ->from($this->_name, array("username", "name", 's_id' => "id"))
                ->joinLeft("lesson_lock", "user.id = lesson_lock.student_id AND lesson_id = ". $lessonId, array("locked" => "status"))
                ->where('role=?', "1")
                //->where("lesson_id = ". $lessonId ." OR lesson_id is null")
        		->where("user.status <> 5");
		if($search){
			$query->where("username like '%$search%' OR name like '%$search%'");
		}
	        return $this->getAdapter()->fetchAll($query);
	}
    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    /*
     * ユーザがロック状態にさせます
     */

    public function lock($username) {
        $update_data = array(
            'status' => 3
        );
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

    public function updateLastLoginTime($uname) {
        $update_data = array(
            'last_login_time' => time()
        );
        $where = "username='$uname'";
        $this->update($update_data, $where);
    }

    /*
     * ユーザが発動状態にさせます
     */

    public function unlock($username) {
        $update_data = array(
            'fail_login_count' => 0,
            'status' => 1
        );
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

    /**
     * 新しいユーザが追加機能
     * @param type $data ユーザの情報
     */
    public static function encryptIt( $q ) {
    	$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    	$qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    	return( $qEncoded );
    }
    
     public static function decryptIt( $q ) {
    	$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    	$qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    	return( $qDecoded );
    }
    public function insertNew($data) {
        $master = new Default_Model_Master();
        $role = $data['role'];
        if ($role == 1) {
            $ins_data = array(
                'username' => $data['username'],
                'first_password' => sha1(md5($data['username'] . '+' . $data['password'] . '+' . $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST))),
                'password' => sha1(md5($data['username'] . '+' . $data['password'] . '+' . $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST))),
                'name' => $data['fullname'],
                'birthday' => $data['day'] . '-' . $data['month'] . '-' . $data['year'],
                'address' => $data['address'],
                'sex' => $data['sex'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'bank_account' => $data['bank_acc'],
                'role' => $data['role'],
                'status' => 2//Wait for confirm by admin
            );
        } else if ($role == 2) {
            $ins_data = array(
                'username' => $data['username'],
                'first_password' => sha1(md5($data['username'] . '+' . $data['password'] . '+' . $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST))),
                'password' => sha1(md5($data['username'] . '+' . $data['password'] . '+' . $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST))),
                'name' => $data['fullname'],
                'birthday' => $data['day'] . '-' . $data['month'] . '-' . $data['year'],
                'address' => $data['address'],
                'sex' => $data['sex'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'bank_account' => $data['bank_acc'],
                'first_secret_question' => Default_Model_Account::encryptIt($data['secret_question']),
                'secret_question' => Default_Model_Account::encryptIt($data['secret_question']),
                'first_secret_answer' => sha1(md5($data['secret_answer'])),
                'secret_answer' => sha1(md5($data['secret_answer'])),
                'role' => $data['role'],
                'status' => 2//Wait for confirm by admin
            );
        }

        $this->insert($ins_data);
    }

    /**
     * ユーザの更新情報が更新機能
     * @param type $data ユーザの新しい情報
     */
    public function updateNewInfo($data) {
        $update_data = array(
            'name' => $data['fullname'],
            'birthday' => $data['day'] . '-' . $data['month'] . '-' . $data['year'],
            'address' => $data['address'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'bank_account' => $data['bank_acc']
        );
        $username = $data['username'];
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

    public function updateLastLoginIp($username, $newIp) {
        $update_data = array(
            'last_login_ip' => $newIp
        );
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

    /**
     * ユーザのパスワードが変更機能アクション
     * @param type $data
     */
    public function updatePassword($data) {
    	$master = new Default_Model_Master();
        $update_data = array(
            'password' => sha1(md5($data['username'] . '+' . $data['new_password'] . '+' . $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST))));
        $username = $data['username'];
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

    /**
     * ユーザの秘密質問変更機能アクション
     * @param type $data
     */
    public function updateSecretQA($data) {
        $update_data = array(
            'secret_question' => Default_Model_Account::encryptIt($data['secret_question']),
            'secret_answer' => sha1(md5($data['secret_answer']))
        );
        $username = $data['username'];
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

    public function listTeacherByStudent($student, $filterAsc=0) {
        $select = $this->getAdapter()->select();
        $select->from('user')
                ->joinInner('lesson', 'lesson.teacher_id=user.id', NULL)
                ->joinInner('learn', 'learn.lesson_id=lesson.id', NULL)
                ->where('learn.student_id=?', $student)
                ->group('user.id');
        $asc_str = "";
        if ($filterAsc == 0) {
            $asc_str = "ASC";
        } else if ($filterAsc == 1) {
            $asc_str = "DESC";
        }
        $select->order('user.name' . ' ' . $asc_str);
        return $this->getAdapter()->fetchAll($select);
    }

    public function upViolationLock($userId) {
        $sql = "UPDATE ".$this->_name." SET lock_count=lock_count+1 WHERE id=".$userId;
        $this->getAdapter()->query($sql);
    }
    
    public function downViolationLock($userId) {
        $sql = "UPDATE ".$this->_name." SET lock_count=lock_count-1 WHERE id=".$userId;
        $this->getAdapter()->query($sql);
    }
}
