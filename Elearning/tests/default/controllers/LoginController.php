<?php
class LoginController extends Zend_Controller_Action {
	protected $form;
	public function init() {
		$baseurl = $this->_request->getbaseurl ();
		$this->view->doctype ();
		$this->view->headtitle ( "Hừng Đông Online" );
		$this->view->headMeta ()->appendName ( "keyword", "Hừng Đông Online..." );
		$this->view->headLink ()->appendStylesheet ( $baseurl . "/public/css/style.css" );
	}
	public function indexAction() {
		$this->form = new Form_Login ();
		// $form = new Form_Login ();
		if ($this->_request->isPost ()) {
			$name = $this->_request->getPost ( 'username' );
			// Lấy các tham số còn lại
		}
		$this->view->form = $this->form;
	}
}