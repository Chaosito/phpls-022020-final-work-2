<?php
namespace app\controllers;

use core\Context;
use core\Context as CoreContext;
use core\ImageFaker;
use core\Security;
use Faker;

class Main extends \core\Controller
{
    const FAKE_USERS_COUNT = 2;
    const FAKE_IMAGES_PER_USER_MAX = 4;
    const FAKE_PASS_LENGTH_MIN = 6;
    const FAKE_PASS_LENGTH_MAX = 7;
    const FAKE_USER_DESCR_LENGTH = 200;
    const FAKE_IMG_WIDTH = 640;
    const FAKE_IMG_HEIGHT = 480;

    // Use faker locale fr_FR, en_US, etc
    const FAKER_LOCALE = 'ru_RU';

    public function indexAction()
    {
        $this->pageTitle = 'Main page';
    }

    public function allusersAction()
    {
        $this->pageTitle = 'All Users';
        $requestVars = CoreContext::getInstance()->getRequest()->getRequestHttpVars();

        $sorting = (!isset($requestVars['sort']) || !in_array(strtoupper($requestVars['sort']), ['ASC', 'DESC']))
            ? 'ASC'
            : $requestVars['sort'];

        $users = \app\emodels\User::query()->selectRaw('
            id, mail, first_name, birthdate, 
            IF(
                MONTH(NOW()) < MONTH(birthdate) OR 
                (MONTH(NOW()) = MONTH(birthdate) AND DAY(NOW()) < DAY(birthdate)), 
                YEAR(NOW()) - YEAR(birthdate) - 1, 
                YEAR(NOW()) - YEAR(birthdate)
            ) AS ages
        ')->orderBy('birthdate', $sorting)->get();

        $this->view->sorting = $sorting;
        $this->view->users = $users;
    }

    public function fakerAction()
    {
        $this->pageTitle = 'Fake data generator';
        $faker = Faker\Factory::create(self::FAKER_LOCALE);
        $usersForView = [];

        for ($i = 0; $i < self::FAKE_USERS_COUNT; $i++) {
            $user = new \app\emodels\User();
            $user->mail = $faker->email;
            $userPassword = $faker->password(self::FAKE_PASS_LENGTH_MIN, self::FAKE_PASS_LENGTH_MAX);
            $user->setNewPassword($userPassword);
            $user->first_name = $faker->firstName;
            $user->description = $faker->realText(self::FAKE_USER_DESCR_LENGTH);
            $user->birthdate = $faker->dateTime()->format('Y-m-d');
            $user->save();

            $imagesCount = mt_rand(0, self::FAKE_IMAGES_PER_USER_MAX);

            $lastFileId = 0;
            for ($j = 0; $j < $imagesCount; $j++) {
                $imageFaker = new ImageFaker(self::FAKE_IMG_WIDTH, self::FAKE_IMG_HEIGHT);


                $newFileName = Security::generateString('filename', 32);
                $pathForImage = "uploads/{$newFileName}.jpg";

                $imageFaker->saveTo(Context::getInstance()->getProjectPath().'public/'.$pathForImage);

                $file = new \app\emodels\Photo();
                $file->user_id = $user->id;
                $file->file_path = $pathForImage;
                $file->file_name = "ImageName_{$j}.jpg";
                $file->mime_type = "image/jpeg";
                $file->save();
                $lastFileId = $file->id;
            }

            if ($imagesCount > 0) {
                $user->avatar_id = $lastFileId;
                $user->save();
            }

            $usersForView[] = array(
                $user->first_name, $user->mail, $userPassword, $imagesCount
            );
        }
        $this->view->usersData = $usersForView;
    }
}
