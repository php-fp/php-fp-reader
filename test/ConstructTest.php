<?php

namespace PhpFp\Reader\Test;

use PhpFp\Reader\Reader;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionClass('PhpFp\Reader\Reader'))
            ->getConstructor()->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testConstructor()
    {
        $this->assertEquals(
            (
                new Reader(
                    function ($x) {
                        return $x + 3;
                    }
                )
            )->run(4),
            7,
            'Constructor works.'
        );
    }
}
