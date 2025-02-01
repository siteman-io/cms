<?php

namespace Siteman\Cms\Commands;

use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    public $signature = 'siteman:update';

    public $description = 'This command updates Siteman';

    public function handle(): int
    {
        $this->call('siteman:publish');

        $this->comment('All done');

        return self::SUCCESS;
    }
}
