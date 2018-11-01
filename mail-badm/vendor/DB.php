<?php

namespace vendor;


class DB
{
    public static $instance = null;

    public static $pdo;

    protected function __construct()
    {
        $db = require $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
        $option = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];
        $this->pdo = new \PDO($db['dsn'], $db['user'], $db['psw'], $option);
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function instance()
    {
        if (self::$instance !== null) return self::$instance;
        return self::$instance = new self();
    }

}