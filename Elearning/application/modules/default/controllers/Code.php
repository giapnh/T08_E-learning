<?php

class Code {

    public static $REGEX_USERNAME = "/^[a-zA-Z][A-Za-z0-9._-]{5,29}$/";
    public static $REGEX_PASSWORD = "/^[A-Za-z0-9._-]{5,29}$/";
    public static $REGEX_IP = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";

}
