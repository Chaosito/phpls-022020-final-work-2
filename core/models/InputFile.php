<?php
namespace core\models;

use core\Context;
use core\Security as CoreSecurity;

class InputFile
{
    const MAX_FILE_SIZE = 10485760; // 10 Мб
    const LENGTH_OF_NEW_NAME_FILE = 32; // Symbols (without ext)

    public $name;
    public $type;
    private $tmp_name;
    private $error;
    private $size;

    private $extension;
    private $allowedExtensions = [];
    private $uploadErrors = [];

    public $newFilePath = '';

    public function __construct($fileArray, $fileIndex = null)
    {
        if (!is_array($fileArray)) {
            return false;
        }

        if (is_array($fileArray['name']) && count($fileArray['name'])) {
            // переданный файл это массив и он имеет 1 или более элементов, значит это точно multiple
            $this->name = $fileArray['name'][$fileIndex];
            $this->type = $fileArray['type'][$fileIndex];
            $this->tmp_name = $fileArray['tmp_name'][$fileIndex];
            $this->error = $fileArray['error'][$fileIndex];
            $this->size = $fileArray['size'][$fileIndex];
        } else {
            // Файл не является массивом, соответственно передан без multiple и $fileIndex нам не нужен
            $this->name = $fileArray['name'];
            $this->type = $fileArray['type'];
            $this->tmp_name = $fileArray['tmp_name'];
            $this->error = $fileArray['error'];
            $this->size = $fileArray['size'];
        }
        $this->extension = $this->getExtension();
    }

    public function setAllowedExtensions($extArray)
    {
        $this->allowedExtensions = $extArray;
    }

    public function isSended()
    {
        return ($this->name || $this->type || $this->tmp_name || $this->size);
    }

    public function validate()
    {
        $this->uploadErrors = [];

        if ($this->name == '') {
            $this->uploadErrors[] = 'Неверное имя файла!';
        }

        if (!in_array($this->extension, $this->allowedExtensions)) {
            $this->uploadErrors[] =
                "`{$this->name}` - Неверный тип файла (Необходим: ".
                implode(', ', $this->allowedExtensions).")!";
        }

        if (!file_exists($this->tmp_name)) {
            $this->uploadErrors[] = "`{$this->name}` - Загруженный файл не найден!";
        }

        if ($this->error != UPLOAD_ERR_OK) {
            $this->uploadErrors[] = "`{$this->name}` - Ошибка загрузки файла №".$this->error;
        }

        if ($this->size == 0 || $this->size > self::MAX_FILE_SIZE) {
            $this->uploadErrors[] = "`{$this->name}` - Неверный размер файла!";
        }

        return !(bool)count($this->uploadErrors);
    }

    private function getExtension()
    {
        return strtolower(substr(strrchr($this->name, '.'), 1));
    }

    public function getUploadErrors()
    {
        return $this->uploadErrors;
    }

    public function genFileNameAndMoveTo($moveToDirectory)
    {
        $newName = CoreSecurity::generateString('filename', self::LENGTH_OF_NEW_NAME_FILE).'.'.$this->extension;
        $this->newFilePath = $moveToDirectory.DIRECTORY_SEPARATOR.$newName;

        return move_uploaded_file(
            $this->tmp_name,
            Context::getInstance()->getProjectPath().'public'.DIRECTORY_SEPARATOR.$this->newFilePath
        );
    }
}
