<?php

namespace PhpFp\Reader\Test;

use PhpFp\Reader\Reader;

class RunTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\Reader\Reader::run'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testRun()
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
            )->run(20),
            22,
            'Runs.'
        );
    }
}
