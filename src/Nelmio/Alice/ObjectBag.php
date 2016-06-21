<?php

/*
 * This file is part of the Alice package.
 *  
 * (c) Nelmio <hello@nelm.io>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice;

use Nelmio\Alice\Definition\Object\SimpleObject;
use Nelmio\Alice\Exception\ObjectNotFoundException;

/**
 * Value object containing a list of objects.
 */
final class ObjectBag implements \IteratorAggregate
{
    private $objects = [];

    public function __construct(array $objects = [])
    {
        foreach ($objects as $className => $classObjects) {
            if (false === is_array($className)) {
                throw new \TypeError(
                    'TODO'
                );
            }
            
            $this->objects[$className] = [];
            foreach ($classObjects as $reference => $object) {
                $this->objects[$reference] = new SimpleObject($reference, $object);
            }
        }

    }

    /**
     * Creates a new instance which will contain the given object. If an object with the same reference already exists,
     * it will be overridden by the new object.
     * 
     * @param ObjectInterface $object
     *
     * @return self
     */
    public function with(ObjectInterface $object): self
    {
        $clone = clone $this;
        $clone->objects[$object->getReference()] = $object;
        
        return $clone;
    }

    /**
     * Creates a new instance with the new objects. If objects with the same reference already exists, they will be
     * overridden by the new ones.
     * 
     * @param ObjectBag $objects
     *
     * @return self
     */
    public function mergeWith(self $objects): self
    {
        $clone = clone $this;
        foreach ($objects as $object) {
            /** @var ObjectInterface $object */
            $clone->objects[$object->getReference()] = $object;
        }
        
        return $clone;
    }
    
    public function has(FixtureInterface $fixture): bool
    {
        $className = $fixture->getClassName();
        $reference = $fixture->getReference();
        
        return isset($this->objects[$className][$reference]);
    }

    /**
     * @param FixtureInterface $fixture
     *
     * @throws ObjectNotFoundException
     * 
     * @return ObjectInterface
     */
    public function get(FixtureInterface $fixture): ObjectInterface
    {
        $className = $fixture->getClassName();
        $reference = $fixture->getReference();
        if (isset($this->objects[$className][$reference])) {
            return $this->objects[$className][$reference];
        }
        
        throw ObjectNotFoundException::create($reference, $className);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->objects);
    }
}
