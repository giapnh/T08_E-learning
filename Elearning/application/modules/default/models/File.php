<?php

require_once '/../controllers/Code.php';

class Default_Model_File extends Zend_Db_Table_Abstract {
    protected $_name = "lesson_file";
    protected $_primary = "id";
    protected $db;
    protected $adapter = null;
    protected $tmp;
    protected $lines;
    protected $lineCount;
    protected $lessonDeadline;
    
    public $fileSaved;
    public $lastError = "";
    
    public static $ID = "id";
    public static $LESSON_ID = "lesson_id";
    public static $FILENAME = "filename";
    public static $DESCRIPTION = "description";
    public static $LOCATION = "location";
    public static $TITLE = "title";
    public static $SUBTITLE = "subtitle";
    public static $TSV_KEY_TITLE = "TestTitle";
    public static $TSV_KEY_SUBTITLE = "TestSubTitle";
    public static $TSV_KEY_END = "End";

    public static $FILE_TYPE_TEST = 1;
    public static $FILE_TYPE_NORMAL = 2;
    
    public static $FILE_MAX_SIZE = 524288000;
    
    // TSV ファイルエラー
    protected $TSV_ERROR_TITLE = "タイトルが無効";
    protected $TSV_ERROR_SUBTITLE = "サブタイトルが無効";

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
        $master = new Default_Model_Master();
        $this->_lessonDeadline = $master->getMasterValue(Default_Model_Master::$KEY_LESSON_DEADLINE);
    }
	public function deleteFileById($fileId){
		$file = $this->findFileById($fileId);
		if(!$file)
			return;
		$sql = "DELETE lesson_file, file_comment, copyright_report
				FROM lesson_file
				LEFT JOIN file_comment ON lesson_file.id = file_comment.file_id
				LEFT JOIN copyright_report ON lesson_file.id = copyright_report.file_id
				WHERE lesson_file.id = ".$fileId;
		$this->getAdapter()->query($sql);
		
		
	}
    /**
     * ファイルを格納するフォールダを取る
     * 
     * @return string ファイルを格納するフォールダ
     */
    public function getFileFolder() {
        $masterModel = new Default_Model_Master();
        return APPLICATION_PATH . "\\..\\" . $masterModel->getMasterValue(Default_Model_Master::$KEY_FILE_LOCATION);
    }
    
    public function getFileFolderName() {
        $masterModel = new Default_Model_Master();
        return $masterModel->getMasterValue(Default_Model_Master::$KEY_FILE_LOCATION);
    }
    
    /**
     * ファイルを格納するフォールダを設定
     * 
     * @param string $path
     * @return boolean
     * 
     */
    public function setFileLocation($path) {
        // TODO: rename dir only
//        if (!mkdir($fileLocation, 0777, true)) {
//            return FALSE;
//        }
        return true;
    }
    
    /**
     * ファイルアップロード処理
     * 
     * @param array $fileDescriptions
     * @return boolean
     */
    public function exercuteFiles($fileDescriptions) {
        $this->fileSaved = array();
        if ($this->adapter == null) {
             $this->adapter = new Zend_File_Transfer_Adapter_Http();
        }
        
        $k = 0;
        foreach ($this->adapter->getFileInfo() as $file => $info) {
            if ($this->adapter->isUploaded($file)) {
                $description = $fileDescriptions[$k];
                if (!$this->exercuteFile($file, $info, $description, $k)) {
                    return false;
                }
                $k++;
            }
        }
        
        if ($k == 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * ファイル処理
     * 
     * @param type $file
     * @param type $info
     * @param type $description
     * @param type $index
     * @return boolean
     */
    public function exercuteFile($file, $info, $description, $index) {
        $ext = $this->_findexts($info['name']);
        if ($ext == "tsv") {
            return $this->exercuteTsvFile($file, $info, $description, $index);
        } else if ( $ext == "mp4" || $ext == "wav" || 
                    $ext == "mp3" || 
                    $ext == "pdf" || 
                    $ext == "png" || $ext == "jpg" || $ext == "gif") {
            return $this->exercuteNormalFile($file, $info, $description, $index);
        } else {
            return false;
        }
    }

    /**
     * ファイルのタイプを取る
     * 
     * @param string $filename
     * @return string
     */
    private function _findexts($filename) {
        $filename = strtolower($filename);
        $exts = explode(".", $filename);
        $n = count($exts) - 1;
        $exts = $exts[$n];
        return strtolower($exts);
    }
    
    /**
     * ファイルのタイプを取る
     * 
     * @param string $filename
     * @return string
     */
    public function getFileExt($filename) {
        return $this->_findexts($filename);
    }
    
    /**
     * TSVじゃないのファイル
     * 
     * @param type $file
     * @param type $info
     * @param type $description
     * @param type $index
     * @return boolean
     */
    protected function exercuteNormalFile($file, $info, $description, $index) {
        $ext = $this->_findexts($info['name']);
        $fileName = time() . $index . '.' . $ext;

        $target = $this->getFileFolder() . "\\" .$fileName;
        $this->adapter->addFilter('Rename', array('target' => $target,
            'overwrite' => true));
        if (!$this->adapter->receive($file)) {
            return false;
        } else {
            $this->fileSaved[] = array(
                "type" => self::$FILE_TYPE_NORMAL,
                "filename" => $info['name'],
                "location" => $fileName,
                "description" => $description,
                "title" => "",
                "subtitle" => "",
            );
            return true;
        }
    }

    /**
     * TSVファイルを読む
     * 
     * @param type $file
     * @param type $info
     * @param type $description
     * @param type $index
     * @return boolean
     */
    public function exercuteTsvFile($file, $info, $description, $index) {
        $fileName = time() . $index . '.html';
        $target = $this->getFileFolder() . "\\" . $fileName;
        $this->adapter->addFilter('Rename', array('target' => $target,
            'overwrite' => true));
        if (!$this->adapter->receive($file)) {
            return false;
        } else {
            if ($this->tsvFileToTest($fileName, $info, $description)) {
                return true;
            } else {
                $lineCount = array_shift(array_values($this->lineCount));
                
                //
                if (isset($lineCount)) {
                    $this->lastError = $info['name']."の行".$lineCount."にエラーが発生：".$this->lastError;
                } else {
                    $this->lastError = $info['name']."の終わりが無効：".$this->lastError;
                }
                return false;
            }
        }
    }
    
    /**
     * TSVファイルをテスト内容に更新する
     * 
     * @param type $fileName
     * @param type $info
     * @param type $description
     * @return boolean
     */
    public function tsvFileToTest($fileName, $info, $description) {
        $target = $this->getFileFolder() . "\\" . $fileName;
        $this->tmp = file($target);
        $this->fileToLines();
        
        $title = $this->readTestTitle();
        if ($title == null) {
//            die("title");
            return false;
        }
        
        $subtitle = $this->readTestSubtitle();
        if ($subtitle == null) {
//            die("sub title");
            return false;
        }
        
        $questions = array();
        $questionIndex = 1;
        
        do {
            $question = $this->readQuestion($questionIndex);
            
            if ($question == null) {
//                die($questionIndex.": Question read error");
                return false;
            }
            if ($question["question"]!=null) {
                $questions[] = $question;
                $questionIndex ++;
            }
        } while ($question["question"]!=null);
        
        $html = $this->createTestHtml($questions);
        file_put_contents($target, $html);
        
        $this->fileSaved[] = array(
            "type" => self::$FILE_TYPE_TEST,
            "filename" => $info['name'],
            "description" => $description,
            "title" => $title,
            "subtitle" => $subtitle,
            "location" => $fileName,
            "questions" => $questions
        );
        
        return true;
    }
    
    /**
     * TSVファイルを行リストに分ける
     */
    protected function fileToLines() {
        $firstLine = $this->tmp[0];
//        var_dump($firstLine);
        $firstLine = $this->remove_utf8_bom($firstLine);
//        var_dump($firstLine);
        $this->tmp[0] = $firstLine;
        
        $this->lines = array();
        $this->lineCount = array();
        $this->lineCount[0] = "Not use";
        
        $count = 0;
        foreach ($this->tmp as $lineNum => $line) {
            $lineC = explode("#", $line);
            $line = $lineC[0];
            $count ++;

            $words = preg_split("/[\t\n\r]/", $line);
            foreach ($words as $index => $word) {
                if (strlen($word) == 0) {
                    unset($words[$index]);
                }
            }
            if (count($words) != 0) {
                $this->lines[] = $words;
                $this->lineCount[] = $count;
            }
        }
    }
    
    function remove_utf8_bom($text)
    {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
    
    /**
     * テストタイトルを読む
     * 
     * @return string
     */
    protected function readTestTitle() {
        $nextLine = $this->nextLine();
        
        if (($nextLine[0] != self::$TSV_KEY_TITLE)) {
            $this->lastError = self::$TSV_KEY_TITLE."キーワードが必要";
            return null;
        }
        
        if (!isset($nextLine[1])) {
            $this->lastError = "タイトルが無効";
            return null;
        }
        
        return $nextLine[1];
    }
    
    /**
     * テストサブタイトルを読む
     * 
     * @return string
     */
    protected function readTestSubtitle() {
        $nextLine = $this->nextLine();
        
        if (($nextLine[0] != self::$TSV_KEY_SUBTITLE)) {
            $this->lastError = self::$TSV_KEY_SUBTITLE."キーワードが必要";
            return null;
        }
        
        if (!isset($nextLine[1])) {
            $this->lastError = "サブタイトルが無効";
            return null;
        }
        
        return $nextLine[1];
    }
    
    /**
     * テストの質問を読む
     * 
     * @param type $questionIndex
     * @return null
     */
    protected function readQuestion($questionIndex) {
        $nextLine = $this->nextLine();
        
        // Check finished
        if ($nextLine[0] == self::$TSV_KEY_END) {
            if (isset($nextLine[1])) {
                return null;
            }
            if (($this->nextLine())) {
                return null;
            }
            return array("question" => null);
        }
        
        if (!isset($nextLine[1]) || !isset($nextLine[2])) {
            $this->updateTsvError("質問が無効");
            return null;
        }
        
        $question = array();
        $question["index"] = $questionIndex;
        if ($nextLine[0] == "Q(".$questionIndex.")" && $nextLine[1] = "QS") {
            $question["question"] = $nextLine[2];
        } else {
            $this->updateTsvError("質問が無効、「Q(".$questionIndex.") QS」が必要");
            return null;
        }
        
        $question["answers"] = array();
        $answerCount = 1;
        do {
            $nextLine = $this->nextLine();
            if (!isset($nextLine[1]) || !isset($nextLine[2])) {
                $this->updateTsvError("答えが無効");
                return null;
            }
            if ($nextLine[0] != "Q(".$questionIndex.")") {
                $this->updateTsvError("答えが無効、「Q(".$questionIndex.")」が必要");
                return null;
            }
            if ($nextLine[1] == "S(".$answerCount.")") {
                $question["answers"][] = $nextLine[2];
            }
            $answerCount++;
        } while($nextLine[1]=="S(".($answerCount-1).")");
        
        if (!isset($nextLine[1]) || !isset($nextLine[2]) || !isset($nextLine[3])) {
            $this->updateTsvError("質問の正しい答えが無効");
            return null;
        }
        if ($nextLine[1] != "KS") {
            $this->updateTsvError("答えが無効、「KS」キーワードが必要");
            return null;
        }
        $p = preg_split("/[()]/", $nextLine[2]);
        
        if (!isset($p[1])) {
            $this->updateTsvError("質問の正しい答えが無効");
            return null;
        }
        if (!$this->isNumber($p[1])) {
            $this->updateTsvError("質問の正しい答えが無効");
            return null;
        }
        if ($p[1] > $answerCount-1) {
            $this->updateTsvError("質問の正しい答えが無効");
            return null;
        }
        $question["trueAnswer"] = $p[1] + 0;
        
        
        $question["point"] = $nextLine[3];
        if (!$this->isNumber($nextLine[3])) {
            $this->updateTsvError("質問の点数が無効");
            return null;
        }
        
        return $question;
    }
    
    /**
     * TSVファイルの次の行を取る
     * 
     * @return type
     */
    protected function nextLine() {
        $nextLine = array_values($this->lines);
        $nextLine = $nextLine[0];
        $nextLineIndex = array_keys($this->lines);
        $nextLineIndex = $nextLineIndex[0];
        unset($this->lines[$nextLineIndex]);
        unset($this->lineCount[$nextLineIndex]);
        return $nextLine;
    }
    
    /**
     * テストのHtmlを作成する
     * 
     * @param type $questions
     * @return string
     */
    protected function createTestHtml($questions) {
        $testHtml = "<div class='test_container'>\n";
        foreach ($questions as $question) {
            $questionHtml = $this->createQuestionHtml($question);
            $testHtml .= $questionHtml;
        }
        $testHtml .= "</div>";
        
        return $testHtml;
    }
    
    /**
     * 質問Htmlを作成
     * 
     * @param type $question
     * @return string
     */
    protected function createQuestionHtml($question) {
        $qIndex = $question["index"];
        $content = $question["question"];
        $point = $question["point"];
        $trueAnswer = $question["trueAnswer"];
        
        $questionHtml = "<hr>";
        $questionHtml .= "<div class='question_container Q".$qIndex."'>\n";
        $questionHtml .= "\t<div class='question'>Q".$qIndex.": ".$content."(".$point."点)</div>\n";
        $questionHtml .="\t<div class='answers'>\n";
        
        foreach ($question["answers"] as $index => $answer) {
            $questionHtml .= "\t\t<input class='S".($index+1)."' type='radio' name='Q[".$qIndex."]' value='".($index + 1)."' >".$answer."<br>\n";
        }
        
        $questionHtml .= "\t</div>\n";
        
        $questionHtml .= "<div class='result-true'>正しい</div>";
        $questionHtml .= "<div class='result-false'>正しくない</div>";
        
        $questionHtml .= "\n</div>";
        
        return $questionHtml;
    }
    
    /**
     * ファイルデータを作成
     * 
     * @param type $lessonId
     */
    public function createFilesData($lessonId) {
        
        // Create lesson folder
        $fileFolder = $this->getFileFolder() . "\\";
        $lessonFolder = $fileFolder . $lessonId;
        if (!file_exists($lessonFolder)) {
            mkdir($lessonFolder, 0777, true);
        }
        
        $questionModel = new Default_Model_Question();
        
        foreach ($this->fileSaved as $fileInfo) {
            // Save file info
            $insertData = array(
                self::$LESSON_ID => $lessonId,
                self::$FILENAME => $fileInfo['filename'],
                self::$DESCRIPTION => $fileInfo['description'],
                self::$TITLE => $fileInfo['title'],
                self::$SUBTITLE => $fileInfo['subtitle'],
                self::$LOCATION => "\\".$lessonId."\\".$fileInfo['location']
            );
            $fileId = $this->insert($insertData);
            
            // Save questions
            if ($fileInfo['type'] == self::$FILE_TYPE_TEST) {
                foreach($fileInfo['questions'] as $question) {
                    $questionModel->createQuestion($fileId, $question);
                }
            }
            
            // Copy file to lesson folder
            copy($fileFolder.$fileInfo['location'], $lessonFolder."/".$fileInfo['location']);
            unlink($fileFolder.$fileInfo['location']);
        }
    }
    
    /**
     * ファイルを取る
     * 
     * @param int $fileId
     * @return array
     */
    public function findFileById($fileId) {
        $result = $this->fetchRow("id='$fileId'");
        if ($result) {
            $result = $result->toArray();
            $result['is_reported'] = $this->isReported($result);
            return $result;
        } else {
            return null;
        }
    }
    
    /**
     * テストHtmlを取る
     * 
     * @param int $testId
     * @return string
     */
    public function getTestHtml($testId) {
        $fileInfo = $this->findFileById($testId);
        $fileLines = file($this->getFileFolder() . $fileInfo['location']);
        return implode("\n", $fileLines);
    }
    
    /**
     * 授業のファイルリストを取る
     * 
     * @param int $lessonId
     * @return array
     */
    public function getFileByLesson($lessonId) {
        $select = $this->getAdapter()->select();
        $select->from($this->_name, "*")
                ->where('lesson_id='.$lessonId." AND status!=3");
        $result = $this->getAdapter()->fetchAll($select);
        foreach ($result as $index => $file) {
            $result[$index]['is_reported'] = $this->isReported($file);
        }
        return $result;
    }
    
    /**
     * このファイルがレポートしたかどうかをチェック
     * 
     * @param array $file
     * @return boolean
     */
    public function isReported($file) {
        $select = $this->getAdapter()->select();
        $select->from('copyright_report', "*")
                ->where("file_id=".$file['id']." and status=1");
        $result = $this->getAdapter()->fetchAll($select);
        if ($result) {
            return TRUE;
        }
        return FALSE;
    }
    
    //thiennx check user can see the file
    public function checkUserCanSeeFile($userId, $fileId){
    	$select = $this->getAdapter()->select()
    			->from($this->_name)
    			->join("learn", "learn.lesson_id = lesson_file.lesson_id")
    			->where("lesson_file.id = ?", $fileId)
    			->where("student_id = ?", $userId)
    			->where("learn.register_time + INTERVAL ".$this->_lessonDeadline." DAY >= NOW() ");
    	return $this->getAdapter()->fetchAll($select);
    }
    
    public function editFile($fileId, $description) {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        
        $currentFile = $this->findFileById($fileId);
        $fileFolder = $this->getFileFolder();
        
        var_dump($fileId);
        var_dump($description);
//        die();
        
        $this->fileSaved = array();
        if ($this->adapter == null) {
             $this->adapter = new Zend_File_Transfer_Adapter_Http();
        }
        
        if ($this->adapter->isUploaded('file')) {
            $file = $this->adapter->getFileInfo();
            $fileInfo = $file['file'];
            if ($this->exercuteFile('file', $fileInfo , $description, 0) == FALSE ){
                return false;
            }
            $fileSavedInfo = $this->fileSaved[0];
            $fileData = array(
                self::$FILENAME => $fileSavedInfo['filename'],
                self::$DESCRIPTION => $fileSavedInfo['description'],
                self::$TITLE => $fileSavedInfo['title'],
                self::$SUBTITLE => $fileSavedInfo['subtitle'],
                self::$LOCATION => "\\".$currentFile['lesson_id']."\\".$fileSavedInfo['location']
            );
            
            var_dump($fileData);
            
            // 現在のファイルを削除
            unlink($fileFolder.$currentFile['location']);
            copy($fileFolder."\\".$fileSavedInfo['location'], $fileFolder.$fileData['location']);
            unlink($fileFolder."\\".$fileSavedInfo['location']);
            
            $this->update($fileData, "id=".$fileId);
            return true;
        }
    }
    
    /**
     * ファイルをロック処理
     * 
     * @param int $fileId
     * @return boolean
     */
    public function lockFile($fileId) {
        $userModel = new Default_Model_Account();
        $file = $this->findFileById($fileId);
        if (!$file) {
            return false;
        }
        if ($file['status'] == 2) {
            return true;
        }
        $result = $this->update(array('status'=>'2'), "id=".$fileId);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * ファイルをアンロック処理
     * 
     * @param int $fileId
     * @return boolean
     */
    public function unlockFile($fileId) {
        $file = $this->findFileById($fileId);
        if (!$file) {
            return false;
        }
        if ($file['status'] == 1) {
            return true;
        }
        $result = $this->update(array('status'=>'1'), "id=".$fileId);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * ファイルを削除処理
     * 
     * @param int $fileId
     * @return boolean
     */
    public function deleteFile($fileId) {
        $file = $this->findFileById($fileId);
        if (!$file) {
            return false;
        }
        $result = $this->delete("id=".$fileId);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    protected function updateTsvError($errorStr) {
        $this->lastError = $errorStr;
    }

        /**
     * エラーを取る
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * 文字列が数字かどうかをチェック
     * 
     * @param string $string
     * @return boolean
     */
    protected function isNumber($string) {
        return ctype_digit($string);
    }
}
?>