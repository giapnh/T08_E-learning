<?php

require '/../../default/controllers/code.php';

class Admin_Model_User extends Zend_Db_Table_Abstract {
    protected $_name = "user";
    protected $_primary = "id";
    protected $db;
    
    /**
     * ユーザリストを取る
     * 
     * @param type $page
     * @return array $pager
     */
    public function getUsers($page, $limit) {
        // TODO
        
        $users = array(
            array(
                'id' => '1',
                'username' => 'minhtq',
                'registerdDate' => '07/03/2014',
                'role' => '学生',
                'status' => '許可を待っている'
            ),
            array(
                'id' => '1',
                'username' => 'minhtq',
                'registerdDate' => '07/03/2014',
                'role' => '学生',
                'status' => '許可を待っている'
            ),
            array(
                'id' => '1',
                'username' => 'minhtq',
                'registerdDate' => '07/03/2014',
                'role' => '学生',
                'status' => '許可を待っている'
            )
        );
        
        return array(
            'page' => $page,
            'totalPages' => 10,
            'limit' => count($users),
            'next' => TRUE,
            'pre' => TRUE,
            'users' => $users
        );
    }
}