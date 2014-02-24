<?php
class Default_Form_Register extends Zend_Form {
	public function init() {
		$this->setAction ( '' )->setMethod ( 'post' );
		$username = $this->createElement ( "text", "username", array (
				"class" => "res_input",
				"id" => "username",
				"maxlength" => "30",
				"required" => true
		) );
		$password = $this->createElement ( "password", "password", array (
				"class" => "res_input res_input_pass",
				"id" => "password",
				"onclick" => "this.select()",
				"maxlength" => "20",
				"required" => true
		) );

		$confirmPass = $this->createElement ( "password", "repassword", array (
				"class" => "res_input res_input_pass",
				"id" => "repassword",
				"onclick" => "this.select()",
				"maxlength" => "20",
				"required" => true
		) );

		$email = $this->createElement ( "text", "email", array (
				"class" => "res_input",
				"id" => "res_email"
		) );

		$name =   $this->createElement ( "text", "fullname", array (
				"class" => "res_input",
				"id" => "res_name"
		) );

		$path = Zend_Controller_Front::getInstance()->getBaseUrl();
		$captcha = new Zend_Form_Element_Captcha('captcha',array(
				'label' => 'Captcha',
				'captcha' => array(
						'captcha' => 'Image',
						'wordLen' => "6",
						'timeout' => "300",
						'font' => APPLICATION_PATH.'/../public/fonts/tunga.ttf',
						'imgDir' => APPLICATION_PATH.'/../public/images/captcha/',
						'imgUrl' => $path.'/../public/images/captcha/',
						'height' => "60",
						'width' => "220",
						//'size' => "30",
						'fontSize' => "40",
				),
		));

		$submit = $this->createElement ( "submit", "submit", array (
				"class" => "showlogfromm",
				"label" => "登録"
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
		$confirmPass->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$email->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$name->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$captcha->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$submit->removeDecorator ( 'DtDdWrapper' );

		$this->addElements ( array (
				$username,
				$password,
				$confirmPass,
				$email,
				$name,
				$captcha,
				$submit
		) );
	}
}