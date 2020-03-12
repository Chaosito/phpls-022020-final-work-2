<?php
namespace app\controllers;

use core\Context;
use core\models\InputFile;
use core\util\globals\AdapterFiles;
use core\util\globals\GlobalsFactory;

class Photo extends \core\Controller
{
    public function indexAction()
    {
        Context::getInstance()->getRouter()->redirectTo('/');
    }

    public function uploadAction()
    {
        $this->pageTitle = 'Upload photos';

        GlobalsFactory::init(new AdapterFiles());
        $files = GlobalsFactory::get('photo');

        if (!isset($files['name'][0]) || !count($files['name'])) {
            return false;
        }

        $uploadErrors = [];
        $successMessages = [];
        $lastSuccessUploadedFileId = 0;
        for ($i = 0; $i < count($files['name']); $i++) {
            $f = new InputFile($files, $i);
            $f->setAllowedExtensions(['jpg', 'jpeg', 'png']);

            if ($f->isSended()) {
                if (!$f->validate()) {
                    $uploadErrors = array_merge($uploadErrors, $f->getUploadErrors());
                } else {
                    if ($f->isSended() && $f->genFileNameAndMoveTo('uploads')) {
                        $p = new \app\emodels\Photo();
                        $p->user_id = $this->curUser->id;
                        $p->file_path = $f->newFilePath;
                        $p->file_name = $f->name;
                        $p->mime_type = $f->type;
                        $p->save();
                        $lastSuccessUploadedFileId = $p->id;
                    }
                    $successMessages[] = "Файл `".$f->name."` загружен успешно!";
                }
            }
        }

        if (count($uploadErrors)) {
            $this->view->viewErrors = $uploadErrors;
        }

        if (count($successMessages)) {
            $this->view->successMessages = $successMessages;
            $this->curUser->avatar_id = $lastSuccessUploadedFileId;
            $this->curUser->save();
        }
    }
}
