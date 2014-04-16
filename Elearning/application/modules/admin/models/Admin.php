<?php

require_once '/../../default/controllers/Code.php';

class Admin_Model_Admin extends Zend_Db_Table_Abstract {

    protected $_name = "admin";
    protected $_primary = "id";
    protected $db;
    
    public static $USERNAME = "username";
    public static $PASSWORD = "password";
    public static $CREATED_ADMIN = "create_admin";
    public static $ID = "id";
    public static $STATUS = "status";
    
    public static $ADMIN_STATUS_AVAILABLE = 1;
    public static $ADMIN_STATUS_DELETED = 2;
    
    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }
    
    /**
     * 
     * @param type $createdAdmin
     */
    public function createAdmin($createdAdmin, $username, $password) {
        $this->insert(array(
            self::$USERNAME => $username,
            self::$PASSWORD => $password,
            self::$CREATED_ADMIN => $createdAdmin,
            self::$STATUS => self::$ADMIN_STATUS_AVAILABLE
        ));
    }
    
    public function getAdminByUsername($username) {
        $query = $this->select()
                ->from($this->_name, "*")
                ->where(self::$USERNAME."=?",$username);
        $result = $this->getAdapter()->fetchAll($query);
        if ($result == null) {
            return null;
        } else {
            return $result[0];
        }
    }
    
    public function getAdminById($userId) {
        $sql = "SELECT admin.id, admin.username, admin2.username as created
                FROM admin, (SELECT id, username FROM admin) AS admin2
                WHERE admin.create_admin = admin2.id
                AND admin.id = '".$userId."'";
        $result = $this->db->query($sql)->fetchAll();
        if ($result == null) {
            return null;
        } else {
            return $result[0];
        }
    }
    
    public function getAllowedIp($userId) {
        $sql = "SELECT * FROM admin_ip WHERE admin_id=".$userId;
        $result = $this->db->query($sql)->fetchALl();
        
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }
    
    /**
     * 管理者を削除する
     * 
     * @param int $userId
     * @return boolean
     */
    public function deleteUser($userId) {
        try {
            $this->delete("id='".$userId."'");
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    
    /**
     * このIP でログインできるかをチェック
     * 
     * @param string $username
     * @param string $ip
     * @return boolean
     */
    public function isAllowedIp($userId, $ip) {
        $sql = "SELECT * FROM admin, admin_ip WHERE "
                . "admin.id='".$userId."' AND "
                . "admin.id=admin_ip.admin_id AND "
                . "ip='".$ip."'";
        $result = $this->db->query($sql)->fetchALl();
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * ログインできるIPを追加
     * 
     * @param id $userId
     * @param string $ip
     */
    public function addIp($userId, $ip) {
        $sql = "INSERT INTO admin_ip VALUES(NULL, '$userId','$ip')";
        $this->db->query($sql);
    }
    
}