<?php
namespace core\util\globals;

class GlobalsFactory
{
    /** @var GlobalsInterface */
    private static $adapter;

    public static function init(GlobalsInterface $adapter)
    {
        self::$adapter = $adapter;
    }

    public static function get($indexName, $defaultValue = null)
    {
        return self::$adapter->get($indexName, $defaultValue);
    }

    public static function set($indexName, $value = null)
    {
        return self::$adapter->set($indexName, $value);
    }

    public static function remove($indexName)
    {
        if (method_exists(self::$adapter, __FUNCTION__)) {
            return self::$adapter->remove($indexName);
        } else {
            return self::set($indexName, null);
        }
    }
}
