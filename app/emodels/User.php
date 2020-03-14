<?php
namespace app\emodels;

use core\Security as CoreSecurity;

class User extends \Illuminate\Database\Eloquent\Model
{
    public $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

//    protected $connection;
//    protected $appends = ['translated'];
//
//    public function getTranslatedAttribute()
//    {
//        return 'the translated tag';
//    }
    public function photos()
    {
        return $this->hasMany(Photo::class, 'user_id', 'id');
    }

    public function avatar()
    {
        return $this->hasOne(Photo::class, 'id', 'avatar_id');
    }

    public function setNewPassword($password)
    {
        $this->salt = CoreSecurity::generateString();
        $this->pass_hash = CoreSecurity::generatePasshash($this->salt, $password);
    }

    public function matchPassword($password)
    {
        return ($this->pass_hash == CoreSecurity::generatePasshash($this->salt, $password));
    }
}
