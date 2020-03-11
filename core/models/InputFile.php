<?php

class InputFile
{
    const MAX_FILE_SIZE = 10485760; // 10 Мб

    public $name;
    public $type;
    public $tmp_name;
    public $error;
    public $size;

    private $extension;

    public function __construct($fileArray, $fileIndex = null)
    {
        if (is_array($fileArray['name']) && count($fileArray['name'])) {
            // переданный файл это массив и он имеет 1 или более элементов, значит это точно multiple
            // Проверям $fileIndex, если он null бросаем исключение
            if ($fileIndex == null) {
                throw new Exception('For multiple files required fileIndex!');
            }

            $this->name = $fileIndex['name'][$fileIndex];
            $this->type = $fileIndex['type'][$fileIndex];
            $this->tmp_name = $fileIndex['tmp_name'][$fileIndex];
            $this->error = $fileIndex['error'][$fileIndex];
            $this->size = $fileIndex['size'][$fileIndex];

        } else {
            // Файл не является массивом, соответственно передан без multiple и $fileIndex нам не нужен
            if ($fileIndex != null) {
                throw new Exception('For non multiple files, index not allowed!');
            }
            $this->name = $fileIndex['name'];
            $this->type = $fileIndex['type'];
            $this->tmp_name = $fileIndex['tmp_name'];
            $this->error = $fileIndex['error'];
            $this->size = $fileIndex['size'];
        }
    }


    public function isSended()
    {
        return ($this->name || $this->type || $this->tmp_name || $this->size);
    }

}

$sdf = new InputFile($_FILES['mememe'], 2);

$sdf->setAllowedExtensions(['jpg','bmp','azaza']);