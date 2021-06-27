<?php

namespace Mpyw\StreamableConsole;

use Illuminate\Console\Command;
use Psr\Http\Message\StreamInterface;
use ReflectionMethod;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PendingStreamableCall
 */
class PendingStreamableCall
{
    /**
     * @var Command
     */
    protected $command;

    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * PendingStreamableCall constructor.
     *
     * @param Command         $command
     * @param StreamInterface $stream
     */
    public function __construct(Command $command, StreamInterface $stream)
    {
        $this->command = $command;
        $this->stream = $stream;
    }

    /**
     * Call another console command.
     *
     * @param  Command|string $command
     * @param  array          $arguments
     * @return int
     */
    public function call($command, array $arguments = []): int
    {
        return $this->callRunCommand($command, $arguments, $this->command->getOutput());
    }

    /**
     * Call another console command silently.
     *
     * @param  Command|string $command
     * @param  array          $arguments
     * @return int
     */
    public function callSilent($command, array $arguments = []): int
    {
        return $this->callRunCommand($command, $arguments, new NullOutput());
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * Run the given the console command.
     *
     * @param  Command|string  $command
     * @param  array           $arguments
     * @param  OutputInterface $output
     * @return int
     */
    protected function callRunCommand($command, array $arguments, OutputInterface $output): int
    {
        $arguments['command'] = $command;

        $input = $this->createInputFromArguments($arguments);
        $input->setStream($this->stream->detach());

        $target = \version_compare($this->command->getApplication()->getVersion(), '6.0.0', '>=')
            ? $this->resolveCommand($command)
            : $this->command->getApplication()->find($command);

        /* @noinspection PhpUnhandledExceptionInspection */
        return $target->run($input, $output);
    }

    /**
     * Resolve the console command instance for the given command.
     *
     * @param  Command|string $command
     * @return Command
     */
    protected function resolveCommand($command): Command
    {
        return $this->callCommandMethod(__FUNCTION__, $command);
    }

    /**
     * Create an input instance from the given arguments.
     *
     * @param  array                    $arguments
     * @return StreamableInputInterface
     */
    protected function createInputFromArguments(array $arguments): StreamableInputInterface
    {
        return $this->callCommandMethod(__FUNCTION__, $arguments);
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * @param  string $method
     * @param  array  $arguments
     * @return mixed
     */
    protected function callCommandMethod(string $method, ...$arguments)
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $method = new ReflectionMethod($this->command, $method);
        $method->setAccessible(true);

        /** @noinspection PhpUnhandledExceptionInspection */
        return $method->invokeArgs($this->command, $arguments);
    }
}
