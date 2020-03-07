<?php
namespace core;

use \core\Security as CoreSecurity;
use Intervention\Image\ImageManager;

class File
{
    const MAX_FILE_SIZE = 10485760; // 10 Мб
    const THUMB_HEIGHT = 200; // px & width by aspect-ratio;
    
    private $name;
    private $type;
    private $tmpName;
    private $error;
    private $size;
    
    private $userId = 0;
    private $ext = '';
    private $newPath = '';
    private $uploadErrors = [];
    
    public function __construct($fileVarsArray)
    {
        $this->name = $fileVarsArray['name'];
        $this->type = $fileVarsArray['type'];
        $this->tmpName = $fileVarsArray['tmp_name'];
        $this->error = $fileVarsArray['error'];
        $this->size = $fileVarsArray['size'];
        $this->ext = $this->getExtension();
    }
    
    public function isSended()
    {
        return ($this->name || $this->type || $this->tmpName || $this->size);
    }
    
    public function validate()
    {
        $this->uploadErrors = [];
        
        if ($this->name == '') {
            $this->uploadErrors[] = 'Неверное имя файла!';
        }
        
        if (!in_array($this->ext, $this->getAllowedExtensions())) {
            $this->uploadErrors[] =
                "`{$this->name}` - Неверный тип файла (Необходим: ".
                implode(', ', $this->getAllowedExtensions()).")!";
        }
        
        if (!file_exists($this->tmpName)) {
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
    
    public function getUploadErrors()
    {
        return $this->uploadErrors;
    }
    
    public function moveUploadedFile()
    {
        $newName = CoreSecurity::generateString('filename', 32).'.'.$this->ext;
        $this->newPath = "images/{$newName}";
        $thumbPath = "images/thumbs/{$newName}";

        @move_uploaded_file($this->tmpName, Context::getInstance()->getProjectPath().$this->newPath);
        $this->createThumbnail($thumbPath);
    }

    private function createThumbnail($thumbPath)
    {
        $filePath = ($this->newPath != '') ? Context::getInstance()->getProjectPath().$this->newPath : $this->tmpName;

        // create an image manager instance with favored driver
        $manager = new ImageManager(array('driver' => 'imagick'));

        $image = $manager->make($filePath)->resize(null, self::THUMB_HEIGHT, function ($constraint) {
            $constraint->aspectRatio();
        });

        $image->save(Context::getInstance()->getProjectPath().$thumbPath);
    }
    
    private function getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'png'];
    }
    
    private function getExtension()
    {
        return strtolower(substr(strrchr($this->name, '.'), 1));
    }
    
    private function getFileMimeType()
    {
        $filePath = ($this->newPath != '') ? Context::getInstance()->getProjectPath().$this->newPath : $this->tmpName;
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
    }
    
    public function setOwnerId($userId)
    {
        $this->userId = $userId;
    }
    
    public function saveToDb()
    {
        if (file_exists(Context::getInstance()->getProjectPath().$this->newPath) && $this->userId > 0) {
            DB::run("INSERT INTO photos (user_id, file_path, file_name, mime_type) VALUES (?, ?, ?, ?)", [
                $this->userId, $this->newPath, $this->name, $this->getFileMimeType()
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
