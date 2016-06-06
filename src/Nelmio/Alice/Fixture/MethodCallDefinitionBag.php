<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Fixture;

final class MethodCallDefinitionBag
{
    /**
     * @var MethodCallDefinition[]
     */
    private $properties = [];

    public function with(MethodCallDefinition $methodCall): self
    {
        $clone = clone $this;
        $clone->properties[$methodCall->getName()] = $methodCall;

        return $clone;
    }
}
