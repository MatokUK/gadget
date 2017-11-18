<?php

namespace Matok\Gadget\Tests\UPC;

use Matok\Gadget\UPC\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidCodes
     */
    public function testValidCode($code)
    {
        $upcValidator = new Validator($code);

        $this->assertTrue($upcValidator->isValid());
    }

    /**
     * @dataProvider getInvalidCodes
     */
    public function testInvalidCode($code)
    {
        $upcValidator = new Validator($code);

        $this->assertFalse($upcValidator->isValid());
    }

    public function getValidCodes()
    {
        return [
            [888631477220],
            [790069358340],
            [714636939817],
        ];
    }

    public function getInvalidCodes()
    {
        return [
            [888631477221],
            [790069358342],
            [714636939810],
        ];
    }
}
