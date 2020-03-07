<?php
namespace core;

abstract class Controller
{
    /** @var View */
    public $view;
    public $curUser;
    protected $render = true;
    protected $jsonData = [];
    protected $pageTitle = '';

    public function __construct()
    {
        $this->curUser = Context::getInstance()->getCurrentUser();
        $this->pageTitle = 'Untitiled';
    }

    public function doFirst()
    {
        $this->view->curUser = $this->curUser;
    }
    
    abstract public function indexAction();

    public function needRender()
    {
        return $this->render;
    }

    public function getPageTitle()
    {
        return $this->pageTitle;
    }
    
    public function json()
    {
        $this->render = false;
        header('Content-Type: application/json');
        echo json_encode($this->jsonData);
        die();
    }
}
