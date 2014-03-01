<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Default_Model_Tag extends Zend_Db_Table_Abstract {

    protected $_name = "tag";
    protected $_primary = "id";
    protected $db;

    public function __construct($config) {
        parent::__construct($config);
        $this->db = Zend_Registry::get('connectDB');
    }

    public function listAll() {
        return $this->fetchAll()->toArray();
    }

}
