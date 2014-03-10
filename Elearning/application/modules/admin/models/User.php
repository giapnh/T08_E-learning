<?php

require_once '/../../default/controllers/code.php';

class Admin_Model_User extends Zend_Db_Table_Abstract {
    protected $_name = "user";
    protected $_primary = "id";
    protected $db;
    
    public static $ID = "id";
    public static $USERNAME = "username";
    public static $FIRST_PASSWORD = "first_password";
    public static $PASSWORD = "password";
    public static $NAME = "name";
    public static $SEX = "sex";
    public static $EMAIL = "email";
    public static $BIRTHDAY = "birthday";
    public static $ADDRESS = "address";
    public static $PHONE = "phone";
    public static $BANK_ACCOUNT = "bank_account";
    public static $FIRST_SECRET_QUESTION = "first_secret_question";
    public static $SECRET_QUESTION = "secret_question";
    public static $FIRST_SECRET_ANSWER = "first_secret_answer";
    public static $SECRET_ANSWER = "secret_answer";
    public static $ROLE = "role";
    public static $STATUS = "status";
    
    public static $DESC = "DESC";
    public static $INSC = "ASC";
    
    public static $USER_STATUS_AVAILABLE = 1;
    public static $USER_STATUS_WAITING = 2;
    public static $USER_STATUS_DELETED = 3;

    /**
     * 
     * @param type $userId
     */
    public function getUser($userId) {
        $users = $this->find($userId);
        
        if ($users) {
            return $users[0];
        } else {
            return NULL;
        }
    }
    
    /**
     * ユーザリストを取る
     * 
     * @param type $page
     * @return array $pager
     */
    public function getUsers($page, $limit, $orderBy, $order) {
        // TODO
        $query = $this->select()
                ->from($this->_name, "*")
                ->order(array($orderBy." ".$order))
                ->limitPage($page, $limit);
        $result = $this->getAdapter()->fetchAll($query);
        if ($page > 1) {
            $pre = $page - 1;
        } else {
            $pre = NULL;
        }
        
        $query = $this->select()
                ->from($this->_name, "*");
        $result2 = $this->getAdapter()->fetchAll($query);
        $total = count($result2);
        $totalPages = ceil($total/$limit);
        if ($page < $totalPages) {
            $next = $page + 1;
        } else {
            $next = NULL;
        }
        
        return array(
            'page' => $page,
            'totalPages' => $totalPages,
            'limit' => count($result),
            'next' => $next,
            'pre' => $pre,
            'users' => $result
        );
    }
    
    /**
     * ユーザを許可する
     * 
     * @param int $userId   ユーザ名
     * @return boolean
     */
    public function accept($userId) {
        try {
            $this->update(
                    array('status'=>  self::$USER_STATUS_AVAILABLE), 
                    "status='".self::$USER_STATUS_WAITING."' AND id='".$userId."'");
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
            return false;
        }
    }
    
    /**
     * 初期のパースワードのリセットする
     * 
     * @param int $userId
     * @return boolean
     */
    public function resetPassword($userId) {
        $user = $this->getUser($userId);
        if ($user == NULL) {
            return false;
        } else {
            try {
                $this->update(
                    array('password'=>$user[self::$FIRST_PASSWORD]), 
                    "id='".$userId."'");
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }
    
    /**
     * 初期のVerifyコードにリセットする
     * 
     * @param int $userId
     * @return boolean
     */
    public function resetVerifyCode($userId) {
        $user = $this->getUser($userId);
        if ($user == NULL) {
            return false;
        } else {
            try {
                $this->update(
                    array(self::$SECRET_QUESTION=>$user[self::$FIRST_SECRET_QUESTION],
                        self::$SECRET_ANSWER=>$user[self::$FIRST_SECRET_ANSWER]), 
                    "id='".$userId."'");
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }
    
    /**
     * ユーザを削除する
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
}