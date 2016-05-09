<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Builder;

/**
 * @internal
 */
final class FlagParser
{
    /**
     * Parses the given key into a name and flags.
     *
     * @param string $key
     *
     * @return array Flag values are returned as keys.
     */
    public function parse(string $key): array 
    {
        $matches = [];
        if (1 !== preg_match('/^(?:.+?)\s*\((.+)\)$/', $key, $matches)) {
            return [];
        }

        $flags = [];
        $rawFlags = explode(',', $matches[1]);
        foreach ($rawFlags as $rawFlag) {
            $flag = trim($rawFlag);
            if ('' !== $flag) {
                $flags[$flag] = true;
            }
        }

        return $flags;
    }
}
