<?php
namespace core\models;

use core\DB;

class UserMapper extends User
{
    public function __construct()
    {
    }

    public function save(User $user)
    {
        if ((int)$user->id > 0) {
            $this->update($user);
        } else {
            $this->insert($user);
            $user->id = $this->id; // after insert update model id;
        }
    }

    private function insert(User $user)
    {
        DB::run("
            INSERT INTO 
                users (avatar_id, mail, pass_hash, salt, first_name, description, birthdate, is_del)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $user->avatarId, $user->mail, $user->passHash, $user->salt, $user->firstName,
            $user->description, $user->birthdate, $user->isDel
        ]);
        $this->id = DB::lastInsertId();
    }
    private function update(User $user)
    {
        DB::run("
            UPDATE 
                users 
            SET 
                avatar_id = ?, mail = ?, pass_hash = ?, salt = ?, first_name = ?, 
                description = ?, birthdate = ?, is_del = ? 
            WHERE 
                id = ? 
            LIMIT 1;
        ", [
            $user->avatarId, $user->mail, $user->passHash, $user->salt, $user->firstName,
            $user->description, $user->birthdate, $user->isDel, $user->id
        ]);
    }
}
