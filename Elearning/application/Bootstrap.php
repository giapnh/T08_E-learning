<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	protected function _initSession(){
		Zend_Session::start();
	}

	protected function _initAutoload() {
		/*Autoload for default module*/
		$default_loader = new Zend_Application_Module_Autoloader( array(    'namespace' => 'Default_',
                                                                            'basePath'  => APPLICATION_PATH . '/modules/default'
                                                                            ));
                                                                            /*Autoload for admin module*/
                                                                            $admin_loader = new Zend_Application_Module_Autoloader( array(    'namespace' => 'Admin_',
                                                                            'basePath'  => APPLICATION_PATH . '/modules/admin'
                                                                            ));
                                                                            // Set Error
                                                                            $front = Zend_Controller_Front::getInstance();
                                                                            $front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
                'module'     => 'error',
                'controller' => 'error',
                'action'     => 'error'
                )));
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

