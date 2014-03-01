<?php

require 'IController.php';

class UserController extends IController {

    public function indexAction() {
        $this->redirect('user/login');
    }

    /**
     * ログイン画面
     * @param string $username ユーザ名
     * @param string $password パースワード
     * @param string $role     役割
     */
    public function loginAction() {
        // 		//Ip cache
        // 		$frontendOptions = array(
        // 				'lifetime' => Code::$LOGIN_FAIL_LOCK_TIME, // cache lifetime of 2 hours
        // 				'automatic_serialization' => true
        // 		);
        // 		$backendOptions = array(
        // 				'cache_dir' => 'C:/Users/Public' // Directory where to put the cache files
        // 		);
        // 		$cache = Zend_Cache::factory('Core',
        // 				'File',
        // 				$frontendOptions,
        // 				$backendOptions);
        // 		// see if a cache already exists:
        // 		if($result = $cache->load('ip_locking')) {
        // 			// 			$cache->save($result, 'myresult');
        // 		} else {
        // 			// cache hit! shout so that we know
        // 			echo "This one is from cache!\n\n";
        // 		}
        $form = new Default_Form_Login ();
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $authAdapter = new Default_Model_Account();
            $auth = Zend_Auth::getInstance();
            $uname = $this->_request->getParam('username');
            $paswd = $this->_request->getParam('password');
            $role = $this->_request->getParam('role');
            // If filed is null
            if (trim($uname) == '') {
                $this->view->errorMessage = Message::$M001;
                return;
            } else if (trim($paswd) == '') {
                $this->view->errorMessage = Message::$M002;
                return;
            }

            $flag = false;
            if ($authAdapter->isValid($uname, $paswd, $role)) {
                $flag = true;
                $curr_ip = $_SERVER['REMOTE_ADDR'];
                if ($curr_ip === "::1") {
                    $curr_ip = "127.0.0.1";
                }
                // If user is teacher, check ip address valid or invalid
                if ($role == 2) {
                    $flag = false;
                    if ($authAdapter->checkIpValid($uname, $curr_ip)) {
                        $flag = true;
                    } else {
                        echo "Invalid ip address";
                        $flag = false;
                        return;
                    }
                }

                $data = $authAdapter->getUserInfo($uname);
                // Save
                $auth->getStorage()->write($data);
            } else {
                $authAdapter->incLoginFailure($uname);
                $this->view->errorMessage = Message::$M003;
            }
            if ($flag == true) {
                if ($role == 1) {
                    $this->_redirect('/student/index');
                } else {
                    $this->_redirect('/teacher/index');
                }
            }
        }
    }

    public function login_new_ipAction() {
        
    }

    /**
     * 登録画面
     * @param type $name Description
     */
    public function registerAction() {
        $user = new Default_Model_Account();
        $form = new Default_Form_Register ();
        $this->view->form = $form;
        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();
            //Check empty
            if (trim($data['username']) == '') {
                $this->view->errorMessage = Message::$M006;
                return;
            } else {
                if (!preg_match(Code::$REGEX_USERNAME, $data['username'])) {
                    $this->view->errorMessage = Message::$M006;
                    return;
                }
            }
            if (trim($data['password']) == '') {
                $this->view->errorMessage = Message::$M007;
                return;
            } else {
                if (!preg_match(Code::$REGEX_PASSWORD, $data['password'])) {
                    $this->view->errorMessage = Message::$M007;
                    return;
                }
            }

            if (trim($data['password']) != trim($data['repassword'])) {
                $this->view->errorMessage = Message::$M008;
                return;
            }

            if (trim($data['fullname']) == '') {
                $this->view->errorMessage = Message::$M009;
                return;
            }

            if ($data['day'] == 0 || $data['month'] == 0 || $data['year'] == 0) {
                $this->view->errorMessage = Message::$M010;
                return;
            }

            if (trim($data['address']) == '') {
                $this->view->errorMessage = Message::$M011;
                return;
            }

            if (trim($data['phone']) == '') {
                $this->view->errorMessage = Message::$M012;
                return;
            }

            if (trim($data['bank_acc']) == '') {
                $this->view->errorMessage = Message::$M013;
            }

            if (trim($data['secret_question']) == '') {
                $this->view->errorMessage = Message::$M014;
                return;
            }

            if (trim($data['secret_answer']) == '') {
                $this->view->errorMessage = Message::$M015;
                return;
            }

            if ($user->isExits($data['username'])) {
                $this->view->errorMessage = Message::$M034;
                return;
            }
            $user->insertNew($data);
            $this->_redirect('user/login');
        }
    }

    /**
     * ログアウト処理
     * 
     */
    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('user/login');
    }

}
