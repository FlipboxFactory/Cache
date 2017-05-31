<?php

namespace Flipbox\Cache\Tests\Exceptions;

use Flipbox\Cache\Exceptions\InvalidDriverException;

class InvalidDriverExceptionTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function nameMatchesTest()
    {
        $exception = new InvalidDriverException();
        $this->assertEquals('Invalid Cache Driver', $exception->getName());

    }
}
