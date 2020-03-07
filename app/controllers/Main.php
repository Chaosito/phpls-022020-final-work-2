<?php
namespace app\controllers;

use core\Context as CoreContext;
use core\ImageFaker;
use core\models\UserMapper;
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

        $objUser = new \app\models\User();
        $users = $objUser->getAllUsers($requestVars['sort']);
        $this->view->users = $users;
        $this->view->sorting = $requestVars['sort'];
    }

    public function fakerAction()
    {
//        $this->render = false;
        $this->pageTitle = 'Fake data generator';
        $faker = Faker\Factory::create(self::FAKER_LOCALE);
        $usersForView = [];

        for ($i = 0; $i < self::FAKE_USERS_COUNT; $i++) {
            $emulatedForm['mail'] = $faker->email;
            $emulatedForm['password'] = $faker->password(self::FAKE_PASS_LENGTH_MIN, self::FAKE_PASS_LENGTH_MAX);
            $emulatedForm['first_name'] = $faker->firstName;
            $emulatedForm['description'] = $faker->realText(self::FAKE_USER_DESCR_LENGTH);
            $emulatedForm['birthdate'] = $faker->dateTime()->format('d.m.Y');
            $objUser = new \app\models\User();
            $objUser->fillFromForm($emulatedForm);
            $userMapper = new UserMapper();
            $userMapper->save($objUser);

            $imagesCount = mt_rand(0, self::FAKE_IMAGES_PER_USER_MAX);

            $lastFileId = 0;
            for ($j = 0; $j < $imagesCount; $j++) {
                $imageFaker = new ImageFaker(self::FAKE_IMG_WIDTH, self::FAKE_IMG_HEIGHT);
                $tempPathForImage = sys_get_temp_dir().DIRECTORY_SEPARATOR.Security::generateString('filename', 20);
                $imageFaker->saveTo($tempPathForImage);

                $emulatedFile['name'] = "ImageName_{$j}.jpg";
                $emulatedFile['type'] = 'image/jpeg';
                $emulatedFile['tmp_name'] = $tempPathForImage;
                $emulatedFile['error'] = 0;
                $emulatedFile['size'] = filesize($tempPathForImage);

                $objFile = new \core\File($emulatedFile);
                $objFile->setOwnerId($userMapper->getId());
                $objFile->moveUploadedFile();
                $lastFileId = $objFile->saveToDb();
            }
            $usersForView[] = array(
                $emulatedForm['first_name'], $emulatedForm['mail'], $emulatedForm['password'], $imagesCount
            );
            $objUser->updateAvatarId($lastFileId);
        }
        $this->view->usersData = $usersForView;
    }
}
