<?php

namespace PhpFp\Reader\Test;

use PhpFp\Reader\Reader;

class OfTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\Reader\Reader::of'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testApplicativeConstructor()
    {
        $this->assertEquals(
            Reader::of(2)->run(4),
            2,
            'Constructs an applicative.'
        );
    }
}
