<?php

namespace PhpFp\Reader\Test;

use PhpFp\Reader\Reader;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\Reader\Reader::chain'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testChain()
    {
        $this->assertEquals(
            Reader::of(2)->chain(
                function ($x)
                {
                    return Reader::of(5)->map(
                        function ($y) use ($x)
                        {
                            return $x * $y;
                        }
                    );
                }
            )->run('Nothing relevant'),
            10,
            'Chains.'
        );
    }
}
