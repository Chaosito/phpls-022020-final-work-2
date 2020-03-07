<?php
namespace core;

class Datetime
{
    public static function isValid($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function convertDate($date, $from = 'Y-m-d', $to = 'd.m.Y')
    {
        if (self::isValid($date, $from)) {
            $d = \DateTime::createFromFormat($from, $date);
            return $d->format($to);
        }
    }
}
