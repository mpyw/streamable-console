<?php

namespace Mpyw\StreamableConsole;

use Illuminate\Support\Facades\Facade;

/**
 * Class StreamableArtisan
 *
 * @see \Mpyw\StreamableConsole\ArtisanCallFactory
 *
 * @method static \Mpyw\StreamableConsole\PendingArtisanCall usingInputStream(null|bool|callable|float|int|\Iterator|\Psr\Http\Message\StreamInterface|resource|string $resource) Entity body data
 * @method static \Mpyw\StreamableConsole\PendingArtisanCall usingInfiniteInput(string $input)
 * @method static \Mpyw\StreamableConsole\PendingArtisanCall yes(string $input = "yes\n")
 * @method static int                                        call(string $command, array $parameters, null|\Symfony\Component\Console\Output\OutputInterface $outputBuffer)
 */
class StreamableArtisan extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return ArtisanCallFactory::class;
    }
}
