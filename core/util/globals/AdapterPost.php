<?php
namespace core\util\globals;

class AdapterPost implements GlobalsInterface
{
    public function get($indexName, $defaultValue)
    {
        return $_POST[$indexName] ?? $defaultValue;
    }

    public function set($indexName, $value)
    {
        return $_POST[$indexName] = $value;
    }
}
