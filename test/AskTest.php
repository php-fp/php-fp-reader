<?php

namespace PhpFp\Reader\Test;

use PhpFp\Reader\Reader;

class AskTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\Reader\Reader::ask'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            0,
            'Takes no parameters.'
        );
    }

    public function testAsk()
    {
        $this->assertEquals(
            Reader::of(2)->chain(
                function ($x)
                {
                    return Reader::ask()->map(
                        function ($y) use ($x)
                        {
                            return $x + $y;
                        }
                    );
                }
            )->run(4),
            6,
            'Accesses environment.'
        );
    }
}
