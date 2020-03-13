<?php
namespace app\controllers;

use core\Context;
use core\util\globals\AdapterPost;
use core\util\globals\GlobalsFactory;
use app\emodels\User as EUser;

class Admin extends \core\Controller
{
    public function indexAction()
    {
        $this->pageTitle = 'Admin page';
        $users = EUser::query()->orderBy('id')->get();
        $this->view->users = $users;
    }

    public function saveusersAction()
    {
        $this->render = false;

        GlobalsFactory::init(new AdapterPost());
        if (empty(GlobalsFactory::get('mail'))) {
            Context::getInstance()->getRouter()->redirectTo('/admin');
        }

        $arrMails = GlobalsFactory::get('mail');
        $arrNames = GlobalsFactory::get('first_name');
        $arrDescriptions = GlobalsFactory::get('description');
        $arrBirthdates = GlobalsFactory::get('birthdate');
        $arrPasswords = GlobalsFactory::get('password');
        $arrForRemove = GlobalsFactory::get('remove');

        foreach($arrMails as $key => $mail) {
            // Здесь должна быть просто тьма проверок, на существование такого юзера, емаила, правильность пароля итд
            // Но проект у нас тестовый, так что для ускорения сделаем все по простому

            if ($key === (int)$key) {
                $user = new EUser();
                $user->mail = $mail;
                $user->first_name = $arrNames[$key];
                $user->description = $arrDescriptions[$key];
                $user->birthdate = $arrBirthdates[$key];
                $user->setNewPassword($arrPasswords[$key]);
                $user->save();
            } else {
                $userId = str_replace('user_', '', $key);
                if ($arrForRemove[$key] > 0) {
                    EUser::destroy($userId);
                } else {
                    $user = EUser::find($userId);
                    if ($user) {
                        $user->mail = $mail;
                        $user->first_name = $arrNames[$key];
                        $user->description = $arrDescriptions[$key];
                        $user->birthdate = $arrBirthdates[$key];
                        if ($arrPasswords[$key] != '') {
                            $user->setNewPassword($arrPasswords[$key]);
                        }
                        $user->save();
                    }
                }
            }
        }
        Context::getInstance()->getRouter()->redirectTo('/admin');
    }
}
