# Streamable Console [![Build Status](https://github.com/mpyw/streamable-console/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/streamable-console/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/streamable-console/badge.svg?branch=master)](https://coveralls.io/github/mpyw/streamable-console?branch=master)

Call interactive artisan command using arbitrary stream.

## Requirements

- PHP: `^8.2`
- Laravel: `^11.0 || ^12.0`
- [guzzlehttp/psr7](https://github.com/guzzle/psr7): `^2.7`

> [!NOTE]
> Older versions have outdated dependency requirements. If you cannot prepare the latest environment, please refer to past releases.

## Installing

```bash
composer require mpyw/streamable-console
```

## Usage

### Using Stream

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QuizCommand extends Command
{
    protected $signature = 'example:quiz';

    /**
     * @return int
     */
    public function handle(): int
    {
        // We need to type "no" and press Enter to pass
        if ($this->confirm('Is one plus one equals to three?', true)) {
            return 1;
        }
        
        return 0;
    }
}
```

```php
<?php

namespace App\Console\Commands;

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
        // Type "no" and press Enter
        return $this->usingInputStream("no\n")->call('example:quiz');
    }
}
```

### Using Infinite Input (`yes` command emulation)

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QuizCommand extends Command
{
    protected $signature = 'example:quiz';

    /**
     * @return int
     */
    public function handle(): int
    {
        // We need to type "no" and press Enter to pass at least for three times
        if ($this->confirm('Is one plus one equals to three?', true)) {
            return 1;
        }
        if ($this->confirm('Is one plus one equals to three?', true)) {
            return 1;
        }
        if ($this->confirm('Is one plus one equals to three?', true)) {
            return 1;
        }
        
        return 0;
    }
}
```

```php
<?php

namespace App\Console\Commands;

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
        // Infinitely type "no" and press Enter
        return $this->usingInfiniteInput("no\n")->call('example:quiz');
    }
}
```

Note that you can use `yes()` as an alias of `usingInfiniteInput()`.
