<?php

namespace Matok\Gadget\Image;

use Gregwar\Image\Image as GregwarImage;

class Image extends GregwarImage
{
    public function getColor($x, $y)
    {
        $resource =  $this->getAdapter()->getResource();
        $rgb = imagecolorat($resource, $x, $y);

        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        var_dump($r, $g, $b);
    }
}