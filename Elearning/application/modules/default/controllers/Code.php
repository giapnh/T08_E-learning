<?php

class Code {

    public static $REGEX_USERNAME = "/^[a-zA-Z][A-Za-z0-9._-]{5,29}$/";
    public static $REGEX_PASSWORD = "/^[a-zZ-Z][A-Za-z0-9._-]{5,29}$/";
    public static $REGEX_IP = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";
    public static $LOCK_COUNT = 5; //ログイン間違い回数
    public static $SESSION_TIME = 86400; //自動ログアウト時間（時）
    public static $LOGIN_FAIL_LOCK_TIME = 3600; //ロック時間
    public static $COMA_PRICE = 20000; //学生が使用する場合、１コマにつき暫定２万ドン。
    public static $TEACHER_FEE_RATE = 60; //先生の課金率（%）
    public static $LESSON_DEADLINE = 10; //授業の期限（日）
    public static $VIOLATION_TIME = 10; //資料の禁止回数
    public static $FILE_LOCATION = "E:\Documents"; //課金情報を格納するフォルダ
    public static $BACKUP_TIME = 30; //自動バックアップ期間（日）
    public static $PASSWORD_CONST = 100;

}
