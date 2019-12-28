<?php

namespace Mpyw\StreamableConsole\Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Mpyw\StreamableConsole\Tests\Feature\Commands\QuizCommand;
use Mpyw\StreamableConsole\Tests\Feature\Commands\RepeatQuizCommand;
use Mpyw\StreamableConsole\Tests\Feature\Commands\RunCommand;
use Mpyw\StreamableConsole\Tests\Feature\Commands\RunInfiniteCommand;
use Orchestra\Testbench\TestCase;

class CommandTest extends TestCase
{
    public function testRun(): void
    {
        /* @var Kernel|\Illuminate\Foundation\Console\Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);

        $kernel->registerCommand(new QuizCommand());
        $kernel->registerCommand(new RunCommand());

        $this->assertSame(0, $kernel->call('example:run'));
    }

    public function testRunInfinite(): void
    {
        /* @var Kernel|\Illuminate\Foundation\Console\Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);

        $kernel->registerCommand(new RepeatQuizCommand());
        $kernel->registerCommand(new RunInfiniteCommand());

        $this->assertSame(0, $kernel->call('example:run-infinite'));
    }
}
