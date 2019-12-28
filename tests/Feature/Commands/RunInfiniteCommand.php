<?php

namespace Mpyw\StreamableConsole\Tests\Feature\Commands;

use Illuminate\Console\Command;
use Mpyw\StreamableConsole\Streamable;

class RunInfiniteCommand extends Command
{
    use Streamable;

    protected $signature = 'example:run-infinite';

    /**
     * @return int
     */
    public function handle(): int
    {
        return $this->yes("2\n")->call('example:repeat-quiz');
    }
}
