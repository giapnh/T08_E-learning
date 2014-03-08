<?php

require_once '/../controllers/Code.php';

class Default_Model_File extends Zend_Db_Table_Abstract {
    protected $_name = "lesson_file";
    protected $_primary = "id";
    protected $db;
    protected $adapter = null;
    protected $lessonId;
    protected $tmp;
    protected $lines;

    public static $UPLOAD_DIR = '\..\files\\';
    
    public static $ID = "id";
    public static $LESSON_ID = "lesson_id";
    public static $FILENAME = "filename";
    public static $DESCRIPTION = "description";
    public static $LOCATION = "location";
    public static $TSV_KEY_TITLE = "TestTitle";
    public static $TSV_KEY_SUBTITLE = "TestSubTitle";
    public static $TSV_KEY_END = "End";

    public function exercuteFiles($lessonId, $fileDescriptions) {
        $this->lessonId = $lessonId;
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
        
        return true;
    }
    
    public function exercuteFile($file, $info, $description, $index) {
        $ext = $this->_findexts($info['name']);
        if ($ext == "tsv") {
            return $this->exercuteTsvFile($file, $info, $description, $index);
        } else if ($ext == "mp3" || $ext == "pdf" || $ext == "png" || $ext == "jpg") {
            return $this->exercuteNormalFile($file, $info, $description, $index);
        } else {
            return false;
        }
    }

    protected function _findexts($filename) {
        $filename = strtolower($filename);
        $exts = explode(".", $filename);
        $n = count($exts) - 1;
        $exts = $exts[$n];
        return $exts;
    }
    
    protected function exercuteNormalFile($file, $info, $description, $index) {
        $ext = $this->_findexts($info['name']);
        $fileName = time() . $index . '.' . $ext;

        $target = APPLICATION_PATH . self::$UPLOAD_DIR . $fileName;
        $this->adapter->addFilter('Rename', array('target' => $target,
            'overwrite' => true));
        if (!$this->adapter->receive($file)) {
            $message = $this->adapter->getMessages();
            return false;
        } else {
            $insertData = array(
                self::$LESSON_ID => $this->lessonId,
                self::$FILENAME => $info['name'],
                self::$DESCRIPTION => $description,
                self::$LOCATION => $fileName
            );
            $this->insert($insertData);
            return true;
        }
    }

    public function exercuteTsvFile($file, $info, $description, $index) {
        $fileName = time() . $index . '.tsv';
        $target = APPLICATION_PATH . self::$UPLOAD_DIR . $fileName;
        $this->adapter->addFilter('Rename', array('target' => $target,
            'overwrite' => true));
        if (!$this->adapter->receive($file)) {
            $message = $this->adapter->getMessages();
            die($message);
            return false;
        } else {
            if ($this->tsvFileToTest($target)) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function tsvFileToTest($filename) {
        $this->tmp = file($filename);
        $this->fileToLines();
        
        $title = $this->readTestTitle();
        if ($title == null) {
            return false;
        }
        
        $subtitle = $this->readTestSubtitle();
        if ($subtitle == null) {
            return false;
        }
        
        $questions = array();
        $questionIndex = 1;
        
        do {
            $question = $this->readQuestion($questionIndex);
            
            var_dump($question);
            if ($question == null) {
                die();
                return false;
            }
            $questions[] = $question;
            $questionIndex ++;
        } while (count($question)!=0);
        
        // TODO: Save test info
        var_dump($questions);
        die();
    }
    
    protected function fileToLines() {
        $firstLine = $this->tmp[0];
        $this->tmp[0] = substr($firstLine, 3);
        $this->lines = array();
        
        foreach ($this->tmp as $lineNum => $line) {
            if ($line[0] != '#') {
                $words = preg_split("/[\t\n\r]/", $line);
                foreach ($words as $index => $word) {
                    if (strlen($word) == 0) {
                        unset($words[$index]);
                    }
                }
                if (count($words) != 0) {
                    $this->lines[] = $words;
                }
            }
        }
    }
    
    protected function readTestTitle() {
        $nextLine = $this->nextLine();
        
        if (($nextLine[0] == self::$TSV_KEY_TITLE) && isset($nextLine[1])) {
            return $nextLine[1];
        }
        
        return null;
    }
    
    protected function readTestSubtitle() {
        $nextLine = $this->nextLine();
        
        if (($nextLine[0] == self::$TSV_KEY_SUBTITLE) && isset($nextLine[1])) {
            return $nextLine[1];
        }
        
        return null;
    }
    
    protected function readQuestion($questionIndex) {
        $nextLine = $this->nextLine();
        if ($nextLine[0] == self::$TSV_KEY_END) {
            return array();
        }
        
        if (!isset($nextLine[1]) || !isset($nextLine[2])) {
            return null;
        }
        
        $question = array();
        if ($nextLine[0] == "Q(".$questionIndex.")" && $nextLine[1] = "QS") {
            $question["question"] = $nextLine[2];
        }
        
        $question["answers"] = array();
        $answerCount = 1;
        do {
            $nextLine = $this->nextLine();
            if (!isset($nextLine[1]) || !isset($nextLine[2])) {
                return null;
            }
            if ($nextLine[0] == "Q(".$questionIndex.")" && $nextLine[1] == "S(".$answerCount.")") {
                $question["answers"][] = $nextLine[2];
            }
            $answerCount++;
        } while($nextLine[1]=="S(".($answerCount-1).")");
        
        if ($nextLine[1] != "KS") {
            return null;
        }
        $question["trueAnswer"] = preg_split("/[()]/", $nextLine[2])[1] + 0;
        
        return $question;
    }
    
    protected function nextLine() {
        $nextLine = array_values($this->lines)[0];
        $nextLineIndex = array_keys($this->lines)[0];
        unset($this->lines[$nextLineIndex]);
        return $nextLine;
    }
}
?>