<?php

require_once 'IController.php';

class UserController extends IController {

    /**
     * Check login or not yet
     */
    public function checkFirst() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_request->getActionName() != 'login') {
                $this->_redirect('user/login');
            }
        } else {
            $infoUser = $auth->getStorage()->read();
            if ($infoUser['role'] == 1) {
                //学生チェックする
                $this->_redirect('student/index');
            } else if ($infoUser['role'] == 2) {
                $this->_redirect('teacher/index');
            }else if ($infoUser['role'] == 3) {
                $this->_redirect('admin/user');
            }
        }
    }

    /**
     * If loged in, get user information
     */
    public function initial() {
        $auth = Zend_Auth::getInstance();
        $infoUser = $auth->getStorage()->read();
        $this->view->user_info = $infoUser;
        $this->user = $infoUser;
        $this->currentUser = $infoUser;
    }

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
        $this->checkFirst();
        $master = new Default_Model_Master();
        $authAdapter = new Default_Model_Account();
        $form = new Default_Form_Login ();
        $this->view->form = $form;
        if ($this->_request->isPost()) {
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
            } else {
//Update last login time
                $uInfo = $authAdapter->getUserInfo($uname);
                $status = $uInfo['status'];
                if ($status == 3) {// Check if user is activing
                    $last_time = $uInfo['last_login_time'];
                    $offset = time() - $last_time;
                    //Unlock
                    if ($offset >= $master->getMasterValue(Default_Model_Master::$KEY_LOGIN_FAIL_LOCK_TIME)) {
                        if ($role == 1 && $uInfo['role'] == 1) {
                            $authAdapter->unlock($uname);
                            $status = 1;
                        } else if ($role == 2 && $uInfo['role'] == 2) {
                            $this->_redirect('user/loginVerifyConfirm?uname=' . $uname);
                            return;
                        } else {
                            $this->view->errorMessage = Message::$M003;
                            return;
                        }
                    } else {
                        $message = Message::$M0041;
                        $lock_time = $master->getMasterValue(Default_Model_Master::$KEY_LOGIN_FAIL_LOCK_TIME);
                        $hour = round($lock_time / 3600);
                        $min = round(($lock_time - 3600 * $hour) / 60);
                        $sec = $lock_time - 3600 * $hour - 60 * $min;
                        $message = str_replace("{%s}", $hour . "時" . $min . "分" . $sec . "秒", $message);
                        $this->view->errorMessage = $message;
                    }
                }

                if ($status == 2) {// If not confirm yet
                    $this->view->errorMessage = Message::$M0032;
                    return;
                }
                if ($status == 1) {// User active
                    // Check valid
                    if ($authAdapter->isValid($uname, $paswd, $role)) {
                        if ($role == 2) {
                            //Check IP
                            $curr_ip = $_SERVER['REMOTE_ADDR'];
                            if ($curr_ip === "::1") {
                                $curr_ip = "127.0.0.1";
                            }
                            if ($authAdapter->checkIpValid($uname, $curr_ip)) {
                                $data = $authAdapter->getUserInfo($uname);
                                $authAdapter->updateLastLoginIp($uname, $curr_ip);
                                $data = $authAdapter->getUserInfo($uname);
                                //Update last login time
                                $authAdapter->updateLastLoginTime($uname);
                                // Save
                                $auth->getStorage()->write($data);
                                $this->_redirect('teacher/index');
                            } else {
                                $data = $authAdapter->getUserInfo($uname);
                                //Update last login time
                                $authAdapter->updateLastLoginTime($uname);
                                // Save
                                $auth->getStorage()->write($data);
                                $this->_redirect('user/loginVerifyConfirm');
                                return;
                            }
                        } else {
                            $data = $authAdapter->getUserInfo($uname);
                            //Update last login time
                            $authAdapter->updateLastLoginTime($uname);
                            // Save
                            $auth->getStorage()->write($data);
                            $this->_redirect('student/index');
                            return;
                        }
                    } else {
                        $authAdapter->incLoginFailure($uname);
                        if ($authAdapter->getFailCount($uname) >= //
                                $master->getMasterValue(Default_Model_Master::$KEY_LOCK_COUNT)) {
                            //Lock
                            $authAdapter->lock($uname);
                            $message = Message::$M0041;
                            $lock_time = $master->getMasterValue(Default_Model_Master::$KEY_LOGIN_FAIL_LOCK_TIME);
                            $hour = round($lock_time / 3600);
                            $min = round(($lock_time - 3600 * $hour) / 60);
                            $sec = $lock_time - 3600 * $hour - 60 * $min;
                            $message = str_replace("{%s}", $hour . "時" . $min . "分" . $sec . "秒", $message);
                            $this->view->errorMessage = $message;
                            $authAdapter->updateLastLoginTime($uname);
                            return;
                        }
                        $this->view->errorMessage = Message::$M003;
                    }
                }
            }
        }
    }

    public function loginverifyconfirmAction() {
        $uname = $this->getParam('uname');
        $acc = new Default_Model_Account();
        $uInfo = $acc->getUserInfo($uname);
        if ($uInfo['status'] != 3) {
            $this->redirect('user/login');
            return;
        }
        $data = $this->_request->getParams();
        if ($this->_request->isPost()) {
            $question = trim($data['secret_question']);
            $anwser = trim($data['secret_answer']);
            if ($question == '') {
                $this->view->errorMessage = Message::$M014;
                return;
            }

            if ($anwser == '') {
                $this->view->errorMessage = Message::$M015;
                return;
            }

            $auth = Zend_Auth::getInstance();
            $user = new Default_Model_Account();
            $curr_ip = $_SERVER['REMOTE_ADDR'];
            if ($curr_ip === "::1") {
                $curr_ip = "127.0.0.1";
            }
            if ($user->isValidSecretQA($uname, $question, $anwser) == 1) {
                $authAdapter = new Default_Model_Account();
                $authAdapter->unlock($uname);
                // 現在IPを更新します
                $this->_redirect('user/login');
            } else {
                $this->view->
                        errorMessage = "秘密質問と秘密答えが会っていない！";
                return;
            }
        }
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
//                if (!preg_match(Code::$REGEX_USERNAME, $data['username'])) {
//                    $this->view->errorMessage = Message::$M006;
//                    return;
//                }
                if (strlen(trim($data['username'])) < 5) {
                    $this->view->errorMessage = Message::$M006;
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

            if (trim($data ['fullname']) == '') {
                $this->view->errorMessage = Message::$M009;
                return;
            }

            if ($data['day'] == 0 || $data['month'] == 0 || $data['year'] == 0) {
                $this->view->errorMessage = Message::$M010;
                return;
            }
//アドレス
            if (trim($data['address']) == '') {
                $this->view->errorMessage = Message::$M011;

                return;
            }
// 電話番号
            if (trim($data['phone']) == '') {
                $this->view->errorMessage = Message::$M012;
                return;
            }

            //銀行アカウント
            if (trim($data['bank_acc']) == '') {
                $this->view->errorMessage = Message::$M013;
                return;
            } else {
                if ($data['role'] == 1) {
                    if (!preg_match(Code::$REGEX_STUDENT_CREADIT, $data['bank_acc'])) {
                        $this->view->errorMessage = Message::$M0131;
                        return;
                    }
                } else if ($data['role'] == 2) {
                    if (!preg_match(Code::$REGEX_TEACHER_BANK_ACC, $data['bank_acc'])) {
                        $this->view->errorMessage = Message::$M0132;
                        return;
                    }
                }
            }
// なぜユーザは先生です、秘密質問がチェックする
            if ($data['role'] == 2) {
                if (trim($data['secret_question']) == '') {
                    $this->view->errorMessage = Message::$M014;
                    return;
                }

                if (trim($data['secret_answer']) == '') {
                    $this->view->errorMessage = Message::$M015;
                    return;
                }
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
        $auth->getStorage()->clear();
        $this->_redirect('user/login');
    }

}
