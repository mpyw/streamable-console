<?php

namespace Mpyw\StreamableConsole\InteractiveInputs;

use Symfony\Component\Console\Input\StringInput as BaseStringInput;

class StringInput extends BaseStringInput
{
    use Concerns\ForcesInteractions;
}
