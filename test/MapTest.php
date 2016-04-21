<?php

namespace PhpFp\Reader\Test;

use PhpFp\Reader\Reader;

class MapTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\Reader\Reader::map'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testMap()
    {
        $this->assertEquals(
            Reader::of(2)->map(
                function ($x)
                {
                    return $x * 5;
                }
            )->run(20),
            10,
            'Maps.'
        );
    }
}
