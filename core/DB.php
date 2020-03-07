<?php
namespace core;

/*
  Класс DB (Singleton)
  Работа с БД через PDO
*/

/**
 * @method static lastInsertId()
 */
class DB
{
    protected static $instance = null;

    public function __construct()
    {
    }
    public function __clone()
    {
    }

    public static function instance()
    {
        if (self::$instance === null) {
            $opt  = array(
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => true,
            );

            $opt[\PDO::ATTR_ERRMODE] = (Settings::DEBUG_MODE) ? \PDO::ERRMODE_EXCEPTION : \PDO::ERRMODE_SILENT;

            $dsn = 'mysql:host='.Settings::MYSQL_HOST.';dbname='.Settings::MYSQL_DB.';charset='.Settings::MYSQL_CHAR;
            self::$instance = new \PDO($dsn, Settings::MYSQL_USER, Settings::MYSQL_PASS, $opt);
            self::$instance->exec("SET NAMES ".Settings::MYSQL_CHAR);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public static function run($sql, $args = array())
    {
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}
