<?php
namespace core\models;

use core\util\globals\GlobalsFactory;

final class CurUser extends \app\emodels\User
{
    const KEY_USER_SESSION = 'user_id';
    const KEY_MAIL_COOKIE = 'uMail';
    const KEY_PASS_COOKIE = 'uPass';

    public static function init()
    {
        GlobalsFactory::init(new \core\util\globals\AdapterCookie());
        $userMail = GlobalsFactory::get(self::KEY_MAIL_COOKIE);
        $userPassHash = GlobalsFactory::get(self::KEY_PASS_COOKIE);

        GlobalsFactory::init(new \core\util\globals\AdapterSession());
        $sessId = GlobalsFactory::get(self::KEY_USER_SESSION);

        if (empty($sessId) && empty($userMail) && empty($userPassHash)) {
            return false;
        }

        if (isset($sessId) && $sessId > 0) {
            $objUser = self::with('avatar')
                ->where('id', '=', $sessId)
                ->first();
        } else {
            $objUser = self::with('avatar')
                ->where('mail', '=', $userMail)
                ->where('pass_hash', '=', $userPassHash)
                ->first();
        }

        if (!$objUser || $objUser->is_del > 0) {
            return false;
        }

        GlobalsFactory::set(self::KEY_USER_SESSION, $objUser->id);

        return $objUser;
    }
}
