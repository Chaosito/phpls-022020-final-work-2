<?php
namespace core;

class Security
{
    public static function checkAddress($mail)
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    public static function safeString($text, $stripTags = true)
    {
        $text = ($stripTags) ? strip_tags($text) : $text;
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML401);
    }
    
    public static function generateString($type = 'salt', $n = 4)
    {
        $key = '';
        $pattern = '';

        if ($type == 'salt') {
            $pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
        } elseif ($type == 'filename') {
            $pattern = '1234567890abcdefghijklmnopqrstuvwxyz_';
        } elseif ($type == 'password') {
            $pattern = '1234567890abcdefghijklmnopqrstuvwxyz_!@#$%';
        }
        
        $counter = strlen($pattern) - 1;
        for ($i = 0; $i < $n; $i++) {
            $key .= $pattern{rand(0, $counter)};
        }
        return $key;
    }
    
    public static function generatePasshash($salt, $password)
    {
        return sha1($salt.md5($password.$salt).$salt);
    }
}
