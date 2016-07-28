<?php

/*
 * This file is part of the Alice package.
 *  
 * (c) Nelmio <hello@nelm.io>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Fixture\Definition;

use Nelmio\Alice\Definition\FakeMethodCall;
use Nelmio\Alice\Definition\MethodCall\MutableMethodCall;
use Nelmio\Alice\Definition\MethodCall\SimpleMethodCall;
use Nelmio\Alice\Definition\MethodCallBag;
use Nelmio\Alice\Definition\MethodCallInterface;
use Nelmio\Alice\Definition\Property;
use Nelmio\Alice\Definition\PropertyBag;
use Nelmio\Alice\Definition\SpecificationBag;

/**
 * @covers Nelmio\Alice\Definition\SpecificationBag
 */
class SpecificationBagTest extends \PHPUnit_Framework_TestCase
{
    public function testReadAccessorsReturnPropertiesValues()
    {
        $constructor = new FakeMethodCall();
        $properties = new PropertyBag();
        $calls = new MethodCallBag();

        $bag = new SpecificationBag($constructor, $properties, $calls);
        $this->assertEquals($constructor, $bag->getConstructor());
        $this->assertEquals($properties, $bag->getProperties());
        $this->assertEquals($calls, $bag->getMethodCalls());

        $constructor = null;

        $bag = new SpecificationBag($constructor, $properties, $calls);
        $this->assertEquals($constructor, $bag->getConstructor());
        $this->assertEquals($properties, $bag->getProperties());
        $this->assertEquals($calls, $bag->getMethodCalls());
    }

    /**
     * @depends PropertyBagTest::testIsImmutable
     * @depends MethodCallBagTest::testIsImmutable
     */
    public function testIsImmutable()
    {
        $constructor = new MutableMethodCall(null, 'mutable');
        $properties = new PropertyBag();
        $calls = new MethodCallBag();

        $bag = new SpecificationBag($constructor, $properties, $calls);

        // Mutate injected value
        $constructor->setMethod('foo');

        // Mutate returned value
        $bag->getConstructor()->setMethod('bar');

        $this->assertNotSame(new MutableMethodCall(null, 'mutable'), $bag->getConstructor());
    }

    public function testWithersReturnNewModifiedInstance()
    {
        $constructor = null;
        $properties = new PropertyBag();
        $calls = new MethodCallBag();
        $bag = new SpecificationBag($constructor, $properties, $calls);

        $newConstructor = new MutableMethodCall(null, 'mutable');
        $newBag = $bag->withConstructor($newConstructor);

        // Mutate injected value
        $newConstructor->setMethod('foo');

        // Mutate returned value
        $newBag->getConstructor()->setMethod('bar');

        $this->assertInstanceOf(SpecificationBag::class, $newBag);

        $this->assertEquals($constructor, $bag->getConstructor());
        $this->assertEquals($calls, $bag->getMethodCalls());
        $this->assertEquals($properties, $bag->getProperties());

        $this->assertEquals(new MutableMethodCall(null, 'mutable'), $newBag->getConstructor());
        $this->assertEquals($calls, $newBag->getMethodCalls());
        $this->assertEquals($properties, $newBag->getProperties());
    }

    public function testMergeTwoBags()
    {
        $constructorA = null;
        $constructorB = null;

        $propertyA1 = new Property('username', 'alice');
        $propertyA2 = new Property('owner', 'bob');

        $propertyB1 = new Property('username', 'mad');
        $propertyB2 = new Property('mail', 'bob@ex.com');

        $propertiesA = (new PropertyBag())
            ->with($propertyA1)
            ->with($propertyA2)
        ;
        $propertiesB = (new PropertyBag())
            ->with($propertyB1)
            ->with($propertyB2)
        ;

        $callA1 = new SimpleMethodCall('setUsername', []);
        $callA2 = new SimpleMethodCall('setOwner', []);

        $callB1 = new SimpleMethodCall('setUsername', []);
        $callB2 = new SimpleMethodCall('setMail', []);

        $callsA = (new MethodCallBag())
            ->with($callA1)
            ->with($callA2)
        ;
        $callsB = (new MethodCallBag())
            ->with($callB1)
            ->with($callB2)
        ;

        $bagA = new SpecificationBag($constructorA, $propertiesA, $callsA);
        $bagB = new SpecificationBag($constructorB, $propertiesB, $callsB);
        $bag = $bagA->mergeWith($bagB);

        $this->assertInstanceOf(SpecificationBag::class, $bag);
        $this->assertEquals($constructorA, $bagA->getConstructor());
        $this->assertEquals($propertiesA, $bagA->getProperties());
        $this->assertEquals($callsA, $bagA->getMethodCalls());

        $this->assertEquals($constructorB, $bagB->getConstructor());
        $this->assertEquals($propertiesB, $bagB->getProperties());
        $this->assertEquals($callsB, $bagB->getMethodCalls());

        $this->assertEquals($constructorA, $bag->getConstructor());
    }

    /**
     * @testdox Merging a bag that has a constructor method with a new one that does not, the result will have a constructor method.
     */
    public function testMergeTwoBags1()
    {
        $constructorA = new SimpleMethodCall('create', []);
        $constructorB = null;

        $bagA = new SpecificationBag($constructorA, new PropertyBag(), new MethodCallBag());
        $bagB = new SpecificationBag($constructorB, new PropertyBag(), new MethodCallBag());
        $bag = $bagA->mergeWith($bagB);

        $this->assertEquals($constructorA, $bagA->getConstructor());
        $this->assertEquals($constructorB, $bagB->getConstructor());
        $this->assertEquals($constructorA, $bag->getConstructor());
    }

    /**
     * @testdox Merging a bag that has a constructor method with a new one that has one as well, the result will kept its constructor method.
     */
    public function testMergeTwoBags2()
    {
        $constructorA = new SimpleMethodCall('childCreate', []);
        $constructorB = new SimpleMethodCall('parentCreate', []);

        $bagA = new SpecificationBag($constructorA, new PropertyBag(), new MethodCallBag());
        $bagB = new SpecificationBag($constructorB, new PropertyBag(), new MethodCallBag());
        $bag = $bagA->mergeWith($bagB);

        $this->assertEquals($constructorA, $bagA->getConstructor());
        $this->assertEquals($constructorB, $bagB->getConstructor());
        $this->assertEquals($constructorA, $bag->getConstructor());
    }
}
