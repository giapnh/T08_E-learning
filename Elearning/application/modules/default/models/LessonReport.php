<?php

class Default_Model_LessonReport extends Zend_Db_Table_Abstract {

	protected $_name = "lesson_report";
	protected $_primary = "id";
	protected $db;

	public function __construct() {
		parent::__construct();
		$this->db = Zend_Registry::get('connectDB');
	}

	public function listAll() {
		return $this->fetchAll()->toArray();
	}

	/**
	 * 授業にレポートを追加する処理
	 *
	 * @param type $userId
	 * @param type $lessonId
	 * @param type $report
	 */
	public function addReport($userId, $lessonId, $report, $role =2) {
		$ins = array(
				'user_id' => $userId,
				'lesson_id' => $lessonId,
				'reason' => $report,
				'status' => 1,
				'role' => $role
		);
		$this->insert($ins);
	}

	/**
	 * 授業にレポートを追加する処理
	 *
	 * @param type $userId
	 * @param type $lessonId
	 * @param type $report
	 */
	public function addAdminReport($userId, $lessonId, $report) {
		$ins = array(
				'user_id' => $userId,
				'lesson_id' => $lessonId,
				'reason' => $report,
				'status' => 1,
				'role' => 3
		);
		return $this->insert($ins);
	}

	/**
	 * 授業のレポートリストを取る
	 *
	 * @param int $lessonId
	 * @return array
	 */
	public function getReports($lessonId){
		$select = $this->getAdapter()->select()
		->from($this->_name)
		->join("user", $this->_name.".user_id = user.id", array("username"))
		->where($this->_name.".status = 1")
		->where($this->_name.".role = 2 OR ".$this->_name.".role = 1")
		->where("lesson_id = ?", $lessonId);
		return $this->getAdapter()->fetchAll($select);
	}

	/**
	 * レポートを取る
	 *
	 * @param int $reportId
	 */
	public function getReportById($reportId) {
		$userModel = new Default_Model_Account();
		$adminModel = new Admin_Model_Admin();
		$select = $this->getAdapter()->select()
		->from($this->_name)
		->where("id = ".$reportId);
		$result = $this->getAdapter()->fetchRow($select);
		if ($result) {
			if ($result['role'] != 3) {
				$user = $userModel->getUserById($result['user_id']);
				$result['username'] = $user['username'];
			} else {
				$user = $adminModel->getAdminById($result['user_id']);
				$result['username'] = $user['username'];
			}
			return $result;
		} else {
			return NULL;
		}
	}


	/**
	 * 授業のレポートリストを取る
	 *
	 * @param int $lessonId
	 * @return array
	 */
	public function getReportsFull($lessonId){
		$userModel = new Default_Model_Account();
		$adminModel = new Admin_Model_Admin();

		$select = $this->getAdapter()->select()
		->from($this->_name)
		->where($this->_name.".status = 1")
		->where("lesson_id = ?", $lessonId);

		$results = $this->getAdapter()->fetchAll($select);
		foreach ($results as $index => $result) {
			if ($result['role'] != 3) {
				$user = $userModel->getUserById($result['user_id']);
				$results[$index]['username'] = $user['username'];
			} else {
				$user = $adminModel->getAdminById($result['user_id']);
				$results[$index]['username'] = $user['username'];
			}
		}
		return $results;
	}

	/**
	 * 授業の管理者からのレポートリストを取る
	 *
	 * @param int $lessonId
	 * @return array
	 */
	public function getAdminReports($lessonId) {
		$select = $this->getAdapter()->select()
		->from($this->_name)
		->join("admin", $this->_name.".user_id = admin.id", array("username"))
		->where($this->_name.".status = 1")
		->where($this->_name.".role = 3")
		->where("lesson_id = ?", $lessonId);
		return $this->getAdapter()->fetchAll($select);
	}

	/**
	 * 授業がレポートがあるかどうかをチェック
	 *
	 * @param int $lessonId
	 * @return boolean
	 */
	public function isReported($lessonId) {
		$select = $this->getAdapter()->select()
		->from($this->_name)
		->where('lesson_id='.$lessonId." AND status=1");
		if ($this->getAdapter()->fetchAll($select)) {
			return true;
		} else {
			return FALSE;
		}
	}


	/**
	 * 「Copyright」レポートを数える
	 *
	 * @return int
	 */
	public function countAllReport() {
		$select = $this->getAdapter()->select()
		->from($this->_name)
		->where("status = 1");
		return count($this->getAdapter()->fetchAll($select));
	}

	/**
	 * レポートを削除する
	 *
	 * @param type $reportId
	 */
	public function deleteReport($reportId) {
		$this->update(array("status"=>0), "id=".$reportId);
	}
}
