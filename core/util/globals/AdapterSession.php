<?php
namespace core\util\globals;

class AdapterSession implements GlobalsInterface
{
    public function get($indexName, $defaultValue)
    {
        return $_SESSION[$indexName] ?? $defaultValue;
    }

    public function set($indexName, $value)
    {
        return $_SESSION[$indexName] = $value;
    }

    public function remove($indexName)
    {
        unset($_SESSION[$indexName]);
    }
}
