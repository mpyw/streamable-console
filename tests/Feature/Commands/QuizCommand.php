<?php

namespace Mpyw\StreamableConsole\Tests\Feature\Commands;

use Illuminate\Console\Command;

class QuizCommand extends Command
{
    protected $signature = 'example:quiz';

    /**
     * @return int
     */
    public function handle(): int
    {
        if ($this->ask('1 + 1 = ') !== '2') {
            return 1;
        }
        if ($this->confirm('Is one plus one equals to three?', true)) {
            return 2;
        }
        return 0;
    }
}
