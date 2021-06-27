<?php

namespace Mpyw\StreamableConsole;

use GuzzleHttp\Psr7\Utils;
use Iterator;
use Psr\Http\Message\StreamInterface;

/**
 * Trait Streamable
 *
 * @mixin \Illuminate\Console\Command
 */
trait Streamable
{
    /**
     * @param  null|bool|callable|float|int|Iterator|resource|StreamInterface|string $resource Entity body data
     * @return PendingStreamableCall
     */
    public function usingInputStream($resource): PendingStreamableCall
    {
        /** @noinspection PhpParamsInspection */
        return new PendingStreamableCall($this, Utils::streamFor($resource));
    }

    /**
     * @param  string                $input
     * @return PendingStreamableCall
     */
    public function usingInfiniteInput(string $input): PendingStreamableCall
    {
        return $this->usingInputStream(InfiniteStream::open($input));
    }

    /**
     * @param  string                $input
     * @return PendingStreamableCall
     */
    public function yes(string $input = "yes\n"): PendingStreamableCall
    {
        return $this->usingInfiniteInput($input);
    }
}
