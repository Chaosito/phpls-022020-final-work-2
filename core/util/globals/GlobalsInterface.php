<?php
namespace core\util\globals;

interface GlobalsInterface
{
    public function get($indexName, $defaultValue);
    public function set($indexName, $value);
}
