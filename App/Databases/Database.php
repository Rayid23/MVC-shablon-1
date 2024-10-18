<?php

namespace App\Databases;

class Database
{
    protected static $localhost = "localhost";
    protected static $dbname = "interfaces";
    protected static $root = "root";
    protected static $password = "";

    protected static function NewConnect(){
        return new \PDO("mysql:localhost=". self::$localhost .";dbname=". self::$dbname, self::$root, self::$password);
    }
}

?>