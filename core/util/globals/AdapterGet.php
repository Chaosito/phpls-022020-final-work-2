<?php
namespace core\util\globals;

class AdapterGet implements GlobalsInterface
{
    public function get($indexName, $defaultValue)
    {
        return $_GET[$indexName] ?? $defaultValue;
    }

    public function set($indexName, $value)
    {
        return $_GET[$indexName] = $value;
    }
}
