<?php
class IController extends Zend_Controller_Action {
	public function init() {
		$baseurl = $this->_request->getbaseurl ();
		$this->view->doctype ();
		$this->view->headtitle ( "Hừng Đông Online" );
		$this->view->headMeta ()->appendName ( "keyword", "Hừng Đông Online..." );
		$this->view->headLink ()->appendStylesheet ( $baseurl . "/public/css/style.css" );
	}
}