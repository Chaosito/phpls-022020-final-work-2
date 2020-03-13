<?php
namespace core;

class Router
{
    const DEFAULT_CONTROLLERS_PATH = 'app\controllers\\';
    const DEFAULT_CONTROLLER = 'main';
    const DEFAULT_ACTION = 'index';
    
    private $controllerName;
    private $actionToken;
    
    protected function getRoutes()
    {
        return [
            'Login' => [
                'index' => 'User.login'
            ],
            'Register' => [
                'index' => 'User.register'
            ],
            'Logout' => [
                'index' => 'User.logout'
            ],
            'Profile' => [
                'index' => 'User.profile'
            ],
            'Admin' => [
                'index' => 'Admin.index'
            ],
        ];
    }
    
    public function route()
    {
        $request = Context::getInstance()->getRequest();
        
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        
        $this->controllerName =
            (!$this->checkParam($controllerName)) ? self::DEFAULT_CONTROLLER : ucfirst(strtolower($controllerName));
        
        $this->actionToken =
            (!$this->checkParam($actionName)) ? self::DEFAULT_ACTION : strtolower($actionName);
            
        $routes = $this->getRoutes();
        $customRoute = $routes[$this->controllerName][$this->actionToken];
        
        if (isset($customRoute)) {
            list($this->controllerName, $this->actionToken) = explode(".", $customRoute, 2);
        }
    }
    
    private function checkParam(string $key)
    {
        return $key && preg_match('/[a-zA-Z0-9]+/', $key);
    }
    
    public function getControllerName()
    {
        return $this->controllerName;
    }

    public function getControllerPath()
    {
        return self::DEFAULT_CONTROLLERS_PATH.$this->getControllerName();
    }
    
    public function getActionName()
    {
        return $this->actionToken.'Action';
    }
    
    public function getActionToken()
    {
        return $this->actionToken;
    }
    
    public function redirectTo($toUrl)
    {
        header("Location: {$toUrl}");
        exit;
    }
}
