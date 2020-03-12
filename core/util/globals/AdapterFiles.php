<?php
namespace core\util\globals;

class AdapterFiles implements GlobalsInterface
{
    public function get($indexName, $defaultValue)
    {
        return $_FILES[$indexName] ?? $defaultValue;
    }

    public function set($indexName, $value)
    {
        return $_FILES[$indexName] = $value;
    }
}