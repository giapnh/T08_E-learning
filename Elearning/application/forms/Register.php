<?php
class Form_Register extends Zend_Form {
	public function init() {
		$this->setAction ( '' )->setMethod ( 'post' );
		$email = $this->createElement ( "text", "email", array (
				"size" => "30",
				"class" => "text medium-text" 
		) );
		
		$username = $this->createElement ( "text", "username", array (
				"size" => "30",
				"class" => "text medium-text" 
		) );
		$password = $this->createElement ( "password", "password", array (
				"size" => "30",
				"class" => "text medium-text" 
		) );
		
		$repass = $this->createElement ( "password", "repass", array (
				"size" => "30",
				"class" => "text medium-text" 
		) );
		
		$submit = $this->createElement ( "submit", "submit", array (
				"class" => "label",
				"label" => "Đăng ký" 
		) );
		
		$this->setDecorators ( array (
				array (
						'viewScript',
						array (
								'viewScript' => 'register.phtml' 
						) 
				) 
		) );
		$username->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$password->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$repass->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$email->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$submit->removeDecorator ( 'DtDdWrapper' );
		
		$this->addElements ( array (
				$username,
				$password,
				$repass,
				$email,
				$submit 
		) );
	}
}