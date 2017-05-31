<?php

namespace Flipbox\Cache\Tests\Exceptions;

use Flipbox\Cache\Exceptions\InvalidCachePoolException;

class InvalidCachePoolExceptionTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function nameMatchesTest()
    {
        $exception = new InvalidCachePoolException();
        $this->assertEquals('Invalid Cache Pool', $exception->getName());

    }
}
