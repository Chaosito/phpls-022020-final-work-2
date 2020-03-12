<?php
namespace core\util\globals;

class AdapterCookie implements GlobalsInterface
{
    public function get($indexName, $defaultValue)
    {
        return $_COOKIE[$indexName] ?? $defaultValue;
    }

    public function set($indexName, $value)
    {
        return setcookie($indexName, $value, time() + \core\Settings::COOKIE_LIFE_TIME, "/");
    }

    public function remove($indexName)
    {
        setcookie($indexName, '', 0, "/");
    }

}