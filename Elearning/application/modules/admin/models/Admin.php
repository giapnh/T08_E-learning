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
                ->where("username=?",$username);
        $result = $this->getAdapter()->fetchAll($query);
        if ($result == null) {
            return null;
        } else {
            return $result[0];
        }
    }
    
}