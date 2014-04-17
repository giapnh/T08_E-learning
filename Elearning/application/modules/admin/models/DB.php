<?php

class Admin_Model_DB {
    
    public static $BACKUP_DIR = "backup";
    public static $DB_BACKUP_PREFIX = "db-backup-";

    /**
     * データベースをバックアップする
     * 
     * @return string
     */
    public function backup_tables()
    {
        // データベース設定をとる
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $host = $bootstrap->getOption('resources')['db']['params']['host'];
        $user = $bootstrap->getOption('resources')['db']['params']['username'];
        $pass = $bootstrap->getOption('resources')['db']['params']['password'];
        $name = $bootstrap->getOption('resources')['db']['params']['dbname'];
        $tables = '*';
        
        $link = mysql_connect($host,$user,$pass);
        mysql_select_db($name,$link);
        mysql_set_charset('utf8',$link);
        $return = "";

        //get all of the tables
        if($tables == '*')
        {
            $tables = array();
            $result = mysql_query('SHOW TABLES');
            while($row = mysql_fetch_row($result))
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        //cycle through
        foreach($tables as $table)
        {
            $result = mysql_query('SELECT * FROM '.$table);
//            var_dump($result);
            if ($result) {
                $num_fields = mysql_num_fields($result);

                $return.= 'DROP TABLE '.$table.';';
                $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
                $return.= "\n\n".$row2[1].";\n\n";

                for ($i = 0; $i < $num_fields; $i++) 
                {
                    while($row = mysql_fetch_row($result))
                    {
                        $return.= 'INSERT INTO '.$table.' VALUES(';
                        for($j=0; $j<$num_fields; $j++) 
                        {
                            $row[$j] = addslashes($row[$j]);
//                                            $row[$j] = preg_replace("\n","\\n",$row[$j]);
                            if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                            if ($j<($num_fields-1)) { $return.= ','; }
                        }
                        $return.= ");\n";
                    }
                }
                $return.="\n\n\n";
            }
        }

        //save file
        $now = getdate();
        $timeStr = $now['year']."-".$now['mon']."-".$now['mday']."-".$now['hours']."-".$now['minutes']."-".$now['seconds'];
        
        $filename = self::$BACKUP_DIR.'/'.self::$DB_BACKUP_PREFIX.$timeStr.'.sql';
        $handle = fopen($filename, 'w+');
        fwrite($handle, $return);
        fclose($handle);
        
        return $filename;
    }
    
    /**
     * バックアップしたファイルリストを取る
     * @return array Files list
     */
    public function getBackupFiles() {
        $filesList = scandir(self::$BACKUP_DIR);
        
        foreach($filesList as $index => $file) {
            if (strpos($file, self::$DB_BACKUP_PREFIX) === FALSE) {
                unset($filesList[$index]);
            }
        }
        return $filesList;
    }
}

