<?php
class IndexController extends Zend_Controller_Action {
	public function indexAction() {
		$baseurl = $this->_request->getbaseurl ();
		$this->view->doctype ();
		$this->view->headtitle ( "Hừng Đông Online" );
		$this->view->headMeta ()->appendName ( "keyword", "Hừng Đông Online..." );
		$this->view->headLink ()->appendStylesheet ( $baseurl . "/public/css/style.css" );
	}
}