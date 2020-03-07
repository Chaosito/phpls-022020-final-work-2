<?php
namespace core;

class Request
{
    private $controllerName;
    private $actionName;
    
    public function __construct()
    {
        $parts = explode('/', $_SERVER['REQUEST_URI']);
        $this->controllerName = $parts[1] ?? '';
        $this->actionName = $parts[2] ?? '';
    }
    
    public function getControllerName()
    {
        return $this->controllerName;
    }
    
    public function getActionName()
    {
        return $this->actionName;
    }
    
    public function getRequestHttpVars()
    {
        return $_REQUEST;
    }
    
    public function getFilesHttpVars()
    {
        return $_FILES;
    }
}
