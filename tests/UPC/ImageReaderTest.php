<?php

namespace Matok\Gadget\Tests\UPC;

use Matok\Gadget\UPC\ImageReader;
use PHPUnit\Framework\TestCase;

class ImageReaderTest extends TestCase
{
    /**
     * @dataProvider getImageFiles
     */
    public function testReadCode($filename, $expectedCode)
    {
        $barcodeReader = new ImageReader($filename);
        $code = $barcodeReader->read();

        $this->assertEquals($expectedCode, $code);
    }

    public function getImageFiles()
    {
        return [
            [__DIR__.'/codes/barcode1.png', ['1-1-1-4', '1-3-1-2', '1-1-3-2', '1-1-3-2', '2-2-2-1', '2-1-2-2', '1-1-2-3', '1-2-2-2', '4-1-1-1', '3-1-2-1', '1-1-2-3', '4-1-1-1']],
            [__DIR__.'/codes/barcode2.png', ['1-1-1-4', '1-1-1-4', '3-1-1-2', '3-1-1-2', '3-1-1-2', '3-1-1-2', '2-2-1-2', '1-3-2-1', '1-2-2-2', '2-2-1-2', '2-3-1-1', '4-1-1-1']],
            [__DIR__.'/codes/barcode3.png', ['1-1-1-4', '1-2-3-1', '3-2-1-1', '1-1-3-2', '3-2-1-1', '3-1-1-2', '1-2-2-2', '4-1-1-1', '2-2-1-2', '1-3-2-1', '3-1-2-1', '1-1-2-3']],
            [__DIR__.'/codes/barcode4.png', ['1-1-1-4', '1-4-1-1', '2-1-2-2', '2-2-2-1', '2-1-2-2', '1-2-3-1', '3-1-2-1', '2-2-1-2', '2-2-1-2', '4-1-1-1', '1-1-2-3', '4-1-1-1']],
        ];
    }
}
