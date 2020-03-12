<?php
namespace core;

use \core\exceptions\Error404;
use core\models\CurUser;
use Illuminate\Database\Capsule\Manager as Capsule;

class Application
{
    /** @var Context */
    private $context;

    protected function initDb()
    {
        /* Eloquent connection settings & initialize */
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => Settings::MYSQL_HOST,
            'database'  => Settings::MYSQL_DB,
            'username'  => Settings::MYSQL_USER,
            'password'  => Settings::MYSQL_PASS,
            'charset'   => Settings::MYSQL_CHAR,
            'collation' => 'utf8_general_ci',
            'prefix'    => '',
        ]);

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
    }


    protected function init()
    {
        $request = new Request();
        $router = new Router();
        $this->context = Context::getInstance();
        $this->context->setRequest($request);
        $this->context->setRouter($router);
        $this->initDb();
        $curUser = CurUser::init();
        if ($curUser) {
            $this->context->setCurrentUser($curUser);
        }
    }
    
    public function run()
    {
        $this->init();

        $router = $this->context->getRouter();
        $router->route();
        $controllerFileName = $router->getControllerPath();

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

        //connects header & footer via this theme
        //$view->setTheme('default');

        $controllerObj->view = $view;
        $controllerObj->doFirst();
        $controllerObj->$actionMethodName();

        $view->setPageTitle($controllerObj->getPageTitle());

        if ($controllerObj->needRender()) {
            echo $view->render();
        }
    }

    public function getPath()
    {
    }
}
