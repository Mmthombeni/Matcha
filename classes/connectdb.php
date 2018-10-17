<?php

class connectDB
{
    private static $_instance = null;
    private static $_handler = null;

    private function __construct(){}
    
    public static function getInstance()
    {
        if (self::$_instance == null)
            self::$_instance == new connectDB();
        return self::$_instance;
    }

    public function getHandler($DB_DSN, $DB_NAME, $DB_USER, $DB_PASSWORD)
    {
        if (self::$_handler == null)
        {
            self::$_handler = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
            self::$_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$_handler;
    }
}