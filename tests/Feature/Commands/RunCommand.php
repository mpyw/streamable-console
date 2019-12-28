<?php

namespace Mpyw\StreamableConsole\Tests\Feature\Commands;

use Illuminate\Console\Command;
use Mpyw\StreamableConsole\Streamable;

class RunCommand extends Command
{
    use Streamable;

    protected $signature = 'example:run';

    /**
     * @return int
     */
    public function handle(): int
    {
        return $this->usingInputStream("2\nno\n")->call('example:quiz');
    }
}
