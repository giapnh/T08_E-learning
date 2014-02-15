<?php
class Form_Login extends Zend_Form {
	public function init() {
		$this->setAction ( '' );
		$this->setMethod ( 'post' );
		// $this->setDecorators ( array (
		// array (
		// 'viewScript',
		// array (
		// 'viewScript' => 'login/index.phtml'
		// )
		// )
		// ) );
		$username = new Zend_Form_Element_Text ( 'username' );
		$username->setLabel ( 'ten dang nhap' );
		$username->setOptions ( array (
				"size" => "36" 
		) );
		
		$password = new Zend_Form_Element_Password ( 'password' );
		$password->setLabel ( 'pass' );
		$password->setOptions ( array (
				"size" => "36" 
		) );
		
		$submit = new Zend_Form_Element_Submit ( 'submit' );
		$this->setDecorators ( array (
				array (
						'viewScript',
						array (
								'viewScript' => 'login.phtml' 
						) 
				) 
		) );
		$username->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$password->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$submit->removeDecorator ( 'DtDdWrapper' );
		$this->addElement ( $username )->addElement ( $password )->addElement ( $submit );
	}
}