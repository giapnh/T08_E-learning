<?php
class AccountController extends IController {
	public function indexAction() {
		$muser=new Default_Model_Account;
		$data=$muser->listAll();
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
        
}