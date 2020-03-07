<?php
namespace core;

use \core\exceptions\Error404;
use core\models\CurrentUser;

class Application
{
    /** @var Context */
    private $context;
    
    protected function init()
    {
        $request = new Request();
        $router = new Router();
        $curUser = new CurrentUser();
        $this->context = Context::getInstance();
        $this->context->setRequest($request);
        $this->context->setRouter($router);
        $this->context->setCurrentUser($curUser);
    }
    
    public function run()
    {
        try {
            $this->init();

            $router = $this->context->getRouter();
            $router->route();
            
            $controllerFileName = 'app\controllers\\'.$router->getControllerName();
            
            if (!class_exists($controllerFileName)) {
                throw new Error404("Контроллер `{$controllerFileName}` не найден!");
            }

            /** @var Controller $controllerObj */
            $controllerObj = new $controllerFileName();
            $actionMethodName = $router->getActionName();

            if (!method_exists($controllerObj, $actionMethodName)) {
                throw new Error404("Метод `{$actionMethodName}` не найден в контроллере `{$controllerFileName}`!");
            }

            $view = new View();
            $view->setWrapperPath("../app/views/MainWrapper.phtml");
            $view->setTemplatePath("../app/views/".$router->getControllerName()."/".$router->getActionToken().".phtml");

            $controllerObj->view = $view;
            $controllerObj->doFirst();
            $controllerObj->$actionMethodName();

            $view->setPageTitle($controllerObj->getPageTitle());

            if ($controllerObj->needRender()) {
                echo $view->render();
            }
        } catch (Error404 $e) {
            header('HTTP/1.0 404 Not Found');
            header('Location: /404.html');
        } catch (\Exception $e) {
            header('HTTP/1.0 404 Not Found');
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

    public function getPath()
    {
    }
}
