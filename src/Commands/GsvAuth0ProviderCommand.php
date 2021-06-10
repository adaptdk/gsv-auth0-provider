<?php

namespace Adaptdk\GsvAuth0Provider\Commands;

use Illuminate\Console\Command;

class GsvAuth0ProviderCommand extends Command
{
    public $signature = 'gsv-auth0-provider';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
