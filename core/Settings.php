<?php
namespace core;

/*
  Класс Settings
  Настройки сайта
*/

class Settings
{
    const DEBUG_MODE = true;

//    const MAX_DOC_SIZE = 83886080; // (80 * 1024 * 1024) = 83.886.080 Bytes = 80 Mb;
    const COOKIE_LIFE_TIME = 604800; // (7 * 24 * 3600) = 604.800 Seconds = 7 Days;

    /* MySQL Connection */
    const MYSQL_HOST = 'localhost';
    const MYSQL_USER = 'sqluserforlf';
    const MYSQL_PASS = '5q1u5erf0r1f';
    const MYSQL_DB   = 'final-work2';
    const MYSQL_CHAR = 'utf8';
}
