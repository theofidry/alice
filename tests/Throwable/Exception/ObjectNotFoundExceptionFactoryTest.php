<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Nelmio\Alice\Throwable\Exception;

/**
 * @covers \Nelmio\Alice\Throwable\Exception\ObjectNotFoundExceptionFactory
 */
class ObjectNotFoundExceptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTestCreateNewExceptionWithFactory()
    {
        $exception = ObjectNotFoundExceptionFactory::create('foo', 'Dummy');

        $this->assertEquals(
            'Could not find the object "foo" of the class "Dummy".',
            $exception->getMessage()
        );
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
