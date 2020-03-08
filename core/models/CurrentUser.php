<?php
namespace core\models;

use core\DB;

class CurrentUser extends User
{
    const KEY_USER_SESSION = 'user_id';
    const KEY_MAIL_COOKIE = 'uMail';
    const KEY_PASS_COOKIE = 'uPass';

    private $logged = false;
    public $id;
    public $avatarId;
    public $avatarPath;
    public $mail;
    public $firstName;
    public $description;
    public $birthdate;


    public function __construct()
    {
        if (
            empty($_SESSION[self::KEY_USER_SESSION]) &&
            empty($_COOKIE[self::KEY_MAIL_COOKIE]) && empty($_COOKIE[self::KEY_PASS_COOKIE)
        ) {
            return;
        }

        if (isset($_SESSION[self::KEY_USER_SESSION])) {
            $this->loadById($_SESSION[self::KEY_USER_SESSION]);
        } else {
            $this->loadByMailHash($_COOKIE[self::KEY_MAIL_COOKIE], $_COOKIE[self::KEY_PASS_COOKIE]);
        }

        if (!$this->id || $this->isDel) {
            return false;
        }

        if ($this->avatarId > 0) {
            $this->avatarPath = DB::run(
                "SELECT file_path FROM photos WHERE id = ? LIMIT 1;",
                [$this->avatarId]
            )->fetchColumn();
            $this->avatarPath = str_replace('uploads/', 'uploads/thumbs/', $this->avatarPath);
        }

        $_SESSION[self::KEY_USER_SESSION] = $this->id;
        $this->logged = true;
    }

    private function loadByMailHash($mail, $hash)
    {
        $userFromDb = DB::run(
            "SELECT * FROM users WHERE mail = ? AND pass_hash = ? LIMIT 1;",
            [$mail, $hash]
        )->fetch();
        return $this->fetchModel($userFromDb);
    }

    public function isLogged()
    {
        return $this->logged;
    }
}
