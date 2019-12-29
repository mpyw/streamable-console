<?php

namespace Mpyw\StreamableConsole;

use Mpyw\StreamInterfaceResource\StreamInterfaceResource;

class InfiniteStream
{
    /**
     * @param  string   $input
     * @return resource
     */
    public static function open(string $input)
    {
        return StreamInterfaceResource::open((function () use ($input) {
            while (true) {
                yield $input;
            }
        })());
    }
}
