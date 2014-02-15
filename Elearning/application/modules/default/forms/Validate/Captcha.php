<?php
class Default_Form_Validate_Captcha{
	static function isValid($captcha){
		$capId = $captcha['id'];
		$capInput = $captcha['input'];
		$capSession = new Zend_Session_Namespace('Zend_Form_Captcha_'.$capId);
		$capWord = $capSession->getIterator();
		if(isset($capWord['word']) && $capWord['word'] == $capInput){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}