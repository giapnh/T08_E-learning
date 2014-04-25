<?php

require_once '/../../default/controllers/code.php';

class Admin_Model_Account extends Zend_Db_Table_Abstract {

    protected $_name = "admin";
    protected $_primary = "id";
    protected $db;
    public static $username = "username";
    public static $password = "password";
    public static $id = "id";

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    /**
     * ユーザが存在しているかどうかをチェック
     * 
     * @param string $username ユーザ名
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
     * 管理者のユーザ名とパースワードがデータベースに会っているかをチェック
     * 
     * @param string $username  ユーザ名
     * @param string $password  パースワード
     * @return boolean
     */
    public function isValid($username, $password) {
        $master = new Default_Model_Master();
        $passwordConst = $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST);
        $query = $this->select()
                ->from($this->_name, array('username', 'password'))
                ->where('username=?', $username)
                ->where('password=?', sha1(md5($username . '+' . $password . '+' . $passwordConst)));
//        var_dump(sha1(md5($username . '+' . $password . '+' . $passwordConst)));
        $result = $this->getAdapter()->fetchAll($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * このIP でログインできるかをチェック
     * 
     * @param string $username
     * @param string $ip
     * @return boolean
     */
    public function isAllowedIp($username, $ip) {
        $query = $this->select()
                ->from(array('a' => $this->_name))
                ->join(array('b' => 'admin_ip'), 'a.id = b.admin_id')
                ->where('username=?', $username)
                ->where('ip=?', $ip);

        $sql = "SELECT * FROM admin, admin_ip WHERE "
                . "admin.username='" . $username . "' AND "
                . "admin.id=admin_ip.admin_id AND "
                . "ip='" . $ip . "'";
        $result = $this->db->query($sql)->fetchALl();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $username
     */
    public function getAdminInfo($username) {
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
     * 管理者リストを取る
     * 
     * @param type $username
     * @return array 管理者リスト
     */
    public function getAllAdmin($username) {
        $sql = "SELECT admin.id, admin.username, admin2.username as created
                FROM admin LEFT JOIN (SELECT id, username FROM admin) AS admin2
                ON admin.create_admin = admin2.id
                WHERE admin.username != '" . $username . "'";
        $result = $this->db->query($sql);
        if ($result) {
            $admins = $result->fetchAll();
            return $admins;
        } else {
            return NULL;
        }
    }

    /**
     * パースワードを更新する
     * 
     * @param type $username
     * @param type $password
     */
    public function changePassword($username, $password) {
        $master = new Default_Model_Master();
        $passwordConst = $master->getMasterValue(Default_Model_Master::$KEY_PASSWORD_CONST);
        $update_data = array(
            'password' => sha1(md5($username . '+' . $password . '+' . $passwordConst)));
        $where = "username='$username'";
        $this->update($update_data, $where);
    }

}
