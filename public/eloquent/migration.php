<?php

include 'init.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager;

Capsule::schema()->dropIfExists('users');

Capsule::schema()->create('users', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('avatar_id')->default(0)->unsigned();
    $table->string('mail', 50)->unique();
    $table->string('pass_hash', 40);
    $table->string('salt', 4);
    $table->string('first_name', 20);
    $table->string('description')->nullable();
    $table->date('birthdate')->nullable();
});

Capsule::schema()->dropIfExists('photos');

Capsule::schema()->create('photos', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('user_id')->default(0)->unsigned();
    $table->string('file_path');
    $table->string('file_name')->nullable();
    $table->string('mime_type')->nullable();
});
