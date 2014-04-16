<?php

class Default_Model_Master extends Zend_Db_Table_Abstract {

    protected $_name = "master";
    protected $_primary = "master_key";
    protected $db;
    public static $KEY = "master_key";
    public static $VALUE = "master_value";
    public static $KEY_LOCK_COUNT = "LOCK_COUNT"; //ログイン間違い回数
    public static $KEY_SESSION_TIME = "SESSION_TIME"; //自動ログアウト時間（時）
    public static $KEY_LOGIN_FAIL_LOCK_TIME = "LOGIN_FAIL_LOCK_TIME"; //ロック時間
    public static $KEY_COMA_PRICE = "COMA_PRICE"; //学生が使用する場合、１コマにつき暫定２万ドン。
    public static $KEY_TEACHER_FEE_RATE = "TEACHER_FEE_RATE"; //先生の課金率（%）
    public static $KEY_LESSON_DEADLINE = "LESSON_DEADLINE"; //授業の期限（日）
    public static $KEY_VIOLATION_TIME = "VIOLATION_TIME"; //資料の禁止回数
    public static $KEY_FILE_LOCATION = "FILE_LOCATION"; //課金情報を格納するフォルダ
    public static $KEY_BACKUP_TIME = "BACKUP_TIME"; //自動バックアップ期間（日）
    public static $KEY_PASSWORD_CONST = "PASSWORD_CONST";

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }

    /**
     * マースターデータを取る
     * 
     * @return array
     */
    public function getMasterData() {
        $query = $this->select()
                ->from($this->_name, "*");
        $rows = $this->getAdapter()->fetchAll($query);
        if ($rows) {
            $masterData = array();
            foreach ($rows as $row) {
                $masterData[$row[self::$KEY]] = $row[self::$VALUE];
            }
            return $masterData;
        } else {
            return NULL;
        }
    }

    public function getMasterValue($master_key) {
        $query = $this->select()
                ->from($this->_name, "*")
                ->where('master_key=?', $master_key);
        $row = $this->getAdapter()->fetchRow($query);
        if ($row) {
            var_dump($row['master_value']);
            die();
            return $row['master_value'];
        } else {
            return 0;
        }
    }

    /**
     * マースターデータを更新する
     * 
     * @param array $masterData
     * @return boolean
     */
    public function setMasterData($masterData) {
        $result = true;
        foreach ($masterData as $key => $value) {
            if (!$this->setMasterDataForKey($key, $value)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * マースターデータを更新する
     * 
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function setMasterDataForKey($key, $value) {
        try {
            $update = array(self::$VALUE => $value);
            $where = self::$KEY . "='" . $key . "'";
            $this->update($update, $where);
            return true;
        } catch (Exception $e) {
            echo $e;
            die();
            return false;
        }
    }

}
