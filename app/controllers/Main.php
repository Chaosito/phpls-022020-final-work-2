<?php
namespace app\controllers;

use core\Context as CoreContext;

class Main extends \core\Controller
{
    public function indexAction()
    {
        $this->pageTitle = 'Main page';
    }

    public function allusersAction()
    {
        $requestVars = CoreContext::getInstance()->getRequest()->getRequestHttpVars();

        /** @var \app\models\User $objUser */
        $objUser = new \app\models\User();
        $users = $objUser->getAllUsers($requestVars['sort']);
        $this->view->users = $users;
        $this->view->sorting = $requestVars['sort'];
    }
}
