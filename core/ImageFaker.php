<?php
namespace core;

use Intervention\Image\ImageManagerStatic;

class ImageFaker
{
    private $objImagick;

    public function __construct($imageWidth, $imageHeight)
    {
        $this->objImagick = ImageManagerStatic::make("http://placeimg.com/{$imageWidth}/{$imageHeight}/any");
    }

    public function saveTo($toPath)
    {
        $this->objImagick->save($toPath);
    }
}