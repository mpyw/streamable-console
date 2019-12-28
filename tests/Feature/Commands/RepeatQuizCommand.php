<?php

namespace Mpyw\StreamableConsole\Tests\Feature\Commands;

use Illuminate\Console\Command;

class RepeatQuizCommand extends Command
{
    protected $signature = 'example:repeat-quiz';

    /**
     * @return int
     */
    public function handle(): int
    {
        for ($i = 0; $i < 10; ++$i) {
            if ($this->ask('1 + 1 = ') !== '2') {
                return 1;
            }
        }

        return 0;
    }
}
