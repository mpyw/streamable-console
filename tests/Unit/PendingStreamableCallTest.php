<?php

namespace Mpyw\StreamableConsole\Tests\Unit;

use Hamcrest\Core\IsInstanceOf;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Mockery;
use Mpyw\StreamableConsole\PendingStreamableCall;
use Orchestra\Testbench\TestCase;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class PendingStreamableCallTest extends TestCase
{
    public function testCallCommand(): void
    {
        $application = Mockery::mock(SymfonyApplication::class);
        $caller = Mockery::mock(Command::class);
        $callee = Mockery::mock(Command::class);
        $input = Mockery::mock(StreamableInputInterface::class);
        $output = Mockery::mock(OutputInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $caller->shouldAllowMockingProtectedMethods();
        $fp = fopen('php://memory', 'r+b');

        $caller->shouldReceive('getOutput')
            ->once()
            ->andReturn($output);
        $caller->shouldReceive('createInputFromArguments')
            ->once()
            ->with([
                'command' => 'example:run',
                'arg' => 'foo',
                '--option' => 'bar',
            ])
            ->andReturn($input);
        $stream->shouldReceive('detach')
            ->once()
            ->andReturn($fp);
        $input->shouldReceive('setStream')
            ->once()
            ->with($fp);

        $caller->shouldReceive('resolveCommand')
            ->once()
            ->with('example:run')
            ->andReturn($callee);

        $callee->shouldReceive('run')
            ->once()
            ->with($input, $output)
            ->andReturn(0);

        $this->assertSame(0, (new PendingStreamableCall($caller, $stream))
            ->call('example:run', ['arg' => 'foo', '--option' => 'bar']));
    }

    public function testCallSilentCommand(): void
    {
        $application = Mockery::mock(SymfonyApplication::class);
        $caller = Mockery::mock(Command::class);
        $callee = Mockery::mock(Command::class);
        $input = Mockery::mock(StreamableInputInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $caller->shouldAllowMockingProtectedMethods();
        $fp = fopen('php://memory', 'r+b');

        $caller->shouldNotReceive('getOutput');
        $caller->shouldReceive('createInputFromArguments')
            ->once()
            ->with([
                'command' => 'example:run',
                'arg' => 'foo',
                '--option' => 'bar',
            ])
            ->andReturn($input);
        $stream->shouldReceive('detach')
            ->once()
            ->andReturn($fp);
        $input->shouldReceive('setStream')
            ->once()
            ->with($fp);

        $caller->shouldReceive('resolveCommand')
            ->once()
            ->with('example:run')
            ->andReturn($callee);

        $callee->shouldReceive('run')
            ->once()
            ->with($input, IsInstanceOf::anInstanceOf(NullOutput::class))
            ->andReturn(0);

        $this->assertSame(0, (new PendingStreamableCall($caller, $stream))
            ->callSilent('example:run', ['arg' => 'foo', '--option' => 'bar']));
    }
}
