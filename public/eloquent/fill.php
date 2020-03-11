<?php

include 'init.php';

$faker = Faker\Factory::create('ru_RU');

for ($i = 0; $i < 30; $i++) {
    $user = new User();
    $user->mail = $faker->email;
    $user->pass_hash = $faker->password();
    $user->first_name = $faker->firstName;
    $user->description = $faker->text;
    $user->birthdate = $faker->date();
    $user->save();
}

$users = User::all();

foreach($users as $user) {
    for($i=0; $i<5;$i++) {
        $photo = new Photo();
        $photo->user_id = $user->id;
        $photo->file_path = $faker->password();
        $photo->save();
    }

}