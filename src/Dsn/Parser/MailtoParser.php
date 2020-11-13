<?php

namespace Zenstruck\Utilities\Dsn\Parser;

use Zenstruck\Utilities\Dsn\Exception\UnableToParse;
use Zenstruck\Utilities\Dsn\Parser;
use Zenstruck\Utilities\Mailto;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MailtoParser implements Parser
{
    public function parse(string $value): Mailto
    {
        if (0 === \mb_strpos($value, 'mailto:')) {
            return new Mailto($value);
        }

        throw new UnableToParse($value);
    }
}
