<?php

namespace Mpyw\StreamableConsole\Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Mpyw\StreamableConsole\StreamableArtisan;
use Mpyw\StreamableConsole\Tests\Feature\Commands\QuizCommand;
use Mpyw\StreamableConsole\Tests\Feature\Commands\RepeatQuizCommand;
use Orchestra\Testbench\TestCase;

class ArtisanTest extends TestCase
{
    public function testRun(): void
    {
        /* @var Kernel|\Illuminate\Foundation\Console\Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);
        $kernel->registerCommand(new QuizCommand());

        $this->assertSame(0, StreamableArtisan::usingInputStream("2\nno\n")->call('example:quiz'));
    }

    public function testRunInfinite(): void
    {
        /* @var Kernel|\Illuminate\Foundation\Console\Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);
        $kernel->registerCommand(new RepeatQuizCommand());

        $this->assertSame(0, StreamableArtisan::yes("2\n")->call('example:repeat-quiz'));
    }
}
