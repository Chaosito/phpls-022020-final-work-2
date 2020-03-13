<?php
namespace app\controllers;

use core\util\globals\AdapterGet;
use core\util\globals\GlobalsFactory;

class Admin extends \core\Controller
{
    public function indexAction()
    {
        $this->pageTitle = 'Admin page';        GlobalsFactory::init(new AdapterGet());
        $users = \app\emodels\User::query()->orderBy('id')->get();

        $this->view->users = $users;
    }
}
