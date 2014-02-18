<?php
class Default_Form_Login extends Zend_Form {
	public function init() {
		$this->setAction ( '' )->setMethod ( 'post' );
		$type=$this->createElement("radio","type",array(
		//"label" => "Gender",
                                          "multioptions"=> array(
                                                    "1" => "student",
                                                    "2" => "teacher",
		),
		));
		$username = $this->createElement ( "text", "username", array (
				"id" => "clogin_username",
				"tabindex" => "1"
				) );
				$password = $this->createElement ( "password", "password", array (
		"tabindex" => "2",
		"id" => "clogin_password"
		) );

		$email = $this->createElement ( "text", "email", array (
				"size" => "30"
				) );
				$submit = $this->createElement ( "submit", "submit", array (
				"class" => "label",
				"label" => "Đăng nhập"
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
								$email->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
								$submit->removeDecorator ( 'DtDdWrapper' );

								$this->addElements ( array (
								$username,
								$password,
								$email,
								$submit,
								) );
	}
}