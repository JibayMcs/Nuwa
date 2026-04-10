<?php

namespace JibayMcs\Nuwa\Commands;

use Illuminate\Console\Command;

class NuwaCommand extends Command
{
    public $signature = 'nuwa';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
