<?php

namespace Mpyw\StreamableConsole;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application as ArtisanContract;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Console\Kernel;
use Mpyw\StreamableConsole\InteractiveInputs\ArrayInput;
use Mpyw\StreamableConsole\InteractiveInputs\StringInput;
use Psr\Http\Message\StreamInterface;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PendingArtisanCall
 */
class PendingArtisanCall
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Kernel|KernelContract
     */
    protected $kernel;

    /**
     * @var null|StreamInterface
     */
    protected $stream;

    /**
     * PendingStreamableCall constructor.
     *
     * @param Container       $container
     * @param KernelContract  $kernel
     * @param StreamInterface $stream
     */
    public function __construct(Container $container, KernelContract $kernel, StreamInterface $stream)
    {
        $this->container = $container;
        $this->kernel = $kernel;
        $this->stream = $stream;
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * Call another console command.
     *
     * @param  Command|string       $command
     * @param  array                $parameters
     * @param  null|OutputInterface $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], ?OutputInterface $outputBuffer = null): int
    {
        $this->kernel->bootstrap();

        $artisan = $this->getArtisan();

        /* @var Command $command */
        /* @var StreamableInputInterface $input */
        [$command, $input] = $this->parseCommand($artisan, $command, $parameters);
        $input->setStream($this->stream->detach());

        if (!$artisan->has($command)) {
            throw new CommandNotFoundException(sprintf('The command "%s" does not exist.', $command));
        }

        /* @noinspection PhpUnhandledExceptionInspection */
        return $artisan->run(
            $input,
            $this->setLastOutput($artisan, $outputBuffer ?: new BufferedOutput())
        );
    }

    /**
     * Get the Artisan application instance.
     *
     * @return Artisan|ArtisanContract
     */
    public function getArtisan(): ArtisanContract
    {
        return $this->callMethod($this->kernel, __FUNCTION__);
    }

    /**
     * Gets the name of the command based on input.
     *
     * @param  ArtisanContract $artisan
     * @param  InputInterface  $input
     * @return null|string
     */
    protected function getCommandName(ArtisanContract $artisan, InputInterface $input): ?string
    {
        return $this->callMethod($artisan, __FUNCTION__, $input);
    }

    /**
     * @param  ArtisanContract $artisan
     * @param  OutputInterface $output
     * @return OutputInterface
     */
    protected function setLastOutput(ArtisanContract $artisan, OutputInterface $output): OutputInterface
    {
        return $this->setProperty($artisan, 'lastOutput', $output);
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * Parse the incoming Artisan command and its input.
     *
     * @param  ArtisanContract $artisan
     * @param  string          $command
     * @param  array           $parameters
     * @return array
     */
    protected function parseCommand(ArtisanContract $artisan, string $command, array $parameters): array
    {
        if (is_subclass_of($command, SymfonyCommand::class)) {
            $callingClass = true;
            /* @noinspection PhpUnhandledExceptionInspection */
            $command = $this->container->make($command)->getName();
        }

        if (!isset($callingClass) && empty($parameters)) {
            $command = $this->getCommandName($artisan, $input = new StringInput($command));
        } else {
            array_unshift($parameters, $command);
            $input = new ArrayInput($parameters);
        }

        return [$command, $input];
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * @param  mixed  $object
     * @param  string $method
     * @param  array  $arguments
     * @return mixed
     */
    protected function callMethod($object, string $method, ...$arguments)
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $method = new ReflectionMethod($object, $method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $arguments);
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * @param  mixed  $object
     * @param  string $property
     * @param  mixed  $value
     * @return mixed
     */
    protected function setProperty($object, string $property, $value)
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $property = new ReflectionProperty($object, $property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        return $value;
    }
}
