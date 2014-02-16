<?php
class IController extends Zend_Controller_Action {
	public function init() {
		$baseurl = $this->_request->getbaseurl ();
		$this->view->doctype ();
<<<<<<< HEAD:Elearning/application/controllers/IController.php
		$this->view->headtitle ( "Elearning" );
		$this->view->headMeta ()->appendName ( "keyword", "Elearning" );
		$this->view->headLink()->appendStylesheet('css/style.css');
=======
		$this->view->headtitle ( "E-learning" );
		$this->view->headMeta ()->appendName ( "keyword", "E-learning..." );
		$this->view->headLink ()->appendStylesheet ( $baseurl . "/public/css/style.css" );
>>>>>>> c04ecbab33ec36b8e11f798927c91e0dff8bf956:Elearning/application/modules/default/controllers/IController.php
	}
}