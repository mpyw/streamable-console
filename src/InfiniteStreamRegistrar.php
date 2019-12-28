<?php

namespace Mpyw\StreamableConsole;

class InfiniteStreamRegistrar
{
    /**
     * @param  string   $input
     * @param  callable $callback
     * @return mixed
     */
    public function usingInfiniteInput(string $input, callable $callback)
    {
        $wrapper = $this->createStreamWrapper($input);
        $class = get_class($wrapper);
        $protocol = sha1($class);

        try {
            stream_wrapper_register($protocol, $class);
            return $callback(fopen("$protocol://", 'r+b'));
        } finally {
            stream_wrapper_unregister($protocol);
        }
    }

    /**
     * @param  string $input
     * @return mixed
     */
    protected function createStreamWrapper(string $input)
    {
        $wrapper = new class() {
            public static $input;

            public function stream_open($path, $mode, $options, &$opened_path): bool
            {
                return true;
            }

            public function stream_read(int $count): string
            {
                return static::$input;
            }

            public function stream_eof(): bool
            {
                return false;
            }
        };

        $wrapper::$input = $input;

        return $wrapper;
    }
}
