<?php

namespace Matok\Gadget\UPC;

class Validator
{
    private $code;
    private $codeWithoutCheckDigit;
    private $checkDigit;


    public function __construct($code)
    {
        $this->code = $code;
        $this->checkDigit = (int) substr($code, -1);
        $this->codeWithoutCheckDigit = substr($code, 0, -1);
    }

    public function isValid()
    {
        $calculatedCheckDigit = $this->calculateCheckDigit($this->codeWithoutCheckDigit);

        return $this->checkDigit === $calculatedCheckDigit;
    }

    private function calculateCheckDigit($code)
    {
        $oddPositionDigits = $this->getOddPositionDigits($code);
        $evenPositionDigits = $this->getEvenPositionDigits($code);

        $sumOddPositionDigits = array_sum($oddPositionDigits);
        $sumEvenPositionDigits = array_sum($evenPositionDigits);

        $result = $sumOddPositionDigits*3 + $sumEvenPositionDigits;

        return $this->findSummandToResult(10, $result);
    }

    private function getOddPositionDigits($code)
    {
        $digits = str_split($code);

        return array_filter($digits, function($key) {
            return $key % 2 === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getEvenPositionDigits($code)
    {
        $digits = str_split($code);

        return array_filter($digits, function($key) {
            return $key % 2 === 1;
        }, ARRAY_FILTER_USE_KEY);
    }

    private function findSummandToResult($factor, $product)
    {
        $rest = $product % $factor;
        if ($rest === 0) {
            return 0;
        }

        return abs($rest - $factor);
    }
}
