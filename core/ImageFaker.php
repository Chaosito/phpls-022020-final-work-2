<?php
namespace core;

use Intervention\Image\ImageManagerStatic;

class ImageFaker
{
    const FAKER_DOMAIN = 'http://placeimg.com';
    private $objImagick;

    public function __construct($imageWidth, $imageHeight)
    {
        $this->objImagick = ImageManagerStatic::make(
            self::FAKER_DOMAIN."/{$imageWidth}/{$imageHeight}/any"
        );
    }

    public function saveTo($toPath)
    {
        $this->objImagick->save($toPath);
    }
}
