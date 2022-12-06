<?php

namespace App\Utils;

class MongoConnector
{
    /**
     * @var array $conf configuration of database
     */
    private static array $conf;
    /**
     * @var \MongoDB\Database $db  Mongo database
     */
    private static \MongoDB\Database $db;


    public static function setConfig(string $path): bool
    {
        if(file_exists($path)){
            self::$conf = parse_ini_file($path);
            return true;
        }
        return false;
    }

    /**
     * @throws \Exception
     * @return \MongoDB\Database $db
     */
    public static function makeConnection(): \MongoDB\Database
    {
        if (!isset(self::$conf)) {
            throw new \Exception("No configuration file found");
        }
        if (!isset($db)) {
           // $client = new \MongoDB\Client('mongodb://' . self::$conf['user'] . ':' . self::$conf['password'] . '@' . self::$conf['host'] . ':' . self::$conf['port']);
            $client = new \MongoDB\Client('mongodb://' . self::$conf['host'] . ':' . self::$conf['port']);
            self::$db = $client->selectDatabase(self::$conf['database']);

        }
        return self::$db;
    }


}