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

    public function uploadAction()
    {
        $this->pageTitle = 'Upload photos';
        $filesVars = Context::getInstance()->getRequest()->getFilesHttpVars();
        $arrPhoto = $filesVars['photo'];

        if (!isset($arrPhoto['name'][0]) || !count($arrPhoto['name'])) {
            return false;
        }

        $imagesCount = count($arrPhoto['name']);
        $uploadErrors = [];
        $successMessages = [];
        $lastFileId = 0;
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
                    $lastFileId = $objFile->saveToDb();
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
            $this->curUser->updateAvatarId($lastFileId);
        }
    }
}
