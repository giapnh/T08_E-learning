<?php

require '/../controllers/Code.php';

class Default_Model_Account extends Zend_Db_Table_Abstract {

    protected $_name = "user";
    protected $_primary = "id";
    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    public function isExits($username) {
        $query = $this->select()
                ->from($this->_name, array('username'))
                ->where('username=?', $username);
        $result = $this->getAdapter()->fetchAll($query);
        if ($result) {
            echo "Exits";
            return true;
        } else {
            echo "Not Exits";
            return false;
        }
    }

    public function isValid($username, $password, $role) {
        $query = $this->select()
                ->from($this->_name, array('username', 'password', 'role'))
                ->where('username=?', $username)
                ->where('password=?', md5($username . '+' . $password . '+' . Code::$PASSWORD_CONST))
                ->where('role=?', $role);
        $result = $this->getAdapter()->fetchAll($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

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

    public function incLoginFailure($username) {
        $data = array(
            'fail_login_count' => "'fail_login_count'+1"
        );
        $where = "username='$username'";
        $this->update($data, $where);
    }

    /**
     * Get user information
     * @param type $username
     * @return null
     */
    public function getUserInfo($username) {
        $query = $this->select()
                ->from($this->_name, array('username', 'password', 'name',))
                ->where('username=?', $username);
        $result = $this->getAdapter()->fetchRow($query);
        if ($result) {
            return $result;
        } else {
            return NULL;
        }
    }

    /**
     * Add new registed user
     * @param type $data User information to save to database
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

}
