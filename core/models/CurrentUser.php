<?php
namespace core\models;

use core\DB;

class CurrentUser extends User
{
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
        if (empty($_SESSION['user_id']) && empty($_COOKIE["uMail"]) && empty($_COOKIE["uPass"])) {
            return;
        }

        if (isset($_SESSION['user_id'])) {
            $this->loadById($_SESSION['user_id']);
        } else {
            $this->loadByMailHash($_COOKIE["uMail"], $_COOKIE["uPass"]);
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

        $_SESSION['user_id'] = $this->id;
        $this->logged = true;
    }

    private function loadByMailHash($mail, $hash)
    {
        $userFromDb = DB::run("SELECT * FROM users WHERE mail = ? AND pass_hash = ? LIMIT 1;", [$mail, $hash])->fetch();
        return $this->fetchModel($userFromDb);
    }

    public function isLogged()
    {
        return $this->logged;
    }
}
