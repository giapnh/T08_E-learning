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
        $query = $this->select()
                ->from($this->_name, array('username', 'password', 'role'))
                ->where('username=?', $username)
                ->where('password=?', md5($username . '+' . $password . '+' . Code::$PASSWORD_CONST))
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
                ->where("last_login_ip='$curr_ip' OR last_login_ip IS NULL");
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
        return $this->fetchRow("username='$username'")['fail_login_count'];
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
                ->where('username=?', $username);
        $result = $this->getAdapter()->fetchRow($query);
        if ($result) {
            return $result;
        } else {
            return NULL;
        }
    }

    /**
     * 先生リストが取る
     * @return list 先生リスト
     */
    public function listTeacher() {
        $query = $this->select()
                ->from($this->_name, "*")
                ->where('status=?', "1")
                ->where('role=?', "2");
        return $this->getAdapter()->fetchAll($query);
    }

    /**
     * 学生リストが取る
     * @return list 学生リスト
     */
    public function listStudent() {
        $query = $this->select()
                ->from($this->_name, "username")
                ->where('role=?', "1");
        return $this->getAdapter()->fetchAll($query)->toArray();
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

    /**
     * 新しいユーザが追加機能
     * @param type $data ユーザの情報
     */
    public function insertNew($data) {
        $ins_data = array(
            'username' => $data['username'],
            'first_password' => md5($data['username'] . '+' . $data['password'] . '+' . Code::$PASSWORD_CONST),
            'password' => md5($data['username'] . '+' . $data['password'] . '+' . Code::$PASSWORD_CONST),
            'name' => $data['fullname'],
            'birthday' => $data['day'] . '-' . $data['month'] . '-' . $data['year'],
            'address' => $data['address'],
            'sex' => $data['sex'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'bank_account' => $data['bank_acc'],
            'first_secret_question' => $data['secret_question'],
            'secret_question' => $data['secret_question'],
            'first_secret_answer' => $data['secret_answer'],
            'secret_answer' => $data['secret_answer'],
            'role' => $data['role'],
            'status' => 2//Wait for confirm by admin
        );
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

    /**
     * ユーザのパスワードが変更機能アクション
     * @param type $data
     */
    public function updatePassword($data) {
        $update_data = array(
            'password' => md5($data['username'] . '+' . $data['new_password'] . '+' . Code::$PASSWORD_CONST));
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
            'secret_question' => $data['secret_question'],
            'secret_answer' => $data['secret_answer']
        );
        $username = $data['username'];
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

    public function listTeacherByStudent($student) {
        $select = $this->getAdapter()->select();
        $select->from('user')
                ->joinInner('lesson', 'lesson.teacher_id=user.id', NULL)
                ->joinInner('learn', 'learn.lesson_id=lesson.id', NULL)
                ->where('learn.student_id=?', $student)
                ->group('user.id');
        return $this->getAdapter()->fetchAll($select);
    }

}
