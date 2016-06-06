<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture\Flag;

use Nelmio\Alice\Builder\Fixture\FlagParserInterface;
use Nelmio\Alice\Fixture\Flag\ExtendFlag;
use Nelmio\Alice\Fixture\Flag\TemplateFlag;
use Nelmio\Alice\Fixture\FlagBag;

final class FlagParser implements FlagParserInterface
{
    /**
     * @var RawFlagParser
     */
    private $rawFlagParser;

    public function __construct()
    {
        $this->rawFlagParser = new RawFlagParser();
    }

    /**
     * @inheritdoc
     */
    public function parse(string $element): FlagBag
    {
        $rawFlagSet = $this->rawFlagParser->parse($element);
        $flags = new FlagBag($rawFlagSet[0]);

        foreach ($rawFlagSet[1] as $rawFlags) {
            $flags = $this->parseRawFlag($flags, $rawFlags);
        }

        return $flags;
    }

    private function parseRawFlag(FlagBag $flags, array $rawFlags): FlagBag
    {
        if ('extends ' === substr($rawFlags, 0, 8)) {
            $flag = new ExtendFlag(substr($rawFlags, 8));

            return $flags->with($flag);
        }

        if ('template' === $rawFlags) {
            $flag = new TemplateFlag();

            return $flags->with($flag);
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Unable to parse the flag "%s".',
                $rawFlags
            )
        );
    }
}
