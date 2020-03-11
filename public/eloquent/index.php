<?php

include 'init.php';

$users = User::query()->limit(10)->get();

$users = User::with('photos')->limit(10)->get();

$users = User::query()->where('id', '>=', '18')->get();

$data = User::with('photos')->where('id', 12)->first()->toArray();


$data = User::with('avatar')->where('id', 12)->first()->toArray();


$data = User::query()->selectRaw('*, IF(
                    MONTH(NOW()) < MONTH(birthdate) OR 
                    (MONTH(NOW()) = MONTH(birthdate) AND DAY(NOW()) < DAY(birthdate)), 
                    YEAR(NOW()) - YEAR(birthdate) - 1, 
                    YEAR(NOW()) - YEAR(birthdate)
                ) AS ages')->where('id', 12)->first()->toArray();

$dfg = new User();


//$data = Photo::with('userdata')->get();

print '<pre>';
print_r($data);

