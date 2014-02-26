<?php
require 'IController.php';
//require 'RegisterController.php';
class TeacherController extends IController {
	protected $user;
/* 	public function init(){
		$auth = Zend_Auth::getInstance ();
		$infoUser = $auth->getIdentity ();
		//$this->view->fullName = $infoUser->full_name;
		if(!$infoUser){
			$this->_redirect("user/login");
		}
		$this->user = $infoUser;
	} */
	public function indexAction(){
		// 	   $this->redirect('student/mylession');
	}
	public function createLessonAction(){
		$form = new Default_Form_CreateLesson();
		$this->view->form = $form;
		if($this->_request->isPost()){
			$parram = $this->_getAllParams();
			Zend_Debug::dump($this->_getAllParams());
			$adapter = new Zend_File_Transfer_Adapter_Http();
			$uploadDir = APPLICATION_PATH.'\..\upload\\';
			$adapter->setDestination($uploadDir);
			//$upload->setFilters($filters)
			//$upload->receive();
			Zend_Debug::dump($adapter->getFileInfo());
			Zend_Debug::dump($_FILES);
			$i= 1;
			$listFiles = array();
			$k = 0;
			foreach ($adapter->getFileInfo() as   $file => $info) {
				if($adapter->isUploaded($file)){
					$ext = $this->_findexts($info['name']);
					$fileName = 'file'.$i.time().'.'.$ext;

					$i++;
					$target = 	$uploadDir.$fileName;
					$adapter->addFilter('Rename',

							array('target' => $target,

									'overwrite' => true));
					echo $file;
					if(!$adapter->receive($file)){

						$this->view->msg=$adapter->getMessages();
						return;
					}
					else {
						$listFiles[] = array(
							"filename"=> $parram["file_title"][$k] !="" ? $parram["file_title"][$k] : $info['name'],
							"description"	=> $parram["file_dec"][$k],
							"location" => $fileName
						);
					}
				}
				$k++;
			}
			if(count($listFiles)>=0){
				$lessonModel = new Default_Model_Lesson();
				$les_id = $lessonModel->insert(array(
					"teacher_id" => $this->user->id,
					"title" => $parram["title"],
					"description" => $parram["les_dec"],
					"status" => 1
				));
				$tags = explode("、", $parram["tag"]);
				$tagModel = new Default_Model_Tag();
				$leTagModel = new Default_Model_LessonTag();
				$lesFileModel = new Default_Model_LessonFile();
				foreach ($tags as $tag){
					if(trim($tag)){
						$tag_id = $tagModel->isExistTag($tag);
						if(!$tag_id){
							$tag_id = $tagModel->insert(array("tag_name" => $tag));

						}
						$leTagModel->insert(array(
							"lesson_id" => $les_id,
							"tag_id" => $tag_id
						));
					}
				}
				foreach ($listFiles as $file){
					$lesFileModel->insert(array(
						"lesson_id" => $les_id,
						"filename" => $file["filename"],
						"description" => $file["description"],
						"location" => $file["location"]
					));
				}
			}
		}
	}
	protected function _findexts($filename)

	{

		$filename = strtolower($filename) ;

		$exts = explode(".", $filename) ;


		$n = count($exts)-1;


		$exts = $exts[$n];


		return $exts;

	}


	public function thunghiemAction(){}
	
	public function managelessonAction(){}

	public function viewlessonAction(){}

	public function viewfileAction(){}

	public function updatelessonAction(){}

	public function viewstudentlessonAction(){}

	public function viewresultAction(){}

	public function viewpayinfoAction(){}
}