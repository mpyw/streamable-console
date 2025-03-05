<?php

namespace Mpyw\StreamableConsole\Tests\Unit;

use Hamcrest\Core\IsInstanceOf;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application as ArtisanContract;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Console\Kernel;
use Mockery;
use Mpyw\StreamableConsole\InteractiveInputs\ArrayInput;
use Mpyw\StreamableConsole\InteractiveInputs\StringInput;
use Mpyw\StreamableConsole\PendingArtisanCall;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PendingArtisanCallTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(enabled: false)]
    public function testCallStringInput(): void
    {
        $container = Mockery::mock(Container::class);
        $kernel = Mockery::mock(Kernel::class, KernelContract::class);
        $artisan = Mockery::mock(Artisan::class, ArtisanContract::class);
        $stream = Mockery::mock(StreamInterface::class);
        $command = Mockery::mock(Command::class);
        $input = Mockery::mock('overload:' . StringInput::class, StreamableInputInterface::class);
        $output = Mockery::mock(OutputInterface::class);

        $kernel->shouldAllowMockingProtectedMethods();
        $artisan->shouldAllowMockingProtectedMethods();
        $fp = fopen('php://memory', 'r+b');

        $pending = new PendingArtisanCall($container, $kernel, $stream);

        $kernel->shouldReceive('bootstrap')
            ->once();
        $kernel->shouldReceive('getArtisan')
            ->once()
            ->andReturn($artisan);
        $container->shouldNotReceive('make');
        $command->shouldNotReceive('getName');
        $artisan->shouldReceive('getCommandName')
            ->with(IsInstanceOf::anInstanceOf($input))
            ->andReturn('example');
        $stream->shouldReceive('detach')
            ->once()
            ->andReturn($fp);
        $input->shouldReceive('setStream')
            ->once()->with($fp);
        $artisan->shouldReceive('has')
            ->with('example')
            ->once()
            ->andReturnTrue();
        $artisan->shouldReceive('run')
            ->with(IsInstanceOf::anInstanceOf($input), $output)
            ->once()
            ->andReturn(0);

        $this->assertSame(
            0,
            $pending->call('example a b c', [], $output)
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(enabled: false)]
    public function testCallArrayInput(): void
    {
        $container = Mockery::mock(Container::class);
        $kernel = Mockery::mock(Kernel::class, KernelContract::class);
        $artisan = Mockery::mock(Artisan::class, ArtisanContract::class);
        $stream = Mockery::mock(StreamInterface::class);
        $command = Mockery::mock(Command::class);
        $input = Mockery::mock('overload:' . ArrayInput::class, StreamableInputInterface::class);
        $output = Mockery::mock(OutputInterface::class);

        $kernel->shouldAllowMockingProtectedMethods();
        $artisan->shouldAllowMockingProtectedMethods();
        $fp = fopen('php://memory', 'r+b');

        $pending = new PendingArtisanCall($container, $kernel, $stream);

        $kernel->shouldReceive('bootstrap')
            ->once();
        $kernel->shouldReceive('getArtisan')
            ->once()
            ->andReturn($artisan);
        $container->shouldNotReceive('make');
        $command->shouldNotReceive('getName');
        $artisan->shouldNotReceive('getCommandName');
        $stream->shouldReceive('detach')
            ->once()
            ->andReturn($fp);
        $input->shouldReceive('setStream')
            ->once()->with($fp);
        $artisan->shouldReceive('has')
            ->with('example')
            ->once()
            ->andReturnTrue();
        $artisan->shouldReceive('run')
            ->with(IsInstanceOf::anInstanceOf($input), $output)
            ->once()
            ->andReturn(0);

        $this->assertSame(
            0,
            $pending->call('example', ['a', 'b', 'c'], $output)
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(enabled: false)]
    public function testCallClass(): void
    {
        $container = Mockery::mock(Container::class);
        $kernel = Mockery::mock(Kernel::class, KernelContract::class);
        $artisan = Mockery::mock(Artisan::class, ArtisanContract::class);
        $stream = Mockery::mock(StreamInterface::class);
        $command = Mockery::mock(Command::class);
        $input = Mockery::mock('overload:' . ArrayInput::class, StreamableInputInterface::class);
        $output = Mockery::mock(OutputInterface::class);

        $kernel->shouldAllowMockingProtectedMethods();
        $artisan->shouldAllowMockingProtectedMethods();
        $fp = fopen('php://memory', 'r+b');

        $pending = new PendingArtisanCall($container, $kernel, $stream);

        $kernel->shouldReceive('bootstrap')
            ->once();
        $kernel->shouldReceive('getArtisan')
            ->once()
            ->andReturn($artisan);
        $container->shouldReceive('make')
            ->with(get_class($command))
            ->once()
            ->andReturn($command);
        $command->shouldNotReceive('getName')
            ->once()
            ->andReturn('example');
        $artisan->shouldNotReceive('getCommandName');
        $stream->shouldReceive('detach')
            ->once()
            ->andReturn($fp);
        $input->shouldReceive('setStream')
            ->once()
            ->with($fp);
        $artisan->shouldReceive('has')
            ->with('example')
            ->once()
            ->andReturnTrue();
        $artisan->shouldReceive('run')
            ->with(IsInstanceOf::anInstanceOf($input), $output)
            ->once()
            ->andReturn(0);

        $this->assertSame(
            0,
            $pending->call(get_class($command), ['a', 'b', 'c'], $output)
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(enabled: false)]
    public function testMissingCommand(): void
    {
        $this->expectException(CommandNotFoundException::class);

        $container = Mockery::mock(Container::class);
        $kernel = Mockery::mock(Kernel::class, KernelContract::class);
        $artisan = Mockery::mock(Artisan::class, ArtisanContract::class);
        $stream = Mockery::mock(StreamInterface::class);
        $command = Mockery::mock(Command::class);
        $input = Mockery::mock('overload:' . ArrayInput::class, StreamableInputInterface::class);
        $output = Mockery::mock(OutputInterface::class);

        $kernel->shouldAllowMockingProtectedMethods();
        $artisan->shouldAllowMockingProtectedMethods();
        $fp = fopen('php://memory', 'r+b');

        $pending = new PendingArtisanCall($container, $kernel, $stream);

        $kernel->shouldReceive('bootstrap')
            ->once();
        $kernel->shouldReceive('getArtisan')
            ->once()
            ->andReturn($artisan);
        $container->shouldNotReceive('make');
        $command->shouldNotReceive('getName');
        $artisan->shouldNotReceive('getCommandName');
        $stream->shouldReceive('detach')
            ->once()
            ->andReturn($fp);
        $input->shouldReceive('setStream')
            ->once()
            ->with($fp);
        $artisan->shouldReceive('has')
            ->with('invalid')
            ->once()
            ->andReturnFalse();
        $artisan->shouldNotReceive('run');

        $pending->call('invalid', ['a', 'b', 'c'], $output);
    }
}
