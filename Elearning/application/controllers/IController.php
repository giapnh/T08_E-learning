<?php
class IController extends Zend_Controller_Action {
	public function init() {
		$baseurl = $this->_request->getbaseurl ();
		$this->view->doctype ();
		$this->view->headtitle ( "Elearning" );
		$this->view->headMeta ()->appendName ( "keyword", "Elearning" );
		$this->view->headLink()->appendStylesheet('css/style.css');
	}
}