<?php

/*
 * This file is part of the Alice package.
 *  
 * (c) Nelmio <hello@nelm.io>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Generator\Instantiator;

use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\Generator\ArgumentsResolverInterface;
use Nelmio\Alice\Generator\InstantiatorInterface;
use Nelmio\Alice\Generator\ResolvedFixtureSet;

/**
 * TODO
 */
final class SimpleInstantiatorDecorator implements InstantiatorInterface
{
    /**
     * @var InstantiatorInterface
     */
    private $instantiator;
    
    /**
     * @var ArgumentsResolverInterface
     */
    private $argumentsResolver;

    public function __construct(InstantiatorInterface $instantiatorInterface, ArgumentsResolverInterface $argumentsResolver)
    {
        $this->instantiator = $instantiatorInterface;
        $this->argumentsResolver = $argumentsResolver;
    }

    /**
     * @inheritdoc
     */
    public function instantiate(FixtureInterface $fixture, ResolvedFixtureSet $fixtureSet): ResolvedFixtureSet
    {
        $fixtureSet = $this->argumentsResolver->resolve(
            $fixture->getSpecs()->getConstructor()->getArguments(),
            $fixture,
            $fixtureSet
        );

        return $this->instantiator->instantiate(
            $fixtureSet->getFixtures()->get($fixture->getId()),
            $fixtureSet
        );
    }
}
