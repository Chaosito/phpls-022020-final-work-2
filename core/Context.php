<?php
namespace core;

use core\models\CurrentUser;
use core\models\User;

class Context
{
    private static $instance;
    private $request;
    private $router;
    private $curUser;
    
    private function __construct()
    {
    }
    private function __clone()
    {
    }
    
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
    
    public function getRouter(): Router
    {
        return $this->router;
    }
    
    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    public function getCurrentUser(): CurrentUser
    {
        return $this->curUser;
    }

    public function setCurrentUser(CurrentUser $curUser): void
    {
        $this->curUser = $curUser;
    }

    public function getProjectPath()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
    }
}
