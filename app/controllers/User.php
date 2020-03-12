<?php
namespace app\controllers;

use core\Context as CoreContext;
use core\Datetime as CoreDatetime;
use core\models\CurUser;
use core\models\InputFile;
use \core\Security as CoreSecurity;
use core\util\globals\AdapterCookie;
use core\util\globals\AdapterFiles;
use core\util\globals\AdapterPost;
use core\util\globals\AdapterSession;
use core\util\globals\GlobalsFactory as GF;

class User extends \core\Controller
{
    const NAME_LENGTH_MIN = 2;
    const PASSWORD_LENGTH_MIN = 6;

    public function indexAction()
    {
        $this->render = false;
        CoreContext::getInstance()->getRouter()->redirectTo('/');
    }

    public function loginAction()
    {
        $this->pageTitle = 'Sign In';

        if ($this->curUser) {
            CoreContext::getInstance()->getRouter()->redirectTo('/');
        }

        GF::init(new AdapterPost());

        if (empty(GF::get('from_login'))) {
            return false;
        }

        $this->view->viewErrors = [];
        $this->view->mail = CoreSecurity::safeString(GF::get('mail'));

        if (empty(GF::get('mail')) || empty(GF::get('password'))) {
            $this->view->viewErrors = ['Не указан логин и/или пароль!'];
            return false;
        }

        $user = \app\emodels\User::where('mail', '=', GF::get('mail'))->first();
        if ($user === null) {
            $this->view->viewErrors = ['Пользователь с таким e-mail не зарегистрирован!'];
            return false;
        }

        if (!$user->matchPassword(GF::get('password'))) {
            $this->view->viewErrors = ['Введенный пароль не верен!'];
            return false;
        }

        // Login process
        if ((int)GF::get('remember')) {
            GF::init(new AdapterCookie());
            GF::set(CurUser::KEY_MAIL_COOKIE, $user->mail);
            GF::set(CurUser::KEY_PASS_COOKIE, $user->pass_hash);
        }
        GF::init(new AdapterSession());
        GF::set(CurUser::KEY_USER_SESSION, $user->id);

        CoreContext::getInstance()->getRouter()->redirectTo('/');
    }
    
    public function registerAction()
    {
        $this->pageTitle = 'Sign Up';

        if ($this->curUser) {
            CoreContext::getInstance()->getRouter()->redirectTo('/');
        }

        $this->view->viewErrors = [];

        GF::init(new AdapterFiles());
        $f = new InputFile(GF::get('photo'));
        $f->setAllowedExtensions(['jpg', 'jpeg', 'png']);
        GF::init(new AdapterPost());

        if (empty(GF::get('first_name'))) {
            return false;
        }

        // validate form
        $regFormErrorsForView = [];

        if (mb_strlen(GF::get('first_name')) < self::NAME_LENGTH_MIN) {
            $regFormErrorsForView[] = 'Слишком короткое имя! (Не менее '.self::NAME_LENGTH_MIN.' символов)';
        }

        if (!CoreSecurity::checkAddress(GF::get('mail'))) {
            $regFormErrorsForView[] = 'Неверный адрес электронной почты!';
        }

        if (GF::get('password') !== GF::get('repassword')) {
            $regFormErrorsForView[] = 'Введенные пароли не совпадают!';
        }

        if (mb_strlen(GF::get('password')) < self::PASSWORD_LENGTH_MIN) {
            $regFormErrorsForView[] = 'Слишком короткий пароль! (Не менее '.self::PASSWORD_LENGTH_MIN.' символов)';
        }

        if (!CoreDatetime::isValid(GF::get('birthdate'), 'd.m.Y')) {
            $regFormErrorsForView[] = 'Дата рождения введена неверно!';
        }

        if (\app\emodels\User::where('mail', '=', GF::get('mail'))->exists()) {
            $regFormErrorsForView[] = 'Пользователь с таким E-mail уже зарегистрирован!';
        }

        $validFile = false;
        if ($f->isSended()) {
            $validFile = $f->validate();
        }

        if (count($regFormErrorsForView) || ($f->isSended() && !$validFile)) {
            $this->view->viewErrors = array_merge($f->getUploadErrors(), $regFormErrorsForView);
            $this->view->first_name = CoreSecurity::safeString(GF::get('first_name'));
            $this->view->mail = CoreSecurity::safeString(GF::get('mail'));
            $this->view->birthdate = CoreSecurity::safeString(GF::get('birthdate'));   // validate date
            $this->view->description = CoreSecurity::safeString(GF::get('description'));
        } else {
            $u = new \app\emodels\User();
            $u->mail = GF::get('mail');
            $u->setNewPassword(GF::get('password'));
            $u->first_name = CoreSecurity::safeString(GF::get('first_name'));
            $u->description = CoreSecurity::safeString(GF::get('description'));
            $u->birthdate = CoreDatetime::convertDate(GF::get('birthdate'), 'd.m.Y', 'Y-m-d');
            $u->save();

            if ($f->isSended() && $f->genFileNameAndMoveTo('uploads')) {
                $p = new \app\emodels\Photo();
                $p->user_id = $u->id;
                $p->file_path = $f->newFilePath;
                $p->file_name = $f->name;
                $p->mime_type = $f->type;
                $p->save();
            }
            $u->avatar_id = $p->id;
            $u->save();

            // Можно сразу авторизовать, как на всех современных сайтах, но мы не будем этого делать
            //$objUser->login();
            //CoreContext::getInstance()->getRouter()->redirectTo('/');

            // Вместо этого перенаправим пользака на мучения - авторизацию
            CoreContext::getInstance()->getRouter()->redirectTo('/login');
        }
    }

    public function logoutAction()
    {
        $this->render = false;

        GF::init(new AdapterSession());
        GF::remove(CurUser::KEY_USER_SESSION);
        session_regenerate_id(true); // Defend from 'Session fixation'

        GF::init(new AdapterCookie());
        GF::remove(CurUser::KEY_MAIL_COOKIE);
        GF::remove(CurUser::KEY_PASS_COOKIE);

        CoreContext::getInstance()->getRouter()->redirectTo('/');
    }

    public function profileAction()
    {
        $this->pageTitle = 'My profile';
        $userData = $this->curUser::with('photos')->where('id', $this->curUser->id)->first()->toArray();
        $this->view->photos = $userData['photos'];
    }
}
