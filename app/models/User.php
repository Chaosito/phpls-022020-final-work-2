<?php
namespace app\models;

use \core\Security as CoreSecurity;
use \core\Datetime as CoreDatetime;
use \core\DB as CoreDB;

class User extends \core\models\User
{
    const NAME_LENGTH_MIN = 2;
    const PASSWORD_LENGTH_MIN = 6;
    
    private $viewErrors = [];
    
    public function __construct()
    {
        // print __METHOD__.' of '.__CLASS__.' is called!<br>';
    }
    
    public function validateRegisterForm($formData)
    {
        $this->viewErrors = [];
        
        if (mb_strlen($formData['first_name']) < self::NAME_LENGTH_MIN) {
            $this->viewErrors[] = 'Слишком короткое имя! (Не менее '.self::NAME_LENGTH_MIN.' символов)';
        }
        
        if (!CoreSecurity::checkAddress($formData['mail'])) {
            $this->viewErrors[] = 'Неверный адрес электронной почты!';
        }
        
        if ($formData['password'] !== $formData['repassword']) {
            $this->viewErrors[] = 'Введенные пароли не совпадают!';
        }
        
        if (mb_strlen($formData['password']) < self::PASSWORD_LENGTH_MIN) {
            $this->viewErrors[] = 'Слишком короткий пароль! (Не менее '.self::PASSWORD_LENGTH_MIN.' символов)';
        }
        
        if (!CoreDatetime::isValid($formData['birthdate'], 'd.m.Y')) {
            $this->viewErrors[] = 'Дата рождения введена неверно!';
        }
        
        if (!$this->viewErrors && $this->isMailExists($formData['mail'])) {
            $this->viewErrors[] = 'Пользователь с таким E-mail уже зарегистрирован!';
        }
        
        return (!count($this->viewErrors));
    }

    
    public function fillFromForm($validFormData)
    {
        $this->mail = $validFormData['mail'];
        $this->salt = CoreSecurity::generateString();
        $this->passHash = CoreSecurity::generatePasshash($this->salt, $validFormData['password']);
        $this->firstName = CoreSecurity::safeString($validFormData['first_name']);
        $this->description = CoreSecurity::safeString($validFormData['description']);
        $this->birthdate = CoreDatetime::convertDate($validFormData['birthdate'], 'd.m.Y', 'Y-m-d');
    }

    private function isMailExists($mail)
    {
        return (bool)CoreDB::run("SELECT id FROM users WHERE mail = ? LIMIT 1;", [$mail])->fetch();
    }
    
    public function getViewErrors()
    {
        return $this->viewErrors;
    }

    public function matchPassword($userPassword)
    {
        return CoreSecurity::generatePasshash($this->salt, $userPassword) == $this->passHash;
    }

    public function login($rememberMe = false)
    {
        $_SESSION['user_id'] = $this->id;
        if ($rememberMe) {
            setcookie('uMail', $this->mail, time() + \core\Settings::COOKIE_LIFE_TIME, "/");
            setcookie('uPass', $this->passHash, time() + \core\Settings::COOKIE_LIFE_TIME, "/");
        }
    }

    public function getUploadedFiles($userId)
    {
        $photos = CoreDB::run(
            "SELECT file_path FROM photos WHERE user_id = ? AND is_del = 0",
            [$userId]
        )->fetchAll();

        foreach ($photos as $key => $photo) {
            $photos[$key]['thumb_path'] = str_replace(
                'uploads/',
                'uploads/thumbs/',
                $photo['file_path']
            );
        }
        return $photos;
    }

    public function getAllUsers($sorting = 'ASC')
    {
        $sorting = in_array(strtoupper($sorting), ['ASC', 'DESC']) ? $sorting : 'ASC';
        $users = CoreDB::run("
            SELECT 
                *, IF(
                    MONTH(NOW()) < MONTH(birthdate) OR 
                    (MONTH(NOW()) = MONTH(birthdate) AND DAY(NOW()) < DAY(birthdate)), 
                    YEAR(NOW()) - YEAR(birthdate) - 1, 
                    YEAR(NOW()) - YEAR(birthdate)
                ) AS ages 
            FROM 
                users 
            ORDER BY 
                birthdate {$sorting}
        ")->fetchAll();
        return $users;
    }
}
