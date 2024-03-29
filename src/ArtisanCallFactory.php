<?php

namespace Mpyw\StreamableConsole;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Container\Container;

/**
 * Class ArtisanCallFactory
 */
class ArtisanCallFactory
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Contracts\Console\Kernel|\Illuminate\Foundation\Console\Kernel
     */
    protected $kernel;

    /**
     * PendingStreamableCall constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Contracts\Console\Kernel      $kernel
     */
    public function __construct(Container $container, Kernel $kernel)
    {
        $this->container = $container;
        $this->kernel = $kernel;
    }

    /**
     * @param  null|bool|callable|float|int|\Iterator|\Psr\Http\Message\StreamInterface|resource|string $resource Entity body data
     * @return \Mpyw\StreamableConsole\PendingArtisanCall
     */
    public function usingInputStream($resource): PendingArtisanCall
    {
        return new PendingArtisanCall($this->container, $this->kernel, Utils::streamFor($resource));
    }

    /**
     * @param  string                                     $input
     * @return \Mpyw\StreamableConsole\PendingArtisanCall
     */
    public function usingInfiniteInput(string $input): PendingArtisanCall
    {
        return $this->usingInputStream(InfiniteStream::open($input));
    }

    /**
     * @param  string                                     $input
     * @return \Mpyw\StreamableConsole\PendingArtisanCall
     */
    public function yes(string $input = "yes\n"): PendingArtisanCall
    {
        return $this->usingInfiniteInput($input);
    }
}
