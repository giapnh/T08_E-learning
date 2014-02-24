<?php
require 'Message.php';
class IController extends Zend_Controller_Action {
	public function init() {
		$baseurl = $this->_request->getbaseurl ();
		$this->view->doctype ();
		$this->view->headtitle ( "E-learning" );
		$this->view->headMeta ()->appendName ( "keyword", "E-learning..." );
		$this->view->headLink ()->appendStylesheet ( $baseurl . "/public/css/style.css" );
	}
}