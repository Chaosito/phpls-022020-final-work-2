<?php
namespace app\controllers;

use core\Context;
use Intervention\Image\ImageManager;

class Photo extends \core\Controller
{
    public function indexAction()
    {
        Context::getInstance()->getRouter()->redirectTo('/');
    }

    public function viewAction()
    {
        $this->readPhoto();
    }

    public function thumbAction()
    {
        $this->readPhoto(1);
    }

    private function readPhoto($thumb = false)
    {
        $this->render = false;
        $httpVars = Context::getInstance()->getRequest()->getRequestHttpVars();
        if ((int)$httpVars['id'] == 0) {
            Context::getInstance()->getRouter()->redirectTo('/');
        }

        $photoPath = \core\DB::run(
            "SELECT file_path FROM photos WHERE id = ? AND is_del = 0 LIMIT 1;",
            [$httpVars['id']]
        )->fetchColumn();

        $manager = new ImageManager(array('driver' => 'imagick'));

        $photoPath = ($thumb) ? str_replace('images/', 'images/thumbs/', $photoPath) : $photoPath;

        $image = $manager->make(Context::getInstance()->getProjectPath().$photoPath);
        echo $image->response('png', 90);
    }

    public function uploadAction()
    {
        $this->pageTitle = 'My profile';
        $filesVars = Context::getInstance()->getRequest()->getFilesHttpVars();
        $arrPhoto = $filesVars['photo'];
//        print '<pre>';
//        print_r($filesVars);

        if (!isset($arrPhoto['name'][0]) || !count($arrPhoto['name'])) {
            return false;
        }

        $imagesCount = count($arrPhoto['name']);
        $uploadErrors = [];
        $successMessages = [];
        for ($i = 0; $i < $imagesCount; $i++) {
            // create valid array

            $arrFile = [];
            foreach ($arrPhoto as $param => $value) {
                $arrFile[$param] = $value[$i];
            }

            $objFile = new \core\File($arrFile);

            if ($objFile->isSended()) {
                $validFile = $objFile->validate();
                if (!$validFile) {
                    $uploadErrors = array_merge($uploadErrors, $objFile->getUploadErrors());
                } else {
                    $objFile->setOwnerId($this->curUser->getId());
                    $objFile->moveUploadedFile();
                    $objFile->saveToDb();
                    $successMessages[] = "Файл `".$objFile->getName()."` загружен успешно!";
                }
            }
            unset($arrFile, $objFile, $validFile);
        }

        if (count($uploadErrors)) {
            $this->view->viewErrors = $uploadErrors;
        }

        if (count($successMessages)) {
            $this->view->successMessages = $successMessages;
        }
    }
}
