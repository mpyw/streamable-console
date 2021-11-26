<?php

namespace Mpyw\StreamableConsole;

use GuzzleHttp\Psr7\StreamWrapper;
use GuzzleHttp\Psr7\Utils;

class InfiniteStream
{
    /**
     * @param  string   $input
     * @return resource
     */
    public static function open(string $input)
    {
        return StreamWrapper::getResource(Utils::streamFor((function () use ($input) {
            while (true) {
                yield $input;
            }
        })()));
    }
}
