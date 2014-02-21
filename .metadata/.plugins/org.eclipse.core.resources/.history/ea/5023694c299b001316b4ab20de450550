<?php
class Default_Form_Login extends Zend_Form {
	public function init() {
		$this->setAction ( '' )->setMethod ( 'post' );
		$username = $this->createElement ( "text", "username", array (
				"id" => "clogin_username",
				"tabindex" => "3"
		) );


		$password = $this->createElement ( "password", "password", array (
				"tabindex" => "4",
				"id" => "clogin_password"
		) );

		$submit = $this->createElement ( "submit", "submit", array (
				"tabindex" => "5",
				"class" => "showlogfromm",
				"label" => "ログイン"
		) );

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

		$this->addElements ( array (
				$username,
				$password,
				$submit,
		) );
	}
}