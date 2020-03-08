<?php
namespace core;

use Intervention\Image\ImageManagerStatic;

class ImageFaker
{
    // https, http, ftp, etc
    const FAKER_DOMAIN_PROTOCOL = 'http';
    const FAKER_DOMAIN = 'placeimg.com';

    private $objImagick;

    public function __construct($imageWidth, $imageHeight)
    {
        $this->objImagick = ImageManagerStatic::make(
            self::FAKER_DOMAIN_PROTOCOL."://".self::FAKER_DOMAIN."/{$imageWidth}/{$imageHeight}/any"
        );
    }

    public function saveTo($toPath)
    {
        $this->objImagick->save($toPath);
    }
}
