<?php

namespace Mpyw\StreamableConsole;

use function GuzzleHttp\Psr7\stream_for;
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
        return new PendingStreamableCall($this, stream_for($resource));
    }

    /**
     * @param  string                $input
     * @return PendingStreamableCall
     */
    public function usingInfiniteInput(string $input): PendingStreamableCall
    {
        return (new InfiniteStreamRegistrar())->usingInfiniteInput($input, [$this, 'usingInputStream']);
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
