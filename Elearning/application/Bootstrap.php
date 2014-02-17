<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	protected function _initSession(){
		Zend_Session::start();
	}
	protected function _initAutoload() {

		// Set Error
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
				'module'     => 'error',
				'controller' => 'error',
				'action'     => 'error'
				)));

				//Load Module - khong co cung duoc - vi da cau hinh file bootstrap cs tung module roi.
				$autoloader = new Zend_Application_Module_Autoloader ( array (
				'namespace' => '',
				'basePath' => dirname ( __FILE__ )
				) );
				return $autoloader;
	}
	/**
	 * Khoi tao database
	 */
	//	protected function __initDB() {
	//		$dbOption = $this->getOption ( 'resources' );
	//		$dbOption = $dbOption ['db'];
	//
	//		// Setup database
	//		$db = Zend_Db::factory ( $dbOption ['adapter'], $dbOption ['params'] );
	//
	//		$db->setFetchMode ( Zend_Db::FETCH_ASSOC );
	//		$db->query ( "SET NAMES 'utf8'" );
	//		$db->query ( "SET CHARACTER SET 'utf8'" );
	//
	//		Zend_Registry::set ( 'connectDB', $db );
	//
	//		// Khi thiet lap che do nay model moi co the su dung duoc
	//		Zend_Db_Table::setDefaultAdapter ( $db );
	//
	//		// Return it, so that it can be stored by the bootstrap
	//		return $db;
	//	}

}

