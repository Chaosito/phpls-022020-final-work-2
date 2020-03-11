<?php

include 'init.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager;

Capsule::schema()->dropIfExists('users');

Capsule::schema()->create('users', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('avatar_id')->default(0);
    $table->string('mail', 50);
    $table->string('pass_hash', 40);
    $table->string('salt', 4);
    $table->string('first_name', 20);
    $table->string('description');
    $table->date('birthdate');
    $table->integer('is_del')->default(0);
    $table->timestamps();
});

Capsule::schema()->dropIfExists('photos');

Capsule::schema()->create('photos', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('user_id')->default(0);
    $table->string('file_path');
    $table->string('file_name');
    $table->string('mime_type');
    $table->integer('is_del')->default(0);
    $table->timestamps();
});
