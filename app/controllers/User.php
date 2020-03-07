<?php
namespace app\controllers;

use core\Context as CoreContext;
use core\models\UserMapper;
use \core\Security as CoreSecurity;

class User extends \core\Controller
{
    public function indexAction()
    {
        $this->render = false;
        CoreContext::getInstance()->getRouter()->redirectTo('/');
    }

//    public function showallAction()
//    {
//        $this->jsonData = \core\DB::run("SELECT * FROM users")->fetchAll();
//        $this->json();
//    }
    
    public function loginAction()
    {
        $this->pageTitle = 'Sign In';
        if ($this->curUser->isLogged()) {
            CoreContext::getInstance()->getRouter()->redirectTo('/');
        }

        $requestVars = CoreContext::getInstance()->getRequest()->getRequestHttpVars();

        if (empty($requestVars['from_login'])) {
            return false;
        }

        $this->view->viewErrors = [];
        $this->view->mail = CoreSecurity::safeString($requestVars['mail']);

        if (empty($requestVars['mail']) || empty($requestVars['password'])) {
            $this->view->viewErrors = ['Не указан логин и/или пароль!'];
            return false;
        }

        $objUser = new \app\models\User();
        if (!$objUser->loadByMail($requestVars['mail'])) {
            $this->view->viewErrors = ['Пользователь с таким e-mail не зарегистрирован!'];
            return false;
        }

        if (!$objUser->matchPassword($requestVars['password'])) {
            $this->view->viewErrors = ['Введенный пароль не верен!'];
            return false;
        }

        $objUser->login((int)$requestVars['remember'] ? 1 : 0);
        CoreContext::getInstance()->getRouter()->redirectTo('/');
    }
    
    public function registerAction()
    {
        $this->pageTitle = 'Sign Up';
        if ($this->curUser->isLogged()) {
            CoreContext::getInstance()->getRouter()->redirectTo('/');
        }

        $this->view->viewErrors = [];
        $requestVars = CoreContext::getInstance()->getRequest()->getRequestHttpVars();
        $filesVars = CoreContext::getInstance()->getRequest()->getFilesHttpVars();

        if (!isset($requestVars['first_name'])) {
            return false;
        }

        /** @var \app\models\User $objUser */
        $objUser = new \app\models\User();

        /** @var \core\File $objFile */
        $objFile = new \core\File($filesVars['photo']);

        $validUserForm = $objUser->validateRegisterForm($requestVars);
        $validFile = false;
        if ($objFile->isSended()) {
            $validFile = $objFile->validate();
        }

        if (!$validUserForm || ($objFile->isSended() && !$validFile)) {
            $this->view->viewErrors = array_merge($objFile->getUploadErrors(), $objUser->getViewErrors());
            $this->view->first_name = CoreSecurity::safeString($requestVars['first_name']);
            $this->view->mail = CoreSecurity::safeString($requestVars['mail']);
            $this->view->birthdate = CoreSecurity::safeString($requestVars['birthdate']);   // validate date
            $this->view->description = CoreSecurity::safeString($requestVars['description']);
        } else {
            $objUser->fillFromForm($requestVars);
            $userMapper = new UserMapper();
            $userMapper->save($objUser);

            if ($objFile->isSended()) {
                $objFile->setOwnerId($userMapper->getId());
                $objFile->moveUploadedFile();
                $objFile->saveToDb();
            }
            // Можно сразу авторизовать, как на всех современных сайтах, но мы не будем этого делать
//            $objUser->login();
//            CoreContext::getInstance()->getRouter()->redirectTo('/');

            // Вместо этого перенаправим пользака на мучения - авторизацию
            CoreContext::getInstance()->getRouter()->redirectTo('/login');
        }
    }

    public function logoutAction()
    {
        $this->render = false;
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        
        session_regenerate_id(true); // Defend from 'Session fixation'

        setcookie('uMail', '', 0, "/");
        setcookie('uPass', '', 0, "/");

        CoreContext::getInstance()->getRouter()->redirectTo('/');
    }

    public function profileAction()
    {
        $this->pageTitle = 'My profile';
        /** @var \app\models\User $objUser */
        $objUser = new \app\models\User();
        $this->view->photos = $objUser->getUploadedFiles($this->curUser->getId());
    }
}
