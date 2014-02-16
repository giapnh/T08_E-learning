<?php
class Default_Form_Register extends Zend_Form {
	public function init() {
		$this->setAction ( '' )->setMethod ( 'post' );
		$email = $this->createElement ( "text", "email", array (
				"size" => "30",
				"class" => "text medium-text"
		) );

		$username = $this->createElement ( "text", "username", array (
				"size" => "30",
				"class" => "text medium-text",
				"required" => true
		) );
		$password = $this->createElement ( "password", "password", array (
				"size" => "30",
				"class" => "text medium-text",
				"required" => true
		) );

		$repass = $this->createElement ( "password", "repass", array (
				"size" => "30",
				"class" => "text medium-text",
				"required" => true
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
		$captcha->removeDecorator ( 'HtmlTag' )->removeDecorator ( 'Label' );
		$submit->removeDecorator ( 'DtDdWrapper' );

		$this->addElements ( array (
				$username,
				$password,
				$repass,
				$email,
				$captcha,
				$submit
		) );
	}
}