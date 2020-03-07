<?php
namespace core\models;

use core\DB;

class User
{
    /* Fields in `Users` table */
    protected $id = 0;
    protected $avatarId = 0;
    protected $mail = '';
    protected $passHash = '';
    protected $salt = '';
    protected $firstName = '';
    protected $description = '';
    protected $birthdate = '';
    protected $isDel = 0;

    public function getId()
    {
        return $this->id;
    }

    protected function fetchModel($tableUsers): bool
    {
        if (!$tableUsers) {
            return false;
        }
        $this->id = $tableUsers['id'];
        $this->avatarId = $tableUsers['avatar_id'];
        $this->mail = $tableUsers['mail'];
        $this->passHash = $tableUsers['pass_hash'];
        $this->salt = $tableUsers['salt'];
        $this->firstName = $tableUsers['first_name'];
        $this->description = $tableUsers['description'];
        $this->birthdate = $tableUsers['birthdate'];
        $this->isDel = $tableUsers['is_del'];
        return true;
    }

    public function loadById(int $userId)
    {
        // Так получится фигня, т.к. в базе у нас {first_name, is_del}, а в модели {firstName, isDel}
        // return DB::run("SELECT * FROM users WHERE id = ? LIMIT 1;", [$userId])->fetchObject('\core\models\User');
        // Поэтому фетчим сами
        $userFromDb = DB::run("SELECT * FROM users WHERE id = ? LIMIT 1;", [$userId])->fetch();
        return $this->fetchModel($userFromDb);
    }

    public function loadByMail($mail)
    {
        $userFromDb = DB::run("SELECT * FROM users WHERE mail = ? LIMIT 1;", [$mail])->fetch();
        return $this->fetchModel($userFromDb);
    }

    public function updateAvatarId($fileId)
    {
        DB::run("UPDATE users SET avatar_id = ? WHERE id = ? LIMIT 1;", [$fileId, $this->id]);
        $this->avatarId = $fileId;
    }
}
