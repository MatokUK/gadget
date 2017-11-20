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
        $buffer = $this->filterBorderBars($buffer);

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

    private function filterBorderBars($buffer)
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

        $begin = $idx;

        $bars = 1;
        $lastValue = $buffer[count($buffer)-1];

        for ($idx = count($buffer) - 2; $idx > 0 ; $idx--) {
            if ($lastValue == $buffer[$idx]) {
                $bars++;
            } else {
                $lastValue = $buffer[$idx];
                $bars = 1;
            }

            if (3 === $bars) {
                break;
            }
        }

        $this->unitSize = $buffer[1];

        return array_slice($buffer, $begin, count($buffer) - $begin*2);
    }

    private function readBars($buffer)
    {
        $result = [];
        foreach ($buffer as $bar) {
            $result[] = $bar / $this->unitSize;
        }

        [$first, $second] = $this->splitToHalves($result);

        $first = array_chunk($first, 4);
        $second = $this->reverseChunks(array_chunk($second, 4));
        $x = array_merge_recursive($first, $second);

        return array_map(function($value) {
            return implode('-', $value);
        }, $x);
    }

    private function splitToHalves($values)
    {
        $half = (count($values)-1) / 2;

        if ($values[$half - 2] == 1 && $values[$half - 1] == 1 && $values[$half] == 1 && $values[$half + 1] == 1 && $values[$half + 2] == 1) {
            $firstHalf = array_slice($values, 0, $half - 2);
            $secondHalf = array_slice($values, $half + 3);


            return [$firstHalf, $secondHalf];
        }
    }

    private function reverseChunks($chunks)
    {
        return array_map(function($value) {
            return array_reverse($value);
        }, $chunks);
    }


}