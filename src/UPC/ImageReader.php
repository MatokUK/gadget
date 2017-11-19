<?php

namespace Matok\Gadget\UPC;

use Gregwar\Image\Image;

class ImageReader
{
    private $filename;

    private $width;

    private $unitSize = null;

    public function __construct($imageFileOrData)
    {
        $this->filename = $imageFileOrData;
    }

    public function read()
    {

        $line = $this->cropLine();
        $data = $line->get('png');
        $buffer = $this->fillBuffer($data);
        $buffer = $this->filterInitBars($buffer);

        $encodedValues = $this->readBars($buffer);

        return $encodedValues;
    }

    private function cropLine()
    {
        $image = $this->openImage();
        $this->width = $image->width();
        $halfHeight = (int) ($image->height() / 2);

        return $image->crop(0, $halfHeight, $this->width, 1);
    }

    private function openImage()
    {
        if (is_file($this->filename)) {
            return Image::open($this->filename);
        }

        return Image::fromData($this->filename);
    }

    private function fillBuffer($data)
    {
        $buffer = [];
        $lastColor = null;
        $pos = -1;
        $im = imagecreatefromstring($data);

        for ($x = 0; $x < $this->width; $x++) {
            $rgb = imagecolorat($im, $x, 0);

            if (!isset($buffer[$pos]) || $lastColor != $rgb) {
                $pos++;
                $buffer[$pos] = 1;
            } else {
                $buffer[$pos]++;
            }

            $lastColor = $rgb;
        }

        return $buffer;
    }

    private function filterInitBars($buffer)
    {
        $bars = 0;
        $lastValue = $buffer[0];

        for ($idx = 1; $idx < count($buffer); $idx++) {
            if ($lastValue == $buffer[$idx]) {
                $bars++;
            } else {
                $lastValue = $buffer[$idx];
                $bars = 0;
            }

            if (3 === $bars) {
                break;
            }
        }

        $this->unitSize = $buffer[$idx];

        return array_slice($buffer, $idx);
    }

    private function readBars($buffer)
    {
        $result = [];
        foreach ($buffer as $bar) {
            $result[] = $bar / $this->unitSize;
        }
        $result = array_chunk($result, 4);

        $result = array_filter($result, function($value) {
            return (count($value) == 4);
        });

        $result = $this->reverseSecondHalf($result);

        return array_map(function($value) {
            return implode('-', $value);
        }, $result);
    }

    private function reverseSecondHalf($bars)
    {
        $revert = false;
        foreach ($bars as $key => $values) {
            if ($revert) {
                $bars[$key] = array_reverse($bars[$key]);
            }

            if ($values[0] == 1 && $values[1] = 1 && $values[2] == 1 && $values[3] == 1) {
                $revert = true;
                unset($bars[$key]);
            }
        }

        return $bars;
    }


}