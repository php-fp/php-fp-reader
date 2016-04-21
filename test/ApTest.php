<?php

namespace PhpFp\Reader\Test;

use PhpFp\Reader\Reader;

class ApTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\Reader\Reader::ap'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testAp()
    {
        $this->assertEquals(
            Reader::of(
                function ($x)
                {
                    return $x + 2;
                }
            )
            ->ap(Reader::of(10))
            ->run(50),
            12,
            'Applies.'
        );
    }
}
