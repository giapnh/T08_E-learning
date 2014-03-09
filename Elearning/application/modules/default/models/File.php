<?php

require_once '/../controllers/Code.php';

class Default_Model_File extends Zend_Db_Table_Abstract {
    protected $_name = "lesson_file";
    protected $_primary = "id";
    protected $db;
    protected $adapter = null;
    protected $tmp;
    protected $lines;
    public $fileSaved;

//    public static $UPLOAD_DIR = '\..\files\\';
    public static $UPLOAD_DIR = 'files';
    
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

    public function __construct() {
        parent::__construct();
        $this->db = Zend_Registry::get('connectDB');
    }
    
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
    
    public function exercuteFile($file, $info, $description, $index) {
        $ext = $this->_findexts($info['name']);
        if ($ext == "tsv") {
            return $this->exercuteTsvFile($file, $info, $description, $index);
        } else if ($ext == "mp4" || $ext == "mp3" || $ext == "pdf" || $ext == "png" || $ext == "jpg") {
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

        $target = APPLICATION_PATH . "\\..\\" . self::$UPLOAD_DIR . "\\" .$fileName;
        $this->adapter->addFilter('Rename', array('target' => $target,
            'overwrite' => true));
        if (!$this->adapter->receive($file)) {
            $message = $this->adapter->getMessages();
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

    public function exercuteTsvFile($file, $info, $description, $index) {
        $fileName = time() . $index . '.html';
        $target = APPLICATION_PATH . "\\..\\" . self::$UPLOAD_DIR . "\\" . $fileName;
        $this->adapter->addFilter('Rename', array('target' => $target,
            'overwrite' => true));
        if (!$this->adapter->receive($file)) {
            $message = $this->adapter->getMessages();
//            die($message);
            return false;
        } else {
            if ($this->tsvFileToTest($fileName, $info, $description)) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function tsvFileToTest($fileName, $info, $description) {
        $target = APPLICATION_PATH . "\\..\\" . self::$UPLOAD_DIR . "\\" . $fileName;
        $this->tmp = file($target);
        var_dump($this->tmp);
        die();
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
            "location" => $fileName
        );
        
        return true;
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
            return array("question" => null);
        }
        
        if (!isset($nextLine[1]) || !isset($nextLine[2])) {
//            die("Format errro");
            return null;
        }
        
        $question = array();
        $question["index"] = $questionIndex;
        if ($nextLine[0] == "Q(".$questionIndex.")" && $nextLine[1] = "QS") {
            $question["question"] = $nextLine[2];
        }
        
        $question["answers"] = array();
        $answerCount = 1;
        do {
            $nextLine = $this->nextLine();
            if (!isset($nextLine[1]) || !isset($nextLine[2])) {
//                die("Answer error");
                return null;
            }
            if ($nextLine[0] == "Q(".$questionIndex.")" && $nextLine[1] == "S(".$answerCount.")") {
                $question["answers"][] = $nextLine[2];
            }
            $answerCount++;
        } while($nextLine[1]=="S(".($answerCount-1).")");
        
        if ($nextLine[1] != "KS") {
//            die("Ks error");
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
    
    protected function createTestHtml($questions) {
        $testHtml = "<div class='test_container'>\n";
        foreach ($questions as $question) {
            $questionHtml = $this->createQuestionHtml($question);
            $testHtml .= $questionHtml;
        }
        $testHtml .= "</div>";
        
        return $testHtml;
    }
    
    protected function createQuestionHtml($question) {
        $questionHtml = "<div class='question_container'>\n";
        $questionHtml .= "\t<div class='question'>Q".$question["index"].": ".$question["question"]."</div>\n";
        $questionHtml .="\t<div class='answers'>\n";
        
        foreach ($question["answers"] as $index => $answer) {
            $questionHtml .= "\t\t<input type='radio' name='Q".$question["index"]."' value='".($index + 1)."' >".$answer."<br>\n";
        }
        
        $questionHtml .= "\t</div>\n</div>\n";
        
        return $questionHtml;
    }
    
    public function createFilesData($lessonId) {
        $testModel = new Default_Model_Test();
        
        // Create lesson folder
        $fileFolder = APPLICATION_PATH . "\\..\\" . self::$UPLOAD_DIR . "\\";
        $lessonFolder = $fileFolder . $lessonId;
        if (!file_exists($lessonFolder)) {
            mkdir($lessonFolder, 0777, true);
        }
        
        foreach ($this->fileSaved as $fileInfo) {
            copy($fileFolder.$fileInfo['location'], $lessonFolder."/".$fileInfo['location']);
            unlink($fileFolder.$fileInfo['location']);
            
            $insertData = array(
                self::$LESSON_ID => $lessonId,
                self::$FILENAME => $fileInfo['filename'],
                self::$DESCRIPTION => $fileInfo['description'],
                self::$TITLE => $fileInfo['title'],
                self::$SUBTITLE => $fileInfo['subtitle'],
                self::$LOCATION => self::$UPLOAD_DIR."\\".$lessonId."\\".$fileInfo['location']
            );
            $this->insert($insertData);
        }
    }
}
?>