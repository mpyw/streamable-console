# Streamable Console [![Build Status](https://travis-ci.com/mpyw/streamable-console.svg?branch=master)](https://travis-ci.com/mpyw/streamable-console) [![Code Coverage](https://scrutinizer-ci.com/g/mpyw/streamable-console/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mpyw/streamable-console/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/streamable-console/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/streamable-console/?branch=master)

Call interactive artisan command using arbitrary stream.

## Requirements

- PHP: ^7.1
- Laravel: ^5.6 || ^6.0

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

### Use via `StreamableArtisan` Facade

```php
<?php

use Mpyw\StreamableConsole\StreamableArtisan;

StreamableArtisan::usingInputStream("no\n")->call('example:quiz');
StreamableArtisan::usingInfiniteInput("no\n")->call('example:quiz');
```
