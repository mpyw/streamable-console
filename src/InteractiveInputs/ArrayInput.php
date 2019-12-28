<?php

namespace Mpyw\StreamableConsole\InteractiveInputs;

use Symfony\Component\Console\Input\ArrayInput as BaseArrayInput;

class ArrayInput extends BaseArrayInput
{
    use Concerns\ForcesInteractions;
}
