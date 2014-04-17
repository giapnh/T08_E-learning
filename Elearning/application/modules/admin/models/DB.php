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
        $resources = $bootstrap->getOption('resources');
        $host = $resources['db']['params']['host'];
        $user = $resources['db']['params']['username'];
        $pass = $resources['db']['params']['password'];
        $name = $resources['db']['params']['dbname'];
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
     * 
     * @param int $page
     * @param int $itemPerPage
     * @return array
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
    
    /**
     * バックアップしたファイルリストを取る
     * 
     * @param int $page
     * @param int $itemPerPage
     * @return array
     */
    public function getBackupFilesPager($page, $itemPerPage) {
        $filesList = scandir(self::$BACKUP_DIR);
        
        foreach($filesList as $index => $file) {
            if (strpos($file, self::$DB_BACKUP_PREFIX) === FALSE) {
                unset($filesList[$index]);
            }
        }
        
        // データ
        $pagerData = array_slice($filesList, ($page-1)*$itemPerPage, $itemPerPage);
        
        // 次
        if (count($filesList) > $page * $itemPerPage) {
            $next = true;
        } else {
            $next = false;
        }
        
        // 前
        if ($page > 1) {
            $pre = true;
        } else {
            $pre = false;
        }
        $total = ceil(count($filesList) / $itemPerPage);
        
        return array(
            "data" => $pagerData,
            "next" => $next,
            "pre" => $pre,
            "currentPage" => $page,
            "total" => $total
        );
    }
    
    /**
     * データベースを回復
     * 
     * @param string $file
     */
    public function restore($file) {
        $filePath = self::$BACKUP_DIR.'/'.$file;
        
        // データベース設定をとる
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $resources = $bootstrap->getOption('resources');
        $host = $resources['db']['params']['host'];
        $user = $resources['db']['params']['username'];
        $pass = $resources['db']['params']['password'];
        $dbName = $resources['db']['params']['dbname'];
        
        $mysqli = new mysqli($host, $user, $pass, $dbName);
        mysqli_set_charset($mysqli, 'utf8');

        /* check connection */
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        
        $query = file_get_contents($filePath);

        if ($mysqli->multi_query($query)) {
            return true;
        } else {
            return false;
        }
    }
    
}

